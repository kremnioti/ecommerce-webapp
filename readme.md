# Simple Shop MVP

A minimal e-commerce demo built for learning and portfolio use. Customers can browse a product catalog, view details, manage a session-based cart, and complete checkout with order persistence in MySQL.

## Features

| Area | What it does |
|------|----------------|
| **Catalog** (`index.php`) | Grid of products loaded from the database |
| **Product page** (`product.php`) | Detail view, stock display, add-to-cart |
| **Cart** (`cart.php`) | Update quantities, remove items (qty `0`) |
| **Checkout** (`checkout.php`) | Collect shipping info, save order + line items, reduce stock |
| **Order confirmation** (`order-success.php`) | Thank-you page with order summary |

## Tech stack

- **Backend:** PHP 8+ (PDO, server-side sessions)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML, Bootstrap 5, vanilla JavaScript, custom CSS

## Project structure

```
ecommerce-webapp/
├── assets/css/style.css      # Layout tweaks
├── assets/js/cart.js         # Small cart UX (confirm empty cart)
├── assets/images/            # SVG product placeholders
├── config/db.php             # Database credentials
├── includes/
│   ├── bootstrap.php         # Session, cart helpers, DB include
│   ├── header.php / footer.php
├── index.php                 # Catalog
├── product.php
├── cart.php
├── checkout.php
├── order-success.php
└── database.sql              # Schema + sample products
```

## Setup (XAMPP)

1. Clone or copy this folder to `C:\xampp\htdocs\ecommerce-webapp` (or your Apache `htdocs` path).
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open [phpMyAdmin](http://localhost/phpmyadmin), import **`database.sql`**, or run it in the MySQL shell.
4. If your MySQL user/password differ from the defaults, edit **`config/db.php`**.
5. Visit **`http://localhost/ecommerce-webapp/`** in your browser.

## How the cart works (learning notes)

- The cart is stored in **`$_SESSION['cart']`** as `[ product_id => quantity ]`.
- Adding items happens on the product page via a **POST** form (no separate API).
- Checkout runs in a **database transaction**: insert order, insert line items, decrement stock; on failure everything rolls back.

## Possible extensions (not in MVP)

- User accounts and login
- Payment gateway (Stripe, PayPal)
- Admin panel to manage products
- CSRF tokens and prepared input validation hardening for production

## Author

Built as an educational MVP — clear structure and short comments in code for reviewers and interview discussion.

## License

MIT (or adjust for your portfolio).
