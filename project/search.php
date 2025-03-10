<?php
session_start();
require_once '../private/config.php';
require_once 'includes/header.php';

// Handle both POST and stored session data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['search_data'] = [
        'from_location' => $_POST['FDestFrom'] ?? '',
        'to_location' => $_POST['FDestTo'] ?? '',
        'departure_date' => $_POST['departure-date'] ?? '',
        'return_date' => $_POST['arrival-date'] ?? '',
        'FAdult' => $_POST['FAdult'] ?? 1,
        'FChild' => $_POST['FChild'] ?? 0,
        'FInfant' => $_POST['FInfant'] ?? 0,
        'FClsType' => $_POST['FClsType'] ?? 'ECONOMY',
        'FAirLine' => $_POST['FAirLine'] ?? 'ALL',
        'trip_type' => $_POST['trip-type'] ?? 'round-trip'
    ];
    $booking_details = $_SESSION['search_data'];
} else {
    $booking_details = $_SESSION['search_data'] ?? null;
}

// Add cache control headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>


<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<main class="available-flights-container">
    <div class="search-form-wrapper">
        <form class="search-booking-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="searchForm">
            <h2>Find Your Perfect Flight</h2>
            
            <!-- Updated radio button section -->
            <div class="form-row">
                <div class="form-group full-width">
                    <div class="search-page-radio-group">
                        <label class="search-page-radio-label">
                            <input type="radio" 
                                   class="search-page-radio" 
                                   name="trip-type" 
                                   onclick="toggleReturnDate()" 
                                   value="round-trip" 
                                   <?php echo (!$booking_details || $booking_details['trip_type'] === 'round-trip') ? 'checked' : ''; ?>>
                            <span class="search-page-radio-text">
                                <i class="fas fa-exchange-alt"></i> Round Trip
                            </span>
                        </label>
                        <label class="search-page-radio-label">
                            <input type="radio" 
                                   class="search-page-radio" 
                                   name="trip-type" 
                                   onclick="toggleReturnDate()" 
                                   value="one-way" 
                                   <?php echo ($booking_details && $booking_details['trip_type'] === 'one-way') ? 'checked' : ''; ?>>
                            <span class="search-page-radio-text">
                                <i class="fas fa-plane"></i> One Way
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <style>
                :root {
    --deep-purple: #69247C;
    --pink-vivid: #DA498D;
    --sunny-yellow: #FAC67A;
    --peach-cream: #F9E6CF;
    --charcoal-gray: #4A4A4A;
    --white: #FFFFFF;
    --gradient: linear-gradient(135deg, #69247C, #DA498D);
}

/* Main Grid Layout */
.available-flights-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    max-width: 1400px;
    margin: 0px auto 0 !important;
    padding: 20px;
}

/* Search Form */
.search-form-wrapper {
    grid-column: 1;
    position: sticky;
    top: 0px;
    height: fit-content;
}

.search-booking-form {
    background: var(--white);
    padding: 1.2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.08);
}

/* Flights Section */
.flights-section {
    grid-column: 2 / 4;
}

/* Flights Grid */
.flights-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

/* After first row, show 3 columns */
.flights-grid > .flight-card:nth-child(n+3) {
    grid-column: auto;
}

/* Flight Cards */
.flight-card {
    background: var(--white);
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

/* Form Elements */
.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .available-flights-container {
        grid-template-columns: 1fr;
    }
    
    .search-form-wrapper {
        position: relative;
        top: 0;
        max-width: 600px;
        margin: 0 auto 0rem;
    }
    
    .flights-section {
        grid-column: 1;
    }
    
    .flights-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .flights-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}

/* Location Dropdown */
.location-dropdown {
    display: none;
    position: absolute;
    z-index: 1000;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Rest of your existing CSS styles... */
.available-flights-container {
    display: grid;
    grid-template-columns: 400px 1fr;  /* Fixed width for form, rest for flights */
    gap: 2rem;
    max-width: 1400px;
    margin: 0px auto 0;
    padding: 20px;
    align-items: start;
}

/* Search form */
.search-form-wrapper {
    position: sticky;
    top: 0px;  /* Adjust this value based on your header height */
    height: fit-content;
    z-index: 10;
    width: 400px !important;
    margin: 0 !important;
}

.search-booking-form {
    width: 100%;
    background: var(--white);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.08);
}

/* Available flights section */
.flights-section {
    padding-left: 6.5rem;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.flights-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    justify-content: center;
}

.flight-card {
    width: 100%;
    max-width: 450px;
    margin: 0 auto;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .available-flights-container {
        grid-template-columns: 1fr;
        padding: 15px;
    }

    .search-form-wrapper {
        position: relative;
        top: 0;
        width: 100% !important;
        max-width: 600px !important;
        margin: 0 auto 0rem !important;
    }

    .flights-section {
        padding-left: 0;
    }
}

@media (max-width: 992px) {
    .search-form-wrapper {
        width: 100% !important;
        max-width: 900px !important;
        padding: 0 15px;
    }
    
    .flights-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .flight-card {
        max-width: none;
    }
    
    .search-booking-form {
        padding: 1.5rem;
    }
}

@media (max-width: 768px) {
    .flights-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}

.booking-summary {
    background: #f5f5f5;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.search-booking-form {
    position: sticky;
    top:0px;
    height: max-content; /* Only as tall as needed */
    background: var(--white);
    padding: 1.2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.08);
    z-index: 10;
    align-self: start;
}

.search-booking-form:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(105, 36, 124, 0.15);
}

.search-booking-form h2 {
    color: var(--deep-purple);
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    text-align: center;
}

.search-booking-form .form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.search-booking-form .form-group {
    flex: unset;
    min-width: 0;
    margin: 0;
    position: relative;
}

.search-booking-form .form-group.full-width {
    grid-column: 1 / -1;
}

.search-booking-form .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--deep-purple);
    font-size: 0.9rem;
    margin-bottom: 0.4rem;
}

.search-booking-form .form-group input,
.search-booking-form .form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    background: rgba(249, 230, 207, 0.3);
    border: 1px solid rgba(105, 36, 124, 0.1);
    border-radius: 10px;
    padding: 0.6rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.search-booking-form .form-group input:focus,
.search-booking-form .form-group select:focus {
    border-color: var(--pink-vivid);
    box-shadow: 0 0 0 3px rgba(218, 73, 141, 0.1);
    outline: none;
    background: var(--white);
}

.search-booking-form .cta-button {
    width: 100%;
    padding: 0.75rem;
    background: var(--gradient);
    color: var(--white);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.3s;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(105, 36, 124, 0.2);
    transition: all 0.3s ease;
}

.search-booking-form .cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 36, 124, 0.3);
}

/* Custom Radio Button Styles */

/* Remove these conflicting sections completely:
- .trip-type-selector
- .trip-type-label
- .trip-type-radio
- .trip-type-text
- .srch-tab-line
*/

