# MediCare Plus — Setup Guide

## Quick Start (XAMPP)

### 1. Copy project files
Place the `MediCare_Plus` folder inside:
```
C:\xampp\htdocs\MediCare_Plus\
```

### 2. Import the database
1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Click **New** → name the database `medicare_databs` → click **Create**
3. Click **Import** → choose `medicare_databs.sql` → click **Go**

### 3. Configure database port (if needed)
Open `db_connect.php` and check the `$port` value:
- Default XAMPP MySQL port is **3306**
- If your MySQL runs on **3307**, change it there

### 4. Visit the site
```
http://localhost/MediCare_Plus/Home.php
```

---

## Demo Credentials
All demo accounts use the same password: `password`  
*(The hash in the SQL is the Laravel/bcrypt default hash for `password`)*

| Role    | Email                      |
|---------|----------------------------|
| Admin   | admin@medicareplus.lk      |
| Doctor  | kasun@medicareplus.lk      |
| Patient | patient@medicareplus.lk    |

> **Tip:** To create your own accounts, use the Register page.  
> The password hash in the sample SQL (`$2y$10$92IXU...`) is the bcrypt hash of the string `password`.

---

## Bugs Fixed in This Release

| # | Error | Fix |
|---|-------|-----|
| 1 | `Fatal error: Cannot redeclare get_db_connection()` on all pages | Removed all duplicate function definitions from `db_connect.php` — it now only handles the DB connection |
| 2 | `Table 'medicare_databs.services' doesn't exist` | Added `services` table with sample data to `medicare_databs.sql` |
| 3 | `require_once 'functions.php'` fatal error | Created the missing `functions.php` placeholder |
| 4 | All pages broken (doctors, blog, dashboard) | All caused by bug #1 — now resolved |
