-- Create and use database
CREATE DATABASE IF NOT EXISTS tickets_to_world;
USE tickets_to_world;

-- Drop tables in reverse order of dependencies
DROP TABLE IF EXISTS trash_items;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS flight_price_history;
DROP TABLE IF EXISTS flight_schedules;
DROP TABLE IF EXISTS flights;
DROP TABLE IF EXISTS airline_flights;
DROP TABLE IF EXISTS available_flights;
DROP TABLE IF EXISTS admin_users;

-- Create available_flights table
CREATE TABLE available_flights (
    flight_id INT AUTO_INCREMENT PRIMARY KEY,
    from_location VARCHAR(100) NOT NULL,
    to_location VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create airline_flights table
CREATE TABLE airline_flights (
    airline_flight_id INT AUTO_INCREMENT PRIMARY KEY,
    flight_id INT NOT NULL,
    airline_name VARCHAR(100) NOT NULL,
    total_journey_time VARCHAR(50) NOT NULL,
    time_departure VARCHAR(50) NOT NULL,
    arrival_time VARCHAR(50) NOT NULL,
    stops VARCHAR(50) NOT NULL,
    available_seats INT NOT NULL,
    pay_in_one_go DECIMAL(10, 2) NOT NULL,
    pay_in_installments DECIMAL(10, 2) NULL,
    FOREIGN KEY (flight_id) REFERENCES available_flights(flight_id) ON DELETE CASCADE
);

-- Create flights table with comprehensive schema
CREATE TABLE flights (
    flight_id INT AUTO_INCREMENT PRIMARY KEY,
    airline_code VARCHAR(10) NOT NULL,
    airline_name VARCHAR(100) NOT NULL,
    from_location VARCHAR(100) NOT NULL,
    to_location VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    arrival_date DATE NOT NULL,
    arrival_time TIME NOT NULL,
    duration VARCHAR(50) NOT NULL,
    stops INT DEFAULT 0,
    total_seats INT NOT NULL,
    available_seats INT NOT NULL,
    economy_price DECIMAL(10,2) NOT NULL,
    business_price DECIMAL(10,2) NOT NULL,
    premium_price DECIMAL(10,2) NOT NULL,
    allow_installments BOOLEAN DEFAULT FALSE,
    installment_price DECIMAL(10,2),
    status ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create index for flight searches
CREATE INDEX idx_flight_search 
ON flights(from_location, to_location, departure_date, status);

-- Create flight_schedules table
CREATE TABLE flight_schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    flight_id INT NOT NULL,
    day_of_week TINYINT NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    FOREIGN KEY (flight_id) REFERENCES flights(flight_id) ON DELETE CASCADE
);

-- Create flight_price_history table
CREATE TABLE flight_price_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_id INT NOT NULL,
    economy_price DECIMAL(10,2) NOT NULL,
    business_price DECIMAL(10,2) NOT NULL,
    premium_price DECIMAL(10,2) NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flight_id) REFERENCES flights(flight_id) ON DELETE CASCADE
);

-- Create bookings table with full schema
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    from_location VARCHAR(100) NOT NULL,
    to_location VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE,
    FAdult INT NOT NULL,
    FChild INT NOT NULL,
    FInfant INT NOT NULL,
    FClsType ENUM('ECONOMY', 'PREMIUM', 'BUSINESS') NOT NULL DEFAULT 'ECONOMY',
    passengers INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    trip_type ENUM('one-way', 'round-trip') DEFAULT 'round-trip',
    FAirLine VARCHAR(10) NOT NULL DEFAULT 'ALL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_class_type (FClsType)
);

-- Create admin_users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create trash_items table
CREATE TABLE trash_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_name VARCHAR(255) NOT NULL,
    original_path VARCHAR(255) NOT NULL,
    trash_name VARCHAR(255) NOT NULL,
    deleted_at DATETIME NOT NULL,
    is_dir TINYINT(1) NOT NULL DEFAULT 0
);

-- Insert initial flight data
INSERT INTO flights (
    airline_code, 
    airline_name, 
    from_location, 
    to_location, 
    departure_date,
    departure_time,
    arrival_date,
    arrival_time,
    duration,
    stops,
    total_seats,
    available_seats,
    economy_price,
    business_price,
    premium_price,
    allow_installments,
    installment_price
) VALUES 
('EK', 'Emirates', 'London', 'Dubai', '2024-01-15', '10:30:00', '2024-01-15', '20:30:00', '10h 00m', 0, 300, 150, 799.99, 2499.99, 1499.99, true, 299.99),
('BA', 'British Airways', 'London', 'New York', '2024-01-15', '11:00:00', '2024-01-15', '14:00:00', '8h 00m', 0, 250, 100, 599.99, 1999.99, 1299.99, true, 249.99),
('QR', 'Qatar Airways', 'Dubai', 'London', '2024-01-15', '09:15:00', '2024-01-15', '14:45:00', '7h 30m', 1, 280, 120, 849.99, 2599.99, 1599.99, true, 319.99);

-- Insert initial booking data
INSERT INTO bookings (
    name, email, phone, from_location, to_location, 
    departure_date, return_date, FAdult, FChild, FInfant, 
    FClsType, passengers, status, trip_type
) VALUES 
('Haseeb', 'carparkingsapp@gmail.com', '03497503218', 'karachi', 'hyderbad', 
 '2024-12-26', '2024-12-30', 2, 2, 1, 'ECONOMY', 5, 'pending', 'round-trip'),
('Haseeb', 'haseeb4143017@gmail.com', '03497503218', 'delhi', 'karachi', 
 '2024-12-21', '2024-12-03', 1, 1, 0, 'BUSINESS', 2, 'confirmed', 'round-trip'),
('Test User', 'test@example.com', '1234567890', 'London', 'Paris', 
 '2024-01-01', '2024-01-07', 2, 0, 0, 'PREMIUM', 2, 'pending', 'round-trip');

-- Insert initial admin user
INSERT INTO admin_users (username, password) 
VALUES ('haseeb', '$2y$10$3VoqYAGDqJEG9eEQjilSf.BPgx5YFAr1AGfIyqQGu1p8hvXL3mR2.');