/* Search Page Specific Radio Button Styles */
.search-flight-radio-group {
    display: flex;
    justify-content: space-between;
    padding: 1rem;
    background: linear-gradient(145deg, var(--peach-cream), var(--white));
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(105, 36, 124, 0.05);
}

.search-flight-radio-label {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.8rem;
    margin: 0 0.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-flight-radio-label:hover {
    background: rgba(218, 73, 141, 0.1);
}

.search-flight-radio {
    appearance: none;
    -webkit-appearance: none;
    width: 22px;
    height: 22px;
    border: 2px solid var(--deep-purple);
    border-radius: 50%;
    margin-right: 10px;
    position: relative;
    transition: all 0.3s ease;
}

.search-flight-radio:checked {
    border-color: var(--pink-vivid);
    background: var(--gradient);
}

.search-flight-radio:checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: var(--white);
    border-radius: 50%;
    animation: searchRadioScale 0.3s ease-out;
}

.search-flight-radio-text {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--deep-purple);
    font-weight: 500;
}

.search-flight-radio:checked + .search-flight-radio-text {
    color: var(--pink-vivid);
    font-weight: 600;
}

@keyframes searchRadioScale {
    0% { transform: translate(-50%, -50%) scale(0); }
    50% { transform: translate(-50%, -50%) scale(1.2); }
    100% { transform: translate(-50%, -50%) scale(1); }
}

/* Updated Available Flights Styling */
.available-flights {
    flex: 1;
    min-width: 300px;
    padding: 15px;
    
    border-radius: 20px;
    box-shadow: 0 6px 6px rgba(105, 36, 124, 0.1);
    margin: 0;
}

.available-flights h2 {
    color: var(--deep-purple);
    margin-bottom: 2rem;
    text-align: center;
    font-size: 1.8rem;
}

.flights-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* Always 2 columns */
    gap: 1rem;
    width: 100%;
}

.flight-card {
    background: var(--white);
    padding: 0.8rem;
    border-radius: 10px;
    border: 1px solid rgba(105, 36, 124, 0.1);
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.flight-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(105, 36, 124, 0.15);
}

.airline-info {
    background: var(--white);
    padding: 0.6rem;
    border-radius: 12px;
    margin-bottom: 0.8rem;
    border-bottom: 3px solid var(--pink-vivid);
}

.airline-info h3 {
    color: var(--deep-purple);
    font-size: 1rem;
    margin-bottom: 0.3rem;
    font-weight: 600;
}

.airline-info .route {
    color: var(--charcoal-gray);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.route-info {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.3rem;
    padding: 0.6rem;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 10px;
}

.route-info p {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.2rem 0;
    border-bottom: 1px dashed rgba(105, 36, 124, 0.1);
    font-size: 0.85rem;
}

.route-info p:last-child {
    border-bottom: none;
}

.route-info strong {
    color: var(--deep-purple);
}

.select-flight-btn {
    width: 100%;
    padding: 1rem;
    background: var(--gradient);
    color: var(--white);
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    margin-top: 1rem;
    box-shadow: 0 4px 15px rgba(105, 36, 124, 0.2);
}

.select-flight-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 36, 124, 0.3);
}

/* Container Layout Fixes */
@media (min-width: 1200px) {
    .available-flights-container {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 2rem;
        align-items: start;
    }

    .search-booking-form {
        position: sticky;
        top: 0px;
    }

    .flights-grid {
        grid-template-columns: repeat(2, 1fr); /* Force two columns on larger screens */
    }
}

@media (max-width: 1199px) {
    .available-flights-container {
        grid-template-columns: 1fr;
    }
    
    .search-booking-form {
        position: relative;
        top: 0;
        height: auto;
        max-width: 600px;
        margin: 0 auto 1.5rem;
    }
    
    .flights-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .flights-grid {
        grid-template-columns: 1fr; /* Single column on mobile */
    }
}

/* Price Highlighting */
.route-info p:nth-last-child(2),
.route-info p:last-child {
    font-size: 1.1rem;
    color: var(--pink-vivid);
    font-weight: 600;
    border-top: 2px solid rgba(105, 36, 124, 0.1);
    padding-top: 1rem;
    margin-top: 0.5rem;
}

.available-flights {
    margin-top: 0rem;
    flex: 1 1 500px;
    min-width: 300px;
}

.available-flights h2 {
    margin-bottom: 1rem;
    color: var (--deep-purple);
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
}

.flights-grid {
    display: grid;
    grid-template-columns: 1fr; /* Changed to single column */
    gap: 0rem;
    width: 100%;
}

.flight-card {
    background: var(--white);
    border-radius: 15px;
    padding: 0rem;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.08);
    border: 1px solid rgba(105, 36, 124, 0.05);
}

.flight-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 30px rgba(105, 36, 124, 0.12);
}

.airline-info {
    border-bottom: 1px solid #eee;
    padding-bottom: 0rem;
    margin-bottom: 0rem;
    background: linear-gradient(145deg, var(--peach-cream), var(--white));
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 0rem;
}

.airline-info h3 {
    margin: 0;
    color: var (--deep-purple);
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.flight-number {
    color: var(--pink-vivid);
    font-size: 0.9rem;
}

.flight-details {
    margin: 0rem 0;
}

.time-info, .additional-info {
    margin-bottom: 0rem;
}

.price-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 5px;
    margin-top: 0rem;
    font-weight: bold;
}

.select-flight-btn {
    width: 100%;
    padding: 1rem;
    background: var(--gradient);
    color: #FFFFFF; /* Fixed white color */
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(105, 36, 124, 0.2);
}

.select-flight-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 36, 124, 0.3);
}

.no-flights {
    text-align: center;
    padding: 2rem;
}

.back-btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    margin-top: 1rem;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .available-flights-container {
        margin-top: 0px;
    }
}

@media (max-width: 1024px) {
    .available-flights-container {
        flex-direction: column;
        margin-top: 0px;
        padding: 15px;
        max-width: 95%;
        gap: 0rem;
    }
    .available-flights{
        margin-top: 0;
    }

    .search-booking-form {
        min-width: unset;
        width: 100%;
        max-width: 100%;
        margin: 0 auto 2rem;
        padding: 1.5rem;
        margin-top: 0px;
    }

    .form-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .flights-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
    }

    .flight-card {
        height: auto;
    }
}

@media (max-width: 768px) {
    .form-group {
        flex: 1 1 100%;
    }
    
    .flights-grid {
        grid-template-columns: 1fr;
    }

    .available-flights-container {
        margin-top: 0px;
        padding: 15px;
        flex-direction: column;
    }

    .search-booking-form {
        padding: 1rem;
        position: relative;
        top: 0;
        width: 100%;
        margin-top: 0px !important;
        max-width: 100%;
    }

    .form-row {
        flex-direction: column;
        gap: 1rem;
    }

    .form-group {
        width: 100%;
    }

    .search-page-radio-group {
        padding: 0.5rem;
    }
}

