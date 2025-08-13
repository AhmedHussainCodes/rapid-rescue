-- Rapid Rescue eAmbulance Database Structure
-- Created for the eAmbulance project

CREATE DATABASE IF NOT EXISTS finalbook1;
USE finalbook1;

-- Users table for both regular users and admins
CREATE TABLE users (
    userid INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    address TEXT NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Emergency requests table
CREATE TABLE requests (
    requestid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT NOT NULL,
    hospital_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(15) NOT NULL,
    pickup_address TEXT NOT NULL,
    type ENUM('Emergency', 'Non-Emergency') NOT NULL,
    request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'En route', 'Completed') DEFAULT 'Pending',
    FOREIGN KEY (userid) REFERENCES users(userid) ON DELETE CASCADE
);

-- Ambulances table
CREATE TABLE ambulances (
    ambulanceid INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(20) UNIQUE NOT NULL,
    equipment_level ENUM('Basic', 'Advanced') NOT NULL,
    status ENUM('Available', 'On call', 'Maintenance') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Drivers table
CREATE TABLE drivers (
    driverid INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medical profiles table (optional)
CREATE TABLE medical_profiles (
    profileid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT NOT NULL,
    allergies TEXT,
    medical_history TEXT,
    emergency_contact VARCHAR(100),
    emergency_contact_phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES users(userid) ON DELETE CASCADE
);

-- Contact queries table
CREATE TABLE contact_queries (
    queryid INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data for testing

-- Sample admin user (password: admin123)
INSERT INTO users (firstname, lastname, email, phone, password, dob, address, role) VALUES
('Admin', 'User', 'admin@rapidrescue.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1990-01-01', '123 Admin Street, City', 'admin');

-- Sample regular users (password: user123)
INSERT INTO users (firstname, lastname, email, phone, password, dob, address, role) VALUES
('John', 'Doe', 'john@example.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1985-05-15', '456 Main Street, City', 'user'),
('Jane', 'Smith', 'jane@example.com', '5555555555', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1992-08-20', '789 Oak Avenue, City', 'user'),
('Mike', 'Johnson', 'mike@example.com', '1111111111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1988-12-10', '321 Pine Road, City', 'user');

-- Sample ambulances
INSERT INTO ambulances (vehicle_number, equipment_level, status) VALUES
('AMB-001', 'Advanced', 'Available'),
('AMB-002', 'Basic', 'Available'),
('AMB-003', 'Advanced', 'On call'),
('AMB-004', 'Basic', 'Maintenance'),
('AMB-005', 'Advanced', 'Available');

-- Sample drivers
INSERT INTO drivers (firstname, lastname, phone) VALUES
('Robert', 'Wilson', '2222222222'),
('Sarah', 'Davis', '3333333333'),
('David', 'Brown', '4444444444'),
('Lisa', 'Miller', '5555555556'),
('Tom', 'Anderson', '6666666666');

-- Sample emergency requests
INSERT INTO requests (userid, hospital_name, address, phone, pickup_address, type, status) VALUES
(2, 'City General Hospital', '100 Hospital Drive, City', '1234567891', '456 Main Street, City', 'Emergency', 'Pending'),
(3, 'Metro Medical Center', '200 Medical Plaza, City', '1234567892', '789 Oak Avenue, City', 'Non-Emergency', 'En route'),
(4, 'Regional Health Center', '300 Health Street, City', '1234567893', '321 Pine Road, City', 'Emergency', 'Completed');

-- Sample medical profiles
INSERT INTO medical_profiles (userid, allergies, medical_history, emergency_contact, emergency_contact_phone) VALUES
(2, 'Penicillin, Peanuts', 'Diabetes Type 2, Hypertension', 'Mary Doe', '9876543211'),
(3, 'None known', 'Asthma', 'Bob Smith', '5555555556'),
(4, 'Shellfish', 'Previous heart surgery in 2020', 'Susan Johnson', '1111111112');

-- Sample contact queries
INSERT INTO contact_queries (name, email, subject, message) VALUES
('Alice Cooper', 'alice@example.com', 'Service Inquiry', 'I would like to know more about your emergency services.'),
('Bob Taylor', 'bob@example.com', 'Feedback', 'Great service! Very professional and quick response time.');
