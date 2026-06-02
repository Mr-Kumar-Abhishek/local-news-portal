# Hind Bihar - Bilingual Local News Portal

`Hind Bihar` is a state-of-the-art, responsive, bilingual (Hindi & English) news platform designed for local, national, and international coverage. Built using the PHP full-stack MVC framework **CodeIgniter 4**, the platform provides a complete Content Management System (CMS) for editors and journalists, and an engaging reading experience for visitors.

---

## 🌟 Key Features

- **Bilingual Content Management**: True multi-lingual publishing. Articles, categories, tags, and media descriptions support side-by-side English and Hindi metadata.
- **Role-Based Access Control (RBAC)**: Secure access levels tailored to different team roles:
  - 👑 **Admin**: Full system control, configurations, user management, and tag/category setup.
  - 📝 **Editor**: Article moderation, comments management, and category/media curation.
  - ✍️ **Journalist**: Draft creation, article editing, and media uploads.
  - 👥 **Reader**: Personalized language preference, bookmark-ready articles, and community commenting.
- **Advanced Comment Moderation**: Reader comments can be filtered, approved, rejected, or deleted by administrators and editors to ensure a high-quality discussion space.
- **SEO & Syndication Suite**:
  - Automatically generated Google-compliant XML Sitemap (`/sitemap.xml`).
  - RSS feeds for global and category-specific streams (`/rss` and `/rss/(:category)`).
  - Clean slug-based URLs with customizable OG metadata tags for Facebook, Twitter, and OpenGraph.
- **Smart Search & Autocomplete**: Real-time autocomplete-assisted search capabilities to help readers discover articles quickly.
- **High-Performance Architecture**: Uses a lightweight SQLite3 backend by default for minimal memory footprint and instant deployments, while fully compatible with MySQL/MariaDB.
- **Security-First Focus**: Out-of-the-box CSRF protection, SQL injection prevention, strict file-upload validation, and XSS sanitization.

---

## 📂 Project Architecture

The portal leverages CodeIgniter 4's Model-View-Controller architecture:

```
                        ┌─────────────────────────────┐
                        │        Client Browser       │
                        └──────────────┬──────────────┘
                                       │ HTTP Request
                                       ▼
                        ┌─────────────────────────────┐
                        │      App Routing Engine     │
                        │    (app/Config/Routes.php)  │
                        └──────────────┬──────────────┘
                                       │
                                       ▼
                        ┌─────────────────────────────┐
                        │         Controllers         │
                        │     (app/Controllers/)      │
                        └──────────┬───────┬──────────┘
             Queries Data          │       │ Renders View
          ┌────────────────────────┘       └────────────────────────┐
          ▼                                                         ▼
┌───────────────────┐                                     ┌───────────────────┐
│      Models       │                                     │       Views       │
│  (app/Models/)    │                                     │   (app/Views/)    │
└─────────┬─────────┘                                     └───────────────────┘
          │ Reads/Writes
          ▼
┌───────────────────┐
│ SQLite3/MySQL DB  │
└───────────────────┘
```

For more details on the database relationships and technical design, refer to the [Software Design Document](DESIGN.md) (`DESIGN.md`).

---

## 🛠️ Tech Stack & Requirements

- **PHP Version**: `^8.2`
- **Backend Framework**: CodeIgniter `^4.7`
- **Database**: SQLite3 (default for local setup) or MySQL/MariaDB
- **Frontend Utilities**: Bootstrap `5.3.2`, Bootstrap Icons `1.11.2`, and Google Fonts (Noto Sans & Noto Sans Devanagari)
- **Testing Suite**: PHPUnit `^10.5`

---

## 🚀 Setup & Installation Guide

Follow these steps to set up the project on your local machine:

### 1. Prerequisites
Ensure you have the following installed on your machine:
- PHP 8.2 or higher
- Composer (PHP Package Manager)
- PHP Extensions: `intl`, `mbstring`, `sqlite3`

### 2. Install Dependencies
Run Composer to download and install all necessary dependencies:
```bash
composer install
```

### 3. Environment Configuration
Copy the default environment template file to create your active `.env` file:
```bash
cp env .env
```
Open `.env` and update the database settings or Base URL if needed. By default, the database is configured to use a local SQLite3 file:
```env
database.default.database = writable/hind-bihar.db
database.default.DBDriver = SQLite3
```

### 4. Database Initialization
Run the database migrations to set up the tables, triggers, and constraints:
```bash
php spark migrate
```

### 5. Seed Initial Data
Seed the database with pre-configured categories, tags, and an administrator account:
```bash
php spark db:seed HindBiharSeeder
```

### 6. Spin Up the Local Server
Run CodeIgniter's built-in development server:
```bash
php spark serve
```
Your local environment is now running at **`http://localhost:8080`**!

---

## 🔐 Default Login Credentials

After seeding, you can log in to the admin panel with the following credentials:

- **Username**: `admin`
- **Email**: `admin@hindbihar.com`
- **Password**: `admin123`
- **Role**: Administrator

To access the administration area, navigate to `/en/admin` or `/hi/admin`.

---

## 📁 Key Directories & Layout

The project structure matches standard CodeIgniter 4 guidelines with modular organization:

```
├── app/
│   ├── Config/          # Configuration files (Routes, Database, App, etc.)
│   ├── Controllers/     # HTTP Controllers (Auth, News, Search, RSS, Sitemap)
│   │   └── Admin/       # Admin controllers (Dashboard, News, Categories, Users)
│   ├── Database/        # Migrations and Seeders
│   ├── Models/          # Database Active-Record Models (Article, Category, Tag, etc.)
│   └── Views/           # HTML layout templates, admin pages, and news views
│       ├── admin/       # Admin-specific templates & views
│       ├── auth/        # Login and Register pages
│       ├── home/        # Home landing views
│       └── templates/   # Header, Footer, and Sidebar layouts
├── public/              # Document root (accessible to web server)
├── tests/               # Unit and database testing suites
└── writable/            # Temp files, cache, session storage, and SQLite databases
```

---

## 🧪 Running Tests

To run the automated PHPUnit test suite, execute:
```bash
composer test
```
Or run phpunit directly:
```bash
vendor/bin/phpunit
```

---

## 🔗 Project Documentation Links

For further technical reading, please refer to the following documents in the repository:
- **[SRS.md](SRS.md)**: Software Requirements Specification containing detailed system features, use cases, and functional specifications.
- **[DESIGN.md](DESIGN.md)**: Software Design Document detailing the system design, Entity-Relationship diagrams, detailed table schemas, and routing schemes.
- **[AGILE_PLAN.md](AGILE_PLAN.md)**: Agile Project Management Plan listing the project roadmap, sprint schedules, epics, and user stories.

---

## 📄 License
This project is licensed under the [MIT License](LICENSE). See the [LICENSE](LICENSE) file for details.
