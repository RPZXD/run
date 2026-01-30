<?php
class ShirtOrder
{
    private $conn;
    private $table = 'shirt_orders';

    public $id;
    public $order_number;
    public $full_name;
    public $phone;
    public $email;
    public $citizen_id;
    public $address;
    public $shirt_sizes;
    public $shirt_quantity;
    public $collar_type;
    public $shipping_method;
    public $payment_amount;
    public $payment_slip;
    public $payment_date;
    public $payment_time;
    public $bank_ref;
    public $sender_name;
    public $status = 'pending';
    public $tracking_number;
    public $notes;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function generateOrderNumber()
    {
        // Format: SO-YYMMDD-XXXX (e.g., SO-251127-0001)
        $prefix = 'SO-' . date('ymd') . '-';

        // Get the last order number for today
        $query = "SELECT order_number FROM " . $this->table . " 
                  WHERE order_number LIKE :prefix 
                  ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $searchPrefix = $prefix . '%';
        $stmt->bindParam(':prefix', $searchPrefix);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $lastNum = intval(substr($row['order_number'], -4));
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return $prefix . $newNum;
    }

    public function create()
    {
        $this->order_number = $this->generateOrderNumber();

        $query = "INSERT INTO " . $this->table . " 
                  SET order_number = :order_number,
                      full_name = :full_name, 
                      phone = :phone,
                      email = :email,
                      citizen_id = :citizen_id,
                      address = :address, 
                      shirt_sizes = :shirt_sizes,
                      shirt_quantity = :shirt_quantity,
                      collar_type = :collar_type,
                      shipping_method = :shipping_method,
                      payment_amount = :payment_amount,
                      payment_slip = :payment_slip,
                      payment_date = :payment_date,
                      payment_time = :payment_time,
                      bank_ref = :bank_ref,
                      sender_name = :sender_name";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->full_name = htmlspecialchars(strip_tags($this->full_name ?? ''));
        $this->phone = htmlspecialchars(strip_tags($this->phone ?? ''));
        $this->email = $this->email ? htmlspecialchars(strip_tags($this->email)) : null;
        $this->citizen_id = $this->citizen_id ? htmlspecialchars(strip_tags($this->citizen_id)) : null;
        $this->address = htmlspecialchars(strip_tags($this->address ?? ''));
        $this->shirt_sizes = htmlspecialchars(strip_tags($this->shirt_sizes ?? ''));
        $this->shirt_quantity = (int) $this->shirt_quantity;
        $this->collar_type = htmlspecialchars(strip_tags($this->collar_type ?? 'round'));
        $this->shipping_method = htmlspecialchars(strip_tags($this->shipping_method ?? 'SELF'));
        $this->payment_amount = $this->payment_amount ? htmlspecialchars(strip_tags($this->payment_amount)) : null;
        $this->payment_slip = $this->payment_slip ? htmlspecialchars(strip_tags($this->payment_slip)) : null;
        $this->payment_date = $this->payment_date ? htmlspecialchars(strip_tags($this->payment_date)) : null;
        $this->payment_time = $this->payment_time ? htmlspecialchars(strip_tags($this->payment_time)) : null;
        $this->bank_ref = $this->bank_ref ? htmlspecialchars(strip_tags($this->bank_ref)) : null;
        $this->sender_name = $this->sender_name ? htmlspecialchars(strip_tags($this->sender_name)) : null;

        // Bind data
        $stmt->bindParam(':order_number', $this->order_number);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':citizen_id', $this->citizen_id);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':shirt_sizes', $this->shirt_sizes);
        $stmt->bindParam(':shirt_quantity', $this->shirt_quantity);
        $stmt->bindParam(':collar_type', $this->collar_type);
        $stmt->bindParam(':shipping_method', $this->shipping_method);
        $stmt->bindParam(':payment_amount', $this->payment_amount);
        $stmt->bindParam(':payment_slip', $this->payment_slip);
        $stmt->bindParam(':payment_date', $this->payment_date);
        $stmt->bindParam(':payment_time', $this->payment_time);
        $stmt->bindParam(':bank_ref', $this->bank_ref);
        $stmt->bindParam(':sender_name', $this->sender_name);

        if ($stmt->execute()) {
            return $this->order_number;
        }

        return false;
    }

    public function readAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByOrderNumber($orderNumber)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE order_number = :order_number LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_number', $orderNumber);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status, $tracking = null, $notes = null)
    {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, 
                      tracking_number = :tracking,
                      notes = :notes
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $status = htmlspecialchars(strip_tags($status));
        $tracking = $tracking ? htmlspecialchars(strip_tags($tracking)) : null;
        $notes = $notes ? htmlspecialchars(strip_tags($notes)) : null;
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':tracking', $tracking);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $query = "UPDATE " . $this->table . " 
                  SET full_name = :full_name, 
                      phone = :phone,
                      email = :email,
                      citizen_id = :citizen_id,
                      address = :address, 
                      shirt_sizes = :shirt_sizes,
                      shirt_quantity = :shirt_quantity,
                      collar_type = :collar_type,
                      shipping_method = :shipping_method,
                      payment_amount = :payment_amount,
                      status = :status,
                      tracking_number = :tracking_number,
                      notes = :notes
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':citizen_id', $data['citizen_id']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':shirt_sizes', $data['shirt_sizes']);
        $stmt->bindParam(':shirt_quantity', $data['shirt_quantity']);
        $collarType = $data['collar_type'] ?? 'round';
        $stmt->bindParam(':collar_type', $collarType);
        $stmt->bindParam(':shipping_method', $data['shipping_method']);
        $stmt->bindParam(':payment_amount', $data['payment_amount']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':tracking_number', $data['tracking_number']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function checkByPhone($phone)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE REPLACE(REPLACE(REPLACE(phone,'-',''),' ',''),'+','') = :phone 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $phone_digits = preg_replace('/\D/', '', $phone);
        $stmt->bindParam(':phone', $phone_digits);
        $stmt->execute();
        return $stmt;
    }

    public function getStats()
    {
        $stats = [];

        // Total orders
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // By status
        $query = "SELECT status, COUNT(*) as count FROM " . $this->table . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total shirts - Parse each order to exclude non-shirt items for consistency
        $query = "SELECT shirt_sizes, collar_type FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $total_shirts = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sizes_str = $row['shirt_sizes'] ?? '';
            $orderCollar = $row['collar_type'] ?? 'round';
            if (!empty($sizes_str)) {
                $size_pairs = explode(',', $sizes_str);
                foreach ($size_pairs as $pair) {
                    $parts = explode(':', trim($pair));
                    if (count($parts) == 2) {
                        $size = trim($parts[0]);
                        $count = (int) trim($parts[1]);

                        // Normalize
                        if (!empty($size) && strpos($size, '(') === false && $size !== 'No Shirt' && $size !== '-') {
                            $suffix = ($orderCollar === 'polo') ? ' (คอปก)' : ' (คอกลม)';
                            $size .= $suffix;
                        }

                        // Filter out non-shirt items
                        if (empty($size) || $size === 'No Shirt' || $size === 'ไม่รับเสื้อ' || $size === 'ไม่ต้องการเสื้อ' || $size === '-' || $size === 'ไม่รับ')
                            continue;
                        $total_shirts += $count;
                    }
                }
            }
        }
        $stats['total_shirts'] = $total_shirts;

        // Total revenue (exclude pending and cancelled)
        $query = "SELECT SUM(payment_amount) as total_revenue FROM " . $this->table . " WHERE status NOT IN ('pending', 'cancelled')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

        return $stats;
    }

    public function getShippingOrders()
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE shipping_method = 'POST' AND status IN ('paid', 'shipped')
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>