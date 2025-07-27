# WanderChicVibes

A full-stack website for a blog about fashion, lifestyle, travel, healthcare, and technology.

## Features

- Fully responsive design
- Advanced SEO features
- Database integration
- Admin panel for content management

## Technologies Used

- HTML
- CSS
- JavaScript
- PHP
- MySQL

## Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/wanderchicvibes.git
   ```
2. **Import the database:**
   - Create a new database in your MySQL server.
   - Import the `database.sql` file into your new database.
3. **Configure the database connection:**
   - Open the `config.php` file.
   - Update the `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME` constants with your database credentials.
4. **Run the website:**
   - Place the project files in your web server's root directory (e.g., `htdocs` for XAMPP).
   - Open your web browser and navigate to the project's URL.

## Admin Panel

- **URL:** `http://your-website.com/admin`
- **Username:** `admin`
- **Password:** `password` (The password in the database is a hash of 'password')

From the admin panel, you can:

- **Manage posts:** Create, read, update, and delete blog posts.
- **Logout:** Securely log out of the admin panel.
