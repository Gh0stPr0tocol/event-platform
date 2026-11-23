-- Tukutane: extra sample venues, events and registrations
-- Run AFTER schema.sql and sample_data.sql (it depends on their data).
-- Do not run it twice.

USE event_platform;
SET NAMES utf8mb4;

-- ---------------------------------------------------------------
-- Extra venues (sample venues; edit names and addresses as you like)
-- ---------------------------------------------------------------
INSERT INTO venues (name, address, region_id) VALUES
('Kenyatta International Convention Centre', 'Harambee Avenue', (SELECT id FROM regions WHERE name = 'Nairobi')),
('Uhuru Gardens', 'Langata Road', (SELECT id FROM regions WHERE name = 'Nairobi')),
('Nairobi Central Library', 'City centre', (SELECT id FROM regions WHERE name = 'Nairobi')),
('Fort Jesus Gardens', 'Old Town', (SELECT id FROM regions WHERE name = 'Mombasa')),
('Kakamega Forest Visitor Centre', 'Isecheno', (SELECT id FROM regions WHERE name = 'Kakamega')),
('Dunga Beach', 'Lakeside', (SELECT id FROM regions WHERE name = 'Kisumu')),
('Garissa Community Hall', 'Garissa town', (SELECT id FROM regions WHERE name = 'Garissa')),
('Nakuru Public Library', 'Nakuru town', (SELECT id FROM regions WHERE name = 'Nakuru')),
('Naivasha Community Hall', 'Naivasha town', (SELECT id FROM regions WHERE name = 'Nakuru')),
('Kitui Cultural Centre', 'Kitui town', (SELECT id FROM regions WHERE name = 'Kitui')),
('Silversands Beach', 'Malindi', (SELECT id FROM regions WHERE name = 'Kilifi')),
('Meru Youth Centre', 'Meru town', (SELECT id FROM regions WHERE name = 'Meru')),
('Gusii Stadium', 'Kisii town', (SELECT id FROM regions WHERE name = 'Kisii')),
('Thika Sports Club', 'Thika town', (SELECT id FROM regions WHERE name = 'Kiambu')),
('Nyeri Public Library', 'Nyeri town', (SELECT id FROM regions WHERE name = 'Nyeri')),
('Lamu Fort Grounds', 'Lamu Old Town', (SELECT id FROM regions WHERE name = 'Lamu')),
('Narok Town Hall', 'Narok town', (SELECT id FROM regions WHERE name = 'Narok')),
('Lodwar Youth Centre', 'Lodwar town', (SELECT id FROM regions WHERE name = 'Turkana')),
('Bungoma Sports Ground', 'Bungoma town', (SELECT id FROM regions WHERE name = 'Bungoma')),
('Embu Town Hall', 'Embu town', (SELECT id FROM regions WHERE name = 'Embu')),
('Kericho Community Hall', 'Kericho town', (SELECT id FROM regions WHERE name = 'Kericho')),
('Busia Community Hall', 'Busia town', (SELECT id FROM regions WHERE name = 'Busia')),
('Nanyuki Community Hall', 'Nanyuki town', (SELECT id FROM regions WHERE name = 'Laikipia'));

-- ---------------------------------------------------------------
-- 24 more upcoming events (image is NULL, so the placeholder shows)
-- ---------------------------------------------------------------
INSERT INTO events
  (title, description, event_date, start_time, capacity, image, category_id, venue_id, created_by)
