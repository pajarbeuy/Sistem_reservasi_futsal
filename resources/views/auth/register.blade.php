<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - FUTSAL 35</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        /* Background decoration */
        .background-decoration {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10 10 L90 90 M90 10 L10 90' stroke='rgba(76, 175, 80, 0.1)' stroke-width='2'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        .container {
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
        }

        .register-box {
            background: rgba(45, 52, 75, 0.95);
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(76, 175, 80, 0.1);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-section .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 32px;
            font-weight: bold;
            color: white;
        }

        .logo-section h1 {
            color: white;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .logo-section p {
            color: #90caf9;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-row .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: #4CAF50;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
        }

        .form-group select {
            cursor: pointer;
        }

        .form-group select option {
            background: #2d344b;
            color: #ffffff;
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength .strength-bar {
            height: 100%;
            width: 0%;
            background: #ff6b6b;
            transition: width 0.3s ease, background 0.3s ease;
        }

        .terms-agree {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .terms-agree input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
            accent-color: #4CAF50;
            flex-shrink: 0;
        }

        .terms-agree label {
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            margin: 0;
        }

        .terms-agree a {
            color: #4CAF50;
            text-decoration: none;
        }

        .terms-agree a:hover {
            text-decoration: underline;
        }

        .register-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #ffffff;
            font-size: 13px;
        }

        .login-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid #f44336;
            color: #ffcdd2;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .error-message ul {
            margin: 0;
            padding-left: 20px;
        }

        .error-message li {
            margin-bottom: 5px;
        }

        @media (max-width: 480px) {
            .register-box {
                padding: 35px 25px;
            }

            .logo-section h1 {
                font-size: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-row .form-group {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="background-decoration"></div>
    
    <div class="container">
        <div class="register-box">
            <div class="logo-section">
                <div class="logo-icon">⚽</div>
                <h1>FUTSAL 35</h1>
                <p>Daftarkan Akun Anda Sekarang</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Nama Depan *</label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name"
                            placeholder="Nama depan"
                            value="{{ old('first_name') }}"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label for="last_name">Nama Belakang</label>
                        <input 
                            type="text" 
                            id="last_name" 
                            name="last_name"
                            placeholder="Nama belakang"
                            value="{{ old('last_name') }}"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        placeholder="nama@email.com"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">No. Telepon</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone"
                            placeholder="08XXXXXXXXXX"
                            value="{{ old('phone') }}"
                        >
                    </div>
                    <div class="form-group">
                        <label for="gender">Jenis Kelamin</label>
                        <select id="gender" name="gender">
                            <option value="">Pilih</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Buat password yang kuat"
                        required
                        onkeyup="checkPasswordStrength(this.value)"
                    >
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password *</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        placeholder="Ulangi password Anda"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <input 
                        type="text" 
                        id="address" 
                        name="address"
                        placeholder="Alamat lengkap Anda"
                        value="{{ old('address') }}"
                    >
                </div>

                <div class="terms-agree">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        Saya setuju dengan <a href="#">Syarat & Ketentuan</a> dan 
                        <a href="#">Kebijakan Privasi</a>
                    </label>
                </div>

                <button type="submit" class="register-btn">Daftar Sekarang</button>
            </form>

            <div class="login-link">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>

    <script>
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('strengthBar');
            let strength = 0;

            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 25;

            strengthBar.style.width = strength + '%';

            if (strength <= 25) {
                strengthBar.style.background = '#ff6b6b';
            } else if (strength <= 50) {
                strengthBar.style.background = '#ffa500';
            } else if (strength <= 75) {
                strengthBar.style.background = '#90caf9';
            } else {
                strengthBar.style.background = '#4CAF50';
            }
        }
    </script>
</body>
</html>
