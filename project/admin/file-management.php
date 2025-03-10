<?php
session_start();
require_once '../../private/config.php';

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Create trash_items table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS trash_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_name VARCHAR(255) NOT NULL,
    original_path VARCHAR(255) NOT NULL,
    trash_name VARCHAR(255) NOT NULL,
    deleted_at DATETIME NOT NULL,
    is_dir TINYINT(1) NOT NULL DEFAULT 0
)";

if (!$conn->query($create_table_sql)) {
    die("Error creating trash_items table: " . $conn->error);
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Define the root directory for file management
$root_dir = realpath(__DIR__ . '/../');
$current_dir = isset($_GET['dir']) ? realpath($root_dir . '/' . $_GET['dir']) : $root_dir;

// Ensure the current directory is within the root directory
if (strpos($current_dir, $root_dir) !== 0) {
    $current_dir = $root_dir;
}

// Ensure trash directory exists
$trash_dir = $root_dir . '/trash';
if (!is_dir($trash_dir)) {
    mkdir($trash_dir, 0777, true);
}

// Add these helper functions at the top of the file
function copyDir($src, $dst) {
    if (!is_dir($dst)) {
        mkdir($dst, 0777, true);
    }
    
    $dir = opendir($src);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $srcFile = $src . '/' . $file;
            $dstFile = $dst . '/' . $file;
            if (is_dir($srcFile)) {
                copyDir($srcFile, $dstFile);
            } else {
                copy($srcFile, $dstFile);
            }
        }
    }
    closedir($dir);
}

function deleteDir($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            deleteDir($path);
        } else {
            unlink($path);
        }
    }
    return rmdir($dir);
}