/* Enhanced Mobile and Tablet Responsive Styles */
@media (max-width: 768px) {
    .available-flights-container {
        margin-top: 0px;
        padding: 12px;
        border-radius: 15px;
    }

    .search-booking-form {
        min-width: unset;
        padding: 1.2rem;
        margin: 0 0 1.5rem 0;
    }

    .search-booking-form h2 {
        font-size: 1.3rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.8rem;
    }

    .flights-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .flight-card {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .available-flights-container {
        margin-top: 0px;
        padding: 10px;
    }

    .search-booking-form {
        padding: 1rem;
        border-radius: 12px;
    }

    .search-page-radio-group {
        flex-direction: row;
        padding: 4px;
        gap: 4px;
    }

    .search-page-radio-label {
        padding: 8px 12px;
    }

    .search-page-radio-text {
        font-size: 0.85rem;
        gap: 6px;
    }

    .search-page-radio-text i {
        font-size: 0.9rem;
    }

    .flight-card {
        margin-bottom: 0.8rem;
    }

    .airline-info {
        padding: 0.8rem;
    }

    .select-flight-btn {
        padding: 0.8rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 375px) {
    .available-flights-container {
        margin-top: 0px;
        padding: 8px;
    }

    .search-booking-form h2 {
        font-size: 1.1rem;
    }

    .search-page-radio-group {
        margin-bottom: 1rem;
    }

    .search-page-radio-label {
        padding: 6px 10px;
    }

    .form-group label {
        font-size: 0.8rem;
    }

    .form-group input,
    .form-group select {
        padding: 0.5rem;
        font-size: 0.85rem;
    }

    .airline-info h3 {
        font-size: 1rem;
    }

    .flight-details {
        font-size: 0.7rem;
    }
}

/* Tablet-specific adjustments */
@media (min-width: 577px) and (max-width: 1024px) {
    .available-flights-container {
        margin-top: 0px;
        padding: 15px;
    }

    .search-booking-form {
        max-width: 600px;
        margin: 0 auto 1.5rem;
    }

    .flights-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
    }

    .form-row {
        grid-template-columns: repeat(2, 1fr);
    }

    .form-group.full-width {
        grid-column: span 2;
    }
}

/* Landscape orientation adjustments */
@media (max-height: 500px) and (orientation: landscape) {
    .available-flights-container {
        margin-top: 0px;
    }

    .search-booking-form {
        padding: 1rem;
    }

    .form-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.8rem;
    }
}

