<?php
session_start();
require_once '../private/config.php';
// SEO meta tags
$pageTitle = "Affordable Adventure Tickets to Top Destinations Worldwide";
$metaDescription = "Explore affordable adventure tickets to top destinations around the world. Book your adventure now and discover exciting travel opportunities!";
$canonicalURL = "http://theticketstoworld.co.uk/";
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
    <meta name="keywords" content="affordable adventure tickets, top travel destinations, adventure travel, cheap world tours, adventure booking">

<?php require_once './includes/header.php';
// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_location = $_POST['FDestFrom'] ?? '';
    $to_location = $_POST['FDestTo'] ?? '';
    $departure_date = $_POST['departure-date'] ?? '';
    $trip_type = $_POST['trip-type'] ?? 'round-trip';
    $return_date = $trip_type === 'one-way' ? NULL : ($_POST['arrival-date'] ?? '');
    $FAdult = $_POST['FAdult'] ?? 0;
    $FChild = $_POST['FChild'] ?? 0;
    $FInfant = $_POST['FInfant'] ?? 0;
    $FClsType = strtoupper(trim($_POST['FClsType']));
    
    // Validate class type
    if (!in_array($FClsType, ['ECONOMY', 'PREMIUM', 'BUSINESS'])) {
        $FClsType = 'ECONOMY'; // Default fallback
    }
    
    $FAirLine = $_POST['FAirLine'] ?? 'ALL';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Validate class type before insertion
    $valid_class_types = ['ECONOMY', 'PREMIUM', 'BUSINESS'];
    $FClsType = strtoupper(trim($_POST['FClsType'] ?? ''));
    if (!in_array($FClsType, $valid_class_types)) {
        $FClsType = 'ECONOMY';
    }

    error_log("Submitting class type: " . $FClsType);

    // Update the INSERT query to include FAirLine
    $stmt = $conn->prepare("INSERT INTO bookings (name, email, phone, from_location, to_location, 
        departure_date, return_date, FAdult, FChild, FInfant, FClsType, passengers, trip_type, FAirLine) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $passengers = $FAdult + $FChild + $FInfant;
    $stmt->bind_param("ssssssssiiisss", 
        $name, $email, $phone, $from_location, $to_location, $departure_date, $return_date, 
        $FAdult, $FChild, $FInfant, $FClsType, $passengers, $trip_type, $FAirLine);

    // Debug log
    error_log("Inserting class type: " . $FClsType);

    if ($stmt->execute()) {
        // Update session data with consistent keys
        $_SESSION['search_data'] = [
            'from_location' => $_POST['FDestFrom'],
            'to_location' => $_POST['FDestTo'],
            'departure_date' => $_POST['departure-date'],
            'return_date' => $_POST['arrival-date'],
            'FAdult' => $_POST['FAdult'],
            'FChild' => $_POST['FChild'],
            'FInfant' => $_POST['FInfant'],
            'FClsType' => $_POST['FClsType'],
            'FAirLine' => $_POST['FAirLine'],
            'trip_type' => $_POST['trip-type']
        ];
        
        header("Location: search.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<main>

<!-- Hero Section with Booking Form -->
<section id="Book-With-Us" class="hero">
    <div class="hero1">
        <div class="hero-content">
            <h1>Welcome to Tickets to World</h1>
            <p>Unbeatable Travel Deals for Every Adventure, Wherever Life Takes You</p>
            <form class="booking-form" method="post" action="index.php">
                <div class="form-row">
                    <div class="form-group">
                        <div class="srch-tab-line">
                            <label class="return">
                                <input type="radio" class="ticket_type" name="trip-type" onclick="toggleReturnDate()" value="round-trip" checked> Round Trip
                            </label>
                            <label class="oneway">
                                <input type="radio" class="ticket_type" name="trip-type" onclick="toggleReturnDate()" value="one-way"> One Way
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="flight-from">Fly from:</label>
                        <input type="text" 
                               id="flight-from" 
                               name="FDestFrom" 
                               placeholder="Type to search locations" 
                               autocomplete="off"
                               required>
                        <div id="fromLocationList" class="location-dropdown"></div>
                    </div>
                    <div class="form-group">
                        <label for="flight-to">Fly to:</label>
                        <input type="text" 
                               id="flight-to" 
                               name="FDestTo" 
                               placeholder="Type to search locations" 
                               autocomplete="off"
                               required>
                        <div id="toLocationList" class="location-dropdown"></div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="departure-date">Departure Date:</label>
                        <input type="date" id="departure-date" name="departure-date" required>
                    </div>
                    <div class="form-group" id="return-date-group">
                        <label for="arrival-date">Return Date:</label>
                        <input type="date" id="arrival-date" name="arrival-date" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="adults">Adults:</label>
                        <select id="FAdult" name="FAdult">
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="children">Children (2-12):</label>
                        <select id="FChild" name="FChild">
                            <option value="0" selected>0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="infants">Infant (&lt; 2):</label>
                        <select id="FInfant" name="FInfant">
                            <option value="0" selected>0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="class">Class:</label>
                        <select id="FClsType" name="FClsType">
                            <option value="ECONOMY">Economy Class</option>
                            <option value="PREMIUM">Premium Economy</option>
                            <option value="BUSINESS">Business Class</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="airlines">Airlines:</label>
                        <select id="FAirLine" name="FAirLine">
                            <option value="ALL">Any Airline</option>
                            <option value="BA">British Airways</option>
                            <option value="KA">Kenya Airways</option>
                            <option value="RAM">Royal Air Maroc</option>
                            <option value="KLM">KLM</option>
                            <option value="AF">Air France</option>
                            <option value="TK">Turkish Airlines</option>
                            <option value="EK">Emirates</option>
                            <option value="QR">Qatar Airways</option>
                            <option value="LH">Lufthansa</option>
                            <option value="VS">Virgin Atlantic</option>
                            <option value="WB">Rwandair Express</option>
                            <option value="ET">Ethiopian Air Lines</option>
                            <option value="LX">Swiss</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                </div>
                <button type="submit" class="cta-button">Search Flights</button>
            </form>
        </div>
    </div>
</section>

<!-- Intro Section -->
<section class="intro">
    <div class="container">
        <h2>Are you dreaming of your next adventure?</h2>
        <p>At Tickets to World, we believe that travel should be convenient, affordable, and tailored to your needs. Whether you’re embarking on a business trip, a family getaway, or a solo journey, our platform connects you to the world’s best travel deals on flights, hotels, and vacation packages.</p>
        <p>With a commitment to simplifying your travel planning, Tickets to World offers an intuitive booking experience backed by expert support. Let us take the stress out of travel so you can focus on making memories that last a lifetime.</p>
        <h2>Why Tickets to World Stands Out as Your Perfect Travel Partner?</h3>
        <p>Planning a trip can be as exciting as it is overwhelming. That’s where Tickets to World comes in—a reliable partner to help you turn your travel dreams into a hassle-free reality. From affordable flight bookings to personalized travel support, we make sure every step of your journey is smooth and enjoyable. Let’s break down why Tickets to World is your go-to choice.</p>
        <h3 style="color: #ff6f61;">Affordable Travel Solutions for Every Budget</h3>
        <p>Traveling shouldn’t cost a fortune, and we believe in offering options that suit everyone. Whether you’re booking a quick domestic flight or planning a long-haul adventure, we make it a point to find the best deals available. Our competitive pricing and regularly updated promotions mean you’ll never have to break the bank to explore the world.</p>
        <h3 style="color: #ff6f61;">Personalized Service You Can Count On</h3>
        <p>No one likes feeling like just another number in a queue. At Tickets to World, we treat your travel plans like our own. Our dedicated team listens to your needs and preferences, helping you customize every detail of your trip. Stuck choosing the best airline? Need advice on connecting flights? We’re always just a call or click away to offer tailored guidance.</p>
        <h3 style="color: #ff6f61;">A Convenient Booking Experience</h3>
        <p>Booking travel can often feel like navigating a maze. But with our user-friendly platform, finding flights, accommodations, or package deals is as simple as a few clicks. Plus, if tech isn’t your thing, our customer support team is always ready to assist with bookings over the phone or via email.</p>
        <h3 style="color: #ff6f61;">Access to Exclusive Deals and Packages</h3>
        <p>Why settle for ordinary when you can get more? We have partnerships with leading airlines and travel providers, giving you access to special deals you won’t find elsewhere. From discounted’t your thing, our customer support team is always ready to assist with bookings over the phone or via email.</p>
        <h3 style="color: #ff6f61;">Access to Exclusive Deals and Packages</h3>
        <p>Why settle for ordinary when you can get more? We have partnerships with leading airlines and travel providers, giving you access to special deals you won’t find elsewhere. From discounted fares to complete travel packages, we provide options that make your trip both memorable and cost-effective.</p>
        <h3 style="color: #ff6f61;">Support That’s There When You Need It</h3>
        <p>Unexpected changes happen, and that’s why we offer round-the-clock support. Whether it’s a flight reschedule or a last-minute travel query, our team is ready to assist. You can rely on us to help you handle any bumps in the road, so you can focus on what matters—enjoying your journey.</p>
        <p>Traveling doesn’t have to be stressful when you’ve got the right partner by your side. Tickets to World combines affordability, personalized service, and a commitment to making your trips worry-free. Why settle for anything less when planning your next adventure? With us, you’re not just booking a trip—you’re setting the stage for memories that last a lifetime.</p>
    </div>
</section>

<!-- Affordable Adventures Section -->
<section class="affordable-adventures">
    <div class="custom-container, container">
        
        <h2>Affordable Adventures with Top Destinations at Unbeatable Prices</h2> 
        <p>Looking to make the most out of your travel plans without breaking the bank? At Tickets to World, we offer incredible prices on some of the world’s most popular destinations. Whether you're dreaming of sunny beaches, cultural hubs, or scenic wonders, we've got a deal that’s just right for you. Let’s dive into how you can travel to top destinations at a fraction of the usual cost.</p>
        <h3>Budget-Friendly Beach Getaways</h3>
        <p>Who doesn’t love a beach vacation? Imagine lounging on golden sands, the waves crashing in the background, and the sun kissing your skin—all without spending a fortune. From tropical paradises in Southeast Asia to exotic coasts in the Caribbean, our affordable beach vacation packages bring the best destinations closer to you.</p>    
        <h3>City Breaks for Culture, Shopping, and Food</h3>
        <p>If city life is more your style, why not visit one of the world's most exciting cities without the hefty price tag? We offer budget-friendly options for vibrant destinations like New York, Paris, or Tokyo. Take in the art, enjoy delicious street food, and shop till you drop—all while saving money.</p>
        
        <h3>Nature Escapes with Stunning Landscapes</h3>
        <p>If you find peace in nature, why not take a trip to some of the world’s most beautiful natural spots? From the snow-capped mountains of Switzerland to the lush rainforests of Costa Rica, you don’t have to spend a fortune to enjoy these breathtaking views. Affordable packages let you explore pristine environments without draining your wallet.</p>
        
        <h3>Authentic Cultural Experiences Without the High Cost</h3>
        <p>Want to experience the local culture of a new country? You don’t have to spend a fortune to embrace a city’s authentic vibe. Whether it’s exploring the ancient ruins of Rome or diving into the bustling markets of Marrakech, you can have a genuine experience without paying premium prices. We make these adventures accessible, ensuring you get the most out of every moment.</p>
        
        <h3>Last-Minute Deals to Plan Your Trip Fast</h3>
        
        <p>Did you just get a sudden craving for travel? With our last-minute deals, you can pack your bags and head off on a dream vacation faster than you think—without the hefty price tag! Sometimes, the best trips are the ones you plan on a whim, and our deals make it easier to go.</p>
       
    </div>
</section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
        <div class="custom-container, container">
            <h2>Why Choose Us?</h2>
            <div class="features">
                <article class="feature">
                    <i class="fas fa-dollar-sign"></i>
                    <h3>Competitive Prices</h3>
                    <p>We offer less prices that help you go further for less.</p>
                </article>
                <article class="feature">
                    <i class="fas fa-map-marked-alt"></i>
                    <h3>Wide Range of Destinations</h3>
                    <p>We Offer Wide Range of Destinations to choose from for any type of vacation.</p>
                </article>
                <article class="feature">
                    <i class="fas fa-check-circle"></i>
                    <h3>Simple Process</h3>
                    <p>The Process is to simple with no hidden fees, just easy booking.</p>
                </article>

                
            </div>
            <p id="custom-p">Whether you’re looking for a quick getaway or a long-term adventure, Tickets to World ensures you get the most value out of your trip. So, what are you waiting for? Let’s make those dream destinations happen, without stressing your budget! </p>
        </div>
    </section>

<!-- How Tickets to World Simplifies Travel Section -->
<section class="simplifies-travel">
    <div class="custom-container, container">
        <h2>How Tickets to World Simplifies Travel and Makes Your Journey Simple?</h2>
        <div class="travel-simplified">
            <div class="travel-step">
                <i class="fas fa-globe"></i>
                <h3>Easy Access to Destinations Worldwide</h3>
                <p>Whether you’re dreaming of a relaxing beach holiday or an exciting city adventure, Tickets to World provides an easy way to find flights to destinations all over the globe. Instead of hopping between different sites, you can compare options and choose the best prices with just a few clicks. This saves you time and effort, allowing you to plan your trip quickly and efficiently.</p>
            </div>
            <div class="travel-step">
                <i class="fas fa-dollar-sign"></i>
                <h3>Affordable Flights Designed to Suit Your Budget</h3>
                <p>Finding affordable flights can be one of the biggest challenges of travel, but Tickets to World makes it easy. With a wide variety of flight options at different price points, you can always find a ticket that works for your budget. The platform is designed to show you all your choices in one place, so you won’t have to worry about hidden fees or unexpected costs.</p>
            </div>
            <div class="travel-step">
                <i class="fas fa-bed"></i>
                <h3>Hassle-Free Hotel and Accommodation Bookings</h3>
                <p>Once you’ve secured your flights, the next step is finding somewhere to stay. Tickets to World simplifies this by offering a wide range of accommodations to suit every traveler. Whether you’re looking for a luxurious hotel or a budget-friendly guesthouse, you can filter your search by price, location, or amenities to find the perfect place for your stay.</p>
            </div>
            <div class="travel-step">
                <i class="fas fa-suitcase-rolling"></i>
                <h3>All-Inclusive Vacation Packages for Convenience</h3>
                <p>With Tickets to World, you don’t have to book flights and hotels separately. The platform offers all-inclusive vacation packages that bundle everything together. You can book your flights, accommodation, and even activities in one easy package, saving you both time and money. Whether you’re planning a relaxing getaway or an action-packed adventure, these packages make it easier to organize your trip.</p>
            </div>
            <div class="travel-step">
                <i class="fas fa-shuttle-van"></i>
                <h3>Smooth Transfers and Local Transportation Options</h3>
                <p>Arriving at your destination is just the start of your journey. Getting from the airport to your hotel or exploring the local area can often be tricky. Tickets to World simplifies this by offering a range of transfer options, from airport pickups to local transportation. This means you can move around your destination with ease, making the most of your time there.</p>
            </div>
        </div>
    </div>
</section>

<!-- Travel Tips Section -->
<section class="travel-tips">
    <div class="custom-container, container">
        <h2>Travel Tips and Insights for a Smooth Journey with Tickets to World</h2>
        <div class="tips-list">
            <article class="tip">
                <div class="tip-content">
                    <h3>Book Tickets Early for the Best Deals</h3>
                    <p>If you're looking to save some cash, booking your tickets ahead of time is a great place to start. It gives you the chance to find better deals and ensure you get a seat on the flights that suit you best. While it’s tempting to wait for last-minute offers, planning in advance can often work out cheaper, especially when it comes to peak travel seasons.</p>
                    <p>To get the best deals, keep an eye out for flash sales or promotions, and consider using price comparison tools or setting up alerts to track fluctuations in flight prices.</p>
                </div>
            </article>
            <article class="tip">
                <div class="tip-content">
                    <h3>Pack Light, Pack Smart</h3>
                    <p>Overpacking is an easy trap to fall into, especially when you're not sure exactly what you'll need. A good rule to follow is to focus on the essentials and avoid packing items you might never use. Check the weather at your destination, think about the activities you have planned, and pack accordingly. Keep your luggage manageable by rolling clothes instead of folding them – it saves space and reduces wrinkles.</p>
                    <p>Invest in packing cubes to keep your clothes organized and make your suitcase feel less chaotic. Remember, you can always pick up anything you forgot once you arrive.</p>
                </div>
            </article>
            <article class="tip">
                <div class="tip-content">
                    <h3>Arrive Early at the Airport</h3>
                    <p>There’s nothing more stressful than rushing to catch a flight. Avoid that last-minute panic by arriving early. This gives you plenty of time to check in, go through security, and grab a bite before you board. It’s especially important if you have a connecting flight or need to navigate through large, busy airports. Aim to arrive 2-3 hours before your flight to avoid unnecessary stress.</p>
                </div>
            </article>
            <article class="tip">
                <div class="tip-content">
                    <h3>Stay Organized with Travel Documents</h3>
                    <p>Before you head to the airport, make sure you’ve got all your important travel documents in one easy-to-access spot. This includes your passport, flight tickets, accommodation information, and any necessary visas. Keeping everything in a travel wallet or a specific pouch ensures you won’t be scrambling around at the last minute. It also helps you avoid losing anything important, so you can focus on enjoying your trip instead.</p>
                </div>
            </article>
            <article class="tip">
                <div class="tip-content">
                    <h3>Make Use of Technology</h3>
                    <p>These days, apps can be a traveler's best friend. From flight tracking to hotel bookings, there’s an app for nearly everything. Download the ones you’ll use most during your trip to save time and hassle. For example, apps like Google Maps can help you get around, while flight-tracking apps give you live updates if there are any delays or cancellations.</p>
                </div>
            </article>
            <article class="tip">
                <div class="tip-content">
                    <h3>Plan for Currency and Payment Options</h3>
                    <p>When you’re traveling to a new country, it's a good idea to have some local currency on hand, but also make sure you have a backup payment option, like a travel card or credit card that works internationally. Some places may not accept cards or could charge extra fees, so having a mix of payment options helps ensure you're not caught off guard.</p>
                </div>
            </article>
            <article class="tip">
                <div class="tip-content">
                    <h3>Keep Your Health in Check While Traveling</h3>
                    <p>Traveling, especially to new countries, can take a toll on your body. Make sure to stay hydrated and take breaks when needed. If you're flying long-haul, try to walk around the cabin or stretch to keep your circulation moving. Don’t forget to pack any medications or travel essentials you might need, and check with your doctor if you need any vaccinations before heading abroad.</p>
                </div>
            </article>
            <article class="tip">
                <div class="tip-content">
                    <h3>Always Have a Backup Plan</h3>
                    <p>Things don’t always go as planned when traveling. Whether it’s a missed flight or unexpected weather, it’s essential to stay flexible. Always have a backup plan in place and allow extra time for things like delays. This way, you won’t get stuck or stressed if things don’t go according to your itinerary.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Why Travelers Trust Tickets to World Section -->
<section class="travelers-trust">
    <div class="custom-container, container">
        <h2>Why Travelers Trust Tickets to World?</h2>
        <div class="trust-list">
            <article class="trust-item">
                <img src="./assets/images/wide-range-of-travel-options.webp" alt="Wide Range of Travel Options">
                <div class="trust-content">
                    <h3>A Wide Range of Travel Options</h3>
                    <p>Whether you’re booking flights, accommodations, or excursions, Tickets to World offers a broad selection of travel options. We understand that every traveler is different, so we provide a variety of choices that cater to various preferences and budgets. From affordable budget travel to luxury experiences, we’ve got something for everyone.</p>
                </div>
            </article>
            <article class="trust-item">
                <img src="./assets/images/hassle-free-booking.webp" alt="Hassle-Free Booking Process">
                <div class="trust-content">
                    <h3>Hassle-Free Booking Process</h3>
                    <p>One of the reasons travelers choose us is our easy-to-use booking system. We’ve streamlined the process, so it’s simple and efficient to find the right tickets for your trip. No more complicated steps or hidden fees—just a straightforward approach to getting your bookings sorted with minimal effort. Plus, our customer support team is always available to help if you need assistance with your bookings.</p>
                </div>
            </article>
            <article class="trust-item">
                <img src="./assets/images/competitive-prices.webp" alt="Competitive Prices and Exclusive Deals">
                <div class="trust-content">
                    <h3>Competitive Prices and Exclusive Deals</h3>
                    <p>We know how important getting good value is when booking travel, and Tickets to World works hard to provide competitive prices that won’t break the bank. With exclusive deals and frequent discounts, travelers can save on flights, hotels, and more. By booking with us, you’re not only securing a great deal, but also getting access to discounts you won’t find elsewhere.</p>
                </div>
            </article>
            <article class="trust-item">
                <img src="./assets/images/customer-service.webp" alt="Trusted Customer Service">
                <div class="trust-content">
                    <h3>Trusted Customer Service</h3>
                    <p>Our customer service team is dedicated to providing top-notch assistance to make your experience as smooth as possible. From pre-booking queries to after-booking support, we’re here to answer any questions and resolve any concerns quickly. Travelers appreciate knowing they can rely on us for support at every stage of their journey.</p>
                </div>
            </article>
            <article class="trust-item">
                <img src="./assets/images/expert-travel-advice.webp" alt="Expert Travel Advice and Insights">
                <div class="trust-content">
                    <h3>Expert Travel Advice and Insights</h3>
                    <p>At Tickets to World, we don’t just sell tickets—we offer valuable insights and expert travel advice. Whether you’re a first-time traveler or a seasoned globetrotter, we provide tips and recommendations that help make your trip enjoyable and stress-free. From booking the best hotels to planning exciting activities, our team is here to ensure you get the most out of your travels.</p>
                </div>
            </article>
            <article class="trust-item">
                <img src="./assets/images/secure-transactions.webp" alt="Secure and Reliable Transactions">
                <div class="trust-content">
                    <h3>Secure and Reliable Transactions</h3>
                    <p>Travelers trust us because we prioritize their safety and privacy. All transactions made on our website are secure, ensuring your payment information is kept safe. Our transparent booking process means you won’t be hit with surprise charges, giving you peace of mind when booking your next trip.</p>
                </div>
            </article>
            
</div>
<div class="trust-custom-item">
            <i class="fas fa-users"></i>
                <div class="trust-content">
                    <h3>Positive Reviews and Testimonials</h3>
                    <p>Don’t just take our word for it—our satisfied customers say it all. Many travelers leave positive reviews praising our reliable service, great deals, and hassle-free booking experience. These testimonials from real customers further prove that Tickets to World is a trusted and reliable travel service provider.</p>
                </div>
        </div>
        
    </div>
</section>

<!-- Call to Action Section -->
<section class="call-to-action">
    <div class="container">
        <h2>Let’s Get You There</h2>
        <p>Your next adventure is just a click away. Whether you’re traveling for work or leisure, Tickets to World is your trusted partner for all things travel. From booking your flights to planning every detail of your trip, we’re here to make your journey smooth and memorable.</p>
        <p>Start exploring our exclusive deals today and see why Tickets to World is the preferred choice for travelers around the globe.</p>
        <a href="#Book-With-Us" class="cta-button">Book now and let us take you places!</a>
    </div>
</section>

<!-- FAQ Section -->
<div class="faqs-container">
        <h2>FAQs</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <span>How can I book my flight with Tickets to World?</span>
                    <div class="arrow"></div>
                </div>
                <div class="faq-answer">
                    <p>Booking your flight is simple! Just visit our website, enter your destination, travel dates, and preferences, and our platform will show you a range of flight options. You can easily filter the results to find the best deal, and then proceed to checkout to confirm your booking.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Do you offer travel insurance?</span>
                    <div class="arrow"></div>
                </div>
                <div class="faq-answer">
                    <p>Yes, we offer comprehensive travel insurance to protect your trip. You can add it to your booking during the checkout process. Our travel insurance covers a variety of situations, including flight delays, cancellations, medical emergencies, and lost luggage.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Can I modify or cancel my booking?</span>
                    <div class="arrow"></div>
                </div>
                <div class="faq-answer">
                    <p>Yes, we understand that travel plans can change. You can modify or cancel your booking, depending on the airline and accommodation provider's policies. Please contact our support team for assistance with your changes.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Are the flight prices on Tickets to World guaranteed?</span>
                    <div class="arrow"></div>
                </div>
                <div class="faq-answer">
                    <p>We strive to offer the best prices available, but please note that prices may fluctuate depending on availability and demand. Once your booking is confirmed, the price is locked in, and no further charges will apply.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Can I book multi-destination flights with Tickets to World?</span>
                    <div class="arrow"></div>
                </div>
                <div class="faq-answer">
                    <p>Absolutely! We offer multi-destination itineraries so you can plan your entire trip, including flights to several cities or countries. You can easily build a custom itinerary using our platform, saving both time and money.</p>
                </div>
            </div>

            <div>
                    <h2>Conclusion</h2>
                    <p class="conclusion-content">At Tickets to World, we’re more than just a travel booking platform—we’re your trusted partner for all things travel. From affordable flights to personalized service, we’re here to make your journey smooth, enjoyable, and unforgettable. With a commitment to competitive pricing, expert support, and a wide range of travel options, we ensure that every trip you take is a memorable experience.</p>
                </div>
        </div>
    </div>

</main>

<!-- Add this script before closing main -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get date input elements
    const departureInput = document.getElementById('departure-date');
    const returnInput = document.getElementById('arrival-date');
    
    // Format date to YYYY-MM-DD
    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    // Set minimum date for departure (today)
    const today = new Date();
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

    // Original toggleReturnDate function (keep existing functionality)
    toggleReturnDate();
});
</script>

<?php
require_once 'includes/footer.php';
?>

<script>
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

    // Keep existing date handling code
    const today = new Date();
    const departureInput = document.getElementById('departure-date');
    const returnInput = document.getElementById('arrival-date');
    
    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    departureInput.min = formatDate(today);

    departureInput.addEventListener('change', function() {
        const selectedDeparture = new Date(this.value);
        const minReturn = new Date(selectedDeparture);
        minReturn.setDate(minReturn.getDate() + 1);
        returnInput.min = formatDate(minReturn);

        if (returnInput.value && new Date(returnInput.value) < minReturn) {
            returnInput.value = formatDate(minReturn);
        }
    });

    // Keep existing toggleReturnDate function
    toggleReturnDate();
});

// Keep existing toggleReturnDate function
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
</script>

