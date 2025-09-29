<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();
requireAdmin();

$pageTitle = 'Manage Books - Admin';
$book = new Book();
$errors = [];

// Handle book actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_book'])) {
        $data = [
            'title' => sanitize($_POST['title']),
            'author' => sanitize($_POST['author']),
            'isbn' => sanitize($_POST['isbn']),
            'description' => sanitize($_POST['description']),
            'price' => floatval($_POST['price']),
            'category' => sanitize($_POST['category']),
            'stock' => intval($_POST['stock']),
            'publisher' => sanitize($_POST['publisher']),
            'publication_year' => intval($_POST['publication_year'])
        ];
        
        if ($book->addBook($data)) {
            setFlashMessage('Book added successfully', 'success');
            redirect('/admin/books.php');
        } else {
            $errors[] = 'Failed to add book';
        }
    }
    
    if (isset($_POST['update_book'])) {
        $id = intval($_POST['book_id']);
        $bookData = $book->getBookById($id);
        
        $data = [
            'title' => sanitize($_POST['title']),
            'author' => sanitize($_POST['author']),
            'isbn' => sanitize($_POST['isbn']),
            'description' => sanitize($_POST['description']),
            'price' => floatval($_POST['price']),
            'category' => sanitize($_POST['category']),
            'stock' => intval($_POST['stock']),
            'image' => $bookData['image'],
            'publisher' => sanitize($_POST['publisher']),
            'publication_year' => intval($_POST['publication_year'])
        ];
        
        if ($book->updateBook($id, $data)) {
            setFlashMessage('Book updated successfully', 'success');
            redirect('/admin/books.php');
        } else {
            $errors[] = 'Failed to update book';
        }
    }
    
    if (isset($_POST['delete_book'])) {
        $id = intval($_POST['book_id']);
        if ($book->deleteBook($id)) {
            setFlashMessage('Book deleted successfully', 'success');
            redirect('/admin/books.php');
        } else {
            $errors[] = 'Failed to delete book';
        }
    }
}

$books = $book->getAllBooks();
$editBook = null;
if (isset($_GET['edit'])) {
    $editBook = $book->getBookById($_GET['edit']);
}

include '../includes/header.php';
?>

<div class="admin-page">
    <h2>Manage Books</h2>
    
    <div class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="books.php" class="active">Manage Books</a>
        <a href="orders.php">Manage Orders</a>
        <a href="users.php">Manage Users</a>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <div class="admin-content">
        <div class="form-section">
            <h3><?php echo $editBook ? 'Edit Book' : 'Add New Book'; ?></h3>
            <form method="POST" action="">
                <?php if ($editBook): ?>
                    <input type="hidden" name="book_id" value="<?php echo $editBook['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" value="<?php echo $editBook['title'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Author *</label>
                        <input type="text" name="author" value="<?php echo $editBook['author'] ?? ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn" value="<?php echo $editBook['isbn'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <input type="text" name="category" value="<?php echo $editBook['category'] ?? ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price *</label>
                        <input type="number" name="price" step="0.01" value="<?php echo $editBook['price'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Stock *</label>
                        <input type="number" name="stock" value="<?php echo $editBook['stock'] ?? '0'; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Publisher</label>
                        <input type="text" name="publisher" value="<?php echo $editBook['publisher'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Publication Year</label>
                        <input type="number" name="publication_year" value="<?php echo $editBook['publication_year'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo $editBook['description'] ?? ''; ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="<?php echo $editBook ? 'update_book' : 'add_book'; ?>" class="btn btn-primary">
                        <?php echo $editBook ? 'Update Book' : 'Add Book'; ?>
                    </button>
                    <?php if ($editBook): ?>
                        <a href="books.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="table-section">
            <h3>All Books</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $bookItem): ?>
                    <tr>
                        <td><?php echo $bookItem['id']; ?></td>
                        <td><?php echo htmlspecialchars($bookItem['title']); ?></td>
                        <td><?php echo htmlspecialchars($bookItem['author']); ?></td>
                        <td><?php echo htmlspecialchars($bookItem['category']); ?></td>
                        <td><?php echo formatPrice($bookItem['price']); ?></td>
                        <td><?php echo $bookItem['stock']; ?></td>
                        <td class="actions">
                            <a href="?edit=<?php echo $bookItem['id']; ?>" class="btn btn-sm">Edit</a>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="book_id" value="<?php echo $bookItem['id']; ?>">
                                <button type="submit" name="delete_book" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>