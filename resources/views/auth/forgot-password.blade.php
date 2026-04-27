<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - FUTSAL 35</title>
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
        }

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
            max-width: 450px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .forgot-box {
            background: rgba(45, 52, 75, 0.95);
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(76, 175, 80, 0.1);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
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

        .form-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 30px;
            line-height: 1.6;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-group input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: #4CAF50;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
            color: #ffffff;
            font-size: 14px;
        }

        .back-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #45a049;
            text-decoration: underline;
        }

        .success-message {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid #4CAF50;
            color: #a5d6a7;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
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

        @media (max-width: 480px) {
            .forgot-box {
                padding: 35px 25px;
            }

            .logo-section h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="background-decoration"></div>
    
    <div class="container">
        <div class="forgot-box">
            <div class="logo-section">
                <div class="logo-icon">⚽</div>
                <h1>FUTSAL 35</h1>
                <p>Reset Password Anda</p>
            </div>

            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <p class="form-description">
                Masukkan email Anda, dan kami akan mengirimkan link untuk reset password ke email tersebut.
            </p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        placeholder="Masukkan email Anda"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <button type="submit" class="submit-btn">Kirim Link Reset Password</button>
            </form>

            <div class="back-link">
                Ingat password Anda? <a href="{{ route('login') }}">Kembali ke login</a>
            </div>
        </div>
    </div>
</body>
</html>
