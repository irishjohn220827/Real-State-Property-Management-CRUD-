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
  <title>Property Management System - Tenants</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
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
      box-shadow: 3px 0 10px rgba(0,0,0,0.1);
    }
    .sidebar .nav-link {
      color: #bdc3c7;
      padding: 12px 20px;
      border-left: 4px solid transparent;
      transition: all 0.3s;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: rgba(255,255,255,0.1);
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
    .action-btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
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
    <!-- Sidebar -->
    <div class="col-md-2 sidebar p-0 d-flex flex-column h-100">
      <div class="p-3 text-white">
        <h4>Property Management</h4>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="properties.php"><i class="fas fa-home me-2"></i>Properties</a></li>
        <li class="nav-item"><a class="nav-link active" href="tenants.php"><i class="fas fa-users me-2"></i>Tenants</a></li>
        <li class="nav-item"><a class="nav-link" href="leases.php"><i class="fas fa-file-contract me-2"></i>Leases</a></li>
        <li class="nav-item"><a class="nav-link" href="repairs.php"><i class="fas fa-tools me-2"></i>Repairs</a></li>
      </ul>
      <div class="mt-auto p-3">
        <button id="logoutBtn" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
      </div>
    </div>


    <div class="col-md-10 p-4 main-content">
      <div class="page-header d-flex justify-content-between align-items-center">
        <h2></i>Tenants Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTenantModal">
          <i class="fas fa-plus me-2"></i>Add Tenant
        </button>
      </div>

      <!-- Search Box -->
      <div class="search-container mb-4">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Search tenants by name, email, or phone...">
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="tenants-table">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="tBodyTenants"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="addTenantModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Tenant</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="tenantForm">
          <div class="mb-3">
            <label for="tenantName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="tenantName" required>
          </div>
          <div class="mb-3">
            <label for="tenantEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="tenantEmail" required>
          </div>
          <div class="mb-3">
            <label for="tenantPhone" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" id="tenantPhone" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveTenant">Save Tenant</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="editTenantModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Tenant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editTenantForm">
          <input type="hidden" id="editTenantId">
          <div class="mb-3">
            <label for="editTenantName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="editTenantName" required>
          </div>
          <div class="mb-3">
            <label for="editTenantEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editTenantEmail" required>
          </div>
          <div class="mb-3">
            <label for="editTenantPhone" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" id="editTenantPhone" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="updateTenant">Update Tenant</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
  loadTenants();
  
  // Initialize search functionality
  initSearch();

  $('#logoutBtn').click(function() {
    if (confirm("Are you sure you want to logout?")) {
      window.location.href = "logout.php";
    }
  });

  function loadTenants(filter = null) {
    // Show loading indicator
    $('#tBodyTenants').html('<tr><td colspan="5" class="text-center py-4">Loading tenants...</td></tr>');
    
    // Prepare the data to send
    let postData = { get_tenants: true };
    
    // Only add filter if it's provided and not empty
    if (filter && Object.keys(filter).length > 0) {
      postData.filter = filter;
    }
    
    $.ajax({
      url: 'request/request.php',
      type: 'POST',
      data: postData,
      success: function(result) {
        try {
          var datas = JSON.parse(result);
          var tBody = '';
          var cnt = 1;

          if (datas.length === 0) {
            tBody = `<tr><td colspan="5" class="text-center py-4">No tenants found. <a href="#" data-bs-toggle="modal" data-bs-target="#addTenantModal">Add your first tenant</a>.</td></tr>`;
          } else {
            datas.forEach(function(data) {
              tBody += `<tr>
                <td>${cnt++}</td>
                <td>${data.name}</td>
                <td>${data.email}</td>
                <td>${data.phone}</td>
                <td>
                  <button class="btn btn-sm btn-primary action-btn edit-tenant" data-id="${data.id}" title="Edit"><i class="fas fa-edit"></i></button>
                  <button class="btn btn-sm btn-danger action-btn delete-tenant" data-id="${data.id}" title="Delete"><i class="fas fa-trash"></i></button>
                </td>
              </tr>`;
            });
          }
          $('#tBodyTenants').html(tBody);
        } catch(e) {
          console.error("Error parsing tenants:", e, result);
          $('#tBodyTenants').html('<tr><td colspan="5" class="text-center text-danger py-4">Error loading tenants.</td></tr>');
        }
      },
      error: function(xhr, status, error) {
        console.error("Error loading tenants:", error);
        $('#tBodyTenants').html('<tr><td colspan="5" class="text-center text-danger py-4">Error loading tenants. Please try again.</td></tr>');
      }
    });
  }

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
          // If search is empty, load all tenants
          loadTenants();
        } else {
          // Perform search on client-side (filter existing data)
          filterTenants(searchTerm);
        }
      }, 300);
    });
  }
  
  // Filter tenants based on search term (client-side filtering)
  function filterTenants(searchTerm) {
    // Show loading indicator
    $('#tBodyTenants').html('<tr><td colspan="5" class="text-center py-4">Searching...</td></tr>');
    
    // Get all tenants first, then filter them
    $.ajax({
      url: 'request/request.php',
      type: 'POST',
      data: { get_tenants: true },
      success: function(result) {
        try {
          const tenants = JSON.parse(result);
          const searchTermLower = searchTerm.toLowerCase();
          
          // Filter tenants based on search term
          const filteredTenants = tenants.filter(tenant => 
            tenant.name.toLowerCase().includes(searchTermLower) ||
            tenant.email.toLowerCase().includes(searchTermLower) ||
            tenant.phone.toLowerCase().includes(searchTermLower)
          );
          
          // Display filtered results
          displayFilteredTenants(filteredTenants);
        } catch(e) {
          console.error("Error parsing JSON:", e, result);
          $('#tBodyTenants').html('<tr><td colspan="5" class="text-center text-danger py-4">Error searching tenants. Please try again.</td></tr>');
        }
      },
      error: function(xhr, status, error) {
        console.error("Error searching tenants:", error);
        $('#tBodyTenants').html('<tr><td colspan="5" class="text-center text-danger py-4">Error searching tenants. Please try again.</td></tr>');
      }
    });
  }
  
  // Display filtered tenants in the table
  function displayFilteredTenants(tenants) {
    var tBody = '';
    var cnt = 1;

    if (tenants.length === 0) {
      tBody = `<tr><td colspan="5" class="text-center py-4">No tenants found matching your search.</td></tr>`;
    } else {
      tenants.forEach(function(data) {
        tBody += `<tr>
          <td>${cnt++}</td>
          <td>${data.name}</td>
          <td>${data.email}</td>
          <td>${data.phone}</td>
          <td>
            <button class="btn btn-sm btn-primary action-btn edit-tenant" data-id="${data.id}" title="Edit"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-danger action-btn delete-tenant" data-id="${data.id}" title="Delete"><i class="fas fa-trash"></i></button>
          </td>
        </tr>`;
      });
    }
    
    $('#tBodyTenants').html(tBody);
  }

  $('#saveTenant').click(function() {
    const tenantData = {
      name: $('#tenantName').val(),
      email: $('#tenantEmail').val(),
      phone: $('#tenantPhone').val()
    };
    if (!tenantData.name || !tenantData.email || !tenantData.phone) {
      alert('Please fill all fields');
      return;
    }
    $.post('request/request.php', { add_tenant: true, ...tenantData }, function(response) {
      if(response.status === 'success') {
        $('#addTenantModal').modal('hide');
        $('#tenantForm')[0].reset();
        loadTenants();
        
        // Clear search and reload all tenants
        $('#searchInput').val('');
        
        alert('Tenant added successfully!');
      }
    }, 'json');
  });

  $(document).on('click', '.edit-tenant', function() {
    const tenantId = $(this).data('id');
    $.post('request/request.php', { get_tenants: true, filter: { id: tenantId } }, function(response) {
      const data = response[0];
      if(data && data.id) {
        $('#editTenantId').val(data.id);
        $('#editTenantName').val(data.name);
        $('#editTenantEmail').val(data.email);
        $('#editTenantPhone').val(data.phone);
        $('#editTenantModal').modal('show');
      }
    }, 'json');
  });

  $('#updateTenant').click(function() {
    const tenantId = $('#editTenantId').val();
    const tenantData = {
      id: tenantId,
      name: $('#editTenantName').val(),
      email: $('#editTenantEmail').val(),
      phone: $('#editTenantPhone').val()
    };
    $.post('request/request.php', { update_tenant: true, ...tenantData }, function(response) {
      if(response.status === 'success') {
        $('#editTenantModal').modal('hide');
        loadTenants();
        
        // Clear search and reload all tenants
        $('#searchInput').val('');
        
        alert('Tenant updated successfully!');
      }
    }, 'json');
  });

  $(document).on('click', '.delete-tenant', function() {
    const tenantId = $(this).data('id');
    const tenantName = $(this).closest('tr').find('td:eq(1)').text();
    
    if(confirm(`Are you sure you want to delete the tenant "${tenantName}"? This action cannot be undone.`)) {
      $.post('request/request.php', { delete_tenant: true, id: tenantId }, function(response) {
        if(response.status === 'success') {
          loadTenants();
          
          // Clear search and reload all tenants
          $('#searchInput').val('');
          
          alert('Tenant deleted successfully!');
        }
      }, 'json');
    }
  });
});
</script>
</body>
</html>