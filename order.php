<?php
class Order {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createOrder($userId, $cartItems, $shippingAddress, $paymentMethod) {
        try {
            $this->db->beginTransaction();
            
            // Calculate total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            // Create order
            $this->db->query('INSERT INTO orders (user_id, total_amount, shipping_address, payment_method) 
                             VALUES (:user_id, :total_amount, :shipping_address, :payment_method)');
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':total_amount', $total);
            $this->db->bind(':shipping_address', $shippingAddress);
            $this->db->bind(':payment_method', $paymentMethod);
            $this->db->execute();
            
            $orderId = $this->db->lastInsertId();
            
            // Add order items and update stock
            foreach ($cartItems as $item) {
                // Check stock availability
                $this->db->query('SELECT stock FROM books WHERE id = :book_id');
                $this->db->bind(':book_id', $item['book_id']);
                $book = $this->db->fetch();
                
                if (!$book || $book['stock'] < $item['quantity']) {
                    throw new Exception('Insufficient stock for: ' . $item['title']);
                }
                
                // Insert order item
                $this->db->query('INSERT INTO order_items (order_id, book_id, quantity, price) 
                                 VALUES (:order_id, :book_id, :quantity, :price)');
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':book_id', $item['book_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->execute();
                
                // Update book stock
                $this->db->query('UPDATE books SET stock = stock - :quantity WHERE id = :book_id');
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':book_id', $item['book_id']);
                $this->db->execute();
            }
            
            // Clear cart
            $this->db->query('DELETE FROM cart WHERE user_id = :user_id');
            $this->db->bind(':user_id', $userId);
            $this->db->execute();
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getOrderById($orderId) {
        $this->db->query('SELECT o.*, u.full_name, u.email 
                         FROM orders o 
                         INNER JOIN users u ON o.user_id = u.id 
                         WHERE o.id = :id');
        $this->db->bind(':id', $orderId);
        return $this->db->fetch();
    }

    public function getOrderItems($orderId) {
        $this->db->query('SELECT oi.*, b.title, b.author, b.image 
                         FROM order_items oi 
                         INNER JOIN books b ON oi.book_id = b.id 
                         WHERE oi.order_id = :order_id');
        $this->db->bind(':order_id', $orderId);
        return $this->db->fetchAll();
    }

    public function getUserOrders($userId) {
        $this->db->query('SELECT * FROM orders WHERE user_id = :user_id 
                         ORDER BY created_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->fetchAll();
    }

    public function getAllOrders() {
        $this->db->query('SELECT o.*, u.full_name, u.email 
                         FROM orders o 
                         INNER JOIN users u ON o.user_id = u.id 
                         ORDER BY o.created_at DESC');
        return $this->db->fetchAll();
    }

    public function updateOrderStatus($orderId, $status) {
        $this->db->query('UPDATE orders SET status = :status WHERE id = :id');
        $this->db->bind(':id', $orderId);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function cancelOrder($orderId) {
        try {
            $this->db->beginTransaction();
            
            // Get order items
            $items = $this->getOrderItems($orderId);
            
            // Restore stock
            foreach ($items as $item) {
                $this->db->query('UPDATE books SET stock = stock + :quantity WHERE id = :book_id');
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':book_id', $item['book_id']);
                $this->db->execute();
            }
            
            // Update order status
            $this->updateOrderStatus($orderId, 'cancelled');
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getTotalOrders() {
        $this->db->query('SELECT COUNT(*) as total FROM orders');
        $result = $this->db->fetch();
        return $result['total'];
    }

    public function getTotalRevenue() {
        $this->db->query('SELECT SUM(total_amount) as revenue FROM orders 
                         WHERE status != "cancelled"');
        $result = $this->db->fetch();
        return $result['revenue'] ?? 0;
    }
}
?>