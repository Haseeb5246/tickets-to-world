<?php
require_once '../private/config.php';
// Remove session_start() from here since it's handled in config.php

// SEO meta tags
$pageTitle = "Flights from UK to Abidjan | Book Cheap Flights United Kingdom to Ivory Coast";
$metaDescription = "Book affordable flights from the United Kingdom to Abidjan, Ivory Coast. Compare flight prices, schedules, and airlines. Find the best deals on UK to Abidjan routes with easy booking.";
$canonicalURL = "http://theticketstoworld.co.uk/uk-to-abidjan";
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
    <meta name="keywords" content="UK to Abidjan flights, cheap flights to Ivory Coast, London to Abidjan, flights to Abidjan, United Kingdom Ivory Coast flights">
<?php
require_once './includes/header.php';

// Pre-set search parameters for this specific route
$default_search = [
    'from_location' => 'United Kingdom',
    'to_location' => 'Abidjan',  // Changed from Abuja to Abidjan
    'trip_type' => 'round-trip',
    'FClsType' => 'ECONOMY',
    'FAirLine' => 'ALL',
    'FAdult' => 1,
    'FChild' => 0,
    'FInfant' => 0,
    'departure_date' => date('Y-m-d'), // Today's date
    'return_date' => date('Y-m-d', strtotime('+7 days')) // Default return date
];

// Use default search parameters if no session data exists
if (!isset($_SESSION['search_data'])) {
    $_SESSION['search_data'] = $default_search;
}

$booking_details = $_SESSION['search_data'];

// Ensure the from/to locations are set correctly for this route
$booking_details['from_location'] = $default_search['from_location'];
$booking_details['to_location'] = $default_search['to_location'];
?>

