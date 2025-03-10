<?php
session_start();
require_once '../../private/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Define the root directory for file management
$root_dir = realpath(__DIR__ . '/../');

if (isset($_POST['file'])) {
    $file = $_POST['file'];
    $file_path = $root_dir . '/' . $file;
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
    } else {
        echo "File not found.";
        exit();
    }
}

if (isset($_POST['save'])) {
    $file = $_POST['file'];
    $content = $_POST['content'];
    if (file_put_contents($root_dir . '/' . $file, $content)) {
        echo "File edited successfully.";
    } else {
        echo "Error editing file.";
    }
     
    header('Location: file-management.php?dir=' . urlencode(dirname($file)));
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit File</title>
    <link rel="stylesheet" href="../assets/css/files.css">
</head>
<body>
    <div class="container">
        <h2>Edit File: <?php echo $file; ?></h2>
        <form method="POST" action="">
            <input type="hidden" name="file" value="<?php echo $file; ?>">
            <textarea name="content" rows="20" cols="100"><?php echo htmlspecialchars($content); ?></textarea>
            <button type="submit" name="save">Save</button>
        </form>
    </div>
</body>
</html>
</body>

</html>
