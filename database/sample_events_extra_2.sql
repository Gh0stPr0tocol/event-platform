-- Tukutane: second batch of extra sample venues, events and registrations
-- Run AFTER schema.sql, sample_data.sql and sample_events_extra.sql.
-- Do not run it twice.

USE event_platform;
SET NAMES utf8mb4;

-- ---------------------------------------------------------------
-- 16 more venues in counties not yet covered (sample names)
-- ---------------------------------------------------------------
INSERT INTO venues (name, address, region_id) VALUES
('Diani Beach', 'Diani', (SELECT id FROM regions WHERE name = 'Kwale')),
('Hola Community Hall', 'Hola town', (SELECT id FROM regions WHERE name = 'Tana River')),
('Voi Community Ground', 'Voi town', (SELECT id FROM regions WHERE name = 'Taita-Taveta')),
('Kitale Museum', 'Kitale town', (SELECT id FROM regions WHERE name = 'Trans-Nzoia')),
('Homa Bay Town Hall', 'Homa Bay town', (SELECT id FROM regions WHERE name = 'Homa Bay')),
('Migori Community Hall', 'Migori town', (SELECT id FROM regions WHERE name = 'Migori')),
('Kerugoya Community Hall', 'Kerugoya town', (SELECT id FROM regions WHERE name = 'Kirinyaga')),
('Murang''a Town Hall', 'Murang''a town', (SELECT id FROM regions WHERE name = 'Murang''a')),
('Wote Community Hall', 'Wote town', (SELECT id FROM regions WHERE name = 'Makueni')),
('Isiolo Community Grounds', 'Isiolo town', (SELECT id FROM regions WHERE name = 'Isiolo')),
('Marsabit Town Hall', 'Marsabit town', (SELECT id FROM regions WHERE name = 'Marsabit')),
('Siaya Community Hall', 'Siaya town', (SELECT id FROM regions WHERE name = 'Siaya')),
('Kapsabet Community Ground', 'Kapsabet town', (SELECT id FROM regions WHERE name = 'Nandi')),
('Kajiado Town Hall', 'Kajiado town', (SELECT id FROM regions WHERE name = 'Kajiado')),
('Kabarnet Town Hall', 'Kabarnet town', (SELECT id FROM regions WHERE name = 'Baringo')),
('Mombasa Community Learning Centre', 'Mombasa island', (SELECT id FROM regions WHERE name = 'Mombasa'));

-- ---------------------------------------------------------------
-- 20 more upcoming events (image is NULL, so the placeholder shows)
-- ---------------------------------------------------------------
INSERT INTO events
  (title, description, event_date, start_time, capacity, image, category_id, venue_id, created_by)
