# 🏋️‍♂️ Gym Management System (PHP + MySQL)

A full-featured web application to manage gym operations including members, trainers, payments, and memberships. Built using PHP, MySQL, HTML/CSS, and JavaScript.

---

## ✅ Features

- 👥 Role-based user login (admin, staff)
- 🧍 Member registration and tracking
- 🧑‍🏫 Trainer management
- 💳 Payment and membership plan tracking
- 📊 Data export to CSV
- 🔐 Session-based access control
- 🎨 Admin and staff dashboards
- 📄 File-based and MySQL integration

---

## 💻 Tech Stack

| Component | Technology         |
|----------|--------------------|
| Backend  | PHP (v7+)          |
| Database | MySQL              |
| Frontend | HTML, CSS, JS      |
| Tables   | DataTables.js      |
| Server   | Apache (via XAMPP) |

---

## 🧰 Requirements

- ✅ [XAMPP](https://www.apachefriends.org/index.html) or WAMP/LAMP/MAMP
- ✅ Browser (Chrome/Firefox)
- ✅ VS Code / Sublime / Any code editor

---

## 🚀 Installation & Setup Guide

### 1️⃣ Download or Clone

```bash
git clone https://github.com/yourusername/gym-management-code.git


Or download and extract the ZIP.


2️⃣ Move to htdocs
Copy the extracted folder into your XAMPP directory:

makefile
Copy
Edit
C:\xampp\htdocs\gym-management-code
3️⃣ Start XAMPP
Open XAMPP Control Panel

Start Apache and MySQL

4️⃣ Import the Database
Go to: http://localhost/phpmyadmin 

Click on New, and create a database named:

sql
Copy
Edit
gymdb
Click Import tab

Upload and import the SQL file:

sql
Copy
Edit
gymdb.sql

5️⃣ Run the App
Open your browser and navigate to:

arduino
Copy
Edit
http://localhost/gym-management-code/index.php
✅ Done! You should see the login page or dashboard.
