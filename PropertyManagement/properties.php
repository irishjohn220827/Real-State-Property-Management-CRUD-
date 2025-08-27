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
    <title>Property Management System - Properties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
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
                    <h4><i></i>Property Management</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="properties.php">
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
                        <a class="nav-link" href="repairs.php">
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
            
            <div class="col-md-10 p-4 main-content">
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2><i></i>Properties Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
                            <i class="fas fa-plus me-2"></i>Add Property
                        </button>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="search-container mb-4">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search properties by address, type, or status...">
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="properties-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Address</th>
                                        <th>Type</th>
                                        <th>Bedrooms</th>
                                        <th>Bathrooms</th>
                                        <th>Rent</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tBodyProperties">
                                    <!-- Data will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Property Modal -->
    <div class="modal fade" id="addPropertyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Property</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="propertyForm">
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="propertyType" class="form-label">Type</label>
                            <select class="form-select" id="propertyType" required>
                                <option value="" selected disabled>Select Property Type</option>
                                <option value="apartment">Apartment</option>
                                <option value="house">House</option>
                                <option value="condo">Condo</option>
                                <option value="townhouse">Townhouse</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bedrooms" class="form-label">Bedrooms</label>
                                <input type="number" class="form-control" id="bedrooms" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bathrooms" class="form-label">Bathrooms</label>
                                <input type="number" class="form-control" id="bathrooms" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="rent" class="form-label">Monthly Rent ($)</label>
                            <input type="number" step="0.01" class="form-control" id="rent" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" required>
                                <option value="" selected disabled>Select Status</option>
                                <option value="available">Available</option>
                                <option value="rented">Rented</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveProperty">Save Property</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Property Modal -->
    <div class="modal fade" id="editPropertyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Property</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPropertyForm">
                        <input type="hidden" id="editPropertyId">
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="editAddress" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPropertyType" class="form-label">Type</label>
                            <select class="form-select" id="editPropertyType" required>
                                <option value="apartment">Apartment</option>
                                <option value="house">House</option>
                                <option value="condo">Condo</option>
                                <option value="townhouse">Townhouse</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editBedrooms" class="form-label">Bedrooms</label>
                                <input type="number" class="form-control" id="editBedrooms" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editBathrooms" class="form-label">Bathrooms</label>
                                <input type="number" class="form-control" id="editBathrooms" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editRent" class="form-label">Monthly Rent ($)</label>
                            <input type="number" step="0.01" class="form-control" id="editRent" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" required>
                                <option value="available">Available</option>
                                <option value="rented">Rented</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateProperty">Update Property</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            loadProperties();
            
            // Initialize search functionality
            initSearch();
        });

        // Logout button
        $('#logoutBtn').click(function () {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        });

        function getStatusBadge(status) {
            switch (status) {
                case 'available': return 'bg-available';
                case 'rented': return 'bg-rented';
                case 'maintenance': return 'bg-maintenance';
                default: return 'bg-secondary';
            }
        }

        function loadProperties(filter = null) {
            // Show loading indicator
            $('#tBodyProperties').html('<tr><td colspan="8" class="text-center py-4">Loading properties...</td></tr>');
            
            // Prepare the data to send
            let postData = { get_properties: true };
            
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

                        if (datas.length === 0) {
                            tBody = `<tr><td colspan="8" class="text-center py-4">No properties found. <a href="#" data-bs-toggle="modal" data-bs-target="#addPropertyModal">Add your first property</a>.</td></tr>`;
                        } else {
                            datas.forEach(function(data) {
                                tBody += `<tr>`;
                                tBody += `<td>${cnt++}</td>`;
                                tBody += `<td>${data.address}</td>`;
                                tBody += `<td>${data.type ? data.type.charAt(0).toUpperCase() + data.type.slice(1) : 'N/A'}</td>`;
                                tBody += `<td>${data.bedrooms}</td>`;
                                tBody += `<td>${data.bathrooms}</td>`;
                                tBody += `<td>$${parseFloat(data.rent).toFixed(2)}</td>`;
                                tBody += `<td><span class="status-badge ${getStatusBadge(data.status)}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span></td>`;
                                tBody += `<td>
                                            <button class="btn btn-sm btn-primary edit-property action-btn" data-id="${data.id}" data-bs-toggle="tooltip" title="Edit Property">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-property action-btn" data-id="${data.id}" data-bs-toggle="tooltip" title="Delete Property">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>`;
                                tBody += `</tr>`;
                            });
                        }
                        
                        $('#tBodyProperties').html(tBody);            
                    } catch (e) {
                        console.error("Error parsing JSON:", e, result);
                        $('#tBodyProperties').html('<tr><td colspan="8" class="text-center text-danger py-4">Error loading properties. Please try again.</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error loading properties:", error);
                    $('#tBodyProperties').html('<tr><td colspan="8" class="text-center text-danger py-4">Error loading properties. Please try again.</td></tr>');
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
                        // If search is empty, load all properties
                        loadProperties();
                    } else {
                        // Perform search on client-side (filter existing data)
                        filterProperties(searchTerm);
                    }
                }, 300);
            });
        }
        
        // Filter properties based on search term (client-side filtering)
        function filterProperties(searchTerm) {
            // Show loading indicator
            $('#tBodyProperties').html('<tr><td colspan="8" class="text-center py-4">Searching...</td></tr>');
            
            // Get all properties first, then filter them
            $.ajax({
                url: 'request/request.php',
                type: 'POST',
                data: { get_properties: true },
                success: function (result) {
                    try {
                        const properties = JSON.parse(result);
                        const searchTermLower = searchTerm.toLowerCase();
                        
                        // Filter properties based on search term
                        const filteredProperties = properties.filter(property => 
                            property.address.toLowerCase().includes(searchTermLower) ||
                            property.type.toLowerCase().includes(searchTermLower) ||
                            property.status.toLowerCase().includes(searchTermLower) ||
                            property.bedrooms.toString().includes(searchTermLower) ||
                            property.bathrooms.toString().includes(searchTermLower) ||
                            property.rent.toString().includes(searchTermLower)
                        );
                        
                        // Display filtered results
                        displayFilteredProperties(filteredProperties);
                    } catch (e) {
                        console.error("Error parsing JSON:", e, result);
                        $('#tBodyProperties').html('<tr><td colspan="8" class="text-center text-danger py-4">Error searching properties. Please try again.</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error searching properties:", error);
                    $('#tBodyProperties').html('<tr><td colspan="8" class="text-center text-danger py-4">Error searching properties. Please try again.</td></tr>');
                }
            });
        }
        
        // Display filtered properties in the table
        function displayFilteredProperties(properties) {
            var tBody = '';
            var cnt = 1;

            if (properties.length === 0) {
                tBody = `<tr><td colspan="8" class="text-center py-4">No properties found matching your search.</td></tr>`;
            } else {
                properties.forEach(function(data) {
                    tBody += `<tr>`;
                    tBody += `<td>${cnt++}</td>`;
                    tBody += `<td>${data.address}</td>`;
                    tBody += `<td>${data.type ? data.type.charAt(0).toUpperCase() + data.type.slice(1) : 'N/A'}</td>`;
                    tBody += `<td>${data.bedrooms}</td>`;
                    tBody += `<td>${data.bathrooms}</td>`;
                    tBody += `<td>$${parseFloat(data.rent).toFixed(2)}</td>`;
                    tBody += `<td><span class="status-badge ${getStatusBadge(data.status)}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span></td>`;
                    tBody += `<td>
                                <button class="btn btn-sm btn-primary edit-property action-btn" data-id="${data.id}" data-bs-toggle="tooltip" title="Edit Property">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-property action-btn" data-id="${data.id}" data-bs-toggle="tooltip" title="Delete Property">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>`;
                    tBody += `</tr>`;
                });
            }
            
            $('#tBodyProperties').html(tBody);
        }

        // Save property
        $('#saveProperty').click(function (e) {
            e.preventDefault();
            
            // Basic validation
            const address = $('#address').val();
            const propertyType = $('#propertyType').val();
            const bedrooms = $('#bedrooms').val();
            const bathrooms = $('#bathrooms').val();
            const rent = $('#rent').val();
            const status = $('#status').val();
            
            if (!address || !propertyType || !bedrooms || !bathrooms || !rent || !status) {
                alert('Please fill all required fields');
                return;
            }
            
            const propertyData = {
                address: address,
                type: propertyType,
                bedrooms: bedrooms,
                bathrooms: bathrooms,
                rent: rent,
                status: status
            };
            
            $.ajax({
                url: 'request/request.php',
                type: 'POST',
                data: { add_property: true, ...propertyData },
                success: function (response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.status === 'success') {
                            $('#addPropertyModal').modal('hide');
                            $('#propertyForm')[0].reset();
                            loadProperties();
                            
                            // Clear search and reload all properties
                            $('#searchInput').val('');
                            
                            // Show success notification
                            alert('Property added successfully!');
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e, response);
                        alert("Unexpected response from server");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error saving property:", error);
                    alert("Error saving property. Check console for details.");
                }
            });
        });

        // Edit property - Show data in edit form
        $(document).on('click', '.edit-property', function () {
            const propertyId = $(this).data('id');

            $.ajax({
                url: 'request/request.php',
                type: 'POST',
                data: { get_properties: true, filter: { id: propertyId } },
                success: function (response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        const data = result[0]; // kasi array ang balik ng get_properties

                        if (data && data.id) {
                            // Populate the edit form with the retrieved data
                            $('#editPropertyId').val(data.id);
                            $('#editAddress').val(data.address || '');
                            $('#editPropertyType').val(data.type || 'apartment');
                            $('#editBedrooms').val(data.bedrooms || 0);
                            $('#editBathrooms').val(data.bathrooms || 0);
                            $('#editRent').val(data.rent || 0);
                            $('#editStatus').val(data.status || 'available');
                            
                            // Show the edit modal
                            $('#editPropertyModal').modal('show');
                        } else {
                            alert('Property not found or invalid data received');
                            console.error('Invalid property data:', data);
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e, response);
                        alert("Error loading property details");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error loading property details:", error);
                    alert("Error loading property details. Check console for details.");
                }
            });
        });


        // Update property
        $('#updateProperty').click(function () {
            const propertyId = $('#editPropertyId').val();
            const propertyData = {
                id: propertyId,
                address: $('#editAddress').val(),
                type: $('#editPropertyType').val(),
                bedrooms: $('#editBedrooms').val(),
                bathrooms: $('#editBathrooms').val(),
                rent: $('#editRent').val(),
                status: $('#editStatus').val()
            };

            // Basic validation
            if (!propertyData.address || !propertyData.type || !propertyData.bedrooms || 
                !propertyData.bathrooms || !propertyData.rent || !propertyData.status) {
                alert('Please fill all required fields');
                return;
            }

            $.ajax({
                url: 'request/request.php',
                type: 'POST',
                data: { update_property: true, ...propertyData },
                success: function (response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.status === 'success') {
                            $('#editPropertyModal').modal('hide');
                            loadProperties();
                            
                            // Clear search and reload all properties
                            $('#searchInput').val('');
                            
                            alert('Property updated successfully!');
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e, response);
                        alert("Unexpected response from server");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error updating property:", error);
                    alert("Error updating property. Check console for details.");
                }
            });
        });

        // Delete property
        $(document).on('click', '.delete-property', function () {
            const propertyId = $(this).data('id');
            const propertyAddress = $(this).closest('tr').find('td:eq(1)').text();
            
            if (confirm(`Are you sure you want to delete the property at "${propertyAddress}"? This action cannot be undone.`)) {
                $.ajax({
                    url: 'request/request.php',
                    type: 'POST',
                    data: { delete_property: true, id: propertyId },
                    success: function (response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.status === 'success') {
                            loadProperties();
                            
                            // Clear search and reload all properties
                            $('#searchInput').val('');
                            
                            // Show success notification
                            alert('Property deleted successfully!');
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e, response);
                        alert("Unexpected response from server");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error deleting property:", error);
                    alert("Error deleting property. Check console for details.");
                }
                });
            }
        });
        

        // Reset form when modal is closed
        $('#addPropertyModal').on('hidden.bs.modal', function () {
            $('#propertyForm')[0].reset();
        });
    </script>
</body>
</html>