<?php
class RegisterController {
    private $db;
    private $registration;

    public function __construct($db) {
        $this->db = $db;
        $this->registration = new Registration($db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle File Upload
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $payment_slip = "";
            if (isset($_FILES["payment_slip"]) && $_FILES["payment_slip"]["error"] == 0) {
                $target_file = $target_dir . basename($_FILES["payment_slip"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["payment_slip"]["tmp_name"]);
                if($check !== false) {
                    // Generate unique name
                    $new_filename = uniqid() . "." . $imageFileType;
                    $target_file = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $target_file)) {
                        $payment_slip = $target_file;
                    }
                }
            }

            $this->registration->full_name = $_POST['full_name'];
            $this->registration->prefix = isset($_POST['prefix']) ? $_POST['prefix'] : null;
            $this->registration->gender = $_POST['gender'];
            $this->registration->birth_date = $_POST['birth_date'];
            $this->registration->age = isset($_POST['age']) ? $_POST['age'] : null;
            $this->registration->email = $_POST['email'];
            $this->registration->phone = $_POST['phone'];
            $this->registration->citizen_id = isset($_POST['citizen_id']) ? $_POST['citizen_id'] : null;
            $this->registration->emergency_contact_name = isset($_POST['emergency_contact_name']) ? $_POST['emergency_contact_name'] : null;
            $this->registration->emergency_contact_phone = isset($_POST['emergency_contact_phone']) ? $_POST['emergency_contact_phone'] : null;
            
            // Combine address parts
            $address_parts = [];
            if (!empty($_POST['address'])) $address_parts[] = $_POST['address'];
            if (!empty($_POST['subdistrict'])) $address_parts[] = "ต." . $_POST['subdistrict'];
            if (!empty($_POST['district_province'])) $address_parts[] = $_POST['district_province'];
            if (!empty($_POST['postal_code'])) $address_parts[] = $_POST['postal_code'];
            $this->registration->address = implode(" ", $address_parts);

            $this->registration->category = $_POST['category'];
            $this->registration->shirt_size = $_POST['shirt_size'];
            $this->registration->shipping_method = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : 'SELF';
            $this->registration->payment_slip = $payment_slip;
            $this->registration->payment_amount = !empty($_POST['payment_amount']) ? $_POST['payment_amount'] : null;
            $this->registration->payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : null;
            $this->registration->payment_time = !empty($_POST['payment_time']) ? $_POST['payment_time'] : null;
            $this->registration->bank_ref = !empty($_POST['bank_ref']) ? $_POST['bank_ref'] : null;
            $this->registration->sender_name = !empty($_POST['sender_name']) ? $_POST['sender_name'] : null;

            if ($this->registration->create()) {
                $this->sendNotifications();
                return ['status' => 'success', 'message' => 'ลงทะเบียนสำเร็จ! ขอบคุณที่เข้าร่วมงาน Phichai Run 2026'];
            } else {
                return ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลงทะเบียน โปรดลองใหม่อีกครั้ง'];
            }
        }
    }

    private function sendNotifications() {
        // Configuration - REPLACE WITH YOUR TOKENS
        $telegram_token = '8575147540:AAGaztN4i3mZh1_g8E35fOu8Tj1oxIixyOc';
        $telegram_chat_id = '-5016102496';
        $discord_webhook_url = 'https://discordapp.com/api/webhooks/1443091303947698339/spMmqpe3TjyfSn5F-1-N7Qv5Ie9byt25Kh8-AB06NsDelEIbJ5JoJaIEUHD31oEIkZQ1';

        // Prepare Message
        $message = "🏃‍♂️ *New Registration!* 🏃‍♀️\n";
        $message .= "--------------------------------\n";
        $message .= "👤 *Name:* " . $this->registration->full_name . "\n";
        $message .= "🏅 *Category:* " . $this->registration->category . "\n";
        $message .= "👕 *Shirt Size:* " . $this->registration->shirt_size . "\n";
        $message .= "📞 *Phone:* " . $this->registration->phone . "\n";
        $message .= "💰 *Amount:* " . ($this->registration->payment_amount ? number_format($this->registration->payment_amount, 2) : '0.00') . " THB\n";
        $message .= "🚚 *Shipping:* " . ($this->registration->shipping_method == 'POST' ? 'Post (+50)' : 'Self Pickup') . "\n";
        $message .= "📅 *Date:* " . date('Y-m-d H:i:s') . "\n";

        // Send to Telegram
        if ($telegram_token !== 'YOUR_TELEGRAM_BOT_TOKEN') {
            $this->sendTelegram($telegram_token, $telegram_chat_id, $message);
        }

        // Send to Discord
        if ($discord_webhook_url !== 'YOUR_DISCORD_WEBHOOK_URL') {
            $this->sendDiscord($discord_webhook_url, $message);
        }
    }

    private function sendTelegram($token, $chat_id, $message) {
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        curl_close($ch);
    }

    private function sendDiscord($webhook_url, $message) {
        // Discord prefers JSON
        $data = [
            'content' => $message
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhook_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        curl_close($ch);
    }
}
?>