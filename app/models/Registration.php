<?php
class Registration {
    private $conn;
    private $table = 'registrations';

    public $full_name;
    public $gender;
    public $birth_date;
    public $email;
    public $phone;
    public $citizen_id;
    public $prefix;
    public $age;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $address;
    public $category;
    public $shirt_size;
    public $shirt_quantity;
    public $collar_type;
    public $shipping_method;
    public $payment_slip;
    public $payment_amount;
    public $payment_date;
    public $payment_time;
    public $bank_ref;
    public $sender_name;
    public $status = 'pending';
    public $reject_reason;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET prefix = :prefix,
                      full_name = :full_name, 
                      gender = :gender, 
                      birth_date = :birth_date, 
                      age = :age,
                      email = :email, 
                      phone = :phone, 
                      citizen_id = :citizen_id,
                      emergency_contact_name = :emergency_contact_name,
                      emergency_contact_phone = :emergency_contact_phone,
                      address = :address, 
                      category = :category, 
                      shirt_size = :shirt_size,
                      shirt_quantity = :shirt_quantity,
                      collar_type = :collar_type,
                      shipping_method = :shipping_method,
                      payment_slip = :payment_slip,
                      payment_amount = :payment_amount,
                      payment_date = :payment_date,
                      payment_time = :payment_time,
                      bank_ref = :bank_ref,
                      sender_name = :sender_name";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->age = htmlspecialchars(strip_tags($this->age));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->citizen_id = htmlspecialchars(strip_tags($this->citizen_id));
        $this->prefix = $this->prefix ? htmlspecialchars(strip_tags($this->prefix)) : null;
        $this->emergency_contact_name = $this->emergency_contact_name ? htmlspecialchars(strip_tags($this->emergency_contact_name)) : null;
        $this->emergency_contact_phone = $this->emergency_contact_phone ? htmlspecialchars(strip_tags($this->emergency_contact_phone)) : null;
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->category = strip_tags($this->category); // Don't use htmlspecialchars for ENUM fields with & character
        $this->shirt_size = strip_tags($this->shirt_size); // ENUM field
        $this->shirt_quantity = $this->shirt_quantity ? (int)$this->shirt_quantity : 1;
        $this->collar_type = strip_tags($this->collar_type ?? 'round'); // round or polo
        $this->shipping_method = strip_tags($this->shipping_method); // ENUM field
        $this->payment_slip = htmlspecialchars(strip_tags($this->payment_slip));
        $this->payment_amount = $this->payment_amount ? htmlspecialchars(strip_tags($this->payment_amount)) : null;
        $this->payment_date = $this->payment_date ? htmlspecialchars(strip_tags($this->payment_date)) : null;
        $this->payment_time = $this->payment_time ? htmlspecialchars(strip_tags($this->payment_time)) : null;
        $this->bank_ref = $this->bank_ref ? htmlspecialchars(strip_tags($this->bank_ref)) : null;
        $this->sender_name = $this->sender_name ? htmlspecialchars(strip_tags($this->sender_name)) : null;

