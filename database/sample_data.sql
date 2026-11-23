-- Tukutane sample data
-- Run AFTER schema.sql. Test logins are listed at the bottom of this file.

USE event_platform;
SET NAMES utf8mb4;

-- ---------------------------------------------------------------
-- Regions: the 47 counties of Kenya
-- ---------------------------------------------------------------
INSERT INTO regions (name) VALUES
('Mombasa'), ('Kwale'), ('Kilifi'), ('Tana River'), ('Lamu'),
('Taita-Taveta'), ('Garissa'), ('Wajir'), ('Mandera'), ('Marsabit'),
('Isiolo'), ('Meru'), ('Tharaka-Nithi'), ('Embu'), ('Kitui'),
('Machakos'), ('Makueni'), ('Nyandarua'), ('Nyeri'), ('Kirinyaga'),
('Murang''a'), ('Kiambu'), ('Turkana'), ('West Pokot'), ('Samburu'),
('Trans-Nzoia'), ('Uasin Gishu'), ('Elgeyo-Marakwet'), ('Nandi'), ('Baringo'),
('Laikipia'), ('Nakuru'), ('Narok'), ('Kajiado'), ('Kericho'),
('Bomet'), ('Kakamega'), ('Vihiga'), ('Bungoma'), ('Busia'),
('Siaya'), ('Kisumu'), ('Homa Bay'), ('Migori'), ('Kisii'),
('Nyamira'), ('Nairobi');

-- ---------------------------------------------------------------
-- Categories
-- ---------------------------------------------------------------
INSERT INTO categories (name) VALUES
('Student & youth'),
('Environment'),
('Health & wellness'),
('Technology'),
('Arts & culture'),
('Sports & recreation'),
('Education & training'),
('Community & volunteering');

-- ---------------------------------------------------------------
-- Venues (sample venues; edit names and addresses as you like)
-- ---------------------------------------------------------------
INSERT INTO venues (name, address, region_id) VALUES
('iHub', 'Senteu Plaza, Galana Road', (SELECT id FROM regions WHERE name = 'Nairobi')),
('Kenya National Theatre', 'Harry Thuku Road', (SELECT id FROM regions WHERE name = 'Nairobi')),
('Nyali Beach', 'Nyali', (SELECT id FROM regions WHERE name = 'Mombasa')),
('Jomo Kenyatta Sports Ground', 'Kisumu town', (SELECT id FROM regions WHERE name = 'Kisumu')),
('Nakuru Youth Resource Centre', 'Nakuru town', (SELECT id FROM regions WHERE name = 'Nakuru')),
('Eldoret Sports Club', 'Eldoret town', (SELECT id FROM regions WHERE name = 'Uasin Gishu')),
('Nyeri Showground', 'Nyeri town', (SELECT id FROM regions WHERE name = 'Nyeri')),
('Machakos People''s Park', 'Machakos town', (SELECT id FROM regions WHERE name = 'Machakos'));

