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
            $this->registration->full_name = $_POST['full_name'];
            $this->registration->bib_name = $_POST['bib_name'];
            $this->registration->gender = $_POST['gender'];
            $this->registration->birth_date = $_POST['birth_date'];
            $this->registration->email = $_POST['email'];
            $this->registration->phone = $_POST['phone'];
            $this->registration->address = $_POST['address'];
            $this->registration->category = $_POST['category'];
            $this->registration->shirt_size = $_POST['shirt_size'];

            if ($this->registration->create()) {
                return ['status' => 'success', 'message' => 'ลงทะเบียนสำเร็จ! ขอบคุณที่เข้าร่วมงาน Phichai Run 2026'];
            } else {
                return ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลงทะเบียน โปรดลองใหม่อีกครั้ง'];
            }
        }
    }
}
?>