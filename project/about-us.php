<?php
session_start();

// Define required variables for header
$page_settings = [
    'title' => 'About Us - Tickets to World',
    'description' => 'Learn about Tickets to World, your trusted travel partner for finding the best deals on flights, hotels, and vacation packages.',
    'keywords' => 'about us, travel agency, flight bookings, hotel reservations, vacation packages, Tickets to World',
    'current_page' => 'about',
    'indexable' => true
];

// Define constants for footer
define('SITE_NAME', 'Tickets to World');
define('SITE_EMAIL', 'simpleblog42@gmail.com');
define('SITE_PHONE', '020 8518 5151');
define('SITE_ADDRESS', '8 Marlborough Business Centre, 96 George Lane, London, E18 1AD');

include('./includes/header.php');
?>

<style>
    :root {
        --primary-color: #69247C;
        --secondary-color: #DA498D;
        --dark-color: #2C3E50;
        --light-color: #F8F9FA;
        --accent-color: #F9E6CF;
    }

    .about-hero {
        position: relative;
        background: linear-gradient(135deg, rgba(105, 36, 124, 0.95), rgba(218, 73, 141, 0.95)),
                    url('assets/images/about-bg.jpg') center/cover;
        padding: 120px 0 80px;
        color: white;
        text-align: center;
    }

    .about-hero h1 {
        font-size: 3.5em;
        margin-bottom: 20px;
        font-weight: 800;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .about-hero p {
        max-width: 800px;
        margin: 0 auto;
        font-size: 1.2em;
        line-height: 1.6;
    }

    .services-section {
        padding: 80px 0;
        background: var(--light-color);
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .service-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .service-card:hover {
        transform: translateY(-10px);
    }

    .service-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        color: white;
        font-size: 1.5em;
    }

    .values-section {
        padding: 80px 0;
        background: var(--accent-color);
    }

    .values-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .section-title {
        text-align: center;
        color: var(--primary-color);
        font-size: 2.5em;
        margin-bottom: 50px;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .value-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-5px);
    }

    

    .contact-details {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
        margin-top: 40px;
        position: relative;
        z-index: 1;
    }

    .contact-item {
        background: rgba(255, 255, 255, 0);
        padding: 30px;
        border-radius: 15px;
        
        transition: transform 0.3s ease;
    }

    .contact-item:hover {
        transform: translateY(-5px);
    }

    .contact-icon {
        font-size: 2.5em;
        color: var(--accent-color);
        margin-bottom: 15px;
    }

    .contact-item h3 {
        font-size: 1.4em;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .contact-item p {
        font-size: 1.1em;
        opacity: 0.9;
    }

    .contact-item small {
        display: block;
        margin-top: 15px;
        opacity: 0.7;
        font-size: 0.9em;
    }

    @media (max-width: 992px) {
        .contact-details {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .contact-details {
            grid-template-columns: 1fr;
        }
        
        .contact-info {
            padding: 40px 20px;
            margin: 0 0px;
        }
    }

    .about-services-section {
        padding: 80px 0;
        background: var(--light-color);
        margin-bottom: 40px;
    }

    .about-services-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 30px;
    }

    .about-service-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        border-left: 4px solid var(--primary-color);
    }

    .about-service-card:hover {
        transform: translateY(-5px);
    }

    .about-service-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .about-service-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8em;
    }

    .about-service-title {
        font-size: 1.5em;
        color: var(--primary-color);
        margin: 0;
    }

    .about-service-description {
        color: var(--dark-color);
        line-height: 1.6;
        font-size: 1.1em;
    }

    @media (max-width: 992px) {
        .about-services-grid {
            grid-template-columns: 1fr;
        }
    }

    .why-choose-section {
        padding: 30px 0;
        
        position: relative;
        margin-bottom: 20px;
    }

    .why-choose-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 30px;
    }

    .why-choose-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
        margin-top: 50px;
    }

    .why-choose-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(105, 36, 124, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border-left: 4px solid var(--primary-color);
    }

    .why-choose-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(105, 36, 124, 0.15);
    }

    .why-choose-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, transparent 50%, rgba(105, 36, 124, 0.05) 50%);
        border-radius: 0 0 0 100%;
    }

    .why-choose-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        color: white;
        font-size: 2em;
        transform: rotate(-5deg);
    }

    .why-choose-title {
        font-size: 1.6em;
        color: var(--primary-color);
        margin-bottom: 15px;
        font-weight: 700;
    }

    .why-choose-description {
        color: var(--dark-color);
        line-height: 1.7;
        font-size: 1.1em;
    }

    .section-subtitle {
        text-align: center;
        color: var(--dark-color);
        font-size: 1.2em;
        max-width: 800px;
        margin: 0 auto 50px;
        line-height: 1.6;
    }

    @media (max-width: 992px) {
        .why-choose-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .why-choose-card {
            padding: 30px;
        }
    }

    .promise-section {
        padding: 100px 0;
        
        position: relative;
        overflow: hidden;
    }

    .promise-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 30px;
    }

    .promise-header {
        text-align: center;
        max-width: 800px;
        margin: 0 auto 60px;
    }

    .promise-title {
        font-size: 2.5em;
        color: var(--primary-color);
        margin-bottom: 25px;
        position: relative;
        padding-bottom: 20px;
    }

    .promise-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .promise-description {
        font-size: 1.2em;
        color: var(--dark-color);
        line-height: 1.8;
    }

    .core-values-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        margin-top: 50px;
    }

    .value-item {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }

    .value-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(105, 36, 124, 0.15);
    }

    .value-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2em;
        flex-shrink: 0;
    }

    .value-content {
        flex: 1;
    }

    .value-title {
        font-size: 1.4em;
        color: var(--primary-color);
        margin-bottom: 15px;
        font-weight: 700;
    }

    .value-text {
        color: var(--dark-color);
        line-height: 1.6;
        font-size: 1.1em;
    }

    .decorative-bg {
        position: absolute;
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        opacity: 0.05;
        border-radius: 50%;
    }

    .decorative-bg-1 {
        top: -100px;
        left: -100px;
    }

    .decorative-bg-2 {
        bottom: -100px;
        right: -100px;
    }

    @media (max-width: 992px) {
        .core-values-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .value-item {
            flex-direction: column;
            text-align: center;
            align-items: center;
        }
        
        .promise-title {
            font-size: 2em;
        }
    }

    .contact1-section {
        padding: 50px 0 0; /* Remove bottom padding */
        
        position: relative;
        margin-bottom: 80px; /* Add margin to create space for footer */
    }

    .contact1-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .contact1-container {
        margin-bottom: 40px; /* Add bottom margin to container */
    }

    .contact1-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .contact1-title {
        font-size: 2.5em;
        color: var(--primary-color);
        margin-bottom: 20px;
        font-weight: 800;
        position: relative;
        padding-bottom: 20px;
    }

    .contact1-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .contact1-subtitle {
        font-size: 1.2em;
        color: var(--dark-color);
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.8;
    }

    .contact1-container {
        background: white;
        border-radius: 30px;
        box-shadow: 0 20px 60px rgba(105, 36, 124, 0.1);
        overflow: hidden;
        position: relative;
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 0;
    }

    .contact1-info {
        background: linear-gradient(145deg, var(--primary-color), var(--secondary-color));
        padding: 60px 40px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .contact1-info::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .contact1-info-content {
        position: relative;
        z-index: 1;
    }

    .contact1-detail {
        margin-bottom: 40px;
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }

    .contact1-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5em;
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }

    .contact1-text {
        flex: 1;
    }

    .contact1-label {
        font-size: 1.2em;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .contact1-value {
        font-size: 1.1em;
        opacity: 0.9;
        line-height: 1.6;
    }

    .contact1-map {
        padding: 40px;
        background: #f8f9fa;
        height: 100%;
        min-height: 400px;
    }

    .contact1-copyright {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.9em;
        opacity: 0.8;
        text-align: center;
    }

    @media (max-width: 992px) {
        .contact1-container {
            grid-template-columns: 1fr;
        }
        
        .contact1-map {
            min-height: 300px;
        }
    }

    @media (max-width: 768px) {
        .contact1-title {
            font-size: 2em;
        }
        
        .contact1-info {
            padding: 40px 20px;
        }
        
        .contact1-section {
            margin-bottom: 40px;
        }
        
        .footer-spacer {
            height: 40px;
        }
    }

    .footer-spacer {
        height: 60px; /* Adjust this value as needed */
        background: transparent;
    }

    /* Add these new media queries at the end of your existing styles */
    @media (max-width: 480px) {
        .about-hero h1 {
            font-size: 2em;
            padding: 0 15px;
        }

        .about-hero p {
            font-size: 1em;
            padding: 0 15px;
        }

        .about-service-card {
            padding: 20px;
        }

        .about-service-header {
            flex-direction: column;
            text-align: center;
        }

        .about-service-icon {
            margin: 0 auto 15px;
        }

        .about-service-title {
            font-size: 1.2em;
        }

        .about-service-description {
            font-size: 0.95em;
        }

        .section-title {
            font-size: 1.8em;
            padding: 0 15px;
        }

        .section-subtitle {
            font-size: 1em;
            padding: 0 15px;
        }

        .why-choose-card {
            padding: 20px;
        }

        .why-choose-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2em;
        }

        .why-choose-title {
            font-size: 1.2em;
        }

        .why-choose-description {
            font-size: 0.95em;
        }

        .value-item {
            padding: 20px;
            flex-direction: column;
            text-align: center;
        }

        .value-icon {
            margin: 0 auto 15px;
        }

        .value-title {
            font-size: 1.2em;
        }

        .value-text {
            font-size: 0.95em;
        }

        .promise-title {
            font-size: 1.8em;
            padding: 0 15px;
        }

        .promise-description {
            font-size: 1em;
            padding: 0 15px;
        }

        .contact1-title {
            font-size: 1.8em;
            padding: 0 15px;
        }

        .contact1-subtitle {
            font-size: 1em;
            padding: 0 15px;
        }

        .contact1-info {
            padding: 30px 15px;
        }

        .contact1-label {
            font-size: 1.1em;
        }

        .contact1-value {
            font-size: 0.95em;
        }

        .contact1-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2em;
        }

        .services-grid,
        .values-grid {
            padding: 0 15px;
        }

        .about-services-grid,
        .why-choose-grid,
        .core-values-grid {
            padding: 0 15px;
        }

        .contact1-wrapper {
            padding: 0 15px;
        }

        .contact1-map {
            padding: 15px;
            min-height: 250px;
        }

        .contact1-copyright {
            font-size: 0.8em;
        }
    }

    @media (max-width: 320px) {
        .about-hero h1 {
            font-size: 1.8em;
        }

        .about-service-card,
        .why-choose-card,
        .value-item {
            padding: 15px;
        }

        .about-service-icon,
        .why-choose-icon,
        .value-icon {
            width: 40px;
            height: 40px;
            font-size: 1em;
        }

        .contact1-detail {
            gap: 10px;
        }

        .contact1-icon {
            width: 35px;
            height: 35px;
            font-size: 1em;
        }
    }
