<?php
require_once '../private/config.php';
// Before including header.php, we'll define meta information
$pageTitle = "Privacy Policy - Tickets to World";
$pageDescription = "Read our privacy policy to understand how Tickets to World handles and protects your personal information.";
require_once 'includes/header.php';
?>

<style>
    .about-hero {
        background-color: #f8f9fa;
        padding: 60px 0;
        margin-bottom: 40px;
        margin-top: 100px;
    }
    .about-hero h1 {
        color: #333;
        font-size: 2.5rem;
        margin-bottom: 20px;
    }
    .privacy-content {
        padding: 30px 0;
    }
</style>

<main>
    <section class="about-hero">
        <div class="container">
            <h1>Privacy Policy</h1>
        </div>
    </section>
    
    <div class="container privacy-content">
        <!-- Add your privacy policy content here -->
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>