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
            $this->registration->address = $_POST['address'];
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
                return ['status' => 'success', 'message' => 'ลงทะเบียนสำเร็จ! ขอบคุณที่เข้าร่วมงาน Phichai Run 2026'];
            } else {
                return ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลงทะเบียน โปรดลองใหม่อีกครั้ง'];
            }
        }
    }
}
?>