@media (max-width: 500px) {
    .search-booking-form {
        padding: 1rem;
    }

    .search-booking-form .form-row {
        grid-template-columns: 1fr;
    }

    .search-booking-form .form-group input,
    .search-booking-form .form-group select {
        padding: 0.5rem;
        font-size: 0.9rem;
    }

    .search-flight-radio-group {
        padding: 0.8rem;
    }

    .search-flight-radio-label {
        padding: 0.6rem;
    }

    .search-flight-radio {
        width: 18px;
        height: 18px;
    }

    .search-flight-radio-text {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .available-flights-container {
        margin-top: 0px;
        padding: 8px;
    }

    .search-booking-form h2 {
        font-size: 1.2rem;
    }

    .form-group label {
        font-size: 0.85rem;
    }

    .form-group input,
    .form-group select {
        padding: 0.5rem;
        font-size: 0.85rem;
    }

    .search-page-radio-text {
        font-size: 0.8rem;
    }

    .search-page-radio-text i {
        font-size: 0.9rem;
    }
}

@media (max-width: 360px) {
    .search-booking-form .form-row {
        grid-template-columns: 1fr;
    }

    .search-page-radio-group {
        flex-direction: column;
    }

    .search-page-radio-label {
        width: 100%;
    }
}

/* Search Page Specific Radio Button Styles */
.search-page-radio-group {
    display: flex;
    width: 100%;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 6px 0px 0px 0px;
    gap: 6px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.search-page-radio-label {
    flex: 1;
    position: relative;
    padding: 12px 16px;
    text-align: center;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: transparent;
}

.search-page-radio {
    position: absolute;
    opacity: 0;
}

.search-page-radio-text {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #495057;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.search-page-radio-text i {
    font-size: 1rem;
    transition: transform 0.3s ease;
}

/* Active state with new colors */
.search-page-radio:checked + .search-page-radio-text {
    color: #fff;
}

.search-page-radio-label:has(.search-page-radio:checked) {
    background: var(--gradient);
    box-shadow: 0 4px 12px rgba(30, 136, 229, 0.2);
}

/* Hover effects */
.search-page-radio-label:hover:not(:has(.search-page-radio:checked)) {
    background: rgba(30, 136, 229, 0.08);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .search-page-radio-group {
        padding: 4px;
    }

    .search-page-radio-label {
        padding: 10px;
    }
}

@media (max-width: 480px) {
    .search-page-radio-group {
        gap: 4px;
    }

    .search-page-radio-label {
        padding: 8px;
    }

    .search-page-radio-text {
        font-size: 0.85rem;
        gap: 4px;
    }

    .search-page-radio-text i {
        font-size: 0.9rem;
    }
}

@media (max-width: 360px) {
    .search-page-radio-group {
        flex-direction: column;
        gap: 4px;
    }

    .search-page-radio-label {
        width: 100%;
        padding: 10px;
    }
}

/* Print styles */
@media print {
    .available-flights-container {
        margin: 0;
        padding: 0;
        display: block;
    }

    .search-booking-form {
        display: none;
    }
}

/* Fix for container width and spacing */
@media (max-width: 1024px) {
    .available-flights-container {
        width: 100%;
        max-width: 100%;
        margin: 0px auto 0;
        padding: 15px;
        box-sizing: border-box;
    }

    .search-booking-form,
    .available-flights {
        width: 100%;
        max-width: 100%;
        
        box-sizing: border-box;
    }

    .flights-grid {
        width: 100%;
        margin: 0;
        padding: 0;
    }
}

@media (max-width: 768px) {
     

    .available-flights-container {
        width: 100%;
        margin: 0px 0 0 0;
        padding: 10px;
        border-radius: 15px;
    }

    .search-booking-form {
        padding: 15px;
        
    }

    .form-row,
    .form-group,
    .flights-grid {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .flight-card {
        width: 100%;
        margin: 0 0 15px 0;
    }
}

@media (max-width: 480px) {
    .available-flights-container {
        margin-top: 0px;
        padding: 8px;
    }

    .search-booking-form {
        padding: 10px;
    }

    .flight-card {
        margin-bottom: 10px;
    }
}

@media (max-width: 1024px) {
    
    .available-flights-container,
    .search-booking-form,
    .available-flights,
    .flights-grid,
    .form-row,
    .form-group {
        width: 100%;
        max-width: 100%;
        margin-left: 0;
        margin-right: 0;
        padding-left: 0px;
        padding-right: 0px;
        box-sizing: border-box;
    }

    .flight-card {
        width: 100%;
        margin: 0 0 15px 0;
        box-sizing: border
    }
}

/* Container fixes */

.available-flights-container {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 auto !important;
    padding: 15px !important;
    overflow: hidden;
    box-sizing: border-box;
}

/* Form and grid fixes */
.search-booking-form,
.available-flights,
.flights-grid,
.form-row,
.form-group,
.flight-card {
    width: 100%;
    margin-left: 0;
    margin-right: 0;
    box-sizing: border-box;
}

/* Container and Footer Width Fix */

.available-flights-container,

footer,
footer  {
    width: 100%;
    max-width: 1400px !important;
    margin-left: auto;
    margin-right: auto;
    box-sizing: border-box;
}

footer {
    margin-top: 40px;
    background: var(--white);
}

/* Responsive Footer Fixes */
@media (max-width: 1024px) {
    footer,
    footer  {
        width: 100% !important;
        max-width: 95% !important;
        padding: 15px;
        box-sizing: border-box;
    }
}

@media (max-width: 768px) {
    footer,
    footer .container {
        padding: 10px;
    }
}

/* Responsive fixes */
@media (max-width: 1024px) {
    .available-flights-container {
        padding: 10px !important;
    }

    .search-booking-form {
        max-width: none;
        padding: 15px;
        margin-top: 0px;
    }

    .flights-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 0;
    }
}

@media (max-width: 768px) {
    .flights-grid {
        grid-template-columns: 1fr;
    }
}

/* Header Overlap Fix */
.available-flights-container {
    margin-top: 0px;
    position: relative;
    z-index: 1;
}

@media (max-width: 1024px) {
    

   
    .available-flights-container,
   
    footer,
    footer  {
        width: 100%;
        max-width: 95% !important;
        padding: 15px;
    }
}

@media (max-width: 768px) {

    .available-flights-container,
    footer  {
        padding: 10px;
        max-width: 100% !important;
    }
}
.location-dropdown {
    position: absolute;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1000;
    display: none;
}

.location-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.location-item:hover {
    background-color: #f5f5f5;
}

.location-item.highlighted {
    background-color: #e3f2fd;
}

.location-item .match {
    font-weight: bold;
    color: #2196F3;
}

.form-group {
    position: relative;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .available-flights-container {
        grid-template-columns: 1fr; /* Stack on smaller screens */
    }
    
    .search-booking-form {
        position: relative;
        top: 0;
        margin-bottom: 2rem;
    }
}

@media (max-width: 992px) {
    .flights-grid {
        grid-template-columns: 1fr; /* Single column on smaller screens */
    }
}

/* Remove any conflicting styles */
.search-booking-form,
.available-flights,
.flights-grid,
.flight-card {
    max-width: none;
    margin-left: 0;
    margin-right: 0;
    box-sizing: border-box;
}

    /* Location dropdown styles */
    .location-dropdown {
        display: none;
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .location-item {
        padding: 8px 12px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .location-item:hover, .location-item.highlighted {
        background-color: #f0f0f0;
    }

    .location-item .match {
        font-weight: bold;
        color: #007bff;
    }

    /* Preserve existing error styles */
    .error-message {
        color: #dc3545;
        font-size: 0.85em;
        margin-top: 5px;
        display: none;
    }

    .input-error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25) !important;
    }
    </style>

            <!-- Origin and Destination with icons -->
            <div class="form-row">
                <div class="form-group">
                    <label for="flight-from"><i class="fas fa-plane-departure"></i> From:</label>
                    <input type="text" 
                           id="flight-from" 
                           name="FDestFrom" 
                           placeholder="Type to search locations" 
                           value="<?php echo htmlspecialchars($booking_details['from_location'] ?? ''); ?>" 
                           autocomplete="off"
                           required>
                    <div id="fromLocationList" class="location-dropdown"></div>
                    <div class="error-message" id="from-error">Please select a location from the dropdown list</div>
                </div>
                <div class="form-group">
                    <label for="flight-to"><i class="fas fa-plane-arrival"></i> To:</label>
                    <input type="text" 
                           id="flight-to" 
                           name="FDestTo" 
                           placeholder="Type to search locations" 
                           value="<?php echo htmlspecialchars($booking_details['to_location'] ?? ''); ?>" 
                           autocomplete="off"
                           required>
                    <div id="toLocationList" class="location-dropdown"></div>
                    <div class="error-message" id="to-error">Please select a location from the dropdown list</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="departure-date">Departure Date:</label>
                    <input type="date" 
                           id="departure-date" 
                           name="departure-date" 
                           min="" 
                           value="<?php echo htmlspecialchars($booking_details['departure_date'] ?? ''); ?>" 
                           required>
                </div>
                <div class="form-group" id="return-date-group" style="<?php echo isset($booking_details['trip-type']) && $booking_details['trip-type'] === 'one-way' ? 'display:none;' : ''; ?>">
                    <label for="arrival-date">Return Date:</label>
                    <input type="date" 
                           id="arrival-date" 
                           name="arrival-date" 
                           min="" 
                           value="<?php echo htmlspecialchars($booking_details['return_date'] ?? ''); ?>" 
                           <?php echo isset($booking_details['trip-type']) && $booking_details['trip-type'] === 'one-way' ? '' : 'required'; ?>>
                </div>
            </div>

            <!-- Passenger count with better layout -->
            <div class="form-row passengers-row">
                <div class="form-group">
                    <label for="adults">Adults:</label>
                    <select id="FAdult" name="FAdult">
                        <option value="1" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 1) ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 2) ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 3) ? 'selected' : ''; ?>>3</option>
                        <option value="4" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 4) ? 'selected' : ''; ?>>4</option>
                        <option value="5" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 5) ? 'selected' : ''; ?>>5</option>
                        <option value="6" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 6) ? 'selected' : ''; ?>>6</option>
                        <option value="7" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 7) ? 'selected' : ''; ?>>7</option>
                        <option value="8" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 8) ? 'selected' : ''; ?>>8</option>
                        <option value="9" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == 9) ? 'selected' : ''; ?>>9</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="children">Children (2-12):</label>
                    <select id="FChild" name="FChild">
                        <option value="0" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 0) ? 'selected' : ''; ?>>0</option>
                        <option value="1" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 1) ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 2) ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 3) ? 'selected' : ''; ?>>3</option>
                        <option value="4" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 4) ? 'selected' : ''; ?>>4</option>
                        <option value="5" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 5) ? 'selected' : ''; ?>>5</option>
                        <option value="6" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 6) ? 'selected' : ''; ?>>6</option>
                        <option value="7" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 7) ? 'selected' : ''; ?>>7</option>
                        <option value="8" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == 8) ? 'selected' : ''; ?>>8</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="infants">Infant (&lt; 2):</label>
                    <select id="FInfant" name="FInfant">
                        <option value="0" <?php echo (isset($booking_details['FInfant']) && $booking_details['FInfant'] == 0) ? 'selected' : ''; ?>>0</option>
                        <option value="1" <?php echo (isset($booking_details['FInfant']) && $booking_details['FInfant'] == 1) ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo (isset($booking_details['FInfant']) && $booking_details['FInfant'] == 2) ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo (isset($booking_details['FInfant']) && $booking_details['FInfant'] == 3) ? 'selected' : ''; ?>>3</option>
                        <option value="4" <?php echo (isset($booking_details['FInfant']) && $booking_details['FInfant'] == 4) ? 'selected' : ''; ?>>4</option>
                        <option value="5" <?php echo (isset($booking_details['FInfant']) && $booking_details['FInfant'] == 5) ? 'selected' : ''; ?>>5</option>
                    </select>
                </div>
            </div>

            <!-- Class and airline selection -->
            <div class="form-row">
                <div class="form-group">
                    <label for="class">Class:</label>
                    <select id="FClsType" name="FClsType">
                        <option value="ECONOMY" <?php echo (isset($booking_details['FClsType']) && $booking_details['FClsType'] == 'ECONOMY') ? 'selected' : ''; ?>>Economy</option>
                        <option value="PREMIUM" <?php echo (isset($booking_details['FClsType']) && $booking_details['FClsType'] == 'PREMIUM') ? 'selected' : ''; ?>>Premium</option>
                        <option value="BUSINESS" <?php echo (isset($booking_details['FClsType']) && $booking_details['FClsType'] == 'BUSINESS') ? 'selected' : ''; ?>>Business</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="airlines">Airlines:</label>
                    <select id="FAirLine" name="FAirLine">
                        <option value="ALL" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'ALL' ? 'selected' : ''; ?>>Any Airline</option>
                        <option value="BA" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'BA' ? 'selected' : ''; ?>>British Airways</option>
                        <option value="KA" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'KA' ? 'selected' : ''; ?>>Kenya Airways</option>
                        <option value="RAM" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'RAM' ? 'selected' : ''; ?>>Royal Air Maroc</option>
                        <option value="KLM" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'KLM' ? 'selected' : ''; ?>>KLM</option>
                        <option value="AF" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'AF' ? 'selected' : ''; ?>>Air France</option>
                        <option value="TK" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'TK' ? 'selected' : ''; ?>>Turkish Airlines</option>
                        <option value="EK" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'EK' ? 'selected' : ''; ?>>Emirates</option>
                        <option value="QR" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'QR' ? 'selected' : ''; ?>>Qatar Airways</option>
                        <option value="LH" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'LH' ? 'selected' : ''; ?>>Lufthansa</option>
                        <option value="VS" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'VS' ? 'selected' : ''; ?>>Virgin Atlantic</option>
                        <option value="WB" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'WB' ? 'selected' : ''; ?>>Rwandair Express</option>
                        <option value="ET" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'ET' ? 'selected' : ''; ?>>Ethiopian Air Lines</option>
                        <option value="LX" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'LX' ? 'selected' : ''; ?>>Swiss</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="cta-button">
                <i class="fas fa-search"></i> Search Flights
            </button>
        </form>
    </div>

    <div class="flights-section">
        <div class="available-flights">
            <h2>Available Flights</h2>

            <?php
            if ($booking_details) {
                if (empty($booking_details['from_location']) || empty($booking_details['to_location'])) {
                    echo "<div class='no-flights'><p>Please select valid locations.</p></div>";
                } else {
                    // Modified query to use case-insensitive LIKE for better matching
                    $query = "SELECT DISTINCT af.*, alf.*, 
                    CASE alf.airline_name 
                      WHEN 'British Airways' THEN 'BA'
                      WHEN 'Kenya Airways' THEN 'KA'
                      WHEN 'Royal Air Maroc' THEN 'RAM'
                      WHEN 'KLM' THEN 'KLM'
                      WHEN 'Air France' THEN 'AF'
                      WHEN 'Turkish Airlines' THEN 'TK'
                      WHEN 'Emirates' THEN 'EK'
                      WHEN 'Qatar Airways' THEN 'QR'
                      WHEN 'Lufthansa' THEN 'LH'
                      WHEN 'Virgin Atlantic' THEN 'VS'
                      WHEN 'Rwandair Express' THEN 'WB'
                      WHEN 'Ethiopian Air Lines' THEN 'ET'
                      WHEN 'Swiss' THEN 'LX'
                    END as airline_code
                    FROM available_flights af 
                    JOIN airline_flights alf ON af.flight_id = alf.flight_id 
                    WHERE LOWER(TRIM(af.from_location)) LIKE LOWER(TRIM(?)) 
                    AND LOWER(TRIM(af.to_location)) LIKE LOWER(TRIM(?))";
                    
                    if ($booking_details['FAirLine'] !== 'ALL') {
                        $query .= " AND alf.airline_name = ?";
                    }
                    
                    if ($stmt = $conn->prepare($query)) {
                        // Add wildcards to the search terms for partial matching
                        $from_location = '%' . trim($booking_details['from_location']) . '%';
                        $to_location = '%' . trim($booking_details['to_location']) . '%';
                        
                        if ($booking_details['FAirLine'] !== 'ALL') {
                            $airline_name = getAirlineName($booking_details['FAirLine']);
                            $stmt->bind_param("sss", $from_location, $to_location, $airline_name);
                        } else {
                            $stmt->bind_param("ss", $from_location, $to_location);
                        }
                        
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $result->num_rows > 0): ?>
                            <div class="flights-grid">
                            <?php 
                            $foundFlights = [];
                            while ($flight = $result->fetch_assoc()): 
                                // Skip duplicate flights
                                $flightKey = $flight['from_location'] . $flight['to_location'] . $flight['airline_name'];
                                if (!isset($foundFlights[$flightKey])):
                                    $foundFlights[$flightKey] = true;
                            ?>
                                <div class="flight-card">
                                    <div class="airline-info">
                                        <div class="airline-logo-container">
                                            <?php
                                            $airlineCode = $flight['airline_code']; // Now directly using the code from query
                                            $logoPath = getAirlineLogo($airlineCode);
                                            ?>
                                            <img src="<?php echo htmlspecialchars($logoPath); ?>" 
                                                 alt="<?php echo htmlspecialchars($flight['airline_name']); ?>" 
                                                 class="airline-logo"
                                                 onerror="this.src='assets/images/air-line/default-airline.png'">
                                        </div>
                                        <div class="airline-details">
                                            <h3><?php echo htmlspecialchars($flight['airline_name']); ?></h3>
                                            <p class="route"><?php echo htmlspecialchars($flight['from_location']); ?> → <?php echo htmlspecialchars($flight['to_location']); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flight-details">
                                        <div class="route-info">
                                            <div class="info-table">
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-plane-departure"></i>
                                                        Departure
                                                    </div>
                                                    <div class="info-value">
                                                        <?php echo htmlspecialchars($booking_details['departure_date']); ?> at <?php echo htmlspecialchars($flight['time_departure']); ?>
                                                    </div>
                                                </div>

                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-plane-arrival"></i>
                                                        Arrival
                                                    </div>
                                                    <div class="info-value">
                                                        <?php echo ($booking_details['trip_type'] === 'round-trip' ? htmlspecialchars($booking_details['return_date']) : htmlspecialchars($booking_details['departure_date'])); ?> at <?php echo htmlspecialchars($flight['arrival_time']); ?>
                                                    </div>
                                                </div>

                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-clock"></i>
                                                        Duration
                                                    </div>
                                                    <div class="info-value">
                                                        <?php echo htmlspecialchars($flight['total_journey_time']); ?>
                                                    </div>
                                                </div>

                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        Stops
                                                    </div>
                                                    <div class="info-value">
                                                        <?php echo htmlspecialchars($flight['stops']); ?>
                                                    </div>
                                                </div>

                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-couch"></i>
                                                        Available Seats
                                                    </div>
                                                    <div class="info-value">
                                                        <?php echo htmlspecialchars($flight['available_seats']); ?>
                                                    </div>
                                                </div>

                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-star"></i>
                                                        Class
                                                    </div>
                                                    <div class="info-value">
                                                        <?php echo htmlspecialchars($booking_details['FClsType']); ?>
                                                    </div>
                                                </div>

                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                        Price (Full Payment)
                                                    </div>
                                                    <div class="info-value price">
                                                        $<?php echo htmlspecialchars(number_format($flight['pay_in_one_go'], 2)); ?>
                                                    </div>
                                                </div>

                                                <?php if ($flight['pay_in_installments'] > 0): ?>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-credit-card"></i>
                                                        Price (Installments)
                                                    </div>
                                                    <div class="info-value price">
                                                        $<?php echo htmlspecialchars(number_format($flight['pay_in_installments'], 2)); ?>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flight-buttons">
                                        <button class="select-flight-btn book-now" onclick="selectFlight(<?php echo $flight['flight_id']; ?>, '<?php echo htmlspecialchars($flight['airline_name']); ?>')">
                                            <i class="fas fa-ticket-alt"></i> Book Now
                                        </button>
                                        <button class="select-flight-btn request-call" onclick="requestCallback(<?php echo $flight['flight_id']; ?>, '<?php echo htmlspecialchars($flight['airline_name']); ?>')">
                                            <i class="fas fa-phone"></i> Request Call Back
                                        </button>
                                    </div>
                                </div>
                            <?php 
                                endif;
                            endwhile; 
                            ?>
                            </div>
                        <?php else: ?>
                            <div class="no-flights">
                                <p>No flights available for selected locations.</p>
                                <p>Please try different locations or dates.</p>
                            </div>
                        <?php endif;
                        $stmt->close();
                    }
                }
            } else { ?>
                <div class="no-flights">
                    <p>Please use the search form above to find available flights.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<style>
.available-flights-container {
    display: grid;
    grid-template-columns: minmax(280px, 300px) 1fr;
    gap: 1.5rem;
    max-width: 1400px;
    margin: 100px 0 0  0!important;
    padding: 20px;
    align-items: start;
}

.search-form-wrapper {
    position: sticky;
    top: 0px;
    height: fit-content;
    align-self: start;
}

.search-booking-form {
    background: var(--white);
    padding: 1.2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.08);
}

