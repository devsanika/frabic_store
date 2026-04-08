# 🧵 Fabric Store

A full-stack e-commerce web application for browsing and purchasing fabrics and textiles, built with **PHP**, **MySQL**, and vanilla **HTML/CSS/JS**. Features a customer-facing storefront and a complete admin panel for managing products, orders, and users.

## 📸 Features

### 🔧 Admin Panel
- **Secure login** with SHA1-hashed passwords and session-based authentication
- **Dashboard** with live stats: total products, active/inactive products, registered users, admins, messages, and order counts
- **Product Management** — Add, edit, view, and toggle active/deactive status; image upload with format and size validation (JPG/PNG, max 2MB)
- **Order Management** — View all orders, update order status (in progress / cancelled) and payment status, delete orders
- **User Management** — View all registered users
- **Messages** — View customer inquiries submitted via the contact form
- **Admin Registration** — Add new admin accounts

## 📸 UI Preview

![Dashboard](UI/dashboard.png)
![Products](UI/view.png)

## 🗂️ Project Structure

```
frabic_store-main/
│
├── index.php                  # Customer-facing storefront (product listing)
├── fabric_store.sql           # Database schema + optional sample data
│
├── admin/
│   ├── login.php              # Admin login
│   ├── register.php           # New admin registration
│   ├── dashboard.php          # Admin overview with live stats
│   ├── add_product.php        # Add new product with image upload
│   ├── edit_product.php       # Edit existing product
│   ├── view_products.php      # List all products (filter by status)
│   ├── read_product.php       # View single product details
│   ├── orders.php             # Manage orders (update/delete)
│   ├── users.php              # View registered users
│   ├── messages.php           # View customer messages
│   └── admin_logout.php       # Session logout
│
├── components/
│   ├── connect.php            # PDO database connection + session setup
│   ├── admin_header.php       # Shared admin navigation/layout
│   └── alert.php             # Flash message display (success/warning)
│
├── admin/admin_style.css      # Admin panel styling
└── uploads/                   # Product images (auto-managed)
```

## 🛠️ Tech Stack

| Layer     | Technology                          |
|-----------|-------------------------------------|
| Backend   | PHP 7.4+ (PDO, sessions)            |
| Database  | MySQL 5.7+                          |
| Frontend  | HTML5, CSS3, Vanilla JS             |
| Icons     | Boxicons 2.1.4                      |
| Alerts    | SweetAlert2                         |
| Server    | Apache (XAMPP / WAMP recommended)   |

## ⚙️ Setup Instructions

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (or any Apache + MySQL stack)
- PHP 7.4 or higher
- A web browser

### 1. Clone or Extract the Project

```bash
# If using Git
git clone https://github.com/your-username/fabric-store.git

# Or extract the ZIP into your server's root directory
# XAMPP: C:/xampp/htdocs/frabic_store-main/
# Linux: /var/www/html/frabic_store-main/
```

### 2. Start Your Local Server

Launch **XAMPP** and start both **Apache** and **MySQL**.

### 3. Create the Database

1. Open [phpMyAdmin](http://localhost/phpmyadmin)
2. Click **Import**
3. Upload the `fabric_store.sql` file from the project root
4. Click **Go** — this creates the `fabric_store` database with all required tables

### 4. Configure the Database Connection

Open `components/connect.php` and verify the credentials match your setup:

```php
$host     = 'localhost';
$db_name  = 'fabric_store';
$username = 'root';
$password = '';           // Change if your MySQL has a password
```

### 5. Set Upload Folder Permissions

Ensure the `uploads/` folder is writable:

```bash
# Linux/Mac
chmod 755 uploads/
```

On Windows/XAMPP this is typically not required.

### 6. Run the Application

Open your browser and navigate to:

```
http://localhost/frabic_store-main/
```

Admin panel:

```
http://localhost/frabic_store-main/admin/login.php
```

## 🗄️ Database Schema

| Table      | Description                              |
|------------|------------------------------------------|
| `admin`    | Admin accounts (id, name, email, password, profile) |
| `products` | Product listings (id, name, price, image, detail, status) |
| `users`    | Registered customers (id, name, email, password) |
| `orders`   | Customer orders with status & payment tracking |
| `cart`     | Shopping cart items per user             |
| `wishlist` | Saved/wishlist items per user            |
| `message`  | Customer contact form submissions        |

## 🚀 Usage

1. **Admin** — Log in at `/admin/login.php` to add products, manage orders, and view messages.
2. **Customers** — Visit `index.php` to browse all active products.
3. To mark a product as visible on the storefront, set its status to **active** in the admin panel.
4. Product images must be **JPG or PNG**, and no larger than **2MB**.


## 📌 Notes

- Passwords are hashed using **SHA1**. For production use, consider upgrading to `password_hash()` with `PASSWORD_BCRYPT`.
- The `uploads/` folder stores all product images — do not delete it.
- The project uses **PDO** with prepared statements throughout, protecting against SQL injection.

## 🤝 Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you'd like to change.


