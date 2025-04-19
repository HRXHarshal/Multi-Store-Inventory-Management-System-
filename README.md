# Inventory Management System

## Overview

MultiWarehouse IMS is a comprehensive web-based Inventory Management System built using PHP and MySQL. It facilitates efficient management of products, categories, multiple warehouses, sales, and inventory levels. Key features include a secure customer purchase request system using OTP email verification via SendGrid, role-based access control, and employee task assignment for order fulfillment with workload tracking.

## Key Features

*   **Dashboard:** At-a-glance view of key metrics like total sales, product count, category count, and customer count.
*   **Product Management:** Add, edit, delete, and view products, including details like quantity, buying price, selling price, category, and warehouse location.
*   **Category Management:** Organize products into categories.
*   **Warehouse Management:** Manage multiple warehouse locations.
*   **Sales Management:** Track sales, view sales reports (daily, weekly, monthly), and manage customer orders.
*   **Customer Purchase Requests:** Allows customers to submit purchase requests directly through the system.
*   **OTP Email Verification:** Secures customer requests by sending a One-Time Password (OTP) to the customer's email for verification before processing the order.
*   **Employee Task Assignment:** Admins can assign order fulfillment tasks to specific employees.
*   **Employee Workload Tracking:** Monitor the number of pending tasks assigned to each employee.
*   **Role-Based Access Control:** Different user roles (Admin, Special User, Employee) with specific permissions.
*   **Reporting:** Generate sales and inventory reports.

## Screenshots

![App Screenshot](https://postimg.cc/XZYJfCgB)

![App Screenshot](https://postimg.cc/GHnSHJcK)

## Tech Stack

*   **Backend:** PHP
*   **Database:** MySQL
*   **Frontend:** HTML, CSS, JavaScript, jQuery, Bootstrap
*   **Email Service:** SendGrid API
*   **Dependency Management:** Composer
*   **Environment:** XAMPP (Apache, MySQL, PHP)

## Setup and Installation

### Prerequisites

*   **XAMPP:** Install XAMPP (or a similar package providing Apache, MySQL, PHP). Download from [https://www.apachefriends.org/](https://www.apachefriends.org/)
*   **SendGrid Account:** A SendGrid account and API Key are required for email notifications (OTP, confirmations).
*   **Composer:** Required for PHP dependencies. Download from [https://getcomposer.org/](https://getcomposer.org/)

### Steps

1.  **Clone or Download:** Place the project files inside your XAMPP `htdocs` directory. The final path should be `c:\xampp\htdocs\InventorySystem_PHP\`.
    ```bash
    git clone <your-repository-url> c:\xampp\htdocs\InventorySystem_PHP
    cd c:\xampp\htdocs\InventorySystem_PHP
    ```

2.  **Start Servers:** Open the XAMPP Control Panel and start the **Apache** and **MySQL** modules.

3.  **Install Dependencies:** Open a command prompt or terminal in the project directory (`c:\xampp\htdocs\InventorySystem_PHP`) and run:
    ```bash
    composer install
    ```

4.  **Database Setup:**
    *   Open your web browser and navigate to `http://localhost/phpmyadmin/`.
    *   Create a new database named `inventory_system` (using `utf8mb4_general_ci` collation is recommended).
    *   Select the `inventory_system` database.
    *   Go to the "Import" tab.
    *   Click "Choose File" and select the database SQL file provided in the repository (e.g., `database/inventory_system.sql` - *Note: Ensure you have this SQL file in your repo*).
    *   Click "Go" or "Import" to import the database structure and any initial data.

5.  **Environment Configuration:**
    *   Copy the `.env.example` file to create a new file named `.env` in the project root.
        ```bash
        copy .env.example .env
        ```
    *   Open the `.env` file and update the following variables with your actual credentials:
        ```dotenv
        SENDGRID_API_KEY="YOUR_ACTUAL_SENDGRID_API_KEY"
        SENDGRID_SENDER_EMAIL="your_verified_sender@example.com"
        SENDGRID_SENDER_NAME="Your Application Name"
        ```

6.  **Configure Database Credentials (if necessary):**
    *   The primary database connection should be configured within `c:\xampp\htdocs\InventorySystem_PHP\includes\database.php`.
    *   Verify that the credentials match your XAMPP MySQL setup. Default XAMPP usually uses `root` with no password.
    ```php
    define( 'DB_HOST', 'localhost' );
    define( 'DB_USER', 'root' );
    define( 'DB_PASS', '' ); // Default XAMPP password is empty
    define( 'DB_NAME', 'inventory_system' );
    ```
    *(Alternatively, you could modify the setup to read these from the `.env` file as well for consistency).*

7.  **Access the Application:** Open your web browser and navigate to `http://localhost/InventorySystem_PHP/`.

## Usage

1.  **Login:** Access the login page and use the credentials for different roles (Admin, Special User, Employee). Default credentials might be provided via the database import or need to be created.
2.  **Admin:** Manage products, categories, warehouses, view reports, manage users, and assign tasks.
3.  **Employee:** View assigned tasks and potentially update their status.
4.  **Customer (via Request Form):** Fill out the purchase request form, receive an OTP via email, and verify the OTP to submit the request.

## Folder Structure (Simplified)
nventorySystem_PHP/
├── admin/              # Admin panel specific files
├── assets/             # CSS, JS, images (frontend assets)
├── customer/           # Customer request related files
├── database/           # SQL dump file(s) (Example location)
├── employee/           # Employee panel specific files
├── includes/           # Core PHP files (database, functions, session, etc.)
├── layouts/            # Header, Footer, Navigation templates
├── libs/               # Third-party libraries (if not using Composer exclusively)
├── screenshots/        # Application screenshots
├── vendor/             # Composer dependencies
├── .env                # Environment variables (ignored by Git)
├── .env.example        # Example environment file
├── .gitignore          # Git ignore rules
├── composer.json       # Composer configuration
├── composer.lock       # Composer lock file
├── index.php           # Main entry point or login redirect
└── README.md           # This file

```plaintext

*(Note: Please adjust the folder structure details if they differ significantly in your project)*.
 ```
