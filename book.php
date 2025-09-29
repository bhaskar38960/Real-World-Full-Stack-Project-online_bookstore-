<?php
class Book {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllBooks($limit = null, $offset = 0) {
        $query = 'SELECT * FROM books WHERE stock > 0 ORDER BY created_at DESC';
        if ($limit) {
            $query .= ' LIMIT :limit OFFSET :offset';
        }
        $this->db->query($query);
        if ($limit) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }
        return $this->db->fetchAll();
    }

    public function getBookById($id) {
        $this->db->query('SELECT * FROM books WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function searchBooks($keyword) {
        $this->db->query('SELECT * FROM books 
                         WHERE (title LIKE :keyword OR author LIKE :keyword 
                         OR description LIKE :keyword OR category LIKE :keyword) 
                         AND stock > 0
                         ORDER BY title');
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->fetchAll();
    }

    public function getBooksByCategory($category) {
        $this->db->query('SELECT * FROM books WHERE category = :category AND stock > 0 
                         ORDER BY title');
        $this->db->bind(':category', $category);
        return $this->db->fetchAll();
    }

    public function getCategories() {
        $this->db->query('SELECT DISTINCT category FROM books WHERE category IS NOT NULL 
                         ORDER BY category');
        return $this->db->fetchAll();
    }

    public function addBook($data) {
        $this->db->query('INSERT INTO books (title, author, isbn, description, price, 
                         category, stock, image, publisher, publication_year) 
                         VALUES (:title, :author, :isbn, :description, :price, 
                         :category, :stock, :image, :publisher, :publication_year)');
        
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':author', $data['author']);
        $this->db->bind(':isbn', $data['isbn']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':image', $data['image'] ?? null);
        $this->db->bind(':publisher', $data['publisher'] ?? null);
        $this->db->bind(':publication_year', $data['publication_year'] ?? null);
        
        return $this->db->execute();
    }

    public function updateBook($id, $data) {
        $this->db->query('UPDATE books SET title = :title, author = :author, isbn = :isbn, 
                         description = :description, price = :price, category = :category, 
                         stock = :stock, image = :image, publisher = :publisher, 
                         publication_year = :publication_year WHERE id = :id');
        
        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':author', $data['author']);
        $this->db->bind(':isbn', $data['isbn']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':publisher', $data['publisher'] ?? null);
        $this->db->bind(':publication_year', $data['publication_year'] ?? null);
        
        return $this->db->execute();
    }

    public function deleteBook($id) {
        $this->db->query('DELETE FROM books WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function updateStock($id, $quantity) {
        $this->db->query('UPDATE books SET stock = stock - :quantity WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':quantity', $quantity);
        return $this->db->execute();
    }

    public function getTotalBooks() {
        $this->db->query('SELECT COUNT(*) as total FROM books');
        $result = $this->db->fetch();
        return $result['total'];
    }
}
?>