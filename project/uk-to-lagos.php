<?php
session_start();
require_once '../private/config.php';

// SEO meta tags
$pageTitle = "Flights from UK to Lagos | Book Cheap Flights to Lagos from United Kingdom";
$metaDescription = "Find and book cheap flights from United Kingdom to Lagos. Compare direct and connecting flights, prices, and airlines. Easy booking with great deals on UK to Lagos routes.";
$canonicalURL = "http://theticketstoworld.co.uk/uk-to-lagos";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $metaDescription; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo $canonicalURL; ?>">
    <meta property="og:title" content="<?php echo $pageTitle; ?>">
    <meta property="og:description" content="<?php echo $metaDescription; ?>">
    <meta property="og:url" content="<?php echo $canonicalURL; ?>">
    <meta property="og:type" content="website">
    <meta name="keywords" content="UK to Lagos flights, cheap flights to Lagos, London to Lagos, British Airways Lagos, flights United Kingdom Nigeria">
<?php
require_once './includes/header.php';


$default_search = [
    'from_location' => 'United Kingdom',
    'to_location' => 'Lagos',  
    'trip_type' => 'round-trip',
    'FClsType' => 'ECONOMY',
    'FAirLine' => 'ALL',
    'FAdult' => 1,
    'FChild' => 0,
    'FInfant' => 0,
    'departure_date' => date('Y-m-d'),
    'return_date' => date('Y-m-d', strtotime('+7 days')) 
];


if (!isset($_SESSION['search_data'])) {
    $_SESSION['search_data'] = $default_search;
}

$booking_details = $_SESSION['search_data'];

$booking_details['from_location'] = $default_search['from_location'];
$booking_details['to_location'] = $default_search['to_location'];
?>