<main class="route-page">
    <div class="simple-hero">
        <h1>Booking Flights from the United Kingdom to Abidjan (ABJ)</h1>
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
                        // Query specific to UK-Abidjan route
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
                        $abidjan_term = '%abidjan%';  // Changed from abuja to abidjan
                        
                        if ($booking_details['FAirLine'] !== 'ALL') {
                            $airline = getAirlineName($booking_details['FAirLine']);
                            $stmt->bind_param("ssssss", 
                                $uk_terms[0], 
                                $uk_terms[1], 
                                $uk_terms[2], 
                                $uk_terms[3], 
                                $abidjan_term, 
                                $airline
                            );
                        } else {
                            $stmt->bind_param("sssss", 
                                $uk_terms[0], 
                                $uk_terms[1], 
                                $uk_terms[2], 
                                $uk_terms[3], 
                                $abidjan_term
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
                        error_log("Error in united-kingdom-to-abuja.php: " . $e->getMessage());
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
                    <h2>United Kingdom to Abidjan (ABJ)</h2>
                    <div class="route-info-divider"></div>
                </div>

                <div class="content-section">
                    <div class="intro-text">
                        <p>Planning a trip from the United Kingdom to Abidjan? Whether you're visiting for business, leisure, or exploring new opportunities, Abidjan offers a vibrant cultural experience and a bustling city atmosphere. Known as the economic capital of Côte d'Ivoire, Abidjan is a city full of life, with a rich history and modern infrastructure. At Tickets to World, we make booking flights from the United Kingdom to Abidjan easy and affordable. With a range of flight options, great deals, and excellent customer service, your journey to this exciting destination begins with us.</p>
                    </div>

                    <div class="airlines-overview">
    <h2 class="section-title">Airlines Offering Flights from the United Kingdom to Abidjan</h2>
    
    <div class="overview-intro">
        <p>Planning a trip from the United Kingdom to Abidjan? There are several airlines that can take you to this bustling city in Côte d'Ivoire. Let's dive into which carriers operate flights from the UK to Abidjan, making your journey to this West African hub smoother and easier.</p>
    </div>

    <div class="airlines-grid">
        <!-- British Airways Card -->
        <div class="airline-card">
            <div class="airline-header">
                <span class="airline-icon">✈️</span>
                <h3>British Airways</h3>
                <span class="route-badge direct">Direct Flights</span>
            </div>
            <div class="airline-content">
                <p>British Airways is one of the most well-known airlines operating direct flights from London Heathrow to Abidjan. It's a reliable choice for those seeking comfort, efficiency, and good customer service.</p>
            </div>
        </div>

        <!-- Air France Card -->
        <div class="airline-card">
            <div class="airline-header">
                <span class="airline-icon">🛩️</span>
                <h3>Air France</h3>
                <span class="route-badge connecting">Via Paris</span>
            </div>
            <div class="airline-content">
                <p>Air France offers convenient connections via Paris, allowing you to hop on a flight to Abidjan with a layover in the French capital. This option is ideal for those who prefer a seamless connection between their European and African destinations.</p>
            </div>
        </div>

        <!-- KLM Card -->
        <div class="airline-card">
            <div class="airline-header">
                <span class="airline-icon">🛫</span>
                <h3>KLM Royal Dutch Airlines</h3>
                <span class="route-badge connecting">Via Amsterdam</span>
            </div>
            <div class="airline-content">
                <p>Flying via Amsterdam, KLM provides another excellent option for UK-based travelers. With a wide array of connections, KLM ensures you get to Abidjan with minimal hassle.</p>
            </div>
        </div>

        <!-- Air Côte d'Ivoire Card -->
        <div class="airline-card">
            <div class="airline-header">
                <span class="airline-icon">🛬</span>
                <h3>Air Côte d'Ivoire</h3>
                <span class="route-badge connecting">Via Paris</span>
            </div>
            <div class="airline-content">
                <p>Air Côte d'Ivoire also connects the UK with Abidjan, with flights often routed through Paris. For travelers seeking an African carrier for their journey, this is a solid choice.</p>
            </div>
        </div>
    </div>

    <div class="flight-options">
        <h3>Flight Options and Booking Tips</h3>
        <div class="options-grid">
            <div class="option-card">
                <h4>Direct vs. Connecting Flights</h4>
                <p>While British Airways provides direct flights, other airlines like Air France and KLM operate with a layover. Depending on your schedule, you can choose between a quicker direct flight or one with a more leisurely layover for a bit of rest before the next leg of your journey.</p>
            </div>
            <div class="option-card">
                <h4>Flexible Travel Dates</h4>
                <p>Ticket prices can fluctuate depending on the time of year and how flexible you are with your travel dates. Use flexible search tools when booking to find the best deals.</p>
            </div>
            <div class="option-card">
                <h4>Booking Your Flight</h4>
                <p>You can easily book flights to Abidjan on popular booking platforms or directly through the airline's website. It's worth comparing prices to find the most affordable options.</p>
            </div>
        </div>
    </div>

    <div class="overview-conclusion">
        <h3>Why Choose Flights to Abidjan?</h3>
        <p>Whether you're heading for business or leisure, the variety of airlines and flight options make it easy to plan your trip from the UK to Abidjan. With direct flights available, and layovers offering brief breaks in major European hubs, you’ll find a flight that suits your needs. The next time you’re planning to head to Côte d'Ivoire, make sure to check out the great options provided by British Airways, Air France, KLM, and Air Côte d'Ivoire.</p>
        <div class="cta-message">
            <p>At TicketstoWorld, we offer helpful information to make booking your flights as simple as possible. Don’t hesitate to reach out if you need more details!</p>
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
    padding: 1rem 1.5rem; /* Balanced padding */
    background: #ff6b6b; /* Warm and inviting red shade */
    color: #ffffff; /* Crisp white text for contrast */
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    font-size: 1.2rem; /* Enhanced font size for better readability */
    font-weight: 600; /* Semi-bold for emphasis */
    text-align: center; /* Center align text for a focused CTA */
    transition: transform 0.2s ease, box-shadow 0.2s ease; /* Smooth hover effect */
}

.cta-message:hover {
    background: #ff4c4c; /* Slightly darker shade for hover */
    transform: scale(1.03); /* Slight zoom effect on hover */
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
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

 
                    <div class="flight-duration-section">
                        <h3>Flight Duration from the United Kingdom to Abidjan</h3>
                        <p class="section-intro">When planning your trip from the United Kingdom to Abidjan, one of the key factors to consider is the flight duration. Depending on whether you are flying directly or with a layover, the travel time can vary.</p>

                        <div class="duration-cards">
                            <div class="duration-card direct">
                                <div class="card-icon">⚡</div>
                                <h4>Direct Flights to Abidjan</h4>
                                <p>If you opt for a direct flight from the UK, specifically from London Heathrow, you can expect to spend approximately <strong>6 to 7 hours</strong> in the air. British Airways is one of the main carriers offering direct flights to Abidjan, making this the quickest and most convenient option for reaching the Ivorian capital.</p>
                            </div>

                            <div class="duration-card layover">
                                <div class="card-icon">🛄</div>
                                <h4>Flights with Layovers</h4>
                                <p>For flights that involve a layover, such as those with Air France or KLM, the total journey time can be longer. Layovers in cities like Paris or Amsterdam usually add around <strong>3 to 5 hours</strong> to the total flight time. So, if you're flying with a connection, the entire trip could take anywhere from <strong>9 to 12 hours</strong>, depending on the duration of the layover.</p>
                            </div>
                        </div>

                        <div class="factors-section">
                            <h4>Factors Influencing Flight Time</h4>
                            <div class="factors-grid">
                                <div class="factor-card">
                                    <div class="factor-icon">🌤️</div>
                                    <h5>Weather Conditions</h5>
                                    <p>Bad weather can occasionally cause delays or route adjustments.</p>
                                </div>

                                <div class="factor-card">
                                    <div class="factor-icon">⏱️</div>
                                    <h5>Layover Times</h5>
                                    <p>Longer layovers at connecting airports will naturally increase your total flight time.</p>
                                </div>

                                <div class="factor-card">
                                    <div class="factor-icon">✈️</div>
                                    <h5>Airport Traffic</h5>
                                    <p>Sometimes, waiting for a gate or experiencing delays during taxiing can add some extra time to your overall trip.</p>
                                </div>
                            </div>
                        </div>

                        <div class="duration-cta">
                            <p>Whether you're flying directly or with a layover, getting to Abidjan from the UK is a manageable journey. Direct flights take about 6 to 7 hours, while connecting flights can extend the journey by a few more hours. The choice between the two depends on your preferences for convenience versus cost.</p>
                            <div class="cta-box">
                                <h4>Ready to Book Your Next Adventure? 🌍</h4>
                                <p>Let TicketstoWorld help you find the best flights to Abidjan!</p>
                            </div>
                        </div>
                    </div>

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

                    <div class="why-choose-section">
                        <h3 class="section-title">Why Choose Tickets to World for Booking Flights to Abidjan?</h3>
                        <p class="section-intro">When it comes to booking your flights to Abidjan, you want a service that offers convenience, reliability, and great deals. That's where Tickets to World comes in. Here's why you should choose us for your next flight booking to Abidjan.</p>

                        <div class="benefits-grid">
                            <div class="benefit-card">
                                <div class="benefit-icon">💰</div>
                                <h4>Competitive Prices and Great Deals</h4>
                                <p>At Tickets to World, we understand how important it is to get the best price for your flight. We offer competitive pricing on flights to Abidjan, ensuring that you get great value for your money. By comparing prices across multiple airlines and booking platforms, we make it easy for you to find affordable flights that match your budget.</p>
                            </div>

                            <div class="benefit-card">
                                <div class="benefit-icon">✈️</div>
                                <h4>Wide Range of Airline Options</h4>
                                <p>Whether you're flying directly with British Airways or making a connection via Air France or KLM, we offer a broad selection of airlines to choose from. Our platform allows you to compare different airlines and their offerings, helping you find the best option based on your preferences, whether it's flight time, amenities, or budget.</p>
                            </div>

                            <div class="benefit-card">
                                <div class="benefit-icon">🎯</div>
                                <h4>Convenience and Easy Booking Process</h4>
                                <p>Booking your flight to Abidjan should be quick and easy, and that's exactly what we provide at Tickets to World. With our user-friendly website, you can search for flights, compare options, and book your ticket in just a few simple steps. No complicated procedures or hidden fees—just straightforward booking.</p>
                            </div>

                            <div class="benefit-card">
                                <div class="benefit-icon">🤝</div>
                                <h4>Customer Support and Assistance</h4>
                                <p>If you need any assistance during your booking process, our dedicated customer support team is here to help. Whether you need help choosing the right flight, have questions about baggage policies, or need last-minute changes to your booking, we're ready to assist you.</p>
                            </div>

                            <div class="benefit-card">
                                <div class="benefit-icon">🔄</div>
                                <h4>Flexible Travel Options</h4>
                                <p>Plans can change, and we get that. That's why we offer flexible travel options, allowing you to adjust your itinerary if needed. With our flexible policies, you can have peace of mind knowing that you have the freedom to make changes should your travel dates shift.</p>
                            </div>

                            <div class="benefit-card">
                                <div class="benefit-icon">🎁</div>
                                <h4>Special Offers and Discounts</h4>
                                <p>We regularly feature exclusive deals, promotions, and discounts, so you can save even more on your flight to Abidjan. Be sure to check our website for the latest offers and take advantage of discounted prices to make your travel even more affordable.</p>
                            </div>

                            <div class="benefit-card highlight">
                                <h4>Expert Travel Advice</h4>
                                <p>Not sure which airline or route is best for you? Our team of travel experts is here to offer advice and recommendations tailored to your needs. We can help you choose the flight that best fits your schedule, budget, and travel preferences, ensuring you get the most out of your trip to Abidjan.</p>
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

                    <div class="faq-section">
                        <div class="section-icon">❓</div>
                        <h4>Frequently Asked Questions</h4>
                        <div class="faq-container">
                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">✈️</div>
                                    <h5>Are there direct flights from the United Kingdom to Abidjan?                                    </h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>Currently, there are no direct flights from the United Kingdom to Abidjan. Most flights involve a layover, typically in cities like Paris, Brussels, or Amsterdam.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">⏱️</div>
                                    <h5>What is the best time to book flights to Abidjan?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>It’s recommended to book your flight at least 3 to 4 weeks in advance to get the best prices. Traveling during the off-peak seasons (such as outside major holidays) can help you find more affordable options.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">📅</div>
                                    <h5>How long is the flight from the United Kingdom to Abidjan?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>A flight from the United Kingdom to Abidjan usually takes around 6 to 7 hours if it's a direct flight. With layovers, the total flight time can range from 10 to 12 hours.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">🧳</div>
                                    <h5>What is the baggage allowance for flights to Abidjan?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>Baggage allowances vary by airline, but typically, you can expect one carry-on bag and one checked bag. Be sure to check your airline’s specific policy for weight limits and additional charges.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <div class="question-icon">🛂</div>
                                    <h5>Do I need a visa to enter Abidjan?</h5>
                                    <div class="toggle-icon">+</div>
                                </div>
                                <div class="faq-answer">
                                    <p>Yes, UK citizens will need a visa to enter Côte d'Ivoire. Be sure to apply for the necessary visa ahead of time to ensure a smooth entry into the country.                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="conclusion-wrapper">
                            <h4>Ready for Your Journey? 🌍</h4>
                            <div class="conclusion-block">
                                <p>Flying from the United Kingdom to Abidjan is an exciting journey to one of West Africa's most vibrant cities. Whether you’re heading there for business, leisure, or to explore, Abidjan offers a unique blend of culture, history, and modern amenities. At Tickets to World, we make booking flights from the UK to Abidjan easy and affordable, offering a variety of flight options, expert support, and great prices. Start your travel planning with us today, and get ready for an unforgettable experience in Abidjan!</p>
                            
                            </div>
                        </div>
                    </div>

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
                margin-top: 4rem;
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
.conclusion-block{
    padding: 0.5rem;
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
