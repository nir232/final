# hotel_Book - Render Ready (SQLite)

This package keeps the existing GrandStay hotel booking UI and application flow,
while replacing the MySQL connection layer with SQLite so the web service does not
need DB_HOST, DB_USER, DB_PASS, or a separate MySQL service.

## Render deployment

1. Put the CONTENTS of this folder directly in the root of a GitHub repository.
2. In Render, create a new Web Service from that repository.
3. Select Docker as the runtime. Render will use the included Dockerfile.
4. The application initializes its SQLite database automatically on startup.
5. The default manager account is:
   Username: manager1
   Password: manager123

## Important persistence note

The SQLite database is stored at /var/www/html/data/hotel_booking.sqlite.
A standard free Render web service has an ephemeral filesystem, so database data
can be lost when the service is replaced/redeployed. The application itself will
still initialize and run correctly. For permanent production booking data, attach
persistent storage or move the database to a managed external database later.

## Local PC test

The SQLite version no longer needs XAMPP MySQL. It requires PHP 8.2+ with the
PDO SQLite extension enabled. From the project folder, run a PHP server and open
home.php/index.php in the browser.

Manager:
- Username: manager1
- Password: manager123