        // Bind data
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':birth_date', $this->birth_date);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':citizen_id', $this->citizen_id);
        $stmt->bindParam(':prefix', $this->prefix);
        $stmt->bindParam(':emergency_contact_name', $this->emergency_contact_name);
        $stmt->bindParam(':emergency_contact_phone', $this->emergency_contact_phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':shirt_size', $this->shirt_size);
        $stmt->bindParam(':shirt_quantity', $this->shirt_quantity);
        $stmt->bindParam(':collar_type', $this->collar_type);
        $stmt->bindParam(':shipping_method', $this->shipping_method);
        $stmt->bindParam(':payment_slip', $this->payment_slip);
        $stmt->bindParam(':payment_amount', $this->payment_amount);
        $stmt->bindParam(':payment_date', $this->payment_date);
        $stmt->bindParam(':payment_time', $this->payment_time);
        $stmt->bindParam(':bank_ref', $this->bank_ref);
        $stmt->bindParam(':sender_name', $this->sender_name);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET prefix = :prefix,
                      full_name = :full_name, 
                      gender = :gender, 
                      birth_date = :birth_date, 
                      age = :age,
                      email = :email, 
                      phone = :phone, 
                      citizen_id = :citizen_id,
                      emergency_contact_name = :emergency_contact_name,
                      emergency_contact_phone = :emergency_contact_phone,
                      address = :address, 
                      category = :category, 
                      shirt_size = :shirt_size,
                      shipping_method = :shipping_method,
                      payment_amount = :payment_amount,
                      payment_date = :payment_date,
                      payment_time = :payment_time,
                      status = :status,
                      reject_reason = :reject_reason
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $prefix = !empty($data['prefix']) ? htmlspecialchars(strip_tags($data['prefix'])) : null;
        $full_name = htmlspecialchars(strip_tags($data['full_name']));
        $gender = htmlspecialchars(strip_tags($data['gender']));
        $birth_date = htmlspecialchars(strip_tags($data['birth_date']));
        $age = htmlspecialchars(strip_tags($data['age']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $phone = htmlspecialchars(strip_tags($data['phone']));
        $citizen_id = htmlspecialchars(strip_tags($data['citizen_id']));
        $emergency_contact_name = !empty($data['emergency_contact_name']) ? htmlspecialchars(strip_tags($data['emergency_contact_name'])) : null;
        $emergency_contact_phone = !empty($data['emergency_contact_phone']) ? htmlspecialchars(strip_tags($data['emergency_contact_phone'])) : null;
        $address = htmlspecialchars(strip_tags($data['address']));
        $category = strip_tags($data['category']); // Don't use htmlspecialchars for ENUM fields with & character
        $shirt_size = strip_tags($data['shirt_size']); // ENUM field
        $shipping_method = strip_tags($data['shipping_method']); // ENUM field
        $payment_amount = !empty($data['payment_amount']) ? htmlspecialchars(strip_tags($data['payment_amount'])) : null;
        $payment_date = !empty($data['payment_date']) ? htmlspecialchars(strip_tags($data['payment_date'])) : null;
        $payment_time = !empty($data['payment_time']) ? htmlspecialchars(strip_tags($data['payment_time'])) : null;
        $status = htmlspecialchars(strip_tags($data['status']));
        $reject_reason = !empty($data['reject_reason']) ? htmlspecialchars(strip_tags($data['reject_reason'])) : null;
        $id = htmlspecialchars(strip_tags($id));

        // Bind data
        $stmt->bindParam(':prefix', $prefix);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':birth_date', $birth_date);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':citizen_id', $citizen_id);
        $stmt->bindParam(':emergency_contact_name', $emergency_contact_name);
        $stmt->bindParam(':emergency_contact_phone', $emergency_contact_phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':shirt_size', $shirt_size);
        $stmt->bindParam(':shipping_method', $shipping_method);
        $stmt->bindParam(':payment_amount', $payment_amount);
        $stmt->bindParam(':payment_date', $payment_date);
        $stmt->bindParam(':payment_time', $payment_time);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':reject_reason', $reject_reason);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateStatus($id, $status, $reason = null) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, reject_reason = :reason 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $status = htmlspecialchars(strip_tags($status));
        $reason = $reason ? htmlspecialchars(strip_tags($reason)) : null;
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function checkStatus($phone) {
        // Normalize phone: compare only digits.
        // Use REPLACE chain to remove common separators from stored phone values so they can be matched
        // against a digits-only input.
        $query = "SELECT * FROM " . $this->table . " WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''),' ',''),'(',''),')',''),'+',''),'.','') = :phone ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        // sanitize and keep digits only for the input
        $phone_digits = preg_replace('/\D/', '', $phone);
        $phone_digits = htmlspecialchars(strip_tags($phone_digits));
        $stmt->bindParam(':phone', $phone_digits);
        $stmt->execute();
        return $stmt;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateShippingStatus($id, $is_printed) {
        $query = "UPDATE " . $this->table . " SET is_printed = :is_printed WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $is_printed = (int)$is_printed;
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':is_printed', $is_printed);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function checkCitizenId($cid) {
        $query = "SELECT id FROM " . $this->table . " WHERE citizen_id = :cid LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $cid = htmlspecialchars(strip_tags($cid));
        $stmt->bindParam(':cid', $cid);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getCategoryCounts() {
        $query = "SELECT category, COUNT(*) as count FROM " . $this->table . " GROUP BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>