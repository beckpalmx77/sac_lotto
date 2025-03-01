<?php
class Shop {
    private $conn;
    private $table_name = "shops";

    public $id;
    public $name;
    public $phone_number;
    public $address;
    public $contact_person;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    phone_number = :phone_number,
                    address = :address,
                    contact_person = :contact_person";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->contact_person = htmlspecialchars(strip_tags($this->contact_person));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':contact_person', $this->contact_person);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
