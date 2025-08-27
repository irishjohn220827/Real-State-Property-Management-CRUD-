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
  <title>Property Management System - Repairs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <link rel="stylesheet" href="css/style.css">
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
        
        /* Search Container Styles */
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
          <li class="nav-item">
            <a class="nav-link" href="index.php">
              <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="properties.php">
              <i class="fas fa-home me-2"></i>Properties
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="tenants.php">
              <i class="fas fa-users me-2"></i>Tenants
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="leases.php">
              <i class="fas fa-file-contract me-2"></i>Leases
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="repairs.php">
              <i class="fas fa-tools me-2"></i>Repairs
            </a>
          </li>
        </ul>
        <div class="mt-auto p-3">
          <button id="logoutBtn" class="btn btn-danger w-100">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
          </button>
        </div>
      </div>
      
      <!-- Main Content -->
      <div class="col-md-10 p-4 main-content">
        <div class="page-header d-flex justify-content-between align-items-center">
          <h2>Repair Requests</h2>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRepairModal">
            <i class="fas fa-plus me-2"></i>Add Repair Request
          </button>
        </div>

        <!-- Search Box -->
        <div class="search-container mb-4">
          <i class="fas fa-search"></i>
          <input type="text" id="searchInput" class="form-control" placeholder="Search repairs by property, tenant, issue, or status...">
        </div>

        <div class="card shadow-sm">
          <div class="card-body">
            <div class="table-responsive">
        <table class="table table-striped" id="repairs-table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Property</th>
              <th>Tenant</th>
              <th>Issue</th>
              <th>Request Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tBodyRepairs">
            <!-- display ng AJAX -->
          </tbody>
        </table>
      </div>
      </div>
      </div>
      </div>
    </div>
  </div>

  <!-- Add Repair Modal -->
  <div class="modal fade" id="addRepairModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Repair Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="repairForm">
            <div class="mb-3">
              <label for="repairProperty" class="form-label">Property</label>
              <select id="repairProperty" class="form-control"></select>
            </div>
            <div class="mb-3">
              <label for="repairTenant" class="form-label">Tenant</label>
              <select id="repairTenant" class="form-control"></select>
            </div>
            <div class="mb-3">
              <label for="repairIssue" class="form-label">Issue</label>
              <textarea id="repairIssue" class="form-control"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="saveRepair" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Repair Modal -->
  <div class="modal fade" id="editRepairModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Repair</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editRepairForm">
            <input type="hidden" id="editRepairId">
            <div class="mb-3">
              <label for="editRepairStatus" class="form-label">Status</label>
              <select id="editRepairStatus" class="form-control">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="editRepairNotes" class="form-label">Notes</label>
              <textarea id="editRepairNotes" class="form-control"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="updateRepair" class="btn btn-success">Update</button>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  <script text="text/javascript">
    $(document).ready(function() {
      /* =====================================================
         1. INITIALIZE DATATABLE
      ===================================================== */

      // Logout
      $('#logoutBtn').click(function() {
        if (confirm("Are you sure you want to logout?")) {
          window.location.href = "logout.php";
        }
      });

      /* =====================================================
         2. LOAD REPAIRS
      ===================================================== */
      function loadRepairs(filter = null) {
          // Show loading indicator
          $('#tBodyRepairs').html('<tr><td colspan="7" class="text-center py-4">Loading repair requests...</td></tr>');
          
          // Prepare the data to send
          let postData = { get_repair_requests: true };
          
          // Only add filter if it's provided and not empty
          if (filter && Object.keys(filter).length > 0) {
              postData.filter = filter;
          }
          
          $.ajax({
              url: 'request/request.php',
              type: 'POST',
              data: postData,
              success: function (result) {
                  try {
                      var datas = JSON.parse(result);
                      var tBody = '';
                      var cnt = 1;

                      if (!datas || datas.length === 0) {
                          tBody = `<tr><td colspan="7" class="text-center py-4">No repair requests found.</td></tr>`;
                      } else {
                          datas.forEach(function(data) {
                              tBody += `<tr>`;
                              tBody += `<td>${cnt++}</td>`;
                              tBody += `<td>${data.property_address ? data.property_address : 'Property ID: ' + data.property_id}</td>`;
                              tBody += `<td>${data.tenant_name ? data.tenant_name : 'Tenant ID: ' + data.tenant_id}</td>`;
                              tBody += `<td>${data.issue_description 
                                  ? data.issue_description.substring(0, 30) + (data.issue_description.length > 30 ? '...' : '') 
                                  : 'N/A'}</td>`;
                              tBody += `<td>${data.request_date ? new Date(data.request_date).toLocaleDateString() : 'N/A'}</td>`;
                              tBody += `<td><span class="status-badge ${getRepairStatusBadge(data.status)}">${data.status.replace('_', ' ')}</span></td>`;
                              tBody += `<td>
                                          <button class="btn btn-sm btn-primary edit-repair action-btn" data-id="${data.id}" data-bs-toggle="tooltip" title="Update Repair">
                                              <i class="fas fa-edit"></i>
                                          </button>
                                        </td>`;
                              tBody += `</tr>`;
                          });
                      }

                      $('#tBodyRepairs').html(tBody);
                  } catch (e) {
                      console.error("Error parsing JSON:", e, result);
                      $('#tBodyRepairs').html('<tr><td colspan="7" class="text-center text-danger py-4">Error loading repairs. Please try again.</td></tr>');
                  }
              },
              error: function (xhr, status, error) {
                  console.error("Error loading repairs:", error);
                  $('#tBodyRepairs').html('<tr><td colspan="7" class="text-center text-danger py-4">Error loading repairs. Please try again.</td></tr>');
              }
          });
      }

      function getRepairStatusBadge(status) {
        switch(status) {
          case 'pending': return 'bg-warning';
          case 'in_progress': return 'bg-primary';
          case 'completed': return 'bg-success';
          default: return 'bg-secondary';
        }
      }

      /* =====================================================
         3. SEARCH FUNCTIONALITY
      ===================================================== */
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
                      // If search is empty, load all repairs
                      loadRepairs();
                  } else {
                      // Perform search on client-side (filter existing data)
                      filterRepairs(searchTerm);
                  }
              }, 300);
          });
      }
      
      // Filter repairs based on search term (client-side filtering)
      function filterRepairs(searchTerm) {
          // Show loading indicator
          $('#tBodyRepairs').html('<tr><td colspan="7" class="text-center py-4">Searching...</td></tr>');
          
          // Get all repairs first, then filter them
          $.ajax({
              url: 'request/request.php',
              type: 'POST',
              data: { get_repair_requests: true },
              success: function (result) {
                  try {
                      const repairs = JSON.parse(result);
                      const searchTermLower = searchTerm.toLowerCase();
                      
                      // Filter repairs based on search term
                      const filteredRepairs = repairs.filter(repair => 
                          (repair.property_address && repair.property_address.toLowerCase().includes(searchTermLower)) ||
                          (repair.tenant_name && repair.tenant_name.toLowerCase().includes(searchTermLower)) ||
                          (repair.issue_description && repair.issue_description.toLowerCase().includes(searchTermLower)) ||
                          (repair.status && repair.status.toLowerCase().includes(searchTermLower)) ||
                          (repair.request_date && repair.request_date.toLowerCase().includes(searchTermLower))
                      );
                      
                      // Display filtered results
                      displayFilteredRepairs(filteredRepairs);
                  } catch (e) {
                      console.error("Error parsing JSON:", e, result);
                      $('#tBodyRepairs').html('<tr><td colspan="7" class="text-center text-danger py-4">Error searching repairs. Please try again.</td></tr>');
                  }
              },
              error: function (xhr, status, error) {
                  console.error("Error searching repairs:", error);
                  $('#tBodyRepairs').html('<tr><td colspan="7" class="text-center text-danger py-4">Error searching repairs. Please try again.</td></tr>');
              }
          });
      }
      
      // Display filtered repairs in the table
      function displayFilteredRepairs(repairs) {
          var tBody = '';
          var cnt = 1;

          if (!repairs || repairs.length === 0) {
              tBody = `<tr><td colspan="7" class="text-center py-4">No repair requests found matching your search.</td></tr>`;
          } else {
              repairs.forEach(function(data) {
                  tBody += `<tr>`;
                  tBody += `<td>${cnt++}</td>`;
                  tBody += `<td>${data.property_address ? data.property_address : 'Property ID: ' + data.property_id}</td>`;
                  tBody += `<td>${data.tenant_name ? data.tenant_name : 'Tenant ID: ' + data.tenant_id}</td>`;
                  tBody += `<td>${data.issue_description 
                      ? data.issue_description.substring(0, 30) + (data.issue_description.length > 30 ? '...' : '') 
                      : 'N/A'}</td>`;
                  tBody += `<td>${data.request_date ? new Date(data.request_date).toLocaleDateString() : 'N/A'}</td>`;
                  tBody += `<td><span class="status-badge ${getRepairStatusBadge(data.status)}">${data.status.replace('_', ' ')}</span></td>`;
                  tBody += `<td>
                              <button class="btn btn-sm btn-primary edit-repair action-btn" data-id="${data.id}" data-bs-toggle="tooltip" title="Update Repair">
                                  <i class="fas fa-edit"></i>
                              </button>
                            </td>`;
                  tBody += `</tr>`;
              });
          }
          
          $('#tBodyRepairs').html(tBody);
      }

      /* =====================================================
         4. ADD REPAIR
      ===================================================== */
      $('#addRepairModal').on('show.bs.modal', function() {
        // Load properties
        $.post('request/request.php', { get_properties: true }, function(data) {
          const select = $('#repairProperty');
          select.empty().append('<option value="">Select Property</option>');
          data.forEach(property => {
            select.append(`<option value="${property.id}">${property.address}</option>`);
          });
        }, 'json');

        // Load tenants
        $.post('request/request.php', { get_tenants: true }, function(data) {
          const select = $('#repairTenant');
          select.empty().append('<option value="">Select Tenant</option>');
          data.forEach(tenant => {
            select.append(`<option value="${tenant.id}">${tenant.name}</option>`);
          });
        }, 'json');
      });

      $('#saveRepair').click(function() {
        const repairData = {
          property_id: $('#repairProperty').val(),
          tenant_id: $('#repairTenant').val(),
          issue_description: $('#repairIssue').val()
        };
        $.post('request/request.php', { add_repair_request: true, ...repairData }, function(response) {
          if (response.status === 'success') {
            $('#addRepairModal').modal('hide');
            $('#repairForm')[0].reset();
            loadRepairs();
            
            // Clear search and reload all repairs
            $('#searchInput').val('');
            
            alert('Repair request submitted successfully!');
          }
        }, 'json');
      });

      /* =====================================================
         5. EDIT / UPDATE REPAIR
      ===================================================== */
      $(document).on('click', '.edit-repair', function() {
        const repairId = $(this).data('id');
        $.post('request/request.php', { get_repair_requests: true, filter: { id: repairId } }, function(data) {
          if (data.length > 0) {
            const repair = data[0];
            $('#editRepairId').val(repair.id);
            $('#editRepairStatus').val(repair.status);
            $('#editRepairNotes').val(repair.notes || '');
            $('#editRepairModal').modal('show');
          }
        }, 'json');
      });

      $('#updateRepair').click(function() {
        const repairId = $('#editRepairId').val();
        const repairData = {
          status: $('#editRepairStatus').val(),
          notes: $('#editRepairNotes').val() || ''
        };
        $.post('request/request.php', { update_repair_request: true, id: repairId, ...repairData }, function(response) {
          if (response.status === 'success') {
            $('#editRepairModal').modal('hide');
            $('#editRepairForm')[0].reset();
            loadRepairs();
            
            // Clear search and reload all repairs
            $('#searchInput').val('');
            
            alert('Repair request updated successfully!');
          }
        }, 'json');
      });

      /* =====================================================
         6. INITIAL LOAD
      ===================================================== */
      loadRepairs();
      initSearch(); // Initialize search functionality
    });
  </script>
</body>
</html>