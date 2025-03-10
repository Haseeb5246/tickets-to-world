<?php
require_once '../../private/config.php';
require_once 'init_db.php';

session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cache_expires']) || $_SESSION['cache_expires'] < time()) {
    // Cache statistics for 5 minutes
    $total_bookings = getQueryResult($conn, "SELECT COUNT(*) as count FROM bookings WHERE deleted_at IS NULL");
    $pending_bookings = getQueryResult($conn, "SELECT COUNT(*) as count FROM bookings WHERE status='pending' AND deleted_at IS NULL");
    $confirmed_bookings = getQueryResult($conn, "SELECT COUNT(*) as count FROM bookings WHERE status='confirmed' AND deleted_at IS NULL");
    $cancelled_bookings = getQueryResult($conn, "SELECT COUNT(*) as count FROM bookings WHERE status='cancelled' AND deleted_at IS NULL");
    
    $_SESSION['stats'] = [
        'total' => $total_bookings,
        'pending' => $pending_bookings,
        'confirmed' => $confirmed_bookings,
        'cancelled' => $cancelled_bookings
    ];
    $_SESSION['cache_expires'] = time() + 300; // 5 minutes
}

// Use cached values
$total_bookings = $_SESSION['stats']['total'];
$pending_bookings = $_SESSION['stats']['pending'];
$confirmed_bookings = $_SESSION['stats']['confirmed'];
$cancelled_bookings = $_SESSION['stats']['cancelled'];

// Initialize database structure
if (!initializeDatabase($conn)) {
    die("Failed to initialize database structure");
}

