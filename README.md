# Full-Stack Digital Products E-Commerce Website

This is a complete, full-stack e-commerce website for selling digital products, built from scratch using vanilla PHP, MySQL, JavaScript, HTML, and CSS. It follows modern development practices without relying on major frameworks like Laravel, Symfony, or React.

## Features

- **Customer-Facing:**
  - Homepage with featured products.
  - Product listing page with grid layout.
  - Detailed product pages.
  - Product reviews and rating system (only for users who purchased the product).
  - Secure user registration and login system.
  - Full shopping cart functionality using PHP sessions.
  - Checkout process with order creation in the database.
  - Contact form with a simple anti-spam check.
  - SEO-friendly URLs for products.
  - Dynamic meta tags for better search engine indexing.

- **Admin Panel:**
  - Secure admin area protected from public users.
  - Admin dashboard.
  - Full CRUD (Create, Read, Update, Delete) functionality for product management.
  - Interface to view all orders placed on the site.
  - Interface to view all registered users.

- **Security:**
  - Password hashing using `password_hash()`.
  - Use of prepared statements (via PDO) to prevent SQL injection.
  - CSRF token protection on all forms.
  - Input sanitization and validation.

## Project Structure

```
/
├── config/
│   └── database.php      # Database credentials
├── public/               # Web server root
│   ├── assets/           # CSS, JS, images
│   ├── .htaccess         # URL rewriting for clean URLs
│   └── index.php         # Front controller / Router
├── scripts/
│   ├── database.sql      # The full database schema
│   └── generate_sitemap.php # (Optional) Script to generate a sitemap
├── src/
│   ├── controllers/      # Contains all business logic
│   ├── auth.php          # Auth helper functions (require_admin, CSRF)
│   ├── db.php            # Database connection logic
│   └── functions.php     # General helper functions (render_view, slugify)
└── templates/            # All view files (HTML)
    ├── admin/            # Admin panel views
    ├── partials/         # Reusable header/footer
    └── ...               # Public page views
```

## Setup Instructions

### 1. Prerequisites
- A web server that supports PHP (like Apache).
- A MySQL database server.
- PHP installed on your system.

### 2. Database Setup
1. Create a new MySQL database. It is recommended to name it `digital_products_store`, but you can use any name.
2. Import the `scripts/database.sql` file into your newly created database. This will create all the necessary tables.
   ```sh
   mysql -u your_username -p your_database_name < scripts/database.sql
   ```

### 3. Configuration
1. Open the `config/database.php` file.
2. Update the `host`, `dbname`, `user`, and `password` values to match your local database credentials.

### 4. Web Server Configuration
- Point your web server's document root to the `/public` directory of this project.
- Ensure that `mod_rewrite` (for Apache) is enabled so that the `.htaccess` file can handle clean URLs.

### 5. Create an Admin User
To access the admin panel, you need to manually set a user as an administrator.
1. Register a new user through the website's registration form.
2. Access your database directly using a tool like phpMyAdmin or the MySQL command line.
3. In the `users` table, find the user you just created.
4. Change the value of the `is_admin` column for that user from `0` to `1`.
5. Now, when you log in with that user, you will be redirected to the admin dashboard. The admin panel is accessible at `/admin/dashboard`.

You are now ready to use the website! You can add products through the admin panel and test the full customer journey.
