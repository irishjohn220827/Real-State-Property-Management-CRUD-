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
  <title>Property Management System - Leases</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="js/jquery.min.js"></script>
  <style>
    :root {
        --primary-color: #3498db;
        --secondary-color: #2c3e50;
        --accent-color: #e74c3c;
        --light-color: #ecf0f1;
        --success-color: #27ae60;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
    }
    
    .sidebar {
        background-color: var(--secondary-color);
        color: white;
        min-height: 100vh;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar .nav-link {
        color: #bdc3c7;
        padding: 12px 20px;
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }
    
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border-left-color: var(--primary-color);
    }
    
    .sidebar .nav-link i {
        width: 24px;
        text-align: center;
    }
    
    .main-content {
        background-color: white;
        min-height: 100vh;
    }
    
    .table th {
        background-color: var(--secondary-color);
        color: white;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .bg-available {
        background-color: #d4edda;
        color: #155724;
    }
    
    .bg-rented {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .bg-maintenance {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .page-header {
        border-bottom: 2px solid var(--light-color);
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    
    .modal-header {
        background-color: var(--secondary-color);
        color: white;
    }
    
    #logoutBtn {
        transition: all 0.3s;
    }
    
    #logoutBtn:hover {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }
    
    .property-card {
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s;
        height: 100%;
    }
    
    .property-card:hover {
        transform: translateY(-5px);
    }
    
    /* Search Styles */
    .search-container {
        position: relative;
        margin-bottom: 20px;
    }
    
    .search-container .form-control {
        padding-left: 40px;
        border-radius: 25px;
    }
    
    .search-container i {
        position: absolute;
        left: 15px;
        top: 12px;
        color: #6c757d;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-2 sidebar p-0 d-flex flex-column h-100">
      <div class="p-3 text-white">
        <h4>Property Management</h4>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="properties.php"><i class="fas fa-home me-2"></i>Properties</a></li>
        <li class="nav-item"><a class="nav-link" href="tenants.php"><i class="fas fa-users me-2"></i>Tenants</a></li>
        <li class="nav-item"><a class="nav-link active" href="leases.php"><i class="fas fa-file-contract me-2"></i>Leases</a></li>
        <li class="nav-item"><a class="nav-link" href="repairs.php"><i class="fas fa-tools me-2"></i>Repairs</a></li>
      </ul>
      <div class="mt-auto p-3">
        <button id="logoutBtn" class="btn btn-danger w-100">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </button>
      </div>
    </div>

    <div class="col-md-10 p-4 main-content">
      <div class="page-header d-flex justify-content-between align-items-center">
        <h2>Leases Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaseModal">
          <i class="fas fa-plus me-2"></i>Add Lease
        </button>
      </div>

      <!-- Search Box -->
      <div class="search-container mb-4">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Search leases by property, tenant, or dates...">
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="leases-table">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Property</th>
                  <th>Tenant</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Monthly Rent</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="tBodyLeases">
                <!-- Data will be loaded here via AJAX -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addLeaseModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Lease</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="leaseForm">
          <div class="mb-3">
            <label for="leaseProperty" class="form-label">Property</label>
            <select class="form-select" id="leaseProperty" required>
              <option value="">Select Property</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="leaseTenant" class="form-label">Tenant</label>
            <select class="form-select" id="leaseTenant" required>
              <option value="">Select Tenant</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="startDate" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="startDate" required>
          </div>
          <div class="mb-3">
            <label for="endDate" class="form-label">End Date</label>
            <input type="date" class="form-control" id="endDate" required>
          </div>
          <div class="mb-3">
            <label for="monthlyRent" class="form-label">Monthly Rent ($)</label>
            <input type="number" step="0.01" class="form-control" id="monthlyRent" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveLease">Save Lease</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editLeaseModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Lease</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editLeaseForm">
          <input type="hidden" id="editLeaseId">
          <div class="mb-3">
            <label for="editLeaseProperty" class="form-label">Property</label>
            <select class="form-select" id="editLeaseProperty" required></select>
          </div>
          <div class="mb-3">
            <label for="editLeaseTenant" class="form-label">Tenant</label>
            <select class="form-select" id="editLeaseTenant" required></select>
          </div>
          <div class="mb-3">
            <label for="editStartDate" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="editStartDate" required>
          </div>
          <div class="mb-3">
            <label for="editEndDate" class="form-label">End Date</label>
            <input type="date" class="form-control" id="editEndDate">
          </div>
          <div class="mb-3">
            <label for="editMonthlyRent" class="form-label">Monthly Rent ($)</label>
            <input type="number" step="0.01" class="form-control" id="editMonthlyRent" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="updateLease">Update Lease</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">
$(document).ready(function () {
  // Initialize search functionality
  initSearch();
  
  // Load leases
  loadLeases();

  $('#logoutBtn').click(function () {
    if (confirm("Are you sure you want to logout?")) {
      window.location.href = "logout.php";
    }
  });

  // Initialize search functionality
  function initSearch() {
    const searchInput = $('#searchInput');
    let searchTimeout;
    
    // Handle search input
    searchInput.on('input', function() {
      clearTimeout(searchTimeout);
      const searchTerm = $(this).val().trim();
      
      // Debounce the search to avoid too many requests
      searchTimeout = setTimeout(() => {
        if (searchTerm.length === 0) {
          // If search is empty, load all leases
          loadLeases();
        } else {
          // Perform search
          filterLeases(searchTerm);
        }
      }, 300);
    });
  }

  // Filter leases based on search term
  function filterLeases(searchTerm) {
    // Show loading indicator
    $('#tBodyLeases').html('<tr><td colspan="7" class="text-center py-4">Searching...</td></tr>');
    
    // Get all leases first, then filter them
    $.ajax({
      url: 'request/request.php',
      type: 'POST',
      data: { get_leases: true, with_details: true },
      success: function (result) {
        try {
          const leases = JSON.parse(result);
          const searchTermLower = searchTerm.toLowerCase();
          
          // Filter leases based on search term
          const filteredLeases = leases.filter(lease => 
            (lease.property_address && lease.property_address.toLowerCase().includes(searchTermLower)) ||
            (lease.tenant_name && lease.tenant_name.toLowerCase().includes(searchTermLower)) ||
            (lease.start_date && lease.start_date.toLowerCase().includes(searchTermLower)) ||
            (lease.end_date && lease.end_date.toLowerCase().includes(searchTermLower)) ||
            (lease.monthly_rent && lease.monthly_rent.toString().includes(searchTermLower))
          );
          
          // Display filtered results
          displayFilteredLeases(filteredLeases);
        } catch (e) {
          console.error("Error parsing JSON:", e, result);
          $('#tBodyLeases').html('<tr><td colspan="7" class="text-center text-danger py-4">Error searching leases. Please try again.</td></tr>');
        }
      },
      error: function (xhr, status, error) {
        console.error("Error searching leases:", error);
        $('#tBodyLeases').html('<tr><td colspan="7" class="text-center text-danger py-4">Error searching leases. Please try again.</td></tr>');
      }
    });
  }

  // Display filtered leases in the table
  function displayFilteredLeases(leases) {
    let tBody = '';
    let cnt = 1;

    if (leases.length === 0) {
      tBody = `<tr><td colspan="7" class="text-center py-4">No leases found matching your search.</td></tr>`;
    } else {
      leases.forEach(function (lease) {
        const startDate = new Date(lease.start_date);
        const endDate = lease.end_date ? new Date(lease.end_date) : null;
        const monthlyRent = lease.monthly_rent
          ? `$${parseFloat(lease.monthly_rent).toFixed(2)}`
          : '$0.00';

        const statusBadge = endDate
          ? `<span class="status-badge bg-secondary">Ended</span>`
          : `<span class="status-badge bg-success">Active</span>`;

        tBody += `
        <tr>
          <td>${cnt++}</td>
          <td>${lease.property_address || `Property ID: ${lease.property_id}`}</td>
          <td>${lease.tenant_name || `Tenant ID: ${lease.tenant_id}`}</td>
          <td>${startDate.toLocaleDateString()}</td>
          <td>${endDate ? endDate.toLocaleDateString() : statusBadge}</td>
          <td>${monthlyRent}</td>
          <td>
            <button class="btn btn-sm btn-primary edit-lease action-btn"
              data-id="${lease.id}"
              title="Edit Lease">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger end-lease action-btn"
              data-id="${lease.id}"
              data-property-id="${lease.property_id}"
              title="End Lease">
              <i class="fas fa-ban"></i>
            </button>
          </td>
        </tr>`;
      });
    }
    
    $('#tBodyLeases').html(tBody);
  }

  // Load leases into table
  function loadLeases() {
    // Show loading indicator
    $('#tBodyLeases').html('<tr><td colspan="7" class="text-center py-4">Loading leases...</td></tr>');
    
    $.ajax({
      url: 'request/request.php',
      type: 'POST',
      data: { get_leases: true, with_details: true },
      success: function (result) {
        try {
          const datas = JSON.parse(result);
          let tBody = '';
          let cnt = 1;

          if (datas.length === 0) {
            tBody = `
              <tr>
                <td colspan="7" class="text-center py-4">
                  No leases found. 
                  <a href="#" data-bs-toggle="modal" data-bs-target="#addLeaseModal">Add your first lease</a>.
                </td>
              </tr>`;
          } else {
            datas.forEach(function (lease) {
              const startDate = new Date(lease.start_date);
              const endDate = lease.end_date ? new Date(lease.end_date) : null;
              const monthlyRent = lease.monthly_rent
                ? `$${parseFloat(lease.monthly_rent).toFixed(2)}`
                : '$0.00';

              const statusBadge = endDate
                ? `<span class="status-badge bg-secondary">Ended</span>`
                : `<span class="status-badge bg-success">Active</span>`;

              tBody += `
              <tr>
                <td>${cnt++}</td>
                <td>${lease.property_address || `Property ID: ${lease.property_id}`}</td>
                <td>${lease.tenant_name || `Tenant ID: ${lease.tenant_id}`}</td>
                <td>${startDate.toLocaleDateString()}</td>
                <td>${endDate ? endDate.toLocaleDateString() : statusBadge}</td>
                <td>${monthlyRent}</td>
                <td>
                  <button class="btn btn-sm btn-primary edit-lease action-btn"
                    data-id="${lease.id}"
                    title="Edit Lease">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-danger end-lease action-btn"
                    data-id="${lease.id}"
                    data-property-id="${lease.property_id}"
                    title="End Lease">
                    <i class="fas fa-ban"></i>
                  </button>
                </td>
              </tr>`;
            });
          }
          $('#tBodyLeases').html(tBody);
        } catch (e) {
          console.error("Error parsing JSON:", e, result);
          $('#tBodyLeases').html('<tr><td colspan="7" class="text-center text-danger py-4">Error loading leases. Please try again.</td></tr>');
        }
      },
      error: function (xhr, status, error) {
        console.error("Error loading leases:", error);
        $('#tBodyLeases').html('<tr><td colspan="7" class="text-center text-danger py-4">Error loading leases. Please try again.</td></tr>');
      }
    });
  }

  // Edit lease - Show data in edit form
  $(document).on('click', '.edit-lease', function () {
    const leaseId = $(this).data('id');

    $.ajax({
      url: 'request/request.php',
      type: 'POST',
      data: { get_leases: true, filter: { id: leaseId }, with_details: true },
      success: function (response) {
        try {
          const result = typeof response === 'string' ? JSON.parse(response) : response;
          const data = result[0];

          if (data && data.id) {
            $('#editLeaseId').val(data.id);

            // Populate selects for property and tenant
            $.post('request/request.php', { get_properties: true }, function (props) {
              const propSelect = $('#editLeaseProperty');
              propSelect.empty();
              props.forEach(p => {
                propSelect.append(`<option value="${p.id}" ${p.id == data.property_id ? 'selected' : ''}>${p.address} (${p.type})</option>`);
              });
            }, 'json');

            $.post('request/request.php', { get_tenants: true }, function (tenants) {
              const tenantSelect = $('#editLeaseTenant');
              tenantSelect.empty();
              tenants.forEach(t => {
                tenantSelect.append(`<option value="${t.id}" ${t.id == data.tenant_id ? 'selected' : ''}>${t.name} (${t.email})</option>`);
              });
            }, 'json');

            $('#editStartDate').val(data.start_date);
            $('#editEndDate').val(data.end_date || '');
            $('#editMonthlyRent').val(data.monthly_rent);

            $('#editLeaseModal').modal('show');
          } else {
            alert('Lease not found.');
          }
        } catch (e) {
          console.error("Error parsing response:", e, response);
          alert("Error loading lease details");
        }
      }
    });
  });

  // Update lease
  $('#updateLease').click(function () {
    const leaseData = {
      id: $('#editLeaseId').val(),
      property_id: $('#editLeaseProperty').val(),
      tenant_id: $('#editLeaseTenant').val(),
      start_date: $('#editStartDate').val(),
      end_date: $('#editEndDate').val(),
      monthly_rent: $('#editMonthlyRent').val()
    };

    $.post('request/request.php', { update_lease: true, ...leaseData }, function (response) {
      if (response.status === 'success') {
        $('#editLeaseModal').modal('hide');
        loadLeases();
        if (typeof loadProperties === 'function') loadProperties();
        if (typeof loadDashboard === 'function') loadDashboard();
        alert('Lease updated successfully!');
      } else {
        alert('Error: ' + response.message);
      }
    }, 'json');
  });

  // End lease
  $(document).on('click', '.end-lease', function () {
    const leaseId = $(this).data('id');
    const propertyId = $(this).data('property-id');
    if (confirm('Are you sure you want to end this lease?')) {
      $(this).prop('disabled', true);
      $.post('request/request.php', { end_lease: true, id: leaseId, property_id: propertyId }, function (response) {
        if (response.status === 'success') {
          loadLeases();
          if (typeof loadProperties === 'function') loadProperties();
          if (typeof loadDashboard === 'function') loadDashboard();
          alert('Lease ended successfully!');
        }
      }, 'json');
    }
  });

  // Populate selects on modal open
  $('#addLeaseModal').on('show.bs.modal', function () {
    // Properties
    $.post('request/request.php', { get_properties: true, filter: { status: 'available' } }, function (data) {
      const select = $('#leaseProperty');
      select.empty().append('<option value="">Select Property</option>');
      data.forEach(property => {
        select.append(`<option value="${property.id}" data-rent="${property.rent}">${property.address} (${property.type})</option>`);
      });
    }, 'json');

    // Tenants
    $.post('request/request.php', { get_tenants: true }, function (data) {
      const select = $('#leaseTenant');
      select.empty().append('<option value="">Select Tenant</option>');
      data.forEach(tenant => {
        select.append(`<option value="${tenant.id}">${tenant.name} (${tenant.email})</option>`);
      });
    }, 'json');
  });

  // Autofill rent when property selected
  $('#leaseProperty').change(function () {
    const rent = $(this).find(':selected').data('rent');
    if (rent) $('#monthlyRent').val(rent);
  });

  // Save lease
  $('#saveLease').click(function () {
    const leaseData = {
      property_id: $('#leaseProperty').val(),
      tenant_id: $('#leaseTenant').val(),
      start_date: $('#startDate').val(),
      end_date: $('#endDate').val(),
      monthly_rent: $('#monthlyRent').val()
    };

    $.post('request/request.php', { add_lease: true, ...leaseData }, function (response) {
      if (response.status === 'success') {
        $('#addLeaseModal').modal('hide');
        $('#leaseForm')[0].reset();
        loadLeases();
        if (typeof loadProperties === 'function') loadProperties();
        if (typeof loadDashboard === 'function') loadDashboard();
        alert('Lease added successfully!');
      }
    }, 'json');
  });
});
</script>
</body>
</html>