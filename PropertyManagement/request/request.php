<?php
require "db.php";
$mydb = new myDB();

session_start();

// =======================
// AUTHENTICATION
// =======================

// Register user
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username already exists
    $mydb->select('users', '*', ['username' => $username]);
    if ($mydb->res->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
        exit;
    }

    $mydb->insert('users', ['username' => $username, 'password' => $password]);
    echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    exit;
}

// Login user
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $mydb->select('users', '*', ['username' => $username]);
    $user = $mydb->res->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }
    exit;
}

// ==============================================
// PROPERTY CRUD OPERATIONS
// ==============================================

// Add new property
if (isset($_POST['add_property'])) {
    unset($_POST['add_property']);
    $mydb->insert('properties', [...$_POST]);
    echo json_encode(['status' => 'success', 'message' => 'Property added successfully']);
    exit;
}

// Get properties (with optional filter)
if (isset($_POST['get_properties'])) {
    $where = isset($_POST['filter']) ? $_POST['filter'] : null;
    $mydb->select('properties', '*', $where);
    $properties = [];

    while ($row = $mydb->res->fetch_assoc()) {
        $properties[] = $row;
    }

    echo json_encode($properties);
    exit;
}

// Update existing property
if (isset($_POST['update_property'])) {
    $id = $_POST['id'];
    unset($_POST['update_property'], $_POST['id']);

    // Kunin muna ang kasalukuyang status
    $mydb->select('properties', 'status', ['id' => $id]);
    $current = $mydb->res->fetch_assoc();

    // Update property
    $affected = $mydb->update('properties', $_POST, ['id' => $id]);

    // Kung dati rented at ginawang available → burahin ang lease
    if ($current && $current['status'] === 'rented' && isset($_POST['status']) && $_POST['status'] === 'available') {
        $mydb->delete('leases', ['property_id' => $id]);
    }

    echo json_encode(['status' => 'success', 'affected_rows' => $affected]);
    exit;
}

// Delete property
// Delete property (and associated leases/repair requests)
if (isset($_POST['delete_property'])) {
    try {
        $mydb->conn->begin_transaction();

        // Delete dependent rows first
        $mydb->delete('leases', ['property_id' => $_POST['id']]);
        $mydb->delete('repair_requests', ['property_id' => $_POST['id']]);

        // Then delete the property
        $affected = $mydb->delete('properties', ['id' => $_POST['id']]);

        $mydb->conn->commit();

        echo json_encode(['status' => 'success', 'affected_rows' => $affected]);
        exit;

    } catch (Exception $e) {
        $mydb->conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete property: ' . $e->getMessage()]);
        exit;
    }
}


// ==============================================
// TENANT CRUD OPERATIONS
// ==============================================

// Add new tenant
if (isset($_POST['add_tenant'])) {
    unset($_POST['add_tenant']);
    $mydb->insert('tenants', [...$_POST]);
    echo json_encode(['status' => 'success']);
    exit;
}

// Get tenants (with optional filter)
if (isset($_POST['get_tenants'])) {
    $where = isset($_POST['filter']) ? $_POST['filter'] : null;
    $mydb->select('tenants', '*', $where);
    $tenants = [];

    while ($row = $mydb->res->fetch_assoc()) {
        $tenants[] = $row;
    }

    echo json_encode($tenants);
    exit;
}

// Update existing tenant
if (isset($_POST['update_tenant'])) {
    $id = $_POST['id'];
    unset($_POST['update_tenant'], $_POST['id']);

    $affected = $mydb->update('tenants', $_POST, ['id' => $id]);
    echo json_encode(['status' => 'success', 'affected_rows' => $affected]);
    exit;
}

// Delete tenant (and associated leases/repair requests)
if (isset($_POST['delete_tenant'])) {
    // First delete associated leases and repair requests
    $mydb->delete('leases', ['tenant_id' => $_POST['id']]);
    $mydb->delete('repair_requests', ['tenant_id' => $_POST['id']]);

    // Then delete the tenant
    $affected = $mydb->delete('tenants', ['id' => $_POST['id']]);
    echo json_encode(['status' => 'success', 'affected_rows' => $affected]);
    exit;
}

// ==============================================
// LEASE/RENTAL OPERATIONS
// ==============================================

