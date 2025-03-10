<form class="search-booking-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="searchForm">
    <h2>Find Your Perfect Flight</h2>
    
    <!-- Trip Type Selection -->
    <div class="form-row">
        <div class="form-group full-width">
            <div class="search-page-radio-group">
                <label class="search-page-radio-label">
                    <input type="radio" 
                           class="search-page-radio" 
                           name="trip-type" 
                           onclick="toggleReturnDate()" 
                           value="round-trip" 
                           <?php echo (!isset($booking_details['trip_type']) || $booking_details['trip_type'] === 'round-trip') ? 'checked' : ''; ?>>
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
                           <?php echo (isset($booking_details['trip_type']) && $booking_details['trip_type'] === 'one-way') ? 'checked' : ''; ?>>
                    <span class="search-page-radio-text">
                        <i class="fas fa-plane"></i> One Way
                    </span>
                </label>
            </div>
        </div>
    </div>

    <!-- Location Inputs -->
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
        </div>
    </div>

    <!-- Date Selection -->
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
        <div class="form-group" id="return-date-group">
            <label for="arrival-date">Return Date:</label>
            <input type="date" 
                   id="arrival-date" 
                   name="arrival-date" 
                   min="" 
                   value="<?php echo htmlspecialchars($booking_details['return_date'] ?? ''); ?>" 
                   <?php echo (!isset($booking_details['trip_type']) || $booking_details['trip_type'] === 'round-trip') ? 'required' : ''; ?>>
        </div>
    </div>

    <!-- Passenger Selection -->
    <div class="form-row passengers-row">
        <div class="form-group">
            <label for="FAdult">Adults:</label>
            <select id="FAdult" name="FAdult">
                <?php for($i = 1; $i <= 9; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo (isset($booking_details['FAdult']) && $booking_details['FAdult'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="FChild">Children (2-12):</label>
            <select id="FChild" name="FChild">
                <?php for($i = 0; $i <= 8; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo (isset($booking_details['FChild']) && $booking_details['FChild'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="FInfant">Infant (<2):</label>
            <select id="FInfant" name="FInfant">
                <?php for($i = 0; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo (isset($booking_details['FInfant']) && $booking_details['FInfant'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <!-- Class and Airline Selection -->
    <div class="form-row">
        <div class="form-group">
            <label for="FClsType">Class:</label>
            <select id="FClsType" name="FClsType">
                <option value="ECONOMY" <?php echo (isset($booking_details['FClsType']) && $booking_details['FClsType'] == 'ECONOMY') ? 'selected' : ''; ?>>Economy</option>
                <option value="PREMIUM" <?php echo (isset($booking_details['FClsType']) && $booking_details['FClsType'] == 'PREMIUM') ? 'selected' : ''; ?>>Premium</option>
                <option value="BUSINESS" <?php echo (isset($booking_details['FClsType']) && $booking_details['FClsType'] == 'BUSINESS') ? 'selected' : ''; ?>>Business</option>
            </select>
        </div>
        <div class="form-group">
            <label for="FAirLine">Airlines:</label>
            <select id="FAirLine" name="FAirLine">
                <option value="ALL" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'ALL' ? 'selected' : ''; ?>>Any Airline</option>
                <option value="BA" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'BA' ? 'selected' : ''; ?>>British Airways</option>
                <option value="KA" <?php echo isset($booking_details['FAirLine']) && $booking_details['FAirLine'] == 'KA' ? 'selected' : ''; ?>>Kenya Airways</option>
                <!-- ... Add other airline options ... -->
            </select>
        </div>
    </div>

    <button type="submit" class="cta-button">
        <i class="fas fa-search"></i> Search Flights
    </button>
</form>

<style>
/* Search Form Styles */
.search-form-wrapper {
    position: relative;
    z-index: 100;
}

.search-booking-form {
    background: var(--white);
    padding: 1.2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.08);
    transition: transform 0.3s ease;
    margin-bottom: 1rem;
}

.search-booking-form:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(105, 36, 124, 0.15);
}

/* Form Layout */
.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

/* Form Elements */
.form-group {
    position: relative;
}

.form-group label {
    display: block;
    color: var(--deep-purple);
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 0.4rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.6rem;
    background: rgba(249, 230, 207, 0.3);
    border: 1px solid rgba(105, 36, 124, 0.1);
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

/* Updated Radio Button Styles */
.search-page-radio-group {
    display: flex;
    width: 100%;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    padding: 4px;
    gap: 4px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(105, 36, 124, 0.08);
}

.search-page-radio-label {
    flex: 1;
    position: relative;
    margin: 0;
    padding: 0;
}

.search-page-radio {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.search-page-radio-text {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 16px;
    color: var(--deep-purple);
    font-weight: 500;
    font-size: 0.95rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: transparent;
    height: 100%;
    white-space: nowrap;
}

.search-page-radio:checked + .search-page-radio-text {
    background: var(--gradient);
    color: white;
    box-shadow: 0 2px 8px rgba(105, 36, 124, 0.2);
}

.search-page-radio-label:hover .search-page-radio-text:not(:checked + *) {
    background: rgba(105, 36, 124, 0.05);
}

/* Remove any conflicting styles */
.search-page-radio-label:has(.search-page-radio:checked) {
    background: none;
    box-shadow: none;
}

@media (max-width: 480px) {
    .search-page-radio-text {
        padding: 10px;
        font-size: 0.85rem;
    }
}

/* Mobile Responsiveness */
@media (max-width: 480px) {
    .search-page-radio-group {
        padding: 3px;
    }

    .search-page-radio-text {
        padding: 6px 12px;
        font-size: 0.9rem;
    }

    .search-page-radio-text i {
        font-size: 1rem;
    }
}

/* ... rest of the existing styles ... */

/* Responsive Styles */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .search-booking-form {
        padding: 1rem;
    }
}

/* Add existing search form styles plus these additional styles */
.search-booking-form {
    position: sticky;
    top: 0px;
    height: max-content;
    background: var(--white);
    padding: 1.2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(105, 36, 124, 0.08);
    z-index: 10;
    align-self: start;
}

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

.search-page-radio:checked + .search-page-radio-text {
    color: #fff;
}

.search-page-radio-label:has(.search-page-radio:checked) {
    background: var(--gradient);
    box-shadow: 0 4px 12px rgba(30, 136, 229, 0.2);
}

.cta-button {
    width: 100%;
    padding: 0.75rem;
    background: var(--gradient);
    color: var(--white);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(105, 36, 124, 0.2);
    transition: all 0.3s ease;
}

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 36, 124, 0.3);
}

/* Location dropdown styles */
.location-dropdown {
    display: none;
    position: absolute;
    z-index: 101;
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

.location-item:hover, 
.location-item.highlighted {
    background-color: #f0f0f0;
}

.location-item .match {
    font-weight: bold;
    color: var(--deep-purple);
}

/* Add responsive styles from search.php */
@media (max-width: 768px) {
    .search-booking-form {
        padding: 1rem;
        margin-top: 0 !important;
    }
    
    .search-page-radio-group {
        padding: 4px;
    }
}

/* Remove error message styles */
.error-message {
    display: none !important;
}

/* Update input styling */
.form-group input {
    margin-bottom: 0;
}

.location-dropdown {
    margin-top: 2px;
}

/* Enhanced mobile responsiveness */
@media (max-width: 480px) {
    .search-booking-form {
        padding: 0.75rem;
    }

    .form-row {
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
}

@media (max-width: 320px) {
    .search-booking-form {
        padding: 0.5rem;
    }

    .search-booking-form h2 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .form-row {
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .form-group label {
        font-size: 0.8rem;
        margin-bottom: 0.2rem;
    }

    .form-group input,
    .form-group select {
        padding: 0.4rem;
        font-size: 0.85rem;
        border-radius: 8px;
    }

    .search-page-radio-text {
        padding: 6px 8px;
        font-size: 0.8rem;
        gap: 4px;
    }

    .search-page-radio-text i {
        font-size: 0.9rem;
    }

    .cta-button {
        padding: 0.6rem;
        font-size: 0.85rem;
    }
}
</style>

<script>
// Date handling
document.addEventListener('DOMContentLoaded', function() {
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

    toggleReturnDate();
});

// Toggle return date field
function toggleReturnDate() {
    const tripType = document.querySelector('input[name="trip-type"]:checked').value;
    const returnDateGroup = document.getElementById('return-date-group');
    const returnDateInput = document.getElementById('arrival-date');
    
    if (tripType === 'one-way') {
        returnDateGroup.style.display = 'none';
        returnDateInput.removeAttribute('required');
    } else {
        returnDateGroup.style.display = 'block';
        returnDateInput.setAttribute('required', 'required');
    }
}
</script>
