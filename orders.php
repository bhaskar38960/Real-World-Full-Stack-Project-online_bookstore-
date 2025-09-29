<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

// Sample orders data (in real app, fetch from database)
$orders = [
    [
        'id' => 'ORD-001',
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'total_amount' => 45.97,
        'status' => 'delivered',
        'items_count' => 3,
        'order_date' => '2024-01-15 14:30:00',
        'payment_status' => 'paid',
        'shipping_address' => '123 Main St, New York, NY 10001'
    ],
    [
        'id' => 'ORD-002',
        'customer_name' => 'Jane Smith',
        'customer_email' => 'jane@example.com',
        'total_amount' => 28.99,
        'status' => 'processing',
        'items_count' => 2,
        'order_date' => '2024-01-14 10:15:00',
        'payment_status' => 'paid',
        'shipping_address' => '456 Oak Ave, Los Angeles, CA 90210'
    ],
    [
        'id' => 'ORD-003',
        'customer_name' => 'Mike Johnson',
        'customer_email' => 'mike@example.com',
        'total_amount' => 15.99,
        'status' => 'pending',
        'items_count' => 1,
        'order_date' => '2024-01-13 16:45:00',
        'payment_status' => 'pending',
        'shipping_address' => '789 Pine Rd, Chicago, IL 60601'
    ],
    [
        'id' => 'ORD-004',
        'customer_name' => 'Sarah Wilson',
        'customer_email' => 'sarah@example.com',
        'total_amount' => 67.50,
        'status' => 'shipped',
        'items_count' => 4,
        'order_date' => '2024-01-12 09:20:00',
        'payment_status' => 'paid',
        'shipping_address' => '321 Elm St, Houston, TX 77001'
    ],
    [
        'id' => 'ORD-005',
        'customer_name' => 'Tom Brown',
        'customer_email' => 'tom@example.com',
        'total_amount' => 22.50,
        'status' => 'cancelled',
        'items_count' => 2,
        'order_date' => '2024-01-11 11:30:00',
        'payment_status' => 'refunded',
        'shipping_address' => '654 Maple Dr, Phoenix, AZ 85001'
    ]
];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    // In real app, update database here
    foreach ($orders as &$order) {
        if ($order['id'] === $order_id) {
            $order['status'] = $new_status;
            if ($new_status === 'delivered') {
                $order['payment_status'] = 'paid';
            }
            break;
        }
    }
    
    $success = "Order status updated successfully!";
}

// Handle filters
$status_filter = $_GET['status'] ?? '';
$search_term = $_GET['search'] ?? '';

$filtered_orders = $orders;

if ($status_filter) {
    $filtered_orders = array_filter($filtered_orders, function($order) use ($status_filter) {
        return $order['status'] === $status_filter;
    });
}

if ($search_term) {
    $filtered_orders = array_filter($filtered_orders, function($order) use ($search_term) {
        return stripos($order['customer_name'], $search_term) !== false || 
               stripos($order['customer_email'], $search_term) !== false ||
               stripos($order['id'], $search_term) !== false;
    });
}