.flights-section {
    flex: 1;
}

.flights-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.flight-card {
    background: var (--white);
    padding: 0.8rem;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.flight-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 0px;
    padding: 0 0px;
}

.select-flight-btn {
    padding: 12px 5px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    font-family: var(--font-family-primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

.book-now {
    background: var(--deep-purple);
    color: var(--white);
}

.book-now:hover {
    background: var(--gradient);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.3);
}

.request-call {
    background: var(--pink-vivid);
    color: var(--white);
}

.request-call:hover {
    background: var(--gradient);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(218, 73, 141, 0.3);
}

.select-flight-btn i {
    margin-right: 8px;
    font-size: 1em;
}

/* Mobile Responsiveness */
@media (max-width: 1200px) {
    .available-flights-container {
        grid-template-columns: 1fr;
    }
    
    .search-form-wrapper {
        position: relative;
        top: 0;
        max-width: 600px;
        margin: 0 auto 0rem;
    }

    .flights-grid {buttons or 
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
}

@media (max-width: 768px) {
    .flights-grid {
        grid-template-columns: 1fr;
    }
}

.return-info {
    color: var(--deep-purple);
    font-weight: 500;
    margin-top: 5px;
    padding-top: 5px;
    border-top: 1px dashed rgba(105, 36, 124, 0.2);
}

.airline-info {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    border-bottom: 1px solid rgba(105, 36, 124, 0.1);
    background: linear-gradient(to right, var(--peach-cream), var(--white));
    border-radius: 8px 8px 0 0;
}

.airline-logo-container {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 8px;
    padding: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.airline-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 6px;
}

.airline-details {
    flex: 1;
}

.airline-details h3 {
    color: var(--deep-purple);
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .airline-logo-container {
        width: 50px;
        height: 50px;
    }
    
    .airline-details h3 {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .airline-logo-container {
        width: 40px;
        height: 40px;
    }
    
    .airline-info {
        padding: 8px;
        gap: 10px;
    }
}

.info-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    border-bottom: 1px solid rgba(105, 36, 124, 0.1);
}

.info-row:last-child {
    border-bottom: none;
}

.info-row i {
    font-size: 1rem;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--deep-purple);
}

.info-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.8rem;
    margin-bottom: 1px;
    color: var(--charcoal-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 0.8rem;
    color: var(--deep-purple);
    font-weight: 500;
}

.info-value.price {
    color: var(--pink-vivid);
    font-weight: 600;
}

.route {
    color: var(--charcoal-gray);
    font-size: 0.4rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.route i {
    color: var(--pink-vivid);
}

.flight-details {
    padding: 0px;
}

.info-table {
    display: grid;
    gap: 10px;
}

.info-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    padding: 8px;
    border-bottom: 1px solid rgba(105, 36, 124, 0.1);
    font-size: 0.9rem;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--charcoal-gray);
    font-weight: 500;
}

