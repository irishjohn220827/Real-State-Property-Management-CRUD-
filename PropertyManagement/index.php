<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Management System - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="js/jquery.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0 d-flex flex-column">
                <div class="p-3 text-white">
                    <h4>Property Management</h4>
                </div>
                <ul class="nav flex-column flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="properties.php">
                            <i class="fas fa-home me-2"></i> Properties
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tenants.php">
                            <i class="fas fa-users me-2"></i> Tenants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="leases.php">
                            <i class="fas fa-file-contract me-2"></i> Leases
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="repairs.php">
                            <i class="fas fa-tools me-2"></i> Repairs
                        </a>
                    </li>
                </ul>

                <!-- Logout Button -->
                <div class="mt-auto p-3">
                    <button id="logoutBtn" class="btn btn-danger w-100">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4 main-content">
                <h2 class="mb-4">Dashboard</h2>

                <!-- Dashboard Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card-counter primary">
                            <i class="fa fa-building"></i>
                            <span class="count-numbers" id="total-properties">0</span>
                            <span class="count-name">Properties</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-counter success">
                            <i class="fa fa-key"></i>
                            <span class="count-numbers" id="available-properties">0</span>
                            <span class="count-name">Available</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-counter warning">
                            <i class="fa fa-user-tie"></i>
                            <span class="count-numbers" id="rented-properties">0</span>
                            <span class="count-name">Rented</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-counter danger">
                            <i class="fa fa-tools"></i>
                            <span class="count-numbers" id="pending-repairs">0</span>
                            <span class="count-name">Pending Repairs</span>
                        </div>
                    </div>
                </div>

                <!-- Tables -->
                <div class="row mt-4">
                    <!-- Recent Properties -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">Recent Properties</div>
                            <div class="card-body">
                                <table class="table" id="recent-properties">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Address</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Repairs -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">Pending Repair Requests</div>
                            <div class="card-body">
                                <table class="table" id="pending-repairs-table">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Property</th>
                                            <th>Issue</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div> 
        </div>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Script -->
    <script type="text/javascript">
        $(document).ready(function () {

            // Logout button
            $('#logoutBtn').click(function () {
                if (confirm("Are you sure you want to logout?")) {
                    window.location.href = "logout.php";
                }
            });

            /* =====================================================
               LOAD DASHBOARD STATS + TABLES
            ===================================================== */
            function loadDashboard() {
                
                // Load stats + recent properties
                $.post('request/request.php', { get_dashboard_stats: true }, function (data) {
                    $('#total-properties').text(data.total_properties);
                    $('#available-properties').text(data.available_properties);
                    $('#rented-properties').text(data.rented_properties);
                    $('#pending-repairs').text(data.pending_repairs);

                    // ✅ Fill Recent Properties Table
                    const tbody = $('#recent-properties tbody');
                    tbody.empty();

                    if (!data.recent_properties || data.recent_properties.length === 0) {
                        tbody.append(`<tr><td colspan="4" class="text-center">No properties found</td></tr>`);
                        return;
                    }

                    data.recent_properties.forEach((property, index) => {
                        tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${property.address}</td>
                                <td>${property.type}</td>
                                <td>
                                    <span class="badge ${
                                        property.status === 'available'
                                            ? 'bg-success'
                                            : property.status === 'rented'
                                            ? 'bg-warning'
                                            : 'bg-secondary'
                                    }">
                                        ${property.status}
                                    </span>
                                </td>
                            </tr>
                        `);
                    });
                }, 'json');

                // Pending Repairs
                $.post('request/request.php', { get_repair_requests: true, filter: { status: 'pending' } }, function (data) {
                    const tbody = $('#pending-repairs-table tbody');
                    tbody.empty();

                    if (!data || data.length === 0) {
                        tbody.append(`<tr><td colspan="4" class="text-center">No pending repairs</td></tr>`);
                        return;
                    }

                    data.slice(0, 5).forEach((repair, index) => {
                        tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${repair.property_address || repair.property_id || 'N/A'}</td>
                                <td>${repair.issue_description.substring(0, 30)}...</td>
                                <td>${new Date(repair.request_date).toLocaleDateString()}</td>
                            </tr>
                        `);
                    });
                }, 'json');
            }

            /* =====================================================
               INITIAL LOAD
            ===================================================== */
            loadDashboard();
        });
    </script>
</body>
</html>
