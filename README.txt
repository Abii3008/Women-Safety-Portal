SAFEHER - WOMEN SAFETY ALERT PORTAL
===================================

TECH STACK
- HTML, CSS, JavaScript
- PHP (XAMPP / Apache)
- MySQL (phpMyAdmin)

WHAT'S NEW IN V2
- Women illustrations (assets/women-hero.svg, woman-auth.svg, woman-banner.svg).
  To use a real photo instead, replace the <img src> in index.php with your own file.
- CSRF protection on every form + login throttling (5 fails / 15 min)
- Safety tools page: fake incoming call, check-in timer, siren, bright screen light, helplines
- SOS with 5-second cancel countdown, then one-tap WhatsApp/SMS sharing to each contact
- Primary contact, custom SOS message, safety-readiness score, profile + change password + delete account
- Already installed the old version? Run upgrade.sql once in phpMyAdmin (new installs just import database.sql).

FEATURES
- Responsive home page with bundled SVG image background
- User registration with validation and password_hash()
- Login/logout using PHP sessions
- Personal dashboard with summary cards
- Add and delete emergency contacts
- SOS alert record creation
- Optional browser geolocation attached to alert records
- Alert history with map link and resolve status
- Admin overview of users and alert records
- Prepared SQL statements and HTML output escaping

INSTALLATION (WINDOWS + XAMPP)
1. Extract the WomenSafetyPortal folder into:
   C:\xampp\htdocs\WomenSafetyPortal

2. Open XAMPP Control Panel and start Apache and MySQL.

3. Open http://localhost/phpmyadmin/

4. Click the Import tab, select database.sql from this folder, then click Import/Go.
   Alternatively, open database.sql and run it in phpMyAdmin's SQL tab.

5. Open:
   http://localhost/WomenSafetyPortal/

6. Register a new account, then log in.

MAKE AN ADMIN ACCOUNT
1. Register normally using the website.
2. In phpMyAdmin, select women_safety_db > SQL.
3. Run this query, replacing the email with the account you registered:
   UPDATE users SET role='admin' WHERE email='your-email@example.com';
4. Log out and log back in. The Admin panel link will appear.

IMPORTANT SAFETY LIMITATIONS
- This is an educational/demo web project.
- The SOS button saves a record in MySQL. It DOES NOT send SMS, place calls,
  or contact police. After saving, YOU press the WhatsApp/SMS buttons on your own
  phone to message contacts. Siren/timer only work while the page stays open.
- Browser location is optional and requires user permission. Location usually
  works on localhost; deployed sites generally require HTTPS.
- Do not rely on this demo in a real emergency. Contact local emergency services
  directly when you need immediate help.
- Before public deployment, add CSRF protection, rate limiting, stronger
  administrative controls, HTTPS, privacy policy, backups and security review.

DATABASE DEFAULTS
- Host: localhost
- Username: root
- Password: empty (typical local XAMPP default)
- Database: women_safety_db
If your MySQL root account has a password, update db.php accordingly.
