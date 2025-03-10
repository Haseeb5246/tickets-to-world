<?php
/**
 * Sanitize string input
 * @param string $input
 * @return string
 */
function sanitizeString($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 * @param string $phone
 * @return bool
 */
function isValidPhone($phone) {
    return preg_match('/^[0-9+\-\s()]{8,20}$/', $phone);
}

/**
 * Validate date format
 * @param string $date
 * @return bool
 */
function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
