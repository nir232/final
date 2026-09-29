# GrandStay Hotel Booking Management System

This package keeps the existing GrandStay hotel booking application and prepares it
for a simple Render deployment using **PHP + Apache + SQLite**. The visible UI and
application workflow are preserved.

## Main features

### Customer
- Register and login
- Homepage
- Customer dashboard
- Book/request a room
- View booking status
- Cancel pending booking
- Profile
- Change password
- Logout

### Hotel Manager
- Manager login
- Dashboard
- View pending booking requests
- Accept or delete booking requests
- View confirmed reservations
- Delete reservations
- Add rooms
- Edit rooms
- Delete rooms
- Profile
- Change password
- Logout

## Render deployment

The project contains a `Dockerfile`, `render.yaml`, and `render-entrypoint.sh`.
The application uses a local SQLite database file, so it does **not** require:

- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- a separate MySQL service

The database and initial rooms are created automatically when the web service starts.

### Default manager

- Username: `manager1`
- Password: `manager123`

Change the manager credentials in `render.yaml` before using this for a real public
hotel system.

## Important persistence note

The SQLite database is stored inside the web service filesystem. A standard free
Render service has an ephemeral filesystem, so database records can be lost when
the service is replaced or redeployed. The application will re-create its tables
and initial rooms after such a reset.

For production use where bookings must survive service replacement/redeployment,
use persistent storage or a managed database later.

## Local testing

The SQLite version does not require XAMPP MySQL. It requires PHP 8.2+ with the
PDO SQLite extension enabled. From the project directory, run PHP's built-in
server and open the application in a browser.

Example:

`php -S localhost:8000`

Then open `http://localhost:8000/home.php`.
