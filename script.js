// Auto-hide alert messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Confirm delete actions
    const deleteForms = document.querySelectorAll('form[action*="delete"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });
    
    // Quantity input validation
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || Infinity;
            const value = parseInt(this.value);
            
            if (value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
                alert('Maximum quantity available: ' + max);
            }
        });
    });
    
    // Search form auto-submit on enter
    const searchForms = document.querySelectorAll('.nav-search form');
    searchForms.forEach(form => {
        const input = form.querySelector('input[name="search"]');
        if (input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    form.submit();
                }
            });
        }
    });
    
    // Mobile menu toggle
    const navToggle = document.createElement('button');
    navToggle.className = 'nav-toggle';
    navToggle.innerHTML = '☰';
    navToggle.style.display = 'none';
    navToggle.style.background = 'none';
    navToggle.style.border = 'none';
    navToggle.style.color = 'white';
    navToggle.style.fontSize = '1.5rem';
    navToggle.style.cursor = 'pointer';
    
    const navMenu = document.querySelector('.nav-menu');
    if (navMenu) {
        const navbar = document.querySelector('.navbar .container');
        navbar.insertBefore(navToggle, navMenu);
        
        navToggle.addEventListener('click', function() {
            navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
        });
        
        // Show/hide toggle button based on screen size
        function handleResize() {
            if (window.innerWidth <= 768) {
                navToggle.style.display = 'block';
                navMenu.style.display = 'none';
                navMenu.style.width = '100%';
                navMenu.style.flexDirection = 'column';
            } else {
                navToggle.style.display = 'none';
                navMenu.style.display = 'flex';
                navMenu.style.flexDirection = 'row';
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize();
    }
    
    // Image lazy loading
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Admin table row highlighting
    const tableRows = document.querySelectorAll('.admin-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('button') && !e.target.closest('a') && !e.target.closest('form')) {
                this.style.background = this.style.background === 'rgb(248, 249, 250)' ? '' : '#f8f9fa';
            }
        });
    });
});

// Print order function (can be called from order detail page)
function printOrder() {
    window.print();
}

// Add to cart with animation (optional enhancement)
function addToCartAnimation(button) {
    const originalText = button.textContent;
    button.textContent = 'Adding...';
    button.disabled = true;
    
    setTimeout(() => {
        button.textContent = 'Added!';
        setTimeout(() => {
            button.textContent = originalText;
            button.disabled = false;
        }, 1000);
    }, 500);
}