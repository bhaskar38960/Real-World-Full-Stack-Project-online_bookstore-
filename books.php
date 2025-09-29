<?php
// Ensure config is loaded first, which sets up the path prefix
require_once '../includes/config.php';
// Include the header (which will now use the correct paths)
require_once '../includes/header.php';

// Access the global path prefix variable
global $path_prefix;

$db = Database::getInstance();
// Fetch all books for the listing page
$all_books = $db->query("SELECT * FROM books ORDER BY title ASC");
?>

<section class="book-listing" style="padding: 2rem 0;">
    <h1 style="font-size: 2.5rem; margin-bottom: 2rem; text-align: center;">Our Complete Collection</h1>
    
    <div class="book-grid">
        <?php if (!empty($all_books)): ?>
            <?php foreach ($all_books as $book): ?>
                <div class="book-card">
                    <!-- Image URL path fixed using $path_prefix -->
                    <img src="<?= $path_prefix . htmlspecialchars($book['image_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image" onerror="this.onerror=null;this.src='https://placehold.co/280x300/e5e7eb/6b7280?text=Book+Cover'">
                    <div class="book-content">
                        <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                        <p class="book-author">By: <?= htmlspecialchars($book['author']) ?></p>
                        <p class="book-price">$<?= number_format($book['price'], 2) ?></p>
                        <p style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 0.5rem;">Stock: <?= $book['stock_quantity'] > 0 ? $book['stock_quantity'] : 'Out of Stock' ?></p>
                        
                        <!-- Link to detail page and simple Add to Cart (placeholder) -->
                        <a href="book-detail.php?id=<?= $book['id'] ?>" class="btn-primary" style="margin-right: 0.5rem;">Details</a>
                        <button class="btn-primary" style="background-color: #f59e0b; border: none; cursor: pointer;">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="grid-column: 1 / -1; text-align: center;">We are currently restocking! No books found.</p>
        <?php endif; ?>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>