-- ---------------------------------------------------------------
-- Users (passwords are bcrypt hashes; plain-text logins are below)
-- ---------------------------------------------------------------
INSERT INTO users (name, email, phone, password_hash, role, region_id) VALUES
('Tukutane Admin', 'admin@tukutane.test', '0700000000',
    '$2y$10$zvkgKn09mBIzFE.QZXNHiu0kGBicfhEu.yiPaKQiYRvK.alw6iNHC',
    'admin', (SELECT id FROM regions WHERE name = 'Nairobi')),
    ('Amina Wanjiru', 'amina@tukutane.test', '0700000001',
    '$2y$10$CzUI1wGX1rPQouAlkOZD2uEZhlZCDkKCOQrQBy7dFwViWPWmW3iwK',
    'attendee', (SELECT id FROM regions WHERE name = 'Nairobi')),
    ('Brian Otieno', 'brian@tukutane.test', '0700000002',
    '$2y$10$CzUI1wGX1rPQouAlkOZD2uEZhlZCDkKCOQrQBy7dFwViWPWmW3iwK',
    'attendee', (SELECT id FROM regions WHERE name = 'Kisumu')),
    ('Faith Chebet', 'faith@tukutane.test', '0700000003',
    '$2y$10$CzUI1wGX1rPQouAlkOZD2uEZhlZCDkKCOQrQBy7dFwViWPWmW3iwK',
    'attendee', (SELECT id FROM regions WHERE name = 'Uasin Gishu')),
    ('Daniel Mutiso', 'daniel@tukutane.test', '0700000004',
    '$2y$10$CzUI1wGX1rPQouAlkOZD2uEZhlZCDkKCOQrQBy7dFwViWPWmW3iwK',
    'attendee', (SELECT id FROM regions WHERE name = 'Machakos'));

    -- ---------------------------------------------------------------
    -- Events (image filenames live in public/images/events/; NULL = placeholder)
    -- ---------------------------------------------------------------
    INSERT INTO events
    (title, description, event_date, start_time, capacity, image, category_id, venue_id, created_by)
    VALUES
    ('Nairobi Youth Innovation Forum',
    'A day of talks, demos and networking for students and young innovators building solutions for their communities.',
    '2026-12-05', '09:00:00', 60, 'youth-innovation-forum.jpg',
    (SELECT id FROM categories WHERE name = 'Student & youth'),
    (SELECT id FROM venues WHERE name = 'iHub'),
    (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

    ('Mombasa Coastal Cleanup',
    'Join neighbours and volunteers to clear plastic and litter from the shoreline. Gloves and bags are provided.',
    '2026-12-12', '07:00:00', 80, 'coastal-cleanup.jpg',
    (SELECT id FROM categories WHERE name = 'Environment'),
    (SELECT id FROM venues WHERE name = 'Nyali Beach'),
    (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

    ('Kisumu Community Health Day',
    'Free health screenings, wellness talks and family activities for the whole community.',
    '2026-12-19', '08:00:00', 100, 'community-health-day.jpg',
    (SELECT id FROM categories WHERE name = 'Health & wellness'),
    (SELECT id FROM venues WHERE name = 'Jomo Kenyatta Sports Ground'),
    (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

    ('Nairobi Open Mic Night',
    'An evening of poetry, music and storytelling. Small, friendly crowd, so seats go quickly.',
    '2026-12-20', '18:00:00', 3, NULL,
    (SELECT id FROM categories WHERE name = 'Arts & culture'),
    (SELECT id FROM venues WHERE name = 'Kenya National Theatre'),
    (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

    ('Nakuru Coding Bootcamp for Beginners',
    'A hands-on introduction to HTML, CSS and JavaScript. No experience needed. Bring a laptop if you have one.',
    '2027-01-09', '09:00:00', 30, NULL,
    (SELECT id FROM categories WHERE name = 'Technology'),
    (SELECT id FROM venues WHERE name = 'Nakuru Youth Resource Centre'),
    (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

    ('Eldoret Community Football Day',
    'Friendly five-a-side matches, coaching clinics and refreshments for all ages.',
    '2027-01-16', '10:00:00', 120, NULL,
    (SELECT id FROM categories WHERE name = 'Sports & recreation'),
    (SELECT id FROM venues WHERE name = 'Eldoret Sports Club'),
    (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

    ('Nyeri Tree Planting Drive',
    'Help restore local green spaces by planting indigenous trees. Seedlings and tools are provided.',
    '2027-01-23', '08:00:00', 150, NULL,
    (SELECT id FROM categories WHERE name = 'Community & volunteering'),
    (SELECT id FROM venues WHERE name = 'Nyeri Showground'),
    (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

    -- A past event, so the "Past" tab and the "attended" status have data
    ('Machakos Digital Skills Workshop',
    'A practical workshop on using everyday digital tools for study and work.',
    '2026-10-24', '09:00:00', 40, NULL,
    (SELECT id FROM categories WHERE name = 'Education & training'),
    (SELECT id FROM venues WHERE name = 'Machakos People''s Park'),
    (SELECT id FROM users WHERE email = 'admin@tukutane.test'));

    -- ---------------------------------------------------------------
    -- Registrations (a mix of statuses)
    -- ---------------------------------------------------------------
    INSERT INTO registrations (user_id, event_id, status) VALUES
    -- Open mic night is full (capacity 3) and has one person on the waitlist
    ((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Nairobi Open Mic Night'), 'confirmed'),
    ((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Nairobi Open Mic Night'), 'confirmed'),
    ((SELECT id FROM users WHERE email = 'faith@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Nairobi Open Mic Night'), 'confirmed'),
    ((SELECT id FROM users WHERE email = 'daniel@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Nairobi Open Mic Night'), 'waitlisted'),

    ((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Nairobi Youth Innovation Forum'), 'confirmed'),
    ((SELECT id FROM users WHERE email = 'daniel@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Nairobi Youth Innovation Forum'), 'confirmed'),

    ((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Mombasa Coastal Cleanup'), 'confirmed'),
    ((SELECT id FROM users WHERE email = 'faith@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Mombasa Coastal Cleanup'), 'cancelled'),

    ((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Kisumu Community Health Day'), 'confirmed'),

    ((SELECT id FROM users WHERE email = 'faith@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Nakuru Coding Bootcamp for Beginners'), 'confirmed'),

    -- Past event
    ((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Machakos Digital Skills Workshop'), 'attended'),
    ((SELECT id FROM users WHERE email = 'daniel@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Machakos Digital Skills Workshop'), 'attended'),
    ((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
    (SELECT id FROM events WHERE title = 'Machakos Digital Skills Workshop'), 'cancelled');

-- ---------------------------------------------------------------
-- TEST LOGINS (also list these in the README)
--   Admin:     admin@tukutane.test    /  Admin@123
--   Attendees: amina@tukutane.test, brian@tukutane.test,
--              faith@tukutane.test, daniel@tukutane.test  /  Attendee@123
-- ---------------------------------------------------------------