// Modified getQueryResult function
function getQueryResult($conn, $sql) {
    // Check if we're querying with deleted_at
    if (strpos($sql, 'deleted_at') !== false) {
        // Check if column exists
        $result = $conn->query("SHOW COLUMNS FROM bookings LIKE 'deleted_at'");
        if ($result->num_rows === 0) {
            // If column doesn't exist, modify query to remove deleted_at conditions
            $sql = preg_replace('/WHERE deleted_at IS [NOT ]?NULL( AND)?/', 'WHERE ', $sql);
            $sql = rtrim($sql, 'WHERE ');
        }
    }
    
    try {
        $result = $conn->query($sql);
        if ($result === false) {
            error_log("SQL Error: " . $conn->error . " in query: " . $sql);
            return 0;
        }
        return $result->fetch_assoc()['count'];
    } catch (Exception $e) {
        error_log("Exception in query: " . $e->getMessage());
        return 0;
    }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get total pages for pagination
$total_rows = getQueryResult($conn, "SELECT COUNT(*) as count FROM bookings WHERE deleted_at IS NULL");
$total_pages = ceil($total_rows / $per_page);

// Add debug logging
error_log("Database connection status: " . ($conn->connect_error ? 'Failed' : 'Success'));

// Add more detailed debug logging
error_log("DEBUG: Attempting to connect to database: " . DB_NAME);
error_log("DEBUG: Current offset: $offset, Per page: $per_page");

// Test query without pagination first
$test_query = "SELECT COUNT(*) as count FROM bookings";
$test_result = $conn->query($test_query);
if ($test_result) {
    $count = $test_result->fetch_assoc()['count'];
    error_log("DEBUG: Total bookings in database: " . $count);
} else {
    error_log("ERROR: Test query failed: " . $conn->error);
}

// Get recent bookings with pagination and error handling
$check_column = $conn->query("SHOW COLUMNS FROM bookings LIKE 'deleted_at'");
if ($check_column->num_rows == 0) {
    $recent_bookings_query = "SELECT * FROM bookings ORDER BY created_at DESC LIMIT $offset, $per_page";
} else {
    $recent_bookings_query = "SELECT * FROM bookings WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT $offset, $per_page";
}
$stmt = $conn->prepare($recent_bookings_query);

if ($stmt === false) {
    error_log("ERROR: Prepare failed: " . $conn->error);
    $recent_bookings = [];
} else {
    if ($stmt->execute()) {
        $recent_bookings = $stmt->get_result();
        error_log("DEBUG: Found " . $recent_bookings->num_rows . " bookings");
        error_log("DEBUG: SQL Query: " . $recent_bookings_query);
    } else {
        error_log("ERROR: Execute failed: " . $stmt->error);
        $recent_bookings = [];
    }
}

// Debug table structure
$table_query = "DESCRIBE bookings";
$table_result = $conn->query($table_query);
if ($table_result) {
    while ($row = $table_result->fetch_assoc()) {
        error_log("DEBUG: Column structure - " . print_r($row, true));
    }
}

// Get trashed bookings with error handling
try {
    $check_column = $conn->query("SHOW COLUMNS FROM bookings LIKE 'deleted_at'");
    if ($check_column->num_rows === 0) {
        $trashed_bookings = []; // No trash if column doesn't exist
    } else {
        $trashed_bookings_query = "SELECT * FROM bookings WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC";
        $trashed_bookings = $conn->query($trashed_bookings_query);
        if ($trashed_bookings === false) {
            error_log("Trashed bookings query failed: " . $conn->error);
            $trashed_bookings = [];
        }
    }
} catch (Exception $e) {
    error_log("Exception in trashed bookings: " . $e->getMessage());
    $trashed_bookings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <aside class="sidebar">
            <div class="admin-brand">
                <link href="http://localhost:3000/admin/index.php"> <img src= "../assets/images/Tickets-To-World.png" width="170px" Heigh="auto"/> </link>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="file-management.php"><i class="fas fa-folder"></i> File Management</a></li>
                    <li><a href="Flights.php"><i class="fas fa-plane"></i> Manage Flights</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    
                    <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            
            <div class="dashboard-welcome">
                <h1>Welcome back, Admin!</h1>
                <p>You have <?php echo $pending_bookings; ?> pending bookings that need your attention.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Total Bookings</h3>
                        <p><?php echo $total_bookings; ?></p>
                        <span class="trend up">+5% from last week</span>
                    </div>
                </div>

                <div class="stat-card pending">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Pending Bookings</h3>
                        <p><?php echo $pending_bookings; ?></p>
                        <span class="trend">Needs attention</span>
                    </div>
                </div>

                <div class="stat-card confirmed">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Confirmed Bookings</h3>
                        <p><?php echo $confirmed_bookings; ?></p>
                        <span class="trend up">+12% from last week</span>
                    </div>
                </div>

                <div class="stat-card cancelled">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Cancelled Bookings</h3>
                        <p><?php echo $cancelled_bookings; ?></p>
                        <span class="trend down">-2% from last week</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="recent-bookings-card">
                    <div class="card-header">
                        <h2>Bookings Management</h2>
                        <div class="bulk-controls">
                            <div class="bulk-select-group">
                                <input type="checkbox" id="selectAllCheckbox" class="bulk-checkbox">
                                <label for="selectAllCheckbox" class="bulk-label-text">Select All</label>
                            </div>
                            <div class="bulk-buttons-group">
                                <button id="bulkDelete" class="bulk-btn bulk-btn-danger" disabled>
                                    <i class="fas fa-trash"></i>
                                    <span>Delete Selected</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="bookings-table">
                            <thead>
                                <tr>
                                    <th width="40px"></th> <!-- Empty header for checkbox column -->
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Route</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (is_object($recent_bookings) && $recent_bookings->num_rows > 0):
                                    while($booking = $recent_bookings->fetch_assoc()): 
                                        error_log("Processing booking ID: " . $booking['id']);
                                ?>
                                <tr id="booking-<?php echo $booking['id']; ?>" 
                                    onclick="handleRowClick(event, <?php echo $booking['id']; ?>)" 
                                    class="booking-row">
                                    <td>
                                        <input type="checkbox" 
                                               class="booking-checkbox" 
                                               data-id="<?php echo $booking['id']; ?>"
                                               onclick="event.stopPropagation();">
                                    </td>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td>
                                        <div class="customer-info">
                                            <span class="customer-name"><?php echo htmlspecialchars($booking['name']); ?></span>
                                            <span class="customer-email"><?php echo htmlspecialchars($booking['email']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="route-info">
                                            <i class="fas fa-route"></i>
                                            <?php echo htmlspecialchars($booking['from_location']); ?> → <?php echo htmlspecialchars($booking['to_location']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($booking['departure_date'])); ?></td>
                                    <td>
                                        <select class="status-select" data-id="<?php echo $booking['id']; ?>" 
                                                onclick="event.stopPropagation();" 
                                                onchange="updateStatus(this)">
                                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="view-booking.php?id=<?php echo $booking['id']; ?>" 
                                               class="btn-action view" 
                                               title="View Details"
                                               onclick="event.stopPropagation();">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button onclick="event.stopPropagation(); deleteBooking(<?php echo $booking['id']; ?>)" 
                                                    class="btn-action delete" 
                                                    title="Delete Booking">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                    error_log("No bookings to display");
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center">No bookings found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Add pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <!-- Add Trash Section -->
            <div class="trash-section">
                <h2><i class="fas fa-trash"></i> Trash</h2>
                <div class="bulk-controls">
                    <div class="bulk-select-group">
                        <input type="checkbox" id="selectAllTrashCheckbox" class="bulk-checkbox">
                        <label for="selectAllTrashCheckbox" class="bulk-label-text">Select All</label>
                    </div>
                    <div class="bulk-buttons-group">
                        <button id="bulkRestore" class="bulk-btn bulk-btn-primary" disabled>
                            <i class="fas fa-undo"></i>
                            <span>Restore Selected</span>
                        </button>
                        <button id="bulkDeletePermanent" class="bulk-btn bulk-btn-danger" disabled>
                            <i class="fas fa-trash"></i>
                            <span>Delete Permanently</span>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th width="40px"></th> <!-- Empty header for checkbox column -->
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Route</th>
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (is_object($trashed_bookings) && $trashed_bookings->num_rows > 0):
                                while($booking = $trashed_bookings->fetch_assoc()): 
                            ?>
                            <tr id="trash-<?php echo $booking['id']; ?>">
                                <td>
                                    <input type="checkbox" 
                                           class="trash-checkbox" 
                                           data-id="<?php echo $booking['id']; ?>"
                                           onclick="event.stopPropagation();">
                                </td>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td>
                                    <div class="customer-info">
                                        <span class="customer-name"><?php echo htmlspecialchars($booking['name']); ?></span>
                                        <span class="customer-email"><?php echo htmlspecialchars($booking['email']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="route-info">
                                        <i class="fas fa-route"></i>
                                        <?php echo htmlspecialchars($booking['from_location']); ?> → <?php echo htmlspecialchars($booking['to_location']); ?>
                                    </div>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($booking['deleted_at'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <button onclick="restoreBooking(<?php echo $booking['id']; ?>)" class="btn-action restore" title="Restore">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button onclick="deletePermanently(<?php echo $booking['id']; ?>)" class="btn-action delete-permanent" title="Delete Permanently">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="5" class="text-center">No items in trash</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    
    <script>
        // Add this helper function at the top
        function reloadWithAnimation() {
            // Store the current scroll position
            const scrollPos = window.scrollY;
            localStorage.setItem('scrollPosition', scrollPos);
            
            // Fade out
            document.body.style.opacity = '0';
            
            // Hard reload after fade
            setTimeout(() => {
                window.location.href = window.location.href;
            }, 300);
        }

        // Modify the delete, restore, and permanent delete functions to use the new reload
        function deleteBooking(id) {
            if(confirm('Are you sure you want to move this booking to trash?')) {
                fetch('delete_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        booking_id: id,
                        action: 'trash'
                    })
                })
                .then(response => {
                    // First check if the response can be parsed as JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }
                    throw new Error('Invalid response format');
                })
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById('booking-' + id);
                        if (row) {
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                window.location.reload();
                            }, 300);
                        }
                    } else {
                        alert(data.message || 'Failed to move booking to trash');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reload the page anyway since the operation might have succeeded
                    window.location.reload();
                });
            }
        }

        function restoreBooking(id) {
            if(confirm('Are you sure you want to restore this booking?')) {
                fetch('restore_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        booking_id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const row = document.getElementById('trash-' + id);
                        if(row) {
                            row.style.opacity = '0';
                            setTimeout(() => {
                                window.location.reload();
                            }, 300);
                        }
                    } else {
                        alert(data.message || 'Error restoring booking');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing request');
                });
            }
        }

        function deletePermanently(id) {
            if(confirm('Are you sure you want to permanently delete this booking? This action cannot be undone!')) {
                fetch('delete_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        booking_id: id,
                        action: 'permanent'
                    })
                })
                .then(async response => {
                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        return { ok: response.ok, data };
                    } catch (e) {
                        console.error('JSON Parse Error:', text);
                        throw new Error('Invalid JSON response');
                    }
                })
                .then(({ok, data}) => {
                    if (!ok) {
                        throw new Error(data.message || 'Server error');
                    }
                    if (data.success) {
                        const row = document.getElementById('trash-' + id);
                        if (row) {
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                const tbody = row.closest('tbody');
                                if (tbody.children.length <= 1) {
                                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">No items in trash</td></tr>';
                                }
                            }, 300);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to delete booking permanently');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'Error processing request');
                    // Reload the page if the operation might have succeeded despite the error
                    window.location.reload();
                });
            }
        }

        // Add this to handle page load animations
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial styles
            document.body.style.transition = 'opacity 0.3s ease';
            document.body.style.opacity = '0';
            
            // Fade in
            requestAnimationFrame(() => {
                document.body.style.opacity = '1';
            });
            
            // Restore scroll position if it exists
            const scrollPos = localStorage.getItem('scrollPosition');
            if (scrollPos) {
                window.scrollTo(0, parseInt(scrollPos));
                localStorage.removeItem('scrollPosition');
            }
        });

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }

        function updateStatus(select) {
            const bookingId = select.dataset.id;
            const status = select.value;
            
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Status updated successfully');
                } else {
                    alert('Error updating status');
                }
            });
        }

        function handleRowClick(event, id) {
            // Don't navigate if clicking on interactive elements
            if (event.target.closest('.actions') || 
                event.target.closest('.status-select') || 
                event.target.tagName === 'SELECT' || 
                event.target.tagName === 'OPTION') {
                return;
            }
            window.location.href = `view-booking.php?id=${id}`;
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Add bulk action handlers
        document.addEventListener('DOMContentLoaded', function() {
            // ...existing DOMContentLoaded code...

            // Bulk action controls
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const selectAllTrashCheckbox = document.getElementById('selectAllTrashCheckbox');
            const bulkDelete = document.getElementById('bulkDelete');
            const bulkRestore = document.getElementById('bulkRestore');
            const bulkDeletePermanent = document.getElementById('bulkDeletePermanent');

            function updateBulkActionButtons() {
                const activeChecked = document.querySelectorAll('.booking-checkbox:checked').length;
                const trashChecked = document.querySelectorAll('.trash-checkbox:checked').length;
                
                bulkDelete.disabled = activeChecked === 0;
                bulkRestore.disabled = trashChecked === 0;
                bulkDeletePermanent.disabled = trashChecked === 0;
            }

            function updateSelectAllCheckbox(checkboxClass, selectAllCheckbox) {
                const checkboxes = document.querySelectorAll('.' + checkboxClass);
                const checkedBoxes = document.querySelectorAll('.' + checkboxClass + ':checked');
                selectAllCheckbox.checked = checkboxes.length > 0 && checkboxes.length === checkedBoxes.length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkboxes.length !== checkedBoxes.length;
            }

            // Handle select all for active bookings
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.booking-checkbox').forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateBulkActionButtons();
            });

            // Handle select all for trashed bookings
            selectAllTrashCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.trash-checkbox').forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateBulkActionButtons();
            });

            // Update buttons when individual checkboxes change
            document.addEventListener('change', function(e) {
                if (e.target.matches('.booking-checkbox') || e.target.matches('.trash-checkbox')) {
                    updateSelectAllCheckbox('booking-checkbox', selectAllCheckbox);
                    updateSelectAllCheckbox('trash-checkbox', selectAllTrashCheckbox);
                    updateBulkActionButtons();
                }
            });

            // Bulk delete
            bulkDelete.addEventListener('click', function() {
                const selected = Array.from(document.querySelectorAll('.booking-checkbox:checked'))
                    .map(cb => cb.dataset.id);
                
                if (selected.length && confirm(`Are you sure you want to move ${selected.length} bookings to trash?`)) {
                    Promise.all(selected.map(id => 
                        fetch('delete_booking.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ booking_id: id, action: 'trash' })
                        })
                    ))
                    .then(() => window.location.reload());
                }
            });

            // Bulk restore
            bulkRestore.addEventListener('click', function() {
                const selected = Array.from(document.querySelectorAll('.trash-checkbox:checked'))
                    .map(cb => cb.dataset.id);
                
                if (selected.length && confirm(`Are you sure you want to restore ${selected.length} bookings?`)) {
                    Promise.all(selected.map(id => 
                        fetch('restore_booking.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ booking_id: id })
                        })
                    ))
                    .then(() => window.location.reload());
                }
            });

            // Bulk delete permanent
            bulkDeletePermanent.addEventListener('click', function() {
                const selected = Array.from(document.querySelectorAll('.trash-checkbox:checked'))
                    .map(cb => cb.dataset.id);
                
                if (selected.length && confirm(`Are you sure you want to permanently delete ${selected.length} bookings? This cannot be undone!`)) {
                    Promise.all(selected.map(id => 
                        fetch('delete_booking.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ booking_id: id, action: 'permanent' })
                        })
                    ))
                    .then(() => window.location.reload());
                }
            });
        });
    </script>

</body>
</html>