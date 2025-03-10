<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Tickets to World'; ?></title>
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Default description'; ?>">
    
    <?php if ($page_settings['indexable']): ?>
    <meta name="description" content="<?php echo htmlspecialchars($page_settings['description']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_settings['keywords']); ?>">
    <meta name="robots" content="index, follow">
    <?php else: ?>
    <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>

    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/header.css">
    <link rel="stylesheet" href="assets/css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="topnav" id="myTopnav">
    <div class="logo-container">
        <a href="index.php" class="logo"> <img src= "./assets/images/Tickets-To-World.png"/> </a>
    </div>
    <nav class="nav-links">
        <a href="index.php" class="active">Home</a>
        <div class="dropdown">
            <a href="#" class="dropbtn">Destinations <i class="fas fa-chevron-down"></i></a>
            <div class="dropdown-content">
                <a href="./uk-to-abuja"><span class="destination-emoji">✈️</span> Abuja</a>
                <a href="./uk-to-abidjan"><span class="destination-emoji">🌍</span> Abidjan</a>
                <a href="./uk-to-accra"><span class="destination-emoji">🌴</span> Accra</a>
                <a href="./uk-to-lagos"><span class="destination-emoji">🌇</span> Lagos</a>
            </div>
        </div>
        <a href="./about-us.php">About Us</a>
        <a href="./contact-us.php">Contact</a>
    </nav>
    <div class="nav-buttons">
        <a href="tel:020 8518 5151" class="phone-link"><i class="fa fa-phone"></i> 020 8518 5151</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars menu-icon"></i>
            <i class="fa fa-times close-icon"></i>
        </a>
    </div>
</header>

</body>
</html>