// Handle file operations
if (isset($_POST['upload'])) {
    $target_file = $current_dir . '/' . basename($_FILES["fileToUpload"]["name"]);
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "File uploaded successfully.";
    } else {
        echo "Error uploading file.";
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Modify the delete operation to handle directories
if (isset($_POST['delete'])) {
    $item = $_POST['file'];
    $item_path = $current_dir . '/' . $item;
    
    // Create a unique identifier for trash items
    $trash_name = time() . '_' . md5($current_dir) . '_' . $item;
    $trash_path = $trash_dir . '/' . $trash_name;
    
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Store metadata in database
        $meta_data = [
            'original_name' => $item,
            'original_path' => str_replace($root_dir, '', $current_dir),
            'trash_name' => $trash_name,
            'deleted_at' => date('Y-m-d H:i:s'),
            'is_dir' => is_dir($item_path)
        ];
        
        // Save metadata to database with error handling
        $sql = "INSERT INTO trash_items (original_name, original_path, trash_name, deleted_at, is_dir) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("ssssi", 
            $meta_data['original_name'], 
            $meta_data['original_path'], 
            $meta_data['trash_name'], 
            $meta_data['deleted_at'], 
            $meta_data['is_dir']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error executing statement: " . $stmt->error);
        }
        
        // Move the file/directory to trash
        if (is_dir($item_path)) {
            copyDir($item_path, $trash_path);
            deleteDir($item_path);
            echo "Directory moved to trash successfully.";
        } else {
            if (!rename($item_path, $trash_path)) {
                throw new Exception("Error moving file to trash");
            }
            echo "File moved to trash successfully.";
        }
        
        // Commit transaction
        $conn->commit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if (isset($_POST['create'])) {
    $new_file = $current_dir . '/' . $_POST['new_file'];
    if (file_exists($new_file)) {
        echo "<div class='error'>Error: File '" . htmlspecialchars($_POST['new_file']) . "' already exists!</div>";
    } else {
        if (file_put_contents($new_file, '') !== false) {
            echo "<div class='success'>File created successfully.</div>";
        } else {
            echo "<div class='error'>Error creating file.</div>";
        }
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if (isset($_POST['create_dir'])) {
    $new_dir = $current_dir . '/' . $_POST['new_dir'];
    if (is_dir($new_dir)) {
        echo "<div class='error'>Error: Directory '" . htmlspecialchars($_POST['new_dir']) . "' already exists!</div>";
    } else {
        if (mkdir($new_dir, 0777, true)) {
            echo "<div class='success'>Directory created successfully.</div>";
        } else {
            echo "<div class='error'>Error creating directory.</div>";
        }
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if (isset($_POST['restore'])) {
    $trash_name = $_POST['file'];
    $trash_path = $trash_dir . '/' . $trash_name;
    
    // Get metadata from database
    $sql = "SELECT * FROM trash_items WHERE trash_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $trash_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $meta_data = $result->fetch_assoc();
    
    if ($meta_data) {
        $restore_path = $root_dir . $meta_data['original_path'] . '/' . $meta_data['original_name'];
        
        // Ensure directory exists
        if (!is_dir(dirname($restore_path))) {
            mkdir(dirname($restore_path), 0777, true);
        }
        
        if (rename($trash_path, $restore_path)) {
            // Remove from trash database
            $sql = "DELETE FROM trash_items WHERE trash_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $trash_name);
            $stmt->execute();
            echo "Item restored successfully.";
        } else {
            echo "Error restoring item.";
        }
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if (isset($_POST['permanent_delete'])) {
    $trash_name = $_POST['file'];
    $trash_path = $trash_dir . '/' . $trash_name;
    
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Get metadata from database
        $sql = "SELECT * FROM trash_items WHERE trash_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $trash_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $meta_data = $result->fetch_assoc();
        
        if ($meta_data) {
            // Check if it's a directory or file
            if ($meta_data['is_dir']) {
                if (is_dir($trash_path)) {
                    deleteDir($trash_path);
                }
            } else {
                if (file_exists($trash_path)) {
                    unlink($trash_path);
                }
            }
            
            // Delete from database
            $sql = "DELETE FROM trash_items WHERE trash_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $trash_name);
            $stmt->execute();
            
            // Commit transaction
            $conn->commit();
            echo "Item permanently deleted.";
        } else {
            throw new Exception("Item not found in trash database.");
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Modify copy/cut operations to handle directories
if (isset($_POST['copy']) || isset($_POST['cut'])) {
    $item = $_POST['file'];
    $item_path = $current_dir . '/' . $item;
    $_SESSION['clipboard'] = [
        'action' => isset($_POST['copy']) ? 'copy' : 'cut',
        'file' => $item_path,
        'is_dir' => is_dir($item_path)
    ];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Modify paste operation to handle directories
if (isset($_POST['paste']) && isset($_SESSION['clipboard'])) {
    $clipboard = $_SESSION['clipboard'];
    $source = $clipboard['file'];
    $target = $current_dir . '/' . basename($source);
    
    if ($clipboard['is_dir']) {
        if ($clipboard['action'] == 'copy') {
            copyDir($source, $target);
            echo "Directory copied successfully.";
        } else {
            rename($source, $target);
            echo "Directory moved successfully.";
        }
    } else {
        if ($clipboard['action'] == 'copy') {
            copy($source, $target);
            echo "File copied successfully.";
        } else {
            rename($source, $target);
            echo "File moved successfully.";
        }
    }
    unset($_SESSION['clipboard']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Function to list files and directories
function list_files($dir) {
    $files = scandir($dir);
    $file_list = [];
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $file_list[] = $file;
        }
    }
    return $file_list;
}

$files = list_files($current_dir);
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Management</title>
    <link rel="stylesheet" href="../assets/css/files.css">
</head>
<body>
    <div class="container">
        <h2>File Management</h2>
        
        <!-- File Management Tools -->
        <div class="management-tools">
            <!-- Upload Section -->
            <div class="upload-section">
                <div class="section-header">Upload File</div>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="fileToUpload">
                    <button type="submit" name="upload">Upload</button>
                </form>
            </div>

            <!-- Create File Section -->
            <div class="create-section">
                <div class="section-header">Create File</div>
                <form method="POST">
                    <input type="text" name="new_file" placeholder="New file name">
                    <button type="submit" name="create">Create File</button>
                </form>
            </div>

            <!-- Create Directory Section -->
            <div class="create-section">
                <div class="section-header">Create Directory</div>
                <form method="POST">
                    <input type="text" name="new_dir" placeholder="New directory name">
                    <button type="submit" name="create_dir">Create Directory</button>
                </form>
            </div>
        </div>

        <!-- File List -->
        <div class="file-list">
            <h3>Files in <?php echo str_replace($root_dir, '', $current_dir); ?></h3>
            <?php if ($current_dir != $root_dir): ?>
                <div class='file-item'>
                    <a href="?dir=<?php echo urlencode(dirname(str_replace($root_dir, '', $current_dir))); ?>">.. (Parent Directory)</a>
                </div>
            <?php endif; ?>
            <?php foreach ($files as $file): ?>
                <div class='file-item'>
                    <?php if (is_dir($current_dir . '/' . $file)): ?>
                        <a href="?dir=<?php echo urlencode(str_replace($root_dir, '', $current_dir . '/' . $file)); ?>"><?php echo $file; ?> (Directory)</a>
                        <div class='actions'>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='file' value='<?php echo $file; ?>'>
                                <button type='submit' name='delete'>Delete</button>
                                <button type='submit' name='copy'>Copy</button>
                                <button type='submit' name='cut'>Cut</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <span><?php echo $file; ?></span>
                        <div class='actions'>
                            <a href='../<?php echo str_replace($root_dir, '', $current_dir . '/' . $file); ?>' target='_blank'>Preview</a>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='file' value='<?php echo $file; ?>'>
                                <button type='submit' name='delete'>Delete</button>
                            </form>
                            <form method='POST' action='edit-file.php' style='display:inline;'>
                                <input type='hidden' name='file' value='<?php echo str_replace($root_dir, '', $current_dir . '/' . $file); ?>'>
                                <button type='submit' name='edit'>Edit</button>
                            </form>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='file' value='<?php echo $file; ?>'>
                                <button type='submit' name='copy'>Copy</button>
                            </form>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='file' value='<?php echo $file; ?>'>
                                <button type='submit' name='cut'>Cut</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paste Button -->
        <?php if (isset($_SESSION['clipboard'])): ?>
            <form method="POST">
                <button type="submit" name="paste">Paste</button>
            </form>
        <?php endif; ?>

        <!-- Trash Section -->
        <div class="trash-section">
            <h3>Trash</h3>
            <?php
            // Get trash items with error handling
            $sql = "SELECT * FROM trash_items ORDER BY deleted_at DESC";
            $trash_items = $conn->query($sql);

            if ($trash_items === false) {
                echo "<div class='error'>Error retrieving trash items: " . $conn->error . "</div>";
            } else {
                if ($trash_items->num_rows > 0) {
                    while ($item = $trash_items->fetch_assoc()) {
                        echo "<div class='file-item'>";
                        echo "<span>" . htmlspecialchars($item['original_name']) . 
                             " (Deleted from: " . htmlspecialchars($item['original_path']) . ")</span>";
                        echo "<div class='actions'>";
                        echo "<form method='POST' style='display:inline;'>";
                        echo "<input type='hidden' name='file' value='" . $item['trash_name'] . "'>";
                        echo "<button type='submit' name='restore'>Restore</button>";
                        echo "<button type='submit' name='permanent_delete'>Delete Permanently</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No items in trash</p>";
                }
            }
            ?>
        </div>
    </div>
        </div>
</body>
</html>