.info-label i {
    color: var(--deep-purple);
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

.info-value {
    text-align: right;
    color: var(--deep-purple);
    font-weight: 500;
}

.info-value.price {
    color: var(--pink-vivid);
    font-weight: 600;
}

@media (max-width: 480px) {
    .info-row {
        font-size: 0.8rem;
    }
    
    .info-label i {
        font-size: 0.9rem;
    }
}
</style>

<?php
// Add this function at the top of your file with other functions
function getAirlineName($code) {
    $airlines = [
        'BA' => 'British Airways',
        'KA' => 'Kenya Airways',
        'RAM' => 'Royal Air Maroc',
        'KLM' => 'KLM',
        'AF' => 'Air France',
        'TK' => 'Turkish Airlines',
        'EK' => 'Emirates',
        'QR' => 'Qatar Airways',
        'LH' => 'Lufthansa',
        'VS' => 'Virgin Atlantic',
        'WB' => 'Rwandair Express',
        'ET' => 'Ethiopian Air Lines',
        'LX' => 'Swiss'
    ];
    return $airlines[$code] ?? 'Unknown Airline';
}

function getAirlineLogo($code) {
    $baseUrl = 'assets/images/air-line/';
    $logoMap = [
        'BA' => 'british-airways.png',
        'KA' => 'kenya-airways.png',
        'RAM' => 'royal-air-maroc.png',
        'KLM' => 'klm.png',
        'AF' => 'air-france.png',
        'TK' => 'turkish-airlines.png',
        'EK' => 'emirates.png',
        'QR' => 'qatar-airways.png',
        'LH' => 'lufthansa.png',
        'VS' => 'virgin-atlantic.png',
        'WB' => 'rwandair.png',
        'ET' => 'ethiopian-airlines.png',
        'LX' => 'swiss.png'
    ];
    
    if (!$code) {
        return $baseUrl . 'default-airline.png';
    }
    
    return $baseUrl . ($logoMap[$code] ?? 'default-airline.png');
}
?>

<?php
require_once 'includes/footer.php';
?>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Simplified JavaScript -->
<script>
function toggleReturnDate() {
    const tripType = document.querySelector('input[name="trip-type"]:checked').value;
    const returnDateGroup = document.getElementById('return-date-group');
    if (tripType === 'one-way') {
        returnDateGroup.style.display = 'none';
        document.getElementById('arrival-date').removeAttribute('required');
    } else {
        returnDateGroup.style.display = 'block';
        document.getElementById('arrival-date').setAttribute('required', 'required');
    }
}

// Update selectFlight function to include airline code
function selectFlight(flightId, airlineCode) {
    // Store flight details in session
    const flight = {
        id: flightId,
        airline: airlineCode,
        from: '<?php echo htmlspecialchars($booking_details['from_location'] ?? ''); ?>',
        to: '<?php echo htmlspecialchars($booking_details['to_location'] ?? ''); ?>',
        departure: '<?php echo htmlspecialchars($booking_details['departure_date'] ?? ''); ?>',
        return: '<?php echo htmlspecialchars($booking_details['return_date'] ?? ''); ?>',
        adults: '<?php echo htmlspecialchars($booking_details['FAdult'] ?? ''); ?>',
        children: '<?php echo htmlspecialchars($booking_details['FChild'] ?? ''); ?>',
        infants: '<?php echo htmlspecialchars($booking_details['FInfant'] ?? ''); ?>',
        class: '<?php echo htmlspecialchars($booking_details['FClsType'] ?? ''); ?>'
    };

    // Send flight details to store in session
    fetch('store_flight_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(flight)
    }).then(() => {
        window.location.href = 'booking.php';
    });
}

