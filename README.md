<img width="1871" height="880" alt="Screenshot 2025-09-27 183522" src="https://github.com/user-attachments/assets/033cd6d5-5933-491d-a950-224ee8a4f5cf" /># Book-Management
# 📚 Book Management System

A comprehensive CRUD (Create, Read, Update, Delete) application built with PHP and MySQL for managing a personal or organizational book library.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

## ✨ Features

### 🔧 Core CRUD Operations
- **Create**: Add new books with detailed information
- **Read**: View all books in an organized table format
- **Update**: Edit existing book details with pre-populated forms
- **Delete**: Remove books with confirmation dialogs

### 🔍 Advanced Search
- Search by **Title**, **Author**, **Publication Year**, or **Genre**
- Partial text matching for flexible searches
- Clear search functionality to reset filters

### 🛡️ Data Validation & Security
- Comprehensive input validation for all fields
- Required field enforcement
- Data type validation (numeric years, length limits)
- XSS protection with input sanitization
- SQL injection prevention using prepared statements

### 🎨 User Interface
- Clean, professional design
- Intuitive navigation and form layouts
- Success/error message notifications
- Responsive table display
- Confirmation dialogs for destructive actions

## 🏗️ Database Schema

```sql
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    publication_year INT NOT NULL,
    isbn VARCHAR(20),
    genre VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or local development environment
- PDO MySQL extension enabled

### Recommended Development Environment
- **XAMPP** (Windows/macOS/Linux)
- **WAMP** (Windows)
- **MAMP** (macOS)
- **LAMP** (Linux)


## 🖼️ Screenshots
<img width="1836" height="898" alt="Screenshot 2025-09-27 183455" src="https://github.com/user-attachments/assets/34598da7-f583-4e99-91e4-976bc4d52167" />

<img width="1871" height="880" alt="Screenshot 2025-09-27 183522" src="https://github.com/user-attachments/assets/92695222-1d5f-4637-a00b-f2c83b30821a" />


### Adding Books
1. Fill out the "Add New Book" form
2. Required fields: Title, Author, Publication Year
3. Optional fields: ISBN, Genre, Description
4. Click "Add Book" to save

### Searching Books
1. Enter search term in the search box
2. Select search field (Title/Author/Year/Genre)
3. Click "Search" to filter results
4. Use "Clear" to reset search

### Editing Books
1. Click "Edit" button next to any book
2. Modify the pre-populated form
3. Click "Update Book" to save changes
4. Click "Cancel" to abort editing

### Deleting Books
1. Click "Delete" button next to any book
2. Confirm deletion in the popup dialog
3. Book will be permanently removed

## 🛠️ Customization

### Adding New Fields
1. Update database schema
2. Modify validation functions
3. Add form inputs in HTML
4. Update CRUD operations

### Styling Changes
Modify the CSS section in `index.php` to customize:
- Colors and themes
- Layout and spacing
- Typography
- Button styles

### Enhanced Features
Potential improvements:
- File upload for book covers
- User authentication system
- Book borrowing/lending tracking
- Export functionality (PDF/Excel)
- Advanced filtering options



---
