CREATE DATABASE IF NOT EXISTS event_platform
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE event_platform;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS saved_events;
DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS venues;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS regions;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE regions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('attendee', 'admin') NOT NULL DEFAULT 'attendee',
    region_id INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_region FOREIGN KEY (region_id)
        REFERENCES regions(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE venues (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address VARCHAR(255) NOT NULL,
    region_id INT UNSIGNED NOT NULL,
    CONSTRAINT fk_venues_region FOREIGN KEY (region_id)
        REFERENCES regions(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    capacity INT UNSIGNED NOT NULL,
    image VARCHAR(255) NULL,
    category_id INT UNSIGNED NOT NULL,
    venue_id INT UNSIGNED NOT NULL,
    created_by INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_category FOREIGN KEY (category_id)
        REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_events_venue FOREIGN KEY (venue_id)
        REFERENCES venues(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_events_creator FOREIGN KEY (created_by)
        REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_event_date (event_date),
    INDEX idx_event_category (category_id)
) ENGINE=InnoDB;

CREATE TABLE registrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    event_id INT UNSIGNED NOT NULL,
    status ENUM('confirmed', 'waitlisted', 'cancelled', 'attended') NOT NULL DEFAULT 'confirmed',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_event (user_id, event_id),
    CONSTRAINT fk_registrations_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_registrations_event FOREIGN KEY (event_id)
        REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
