<div align="center">

# Aakar Creatives

### Personalized Gifting & Product Catalog Platform

A modern full-stack web application built with PHP and MySQL for showcasing personalized gifts, managing products, and streamlining customer inquiries.

<p>

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![Apache](https://img.shields.io/badge/Apache-D22128?style=flat-square&logo=apache&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-22c55e?style=flat-square)
![Status](https://img.shields.io/badge/Status-Live-22c55e?style=flat-square)

</p>

**[Live Demo →](https://aakar-creatives.infinityfree.me)**

</div>

---

## Overview

**Aakar Creatives** is a full-stack personalized gifting platform built to simplify product showcase and customer engagement for a customized gifting business. The application delivers a responsive product catalog, dynamic category management, and a WhatsApp-based inquiry workflow — all backed by a secure PHP-powered admin dashboard and a MySQL database.

Built as a real-world business solution, this project demonstrates end-to-end web development: database schema design, server-side PHP logic, REST-style API endpoints, and a clean, mobile-friendly UI.

---

## Live Preview

> **URL:** [https://aakar-creatives.infinityfree.me](https://aakar-creatives.infinityfree.me)

---

## Features

| Customer-Facing | Admin Dashboard |
|---|---|
| Responsive product catalog | Full product CRUD |
| Category & occasion filtering | Category management |
| Product detail pages | Banner / highlight management |
| Live search (AJAX) | Dynamic MySQL-backed data |
| WhatsApp inquiry button | Secure admin panel |
| Newsletter subscription | Order / inquiry tracking |
| Occasion-based browsing | Upload management |

---

## Technology Stack

| Layer | Technologies |
|---|---|
| **Frontend** | HTML5, CSS3, Bootstrap 5, JavaScript (ES6) |
| **Backend** | PHP 8.2 |
| **Database** | MySQL |
| **Server** | Apache (shared hosting compatible) |
| **APIs** | Custom PHP REST-style endpoints |
| **Version Control** | Git & GitHub |

---

## Project Structure

```text
aakar-creatives/
│
├── config/
│   └── .env                   # Environment variables (DB credentials, keys)
│
├── includes/
│   ├── db.php                 # Database connection
│   ├── header.php             # Shared site header
│   └── footer.php             # Shared site footer
│
├── public/
│   ├── css/                   # Stylesheets
│   ├── images/                # Static assets
│   └── js/                    # Client-side scripts
│
├── uploads/
│   ├── categories/            # Category images
│   └── products/              # Product images
│
├── index.php                  # Home page
├── shop.php                   # Product catalog
├── product.php                # Product detail page
├── about.php                  # About page
├── occasions.php              # Occasion-based browsing
├── track.php                  # Order / inquiry tracker
├── admin.php                  # Admin dashboard
├── auth-api.php               # Authentication API
├── search-api.php             # Live search API
├── newsletter_subscribe.php   # Newsletter handler
├── database.sql               # Full DB schema + seed data
└── README.md
```

---

## Architecture

```
Browser
   │
   ├─── Static assets (public/css, public/js, public/images)
   │
   ├─── PHP Pages (index, shop, product, occasions, about, track)
   │         │
   │         └─── includes/db.php ──► MySQL Database
   │
   ├─── AJAX Endpoints
   │         ├── search-api.php        (live product search)
   │         ├── auth-api.php          (admin authentication)
   │         └── newsletter_subscribe  (email capture)
   │
   └─── admin.php ──────────────────► CRUD (products, categories, banners)
                                             │
                                        uploads/ (images)
```

---

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/amitghoyal/aakar-creatives.git
cd aakar-creatives
```

### 2. Configure the Database

```bash
# Create a MySQL database, then import the schema
mysql -u your_user -p your_database < database.sql
```

### 3. Set Environment Variables

```bash
# Copy the example env file and update credentials
cp config/.env.example config/.env
```

Edit `config/.env`:

```env
DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password
```

### 4. Start the Server

- **Local:** Start Apache & MySQL via XAMPP / Laragon / MAMP
- **Shared Hosting:** Upload files via FTP, create a MySQL database in cPanel, import `database.sql`, and update `.env`

---

## API Endpoints

| Endpoint | Method | Description |
|---|---|---|
| `search-api.php?q={query}` | GET | Live product search (JSON) |
| `auth-api.php` | POST | Admin login / session management |
| `newsletter_subscribe.php` | POST | Newsletter email capture |

---

## Key Highlights

- **Modular PHP architecture** — shared `header.php` / `footer.php` keeps markup DRY
- **Dynamic MySQL integration** — all products, categories, and banners are database-driven
- **Fully responsive UI** — Bootstrap 5 grid adapts to mobile, tablet, and desktop
- **WhatsApp inquiry workflow** — customers can inquire directly without a cart/checkout system
- **AJAX live search** — `search-api.php` powers instant results as users type
- **Shared hosting compatible** — no Composer, no Node; runs on standard PHP + Apache stacks
- **Clean upload management** — category and product images stored in organized subdirectories

---

## Future Enhancements

- [x] Shopping cart & checkout flow
- [ ] Razorpay / UPI payment gateway
- [ ] Customer authentication & order history
- [x] Wishlist & save-for-later
- [ ] Product reviews & star ratings
- [ ] Email notifications (order confirmation, inquiry reply)
- [ ] REST API (JSON) for potential mobile app
- [ ] SEO: sitemap.xml, meta tags, structured data

---

## Author

<div align="center">

**Amit Ghoyal**
MCA Student · Full-Stack Developer

[![GitHub](https://img.shields.io/badge/GitHub-amitghoyal-181717?style=flat-square&logo=github)](https://github.com/amitghoyal)

</div>

---

<div align="center">

Made with care for **Aakar Creatives** — personalized gifts, delivered with love.

</div>
