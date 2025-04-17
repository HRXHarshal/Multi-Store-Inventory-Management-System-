# Inventory Management System

## Overview

This project is a comprehensive web-based Inventory Management System built using PHP and MySQL. It provides functionalities for managing products, categories, warehouses, sales, customer purchase requests with secure verification, employee task assignments, and reporting.

## Problem Statement

Businesses often face challenges with:
*   Inaccurate inventory tracking leading to stockouts or overstocking.
*   Inefficient and insecure handling of customer purchase orders.
*   Lack of visibility into stock levels across multiple locations.
*   Difficulty in assigning and tracking order fulfillment tasks among employees.
*   Manual processes for inventory transfers and sales recording.

This system aims to solve these problems by providing a centralized, automated, and secure platform.

## Features

*   **Dashboard:** Overview of key metrics (total products, sales, categories, etc.), recent sales, and low stock items.
*   **User Management:** Multi-level user roles (Admin, Special User, Employee) with distinct permissions. Secure login and profile management.
*   **Product Management:** Add, edit, delete products with details like name, quantity, pricing, category, warehouse, and image.
*   **Category Management:** Organize products into categories.
*   **Warehouse Management:** Manage multiple warehouse locations, view stock per warehouse.
*   **Customer Management:** Maintain customer records (name, email, phone, address).
*   **Purchase Request System:**
    *   Customer-facing interface to submit purchase requests.
    *   **Secure OTP Email Verification:** Ensures request validity via One-Time Password sent to the customer's email.
    *   Admin/Special User can view, manage, and assign verified requests.
    *   Track request status (Pending, Verified, Assigned, Completed, Cancelled).
*   **Employee Workload Management:** Assign requests to employees and view current workload during reassignment for balanced distribution.
*   **Sales Management:** Automatically records sales upon completion of purchase requests. Track sales history.
*   **Inventory Transfers:** Record and track stock movement between warehouses.
*   **Reporting:** Generate sales reports (daily, monthly, date range) and inventory status reports.
*   **Email Notifications:** Automated emails for OTP verification and order confirmation using SendGrid.

## Unique Selling Points (USP) / Novelty

*   **Integrated Customer Purchase Request System:** Directly links customer demand with internal inventory and fulfillment processes.
*   **Secure OTP Verification:** Adds a crucial security layer to customer requests, validating email and intent.
*   **Intelligent Employee Workload Management:** Facilitates balanced task assignment based on real-time workload visibility.
*   **End-to-End Workflow:** Provides a seamless, secure flow from customer request initiation to final order completion and notification.

## Technology Stack

*   **Backend:** PHP (Procedural with functional decomposition)
*   **Database:** MySQL
*   **Web Server:** Apache (Typically run via XAMPP)
*   **Frontend:** HTML, CSS, Bootstrap, JavaScript, jQuery
*   **Email Service:** SendGrid API

## Architecture

The system follows a layered architecture:
*   **Presentation Layer:** HTML/CSS/JS rendered by PHP view files (`.php` files in root, `layouts/`).
*   **Application Layer:** PHP scripts handling requests, business logic (`includes/functions.php`), session management, and controlling data flow.
*   **Data Access Layer:** PHP functions (`includes/sql.php`, `includes/database.php`) interacting with the MySQL database using the `mysqli` extension.
*   **External Services:** Integration with SendGrid API for emails (`includes/email_functions.php`).

## Setup and Installation

### Prerequisites

*   **XAMPP:** Install XAMPP (or a similar package providing Apache, MySQL, PHP). Download from [https://www.apachefriends.org/](https://www.apachefriends.org/)
*   **SendGrid Account:** A SendGrid account and API Key are required for email notifications.

### Steps

1.  **Clone or Download:** Place the project files inside your XAMPP `htdocs` directory. The final path should be `c:\xampp\htdocs\InventorySystem_PHP\`.
2.  **Start Servers:** Open the XAMPP Control Panel and start the **Apache** and **MySQL** modules.
3.  **Database Setup:**
    *   Open your web browser and navigate to `http://localhost/phpmyadmin/`.
    *   Create a new database named `inventory_system`.
    *   Select the `inventory_system` database.
    *   Go to the "Import" tab.
    *   Click "Choose File" and select the database SQL file (e.g., `inventory_system.sql` - *Note: You might need to export this from your development environment or provide it separately*).
    *   Click "Go" to import the database structure and any initial data.
4.  **Configure SendGrid API Key:**
    *   Open the file `c:\xampp\htdocs\InventorySystem_PHP\includes\email_functions.php`.
    *   Locate the line where the SendGrid API key is set (it might look like `$sendgrid = new \SendGrid('YOUR_SENDGRID_API_KEY');`).
    *   Replace `'YOUR_SENDGRID_API_KEY'` with your actual SendGrid API key. Save the file.
5.  **Configure Database Credentials (if necessary):**
    *   Open the file `c:\xampp\htdocs\InventorySystem_PHP\includes\database.php`.
    *   Verify that the database credentials (host, username, password, database name) match your XAMPP MySQL setup. Default XAMPP usually uses `root` with no password.
    ```php
    define( 'DB_HOST', 'localhost' );
    define( 'DB_USER', 'root' );
    define( 'DB_PASS', '' ); // Default XAMPP password is empty
    define( 'DB_NAME', 'inventory_system' );
    ```

## Running the Application

1.  Ensure Apache and MySQL are running via the XAMPP Control Panel.
2.  Open your web browser and navigate to: `http://localhost/InventorySystem_PHP/`

## Login Credentials

Use the following default credentials (as found in `DATABASE FILE/01 LOGIN DETAILS & PROJECT INFO.txt`):

*   **Admin:**
    *   Username: `admin`
    *   Password: `admin`
*   **Special User:**
    *   Username: `special`
    *   Password: `special`
*   **User (Employee):**
    *   Username: `user`
    *   Password: `user`

## Key Workflows

*   **Purchase Request:**
    1.  Customer accesses the request form (`customer_view.php`).
    2.  Submits request details (`process_customer_request.php`).
    3.  Receives OTP via email (`email_functions.php`).
    4.  Verifies OTP (`verify_purchase.php`).
    5.  Admin views verified request (`purchase_requests.php`).
    6.  Admin assigns request to an employee (`reassign_request.php`).
    7.  Admin approves/completes the request (`approve_request.php`).
    8.  Stock is updated, sale is recorded, workload adjusted (`complete_purchase_request()` in `functions.php`).
    9.  Confirmation email sent to customer (`email_functions.php`).

## Folder Structure (Simplified)