// Statistics
$total_orders = count($orders);
$total_revenue = array_sum(array_column($orders, 'total_amount'));
$pending_orders = count(array_filter($orders, function($order) {
    return $order['status'] === 'pending';
}));
$completed_orders = count(array_filter($orders, function($order) {
    return $order['status'] === 'delivered';
}));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .status-badge {
            font-size: 0.75rem;
        }
        .order-card {
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-shopping-cart"></i> Orders Management
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printOrders()">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportOrders()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-primary fw-bold"><?php echo $total_orders; ?></div>
                                        <div class="small text-muted">Total Orders</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-shopping-cart text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-success fw-bold">$<?php echo number_format($total_revenue, 2); ?></div>
                                        <div class="small text-muted">Total Revenue</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-dollar-sign text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-warning fw-bold"><?php echo $pending_orders; ?></div>
                                        <div class="small text-muted">Pending</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-info fw-bold"><?php echo $completed_orders; ?></div>
                                        <div class="small text-muted">Completed</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-danger fw-bold">1</div>
                                        <div class="small text-muted">Cancelled</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-times-circle text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="card border-secondary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-secondary fw-bold">4.8</div>
                                        <div class="small text-muted">Avg. Rating</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-star text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Orders</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search_term); ?>" 
                                       placeholder="Search by customer, email, or order ID...">
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Filter by Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Apply Filters
                                    </button>
                                    <a href="orders.php" class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Success Message -->
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Orders Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Orders List
                            <span class="badge bg-primary ms-2"><?php echo count($filtered_orders); ?> orders</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($filtered_orders)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5>No orders found</h5>
                                <p class="text-muted">Try adjusting your search filters</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($filtered_orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $order['id']; ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold"><?php echo $order['customer_name']; ?></div>
                                                    <small class="text-muted"><?php echo $order['customer_email']; ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo date('M j, Y', strtotime($order['order_date'])); ?>
                                                <br>
                                                <small class="text-muted"><?php echo date('g:i A', strtotime($order['order_date'])); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo $order['items_count']; ?> items</span>
                                            </td>
                                            <td>
                                                <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ][$order['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $status_class; ?> status-badge">
                                                    <i class="fas fa-<?php 
                                                        echo $order['status'] === 'delivered' ? 'check' : 
                                                             ($order['status'] === 'cancelled' ? 'times' : 'clock'); 
                                                    ?>"></i>
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $payment_class = [
                                                    'paid' => 'success',
                                                    'pending' => 'warning',
                                                    'refunded' => 'info',
                                                    'failed' => 'danger'
                                                ][$order['payment_status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $payment_class; ?> status-badge">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success" 
                                                            data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $order['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="#" class="btn btn-outline-info">
                                                        <i class="fas fa-receipt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Order Details Modal -->
                                        <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Order Details - <?php echo $order['id']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6>Customer Information</h6>
                                                                <p>
                                                                    <strong>Name:</strong> <?php echo $order['customer_name']; ?><br>
                                                                    <strong>Email:</strong> <?php echo $order['customer_email']; ?><br>
                                                                    <strong>Phone:</strong> +1 (555) 123-4567
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Order Information</h6>
                                                                <p>
                                                                    <strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?><br>
                                                                    <strong>Status:</strong> 
                                                                    <span class="badge bg-<?php echo $status_class; ?>">
                                                                        <?php echo ucfirst($order['status']); ?>
                                                                    </span><br>
                                                                    <strong>Payment:</strong> 
                                                                    <span class="badge bg-<?php echo $payment_class; ?>">
                                                                        <?php echo ucfirst($order['payment_status']); ?>
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        
                                                        <h6>Shipping Address</h6>
                                                        <p><?php echo nl2br($order['shipping_address']); ?></p>
                                                        
                                                        <h6>Order Items</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Book</th>
                                                                        <th>Quantity</th>
                                                                        <th>Price</th>
                                                                        <th>Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>The Great Gatsby</td>
                                                                        <td>1</td>
                                                                        <td>$12.99</td>
                                                                        <td>$12.99</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>To Kill a Mockingbird</td>
                                                                        <td>1</td>
                                                                        <td>$14.99</td>
                                                                        <td>$14.99</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                                                        <td><strong>$27.98</strong></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                                                        <td><strong>$5.99</strong></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                                        <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary" onclick="printInvoice('<?php echo $order['id']; ?>')">
                                                            <i class="fas fa-print"></i> Print Invoice
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Update Modal -->
                                        <div class="modal fade" id="statusModal<?php echo $order['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Order Status - <?php echo $order['id']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <div class="mb-3">
                                                                <label for="status<?php echo $order['id']; ?>" class="form-label">Order Status</label>
                                                                <select class="form-select" id="status<?php echo $order['id']; ?>" name="status" required>
                                                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="notes<?php echo $order['id']; ?>" class="form-label">Admin Notes (Optional)</label>
                                                                <textarea class="form-control" id="notes<?php echo $order['id']; ?>" name="notes" rows="3" placeholder="Add any notes about this order..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                Showing <?php echo count($filtered_orders); ?> of <?php echo $total_orders; ?> orders
                            </div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printOrders() {
            alert('Print functionality would be implemented here');
            // window.print(); // Uncomment for actual printing
        }

        function exportOrders() {
            alert('Export functionality would be implemented here');
            // Implementation for CSV/Excel export
        }

        function printInvoice(orderId) {
            alert('Printing invoice for order: ' + orderId);
            // Implementation for invoice printing
        }

        // Auto-refresh orders every 30 seconds
        setInterval(() => {
            // In real app, this would refresh the orders list
            console.log('Auto-refreshing orders...');
        }, 30000);
    </script>
</body>
</html>