// Get leases with property and tenant details
if (isset($_POST['get_leases'])) {
    try {
        $query = "SELECT leases.*, 
                         properties.address AS property_address, 
                         tenants.name AS tenant_name 
                  FROM leases
                  LEFT JOIN properties ON leases.property_id = properties.id
                  LEFT JOIN tenants ON leases.tenant_id = tenants.id";

        $where  = [];
        $params = [];
        $types  = '';

        if (isset($_POST['filter'])) {
            foreach ($_POST['filter'] as $key => $value) {
                $where[]  = "leases.$key = ?";
                $params[] = $value;
                $types   .= substr(gettype($value), 0, 1);
            }
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $mydb->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $leases = [];
        while ($row = $result->fetch_assoc()) {
            $leases[] = $row;
        }

        echo json_encode($leases);
        exit;

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

// End lease and mark property as available
if (isset($_POST['end_lease'])) {
    try {
        $mydb->conn->begin_transaction();

        $mydb->delete('leases', ['id' => $_POST['id']]);
        $mydb->update('properties', ['status' => 'available'], ['id' => $_POST['property_id']]);

        $mydb->conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Lease ended successfully']);
        exit;

    } catch (Exception $e) {
        $mydb->conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to end lease: ' . $e->getMessage()]);
        exit;
    }
}

// Add new lease (with property status check)
if (isset($_POST['add_lease'])) {
    try {
        $mydb->conn->begin_transaction();

        $mydb->select('properties', 'status', ['id' => $_POST['property_id']]);
        $property = $mydb->res->fetch_assoc();

        if ($property && $property['status'] === 'rented') {
            throw new Exception('Property is already rented');
        }

        unset($_POST['add_lease']);
        $mydb->insert('leases', [...$_POST]);

        $mydb->update('properties', ['status' => 'rented'], ['id' => $_POST['property_id']]);

        $mydb->conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Lease added successfully']);
        exit;

    } catch (Exception $e) {
        $mydb->conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// Update existing lease
if (isset($_POST['update_lease'])) {
    try {
        $mydb->conn->begin_transaction();
        
        $id = $_POST['id'];
        unset($_POST['update_lease'], $_POST['id']);
        
        // Update the lease
        $affected = $mydb->update('leases', $_POST, ['id' => $id]);
        
        $mydb->conn->commit();
        
        echo json_encode(['status' => 'success', 'affected_rows' => $affected]);
        exit;
        
    } catch (Exception $e) {
        $mydb->conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update lease: ' . $e->getMessage()]);
        exit;
    }
}
// ==============================================
// REPAIR REQUEST OPERATIONS
// ==============================================

// Add new repair request
if (isset($_POST['add_repair_request'])) {
    unset($_POST['add_repair_request']);
    $_POST['status']       = 'pending';
    $_POST['request_date'] = date('Y-m-d H:i:s');

    $mydb->insert('repair_requests', [...$_POST]);
    echo json_encode(['status' => 'success']);
    exit;
}

// Update repair request
if (isset($_POST['update_repair_request'])) {
    $id = $_POST['id'];
    unset($_POST['update_repair_request'], $_POST['id']);

    $affected = $mydb->update('repair_requests', $_POST, ['id' => $id]);
    echo json_encode(['status' => 'success', 'affected_rows' => $affected]);
    exit;
}

// Get repair requests with property and tenant details
if (isset($_POST['get_repair_requests'])) {
    try {
        $query = "SELECT rr.*, 
                         p.address AS property_address, 
                         t.name AS tenant_name 
                  FROM repair_requests rr
                  LEFT JOIN properties p ON rr.property_id = p.id
                  LEFT JOIN tenants t ON rr.tenant_id = t.id";

        $where  = [];
        $params = [];
        $types  = '';

        if (isset($_POST['filter'])) {
            foreach ($_POST['filter'] as $key => $value) {
                $where[]  = "rr.$key = ?";
                $params[] = $value;
                $types   .= substr(gettype($value), 0, 1);
            }
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $mydb->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }

        echo json_encode($requests);
        exit;

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

// ==============================================
// DASHBOARD STATISTICS
// ==============================================

// Get dashboard statistics
if (isset($_POST['get_dashboard_stats'])) {
    $stats = [];

    // Stats
    $mydb->select('properties', 'COUNT(*) as total');
    $stats['total_properties'] = $mydb->res->fetch_assoc()['total'];

    $mydb->select('properties', 'COUNT(*) as rented', ['status' => 'rented']);
    $stats['rented_properties'] = $mydb->res->fetch_assoc()['rented'];

    $mydb->select('properties', 'COUNT(*) as available', ['status' => 'available']);
    $stats['available_properties'] = $mydb->res->fetch_assoc()['available'];

    $mydb->select('repair_requests', 'COUNT(*) as pending', ['status' => 'pending']);
    $stats['pending_repairs'] = $mydb->res->fetch_assoc()['pending'];

    // ✅ Recent Available Properties (latest 5)
    $recent = [];
    $result = $mydb->conn->query("SELECT * FROM properties WHERE status = 'available' ORDER BY id DESC LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        $recent[] = $row;
    }
    $stats['recent_properties'] = $recent;

    echo json_encode($stats);
    exit;
}



// ==============================================
// INVALID REQUEST HANDLER
// ==============================================

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