</style>

<div class="about-hero">
    <div class="container">
        <h1>About Us - Tickets to World</h1>
        <p>Welcome to Tickets to World, your trusted travel partner for finding the best deals on flights, hotels, vacation packages, and more. We aim to make your travel experience as smooth, affordable, and enjoyable as possible. Whether you're planning a business trip, a relaxing vacation, or an adventure to a new destination, we’re here to make the journey easy and stress-free.</p>
    </div>
</div>

<section class="about-services-section">
    <h2 class="section-title">What We Offer</h2>
    <div class="about-services-grid">
        <div class="about-service-card">
            <div class="about-service-header">
                <div class="about-service-icon">
                    <i class="fas fa-plane"></i>
                </div>
                <h3 class="about-service-title">Flight Bookings</h3>
            </div>
            <p class="about-service-description">
                Find the best flight deals to destinations around the globe. We partner with top airlines to offer a variety of options that fit your schedule and budget, whether you're flying for business or leisure.
            </p>
        </div>

        <div class="about-service-card">
            <div class="about-service-header">
                <div class="about-service-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <h3 class="about-service-title">Hotel Reservations</h3>
            </div>
            <p class="about-service-description">
                Choose from a wide selection of accommodations. Whether you're looking for a luxury resort or a budget-friendly hotel, we have a variety of options to meet your needs and preferences.
            </p>
        </div>

        <div class="about-service-card">
            <div class="about-service-header">
                <div class="about-service-icon">
                    <i class="fas fa-suitcase"></i>
                </div>
                <h3 class="about-service-title">Vacation Packages</h3>
            </div>
            <p class="about-service-description">
                Let us help you plan your dream vacation. Our customized vacation packages allow you to bundle flights, hotels, and activities, creating a perfect travel experience within your budget.
            </p>
        </div>

        <div class="about-service-card">
            <div class="about-service-header">
                <div class="about-service-icon">
                    <i class="fas fa-car"></i>
                </div>
                <h3 class="about-service-title">Car Rentals & Transfers</h3>
            </div>
            <p class="about-service-description">
                We offer car rental services and transfers so you can easily get around during your trip. Whether you need a vehicle for a road trip or an airport transfer, we have you covered.
            </p>
        </div>
    </div>