VALUES
('Nairobi Startup Pitch Night',
 'Early-stage founders pitch to a friendly audience of mentors, students and investors, followed by open networking.',
 '2026-12-08', '17:30:00', 150, NULL,
 (SELECT id FROM categories WHERE name = 'Technology'),
 (SELECT id FROM venues WHERE name = 'Kenyatta International Convention Centre'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Mombasa Heritage Walk',
 'A guided walk through Old Town covering the stories, architecture and traditions of the Swahili coast.',
 '2026-12-10', '09:00:00', 40, NULL,
 (SELECT id FROM categories WHERE name = 'Arts & culture'),
 (SELECT id FROM venues WHERE name = 'Fort Jesus Gardens'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kakamega Forest Tree Care Day',
 'Volunteers help maintain seedlings and clear invasive plants around the forest edge. Tools provided.',
 '2026-12-13', '08:00:00', 70, NULL,
 (SELECT id FROM categories WHERE name = 'Environment'),
 (SELECT id FROM venues WHERE name = 'Kakamega Forest Visitor Centre'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kisumu Lakeside Clean-up',
 'Help clear litter from the lakeshore and learn how local groups protect the lake.',
 '2026-12-14', '07:30:00', 90, NULL,
 (SELECT id FROM categories WHERE name = 'Environment'),
 (SELECT id FROM venues WHERE name = 'Dunga Beach'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Garissa Youth Skills Fair',
 'Stalls, demos and short sessions on practical skills, from tailoring to basic coding.',
 '2026-12-15', '09:00:00', 120, NULL,
 (SELECT id FROM categories WHERE name = 'Student & youth'),
 (SELECT id FROM venues WHERE name = 'Garissa Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Nakuru Women in Tech Meetup',
 'A relaxed meetup for women building careers in technology. Lightning talks and a mentoring circle.',
 '2026-12-17', '14:00:00', 50, NULL,
 (SELECT id FROM categories WHERE name = 'Technology'),
 (SELECT id FROM venues WHERE name = 'Nakuru Public Library'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Nairobi Mental Wellness Walk',
 'A community walk to start conversations about mental wellbeing, with short talks and refreshments.',
 '2026-12-21', '08:00:00', 200, NULL,
 (SELECT id FROM categories WHERE name = 'Health & wellness'),
 (SELECT id FROM venues WHERE name = 'Uhuru Gardens'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Eldoret Running Clinic',
 'Coaches share training, pacing and recovery tips for beginner and intermediate runners.',
 '2026-12-22', '07:00:00', 60, NULL,
 (SELECT id FROM categories WHERE name = 'Sports & recreation'),
 (SELECT id FROM venues WHERE name = 'Eldoret Sports Club'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kitui Farmers Training Day',
 'Practical sessions on water-saving farming, crop planning and selling produce.',
 '2026-12-28', '09:00:00', 80, NULL,
 (SELECT id FROM categories WHERE name = 'Education & training'),
 (SELECT id FROM venues WHERE name = 'Kitui Cultural Centre'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Malindi Beach Volleyball Tournament',
 'Friendly mixed teams, a relaxed atmosphere and prizes for the most spirited players.',
 '2027-01-02', '09:00:00', 64, NULL,
 (SELECT id FROM categories WHERE name = 'Sports & recreation'),
 (SELECT id FROM venues WHERE name = 'Silversands Beach'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Meru Youth Career Talk',
 'Professionals from different fields share how they started and answer questions from students.',
 '2027-01-06', '10:00:00', 100, NULL,
 (SELECT id FROM categories WHERE name = 'Student & youth'),
 (SELECT id FROM venues WHERE name = 'Meru Youth Centre'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Nairobi Digital Literacy for Seniors',
 'Patient, hands-on help with smartphones, messaging, video calls and staying safe online.',
 '2027-01-12', '10:00:00', 35, NULL,
 (SELECT id FROM categories WHERE name = 'Education & training'),
 (SELECT id FROM venues WHERE name = 'Nairobi Central Library'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kisii Cultural Festival',
 'Music, dance, food and crafts celebrating local culture, with performances all day.',
 '2027-01-14', '11:00:00', 300, NULL,
 (SELECT id FROM categories WHERE name = 'Arts & culture'),
 (SELECT id FROM venues WHERE name = 'Gusii Stadium'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Thika Blood Donation Drive',
 'Donate blood and help local hospitals. Health staff on site. Please eat beforehand and bring ID.',
 '2027-01-19', '08:00:00', 120, NULL,
 (SELECT id FROM categories WHERE name = 'Health & wellness'),
 (SELECT id FROM venues WHERE name = 'Thika Sports Club'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Naivasha Wildlife Conservation Talk',
 'Rangers and researchers explain how communities and wildlife can share the landscape.',
 '2027-01-21', '15:00:00', 60, NULL,
 (SELECT id FROM categories WHERE name = 'Environment'),
 (SELECT id FROM venues WHERE name = 'Naivasha Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Nyeri Student Hackathon',
 'A one-day team challenge to build small tools that solve local problems. Mentors on hand.',
 '2027-01-30', '09:00:00', 80, NULL,
 (SELECT id FROM categories WHERE name = 'Technology'),
 (SELECT id FROM venues WHERE name = 'Nyeri Public Library'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Lamu Cultural Heritage Evening',
 'An evening of storytelling, taarab music and local food in the grounds of the old fort.',
 '2027-02-04', '17:00:00', 100, NULL,
 (SELECT id FROM categories WHERE name = 'Arts & culture'),
 (SELECT id FROM venues WHERE name = 'Lamu Fort Grounds'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Narok Community Peace Forum',
 'Elders, youth and local leaders talk about working together and resolving disputes peacefully.',
 '2027-02-09', '10:00:00', 150, NULL,
 (SELECT id FROM categories WHERE name = 'Community & volunteering'),
 (SELECT id FROM venues WHERE name = 'Narok Town Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Lodwar Water and Sanitation Awareness Day',
 'Demonstrations and talks on safe water storage, handwashing and keeping communities healthy.',
 '2027-02-13', '09:00:00', 90, NULL,
 (SELECT id FROM categories WHERE name = 'Health & wellness'),
 (SELECT id FROM venues WHERE name = 'Lodwar Youth Centre'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Bungoma Football for Peace Cup',
 'A friendly community tournament for local teams, with a family-friendly atmosphere.',
 '2027-02-20', '10:00:00', 200, NULL,
 (SELECT id FROM categories WHERE name = 'Sports & recreation'),
 (SELECT id FROM venues WHERE name = 'Bungoma Sports Ground'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Embu Youth Entrepreneurship Workshop',
 'Learn how to test a business idea, keep simple records and find your first customers.',
 '2027-02-25', '09:00:00', 70, NULL,
 (SELECT id FROM categories WHERE name = 'Student & youth'),
 (SELECT id FROM venues WHERE name = 'Embu Town Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Kericho Tea Valley Community Walk',
 'A scenic group walk to raise funds for local school supplies, followed by tea and snacks.',
 '2027-03-06', '08:00:00', 110, NULL,
 (SELECT id FROM categories WHERE name = 'Community & volunteering'),
 (SELECT id FROM venues WHERE name = 'Kericho Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Busia Border Town Skills Expo',
 'Short training sessions and exhibitions on trades, small business and digital skills.',
 '2027-03-13', '09:00:00', 130, NULL,
 (SELECT id FROM categories WHERE name = 'Education & training'),
 (SELECT id FROM venues WHERE name = 'Busia Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test')),

('Nanyuki Wildlife Photography Workshop',
 'Learn composition and light with a working photographer, then practise on a guided outing.',
 '2027-03-20', '09:00:00', 25, NULL,
 (SELECT id FROM categories WHERE name = 'Arts & culture'),
 (SELECT id FROM venues WHERE name = 'Nanyuki Community Hall'),
 (SELECT id FROM users WHERE email = 'admin@tukutane.test'));

-- ---------------------------------------------------------------
-- A few more registrations so seat counts look realistic
-- ---------------------------------------------------------------
INSERT INTO registrations (user_id, event_id, status) VALUES
((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nairobi Startup Pitch Night'), 'confirmed'),
((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nairobi Startup Pitch Night'), 'confirmed'),
((SELECT id FROM users WHERE email = 'daniel@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nairobi Startup Pitch Night'), 'confirmed'),
((SELECT id FROM users WHERE email = 'faith@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nairobi Mental Wellness Walk'), 'confirmed'),
((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nairobi Mental Wellness Walk'), 'confirmed'),
((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Kisumu Lakeside Clean-up'), 'confirmed'),
((SELECT id FROM users WHERE email = 'faith@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Eldoret Running Clinic'), 'confirmed'),
((SELECT id FROM users WHERE email = 'daniel@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nyeri Student Hackathon'), 'confirmed'),
((SELECT id FROM users WHERE email = 'amina@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Nakuru Women in Tech Meetup'), 'cancelled'),
((SELECT id FROM users WHERE email = 'brian@tukutane.test'),
 (SELECT id FROM events WHERE title = 'Kisii Cultural Festival'), 'confirmed');