VALUES
('Diani Beach Clean-up and Eco Talk',
 'Clear the shoreline with local volunteers, then hear how hotels and residents protect the reef.',
 '2026-12-09', '07:30:00', 100, NULL,
 (SELECT id FROM categories WHERE name = 'Environment'),
 (SELECT id FROM venues WHERE name = 'Diani Beach'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Hola Irrigation Farmers Forum',
 'Farmers and extension officers share practical ideas on irrigation, water use and crop choice.',
 '2026-12-11', '09:00:00', 80, NULL,
 (SELECT id FROM categories WHERE name = 'Education & training'),
 (SELECT id FROM venues WHERE name = 'Hola Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Voi Youth Football Trials',
 'Open trials for young players, with coaches giving feedback and training tips.',
 '2026-12-16', '10:00:00', 150, NULL,
 (SELECT id FROM categories WHERE name = 'Sports & recreation'),
 (SELECT id FROM venues WHERE name = 'Voi Community Ground'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kitale Museum Heritage Day',
 'Exhibits, storytelling and family activities celebrating the history and cultures of the region.',
 '2026-12-18', '10:00:00', 90, NULL,
 (SELECT id FROM categories WHERE name = 'Arts & culture'),
 (SELECT id FROM venues WHERE name = 'Kitale Museum'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Homa Bay Lakeside Community Health Camp',
 'Free check-ups, health talks and advice for families living near the lake.',
 '2026-12-23', '08:00:00', 120, NULL,
 (SELECT id FROM categories WHERE name = 'Health & wellness'),
 (SELECT id FROM venues WHERE name = 'Homa Bay Town Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Migori Girls Mentorship Day',
 'Mentors from different careers meet students for small-group talks, advice and encouragement.',
 '2026-12-30', '09:00:00', 100, NULL,
 (SELECT id FROM categories WHERE name = 'Student & youth'),
 (SELECT id FROM venues WHERE name = 'Migori Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kerugoya Coffee Farmers Workshop',
 'Practical sessions on picking, processing and selling coffee for better returns.',
 '2027-01-05', '09:00:00', 70, NULL,
 (SELECT id FROM categories WHERE name = 'Education & training'),
 (SELECT id FROM venues WHERE name = 'Kerugoya Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Murang''a Storytelling and Reading Day',
 'Local writers and elders share stories, with reading corners and activities for children.',
 '2027-01-08', '10:00:00', 60, NULL,
 (SELECT id FROM categories WHERE name = 'Arts & culture'),
 (SELECT id FROM venues WHERE name = 'Murang''a Town Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Wote Tree Nursery Volunteer Day',
 'Help prepare and pot seedlings that will be planted around schools and water points.',
 '2027-01-15', '08:00:00', 80, NULL,
 (SELECT id FROM categories WHERE name = 'Community & volunteering'),
 (SELECT id FROM venues WHERE name = 'Wote Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Isiolo Community Sports Day',
 'Races, team games and cultural performances for all ages, with refreshments on the grounds.',
 '2027-01-22', '09:00:00', 250, NULL,
 (SELECT id FROM categories WHERE name = 'Sports & recreation'),
 (SELECT id FROM venues WHERE name = 'Isiolo Community Grounds'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Marsabit Cultural Day',
 'A day of music, dance, food and crafts celebrating the communities of Marsabit.',
 '2027-01-27', '10:00:00', 150, NULL,
 (SELECT id FROM categories WHERE name = 'Arts & culture'),
 (SELECT id FROM venues WHERE name = 'Marsabit Town Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Siaya Youth Tech Bootcamp',
 'A beginner-friendly introduction to web basics and digital skills, with plenty of hands-on practice.',
 '2027-02-02', '09:00:00', 40, NULL,
 (SELECT id FROM categories WHERE name = 'Technology'),
 (SELECT id FROM venues WHERE name = 'Siaya Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kapsabet Athletics Open Day',
 'Try track and field events with local coaches, and meet athletes from the region.',
 '2027-02-06', '08:00:00', 180, NULL,
 (SELECT id FROM categories WHERE name = 'Sports & recreation'),
 (SELECT id FROM venues WHERE name = 'Kapsabet Community Ground'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kajiado Maasai Craft Fair',
 'Browse and learn about traditional beadwork and crafts, with demonstrations and live music.',
 '2027-02-11', '10:00:00', 200, NULL,
 (SELECT id FROM categories WHERE name = 'Arts & culture'),
 (SELECT id FROM venues WHERE name = 'Kajiado Town Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kabarnet Environmental Awareness Walk',
 'A community walk with short talks on protecting forests, water sources and wildlife.',
 '2027-02-18', '08:00:00', 100, NULL,
 (SELECT id FROM categories WHERE name = 'Environment'),
 (SELECT id FROM venues WHERE name = 'Kabarnet Town Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Nairobi Community Garden Day',
 'Plant, weed and learn simple food-growing tips in a shared community garden.',
 '2027-02-27', '09:00:00', 60, NULL,
 (SELECT id FROM categories WHERE name = 'Community & volunteering'),
 (SELECT id FROM venues WHERE name = 'Uhuru Gardens'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Mombasa Coding for Kids Saturday',
 'Fun, beginner-friendly coding games and activities for children, with parents welcome to join in.',
 '2027-03-04', '09:00:00', 30, NULL,
 (SELECT id FROM categories WHERE name = 'Technology'),
 (SELECT id FROM venues WHERE name = 'Mombasa Community Learning Centre'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kisumu Women Entrepreneurs Expo',
 'Women-led businesses showcase their work, with short talks on starting and growing a business.',
 '2027-03-27', '09:00:00', 160, NULL,
 (SELECT id FROM categories WHERE name = 'Education & training'),
 (SELECT id FROM venues WHERE name = 'Jomo Kenyatta Sports Ground'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Eldoret First Aid Training',
 'Learn practical first aid skills, from treating cuts and burns to what to do in an emergency.',
 '2027-04-10', '09:00:00', 40, NULL,
 (SELECT id FROM categories WHERE name = 'Health & wellness'),
 (SELECT id FROM venues WHERE name = 'Eldoret Sports Club'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Nyeri Mount Kenya Charity Hike',
 'A guided group hike to raise funds for local schools. Suitable for beginners with moderate fitness.',
 '2027-04-24', '06:30:00', 120, NULL,
 (SELECT id FROM categories WHERE name = 'Sports & recreation'),
 (SELECT id FROM venues WHERE name = 'Nyeri Showground'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test'));

-- ---------------------------------------------------------------
-- A few more registrations so seat counts look realistic
-- ---------------------------------------------------------------
INSERT INTO registrations (user_id, event_id, status) VALUES
((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nairobi Community Garden Day'), 'confirmed'),
((SELECT id FROM users WHERE email = 'daniel@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nairobi Community Garden Day'), 'confirmed'),
((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Kisumu Women Entrepreneurs Expo'), 'confirmed'),
((SELECT id FROM users WHERE email = 'faith@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Eldoret First Aid Training'), 'confirmed'),
((SELECT id FROM users WHERE email = 'faith@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Kapsabet Athletics Open Day'), 'confirmed'),
((SELECT id FROM users WHERE email = 'daniel@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Siaya Youth Tech Bootcamp'), 'confirmed'),
((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Mombasa Coding for Kids Saturday'), 'confirmed'),
((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Diani Beach Clean-up and Eco Talk'), 'cancelled'),
((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nyeri Mount Kenya Charity Hike'), 'confirmed');
