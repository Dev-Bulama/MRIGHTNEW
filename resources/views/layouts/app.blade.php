<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'M-right Digital Receipt') | {{ config('app.name', 'M-right Portal') }}</title>
    <meta name="description" content="@yield('description', 'Digital receipt generation system for phone shops')">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #0D8ABC;
            --secondary-color: #17a2b8;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --muted-color: #6c757d;
            
            --sidebar-width: 280px;
            --topbar-height: 70px;
            
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            line-height: 1.5;
            color: var(--dark-color);
            background-color: #f5f6fa;
            overflow-x: hidden;
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Custom Button Styles */
        .btn {
            font-weight: 500;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #1e7e34 100%);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #bd2130 100%);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-bottom: 1px solid #e9ecef;
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Form Styles */
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 138, 188, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
        }
        
        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #d1e7dd;
            border-left-color: var(--success-color);
            color: #0a3622;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-left-color: var(--danger-color);
            color: #58151c;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-left-color: var(--warning-color);
            color: #664d03;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-left-color: var(--info-color);
            color: #055160;
        }
        
        /* Utilities */
        .text-muted {
            color: var(--muted-color) !important;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
        }
        
        .shadow-custom {
            box-shadow: var(--shadow);
        }
        
        .rounded-custom {
            border-radius: 12px;
        }
        
        /* Loading Spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .card-header {
                padding: 1rem 1.25rem;
            }
        }
        
        /* Custom animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-10px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        @auth
            @include('partials.sidebar')
            @include('partials.topbar')
            
            <!-- Main Content -->
            <main class="main-content">
                <div class="container-fluid py-4">
                    @include('partials.alerts')
                    @yield('content')
                </div>
            </main>
        @else
            @yield('content')
        @endauth
    </div>
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // CSRF Token Setup
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Set CSRF token for all AJAX requests
        fetch.defaults = fetch.defaults || {};
        fetch.defaults.headers = fetch.defaults.headers || {};
        fetch.defaults.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Toast notification function
        function showToast(message, type = 'success') {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            const toast = new bootstrap.Toast(toastContainer.lastElementChild);
            toast.show();
            
            // Remove toast after it's hidden
            toastContainer.lastElementChild.addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
        
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1070';
            document.body.appendChild(container);
            return container;
        }
        
        // Loading state helper
        function setLoading(element, loading = true) {
            if (loading) {
                element.disabled = true;
                const originalText = element.innerHTML;
                element.setAttribute('data-original-text', originalText);
                element.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
            } else {
                element.disabled = false;
                element.innerHTML = element.getAttribute('data-original-text');
            }
        }
        
        // Form validation helper
        function clearFormErrors(form) {
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        }
        
        function showFormErrors(form, errors) {
            clearFormErrors(form);
            
            Object.keys(errors).forEach(field => {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = errors[field][0];
                    input.parentNode.appendChild(feedback);
                }
            });
        }
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert[data-auto-dismiss="true"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>