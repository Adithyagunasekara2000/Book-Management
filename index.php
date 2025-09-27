<?php

require_once 'config.php';

$books = [];
$message = '';
$error = '';
$editBook = null;
$searchTerm = '';
$searchField = 'title';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $result = createBook($_POST);
                if ($result['success']) {
                    $message = "Book added successfully!";
                } else {
                    $error = $result['error'];
                }
                break;
                
            case 'update':
                $result = updateBook($_POST);
                if ($result['success']) {
                    $message = "Book updated successfully!";
                } else {
                    $error = $result['error'];
                }
                break;
                
            case 'delete':
                $result = deleteBook($_POST['id']);
                if ($result['success']) {
                    $message = "Book deleted successfully!";
                } else {
                    $error = $result['error'];
                }
                break;
                
            case 'search':
                $searchTerm = $_POST['search_term'];
                $searchField = $_POST['search_field'];
                break;
        }
    }
}

if (isset($_GET['edit'])) {
    $editBook = getBookById($_GET['edit']);
}

if (!empty($searchTerm)) {
    $books = searchBooks($searchTerm, $searchField);
} else {
    $books = getAllBooks();
}

function createBook($data) {
    global $pdo;
    
    $errors = validateBookData($data);
    if (!empty($errors)) {
        return ['success' => false, 'error' => implode(', ', $errors)];
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, publication_year, isbn, genre, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['author'],
            (int)$data['publication_year'],
            $data['isbn'],
            $data['genre'],
            $data['description']
        ]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function updateBook($data) {
    global $pdo;
    
    $errors = validateBookData($data);
    if (!empty($errors)) {
        return ['success' => false, 'error' => implode(', ', $errors)];
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, publication_year=?, isbn=?, genre=?, description=? WHERE id=?");
        $stmt->execute([
            $data['title'],
            $data['author'],
            (int)$data['publication_year'],
            $data['isbn'],
            $data['genre'],
            $data['description'],
            (int)$data['id']
        ]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function deleteBook($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([(int)$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function getAllBooks() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM books ORDER BY title ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getBookById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

function searchBooks($searchTerm, $searchField) {
    global $pdo;
    
    $allowedFields = ['title', 'author', 'publication_year', 'genre'];
    if (!in_array($searchField, $allowedFields)) {
        $searchField = 'title';
    }
    
    try {
        if ($searchField == 'publication_year') {
            $stmt = $pdo->prepare("SELECT * FROM books WHERE $searchField = ? ORDER BY title ASC");
            $stmt->execute([(int)$searchTerm]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM books WHERE $searchField LIKE ? ORDER BY title ASC");
            $stmt->execute(['%' . $searchTerm . '%']);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function validateBookData($data) {
    $errors = [];
    
    if (empty($data['title'])) {
        $errors[] = "Title is required";
    } elseif (strlen($data['title']) > 255) {
        $errors[] = "Title must be less than 255 characters";
    }
    
    if (empty($data['author'])) {
        $errors[] = "Author is required";
    } elseif (strlen($data['author']) > 255) {
        $errors[] = "Author must be less than 255 characters";
    }
    
    if (empty($data['publication_year'])) {
        $errors[] = "Publication year is required";
    } elseif (!is_numeric($data['publication_year'])) {
        $errors[] = "Publication year must be a number";
    } elseif ($data['publication_year'] < 1000 || $data['publication_year'] > date('Y')) {
        $errors[] = "Publication year must be between 1000 and " . date('Y');
    }
    
    if (!empty($data['isbn']) && strlen($data['isbn']) > 20) {
        $errors[] = "ISBN must be less than 20 characters";
    }
    
    if (!empty($data['genre']) && strlen($data['genre']) > 100) {
        $errors[] = "Genre must be less than 100 characters";
    }
    
    return $errors;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        
        h2 {
            color: #555;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
        }
        
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        textarea {
            height: 80px;
            resize: vertical;
        }
        
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .actions {
            white-space: nowrap;
        }
        
        .search-section {
            background-color: #e9ecef;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
            align-items: end;
        }
        
        .search-form > div {
            flex: 1;
        }
        
        .search-form button {
            margin: 0;
        }
        
        .no-books {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Book Management System</h1>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="search-section">
            <h2>🔍 Search Books</h2>
            <form method="POST" class="search-form">
                <input type="hidden" name="action" value="search">
                <div>
                    <label for="search_term">Search Term:</label>
                    <input type="text" id="search_term" name="search_term" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Enter search term...">
                </div>
                <div>
                    <label for="search_field">Search By:</label>
                    <select id="search_field" name="search_field">
                        <option value="title" <?php echo $searchField == 'title' ? 'selected' : ''; ?>>Title</option>
                        <option value="author" <?php echo $searchField == 'author' ? 'selected' : ''; ?>>Author</option>
                        <option value="publication_year" <?php echo $searchField == 'publication_year' ? 'selected' : ''; ?>>Publication Year</option>
                        <option value="genre" <?php echo $searchField == 'genre' ? 'selected' : ''; ?>>Genre</option>
                    </select>
                </div>
                <div>
                    <button type="submit">Search</button>
                    <a href="index.php"><button type="button" class="btn-secondary">Clear</button></a>
                </div>
            </form>
        </div>
        
        <div class="form-section">
            <h2><?php echo $editBook ? '✏️ Edit Book' : '➕ Add New Book'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editBook ? 'update' : 'create'; ?>">
                <?php if ($editBook): ?>
                    <input type="hidden" name="id" value="<?php echo $editBook['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo $editBook ? htmlspecialchars($editBook['title']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Author *</label>
                        <input type="text" id="author" name="author" required 
                               value="<?php echo $editBook ? htmlspecialchars($editBook['author']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="publication_year">Publication Year *</label>
                        <input type="number" id="publication_year" name="publication_year" required min="1000" max="<?php echo date('Y'); ?>"
                               value="<?php echo $editBook ? $editBook['publication_year'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" id="isbn" name="isbn" 
                               value="<?php echo $editBook ? htmlspecialchars($editBook['isbn']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <input type="text" id="genre" name="genre" 
                               value="<?php echo $editBook ? htmlspecialchars($editBook['genre']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo $editBook ? htmlspecialchars($editBook['description']) : ''; ?></textarea>
                </div>
                
                <button type="submit"><?php echo $editBook ? 'Update Book' : 'Add Book'; ?></button>
                <?php if ($editBook): ?>
                    <a href="index.php"><button type="button" class="btn-secondary">Cancel</button></a>
                <?php endif; ?>
            </form>
        </div>
        
        <div>
            <h2>📖 Books Library <?php if (!empty($searchTerm)): ?>(Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"<?php endif; ?></h2>
            
            <?php if (empty($books)): ?>
                <div class="no-books">
                    <?php if (!empty($searchTerm)): ?>
                        No books found matching your search criteria.
                    <?php else: ?>
                        No books available. Add your first book using the form above!
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>ISBN</th>
                            <th>Genre</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($book['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo $book['publication_year']; ?></td>
                            <td><?php echo htmlspecialchars($book['isbn'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($book['genre'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($book['description'] ? (strlen($book['description']) > 100 ? substr($book['description'], 0, 100) . '...' : $book['description']) : '-'); ?></td>
                            <td class="actions">
                                <a href="?edit=<?php echo $book['id']; ?>">
                                    <button class="btn-warning" type="button">Edit</button>
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #6c757d;">
            <p>Total Books: <strong><?php echo count($books); ?></strong> | Book Management System v1.0</p>
        </div>
    </div>
</body>
</html>