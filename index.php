<?php
require_once 'includes/header.php';

// In a real application, you would create a Book class and fetch data using it.
// For this example, we use the Database class directly to show Featured Books.

$db = Database::getInstance();
$featured_books = $db->query("SELECT * FROM books LIMIT 3");
?>

<section class="hero" style="background-color: var(--primary-color); color: var(--bg-white); padding: 4rem 2rem; border-radius: 8px; text-align: center; margin-top: 2rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Welcome to the Digital Library</h1>
    <p style="font-size: 1.25rem; margin-bottom: 1.5rem;">Your destination for the best technical and fictional literature.</p>
    <a href="pages/books.php" class="btn-primary" style="background-color: var(--secondary-color); padding: 0.75rem 1.5rem; font-size: 1rem; font-weight: 600;">Browse All Books</a>
</section>

<section class="featured-books" style="padding: 2rem 0;">
    <h2 style="font-size: 2rem; margin-bottom: 1.5rem; text-align: center;">Featured Reads</h2>
    
    <div class="book-grid">
        <?php if (!empty($featured_books)): ?>
            <?php foreach ($featured_books as $book): ?>
                <div class="book-card">
                    <img src="<?= htmlspecialchars($book['image_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image" onerror="this.onerror=null;this.src='https://placehold.co/280x300/e5e7eb/6b7280?text=Book+Cover'">
                    <div class="book-content">
                        <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                        <p class="book-author">By: <?= htmlspecialchars($book['author']) ?></p>
                        <p class="book-price">$<?= number_format($book['price'], 2) ?></p>
                        <!-- Link to detail page (placeholder) -->
                        <a href="pages/book-detail.php?id=<?= $book['id'] ?>" class="btn-primary">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="grid-column: 1 / -1; text-align: center;">No featured books available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
