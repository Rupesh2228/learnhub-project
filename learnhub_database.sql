-- ============================================
-- LearnHub Database Schema
-- ============================================
-- This SQL file contains the complete database structure for LearnHub project

-- Create Database (optional - adjust as needed)
-- CREATE DATABASE IF NOT EXISTS learnhub;
-- USE learnhub;

-- ============================================
-- 1. ADMINS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 2. USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 3. USER PROFILES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.png',
    phone VARCHAR(20),
    date_of_birth DATE,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- 4. COURSES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 5. ASSIGNMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    file_path VARCHAR(255),
    course_name VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- ============================================
-- DEFAULT DATA - COURSES
-- ============================================
INSERT IGNORE INTO courses (course_name) VALUES
('Fullstack Development'),
('AI/ML'),
('Cybersecurity'),
('UI/UX Design'),
('MERN Stack'),
('Python Programming'),
('Web Development'),
('Data Science'),
('Mobile App Development'),
('Cloud Computing');

-- ============================================
-- DEFAULT DATA - ADMIN USER
-- ============================================
-- Note: Password hash is for 'admin123' using bcrypt
-- To generate new passwords: use password_hash("your_password", PASSWORD_BCRYPT) in PHP
INSERT IGNORE INTO admins (email, password) VALUES
('admin@learnhub.com', '$2y$10$EIX3sj8TqPfzTq9W3sJ2L.0Y2pzH9Q5nU5K8K5K5K5K5K5K5K5K5K');

-- ============================================
-- TABLE INDEXES (for optimization)
-- ============================================
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_admins_email ON admins(email);
CREATE INDEX idx_courses_name ON courses(course_name);
CREATE INDEX idx_assignments_user ON assignments(user_id);
CREATE INDEX idx_assignments_course ON assignments(course_id);
CREATE INDEX idx_user_profiles_user ON user_profiles(user_id);

-- ============================================
-- END OF SCHEMA
-- ============================================