// Update requestCallback function to include airline code
function requestCallback(flightId, airlineCode) {
    // Store callback details in session
    const callbackData = {
        id: flightId,
        airline: airlineCode,
        from: '<?php echo htmlspecialchars($booking_details['from_location'] ?? ''); ?>',
        to: '<?php echo htmlspecialchars($booking_details['to_location'] ?? ''); ?>',
        departure: '<?php echo htmlspecialchars($booking_details['departure_date'] ?? ''); ?>',
        return: '<?php echo htmlspecialchars($booking_details['return_date'] ?? ''); ?>',
        adults: '<?php echo htmlspecialchars($booking_details['FAdult'] ?? ''); ?>',
        children: '<?php echo htmlspecialchars($booking_details['FChild'] ?? ''); ?>',
        infants: '<?php echo htmlspecialchars($booking_details['FInfant'] ?? ''); ?>',
        class: '<?php echo htmlspecialchars($booking_details['FClsType'] ?? ''); ?>'
    };

    fetch('store_callback_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(callbackData)
    }).then(() => {
        window.location.href = 'Contact-us.php';
    });
}

function goBack() {
    window.location.replace('index.php');
}

document.addEventListener('DOMContentLoaded', function() {
    toggleReturnDate();
});

document.addEventListener('DOMContentLoaded', function() {
    const fromInput = document.getElementById('flight-from');
    const toInput = document.getElementById('flight-to');
    const fromList = document.getElementById('fromLocationList');
    const toList = document.getElementById('toLocationList');
    const searchForm = document.getElementById('searchForm');
    const fromError = document.getElementById('from-error');
    const toError = document.getElementById('to-error');

    function setupLocationField(input, dropdownList, errorElement) {
        let selectedFromDropdown = false;
        let validLocations = new Set();
        let currentHighlighted = -1;
        let lastSearchResults = [];

        function selectLocation(location) {
            input.value = location;
            selectedFromDropdown = true;
            input.classList.remove('input-error');
            errorElement.style.display = 'none';
            dropdownList.style.display = 'none';
            currentHighlighted = -1;
            validLocations.add(location);
            lastSearchResults = [location];
        }

        function removeHighlight() {
            const items = dropdownList.getElementsByClassName('location-item');
            Array.from(items).forEach(item => item.classList.remove('highlighted'));
        }

        // Update keyboard navigation
        input.addEventListener('keydown', function(e) {
            const items = dropdownList.getElementsByClassName('location-item');
            if (items.length > 0) {
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        removeHighlight();
                        currentHighlighted = (currentHighlighted + 1) % items.length;
                        items[currentHighlighted].classList.add('highlighted');
                        items[currentHighlighted].scrollIntoView({ block: 'nearest' });
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        removeHighlight();
                        currentHighlighted = currentHighlighted <= 0 ? items.length - 1 : currentHighlighted - 1;
                        items[currentHighlighted].classList.add('highlighted');
                        items[currentHighlighted].scrollIntoView({ block: 'nearest' });
                        break;
                    case 'Enter':
                        e.preventDefault();
                        if (currentHighlighted >= 0) {
                            selectLocation(items[currentHighlighted].textContent);
                        }
                        break;
                    case 'Escape':
                        dropdownList.style.display = 'none';
                        currentHighlighted = -1;
                        break;
                }
            }
        });

        input.addEventListener('input', function() {
            const searchText = this.value.trim();
            input.classList.remove('input-error');
            errorElement.style.display = 'none';
            currentHighlighted = -1;
            
            if (searchText.length > 0) {
                fetch(`get_locations.php?search=${encodeURIComponent(searchText)}`)
                    .then(response => response.json())
                    .then(locations => {
                        dropdownList.innerHTML = '';
                        lastSearchResults = locations;
                        validLocations = new Set(locations);

                        locations.forEach((location, index) => {
                            const div = document.createElement('div');
                            div.className = 'location-item';
                            const regex = new RegExp(`(${searchText})`, 'gi');
                            const highlightedText = location.replace(regex, '<span class="match">$1</span>');
                            div.innerHTML = highlightedText;
                            
                            // Fix click handler
                            div.addEventListener('click', (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                selectLocation(location);
                            });

                            div.addEventListener('mouseover', () => {
                                removeHighlight();
                                div.classList.add('highlighted');
                                currentHighlighted = index;
                            });

                            dropdownList.appendChild(div);
                        });
                        
                        if (locations.length > 0) {
                            dropdownList.style.display = 'block';
                        } else {
                            dropdownList.style.display = 'none';
                        }
                    });
            } else {
                dropdownList.style.display = 'none';
            }
        });

        // ... rest of existing code ...
    }

    const fromValidator = setupLocationField(fromInput, fromList, fromError);
    const toValidator = setupLocationField(toInput, toList, toError);

    searchForm.addEventListener('submit', function(e) {
        const isFromValid = fromValidator.isValid();
        const isToValid = toValidator.isValid();

        if (!isFromValid) {
            e.preventDefault();
            fromInput.classList.add('input-error');
            fromError.style.display = 'block';
        }

        if (!isToValid) {
            e.preventDefault();
            toInput.classList.add('input-error');
            toError.style.display = 'block';
        }

        if (!isFromValid || !isToValid) {
            e.preventDefault();
        }
    });

    // ... rest of your existing keyboard navigation code ...
});

