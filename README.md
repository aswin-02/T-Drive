# 🚀 T-Drive: Google Drive-Like File Management System

<p align="center">
    <strong>A powerful, feature-rich file management and storage application built with Laravel 12</strong>
</p>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Database Schema](#database-schema)
- [Authentication & Authorization](#authentication--authorization)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 Overview

**T-Drive** is a cloud storage and file management system inspired by Google Drive. It provides users with a complete suite of tools to upload, organize, share, and manage files with granular permission controls. Built with modern Laravel frameworks and best practices, it ensures security, performance, and scalability.

### Key Highlights:
- 📁 Organized folder and file management
- 🔗 Advanced file sharing with permission control
- 🗑️ Soft delete with restore functionality  
- 🔐 Role-based access control (RBAC)
- 📱 Responsive, modern UI
- 🎯 Drag-and-drop file uploads
- 🔍 Full-text search capabilities
- 📊 Activity logging and audit trail

---

## ✨ Features

### 1. **File Management**
- ✅ Upload files of various types (images, documents, archives, etc.)
- ✅ Organize files into folders with hierarchical structure
- ✅ Smart file type validation and size restrictions
- ✅ Rename files and folders
- ✅ Preview files (PDFs, images, documents)
- ✅ Download files with proper MIME types

### 2. **Storage & Trash**
- ✅ Soft delete functionality - move files to trash without permanent deletion
- ✅ Restore trashed files with one click
- ✅ Force delete for permanent removal
- ✅ Track storage usage per user
- ✅ Configurable retention policies

### 3. **File Sharing & Permissions**
- ✅ Share files and folders with specific users
- ✅ Control sharing permissions (view-only, download, edit)
- ✅ Generate public share links with unique tokens
- ✅ View list of shared files and their status
- ✅ Revoke access anytime
- ✅ Track shared file access

### 4. **User Management**
- ✅ Dual authentication: Admin & Regular Users
- ✅ User registration and sign-in
- ✅ Two-factor authentication (2FA) support
- ✅ User profile management
- ✅ Referral system with invite codes
- ✅ Bank details and payment information storage

### 5. **Search & Discovery**
- ✅ Full-text search across all files
- ✅ Search suggestions
- ✅ Filter by file type, date modified, owner
- ✅ Recent files tracking
- ✅ "Shared with me" view
- ✅ Advanced search filters

### 6. **Admin Dashboard**
- ✅ User management
- ✅ Role and permission management
- ✅ Action logging and audit trail
- ✅ System activity monitoring
- ✅ User approval workflows

### 7. **Additional Features**
- ✅ Drag-and-drop uploads with real-time progress
- ✅ Beautiful glassmorphism UI
- ✅ Activity action logs (who did what and when)
- ✅ Recent view tracking
- ✅ Multi-file operations
- ✅ Progressive Web App (PWA) support

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12 with PHP 8.2+
- **Database**: MySQL/PostgreSQL (via Laravel ORM)
- **Authentication**: Laravel Auth with custom logic
- **Authorization**: Spatie Laravel Permission (RBAC)
- **Data Tables**: Yajra Laravel DataTables
- **Task Queue**: Laravel Queue system
- **Real-time**: Pusher for broadcasting events

### Frontend
- **Build**: Vite
- **CSS Framework**: Tailwind CSS (or similar)
- **JavaScript**: Vanilla JS + Vite modules
- **Package Manager**: npm

### Development & Testing
- **Testing**: Pest PHP
- **Code Quality**: Laravel Pint
- **Local Development**: Laravel Sail (Docker)
- **Faker**: FakerPHP for seeding

---

## 📦 System Requirements

- **PHP**: 8.2 or higher
- **MySQL/PostgreSQL**: 5.7 or higher
- **Node.js**: 18 or higher
- **Composer**: Latest version
- **npm**: Latest version

---

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd t-drive
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=t_drive
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations
```bash
php artisan migrate --seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Start Development Server
```bash
php artisan serve
npm run dev
```

Visit `http://localhost:8000` to access the application.

### Quick Setup Command
```bash
composer run setup
```

---

## ⚙️ Configuration

### Key Configuration Files

#### `.env` Variables
```env
# Application
APP_NAME="T-Drive"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=t_drive

# Mail
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465

# Authentication
AUTH_GUARD=web

# File Storage
FILESYSTEM_DISK=local
```

#### File Upload Settings
Configure in `config/filesystems.php`:
- Maximum file size limits
- Supported file types
- Storage paths

#### Permissions
Managed through Spatie Permission in `config/permission.php`:
- Define roles (admin, user)
- Define permissions for each role

---

## 📚 Usage

### User Features

#### 1. File Upload
- Drag and drop files onto dashboard
- Click to browse and select files
- Multiple file uploads supported

#### 2. Folder Management
```
Create → Organize → Share → Download
```

#### 3. File Sharing
1. Right-click file → Share
2. Select users or generate public link
3. Set permissions (view, download, edit)
4. Share the link or notify users

#### 4. Search
- Use search bar to find files
- Filter by type, date, owner
- View recent searches

#### 5. Trash Management
- Move files to trash (soft delete)
- Restore from trash
- Permanently delete

### Admin Features

#### User Management
- Approve/reject user registrations
- Manage user roles and permissions
- View user activity logs
- Reset passwords

#### System Monitoring
- View action logs
- Monitor storage usage
- Check system health
- Generate reports

---

## 🔗 API Endpoints

### Authentication
```
GET    /sign-in                    User sign-in page
POST   /sign-in                    Process sign-in
GET    /sign-up                    User registration page
POST   /sign-up                    Process registration
POST   /logout                     User logout
```

### Files
```
POST   /files/upload               Upload file
GET    /files/{id}/view            View file details
GET    /files/{id}/download        Download file
GET    /files/{id}/preview-pdf     Preview as PDF
DELETE /files/{id}                 Move to trash
POST   /files/{id}/restore         Restore file
DELETE /files/{id}/force-delete    Permanent delete
PUT    /files/{id}/rename          Rename file
GET    /files/trash/list           List trashed files
```

### Folders
```
GET    /folders                    List all folders
GET    /folders/{id}               View folder
POST   /folders/create             Create folder
DELETE /folders/{id}               Delete folder
PUT    /folders/{id}/rename        Rename folder
POST   /folders/{id}/restore       Restore folder
```

### Sharing
```
POST   /shares                     Create share
GET    /shares                     List shared items
GET    /shared-with-me             Files shared with user
DELETE /shares/{id}                Revoke share
GET    /s/{token}                  View public share
GET    /s/{token}/download         Download public share
```

### Search & Discovery
```
GET    /search                     Search files
GET    /search/suggest             Search suggestions
GET    /recent                     Recent files
GET    /trash                      Trash contents
```

### Admin
```
GET    /roles                      Manage roles
GET    /users                      Manage users
GET    /admin/logs                 View action logs
```

---

## 📊 Database Schema

### Core Tables

#### `users`
- User authentication and profile information
- Tracks user type (admin/regular)
- Stores personal and bank details
- Soft deletable

#### `files`
- File metadata and storage information
- Belongs to users and folders
- Includes mime type, size, path
- Soft deletable with restore capability

#### `folders`
- Hierarchical folder structure
- Belongs to users
- Parent-child relationships
- Soft deletable

#### `shares`
- File and folder sharing records
- Links files to users with permissions
- Tracks share creation and access

#### `share_users`
- Individual user permissions for shared items
- Defines what permissions each user has

#### `action_logs`
- Audit trail of all system activities
- Tracks who did what and when
- Helps with compliance and debugging

#### `recent_views`
- Tracks recently viewed files
- Improves user experience with recent file listings

---

## 🔐 Authentication & Authorization

### Authentication Methods
- **Email/Password**: Standard Laravel authentication
- **Two-Factor Authentication**: SMS/Email verification
- **Guest Access**: Public share links don't require authentication

### Authorization Levels

#### Admin
- Full system access
- User management
- Role and permission management
- View all activity logs

#### User
- Upload and manage own files
- Share files with specific permissions
- Access shared files from others
- Search and organize content

### Permission Levels for Shared Items
- **View**: Can view file/folder contents
- **Download**: Can download files
- **Edit**: Can modify files (if enabled)
- **Share**: Can share with others (if enabled)

---

## 🧪 Testing

Run tests using Pest:
```bash
php artisan test

# Run specific test file
php artisan test tests/Feature/FileUploadTest.php

# Run with coverage
php artisan test --coverage
```

---

## 📝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 PHP coding standards
- Run `composer run-script lint` to check code quality
- Write tests for new features
- Update documentation

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 👥 Support

For issues and questions:
- Create an issue on GitHub
- Contact the development team
- Check existing documentation

---

## 🙏 Acknowledgments

Built with ❤️ using Laravel 12, modern PHP, and best development practices.

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
