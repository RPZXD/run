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
            $this->registration->bib_name = $_POST['bib_name'];
            $this->registration->prefix = isset($_POST['prefix']) ? $_POST['prefix'] : null;
            $this->registration->gender = $_POST['gender'];
            $this->registration->birth_date = $_POST['birth_date'];
            $this->registration->email = $_POST['email'];
            $this->registration->phone = $_POST['phone'];
            $this->registration->club = isset($_POST['club']) ? $_POST['club'] : null;
            $this->registration->emergency_contact_name = $_POST['emergency_contact_name'];
            $this->registration->emergency_contact_phone = $_POST['emergency_contact_phone'];
            $this->registration->address = $_POST['address'];
            $this->registration->category = $_POST['category'];
            $this->registration->shirt_size = $_POST['shirt_size'];
            $this->registration->payment_slip = $payment_slip;

            if ($this->registration->create()) {
                return ['status' => 'success', 'message' => 'ลงทะเบียนสำเร็จ! ขอบคุณที่เข้าร่วมงาน Phichai Run 2026'];
            } else {
                return ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลงทะเบียน โปรดลองใหม่อีกครั้ง'];
            }
        }
    }
}
?>