</section>

<section class="why-choose-section">
    <div class="why-choose-container">
        <h2 class="section-title">Why Choose Us?</h2>
        <p class="section-subtitle">Here's why travelers love booking with Tickets to World. We're committed to making your travel experience exceptional from start to finish.</p>
        
        <div class="why-choose-grid">
            <div class="why-choose-card">
                <div class="why-choose-icon">
                    <i class="fas fa-pound-sign"></i>
                </div>
                <h3 class="why-choose-title">Affordable Prices</h3>
                <p class="why-choose-description">
                    We know how important it is to stick to a budget. By working with trusted airlines, hotels, and travel providers, we're able to offer competitive prices and great deals that help you save money while traveling.
                </p>
            </div>

            <div class="why-choose-card">
                <div class="why-choose-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="why-choose-title">Easy Booking Process</h3>
                <p class="why-choose-description">
                    Our user-friendly website makes it quick and simple to search for flights, hotels, vacation packages, and car rentals. Booking your next trip is just a few clicks away, and we make the process as hassle-free as possible.
                </p>
            </div>

            <div class="why-choose-card">
                <div class="why-choose-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="why-choose-title">Expert Support</h3>
                <p class="why-choose-description">
                    Our knowledgeable team is always ready to assist you. Whether you need help with finding the best deals, changing a booking, or getting advice on your trip, we're here to provide expert support every step of the way.
                </p>
            </div>

            <div class="why-choose-card">
                <div class="why-choose-icon">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <h3 class="why-choose-title">Wide Range of Destinations</h3>
                <p class="why-choose-description">
                    No matter where you want to go, we can help. We offer travel options to a wide variety of destinations, from popular tourist spots to hidden gems, ensuring there's always a place for you to explore.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="promise-section">
    <div class="decorative-bg decorative-bg-1"></div>
    <div class="decorative-bg decorative-bg-2"></div>
    
    <div class="promise-container">
        <div class="promise-header">
            <h2 class="promise-title">Our Promise to You</h2>
            <p class="promise-description">
                At Tickets to World, we are committed to providing you with top-notch service and ensuring your travel experience is as smooth as possible. Whether it's a business trip or a well-deserved vacation, we promise to make your journey enjoyable, affordable, and stress-free from start to finish.
            </p>
        </div>

        <div class="core-values-grid">
            <div class="value-item">
                <div class="value-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="value-content">
                    <h3 class="value-title">Customer First</h3>
                    <p class="value-text">We put your needs and satisfaction at the heart of everything we do. We listen to you and work hard to make your travel dreams a reality.</p>
                </div>
            </div>

            <div class="value-item">
                <div class="value-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="value-content">
                    <h3 class="value-title">Transparency</h3>
                    <p class="value-text">We believe in clear and honest pricing. No hidden fees, no surprises—just straightforward deals so you know exactly what you're paying for.</p>
                </div>
            </div>

            <div class="value-item">
                <div class="value-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="value-content">
                    <h3 class="value-title">Reliability</h3>
                    <p class="value-text">We collaborate with trusted partners to ensure your bookings are secure, and your travels go as smoothly as possible.</p>
                </div>
            </div>

            <div class="value-item">
                <div class="value-icon">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <div class="value-content">
                    <h3 class="value-title">Passion for Travel</h3>
                    <p class="value-text">We're passionate about travel and want to help you explore the world. Whether it's your first trip abroad or a return to a favorite destination, we're here to help you make the most of your journey.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact1-section">
    <div class="contact1-wrapper">
        <div class="contact1-header">
            <h2 class="contact1-title">Get in Touch</h2>
            <p class="contact1-subtitle">Have questions or need assistance with booking? Our friendly team is here to help. Feel free to contact us for advice or support, and we'll ensure that your trip planning is as simple as possible.</p>
        </div>
        
        <div class="contact1-container">
            <div class="contact1-info">
                <div class="contact1-info-content">
                    <div class="contact1-detail">
                        <div class="contact1-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact1-text">
                            <div class="contact1-label">Phone Number</div>
                            <div class="contact1-value">020 8518 5151</div>
                        </div>
                    </div>

                    <div class="contact1-detail">
                        <div class="contact1-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact1-text">
                            <div class="contact1-label">Office Hours</div>
                            <div class="contact1-value">Monday - Friday: 9AM-6PM</div>
                        </div>
                    </div>

                    <div class="contact1-detail">
                        <div class="contact1-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact1-text">
                            <div class="contact1-label">Location</div>
                            <div class="contact1-value">8 Marlborough Business Centre, 96 George Lane, London, E18 1AD, United Kingdom</div>
                        </div>
                    </div>

                    <div class="contact1-copyright">
                        ©2003-17 Tickets to World is a Trading Name of Acetrip Ltd
                    </div>
                </div>
            </div>
            
            <div class="contact1-map">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2480.916042316991!2d0.02051221572937317!3d51.5912456796493!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47d8a755c9c6dc1b%3A0x1c8c6c77a5a2e8b0!2s96%20George%20Ln%2C%20London%20E18%201AD%2C%20UK!5e0!3m2!1sen!2s!4v1627900956824!5m2!1sen!2s"
                    width="100%" 
                    height="100%" 
                    style="border:0; border-radius: 15px;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

<!-- Add spacing div before footer -->
<div class="footer-spacer"></div>

<?php include('./includes/footer.php'); ?>
