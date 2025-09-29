<!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <h4 class="text-white text-center mb-4">
            <i class="fas fa-cog"></i> Admin Panel
        </h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="books.php">
                    <i class="fas fa-book"></i> Books
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white active" href="orders.php">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="users.php">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="../index.php">
                    <i class="fas fa-home"></i> Back to Site
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-warning" href="../pages/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
        
        <!-- Admin Info -->
        <div class="mt-4 p-3 bg-secondary rounded">
            <small class="text-white">Logged in as:</small>
            <div class="text-white fw-bold"><?php echo $_SESSION['user_name']; ?></div>
            <small class="text-light"><?php echo $_SESSION['user_email']; ?></small>
        </div>
    </div>
</nav>