<aside class="sidebar">
    <div class="admin-brand">
        <a href="index.php">
            <img src="../assets/images/Tickets-To-World.png" alt="Logo" width="170px" height="auto"/>
        </a>
    </div>
    <nav>
        <ul>
            <li <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>
                <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li <?php echo (basename($_SERVER['PHP_SELF']) == 'file-management.php') ? 'class="active"' : ''; ?>>
                <a href="file-management.php"><i class="fas fa-folder"></i> File Management</a>
            </li>
            <li <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'class="active"' : ''; ?>>
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            </li>
            <li class="logout">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </nav>
</aside>
