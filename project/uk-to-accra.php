<?php
session_start();
require_once '../private/config.php';

// SEO meta tags
$pageTitle = "Flights from UK to Accra | Book Cheap Flights United Kingdom to Ghana";
$metaDescription = "Book affordable flights from United Kingdom to Accra, Ghana. Compare flight prices, schedules, and airlines. Find the best deals on UK to Accra routes with easy booking.";
$canonicalURL = "http://theticketstoworld.co.uk/uk-to-accra";
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
    <meta name="keywords" content="UK to Accra flights, cheap flights to Ghana, London to Accra, flights to Accra, United Kingdom Ghana flights">
<?php
require_once './includes/header.php';


$default_search = [
    'from_location' => 'United Kingdom',
    'to_location' => 'Accra',  // Changed to Accra
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
        <h1>Find the Best Flights from the United Kingdom to Accra ACC</h1>
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
                        // Query specifically for UK to Accra flights
                        $query = "SELECT DISTINCT af.*, alf.* 
                                 FROM available_flights af 
                                 JOIN airline_flights alf ON af.flight_id = alf.flight_id 
                                 WHERE (
                                     (af.from_location = 'United Kingdom' OR 
                                      af.from_location = 'London' OR 
                                      af.from_location = 'Manchester' OR 
                                      af.from_location = 'Birmingham' OR 
                                      af.from_location = 'Edinburgh' OR 
                                      af.from_location = 'Glasgow')
                                     AND 
                                     af.to_location = 'Accra'
                                 )";
                        
                        if ($booking_details['FAirLine'] !== 'ALL') {
                            $query .= " AND alf.airline_name = ?";
                        }
                        
                        $stmt = $conn->prepare($query);
                        
                        if ($booking_details['FAirLine'] !== 'ALL') {
                            $airline = getAirlineName($booking_details['FAirLine']);
                            $stmt->bind_param("s", $airline);
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
                            echo "<div class='no-flights'>No direct flights available from United Kingdom to Accra currently.</div>";
                        }
                        $stmt->close();
                    } catch (Exception $e) {
                        error_log("Error in uk-to-accra.php: " . $e->getMessage());
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
                    <h2>United Kingdom to Accra Flight Guide</h2>
                    <div class="route-info-divider"></div>
                </div>

                <div class="content-section">
                    <div class="intro-text">
                        <p>Planning a trip from the United Kingdom to Accra? Whether you're visiting for business, leisure, or to reconnect with family, Accra, the vibrant capital of Ghana, offers a unique cultural experience. Known for its bustling markets, rich history, and warm hospitality, Accra is a destination that welcomes travelers from around the world.</p>
                        <p> At Tickets to World, we make booking flights from the United Kingdom to Accra simple and affordable. With a variety of flight options, competitive prices, and dedicated customer service, your journey to this exciting West African city starts with us.</p>
                    </div>

                    <div class="airlines-overview">
                        <h2 class="section-title">Airlines Operating Flights from the UK to Accra</h2>
                        
                        <div class="overview-intro">
                            <p>If you’re looking to travel from the United Kingdom to Accra, Ghana, you have several airline options to choose from. Whether you’re going for business, leisure, or to visit family, here are the airlines that operate flights on this route, with both direct and connecting options available.</p>
                        </div>

                        <div class="airlines-grid">
                            <!-- British Airways Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">✈️</span>
                                    <h3>British Airways</h3>
                                    <span class="route-badge direct">Offers Direct Flights to Accra</span>
                                </div>
                                <div class="airline-content">
                                    <p>British Airways is one of the primary airlines offering direct flights from London Heathrow to Accra. The direct flight typically takes around 6 to 7 hours, making it a fast and convenient option. Known for its excellent service, British Airways provides comfortable seating, entertainment, and meals, making the flight more enjoyable. With regular departures, it’s a reliable choice for your trip.</p>
                                </div>
                            </div>

                            <!-- KLM Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🛩️</span>
                                    <h3>KLM Royal Dutch Airlines</h3>
                                    <span class="route-badge direct">Connecting Flights via Amsterdam</span>
                                </div>
                                <div class="airline-content">
                                    <p>If you’re open to connecting flights, KLM offers a route with a stop in Amsterdam before continuing to Accra. While this adds a bit of extra travel time, KLM is known for its comfortable service and well-organized connections. The Amsterdam stop gives you a chance to stretch your legs before hopping on your second flight to Accra.</p>
                                </div>
                            </div>

                            <!-- Air France Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🛫</span>
                                    <h3>Air France</h3>
                                    <span class="route-badge connecting">Provides Connecting Flights via Paris</span>
                                </div>
                                <div class="airline-content">
                                    <p>Air France operates flights from the UK to Accra with a layover in Paris. The Paris stop is typically short, and it’s a good way to break up the journey. While it may not be as quick as a direct flight, Air France offers quality service, and the in-flight experience is comfortable and relaxing.</p>
                                </div>
                            </div>

                            <!-- Turkish Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🛬</span>
                                    <h3>Turkish Airlines</h3>
                                    <span class="route-badge connecting">Offers Connecting Flights via Istanbul</span>
                                </div>
                                <div class="airline-content">
                                    <p>For those willing to make a longer journey, Turkish Airlines offers flights with a layover in Istanbul. The flight from London to Istanbul takes around 3.5 hours, followed by a flight to Accra. Although it’s not a direct flight, Turkish Airlines provides excellent service, spacious seats, and a wide range of entertainment options, making it a popular choice for travelers.</p>
                                </div>
                            </div>
                            <!-- Emirates Airlines Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🚀</span>
                                    <h3>Emirates</h3>
                                    <span class="route-badge connecting">Connecting Flights via Dubai</span>
                                </div>
                                <div class="airline-content">
                                    <p>Emirates is another airline offering connecting flights to Accra, with a layover in Dubai. This option adds extra time to your travel but comes with world-class service. Emirates is known for its high-quality customer service, in-flight entertainment, and comfortable seats. A quick stop in Dubai lets you refresh before the final leg of your journey to Accra.</p>
                                </div>
                            </div>

                            <!--Lufthansa Card -->
                            <div class="airline-card">
                                <div class="airline-header">
                                    <span class="airline-icon">🛬</span>
                                    <h3>Lufthansa</h3>
                                    <span class="route-badge connecting">Offers Connecting Flights via Frankfurt</span>
                                </div>
                                <div class="airline-content">
                                    <p>Lufthansa provides another connecting option for flights from the UK to Accra with a stop in Frankfurt. Known for its excellent service, Lufthansa ensures a comfortable and enjoyable journey. The stop in Frankfurt is an opportunity to relax before continuing to Accra on your second flight.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="content-wrapper123">
    <div class="flight-title123">
         <h2>Flight Duration from the UK to Accra</h2>
    </div>
    <div class="flight-content123">
        <p>📍 When traveling from the United Kingdom to Accra, Ghana, flight duration is an important factor to consider. The time it takes to reach Accra depends on whether you’re taking a direct flight or connecting through another city. Here’s a breakdown of the flight durations based on different routes.</p>
        
        <div class="direct-flights123">
            <h2>🛫 Direct Flights from London to Accra</h2>
            <p>🕒 The fastest way to travel from the UK to Accra is by taking a direct flight, which typically departs from London Heathrow. Direct flights usually take between <strong>6 to 7 hours</strong>. This is the most time-efficient option, allowing you to get to your destination without any long layovers or connections.</p>
        </div>

        <div class="connecting-flights123">
            <h2>🔄 Connecting Flights with Layovers</h2>
            <p>🕒 If you choose a connecting flight, the overall travel time will be longer. The duration depends on your layover city and how long you spend there. Common layover cities for flights to Accra include:</p>
            
            <ul>
                <li> <strong>Via Amsterdam (KLM):</strong> Flight time from London to Amsterdam is about <strong>1 hour</strong>, and then from Amsterdam to Accra takes around <strong>6 to 7 hours</strong>. Total travel time is approximately <strong>8 to 9 hours</strong>, depending on layover duration.</li>
                <li> <strong>Via Paris (Air France):</strong> From London to Paris takes about <strong>1 hour</strong>, with another <strong>6 to 7 hours</strong> from Paris to Accra. Expect a total travel time of around <strong>8 to 9 hours</strong>, depending on your stopover.</li>
                <li> <strong>Via Istanbul (Turkish Airlines):</strong> The flight from London to Istanbul takes around <strong>3.5 hours</strong>, and then from Istanbul to Accra, it’s another <strong>7 hours</strong>. The total journey time is roughly <strong>10 to 11 hours</strong>.</li>
                <li> <strong>Via Dubai (Emirates):</strong> London to Dubai is approximately <strong>7 hours</strong>, followed by a flight to Accra of around <strong>8 hours</strong>. The total travel time can be <strong>15 hours or more</strong>, depending on layover time.</li>
                <li> <strong>Via Frankfurt (Lufthansa):</strong> The London to Frankfurt leg takes about <strong>1 hour</strong>, and from Frankfurt to Accra, the flight is around <strong>6 to 7 hours</strong>. Total travel time is usually between <strong>8 and 9 hours</strong>, depending on the layover.</li>
            </ul>
        </div>

        <p>If you’re looking for the quickest route, direct flights from London to Accra are your best option, with a flight time of around <strong>6 to 7 hours</strong>. However, if you're considering connecting flights, be prepared for longer journey times, which can range from <strong>8 to 15 hours</strong> depending on the layover duration and the airline chosen.</p>
    </div>
</div>





                    <style>
.content-wrapper123 {
    background: white;
    border-radius: 15px;
    padding: 4rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-top: 2rem;
    max-width: 1200px;
    margin: 2rem auto;
    font-family: 'Arial', sans-serif;
}

.flight-title123 h2 {
    color: var(--primary-color);
    font-size: 2.5rem;
    text-align: center;
    font-weight: bold;
    margin-bottom: 1rem;
}

.flight-content123 p {
    font-size: 1.2rem;
    line-height: 1.9;
    color: #2c3e50;
    margin-bottom: 2rem;
}

.direct-flights123, .connecting-flights123 {
    margin-bottom: 2.5rem;
    padding: 2rem;
    background: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-left: 5px solid var(--primary-color);
}

.direct-flights123 h2, .connecting-flights123 h2 {
    color: var(--primary-color);
    font-size: 1.8rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.direct-flights123 p, .connecting-flights123 p {
    font-size: 1.15rem;
    color: #444;
}

.connecting-flights123 ul {
    list-style-type:circle ;
    padding-left: 0;
    margin: 1rem 0;
}

.connecting-flights123 ul li {
    margin-bottom: 1rem;
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
}

.connecting-flights123 ul li:before {
    content: "";
}



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
                            .content-wrapper123 {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-top: 2rem;
    max-width: 1200px;
    margin: 2rem auto;
    font-family: 'Arial', sans-serif;
}
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
                            color: var(--primary-color);
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
                                <h4>Best Time to Book Flights from the UK to Accra</h4>
                                <p class="section-intro">When planning a trip from the United Kingdom to Accra, Ghana, knowing the best time to book your flight can save you money and ensure a smooth travel experience. Whether you're planning your trip well in advance or booking closer to your departure, timing is key. Here's a guide to help you choose the best time to secure your flight.</p>
                                
                                <div class="booking-tips-grid">
                                    <div class="booking-tip-card">
                                        <div class="tip-icon">📅</div>
                                        <h5>Book Early for the Best Prices</h5>
                                        <p>Generally, booking your flight several months in advance will give you access to the best fares. For international flights like those from the UK to Accra, it's recommended to book at least 2 to 3 months before your departure. Airlines often offer their lowest prices early on, so if you can plan ahead, you'll likely find cheaper options.</p>
                                    </div>

                                    <div class="booking-tip-card">
                                        <div class="tip-icon">💰</div>
                                        <h5>Check for Deals and Discounts</h5>
                                        <p>Don’t forget to keep an eye on special promotions or sales. Airlines sometimes offer discounts during specific times of the year or during major sales events. Signing up for airline newsletters or using flight comparison websites can alert you to any upcoming discounts on flights from the UK to Accra.</p>
                                        
                                    </div>
                                    
                                                              
                                
                                </div>
                                
                            </div>

                            <div class="why-choose-section">
                        <h3 class="section-title">Why Choose Tickets to World for Your Flights to Accra?</h3>
                        <p class="section-intro">When it comes to booking your flight from the United Kingdom to Accra, Ghana, choosing the right platform is key to ensuring a hassle-free and affordable experience. Tickets to World stands out as a top choice for travelers heading to Accra. Here’s why you should consider booking your flights through us.</p>

                        <div class="benefits-grid">
                            <div class="benefit-card">
                                <div class="benefit-icon">💰</div>
                                <h4>Affordable Prices for Every Budget</h4>
                                <p>At Tickets to World, we understand that cost plays a significant role when booking flights. We work hard to find the best deals and offer competitive prices, whether you’re traveling on a budget or looking for premium services.</p>
                                <p>Our goal is to make sure you get great value for your money without sacrificing comfort or convenience. You can count on us to find you the best fares for flights to Accra, allowing you to keep your travel plans within budget. </p>
                            </div>

                            <div class="benefit-card">
                                <div class="benefit-icon">📱</div>
                                <h4>Convenient and Easy-to-Use Platform</h4>
                                <p>Booking a flight should be simple, and that’s exactly what we offer. Our platform is user-friendly, allowing you to search and compare flight options with ease. Whether you’re looking for direct flights or connections, we provide detailed options to help you make an informed decision. Our booking system is streamlined and designed to make your experience as smooth as possible.</p>
                            </div>

                            <div class="benefit-card">
                                <div class="benefit-icon">🛫</div>
                                <h4>A Wide Range of Airlines to Choose From</h4>
                                <p>We partner with top airlines that fly to Accra from the UK, including <strong>British Airways, KLM, Air France, Turkish Airlines, and Emirates.</strong> This gives you a variety of options, whether you prefer a direct flight or don’t mind a layover.</p>
                                <p>With us, you can compare airlines, schedules, and prices all in one place, ensuring that you find the best flight that fits your travel needs.</p>
                            </div>

                            <div class="benefit-card">
                                <div class="benefit-icon">🙋‍♂️</div>
                                <h4>Excellent Customer Service</h4>
                                <p>Booking flights can sometimes raise questions or concerns, and that's where our customer service team steps in. At Tickets to World, we pride ourselves on providing exceptional support throughout your booking journey.</p>
                                <p>Whether you need assistance with your booking or have questions about your flight to Accra, our team is here to guide you every step of the way. We’re dedicated to ensuring that you feel supported and confident throughout the entire process.</p>
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
                                    <h5>Are there direct flights from the United Kingdom to Accra?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>Yes, there are direct flights from the United Kingdom to Accra, typically departing from London Heathrow Airport. Airlines such as British Airways and Kenya Airways offer direct routes to Accra.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">⏱️</div>
                                    <h5>How long is the flight from the United Kingdom to Accra?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>A direct flight from the United Kingdom to Accra takes around 6 to 7 hours. Flights with layovers may take longer, depending on the connection time and the stopover city.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">📅</div>
                                    <h5>When is the best time to book flights from the United Kingdom to Accra?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>To get the best prices, it’s advisable to book your flight at least 3 to 4 weeks in advance. Traveling outside peak seasons such as summer holidays and Christmas can also help you find cheaper flights.</p>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="conclusion-wrapper">
                            <h4>Conclusion 🔚</h4>
                            <div class="conclusion-block">
                                <p>Booking a flight from the United Kingdom to Accra is your first step toward an exciting adventure in one of West Africa’s most dynamic cities. Whether you’re visiting for business or exploring Accra’s rich culture and history, we make it easy and affordable to book your flight with Tickets to World. From direct flights to great deals and reliable customer service, we ensure that your trip to Accra is as seamless as possible. Start planning your journey today and experience the warmth and energy of Accra.</p>
                            
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
                            max-width: 1000px;
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



<style>
.flight-duration-wrapper {
    background: white;
    border-radius: 15px;
    padding: 3rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin: 2rem 0;
}

.flight-duration-header {
    text-align: center;
    margin-bottom: 2rem;
}

.flight-duration-header h2 {
    color: var(--primary-color);
    font-size: 2rem;
    margin-bottom: 1rem;
}

.duration-divider {
    height: 4px;
    width: 80px;
    background: var(--primary-color);
    margin: 0 auto;
    border-radius: 2px;
}

.duration-intro {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 2rem;
    color: #555;
    font-size: 1.1rem;
    line-height: 1.6;
}

.duration-cards {
    display: grid;
    gap: 2rem;
}

.duration-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
    border: 1px solid #eee;
}

.duration-card.direct {
    border-left: 5px solid var(--primary-color);
}

.card-header {
    padding: 1.5rem;
    background: linear-gradient(to right, #f8f9fa, #fff);
    border-bottom: 2px solid #eee;
}

.duration-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
}

.duration-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #e8f5e9;
    color: #2e7d32;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
}

.card-content {
    padding: 1.5rem;
}

.time-info {
    text-align: center;
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.time-info .time {
    display: block;
    font-size: 2rem;
    color: var(--primary-color);
    font-weight: bold;
}

.time-info .label {
    color: #666;
    font-size: 0.9rem;
}

.connecting-routes {
    margin-top: 2rem;
}

.connecting-routes h3 {
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 1.5rem;
}

.routes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.route-card {
    background: #fff;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid #eee;
    transition: transform 0.3s ease;
}

.route-card:hover {
    transform: translateY(-5px);
}

.route-icon {
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.route-card h4 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #eee;
}

.route-details {
    list-style: none;
    padding: 0;
    margin: 0;
}

.route-details li {
    margin-bottom: 0.5rem;
    color: #555;
}

.note {
    text-align: center;
    color: #666;
    font-size: 0.9rem;
    margin-top: 1rem;
    font-style: italic;
}

@media (max-width: 768px) {
    .flight-duration-wrapper {
        padding: 1.5rem;
    }

    .flight-duration-header h2 {
        font-size: 1.5rem;
    }

    .routes-grid {
        grid-template-columns: 1fr;
    }

    .route-card {
        padding: 1rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
</main>
