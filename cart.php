<?php
/**
 * Cart Class handles session-based shopping cart operations.
 */
class Cart {
    private $book_model;

    public function __construct() {
        // Initialize the cart in the session if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // Array format: ['book_id' => ['book' => [], 'quantity' => 0]]
        }
        $this->book_model = new Book();
    }

    /**
     * Adds an item to the cart or increments its quantity.
     * @param int $book_id
     * @param int $quantity
     * @return bool
     */
    public function addItem(int $book_id, int $quantity = 1): bool {
        $book = $this->book_model->getById($book_id);

        if (!$book) {
            return false; // Book not found
        }

        if (isset($_SESSION['cart'][$book_id])) {
            // Item exists, just update quantity
            $_SESSION['cart'][$book_id]['quantity'] += $quantity;
        } else {
            // New item
            $_SESSION['cart'][$book_id] = [
                'book' => $book,
                'quantity' => $quantity,
            ];
        }
        return true;
    }

    /**
     * Updates the quantity of a specific cart item.
     * @param int $book_id
     * @param int $quantity
     */
    public function updateQuantity(int $book_id, int $quantity): void {
        if ($quantity > 0 && isset($_SESSION['cart'][$book_id])) {
            $_SESSION['cart'][$book_id]['quantity'] = $quantity;
        } elseif ($quantity <= 0 && isset($_SESSION['cart'][$book_id])) {
            $this->removeItem($book_id);
        }
    }

    /**
     * Removes an item completely from the cart.
     * @param int $book_id
     */
    public function removeItem(int $book_id): void {
        if (isset($_SESSION['cart'][$book_id])) {
            unset($_SESSION['cart'][$book_id]);
        }
    }

    /**
     * Empties the entire cart.
     */
    public function clearCart(): void {
        $_SESSION['cart'] = [];
    }

    /**
     * Gets all items currently in the cart.
     * @return array
     */
    public function getItems(): array {
        return $_SESSION['cart'];
    }

    /**
     * Calculates the total price of all items in the cart.
     * @return float
     */
    public function getTotal(): float {
        $total = 0.0;
        foreach ($this->getItems() as $item) {
            $total += ($item['book']['price'] * $item['quantity']);
        }
        return $total;
    }

    /**
     * Gets the number of unique items in the cart.
     * @return int
     */
    public function getCount(): int {
        return count($this->getItems());
    }
}