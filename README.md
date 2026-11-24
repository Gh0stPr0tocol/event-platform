# Tukutane

Tukutane is a free event-registration app for community, student, and county-level
events across Kenya. Attendees can discover events, save events, register, and manage
their registrations. Administrators can manage events, venues, categories, and
registration statuses.

## Features

### Attendees

- Create an account, sign in, and manage a profile.
- Browse and filter upcoming events by keyword, county, category, and date.
- Save events and revisit saved events.
- Register for events; when an event is full, join its waitlist.
- Review and cancel personal registrations.

### Administrators

- Sign in using an account with the admin role.
- View event and registration summaries.
- Create, edit, and delete events.
- Manage categories and venues.
- Review registrations and change their status.

## Technologies

- PHP without a framework
- MySQL/MariaDB through PDO
- HTML, CSS, and vanilla JavaScript
- XAMPP for local Apache and MySQL/MariaDB

## Project structure

```text
app/
  controllers/       Request handling and route actions
  helpers/           Authentication, validation, and shared functions
  models/            PDO queries and business rules
  views/
    admin/           Administrator pages
    auth/            Login and registration
    dashboard/       Attendee dashboard and profile
    errors/          Error pages
    events/          Event discovery and details
    layouts/         Shared page layouts
config/              Application and database configuration
database/            Schema, sample data, and migration SQL
public/
  css/               Main and admin stylesheets
  images/            ERD, event images, and other visual assets
  js/                Browser-side interactions and validation
  index.php          Front controller
```

## XAMPP setup

1. Put the repository in `C:\xampp\htdocs\event-platform`.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin/`. The schema script creates the
   `event_platform` database if it does not exist. You may also create that database
   in phpMyAdmin first.
4. Import these SQL files into `event_platform` in this exact order:
   1. `database/schema.sql` — creates/rebuilds the base tables.
   2. `database/sample_data.sql` — inserts counties, categories, venues, accounts,
      events, and registrations.
   3. `database/sample_events_extra.sql` — adds sample venues and events.
   4. `database/sample_events_extra_2.sql` — adds another sample venue/event batch.
   5. `database/add_saved_events_and_indexes.sql` — adds saved events and supporting
      indexes; run this migration once.

   The sample-data scripts are not intended to be run twice. `schema.sql` drops and
   recreates the tables, so do not import it into a database whose data you need to
   keep. The application connects as MySQL user `root` with an empty password by
   default; update `config/database.php` if your local XAMPP credentials differ.
5. Open `http://localhost/event-platform/public/`.

For local development, `APP_DEBUG` is `false` in `config/config.php`. Keep it false
in a submitted/deployed version. If you need diagnostic detail locally, switch it
to `true` temporarily and never expose that setting on a public server.

## Test accounts

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@tukutane.test` | `Admin@123` |
| Attendee | `amina@tukutane.test` | `Attendee@123` |
| Attendee | `brian@tukutane.test` | `Attendee@123` |
| Attendee | `faith@tukutane.test` | `Attendee@123` |
| Attendee | `daniel@tukutane.test` | `Attendee@123` |

These are sample credentials for a local development database only. Do not use
them for a public installation.

## Manual browser checklist

- Open the home page and browse/search events using keyword, county, category, and
  date filters; check clear-filter behavior and invalid date-range feedback.
- Browse multiple events pages and verify the query filters remain selected.
- Create an account and sign in; try invalid values and check inline field feedback.
- Save an event, reload, and confirm the saved state/count persists for a signed-in
  account.
- Register for an event, confirm its status, then cancel it from **My registrations**.
- Sign in as an admin; create/edit/delete an event and manage a category or venue.
- Change a registration status and verify the updated status is visible.
- Use Logout and confirm the signed-out navigation appears.
- Open the ERD at `public/images/erd.svg` and compare it with the schema and migration.

## Database tables and ERD

The schema and saved-events migration create seven tables: `regions`, `categories`,
`users`, `venues`, `events`, `registrations`, and `saved_events`. The ERD is
[`public/images/erd.svg`](public/images/erd.svg).

## Future work

- M-Pesa payments
- SMS reminders
- Swahili language support
- A regional organiser role
