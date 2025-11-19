<?php
class Registration {
    private $conn;
    private $table = 'registrations';

    public $full_name;
    public $bib_name;
    public $gender;
    public $birth_date;
    public $email;
    public $phone;
    public $prefix;
    public $club;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $address;
    public $category;
    public $shirt_size;
    public $payment_slip;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET prefix = :prefix,
                      full_name = :full_name, 
                      bib_name = :bib_name, 
                      gender = :gender, 
                      birth_date = :birth_date, 
                      email = :email, 
                      phone = :phone, 
                      club = :club,
                      emergency_contact_name = :emergency_contact_name,
                      emergency_contact_phone = :emergency_contact_phone,
                      address = :address, 
                      category = :category, 
                      shirt_size = :shirt_size,
                      payment_slip = :payment_slip";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->bib_name = htmlspecialchars(strip_tags($this->bib_name));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
    $this->prefix = htmlspecialchars(strip_tags($this->prefix));
    $this->club = htmlspecialchars(strip_tags($this->club));
        $this->emergency_contact_name = htmlspecialchars(strip_tags($this->emergency_contact_name));
        $this->emergency_contact_phone = htmlspecialchars(strip_tags($this->emergency_contact_phone));
        $this->medical_condition = htmlspecialchars(strip_tags($this->medical_condition));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->shirt_size = htmlspecialchars(strip_tags($this->shirt_size));
        $this->payment_slip = htmlspecialchars(strip_tags($this->payment_slip));

        // Bind data
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':bib_name', $this->bib_name);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':birth_date', $this->birth_date);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
    $stmt->bindParam(':prefix', $this->prefix);
    $stmt->bindParam(':club', $this->club);
        $stmt->bindParam(':emergency_contact_name', $this->emergency_contact_name);
        $stmt->bindParam(':emergency_contact_phone', $this->emergency_contact_phone);
        $stmt->bindParam(':medical_condition', $this->medical_condition);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':shirt_size', $this->shirt_size);
        $stmt->bindParam(':payment_slip', $this->payment_slip);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>