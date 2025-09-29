<?php
require_once '../includes/config.php';
global $path_prefix;

$cart = new Cart();
$message = '';

// --- Handle Cart Actions (Add, Remove, Update) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $book_id = (int)($_POST['book_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    switch ($action) {
        case 'add':
            if ($cart->addItem($book_id, $quantity)) {
                $message = 'Item added to cart!';
            } else {
                $message = 'Error: Could not add item to cart.';
            }
            break;
        case 'update':
            $cart->updateQuantity($book_id, $quantity);
            $message = 'Cart updated.';
            break;
        case 'remove':
            $cart->removeItem($book_id);
            $message = 'Item removed from cart.';
            break;
        case 'clear':
            $cart->clearCart();
            $message = 'Your cart has been cleared.';
            break;
    }
    // Prevent form resubmission on refresh
    header("Location: " . $path_prefix . "pages/cart.php");
    exit();
}

$cart_items = $cart->getItems();
$cart_total = $cart->getTotal();

require_once '../includes/header.php';
?>

<section class="shopping-cart" style="padding: 3rem 0;">
    <h1 style="font-size: 2.5rem; text-align: center; margin-bottom: 2rem; color: var(--primary-color);">
        <i class="fas fa-shopping-cart"></i> Your Shopping Cart (<?= $cart->getCount() ?> Items)
    </h1>

    <?php if (!empty($message)): ?>
        <div style="background-color: var(--secondary-light); color: var(--secondary-color); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; text-align: center;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart" style="text-align: center; padding: 4rem 0; border: 1px dashed var(--border-color); border-radius: 8px;">
            <p style="font-size: 1.2rem; margin-bottom: 1rem;">Your cart is empty!</p>
            <a href="<?= $path_prefix ?>pages/books.php" class="btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>

    <div class="cart-layout" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        
        <!-- Cart Items List (responsive table/grid) -->
        <div class="cart-items-list">
            <?php foreach ($cart_items as $book_id => $item): ?>
                <?php $book = $item['book']; ?>
                <div class="cart-item-card" style="display: flex; gap: 1rem; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
                    
                    <img src="<?= $path_prefix . htmlspecialchars($book['image_url']) ?>" 
                         alt="<?= htmlspecialchars($book['title']) ?>" 
                         style="width: 80px; height: 100px; object-fit: cover; border-radius: 4px;"
                         onerror="this.onerror=null;this.src='<?= $path_prefix ?>assets/images/placeholder.jpg';"
                    >
                    
                    <div style="flex-grow: 1;">
                        <h4 style="font-size: 1.1rem; margin: 0;"><?= htmlspecialchars($book['title']) ?></h4>
                        <p style="color: var(--text-muted); margin: 0.25rem 0;">By: <?= htmlspecialchars($book['author']) ?></p>
                        <p style="font-weight: bold; color: var(--secondary-color);">
                            $<?= number_format($book['price'], 2) ?>
                        </p>
                    </div>

                    <form action="cart.php" method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="book_id" value="<?= $book_id ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $book['stock_quantity'] ?>" required 
                               style="width: 60px; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px; text-align: center;"
                        >
                        <button type="submit" class="btn-sm" style="background-color: #3b82f6; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 4px;"><i class="fas fa-sync-alt"></i></button>
                    </form>

                    <p style="font-weight: bold; width: 100px; text-align: right;">
                        $<?= number_format($book['price'] * $item['quantity'], 2) ?>
                    </p>
                    
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="book_id" value="<?= $book_id ?>">
                        <button type="submit" class="btn-sm" style="background-color: #ef4444; color: white; border: none; padding: 0.5rem; border-radius: 50%;"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Cart Summary and Actions -->
        <div class="cart-summary" style="padding: 1.5rem; background-color: var(--bg-light); border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">
            <h2 style="font-size: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">Order Summary</h2>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <p>Subtotal (<?= $cart->getCount() ?> items):</p>
                <p>$<?= number_format($cart_total, 2) ?></p>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-weight: bold; font-size: 1.2rem;">
                <p>Order Total:</p>
                <p style="color: var(--primary-color);">$<?= number_format($cart_total, 2) ?></p>
            </div>
            
            <a href="checkout.php" class="btn-primary" style="width: 100%; text-align: center; padding: 1rem; background-color: var(--secondary-color);">Proceed to Checkout</a>

            <form action="cart.php" method="POST" style="margin-top: 1rem;">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="btn-secondary" style="width: 100%; text-align: center; padding: 0.75rem; background-color: var(--text-muted); color: var(--bg-white);">Clear Cart</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
require_once '../includes/footer.php';
?>