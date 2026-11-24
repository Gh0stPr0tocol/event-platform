# event-platform

# Tukutane

Tukutane is a free event registration web app for community, student and county-level
events across Kenya. It uses plain PHP, MySQL/MariaDB through PDO, HTML, CSS and
vanilla JavaScript.

## Local setup

1. Start Apache and MySQL from the XAMPP control panel.
2. Import `database/schema.sql`, then `database/sample_data.sql` in phpMyAdmin or
   with the MySQL command line.
3. Run `database/add_saved_events_and_indexes.sql` once against `event_platform`.
   It is additive; do not rerun `database/schema.sql` to apply this update.
4. Open `http://localhost/event-platform/public/`.

## Test accounts

- Admin: `admin@tukutane.test` / `Admin@123`
- Attendee: `amina@tukutane.test` / `Attendee@123`

## Browser checks

- Home: verify the hero, search form, three upcoming event cards and three involvement steps.
- Search: combine keyword, county, category, and date filters; check matching results,
  active-filter count, clear action, and a reversed date range's inline error.
- Pagination: move between pages and verify the selected filters remain in the URL and
  controls; the results section should scroll into view.
- Saved events: toggle a heart as a guest and reload; then sign in, toggle a heart,
  reload, and confirm the account's saved list/count persists.
- Registration: register a test attendee for an event with one remaining seat from two
  sessions; only one registration should confirm and the other should be waitlisted.
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
