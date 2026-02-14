# Fiche d'Evaluation — EST Fkih Ben Salah

A web-based internship and thesis evaluation sheet generator for **Ecole Superieure de Technologie — Fkih Ben Salah**, part of Universite Sultan Moulay Slimane (Morocco).

Professors log in, fill in student details and grades through cascading forms, and generate a formal A4 evaluation document ready for print with digital signature support.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-10.4-003545?logo=mariadb&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-blue)

---

## Table of Contents

- [Features](#features)
- [Screenshots](#screenshots)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Security](#security)
- [Configuration](#configuration)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## Features

- **Secure Authentication** — Email + CIN-based login for jury members with session management
- **Cascading Dropdown Forms** — Dynamic form that chains filiere, academic year, stage type, and student selection
- **Automatic Grade Calculation** — Weighted final grade computed from configurable percentages per filiere
- **A4 Evaluation Sheet** — Professional document layout with institutional headers, logos, and structured tables
- **Digital Signatures** — HTML5 Canvas-based signature pads for jury members and the filiere coordinator
- **Print-Ready Output** — Optimized `@media print` styles that produce a clean A4 document directly from the browser
- **Responsive Design** — Works on desktop, tablet, and mobile devices
- **Security Hardened** — CSRF protection, prepared SQL statements, XSS escaping, session fixation prevention

---

## Screenshots

| Login | Form | Evaluation Sheet |
|-------|------|------------------|
| Split-panel design with institutional branding | Card-based layout with cascading dropdowns | A4 document with tables, signatures, and print support |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2 (vanilla, no framework) |
| Database | MariaDB 10.4 / MySQL 5.7+ |
| Frontend | HTML5, CSS3 (custom properties), vanilla JavaScript |
| Typography | Google Fonts — DM Sans + Playfair Display |
| Server | XAMPP (Apache + MariaDB) or PHP built-in server |

No external PHP libraries or JavaScript frameworks are required.

---

## Prerequisites

- **PHP 8.0+** with `mysqli` extension enabled
- **MariaDB 10.4+** or **MySQL 5.7+**
- **XAMPP** (recommended) or any Apache/Nginx + PHP + MySQL stack
- A modern web browser (Chrome, Firefox, Edge, Safari)

---

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/fiche-d-evaluation.git
cd fiche-d-evaluation
```

### 2. Configure the Database Connection

Edit `config/database.php` and update the connection parameters to match your environment:

```php
$server   = "localhost";
$username = "root";
$password = "";           // Your MySQL password
$database = "f_evaluation";
$port     = 3306;         // Default MySQL port (XAMPP may use 3307)
```

> **Note:** XAMPP installations sometimes run MariaDB on port **3307** instead of the default 3306. Check your XAMPP configuration if you encounter connection issues.

### 3. Set Up the Database

See [Database Setup](#database-setup) below.

---

## Database Setup

### Option A: Import via Command Line

```bash
mysql -u root -p --port=3306 < f_evaluation.sql
```

### Option B: Import via phpMyAdmin

1. Open phpMyAdmin (typically at `http://localhost/phpmyadmin`)
2. Create a new database named `f_evaluation`
3. Select the database, go to the **Import** tab
4. Choose the `f_evaluation.sql` file and click **Go**

The SQL file will create all 10 tables and seed them with sample data including:
- 4 academic programs (filieres)
- 52 students
- 24 jury members (professors)
- 5 internship/project types
- Grade weighting percentages per filiere

---

## Running the Application

### Option A: Using XAMPP (Recommended)

1. Copy the project folder into your XAMPP web root:
   ```bash
   cp -r fiche-d-evaluation /opt/lampp/htdocs/fiche_d_evaluation
   ```
2. Start Apache and MySQL from the XAMPP control panel
3. Open your browser and navigate to:
   ```
   http://localhost/fiche_d_evaluation/login/login.php
   ```

### Option B: Using PHP Built-in Server

```bash
cd fiche-d-evaluation
php -S localhost:8080
```

Then open:
```
http://localhost:8080/login/login.php
```

### Test Credentials

The database comes seeded with jury members. Use any professor's email and CIN to log in. For example:

| Field | Value |
|-------|-------|
| Email | `elazzouzi.mohamed@gmail.com` |
| Password (CIN) | `123456` |

> The password field accepts the professor's **CIN number** (national ID), not a hashed password.

---

## Usage

### 1. Login
Enter your email and CIN number on the login page.

### 2. Fill the Evaluation Form
- **Select a filiere** (academic program) — this loads the available academic years
- **Select an academic year** — this loads available stage types and students
- **Select a stage type** — e.g., Stage Technique, Projet de Fin d'Etude
- **Select a student** — from the filtered list
- **Set jury size** (1-6 members), defense date, time, and location
- **Enter grades** — Rapport, Presentation orale, Note encadrant (each out of 20)
- **Assign jury members** — Select professors and assign roles (President, Rapporteur, etc.)
- Click **Soumettre l'evaluation**

### 3. Generate and Print
- Review the generated A4 evaluation sheet
- Jury members and the coordinator can **sign digitally** using the canvas pads
- Click **Imprimer** to print or save as PDF via the browser's print dialog

---

## Project Structure

```
fiche-d-evaluation/
├── config/
│   └── database.php              # Database connection (mysqli)
├── includes/
│   ├── auth.php                  # Session guard, CSRF token helpers
│   └── functions.php             # XSS escaping, redirect, sanitization
├── login/
│   ├── login.php                 # Authentication page
│   ├── login.css                 # Login styles (split-panel layout)
│   ├── login.js                  # Password toggle, focus effects
│   └── logout.php                # Session destruction
├── form/
│   ├── form.php                  # Evaluation form with cascading selects
│   ├── form.css                  # Card-based layout, grid system
│   └── form.js                   # Burger menu, scroll-top, jury filter
├── generate_pdf/
│   ├── generate_pdf.php          # A4 evaluation document generator
│   ├── generate_pdf.css          # A4 layout, tables, print styles
│   ├── generate_pdf.js           # Print button handler
│   ├── generate_signature.js     # Canvas signature (mouse + touch)
│   ├── EST Fkih Ben Saleh (1).png  # Institution logo
│   └── UMS.png                   # University system logo
├── f_evaluation.sql              # Database schema + seed data
├── deploy.php                    # Deployment helper script
└── README.md
```

---

## Database Schema

The application uses 10 tables in a relational MySQL/MariaDB database:

```
filiere (1)──────(*) annee_scolaire
   │                      │
   │                      ├──(*:*) stage         [via stage_par_annee_scolaire]
   │                      │
   ├──(*) etudiant ───────┘
   │        │
   │        ├──(*) rapport
   │        └──(*) soutenance ──(*:*) membre_jury [via jury_soutenance]
   │
   ├──(*) membre_jury
   └──(1) pourcentage
```

### Grade Weighting by Filiere

| Filiere | Rapport | Oral Presentation | Supervisor |
|---------|---------|-------------------|------------|
| Genie Informatique | 40% | 30% | 30% |
| Genie Civil | 35% | 35% | 30% |
| Informatique Decisionnelle | 40% | 40% | 20% |
| Industriel Agroalimentaire | 40% | 35% | 25% |

The final grade is calculated as:

```
Note finale = (Note rapport * %) + (Note presentation * %) + (Note encadrant * %)
```

---

## Security

| Measure | Implementation |
|---------|---------------|
| **SQL Injection** | All queries use prepared statements with `bind_param()` |
| **XSS Protection** | All output escaped via `esc()` (`htmlspecialchars` with `ENT_QUOTES`) |
| **CSRF Protection** | Token generated per session, validated on every form POST |
| **Session Fixation** | Session ID regenerated on successful login |
| **Auth Guard** | `require_login()` called on every protected page |
| **Secure Logout** | Full session destruction with cookie cleanup |

---

## Configuration

### Database Port

If your MySQL/MariaDB runs on a non-standard port, update `config/database.php`:

```php
$port = 3307; // Change to your port
```

### Grade Percentages

Grade weightings are stored in the `pourcentage` table and can be modified via phpMyAdmin or SQL:

```sql
UPDATE pourcentage
SET Pourcentage_rapport = 0.35,
    Pourcentage_presentation_orale = 0.35,
    Pourcentage_encadrant = 0.30
WHERE ID_filiere = 1;
```

### Adding New Data

- **New filiere:** Insert into `filiere`, then add corresponding `annee_scolaire`, `pourcentage`, and `stage_par_annee_scolaire` entries
- **New students:** Insert into `etudiant` with the correct `ID_filiere` and `ID_annee`
- **New jury members:** Insert into `membre_jury` with their filiere assignment

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| **Database connection failed** | Verify port number in `config/database.php`. XAMPP often uses 3307. |
| **Blank page after login** | Check PHP error logs. Ensure `mysqli` extension is enabled in `php.ini`. |
| **Cascading dropdowns not loading** | Verify the `f_evaluation.sql` was fully imported (all 10 tables with seed data). |
| **Table not found errors** | MySQL on Linux is case-sensitive. All table names must be **lowercase**. |
| **Print layout broken** | Use Chrome or Firefox for best `@media print` support. Ensure "Background graphics" is checked in print settings. |
| **Signatures not working on mobile** | Ensure you are using a modern browser. The canvas uses both mouse and touch event listeners. |

---

## License

This project is open source and available under the [MIT License](LICENSE).

---

**EST Fkih Ben Salah** — Universite Sultan Moulay Slimane  
Hay Tighnari, Route Nationale N11, 23200 Fkih Ben Salah  
Tel.: 05.23.43.46.66 / 05.23.43.49.99 | estfbs@usms.ma | [estfbs.usms.ac.ma](http://estfbs.usms.ac.ma/)
