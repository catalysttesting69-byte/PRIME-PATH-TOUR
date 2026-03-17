-- =============================================================
--  PrimePath Tours & Safaris — Database Schema
--  Run this entire file in phpMyAdmin's SQL tab.
--  Create your database first (e.g. "primepath_db") then run.
-- =============================================================

-- -------------------------------------------------------
-- 1. BOOKINGS
--    Stores every tour enquiry / booking form submission.
--    Visitors submit anonymously — no account required.
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100) NOT NULL,
  email        VARCHAR(100) NOT NULL,
  phone        VARCHAR(20),
  tour_name    VARCHAR(150),
  travel_date  DATE,
  num_people   INT,
  message      TEXT,
  status       ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- 2. SUBSCRIBERS
--    Stores newsletter subscriber emails (anonymous).
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS subscribers (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(100) UNIQUE NOT NULL,
  subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- 3. USERS (Admin only)
--    Only the business owner logs in here.
--    Passwords are stored as bcrypt hashes — NEVER plain text.
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin') DEFAULT 'admin',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- 4. CLIENTS
--    Auto-built from booking submissions.
--    Not system users — they never log in.
--    total_bookings is incremented each time the same
--    email submits a new booking form.
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS clients (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(100) NOT NULL,
  email          VARCHAR(100) UNIQUE NOT NULL,
  phone          VARCHAR(20),
  total_bookings INT DEFAULT 0,
  first_seen     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_booking   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- 5. TOURS
--    Managed by the admin through the dashboard.
--    active = 0 hides the tour without deleting it.
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS tours (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(150) NOT NULL,
  description   TEXT,
  price         DECIMAL(10,2),
  duration_days INT,
  image_url     VARCHAR(255),
  active        BOOLEAN DEFAULT TRUE,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- 6. DEFAULT ADMIN ACCOUNT
--    Password is: Admin@PrimePath2026
--    Change this IMMEDIATELY after first login via
--    the admin dashboard password-change feature,
--    or update the hash below with your own.
--
--    To generate a new hash in PHP run:
--      echo password_hash('YourPassword', PASSWORD_DEFAULT);
-- -------------------------------------------------------
INSERT IGNORE INTO users (name, email, password_hash, role)
VALUES (
  'Admin',
  'admin@primepath.com',
  '$2y$12$kIhVn9QaP.eXj3zQpM6UJekNP5VfDoJmU3F7e3HFtSrGHe3MvMj6q',
  'admin'
);