<main class="route-page">
    <div class="simple-hero">
        <h1>How to Book Flights from the United Kingdom to Lagos</h1>
    </div>

    <div class="route-container">
        <!-- Main Content Area -->
        <div class="content-wrapper">
            <!-- Sticky Search Section -->
            <aside class="search-sidebar">
                <?php include 'includes/search-form.php'; ?>
            </aside>

            <!-- Flights Grid Section -->
            <section class="flights-section">
                <h2>Available Flights</h2>
                <div class="flights-grid">
                    <?php
                    try {
                        // Query specific to UK-Lagos route
                        $query = "SELECT DISTINCT af.*, alf.* 
                                 FROM available_flights af 
                                 JOIN airline_flights alf ON af.flight_id = alf.flight_id 
                                 WHERE (LOWER(af.from_location) LIKE ? 
                                 OR LOWER(af.from_location) LIKE ?
                                 OR LOWER(af.from_location) LIKE ?
                                 OR LOWER(af.from_location) LIKE ?)
                                 AND LOWER(af.to_location) LIKE ?";
                        
                        if ($booking_details['FAirLine'] !== 'ALL') {
                            $query .= " AND alf.airline_name = ?";
                        }
                        
                        $stmt = $conn->prepare($query);
                        
                        // Search terms
                        $uk_terms = [
                            '%united kingdom%',
                            '%london%',
                            '%manchester%',
                            '%birmingham%'
                        ];
                        $lagos_term = '%lagos%';  // Changed to Lagos
                        
                        if ($booking_details['FAirLine'] !== 'ALL') {
                            $airline = getAirlineName($booking_details['FAirLine']);
                            $stmt->bind_param("ssssss", 
                                $uk_terms[0], 
                                $uk_terms[1], 
                                $uk_terms[2], 
                                $uk_terms[3], 
                                $lagos_term, 
                                $airline
                            );
                        } else {
                            $stmt->bind_param("sssss", 
                                $uk_terms[0], 
                                $uk_terms[1], 
                                $uk_terms[2], 
                                $uk_terms[3], 
                                $lagos_term
                            );
                        }
                        
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $result->num_rows > 0) {
                            echo '<div class="flights-grid">';
                            while ($flight = $result->fetch_assoc()) {
                                // Display flight card
                                include 'includes/flight-card-template.php';
                            }
                            echo '</div>';
                        } else {
                            echo "<div class='no-flights'>No flights available for these locations currently.</div>";
                        }
                        $stmt->close();
                    } catch (Exception $e) {
                        error_log("Error in united-kingdom-to-lagos.php: " . $e->getMessage());
                        $error_message = "An error occurred while fetching flights. Please try again later.";
                        echo "<div class='error-message'>$error_message</div>";
                    }
                    ?>
                </div>
            </section>
        </div>

        <!-- Route Info Section -->
        <section class="route-info-section">
            <div class="route-info-container">
                <div class="route-info-header">
                    <h2>United Kingdom to Lagos Flight Guide</h2>
                    <div class="route-info-divider"></div>
                </div>

                <div class="content-section">
                    <div class="intro-text">
                        <p>Planning a trip from the United Kingdom to Lagos? Whether you're traveling for business, vacation, or to visit family, Lagos is a vibrant and bustling city offering a unique cultural experience. As Nigeria’s largest city and commercial hub, Lagos is known for its lively atmosphere, beautiful beaches, and rich history. At Tickets to World, we make booking your flight from the United Kingdom to Lagos simple, affordable, and hassle-free. With a range of flight options and competitive prices, we are here to help you start your journey to Lagos with ease.</p>
                    </div>

                    <div class="airlines-overview">
                        <h2 class="section-title">Airlines Flying from the United Kingdom to Lagos                        </h2>
                        
                        <div class="overview-intro">
                            <p>Traveling from the United Kingdom to Lagos, Nigeria, is made easy with several airlines offering both direct and connecting flights. Here's a quick look at your options:</p>
                        </div>

                        <div class="airlines-grid">
                            <!-- British Airways Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">✈️</span>
                                    <h3>British Airways</h3>
                                    <span class="route-badge direct">(Direct Flights)</span>
                                </div>
                                <div class="airline-content">
                                    <p>British Airways offers direct flights from London Heathrow to Lagos, taking about 6 to 7 hours. This is one of the quickest and most convenient options.</p>
                                </div>
                            </div>

                            <!-- Virgin Atlantic Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🛩️</span>
                                    <h3>Virgin Atlantic</h3>
                                    <span class="route-badge direct">(Non-Stop Flights)</span>
                                </div>
                                <div class="airline-content">
                                    <p>Virgin Atlantic also provides direct flights from London Heathrow to Lagos, with a similar flight time of 6 to 7 hours, known for its friendly service and comfort.</p>
                                </div>
                            </div>

                            <!-- KLM Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🛫</span>
                                    <h3>KLM</h3>
                                    <span class="route-badge connecting">(Connecting via Amsterdam)</span>
                                </div>
                                <div class="airline-content">
                                    <p>KLM operates flights with a layover in Amsterdam. The flight from London to Amsterdam is about 1 hour, followed by another 6 to 7 hours from Amsterdam to Lagos.</p>
                                </div>
                            </div>

                            <!-- Airfrance Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🛬</span>
                                    <h3>Air France</h3>
                                    <span class="route-badge connecting">(Connecting via Paris)</span>
                                </div>
                                <div class="airline-content">
                                    <p>Air France offers flights with a short layover in Paris. The journey from London to Paris is about 1 hour, and then a 6 to 7-hour flight to Lagos.</p>
                                </div>
                            </div>
                            <!-- Emirates Airlines Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🚀</span>
                                    <h3>Emirates</h3>
                                    <span class="route-badge connecting">(Connecting via Dubai)</span>
                                </div>
                                <div class="airline-content">
                                    <p>Emirates offers flights with a layover in Dubai, taking about 7 hours from London to Dubai, and then 8 hours from Dubai to Lagos. Whether you prefer direct flights with British Airways or Virgin Atlantic, or don’t mind a layover with KLM, Air France, Turkish Airlines, or Emirates, there are plenty of options to suit your needs.</p>
                                </div>
                            </div>

                            <!--Turkish Airlines Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🛬</span>
                                    <h3>Turkish Airlines</h3>
                                    <span class="route-badge connecting">(Connecting via Istanbul)</span>
                                </div>
                                <div class="airline-content">
                                    <p>Turkish Airlines flies via Istanbul, with the London to Istanbul leg taking around 3.5 hours, followed by a 6-hour flight to Lagos.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <style>
                        .airlines-overview {
                            padding: 2rem;
                            background: #fff;
                            border-radius: 12px;
                            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
                            margin: 2rem 0;
                        }

                        .airlines-overview .section-title {
                            color: var(--primary-color);
                            font-size: 2rem;
                            text-align: center;
                            margin-bottom: 1.5rem;
                        }

                        .overview-intro {
                            max-width: 800px;
                            margin: 0 auto 2rem;
                            text-align: center;
                            line-height: 1.6;
                            color: #555;
                        }

                        .airlines-grid {
                            display: grid;
                            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                            gap: 1.5rem;
                            margin-bottom: 2rem;
                        }

                        .airline-card {
                            background: #fff;
                            border-radius: 10px;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                            transition: transform 0.3s ease;
                            border: 1px solid #eee;
                            overflow: hidden;
                        }

                        .airline-card:hover {
                            transform: translateY(-5px);
                        }

                        .airline-header {
                            padding: 1.5rem;
                            background: linear-gradient(to right, #f8f9fa, #fff);
                            border-bottom: 2px solid #eee;
                            position: relative;
                        }

                        .airline-icon {
                            font-size: 1.5rem;
                            margin-right: 0.5rem;
                        }

                        .airline-header h3 {
                            margin: 0.5rem 0;
                            color: var(--primary-color);
                        }

                        .route-badge {
                            display: inline-block;
                            padding: 0.25rem 0.75rem;
                            border-radius: 15px;
                            font-size: 0.85rem;
                            font-weight: 500;
                        }

                        .route-badge.direct {
                            background: #e8f5e9;
                            color: #2e7d32;
                        }

                        .route-badge.connecting {
                            background: #e3f2fd;
                            color: #1565c0;
                        }

                        .airline-content {
                            padding: 1.5rem;
                        }

                        .flight-options {
                            margin: 2rem 0;
                            padding: 2rem;
                            background: #f8f9fa;
                            border-radius: 10px;
                        }

                        .options-grid {
                            display: grid;
                            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                            gap: 1.5rem;
                            margin-top: 1.5rem;
                        }

                        .option-card {
                            background: #fff;
                            padding: 1.5rem;
                            border-radius: 8px;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                        }

                        .overview-conclusion {
                            text-align: center;
                            margin-top: 2rem;
                            padding: 2rem;
                            background: linear-gradient(to right bottom, #f8f9fa, #fff);
                            border-radius: 10px;
                            border: 2px solid var(--primary-color);
                        }

                        .cta-message {
                            margin-top: 1.5rem;
                            padding: 1rem 1.5rem; 
                            background: #ff6b6b; 
                            color: #ffffff; 
                            border-radius: 8px;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                            font-size: 1.2rem; 
                            font-weight: 600; 
                            text-align: center; 
                            transition: transform 0.2s ease, box-shadow 0.2s ease; 
                        }

                        .cta-message:hover {
                            background: #ff4c4c;
                            transform: scale(1.03);
                            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
                        }

                        @media (max-width: 768px) {
                            .airlines-overview {
                                padding: 1rem;
                            }

                            .section-title {
                                font-size: 1.5rem;
                            }

                            .airlines-grid {
                                grid-template-columns: 1fr;
                            }

                            .flight-options {
                                padding: 1rem;
                            }

                            .options-grid {
                                grid-template-columns: 1fr;
                            }

                            .overview-conclusion {
                                padding: 0.5rem;
                            }
                        }
                        .route-info-section{
                            margin-bottom: 3rem;
                        }
                    </style>

                    <style>
                        .flight-duration-section {
                            padding: 3rem 0;
                        }

                        .duration-cards {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 2rem;
                            margin: 2rem 0;
                        }

                        .duration-card {
                            background: #fff;
                            padding: 2rem;
                            border-radius: 12px;
                            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
                            border: 1px solid #eee;
                            transition: transform 0.3s ease;
                        }

                        .duration-card:hover {
                            transform: translateY(-5px);
                        }

                        .duration-card.direct {
                            border-left: 5px solid #4CAF50;
                        }

                        .duration-card.layover {
                            border-left: 5px solid #2196F3;
                        }

                        .card-icon {
                            font-size: 2.5rem;
                            margin-bottom: 1rem;
                        }

                        .factors-section {
                            margin-top: 3rem;
                            padding: 2rem;
                            background: linear-gradient(to right bottom, #f8f9fa, #ffffff);
                            border-radius: 12px;
                            border: 2px solid var(--primary-color);
                        }

                        .factors-grid {
                            display: grid;
                            grid-template-columns: repeat(3, 1fr);
                            gap: 1.5rem;
                            margin-top: 1.5rem;
                        }

                        .factor-card {
                            background: #fff;
                            padding: 1.5rem;
                            border-radius: 10px;
                            text-align: center;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                            transition: transform 0.3s ease;
                        }

                        .factor-card:hover {
                            transform: translateY(-3px);
                        }

                        .factor-icon {
                            font-size: 2rem;
                            margin-bottom: 1rem;
                        }

                        .duration-cta {
                            margin-top: 3rem;
                            text-align: center;
                        }

                        .cta-box {
                            background: var(--gradient);
                            color: white;
                            padding: 2rem;
                            border-radius: 12px;
                            margin-top: 1.5rem;
                        }

                        .cta-box h4 {
                            margin-bottom: 1rem;
                        }

                        @media (max-width: 768px) {
                            .duration-cards,
                            .factors-grid {
                                grid-template-columns: 1fr;
                                gap: 1rem;
                            }

                            .duration-card,
                            .factor-card {
                                padding: 1.5rem;
                            }

                            .factors-section {
                                padding: 1.5rem;
                            }
                        }
                    </style>

                    <style>
                        .attractions-section {
                            margin-top: 4rem;
                            padding-top: 3rem;
                            border-top: 2px solid #eee;
                        }

                        .attractions-grid {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 2rem;
                            margin: 2rem 0;
                        }

                        .attraction-card {
                            background: linear-gradient(to right bottom, #f8f9fa, #ffffff);
                            border-radius: 12px;
                            padding: 2rem;
                            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
                            transition: transform 0.3s ease;
                            border: 1px solid #eee;
                            text-align: center;
                        }

                        .attraction-card:hover {
                            transform: translateY(-5px);
                        }

                        .attraction-icon {
                            font-size: 3rem;
                            margin-bottom: 1rem;
                        }

                        .attraction-card h5 {
                            color: var(--primary-color);
                            margin: 1rem 0;
                            font-size: 1.2rem;
                        }

                        .attraction-card p {
                            color: #666;
                            font-size: 0.95rem;
                            line-height: 1.6;
                        }
                        .attractions-section h4{
                            color: var(--primary-color);
                            font-size: 2rem;
                            text-align: center;
                            margin-bottom: 1rem;
                            position: relative;
                            padding-bottom: 1rem;
                        }

                        @media (max-width: 768px) {
                            .attractions-grid {
                                grid-template-columns: 1fr;
                                gap: 1rem;
                            }

                            .attraction-card {
                                padding: 1.5rem;
                            }

                            .attraction-icon {
                                font-size: 2.5rem;
                            }
                            .attractions-section h4{
                            color: var(--primary-color);
                            font-size: 1.5rem;
                            text-align: center;
                            margin-bottom: 1rem;
                            position: relative;
                            padding-bottom: 1rem;
                        }
                        }
                    </style>

                    <style>
                        .booking-guide {
                            margin-top: 4rem;
                            padding: 3rem;
                            background: #fff;
                            border-radius: 15px;
                            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                        }

                        .booking-guide h4 {
                            color: var(--primary-color);
                            font-size: 2rem;
                            text-align: center;
                            margin-bottom: 2rem;
                        }

                        .booking-tips-grid {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 2rem;
                            margin: 2rem 0;
                        }

                        .booking-tip-card {
                            padding: 2rem;
                            background: linear-gradient(to right bottom, #f8f9fa, #ffffff);
                            border-radius: 12px;
                            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
                            border: 1px solid #eee;
                            transition: transform 0.3s ease;
                        }

                        .booking-tip-card:hover {
                            transform: translateY(-5px);
                        }

                        .tip-icon {
                            font-size: 2rem;
                            margin-bottom: 1rem;
                        }

                        .attractions-section {
                            margin-top: 4rem;
                            padding-top: 3rem;
                            border-top: 2px solid #eee;
                        }

                        .attractions-grid {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 2rem;
                            margin: 2rem 0;
                        }

                        .attraction-card {
                            background: #fff;
                            border-radius: 12px;
                            overflow: hidden;
                            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
                            transition: transform 0.3s ease;
                        }

                        .attraction-card:hover {
                            transform: translateY(-5px);
                        }

                        .attraction-image {
                            height: 200px;
                            background-size: cover;
                            background-position: center;
                        }

                        .attraction-card h5 {
                            color: var(--primary-color);
                            padding: 1rem;
                            margin: 0;
                            font-size: 1.2rem;
                        }

                        .attraction-card p {
                            padding: 0 1rem 1rem;
                            color: #666;
                            font-size: 0.95rem;
                        }

                        @media (max-width: 768px) {
                            .booking-tips-grid,
                            .attractions-grid {
                                grid-template-columns: 1fr;
                                gap: 1rem;
                            }

                            .booking-guide {
                                padding: 1.5rem;
                            }

                            .booking-guide h4 {
                                font-size: 1.6rem;
                            }
                        }
                    </style>

                    <style>
                        /* Guide Section Styles */
                        .guide-section {
                            background: #fff;
                            border-radius: 15px;
                            padding: 3rem;
                            margin-bottom: 3rem;
                            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                        }

                        .guide-section h4 {
                            color: var(--primary-color);
                            font-size: 2rem;
                            text-align: center;
                            margin-bottom: 2rem;
                            position: relative;
                            padding-bottom: 1rem;
                        }

                        .guide-section h4::after {
                            content: '';
                            position: absolute;
                            bottom: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 60px;
                            height: 3px;
                            background: var(--primary-color);
                            border-radius: 2px;
                        }

                        .guide-content {
                            background: linear-gradient(to right bottom, #f8f9fa, #ffffff);
                            padding: 2rem;
                            border-radius: 12px;
                            border: 1px solid #eee;
                            margin-bottom: 2rem;
                        }

                        .guide-content p {
                            font-size: 1.1rem;
                            line-height: 1.8;
                            color: #444;
                            margin-bottom: 1rem;
                        }

                        .info-grid {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 2rem;
                            margin: 2.5rem 0;
                        }

                        .info-card {
                            background: #fff;
                            padding: 2rem;
                            border-radius: 12px;
                            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
                            border: 1px solid #eee;
                            transition: transform 0.3s ease;
                        }

                        .info-card:hover {
                            transform: translateY(-5px);
                        }

                        .info-card h5 {
                            color: var (--primary-color);
                            font-size: 1.3rem;
                            margin-bottom: 1rem;
                            padding-bottom: 0.8rem;
                            border-bottom: 2px solid #eee;
                        }

                        .travel-tips {
                            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                            padding: 2.5rem;
                            border-radius: 12px;
                            border: 2px solid var(--primary-color);
                            margin-top: 3rem;
                        }

                        .travel-tips h5 {
                            color: var(--primary-color);
                            font-size: 1.5rem;
                            text-align: center;
                            margin-bottom: 2rem;
                        }

                        .tips-list {
                            list-style: none;
                            padding: 0;
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 1.5rem;
                        }

                        .tips-list li {
                            position: relative;
                            padding-left: 2rem;
                            line-height: 1.6;
                        }

                        .tips-list li:before {
                            content: "✈";
                            position: absolute;
                            left: 0;
                            color: var(--primary-color);
                            font-size: 1.2rem;
                        }

                        .tips-list li strong {
                            color: var(--primary-color);
                            display: block;
                            margin-bottom: 0.3rem;
                        }

                        .cta-block {
                            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
                            color: white;
                            padding: 2.5rem;
                            border-radius: 12px;
                            text-align: center;
                            margin-top: 3rem;
                            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                        }

                        .cta-block p {
                            color: black;
                            font-size: 1.2rem;
                            margin-bottom: rem;
                            line-height: 1.6;
                        }

                        .intro-note {
                            font-size: 1.2rem;
                            color: #555;
                            text-align: center;
                            margin: 2rem 0;
                            line-height: 1.8;
                            padding: 1.5rem;
                            background: #f8f9fa;
                            border-radius: 10px;
                        }

                        .airline-icon, .section-icon, .card-icon, .tips-icon, .cta-icon {
                            font-size: 2.5rem;
                            margin-bottom: 1rem;
                            text-align: center;
                        }

                        .section-icon {
                            font-size: 3rem;
                            margin-bottom: 1.5rem;
                        }

                        .airline-block {
                            text-align: center;
                            padding-top: 2rem;
                        }

                        .airline-block h4 {
                            margin-top: 0.5rem;
                        }

                        .info-card {
                            text-align: center;
                            padding-top: 2rem;
                        }

                        .card-icon {
                            margin-bottom: 1.5rem;
                        }

                        .tips-list li {
                            padding-left: 3rem;
                        }

                        .tips-list li:before {
                            display: none;
                        }

                        .cta-block {
                            text-align: center;
                        }

                        .cta-icon {
                            font-size: 3.5rem;
                            margin-bottom: 1.5rem;
                            animation: bounce 2s infinite;
                        }

                        @keyframes bounce {
                            0%, 100% { transform: translateY(0); }
                            50% { transform: translateY(-10px); }
                        }

                        @media (max-width: 768px) {
                            .airline-icon, .section-icon, .card-icon, .tips-icon {
                                font-size: 2rem;
                            }

                            .cta-icon {
                                font-size: 2.5rem;
                            }
                        }

                        @media (max-width: 768px) {
                            .guide-section {
                                padding: 2rem 1.5rem;
                            }

                            .guide-section h4 {
                                font-size: 1.6rem;
                            }

                            .info-grid {
                                grid-template-columns: 1fr;
                                padding: 0 0rem !important;
                            }

                            .tips-list {
                                grid-template-columns: 1fr;
                            }

                            .guide-content {
                                padding: 1.5rem;
                            }

                            .travel-tips {
                                padding: 1.5rem;
                            }

                            .cta-block {
                                padding: 2rem 1.5rem;
                            }
                        }
                    </style>

                   
                    <div class="booking-guide">
                                <h4>Best Time to Secure a Flight to Lagos from the United Kingdom</h4>
                                <p class="section-intro">Booking the right time for your flight from the United Kingdom to Lagos can make a big difference in both price and convenience. Here’s a guide to help you get the best deals and ensure smooth travel.</p>
                                
                                <div class="booking-tips-grid">
                                    <div class="booking-tip-card">
                                        <div class="tip-icon">📅</div>
                                        <h5>Book Early for the Best Deals</h5>
                                        <p>The earlier you book, the better the chance of securing a good deal. Aim to book your flight 2 to 3 months in advance to get the most competitive prices. Airlines often offer their best rates during this period, especially for direct flights.</p>
                                    </div>

                                    <div class="booking-tip-card">
                                        <div class="tip-icon">❄️</div>
                                        <h5>Avoid Peak Travel Seasons</h5>
                                        <p>Lagos experiences peak tourist season during December to February, which coincides with the dry season in Nigeria. During these months, demand for flights is higher, and prices tend to be more expensive. If you're flexible with your dates, it’s best to avoid these peak months.</p>
                                    </div>

                                    <div class="booking-tip-card">
                                        <div class="tip-icon">💸</div>
                                        <h5>Travel During Off-Peak Months for Lower Prices</h5>
                                        <p>For the best savings, consider flying during off-peak months, such as May to September. These months are less busy, and you can often find cheaper fares as airlines lower prices to attract passengers. Though it’s the rainy season in Lagos, flights tend to be less expensive, and the weather doesn't usually disrupt travel plans.</p>
                                    </div>

                                    <div class="booking-tip-card">
                                        <div class="tip-icon">💰</div>
                                        <h5>Keep an Eye on Sales and Deals</h5>
                                        <p>Don’t forget to watch out for special promotions or seasonal sales. Airlines often run discounts during major events or holidays, so signing up for newsletters or using flight comparison sites can help you spot a great deal when it becomes available.</p>
                                        <p>For the best time to secure your flight to Lagos from the UK, aim to book 2 to 3 months in advance and consider traveling during the off-peak months of May to September for the best prices. Avoid the high-demand period of December to February, and keep an eye out for promotions to ensure you get the best deal possible.</p>
                                    </div>
                                    
                                                              
                                
                                </div>
                                
                            </div>
                            <div class="attractions-section">
    <h4>Key Attractions to Explore in Lagos</h4>
    <p class="section-intro">Lagos, Nigeria's bustling commercial capital, offers a vibrant mix of culture, history, and natural beauty. Whether you're visiting for the first time or returning, here are some must-see attractions in this dynamic city.</p>

    <div class="attractions-grid">
        <div class="attraction-card">
            <div class="attraction-icon">🎨</div>
            <h5>Nike Art Gallery</h5>
            <p>For art lovers, the Nike Art Gallery is a treasure trove of Nigerian culture. This massive gallery showcases a vast collection of traditional and contemporary African art, including paintings, sculptures, and textiles. It's a perfect place to experience Nigeria's rich artistic heritage and pick up unique souvenirs.</p>
        </div>

        <div class="attraction-card">
            <div class="attraction-icon">🌿</div>
            <h5>Lekki Conservation Centre</h5>
            <p>Nature enthusiasts will enjoy the Lekki Conservation Centre, a beautiful reserve offering a peaceful escape from the city’s hustle. You can walk along the canopy bridge, one of the longest in Africa, or spot various species of birds and animals. It’s an ideal spot for a serene and educational experience.</p>
        </div>

        <div class="attraction-card">
            <div class="attraction-icon">🏖️</div>
            <h5>Tarkwa Bay Beach</h5>
            <p>If you’re looking for a beach day, Tarkwa Bay is a popular choice. Known for its calm waters, it’s great for swimming, surfing, and relaxing by the shore. The beach is only accessible by boat, adding to its secluded charm. It’s perfect for a laid-back day in Lagos.</p>
        </div>

        <div class="attraction-card">
            <div class="attraction-icon">🏛️</div>
            <h5>National Museum Lagos</h5>
            <p>For a deeper dive into Nigeria's history, the National Museum Lagos is a must-visit. Located in Onikan, this museum offers fascinating exhibits on Nigeria’s cultural history, including ancient artifacts, sculptures, and traditional costumes. It’s a great place to learn about the country’s past and diverse cultures.</p>
        </div>
    </div>
</div>
                    <div class="faq-section">
                        <div class="section-icon">❓</div>
                        <h4>Frequently Asked Questions</h4>
                        <div class="faq-container">
                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">✈️</div>
                                    <h5>Are there direct flights from the United Kingdom to Lagos?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>Yes, there are direct flights from the United Kingdom to Lagos. Airlines such as British Airways and Nigerian Airways operate non-stop flights, typically departing from London Heathrow Airport.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">⏱️</div>
                                    <h5>How long does it take to fly from the United Kingdom to Lagos?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>A direct flight from the United Kingdom to Lagos takes approximately 6 to 7 hours. However, flights with layovers may take longer depending on the stopover locations and duration.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">📅</div>
                                    <h5>When is the best time to book a flight to Lagos?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>To find the best deals, it’s recommended to book your flight to Lagos 3 to 4 weeks in advance. Traveling during off-peak seasons, like after major holidays, may also help reduce costs.</p>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="conclusion-wrapper">
                            <h4>Conclusion 🔚</h4>
                            <div class="conclusion-block">
                                <p>Booking a flight from the United Kingdom to Lagos is the first step toward an exciting journey to one of Africa's most dynamic cities. Whether you are visiting for business, vacation, or family, Lagos offers a unique and bustling environment that promises to leave you with unforgettable memories. With Tickets to World, we make the flight booking process simple, offering great prices, a variety of flight options, and excellent customer support. Start planning your trip today and enjoy a seamless travel experience from the United Kingdom to Lagos.</p>
                            
                            </div>
                        </div>
                    </div>


                    <style>
                        .why-choose-section {
                            padding: 4rem 0;
                            background: #fff;
                            margin: 3rem 0;
                        }

                        .why-choose-section .section-title {
                            color: var(--primary-color);
                            font-size: 2.2rem;
                            text-align: center;
                            margin-bottom: 1.5rem;
                            position: relative;
                            padding-bottom: 1rem;
                        }

                        .why-choose-section .section-title::after {
                            content: '';
                            position: absolute;
                            bottom: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 80px;
                            height: 4px;
                            background: var(--primary-color);
                            border-radius: 2px;
                        }

                        .why-choose-section .section-intro {
                            text-align: center;
                            font-size: 1.2rem;
                            color: #555;
                            max-width: 800px;
                            margin: 0 auto 3rem;
                            line-height: 1.8;
                        }

                        .benefits-grid {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 2rem;
                            margin-top: 2rem;
                        }

                        .benefit-card {
                            background: linear-gradient(to right bottom, #f8f9fa, #ffffff);
                            padding: 2rem;
                            border-radius: 15px;
                            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
                            border: 1px solid #eee;
                            transition: transform 0.3s ease, box-shadow 0.3s ease;
                        }

                        .benefit-card:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
                        }

                        .benefit-card.highlight {
                            grid-column: span 2;
                            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                            border: 2px solid var(--primary-color);
                        }

                        .benefit-icon {
                            font-size: 2.5rem;
                            margin-bottom: 1rem;
                            text-align: center;
                        }

                        .benefit-card h4 {
                            color: var(--primary-color);
                            font-size: 1.4rem;
                            margin-bottom: 1rem;
                            text-align: center;
                        }

                        .benefit-card p {
                            color: #666;
                            line-height: 1.7;
                            font-size: 1.05rem;
                        }

                        /* Fix for FAQ title */
                        .faq-section h4 {
                            color: var(--primary-color);
                            font-size: 2.2rem;
                            text-align: center;
                            margin: 2rem 0;
                            position: relative;
                            padding-bottom: 1rem;
                        }

                        .faq-section h4::after {
                            content: '';
                            position: absolute;
                            bottom: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 80px;
                            height: 4px;
                            background: var(--primary-color);
                            border-radius: 2px;
                        }

                        @media (max-width: 768px) {
                            .benefits-grid {
                                grid-template-columns: 1fr;
                            }

                            .benefit-card.highlight {
                                grid-column: span 1;
                            }

                            .benefit-card {
                                padding: 1.5rem;
                            }

                            .why-choose-section .section-title {
                                font-size: 1.8rem;
                            }

                            .why-choose-section .section-intro {
                                font-size: 1.1rem;
                                padding: 0 1rem;
                            }

                            .benefit-card h4 {
                                font-size: 1.3rem;
                            }
                        }
                    </style>
                    <style>
                        .faq-section {
                            margin-top: 4rem;
                            padding: 0rem;
                            
                            border-radius: 15px;
                            
                        }

                        .faq-container {
                            max-width: 1200px;
                            margin: 2rem auto;
                            display: grid;
                            gap: 1.5rem;
                        }

                        .faq-item {
                            background: #fff;
                            border-radius: 12px;
                            padding: 0;
                            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
                            border: 1px solid #eee;
                            transition: all 0.3s ease;
                            overflow: hidden;
                        }

                        .faq-item:hover {
                            transform: translateY(-3px);
                            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                        }

                        .faq-question {
                            display: flex;
                            align-items: center;
                            gap: 1rem;
                            padding: 1.5rem;
                            cursor: pointer;
                            background: linear-gradient(to right, #f8f9fa, #ffffff);
                            position: relative;
                        }

                        .question-icon {
                            font-size: 1.5rem;
                            color: var(--primary-color);
                            flex-shrink: 0;
                        }

                        .toggle-icon {
                            position: absolute;
                            right: 1.5rem;
                            color: var(--primary-color);
                            font-size: 1.5rem;
                            transition: transform 0.3s ease;
                        }

                        .faq-item.active .toggle-icon {
                            transform: rotate(45deg);
                        }

                        .faq-question h5 {
                            color: #2c3e50;
                            font-size: 1.1rem;
                            margin: 0;
                            font-weight: 600;
                            flex-grow: 1;
                        }

                        .faq-answer {
                            padding: 0 1.5rem 1.5rem 4rem;
                            color: #444;
                            line-height: 1.6;
                            display: none;
                        }

                        .faq-item.active .faq-answer {
                            display: block;
                        }

                        .faq-answer p {
                            margin: 0;
                            color: #555;
                        }

                        .conclusion-wrapper {
                            margin-top: 3rem;
                            text-align: center;
                            background: linear-gradient(to right bottom, #f8f9fa, #ffffff);
                            padding: 2.5rem;
                            border-radius: 12px;
                            border: 2px solid var(--primary-color);
                        }
                        .conclusion-wrapper h4 {
                            color: var(--primary-color);
                            margin-bottom: 1.5rem;
                            font-size: 2rem;
                        }
                    </style>

                    <script>
                        document.querySelectorAll('.faq-question').forEach(question => {
                            question.addEventListener('click', () => {
                                const item = question.closest('.faq-item');
                                const wasActive = item.classList.contains('active');
                                
                                // Close all items
                                document.querySelectorAll('.faq-item').forEach(faq => {
                                    faq.classList.remove('active');
                                });

                                // Open clicked item if it wasn't active
                                if (!wasActive) {
                                    item.classList.add('active');
                                }
                            });
                        });
                        
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const item = question.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            
            // Close all other answers
            document.querySelectorAll('.faq-item').forEach(faq => {
                if (faq !== item) {
                    faq.classList.remove('active');
                    faq.querySelector('.faq-answer').style.display = 'none';
                }
            });
            
            // Toggle current answer
            item.classList.toggle('active');
            answer.style.display = item.classList.contains('active') ? 'block' : 'none';
        });
    });
});

                    </script>

                </div>
            </div>
        </section>

        <style>
            .route-info-container {
                background: white;
                border-radius: 15px;
                padding: 4rem;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                margin-top: 2rem;
            }

            .route-info-header {
                text-align: center;
                margin-bottom: 3rem;
            }

            .route-info-header h2 {
                color: var(--primary-color);
                font-size: 2.2rem;
                font-weight: 700;
                margin-bottom: 0rem;
            }

            .route-info-divider {
                height: 4px;
                width: 80px;
                background: var(--primary-color);
                margin: 0 auto;
                border-radius: 2px;
            }

            .content-section {
                max-width: 1200px;
                margin: 0 auto;
            }

            .intro-text {
                font-size: 1.2rem;
                line-height: 1.9;
                color: #2c3e50;
                margin-bottom: 3rem;
                padding: 2rem;
                background: linear-gradient(to right bottom, #f8f9fa, #ffffff);
                border-radius: 12px;
                border-left: 5px solid var(--primary-color);
            }

            .airlines-section h3 {
                color: var(--primary-color);
                font-size: 2rem;
                margin: 3rem 0 1.5rem;
                text-align: center;
                font-weight: 600;
            }

            .section-intro {
                font-size: 1.15rem;
                line-height: 1.8;
                margin-bottom: 2.5rem;
                padding: 1.5rem;
                background: #f8f9fa;
                border-radius: 12px;
                box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            }

            .airline-block {
                margin-bottom: 2.5rem;
                padding: 2.5rem;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 3px 15px rgba(0,0,0,0.08);
                border-left: 5px solid var(--primary-color);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .airline-block:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 20px rgba(0,0,0,0.12);
            }

            .airline-block h4 {
                color: #2c3e50;
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
                padding-bottom: 0.8rem;
                border-bottom: 2px solid #eee;
            }

            .airline-name {
                color: var(--primary-color);
                font-weight: 700;
                font-size: 1.1em;
            }

            .airline-description {
                line-height: 1.8;
                color: #444;
            }

            .airline-description p {
                margin-bottom: 1rem;
                font-size: 1.05rem;
            }

            .airline-description p:last-child {
                margin-bottom: 0;
            }

            .conclusion-block {
                color:rgb(6, 6, 6);
                margin-top: 2rem;
                padding: 2.5rem;
                background: linear-gradient(to right bottom, #f8f9fa, #ffffff);
                border-radius: 12px;
                border: 2px solid var(--primary-color);
                box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            }

            .conclusion-block h4 {
                color: var(--primary-color);
                margin-bottom: 1.5rem;
                font-size: 1.6rem;
                text-align: center;
            }

            @media (max-width: 900px) {
                .airline-cards-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 600px) {
                .airline-cards-grid {
                    grid-template-columns: 1fr;
                }

                .airline-card {
                    padding: 1.25rem;
                }
            }

            @media (max-width: 768px) {
                .route-info-container {
                background: white;
                border-radius: 15px;
                padding: 0.6rem;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                margin-top: 2rem;
            }

                .route-info-header h2 {
                    font-size: 1.8rem;
                }

                .intro-text {
                    font-size: 1.1rem;
                    padding: 1.5rem;
                }

                .airline-block {
                    padding: 1.5rem;
                    margin-bottom: 1.5rem;
                }

                .airline-block h4 {
                    font-size: 1.3rem;
                }

                .airline-description p {
                    font-size: 1rem;
                }

                .conclusion-block {
                    padding: 1.5rem;
                }
                .intro-note {
                                    font-size: 1rem;
                                    color: #555;
                                    text-align: center;
                                    margin: 2rem 0;
        grid-template-columns: 1fr;
    }
    
    .search-sidebar {
        position: relative;
        top: 0;
    }

    .info-grid,
    .booking-tips-grid,
    .attractions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 900px) {
    .airline-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .route-info-container {
        padding: 0.6rem;
    }
}

@media (max-width: 768px) {
    .tips-list li {
    padding-left: 0px;
}
.booking-tip-card {
    padding: 0.5rem;
}
.attraction-card{
    padding: 0.5rem;
}
.conclusion-wrapper{
   padding: 0.5rem;
}
.conclusion-wrapper h4 {
   color: var(--primary-color);
   margin-bottom: 0rem;
   margin-top: 1rem;
   font-size: 2rem;
                        }
.conclusion-block{
    padding: 0.5rem;
    margin-top: 0rem;
}
   .simple-hero {
        margin-top: 4rem;
        padding: 2rem 0;
    }

    .simple-hero h1 {
        font-size: 2rem;
    }

    .route-info-container {
        padding: 0.5rem;
    }

    .route-info-header h2 {
        font-size: 1.8rem;
    }

    .intro-text {
        font-size: 1.1rem;
        padding: 1.5rem;
    }

    .airline-block {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .airline-block h4 {
        font-size: 1.3rem;
    }

    .info-grid,
    .booking-tips-grid,
    .attractions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .tips-list {
        grid-template-columns: 1fr;
    }

    .guide-section {
        padding: 2rem 0.5rem;
    }

    .guide-section h4 {
        font-size: 1rem;
    }

    .guide-content {
        padding: 1rem;
        font-size: 0.9rem;
    }

    .travel-tips {
        padding: 1.5rem;
    }

    .cta-block {
        padding: 2rem 1.5rem;
    }

    .airline-icon, 
    .section-icon, 
    .card-icon, 
    .tips-icon {
        font-size: 2rem;
    }

    .cta-icon {
        font-size: 2.5rem;
    }
    .faq-question h5 {
    color: #2c3e50;
    font-size: 0.8rem;
    margin: 0;
    font-weight: 600;
    flex-grow: 1;
   }
}

@media (max-width: 480px) {
    .simple-hero {
        margin-top: 3.5rem;
        padding: 1.5rem 0;
    }

    .simple-hero h1 {
        font-size: 1.3rem;
    }

    .route-container {
        padding: 0 0.5rem;
    }

    .route-info-header h2 {
        font-size: 1.5rem;
    }

    .airlines-section h3 {
        font-size: 1.6rem;
    }

    .airline-block {
        padding: 1.25rem;
    }

    .faq-question {
        padding: 1.25rem;
    }

    .faq-answer {
        padding: 0 1.25rem 1.25rem 3.5rem;
    }
}

@media (max-width: 320px) {
    .simple-hero {
        margin-top: 4rem;
        padding: 1rem 0;
    }

    .simple-hero h1 {
        font-size: 1.1rem;
        padding: 0 0.5rem;
    }

    .route-container {
        padding: 0 0.25rem;
    }

    .content-wrapper {
        gap: 1rem;
    }

    .flights-section h2 {
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }

    .airline-block,
    .faq-question,
    .guide-section {
        padding: 1rem;
    }
}
/* New Simple Hero Style */
.simple-hero {
    background: var(--gradient);
    padding: 2rem 0;
    margin-bottom: 2rem;
    margin-top: 5rem;
    clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
    text-align: center;
}

.simple-hero h1 {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

.flights-section h2 {
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
/* Updated Container Layout */
.route-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

.content-wrapper {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 2.5rem;
    align-items: start;
}

/* Sticky Search Form Fix */
.search-sidebar {
    position: sticky;
    top: 2rem;
    z-index: 100;
    height: fit-content;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .search-sidebar {
        position: relative;
        top: 0;
    }
}

@media (max-width: 768px) {
    .simple-hero h1 {
        font-size: 2rem;
    }
    .simple-hero {
    background: var(--gradient);
    padding: 2rem 0;
    margin-bottom: 2rem;
    margin-top: 4rem;
    clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
    text-align: center;
}
}

@media (max-width: 480px) {
    .simple-hero h1 {
        font-size: 1.5rem;
    }
}

/* Remove old hero styles */
.route-hero,
.hero-bg,
.hero-content,
.hero-text,
.route-stats,
.stat-item {
    display: none;
}


@media (max-width: 480px) {
    .simple-hero {
        margin-top: 3.5rem;
        padding: 1.5rem 0;
    }

    .simple-hero h1 {
        font-size: 1.3rem;
    }

    .route-container {
        padding: 0 0.5rem;
    }
}

@media (max-width: 320px) {
    .simple-hero {
        margin-top: 4rem;
        padding: 1rem 0;
    }

    .simple-hero h1 {
        font-size: 1.1rem;
        padding: 0 0.5rem;
    }

    .route-container {
        padding: 0 0.25rem;
    }

    .content-wrapper {
        gap: 1rem;
    }

    .flights-section h2 {
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
</main>
