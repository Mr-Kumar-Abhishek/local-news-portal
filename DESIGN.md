# Software Design Document
## Hind Bihar - Local News Website

**Version:** 1.0  
**Date:** June 2026  
**Project:** Hind Bihar News Portal  
**Framework:** CodeIgniter 4

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [System Architecture](#2-system-architecture)
3. [Database Design](#3-database-design)
4. [Component Design](#4-component-design)
5. [Directory Structure](#5-directory-structure)
6. [Security Design](#6-security-design)
7. [API Design](#7-api-design)
8. [Routing Design](#8-routing-design)

---

## 1. Architecture Overview

### 1.1 MVC Pattern Implementation

Hind Bihar follows the Model-View-Controller (MVC) architectural pattern as implemented by CodeIgniter 4:

```
┌─────────────────────────────────────────────────────────────┐
│                        Browser Request                       │
└─────────────────────────────┬───────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      CodeIgniter Router                      │
│                    (app/Config/Routes.php)                   │
└─────────────────────────────┬───────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        CONTROLLER                            │
│              - Handles HTTP requests                         │
│              - Validates input                               │
│              - Orchestrates business logic                   │
└───────────┬─────────────────────────────────┬───────────────┘
            │                                 │
            ▼                                 ▼
┌───────────────────────┐         ┌───────────────────────────┐
│        MODEL          │         │           VIEW            │
│  - Database queries   │         │  - HTML templates         │
│  - Business logic     │         │  - Presentation layer     │
│  - Data validation    │         │  - Language files         │
└───────────┬───────────┘         └───────────────────────────┘
            │
            ▼
┌───────────────────────┐
│     MySQL Database    │
└───────────────────────┘
```

### 1.2 Design Principles

- **Separation of Concerns**: Clear boundaries between controllers, models, and views
- **DRY (Do not Repeat Yourself)**: Reusable components and helper functions
- **SOLID Principles**: Single responsibility, open/closed, interface segregation
- **Security by Default**: Input validation, output escaping, CSRF protection

---

## 2. System Architecture

### 2.1 High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                           CLIENT LAYER                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              │
│  │Desktop Browser│  │Mobile Browser│  │  RSS Reader  │              │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘              │
└─────────┴─────────────────┴─────────────────┴───────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────────┐
│                          WEB SERVER LAYER                            │
│                    Apache/Nginx + PHP-FPM                            │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │                    CodeIgniter 4 Application                   │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐           │  │
│  │  │ Controllers │  │   Models    │  │    Views    │           │  │
│  │  └─────────────┘  └─────────────┘  └─────────────┘           │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐           │  │
│  │  │   Filters   │  │  Libraries  │  │   Helpers   │           │  │
│  │  └─────────────┘  └─────────────┘  └─────────────┘           │  │
│  └───────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         DATA LAYER                                   │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐  │
│  │   MySQL/MariaDB  │  │   File Storage   │  │  Cache - Redis   │  │
│  │    Database      │  │   Media/Uploads  │  │   (Optional)     │  │
│  └──────────────────┘  └──────────────────┘  └──────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.2 Technology Stack

| Layer | Technology | Version |
|-------|------------|---------|
| Frontend | HTML5, CSS3, JavaScript | Latest |
| CSS Framework | Bootstrap | 5.x |
| JavaScript | Vanilla JS / Alpine.js | Latest |
| Backend Framework | CodeIgniter | 4.x |
| Programming Language | PHP | 8.0+ |
| Database | MySQL / MariaDB | 8.0+ / 10.5+ |
| Web Server | Apache / Nginx | 2.4+ / 1.18+ |
| Cache | Redis / Memcached | Optional |
| Search | MySQL Full-Text | Built-in |

### 2.3 Server Requirements

- PHP 8.0 or higher with extensions: intl, mbstring, json, mysqlnd, xml, curl
- MySQL 8.0+ or MariaDB 10.5+
- Apache with mod_rewrite or Nginx
- Composer for dependency management
- Minimum 2GB RAM, 20GB storage

---

## 3. Database Design

### 3.1 Entity Relationship Overview

```
┌─────────┐       ┌───────────┐       ┌──────────┐
│  users  │───────│ articles  │───────│categories│
└─────────┘       └───────────┘       └──────────┘
     │                  │                   │
     │            ┌─────┴─────┐             │
     │            │           │             │
     │       ┌────▼───┐  ┌────▼────┐        │
     │       │  tags  │  │  media  │        │
     │       └────────┘  └─────────┘        │
     │                                      │
     └──────────────┬───────────────────────┘
                    │
              ┌─────▼─────┐
              │ comments  │
              └───────────┘
```

### 3.2 Table Schemas

#### 3.2.1 Users Table

```sql
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'editor', 'journalist', 'reader') NOT NULL DEFAULT 'reader',
    `first_name` VARCHAR(100) NULL,
    `last_name` VARCHAR(100) NULL,
    `display_name` VARCHAR(200) NULL,
    `bio` TEXT NULL,
    `avatar` VARCHAR(255) NULL,
    `language_preference` ENUM('en', 'hi') NOT NULL DEFAULT 'en',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `email_verified_at` DATETIME NULL,
    `last_login_at` DATETIME NULL,
    `reset_token` VARCHAR(255) NULL,
    `reset_token_expires` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_users_email` (`email`),
    UNIQUE KEY `idx_users_username` (`username`),
    KEY `idx_users_role` (`role`),
    KEY `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.2.2 Articles Table

```sql
CREATE TABLE `articles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title_en` VARCHAR(500) NULL,
    `title_hi` VARCHAR(500) NULL,
    `slug_en` VARCHAR(500) NULL,
    `slug_hi` VARCHAR(500) NULL,
    `content_en` LONGTEXT NULL,
    `content_hi` LONGTEXT NULL,
    `excerpt_en` TEXT NULL,
    `excerpt_hi` TEXT NULL,
    `meta_title_en` VARCHAR(255) NULL,
    `meta_title_hi` VARCHAR(255) NULL,
    `meta_description_en` TEXT NULL,
    `meta_description_hi` TEXT NULL,
    `featured_image_id` INT UNSIGNED NULL,
    `author_id` INT UNSIGNED NOT NULL,
    `editor_id` INT UNSIGNED NULL,
    `category_id` INT UNSIGNED NOT NULL,
    `status` ENUM('draft', 'pending', 'approved', 'published', 'archived') NOT NULL DEFAULT 'draft',
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `is_breaking` TINYINT(1) NOT NULL DEFAULT 0,
    `allow_comments` TINYINT(1) NOT NULL DEFAULT 1,
    `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `published_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_articles_slug_en` (`slug_en`),
    UNIQUE KEY `idx_articles_slug_hi` (`slug_hi`),
    KEY `idx_articles_author` (`author_id`),
    KEY `idx_articles_category` (`category_id`),
    KEY `idx_articles_status` (`status`),
    KEY `idx_articles_published` (`published_at`),
    KEY `idx_articles_featured` (`is_featured`),
    FULLTEXT KEY `ft_articles_en` (`title_en`, `content_en`),
    FULLTEXT KEY `ft_articles_hi` (`title_hi`, `content_hi`),
    CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_articles_editor` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_articles_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_articles_featured_image` FOREIGN KEY (`featured_image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.2.3 Categories Table

```sql
CREATE TABLE `categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` INT UNSIGNED NULL,
    `name_en` VARCHAR(200) NOT NULL,
    `name_hi` VARCHAR(200) NOT NULL,
    `slug_en` VARCHAR(200) NOT NULL,
    `slug_hi` VARCHAR(200) NOT NULL,
    `description_en` TEXT NULL,
    `description_hi` TEXT NULL,
    `icon` VARCHAR(100) NULL,
    `color` VARCHAR(7) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_categories_slug_en` (`slug_en`),
    UNIQUE KEY `idx_categories_slug_hi` (`slug_hi`),
    KEY `idx_categories_parent` (`parent_id`),
    KEY `idx_categories_active` (`is_active`),
    CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.2.4 Tags Table

```sql
CREATE TABLE `tags` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name_en` VARCHAR(100) NOT NULL,
    `name_hi` VARCHAR(100) NOT NULL,
    `slug_en` VARCHAR(100) NOT NULL,
    `slug_hi` VARCHAR(100) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_tags_slug_en` (`slug_en`),
    UNIQUE KEY `idx_tags_slug_hi` (`slug_hi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.2.5 Article Tags Pivot Table

```sql
CREATE TABLE `article_tags` (
    `article_id` INT UNSIGNED NOT NULL,
    `tag_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`article_id`, `tag_id`),
    KEY `idx_article_tags_tag` (`tag_id`),
    CONSTRAINT `fk_article_tags_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_article_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.2.6 Comments Table

```sql
CREATE TABLE `comments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `article_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NULL,
    `parent_id` INT UNSIGNED NULL,
    `guest_name` VARCHAR(100) NULL,
    `guest_email` VARCHAR(255) NULL,
    `content` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'spam', 'rejected') NOT NULL DEFAULT 'pending',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_comments_article` (`article_id`),
    KEY `idx_comments_user` (`user_id`),
    KEY `idx_comments_parent` (`parent_id`),
    KEY `idx_comments_status` (`status`),
    CONSTRAINT `fk_comments_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.2.7 Media Table

```sql
CREATE TABLE `media` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(100) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `file_size` INT UNSIGNED NOT NULL,
    `width` INT UNSIGNED NULL,
    `height` INT UNSIGNED NULL,
    `alt_text_en` VARCHAR(255) NULL,
    `alt_text_hi` VARCHAR(255) NULL,
    `caption_en` TEXT NULL,
    `caption_hi` TEXT NULL,
    `thumbnail_path` VARCHAR(500) NULL,
    `medium_path` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_media_user` (`user_id`),
    KEY `idx_media_type` (`file_type`),
    CONSTRAINT `fk_media_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.2.8 Settings Table

```sql
CREATE TABLE `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT NULL,
    `setting_type` ENUM('string', 'integer', 'boolean', 'json') NOT NULL DEFAULT 'string',
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `is_public` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_settings_key` (`setting_key`),
    KEY `idx_settings_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.2.9 Sessions Table

```sql
CREATE TABLE `ci_sessions` (
    `id` VARCHAR(128) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `data` BLOB NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 4. Component Design

### 4.1 Controllers

#### 4.1.1 Controller Overview

| Controller | Namespace | Purpose |
|------------|-----------|---------|
| Home | App\Controllers | Homepage and landing pages |
| News | App\Controllers | Article display and listing |
| Category | App\Controllers | Category pages |
| Search | App\Controllers | Search functionality |
| Auth | App\Controllers | Authentication |
| Admin\Dashboard | App\Controllers\Admin | Admin overview |
| Admin\Articles | App\Controllers\Admin | Article management |
| Admin\Categories | App\Controllers\Admin | Category management |
| Admin\Users | App\Controllers\Admin | User management |
| Admin\Comments | App\Controllers\Admin | Comment moderation |
| Admin\Media | App\Controllers\Admin | Media library |
| Admin\Settings | App\Controllers\Admin | System settings |
| Api\News | App\Controllers\Api | REST API endpoints |

#### 4.1.2 News Controller Example

```php
<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\CategoryModel;

class News extends BaseController
{
    protected ArticleModel $articleModel;
    protected CategoryModel $categoryModel;
    
    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * Display a single article
     */
    public function show(string $lang, string $slug): string
    {
        $article = $this->articleModel->findBySlug($slug, $lang);
        
        if (!$article) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        
        // Increment view count
        $this->articleModel->incrementViews($article['id']);
        
        // Get related articles
        $relatedArticles = $this->articleModel->getRelated($article['id'], 5);
        
        return view('news/show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'lang' => $lang
        ]);
    }
    
    /**
     * List articles by category
     */
    public function category(string $lang, string $categorySlug): string
    {
        $category = $this->categoryModel->findBySlug($categorySlug, $lang);
        
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        
        $articles = $this->articleModel
            ->where('category_id', $category['id'])
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->paginate(10);
        
        return view('news/category', [
            'category' => $category,
            'articles' => $articles,
            'pager' => $this->articleModel->pager,
            'lang' => $lang
        ]);
    }
}
```

### 4.2 Models

#### 4.2.1 Model Overview

| Model | Table | Key Methods |
|-------|-------|-------------|
| ArticleModel | articles | findBySlug, getPublished, getRelated, incrementViews |
| CategoryModel | categories | findBySlug, getWithArticleCount, getHierarchy |
| UserModel | users | authenticate, createUser, updateProfile |
| CommentModel | comments | getByArticle, getPending, approve, reject |
| TagModel | tags | findOrCreate, getPopular |
| MediaModel | media | upload, delete, getByType |
| SettingModel | settings | get, set, getByGroup |

#### 4.2.2 Article Model Example

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    
    protected $allowedFields = [
        'title_en', 'title_hi', 'slug_en', 'slug_hi',
        'content_en', 'content_hi', 'excerpt_en', 'excerpt_hi',
        'meta_title_en', 'meta_title_hi', 
        'meta_description_en', 'meta_description_hi',
        'featured_image_id', 'author_id', 'editor_id', 'category_id',
        'status', 'is_featured', 'is_breaking', 'allow_comments',
        'view_count', 'published_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    /**
     * Find article by slug and language
     */
    public function findBySlug(string $slug, string $lang = 'en'): ?array
    {
        $slugField = "slug_{$lang}";
        
        return $this->where($slugField, $slug)
                    ->where('status', 'published')
                    ->first();
    }
    
    /**
     * Get published articles with pagination
     */
    public function getPublished(int $limit = 10, int $offset = 0): array
    {
        return $this->where('status', 'published')
                    ->orderBy('published_at', 'DESC')
                    ->findAll($limit, $offset);
    }
    
    /**
     * Get related articles based on category and tags
     */
    public function getRelated(int $articleId, int $limit = 5): array
    {
        $article = $this->find($articleId);
        
        return $this->where('category_id', $article['category_id'])
                    ->where('id !=', $articleId)
                    ->where('status', 'published')
                    ->orderBy('published_at', 'DESC')
                    ->findAll($limit);
    }
    
    /**
     * Increment article view count
     */
    public function incrementViews(int $articleId): void
    {
        $this->builder()
             ->where('id', $articleId)
             ->set('view_count', 'view_count + 1', false)
             ->update();
    }
}
```

### 4.3 Views

#### 4.3.1 View Structure

```
app/Views/
├── layouts/
│   ├── main.php              # Main public layout
│   ├── admin.php             # Admin panel layout
│   └── partials/
│       ├── header.php
│       ├── footer.php
│       ├── nav.php
│       └── sidebar.php
├── components/
│   ├── article_card.php
│   ├── category_list.php
│   ├── comment_form.php
│   ├── pagination.php
│   └── search_box.php
├── news/
│   ├── index.php
│   ├── show.php
│   ├── category.php
│   └── search_results.php
├── home/
│   └── index.php
├── auth/
│   ├── login.php
│   ├── register.php
│   └── forgot_password.php
├── admin/
│   ├── dashboard/
│   │   └── index.php
│   ├── articles/
│   │   ├── index.php
│   │   ├── create.php
│   │   └── edit.php
│   ├── categories/
│   │   ├── index.php
│   │   └── form.php
│   ├── users/
│   │   ├── index.php
│   │   └── form.php
│   ├── comments/
│   │   └── index.php
│   ├── media/
│   │   └── index.php
│   └── settings/
│       └── index.php
├── errors/
│   ├── 404.php
│   └── 500.php
└── emails/
    ├── welcome.php
    └── password_reset.php
```

#### 4.3.2 Layout Template Example

```php
<!-- app/Views/layouts/main.php -->
<!DOCTYPE html>
<html lang="<?= $lang ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Hind Bihar</title>
    <meta name="description" content="<?= $this->renderSection('meta_description') ?>">
    <link rel="stylesheet" href="/assets/css/app.css">
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <?= $this->include('layouts/partials/header') ?>
    <?= $this->include('layouts/partials/nav') ?>
    
    <main class="container">
        <?= $this->renderSection('content') ?>
    </main>
    
    <?= $this->include('layouts/partials/footer') ?>
    
    <script src="/assets/js/app.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
```

---

## 5. Directory Structure

### 5.1 Full CodeIgniter Project Structure

```
hind-bihar/
├── app/
│   ├── Config/
│   │   ├── App.php
│   │   ├── Autoload.php
│   │   ├── Database.php
│   │   ├── Filters.php
│   │   ├── Routes.php
│   │   └── Services.php
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── Home.php
│   │   ├── News.php
│   │   ├── Category.php
│   │   ├── Search.php
│   │   ├── Auth.php
│   │   ├── Feed.php
│   │   ├── Admin/
│   │   │   ├── Dashboard.php
│   │   │   ├── Articles.php
│   │   │   ├── Categories.php
│   │   │   ├── Users.php
│   │   │   ├── Comments.php
│   │   │   ├── Media.php
│   │   │   └── Settings.php
│   │   └── Api/
│   │       └── News.php
│   ├── Database/
│   │   ├── Migrations/
│   │   │   ├── 2026-06-01-000001_CreateUsersTable.php
│   │   │   ├── 2026-06-01-000002_CreateCategoriesTable.php
│   │   │   ├── 2026-06-01-000003_CreateTagsTable.php
│   │   │   ├── 2026-06-01-000004_CreateMediaTable.php
│   │   │   ├── 2026-06-01-000005_CreateArticlesTable.php
│   │   │   ├── 2026-06-01-000006_CreateArticleTagsTable.php
│   │   │   ├── 2026-06-01-000007_CreateCommentsTable.php
│   │   │   └── 2026-06-01-000008_CreateSettingsTable.php
│   │   └── Seeds/
│   │       ├── UserSeeder.php
│   │       ├── CategorySeeder.php
│   │       └── SettingsSeeder.php
│   ├── Filters/
│   │   ├── AuthFilter.php
│   │   ├── AdminFilter.php
│   │   ├── LanguageFilter.php
│   │   └── ThrottleFilter.php
│   ├── Helpers/
│   │   ├── seo_helper.php
│   │   ├── media_helper.php
│   │   └── language_helper.php
│   ├── Language/
│   │   ├── en/
│   │   │   ├── App.php
│   │   │   ├── Auth.php
│   │   │   ├── News.php
│   │   │   └── Admin.php
│   │   └── hi/
│   │       ├── App.php
│   │       ├── Auth.php
│   │       ├── News.php
│   │       └── Admin.php
│   ├── Libraries/
│   │   ├── MediaUploader.php
│   │   ├── SlugGenerator.php
│   │   └── SitemapGenerator.php
│   ├── Models/
│   │   ├── ArticleModel.php
│   │   ├── CategoryModel.php
│   │   ├── CommentModel.php
│   │   ├── MediaModel.php
│   │   ├── SettingModel.php
│   │   ├── TagModel.php
│   │   └── UserModel.php
│   ├── Validation/
│   │   └── CustomRules.php
│   └── Views/
│       └── [see View Structure above]
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── robots.txt
│   ├── favicon.ico
│   └── assets/
│       ├── css/
│       │   ├── app.css
│       │   └── admin.css
│       ├── js/
│       │   ├── app.js
│       │   └── admin.js
│       └── images/
│           └── logo.png
├── writable/
│   ├── cache/
│   ├── logs/
│   ├── session/
│   └── uploads/
│       ├── images/
│       ├── thumbnails/
│       └── videos/
├── tests/
│   ├── unit/
│   └── integration/
├── vendor/
├── .env
├── .gitignore
├── composer.json
├── phpunit.xml
└── spark
```

---

## 6. Security Design

### 6.1 Authentication Security

```php
// Password hashing
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Password verification
if (password_verify($inputPassword, $storedHash)) {
    // Valid password
}
```

### 6.2 CSRF Protection

CodeIgniter 4 provides built-in CSRF protection:

```php
// app/Config/Filters.php
public array $globals = [
    'before' => [
        'csrf',
    ],
];

// In forms
<?= csrf_field() ?>
```

### 6.3 XSS Filtering

```php
// Output escaping in views
<?= esc($userInput) ?>

// HTML content (when trusted)
<?= esc($content, 'raw') ?>
```

### 6.4 Input Validation

```php
// Controller validation
$rules = [
    'title_en' => 'required|min_length[3]|max_length[500]',
    'content_en' => 'required|min_length[50]',
    'category_id' => 'required|is_natural_no_zero',
];

if (!$this->validate($rules)) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}
```

### 6.5 SQL Injection Prevention

```php
// Using Query Builder (safe)
$this->db->table('articles')
         ->where('id', $id)
         ->get();

// Using parameterized queries
$this->db->query('SELECT * FROM articles WHERE id = ?', [$id]);
```

### 6.6 File Upload Security

```php
// Media upload validation
$rules = [
    'file' => [
        'uploaded[file]',
        'max_size[file,10240]', // 10MB
        'mime_in[file,image/png,image/jpeg,image/gif]',
        'ext_in[file,png,jpg,jpeg,gif]',
    ],
];
```

---

## 7. API Design

### 7.1 REST API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/articles | List published articles |
| GET | /api/articles/{id} | Get single article |
| GET | /api/categories | List all categories |
| GET | /api/categories/{slug}/articles | Articles by category |
| GET | /api/search | Search articles |

### 7.2 API Response Format

```json
{
    "status": "success",
    "data": {
        "articles": [...],
        "pagination": {
            "current_page": 1,
            "total_pages": 10,
            "total_items": 100,
            "per_page": 10
        }
    },
    "message": null
}
```

### 7.3 Error Response Format

```json
{
    "status": "error",
    "data": null,
    "message": "Article not found",
    "code": 404
}
```

---

## 8. Routing Design

### 8.1 URL Routing Scheme

```php
// app/Config/Routes.php

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');

// Language-prefixed routes
$routes->group('(:segment)', ['filter' => 'language'], function ($routes) {
    // Home
    $routes->get('/', 'Home::index');
    
    // News articles
    $routes->get('news', 'News::index');
    $routes->get('news/(:segment)', 'News::show/$1');
    
    // Categories
    $routes->get('category/(:segment)', 'Category::show/$1');
    
    // Geographic sections
    $routes->get('international', 'News::section/international');
    $routes->get('national', 'News::section/national');
    $routes->get('bihar', 'News::section/bihar');
    
    // Search
    $routes->get('search', 'Search::index');
    
    // Tags
    $routes->get('tag/(:segment)', 'Tag::show/$1');
    
    // Author
    $routes->get('author/(:segment)', 'Author::show/$1');
});

// Authentication routes
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attemptLogin');
    $routes->get('logout', 'Auth::logout');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::attemptRegister');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('forgot-password', 'Auth::sendResetLink');
    $routes->get('reset-password/(:segment)', 'Auth::resetPassword/$1');
    $routes->post('reset-password', 'Auth::attemptReset');
});

// Admin routes
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    
    // Articles
    $routes->get('articles', 'Admin\Articles::index');
    $routes->get('articles/create', 'Admin\Articles::create');
    $routes->post('articles', 'Admin\Articles::store');
    $routes->get('articles/edit/(:num)', 'Admin\Articles::edit/$1');
    $routes->put('articles/(:num)', 'Admin\Articles::update/$1');
    $routes->delete('articles/(:num)', 'Admin\Articles::delete/$1');
    
    // Categories
    $routes->get('categories', 'Admin\Categories::index');
    $routes->get('categories/create', 'Admin\Categories::create');
    $routes->post('categories', 'Admin\Categories::store');
    $routes->get('categories/edit/(:num)', 'Admin\Categories::edit/$1');
    $routes->put('categories/(:num)', 'Admin\Categories::update/$1');
    $routes->delete('categories/(:num)', 'Admin\Categories::delete/$1');
    
    // Users
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/create', 'Admin\Users::create');
    $routes->post('users', 'Admin\Users::store');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->put('users/(:num)', 'Admin\Users::update/$1');
    $routes->delete('users/(:num)', 'Admin\Users::delete/$1');
    
    // Comments
    $routes->get('comments', 'Admin\Comments::index');
    $routes->put('comments/(:num)/approve', 'Admin\Comments::approve/$1');
    $routes->put('comments/(:num)/reject', 'Admin\Comments::reject/$1');
    $routes->delete('comments/(:num)', 'Admin\Comments::delete/$1');
    
    // Media
    $routes->get('media', 'Admin\Media::index');
    $routes->post('media/upload', 'Admin\Media::upload');
    $routes->delete('media/(:num)', 'Admin\Media::delete/$1');
    
    // Settings
    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings', 'Admin\Settings::update');
});

// API routes
$routes->group('api', function ($routes) {
    $routes->get('articles', 'Api\News::index');
    $routes->get('articles/(:num)', 'Api\News::show/$1');
    $routes->get('categories', 'Api\News::categories');
    $routes->get('search', 'Api\News::search');
});

// RSS Feeds
$routes->get('feed', 'Feed::index');
$routes->get('feed/category/(:segment)', 'Feed::category/$1');
$routes->get('feed/(:segment)', 'Feed::language/$1');

// Sitemap
$routes->get('sitemap.xml', 'Sitemap::index');
```

### 8.2 URL Examples

| URL | Description |
|-----|-------------|
| `/en` | English homepage |
| `/hi` | Hindi homepage |
| `/en/news/article-title-slug` | English article |
| `/hi/news/लेख-शीर्षक-स्लग` | Hindi article |
| `/en/category/politics` | English politics category |
| `/hi/international` | Hindi international news |
| `/en/search?q=keyword` | Search results |
| `/admin/articles` | Admin article list |
| `/api/articles` | API article list |

---

**Document Revision History**

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | June 2026 | Hind Bihar Team | Initial document |

---

*End of Software Design Document*
