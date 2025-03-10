<?php
require_once 'airline-utils.php';
?>
<div class="flight-card">
    <div class="airline-info">
        <div class="airline-logo-container">
            <?php
            $airlineCode = getAirlineCodeFromName($flight['airline_name']);
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

                <!-- Only show return info for round trips -->
                <?php if (isset($booking_details['trip_type']) && $booking_details['trip_type'] === 'round-trip'): ?>
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-plane-arrival"></i>
                        Return
                    </div>
                    <div class="info-value">
                        <?php echo htmlspecialchars($booking_details['return_date']); ?> at <?php echo htmlspecialchars($flight['arrival_time']); ?>
                    </div>
                </div>
                <?php endif; ?>

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

                <!-- Price Information -->
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-money-bill-wave"></i>
                        Full Payment
                    </div>
                    <div class="info-value price">
                        $<?php echo htmlspecialchars(number_format($flight['pay_in_one_go'], 2)); ?>
                    </div>
                </div>

                <?php if ($flight['pay_in_installments'] > 0): ?>
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-credit-card"></i>
                        Installment Price
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

<style>
/* Flight Card Styles */
.flight-card {
    display: flex;
    flex-direction: column;
    width: 100%;
    background: var(--white);
    border-radius: 15px;
    padding: 0;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.08);
    border: 1px solid rgba(105, 36, 124, 0.05);
    height: 100%; /* Ensure full height */
}

.flight-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 30px rgba(105, 36, 124, 0.12);
}

/* Ensure even spacing in grid */
.flights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 1.5rem;
    width: 100%;
}

@media (max-width: 992px) {
    .flight-card {
        max-width: 100%;
    }
    
    .flights-grid {
        grid-template-columns: 1fr;
    }
}

/* Airline Info Section */
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

.flight-details {
    flex: 1 1 auto; /* Allow this section to grow */
}

.flight-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    padding: 15px;
    flex: 0 0 auto;
    margin-top: auto; /* Push buttons to bottom */
}

.select-flight-btn {
    padding: 12px 5px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

.book-now {
    background: var(--deep-purple);
    color: var(--white);
}

.request-call {
    background: var(--pink-vivid);
    color: var(--white);
}

.book-now:hover,
.request-call:hover {
    background: var(--gradient);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.3);
}

.info-table {
    padding: 15px;
}

.info-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    padding: 8px;
    border-bottom: 1px solid rgba(105, 36, 124, 0.1);
    font-size: 0.9rem;
}

.info-value.price {
    color: var(--pink-vivid);
    font-weight: 600;
}

/* Responsive styles */
@media (max-width: 768px) {
    .flight-card {
        margin-bottom: 1rem;
    }
    
    .airline-info {
        padding: 0.8rem;
    }

    .airline-logo-container {
        width: 50px;
        height: 50px;
    }
    
    .airline-details h3 {
        font-size: 1rem;
    }

    .flight-buttons {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}

/* Responsive adjustments for very small screens */
@media (max-width: 329px) {
    .flight-card {
        font-size: 0.9rem;
    }

    .airline-info {
        padding: 0.75rem;
    }

    .info-row {
        padding: 6px;
        font-size: 0.85rem;
    }

    .flight-buttons {
        padding: 10px;
        gap: 8px;
    }

    .select-flight-btn {
        padding: 8px 4px;
        font-size: 0.8rem;
    }

    .info-label i {
        font-size: 0.9rem;
    }
}

/* Enhanced responsive styles */
@media (max-width: 480px) {
    .flights-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 0 0.25rem;
    }

    .airline-info {
        padding: 0.75rem;
        gap: 10px;
    }

    .airline-logo-container {
        width: 40px;
        height: 40px;
    }
    
    .airline-details h3 {
        font-size: 1rem;
    }

    .airline-details .route {
        font-size: 0.85rem;
    }

    .info-row {
        grid-template-columns: 1fr;
        gap: 0.25rem;
        padding: 6px;
    }
}

/* New styles for very small devices */
@media (max-width: 320px) {
    .flight-card {
        font-size: 0.85rem;
        border-radius: 10px;
    }

    .airline-info {
        padding: 0.5rem;
        gap: 6px;
    }

    .airline-icon i {
        font-size: 1rem;
    }

    .airline-details h3 {
        font-size: 0.95rem;
    }

    .info-table {
        padding: 8px;
    }

    .info-row {
        padding: 4px;
        font-size: 0.8rem;
    }

    .info-label i {
        font-size: 0.85rem;
        margin-right: 4px;
    }

    .flight-buttons {
        padding: 8px;
        gap: 6px;
    }

    .select-flight-btn {
        padding: 8px 4px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    .info-value.price {
        font-size: 0.9rem;
    }
}
</style>

<script>
// Flight action handlers
function selectFlight(flightId, airlineCode) {
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

function requestCallback(flightId, airlineCode) {
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

document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for all flight cards
    document.querySelectorAll('.flight-card').forEach(card => {
        const bookBtn = card.querySelector('.book-now');
        const callBtn = card.querySelector('.request-call');
        
        if (bookBtn) {
            bookBtn.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                this.disabled = true;
            });
        }
        
        if (callBtn) {
            callBtn.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                this.disabled = true;
            });
        }
    });
});
</script>
