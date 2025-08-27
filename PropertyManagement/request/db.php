<?php
class myDB {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $db_name = "propertyManagement";
    public $res;
    public $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->db_name);
        } catch (Exception $e) {
            die("Database connecton failed error!. <br>".$e);
        }
    }

    public function __destruct() {
        
    }

    public function insert($table, $data) {
        try {
            $table_columns = implode(",", array_keys($data));
            $prep=$types="";
            foreach($data as $key => $value) {
                $prep .= "?,";
                $types .= substr(gettype($value), 0, 1);
            }
            $prep = substr($prep, 0, -1);
            $stmt = $this->conn->prepare("INSERT INTO $table ($table_columns) VALUES ($prep)");
            $stmt->bind_param($types, ...array_values($data));
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            die("Error while inserting data! <br>".$e);
        }
    }

    public function select($table, $row="*", $where=NULL) {
        try {
            if(!is_null($where)) {
                $cond = [];
                $types = "";
                $values = [];
                
                foreach($where as $key => $value) {
                    $cond[] = "$key = ?";
                    $types .= substr(gettype($value), 0, 1);
                    $values[] = $value;
                }
                
                $whereClause = implode(" AND ", $cond);
                $stmt = $this->conn->prepare("SELECT $row FROM $table WHERE $whereClause");
                $stmt->bind_param($types, ...$values);
            } else {
                $stmt = $this->conn->prepare("SELECT $row FROM $table");
            }
            $stmt->execute();
            $this->res = $stmt->get_result();
        } catch(Exception $e) {
            die("Error while selecting data! <br>".$e);
        }
    }
    
    public function update($table, $data, $where) {
    try {
        $setParts = [];
        $whereParts = [];
        $params = [];
        $types = "";
        
        // Prepare SET part
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $params[] = $value;
            $types .= substr(gettype($value), 0, 1);
        }
        
        // Prepare WHERE part
        foreach ($where as $key => $value) {
            $whereParts[] = "$key = ?";
            $params[] = $value;
            $types .= substr(gettype($value), 0, 1);
        }
        
        $sql = "UPDATE $table SET " . implode(", ", $setParts) . " WHERE " . implode(" AND ", $whereParts);
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected;
    } catch (Exception $e) {
        die("Error while updating data! <br>".$e);
    }
}


    public function delete($table, $where) {
        try {
            // Prepare WHERE part
            $whereParts = [];
            $params = [];
            $types = "";
            
            foreach ($where as $key => $value) {
                $whereParts[] = "$key = ?";
                $params[] = $value;
                $types .= substr(gettype($value), 0, 1);
            }
            
            $sql = "DELETE FROM $table WHERE " . implode(" AND ", $whereParts);
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            
            return $affected;
        } catch (Exception $e) {
            die("Error while deleting data! <br>".$e);
        }
    }
}
?> 