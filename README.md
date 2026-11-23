# event-platform

# Tukutane

Tukutane is a free event registration web app for community, student and county-level
events across Kenya. It uses plain PHP, MySQL/MariaDB through PDO, HTML, CSS and
vanilla JavaScript.

## Local setup

1. Start Apache and MySQL from the XAMPP control panel.
2. Import `database/schema.sql`, then `database/sample_data.sql` in phpMyAdmin or
   with the MySQL command line.
3. Open `http://localhost/event-platform/public/`.

## Test accounts

- Admin: `admin@tukutane.test` / `Admin@123`
- Attendee: `amina@tukutane.test` / `Attendee@123`

## Browser checks

- Home: verify the hero, search form, three upcoming event cards and three involvement steps.
- Search: choose a county or category and submit; matching upcoming events should appear.
- Registration: open Create account, submit invalid values, then create a valid account.
- Login: use an attendee or admin test account.
- Logout: confirm the header returns to Log in / Create account.

## Page routes

- Home: `?page=home&action=index`
- Explore events: `?page=events&action=index`
- Event details: `?page=events&action=show&id=EVENT_ID`
- Register: `?page=auth&action=register`
- Login: `?page=auth&action=login`
- Logout: `?page=auth&action=logout`
