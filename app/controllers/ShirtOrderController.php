<?php
class ShirtOrderController {
    private $db;
    private $shirtOrder;

    public function __construct($db) {
        $this->db = $db;
        $this->shirtOrder = new ShirtOrder($db);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle File Upload
            $target_dir = "uploads/shirts/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $payment_slip = "";
            if (isset($_FILES["payment_slip"]) && $_FILES["payment_slip"]["error"] == 0) {
                $target_file = $target_dir . basename($_FILES["payment_slip"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                $check = getimagesize($_FILES["payment_slip"]["tmp_name"]);
                if($check !== false) {
                    $new_filename = uniqid() . "." . $imageFileType;
                    $target_file = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $target_file)) {
                        $payment_slip = $target_file;
                    }
                }
            }

            $this->shirtOrder->full_name = !empty($_POST['full_name']) ? $_POST['full_name'] : (($_POST['prefix'] ?? '') . ($_POST['name'] ?? ''));
            $this->shirtOrder->phone = $_POST['phone'];
            $this->shirtOrder->email = isset($_POST['email']) && !empty($_POST['email']) ? $_POST['email'] : null;
            $this->shirtOrder->citizen_id = isset($_POST['citizen_id']) ? $_POST['citizen_id'] : null;
            $this->shirtOrder->address = !empty($_POST['address']) ? $_POST['address'] : ($_POST['full_address'] ?? '');
            $this->shirtOrder->shirt_sizes = $_POST['shirt_sizes'];
            $this->shirtOrder->shirt_quantity = isset($_POST['shirt_quantity']) ? (int)$_POST['shirt_quantity'] : 1;
            $this->shirtOrder->shipping_method = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : 'SELF';
            $this->shirtOrder->payment_slip = $payment_slip;
            $this->shirtOrder->payment_amount = !empty($_POST['payment_amount']) ? $_POST['payment_amount'] : null;
            $this->shirtOrder->payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : null;
            $this->shirtOrder->payment_time = !empty($_POST['payment_time']) ? $_POST['payment_time'] : null;
            $this->shirtOrder->bank_ref = !empty($_POST['bank_ref']) ? $_POST['bank_ref'] : null;
            $this->shirtOrder->sender_name = !empty($_POST['sender_name']) ? $_POST['sender_name'] : null;

            $orderNumber = $this->shirtOrder->create();
            
            if ($orderNumber) {
                $this->sendNotifications($orderNumber);
                return [
                    'status' => 'success', 
                    'message' => 'สั่งซื้อเสื้อสำเร็จ! หมายเลขคำสั่งซื้อ: ' . $orderNumber,
                    'order_number' => $orderNumber
                ];
            } else {
                return ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการสั่งซื้อ โปรดลองใหม่อีกครั้ง'];
            }
        }
    }

    private function sendNotifications($orderNumber) {
        $telegram_token = '8575147540:AAGaztN4i3mZh1_g8E35fOu8Tj1oxIixyOc';
        $telegram_chat_id = '-5016102496';
        $discord_webhook_url = 'https://discordapp.com/api/webhooks/1443091303947698339/spMmqpe3TjyfSn5F-1-N7Qv5Ie9byt25Kh8-AB06NsDelEIbJ5JoJaIEUHD31oEIkZQ1';

        $message = "👕 *มีคำสั่งซื้อเสื้อใหม่! (New Shirt Order)* 👕\n";
        $message .= "--------------------------------\n";
        $message .= "📦 *เลขที่คำสั่งซื้อ:* " . $orderNumber . "\n";
        $message .= "👤 *ชื่อ-นามสกุล:* " . $this->shirtOrder->full_name . "\n";
        $message .= "👕 *ไซส์:* " . $this->shirtOrder->shirt_sizes . "\n";
        $message .= "🔢 *จำนวน:* " . $this->shirtOrder->shirt_quantity . " ตัว\n";
        $message .= "📞 *เบอร์โทร:* " . $this->shirtOrder->phone . "\n";
        $message .= "💰 *ยอดโอน:* " . ($this->shirtOrder->payment_amount ? number_format($this->shirtOrder->payment_amount, 2) : '0.00') . " บาท\n";
        $message .= "🚚 *การจัดส่ง:* " . ($this->shirtOrder->shipping_method == 'POST' ? 'จัดส่งไปรษณีย์ (+50)' : 'รับด้วยตนเอง') . "\n";
        $message .= "📅 *วันที่:* " . date('Y-m-d H:i:s') . "\n";

        if ($telegram_token !== 'YOUR_TELEGRAM_BOT_TOKEN') {
            $this->sendTelegram($telegram_token, $telegram_chat_id, $message);
        }

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
        $data = ['content' => $message];

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
