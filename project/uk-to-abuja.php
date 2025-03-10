<?php
session_start();
require_once '../private/config.php';

// SEO meta tags
$pageTitle = "Flights from UK to Abuja | Book Cheap Flights United Kingdom to Nigeria";
$metaDescription = "Compare and book flights from United Kingdom to Abuja. Find the best deals on direct and connecting flights. Easy booking with competitive prices on UK to Abuja routes.";
$canonicalURL = "http://theticketstoworld.co.uk/uk-to-abuja";
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
    <meta name="keywords" content="UK to Abuja flights, cheap flights to Abuja, London to Abuja, flights to Nigeria, United Kingdom Nigeria flights">
<?php require_once './includes/header.php'; ?>

<!-- Pre-set search parameters for this specific route -->
<?php
$default_search = [
    'from_location' => 'United Kingdom',
    'to_location' => 'Abuja',
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
        <h1>Flights from the United Kingdom to Abuja (ABV)</h1>
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
                        // Query specific to UK-Abuja route
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
                        $abuja_term = '%abuja%';
                        
                        if ($booking_details['FAirLine'] !== 'ALL') {
                            $airline = getAirlineName($booking_details['FAirLine']);
                            $stmt->bind_param("ssssss", 
                                $uk_terms[0], 
                                $uk_terms[1], 
                                $uk_terms[2], 
                                $uk_terms[3], 
                                $abuja_term, 
                                $airline
                            );
                        } else {
                            $stmt->bind_param("sssss", 
                                $uk_terms[0], 
                                $uk_terms[1], 
                                $uk_terms[2], 
                                $uk_terms[3], 
                                $abuja_term
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
                    <h2>United Kingdom to Abuja (ABV)</h2>
                    <div class="route-info-divider"></div>
                </div>

                <div class="content-section">
                    <div class="intro-text">
                        <p>Planning a trip from the United Kingdom to Abuja, the vibrant capital of Nigeria? Whether you’re traveling for business, leisure, or to visit loved ones, we’re here to help you find affordable and convenient flights that suit your travel needs. Abuja, known for its stunning architecture, cultural heritage, and warm hospitality, is an exciting destination waiting to be explored.</p>
                    </div>

                    <div class="airlines-section">
                        <h3>Best Airlines Offering Flights from the United Kingdom to Abuja</h3>
                        <p class="section-intro">Are you planning a trip to Abuja from the United Kingdom? The good news is you have several airlines to choose from, each offering reliable service and varying levels of comfort. Let’s break it down so you can pick the one that works best for your needs.</p>

                        <div class="airline-details">
                            <div class="airline-block">
                                <div class="airline-icon">✈️</div>
                                <h4><span class="airline-name">British Airways</span> Offers Direct and Hassle-Free Flights</h4>
                                <div class="airline-description">
                                    <p>British Airways is a popular choice for those looking for a direct connection from London to Abuja. Known for its efficient service and comfortable seating, this airline ensures a smooth journey with minimal layovers. Whether you are flying economy or splurging on business class, British Airways provides consistent quality.</p>
                                    <p>The flight duration is approximately six and a half hours, making it one of the fastest ways to get to your destination. British Airways also offers complimentary meals and in-flight entertainment to keep you comfortable throughout the journey.</p>
                                </div>
                            </div>

                            <div class="airline-block">
                                <div class="airline-icon">🌟</div>
                                <h4><span class="airline-name">Virgin Atlantic</span> Provides Premium Comfort and Style</h4>
                                <div class="airline-description">
                                    <p>Virgin Atlantic is another excellent option for travelers seeking flights to Abuja. While it may not have direct flights, Virgin Atlantic's partnerships with other carriers make it easy to reach Abuja with just one connection. This airline is particularly known for its customer service and stylish cabin design.</p>
                                    <p>With perks such as extra legroom in premium cabins and an award-winning entertainment system, Virgin Atlantic ensures your journey feels more like a luxury experience than just a commute.</p>
                                </div>
                            </div>

                            <div class="airline-block">
                                <div class="airline-icon">🌍</div>
                                <h4><span class="airline-name">Qatar Airways</span> Offers World-Class Service with a Stopover</h4>
                                <div class="airline-description">
                                    <p>If you are open to a layover, Qatar Airways is a fantastic choice. The airline operates flights from several UK cities to Abuja via Doha. Known for its exceptional service, Qatar Airways frequently ranks as one of the best airlines globally.</p>
                                    <p>Passengers can enjoy state-of-the-art entertainment, gourmet meals, and comfortable seating. The layover in Doha also gives you a chance to stretch your legs and explore one of the world’s top airports.</p>
                                </div>
                            </div>

                            <div class="airline-block">
                                <div class="airline-icon">⚡</div>
                                <h4><span class="airline-name">Lufthansa</span> Combines Efficiency with Comfort</h4>
                                <div class="airline-description">
                                    <p>Lufthansa provides reliable flights to Abuja with a connection in Frankfurt. This German carrier is well-regarded for its punctuality and friendly service. Whether you are traveling for business or leisure, Lufthansa offers various cabin classes to suit your budget and comfort level.</p>
                                    <p>The stopover in Frankfurt is typically short, allowing you to reach Abuja with minimal delays. Lufthansa also provides generous baggage allowances, making it a great option if you have plenty of luggage.</p>
                                </div>
                            </div>

                            <div class="airline-block">
                                <div class="airline-icon">🌙</div>
                                <h4><span class="airline-name">Turkish Airlines</span> Delivers a Unique Experience</h4>
                                <div class="airline-description">
                                    <p>Turkish Airlines offers flights from the UK to Abuja with a layover in Istanbul. This airline is famous for its hospitality and exceptional in-flight meals.</p>
                                    <p>Flying with Turkish Airlines gives you a taste of Turkish culture, even before you reach your final destination. With convenient schedules and competitive pricing, it is a good choice for budget-conscious travelers who still want a premium experience.</p>
                                </div>
                            </div>

                            <div class="travel-guide">
                                <div class="guide-section choosing-airline">
                                    <div class="section-icon">🎯</div>
                                    <h4>Which Airline Should You Choose?</h4>
                                    <div class="guide-content">
                                        <p>Choosing the right airline depends on your preferences. If you value speed and convenience, British Airways is hard to beat. For those who prefer a more luxurious experience, Virgin Atlantic or Qatar Airways might be the way to go. If you are looking for cost-effective options with good service, Lufthansa or Turkish Airlines can be great alternatives.</p>
                                        <p>Whatever your choice, ensure you book early to secure the best deals and seat options for your journey to Abuja.</p>
                                    </div>
                                </div>

                                <div class="guide-section flight-duration">
                                    <div class="section-icon">⏱️</div>
                                    <h4>How Long is the Flight from the UK to Abuja?</h4>
                                    <p class="intro-note">Planning a trip to Abuja and wondering how long you’ll be in the air? Let’s break it down step by step so you can plan your journey with ease.                                    </p>
                                    
                                    <div class="info-grid">
                                        <div class="info-card">
                                            <div class="card-icon">⌛</div>
                                            <h5>Average Flight Duration</h5>
                                            <p>On average, a direct flight from the United Kingdom to Abuja, Nigeria, takes about 6 to 7 hours. The exact duration depends on factors like the airline, route, and weather conditions. Non-stop flights are your fastest option, cutting down unnecessary layovers and getting you to your destination in record time</p>
                                        </div>

                                        <div class="info-card">
                                            <div class="card-icon">🛫</div>
                                            <h5>Direct vs. Connecting Flights</h5>
                                            <p>If you choose a connecting flight, be prepared for longer travel times. Layovers at hubs like Paris, Istanbul, or Amsterdam can add anywhere from 2 to 10 hours to your journey. While connecting flights are often cheaper, they’re less convenient if you’re in a rush or dislike spending hours waiting in transit.</p>
                                        </div>

                                        <div class="info-card">
                                            <div class="card-icon">✈️</div>
                                            <h5>Major Airlines and Routes</h5>
                                            <p>Popular airlines like British Airways and Virgin Atlantic often offer direct flights from London to Abuja. If you’re flying from other cities in the UK, such as Manchester or Birmingham, you’ll likely need to connect in London or at an international hub. Keep in mind that routes and availability can vary, so it’s a good idea to check schedules in advance.                                            </p>
                                        </div>

                                        <div class="info-card">
                                            <div class="card-icon">📅</div>
                                            <h5>Best Time to Book Your Flight</h5>
                                            <p>Booking early is key to snagging the best deals. Flights to Abuja are in high demand, especially during festive seasons like Christmas or summer holidays. If flexibility isn’t an issue, consider flying midweek or during off-peak hours to save both time and money.</p>
                                        </div>
                                    </div>

                                    <div class="travel-tips">
                                        <div class="tips-icon">💡</div>
                                        <h5>Tips for a Comfortable Journey</h5>
                                        <ul class="tips-list">
                                            <li>🎒 <strong>Pack Smart:</strong> Keep essentials like snacks, a neck pillow, and entertainment handy for the flight.</li>
                                            <li>👕 <strong>Dress Comfortably:</strong> Long flights are no place for tight jeans or heels. Opt for loose, breathable clothing.</li>
                                            <li>💧 <strong>Stay Hydrated:</strong> Airplane cabins can be dry, so drink plenty of water to stay refreshed.</li>
                                            <li>🚶‍♂️ <strong>Stretch Often:</strong> Moving around periodically can help prevent stiffness and discomfort.</li>
                                        </ul>
                                    </div>

                                    <div class="cta-block">
                                        <div class="cta-icon">🎫</div>
                                        <p>Ready to book your ticket? Visit Tickets to World and find the best deals for your trip to Abuja. Whether you’re traveling for business or a getaway, we’ve got you covered!</p>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-guide">
                                <h4>When's the Best Time to Book Flights from the UK to Abuja?</h4>
                                <p class="section-intro">Timing is everything when it comes to booking flights, especially if you’re looking for the best deals. Let’s dive into how you can snag the perfect ticket without overspending.</p>
                                
                                <div class="booking-tips-grid">
                                    <div class="booking-tip-card">
                                        <div class="tip-icon">📅</div>
                                        <h5>Book Early for Peak Travel Seasons</h5>
                                        <p>If you’re planning to fly during high-demand periods, like Christmas, New Year, or summer holidays, it’s wise to book your tickets at least 3 to 6 months in advance. Flights to Abuja during these times fill up quickly, and prices tend to rise the closer you get to the travel date. The early bird gets the worm—and the best seats!                                        </p>
                                    </div>

                                    <div class="booking-tip-card">
                                        <div class="tip-icon">📊</div>
                                        <h5>Aim for Midweek Travel</h5>
                                        <p>Flying midweek, especially on Tuesdays or Wednesdays, often comes with lower ticket prices compared to weekends. Airlines know that most travelers prefer weekends, so they bump up the rates. Choosing a midweek departure can save you money and make the airport less crowded.                                        </p>
                                    </div>

                                    <div class="booking-tip-card">
                                        <div class="tip-icon">🔄</div>
                                        <h5>Be Flexible with Dates</h5>
                                        <p>If you can adjust your travel dates, you’ll have a better chance of finding cheaper flights. Use tools like fare calendars or flexible date searches to identify when tickets are the lowest. Generally, off-peak seasons, like late January or September, offer better rates compared to busy travel periods.</p>
                                    </div>

                                    <div class="booking-tip-card">
                                        <div class="tip-icon">💰</div>
                                        <h5>Monitor Flight Deals</h5>
                                        <p>Keep an eye on promotions and discounts. Airlines and booking platforms like Tickets to World often announce flash sales or limited-time deals. Signing up for fare alerts can help you catch these offers before they’re gone.</p>
                                    </div>
                                

                                <div class="booking-tip-card">
                                        <div class="tip-icon">🕒</div>
                                        <h5>Book at the Right Time</h5>
                                        <p>The “sweet spot” for booking international flights is typically 2 to 3 months before your departure date. For trips during less popular travel times, you might find deals even closer to your travel date. However, waiting too long could mean higher prices, especially if seats are selling fast.</p>
                                    </div>
                                
                                <div class="booking-tip-card">
                                        <div class="tip-icon">🌍</div>
                                        <h5>Consider the Departure City</h5>
                                        <p>Flights to Abuja from major airports like London Heathrow or Gatwick are usually more frequent and competitively priced. However, if you’re flying from smaller UK cities, connecting flights may be required, and booking early becomes even more crucial to secure the best rates.                                        </p>
                                    </div>
                                </div>

                                <div class="attractions-section">
    <h4>Top Attractions to Visit in Abuja After You Arrive</h4>
    <p class="section-intro">Abuja is more than just Nigeria's capital—it’s a city full of culture, history, and stunning landscapes. If you’re looking for things to do once you land, here’s a list of the best attractions and experiences to explore in Abuja.</p>

    <div class="attractions-grid">
        <div class="attraction-card">
            <div class="attraction-icon">🏔️</div>
            <h5>Discover Aso Rock</h5>
            <p>Standing tall at the heart of Abuja, Aso Rock is an impressive natural rock formation that defines the city’s skyline. It’s not just a sight to admire; it’s a symbol of the nation’s identity. You can enjoy a scenic view of the area and snap some incredible photos. The surrounding government buildings also give insight into Nigeria’s political importance.</p>
        </div>

        <div class="attraction-card">
            <div class="attraction-icon">🌳</div>
            <h5>Take a Stroll in Millennium Park</h5>
            <p>Millennium Park is Abuja’s largest public park and a favorite for both locals and visitors. With its lush gardens, walking paths, and fountains, it’s a great place to relax and escape the city buzz. It’s especially vibrant during weekends when families and groups come together to enjoy the outdoors.</p>
        </div>

        <div class="attraction-card">
            <div class="attraction-icon">🌊</div>
            <h5>Experience Jabi Lake</h5>
            <p>For a mix of leisure and fun, Jabi Lake is the place to be. The serene waters offer opportunities for boat rides, while Jabi Lake Mall nearby provides shopping, dining, and entertainment options. It’s a spot that blends natural beauty with modern conveniences perfectly.</p>
        </div>

        <div class="attraction-card">
            <div class="attraction-icon">🕌</div>
            <h5>Explore the Abuja National Mosque</h5>
            <p>This iconic structure, with its golden dome and towering minarets, is one of Abuja’s most recognized landmarks. While it’s primarily a place of worship, the mosque’s beauty and significance make it a must-see for visitors.</p>
        </div>

        <div class="attraction-card">
            <div class="attraction-icon">⛪</div>
            <h5>Visit the Nigerian National Christian Centre</h5>
            <p>Located not far from the National Mosque, this architectural marvel is another site worth visiting. The Christian Centre is known for its impressive design and peaceful ambiance, showcasing Abuja’s religious and cultural diversity.</p>
        </div>

        <div class="attraction-card">
            <div class="attraction-icon">🍲</div>
            <h5>Dive into Nigerian Cuisine</h5>
            <p>Food lovers will enjoy sampling Abuja’s rich culinary offerings. Visit local Bukka spots for traditional dishes like suya, jollof rice, and pepper soup. If you prefer fine dining, the city also boasts upscale restaurants offering both local and international flavors.</p>
        </div>
    </div>
</div>
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

                            <div class="faq-section">
                                <div class="section-icon">❓</div>
                                <h4>Frequently Asked Questions</h4>
                                <div class="faq-container">
                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <div class="question-icon">✈️</div>
                                            <h5>Are there direct flights from the United Kingdom to Abuja?</h5>
                                            <div class="toggle-icon">+</div>
                                        </div>
                                        <div class="faq-answer">
                                            <p>Yes, direct flights are available from London Heathrow to Abuja, typically operated by airlines like British Airways.</p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <div class="question-icon">⏱️</div>
                                            <h5>How long is the flight from the United Kingdom to Abuja?</h5>
                                            <div class="toggle-icon">+</div>
                                        </div>
                                        <div class="faq-answer">
                                            <p>A direct flight from the UK to Abuja generally takes around 6 to 7 hours, while connecting flights can take 10 to 15 hours, depending on layover times.</p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <div class="question-icon">📅</div>
                                            <h5>What is the best time to book flights from the UK to Abuja?</h5>
                                            <div class="toggle-icon">+</div>
                                        </div>
                                        <div class="faq-answer">
                                            <p>Booking your flight 3 to 4 weeks in advance is recommended to secure the best deals. Avoid peak seasons like holidays to get more affordable options.</p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <div class="question-icon">🧳</div>
                                            <h5>What are the baggage restrictions for flights from the UK to Abuja?</h5>
                                            <div class="toggle-icon">+</div>
                                        </div>
                                        <div class="faq-answer">
                                            <p>Baggage allowances vary by airline, but typically, airlines allow one carry-on and one checked bag. Be sure to check the specific airline's policy for weight limits and extra baggage fees.</p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <div class="question-icon">🛂</div>
                                            <h5>Do I need a visa to travel to Abuja from the United Kingdom?</h5>
                                            <div class="toggle-icon">+</div>
                                        </div>
                                        <div class="faq-answer">
                                            <p>Yes, UK citizens need a visa to enter Nigeria. Ensure you apply for the correct visa type before your departure.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="conclusion-wrapper">
                                    <h4>Ready for Your Journey? 🌍</h4>
                                    <div class="conclusion-block">
                                        <p>Booking a flight from the United Kingdom to Abuja is a straightforward process with multiple airline options to suit different schedules and budgets. Whether you're visiting for business, leisure, or family, Abuja offers a rich cultural experience and exciting opportunities for exploration. At Tickets to World, we strive to make your travel plans easy and affordable. By offering great flight options, helpful support, and affordable prices, we ensure your journey from the UK to Abuja is smooth and hassle-free. Start planning your trip today and let us help you with all your travel needs.</p>
                                    
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
                            </script>

                        </div>
                    </div>
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
                    padding: 2rem 1.5rem;
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
                                    line-height: 1.8;
                                    padding: 0.5rem;
                                    background: #f8f9fa;
                                    border-radius: 10px;
                                }

            }

            @media (max-width: 480px) {
                .route-info-container {
                    padding: 1.5rem 1rem;
                }

                .route-info-header h2 {
                    font-size: 1.5rem;
                }

                .airlines-section h3 {
                    font-size: 1.6rem;
                }
            }
        </style>

        <style>
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

<!-- Add this before the closing </main> tag -->
<script>
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

<!-- Replace all scattered media queries with this consolidated version -->
<style>
/* Consolidated Media Queries */
@media (max-width: 1200px) {
    .content-wrapper {
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
        padding: 2rem;
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
        padding: 2rem 1.5rem;
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
.route-info-section{
    margin-bottom: 3rem;
}
</style>

<?php require_once 'includes/footer.php'; ?>
</main>
