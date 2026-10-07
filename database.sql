-- Multi-Specialty Hospital Management System Schema
CREATE DATABASE IF NOT EXISTS `hospital_db`;
USE `hospital_db`;

-- Departments Table
CREATE TABLE IF NOT EXISTS `departments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `dept_name` VARCHAR(100) NOT NULL,
    `block_floor` VARCHAR(50) NOT NULL,
    `hod_name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Staff Table (Doctors & Nurses)
CREATE TABLE IF NOT EXISTS `staff` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `department_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `role` ENUM('Doctor', 'Nurse') NOT NULL,
    `specialization` VARCHAR(100) NOT NULL,
    `shift` ENUM('Morning', 'Evening', 'Night') NOT NULL,
    `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE
);

-- Patients Table
CREATE TABLE IF NOT EXISTS `patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `age` INT NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
    `emergency_contact` VARCHAR(20) NOT NULL,
    `medical_history` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Wards & Beds Table
CREATE TABLE IF NOT EXISTS `wards` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ward_name` VARCHAR(100) NOT NULL,
    `category` ENUM('ICU', 'General Ward', 'Private') NOT NULL,
    `total_beds` INT NOT NULL,
    `available_beds` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admissions Table
CREATE TABLE IF NOT EXISTS `admissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `doctor_id` INT NOT NULL,
    `ward_id` INT NOT NULL,
    `bed_number` VARCHAR(20) NOT NULL,
    `admission_date` DATE NOT NULL,
    `discharge_date` DATE DEFAULT NULL,
    `status` ENUM('Admitted', 'Discharged') DEFAULT 'Admitted',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`doctor_id`) REFERENCES `staff`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ward_id`) REFERENCES `wards`(`id`) ON DELETE CASCADE
);

-- Initial Mock Data
INSERT INTO `departments` (`dept_name`, `block_floor`, `hod_name`) VALUES
('Cardiology', 'Block A - 2nd Floor', 'Dr. Rajesh Mehta'),
('Pediatrics', 'Block B - 1st Floor', 'Dr. Sunita Patel'),
('Neurology', 'Block A - 3rd Floor', 'Dr. Vikram Joshi');

INSERT INTO `staff` (`department_id`, `name`, `role`, `specialization`, `shift`, `status`) VALUES
(1, 'Dr. Ramesh Sharma', 'Doctor', 'Cardiologist', 'Morning', 'Active'),
(1, 'Sister Priya Roy', 'Nurse', 'ICU Care Specialist', 'Night', 'Active'),
(2, 'Dr. Ananya Shah', 'Doctor', 'Pediatric Specialist', 'Evening', 'Active');

INSERT INTO `wards` (`ward_name`, `category`, `total_beds`, `available_beds`) VALUES
('ICU - Unit 1', 'ICU', 10, 9),
('General Ward - Male', 'General Ward', 20, 20),
('Deluxe Private Ward', 'Private', 5, 5);

INSERT INTO `patients` (`name`, `age`, `gender`, `emergency_contact`, `medical_history`) VALUES
('Siddhi Patel', 22, 'Female', '9876543210', 'No prior major surgeries. Mild asthma.'),
('Aarav Mehta', 30, 'Male', '9876543211', 'Hypertension history.');

INSERT INTO `admissions` (`patient_id`, `doctor_id`, `ward_id`, `bed_number`, `admission_date`, `status`) VALUES
(1, 1, 1, 'ICU-B1', CURDATE(), 'Admitted');
