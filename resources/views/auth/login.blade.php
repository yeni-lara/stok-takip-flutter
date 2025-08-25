<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }} - Giriş</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #059669;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --info-color: #0891b2;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
        }
        
        .login-container {
            position: relative;
            z-index: 2;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.1),
                0 10px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm10 0c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            opacity: 0.1;
        }
        
        .login-header .logo {
            position: relative;
            z-index: 1;
        }
        
        .logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
        }
        
        .login-body {
            padding: 2.5rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-floating > .form-control {
            height: 60px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 0.75rem 0.25rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .form-floating > .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
            background: white;
            transform: translateY(-2px);
        }
        
        .form-floating > label {
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        .btn-login {
            width: 100%;
            height: 55px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-right: 0.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        
        .loading {
            display: none;
        }
        
        .btn-login.loading .btn-text {
            opacity: 0;
        }
        
        .btn-login.loading .loading {
            display: inline-block;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        .login-footer {
            background: #f8fafc;
            padding: 1.5rem 2.5rem;
            text-align: center;
            color: var(--secondary-color);
            font-size: 0.9rem;
            border-top: 1px solid #e2e8f0;
        }
        
        @media (max-width: 576px) {
            .login-container {
                margin: 10px;
            }
            
            .login-header,
            .login-body {
                padding: 1.5rem;
            }
            
            .login-footer {
                padding: 1rem 1.5rem;
            }
        }
        
        .input-group-text {
            background: transparent;
            border: none;
            color: var(--secondary-color);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            z-index: 10;
            padding: 5px;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <h3 class="mb-1">{{ config('app.name') }}</h3>
                    <p class="mb-0 opacity-75">Stok Takip ve Yönetim Sistemi</p>
                </div>
            </div>

            <!-- Body -->
            <div class="login-body">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Giriş yapılamadı!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Email Address -->
                    <div class="form-floating">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="E-posta adresiniz" 
                               required 
                               autofocus 
                               autocomplete="email">
                        <label for="email">
                            <i class="bi bi-envelope me-2"></i>E-posta Adresi
                        </label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-floating position-relative">
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Şifreniz" 
                               required 
                               autocomplete="current-password">
                        <label for="password">
                            <i class="bi bi-lock me-2"></i>Şifre
                        </label>
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="remember-me">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label for="remember" class="form-check-label">
                            Beni hatırla
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <span class="btn-text">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Giriş Yap
                        </span>
                        <span class="loading">
                            <span class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </span>
                            Giriş yapılıyor...
                        </span>
                    </button>
                </form>

                <!-- Forgot Password -->
                @if (Route::has('password.request'))
                    <div class="forgot-password">
                        <a href="{{ route('password.request') }}">
                            <i class="bi bi-key me-1"></i>
                            Şifremi unuttum
                        </a>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                    <span>
                        <i class="bi bi-shield-check me-1"></i>
                        Güvenli Giriş
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordIcon = document.getElementById('passwordIcon');
            
            passwordToggle.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.className = 'bi bi-eye-slash';
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.className = 'bi bi-eye';
                }
            });
            
            // Form submission loading state
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            
            loginForm.addEventListener('submit', function() {
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
                
                // Re-enable button after 5 seconds in case of error
                setTimeout(function() {
                    loginBtn.classList.remove('loading');
                    loginBtn.disabled = false;
                }, 5000);
            });
            
            // Auto-focus on email field
            document.getElementById('email').focus();
            
            // Animate form inputs
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
        });
    </script>
</body>
</html>