document.addEventListener('DOMContentLoaded', function() {
    // Set minimum dates for departure and return
    const today = new Date();
    const departureInput = document.getElementById('departure-date');
    const returnInput = document.getElementById('arrival-date');
    
    // Format date to YYYY-MM-DD
    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    // Set minimum date for departure (today)
    departureInput.min = formatDate(today);

    // Update return date minimum when departure date changes
    departureInput.addEventListener('change', function() {
        const selectedDeparture = new Date(this.value);
        // Set return date minimum to day after selected departure
        const minReturn = new Date(selectedDeparture);
        minReturn.setDate(minReturn.getDate() + 1);
        returnInput.min = formatDate(minReturn);

        // If return date is less than new minimum, update it
        if (returnInput.value && new Date(returnInput.value) < minReturn) {
            returnInput.value = formatDate(minReturn);
        }
    });

    // Initial call to set return date minimum if departure is already selected
    if (departureInput.value) {
        const selectedDeparture = new Date(departureInput.value);
        const minReturn = new Date(selectedDeparture);
        minReturn.setDate(minReturn.getDate() + 1);
        returnInput.min = formatDate(minReturn);
    }

    // Original toggleReturnDate function
    toggleReturnDate();
});

document.addEventListener('DOMContentLoaded', function() {
    const fromInput = document.getElementById('flight-from');
    const toInput = document.getElementById('flight-to');
    const fromList = document.getElementById('fromLocationList');
    const toList = document.getElementById('toLocationList');

    function setupLocationField(input, dropdownList) {
        let selectedFromDropdown = false;
        let currentHighlighted = -1;
        let lastSearchResults = [];

        // Show popular locations on focus
        input.addEventListener('focus', function() {
            if (!input.value.trim()) {
                fetch('get_locations.php')
                    .then(response => response.json())
                    .then(locations => {
                        updateDropdown(locations);
                        dropdownList.style.display = 'block';
                    });
            }
        });

        // Handle outside clicks
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdownList.contains(e.target)) {
                dropdownList.style.display = 'none';
                currentHighlighted = -1;
            }
        });

        function updateDropdown(locations) {
            dropdownList.innerHTML = '';
            lastSearchResults = locations;

            locations.forEach((location, index) => {
                const div = document.createElement('div');
                div.className = 'location-item';
                
                if (input.value.trim()) {
                    const regex = new RegExp(`(${input.value.trim()})`, 'gi');
                    div.innerHTML = location.replace(regex, '<span class="match">$1</span>');
                } else {
                    div.textContent = location;
                }

                div.addEventListener('click', () => selectLocation(location));
                div.addEventListener('mouseover', () => {
                    removeHighlight();
                    div.classList.add('highlighted');
                    currentHighlighted = index;
                });

                dropdownList.appendChild(div);
            });
        }

        function selectLocation(location) {
            input.value = location;
            selectedFromDropdown = true;
            dropdownList.style.display = 'none';
            currentHighlighted = -1;
        }

        function removeHighlight() {
            const items = dropdownList.getElementsByClassName('location-item');
            Array.from(items).forEach(item => item.classList.remove('highlighted'));
        }

        // Enhanced keyboard navigation
        input.addEventListener('keydown', function(e) {
            const items = dropdownList.getElementsByClassName('location-item');
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    if (dropdownList.style.display === 'none') {
                        dropdownList.style.display = 'block';
                        if (lastSearchResults.length > 0) {
                            currentHighlighted = 0;
                        }
                    } else {
                        currentHighlighted = (currentHighlighted + 1) % items.length;
                    }
                    updateHighlight();
                    break;

                case 'ArrowUp':
                    e.preventDefault();
                    if (dropdownList.style.display !== 'none') {
                        currentHighlighted = currentHighlighted <= 0 ? items.length - 1 : currentHighlighted - 1;
                        updateHighlight();
                    }
                    break;

                case 'Enter':
                    e.preventDefault();
                    if (currentHighlighted >= 0 && items[currentHighlighted]) {
                        selectLocation(items[currentHighlighted].textContent);
                    }
                    break;

                case 'Escape':
                    dropdownList.style.display = 'none';
                    currentHighlighted = -1;
                    break;
            }
        });

        function updateHighlight() {
            removeHighlight();
            const items = dropdownList.getElementsByClassName('location-item');
            if (items[currentHighlighted]) {
                items[currentHighlighted].classList.add('highlighted');
                items[currentHighlighted].scrollIntoView({ block: 'nearest' });
            }
        }

        // Modified input handler
        input.addEventListener('input', function() {
            const searchText = this.value.trim();
            
            if (searchText.length > 0) {
                fetch(`get_locations.php?search=${encodeURIComponent(searchText)}`)
                    .then(response => response.json())
                    .then(locations => {
                        updateDropdown(locations);
                        dropdownList.style.display = locations.length ? 'block' : 'none';
                    });
            } else {
                fetch('get_locations.php')
                    .then(response => response.json())
                    .then(locations => {
                        updateDropdown(locations);
                        dropdownList.style.display = 'block';
                    });
            }
        });
    }

    // Initialize the location fields
    setupLocationField(fromInput, fromList);
    setupLocationField(toInput, toList);
});
</script>