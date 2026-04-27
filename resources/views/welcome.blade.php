<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FUTSAL 35 - Reservasi Lapangan Futsal Online</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Navigation */
        nav {
            background: rgba(0, 0, 0, 0.9);
            padding: 20px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-logo span {
            color: #4CAF50;
        }

        .nav-menu {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .nav-menu a:hover {
            color: #4CAF50;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        .nav-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .nav-btn-login {
            background: transparent;
            color: white;
            border: 1px solid white;
        }

        .nav-btn-login:hover {
            background: white;
            color: #333;
        }

        .nav-btn-register {
            background: #4CAF50;
            color: white;
        }

        .nav-btn-register:hover {
            background: #45a049;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin-top: 70px;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 600'%3E%3Crect width='1200' height='600' fill='none'/%3E%3Ccircle cx='900' cy='300' r='200' fill='rgba(76,175,80,0.1)'/%3E%3Ccircle cx='1000' cy='400' r='150' fill='rgba(76,175,80,0.05)'/%3E%3C/svg%3E");
            background-size: cover;
            opacity: 0.5;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-text h1 {
            font-size: 48px;
            color: #ffffff;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .hero-text h1 .highlight {
            color: #4CAF50;
        }

        .hero-text p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid #4CAF50;
        }

        .btn-secondary:hover {
            background: #4CAF50;
        }

        .hero-image {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-image-box {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #2d3561 0%, #1f2647 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 120px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Features Section */
        .features {
            padding: 80px 20px;
            background: #f8f9fa;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 50px;
            color: #333;
        }

        .section-title .highlight {
            color: #4CAF50;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.2);
        }

        .feature-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            padding: 60px 20px;
            text-align: center;
            color: white;
        }

        .cta h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .cta .btn {
            background: white;
            color: #4CAF50;
        }

        .cta .btn:hover {
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            text-align: left;
            margin-bottom: 30px;
        }

        .footer-section h3 {
            margin-bottom: 15px;
            color: #4CAF50;
        }

        .footer-section a {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #4CAF50;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }

            .nav-buttons {
                display: none;
            }

            .menu-toggle {
                display: block;
            }

            .hero-content {
                grid-template-columns: 1fr;
            }

            .hero-text h1 {
                font-size: 32px;
            }

            .hero-image-box {
                height: 300px;
                font-size: 80px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 28px;
            }

            .cta h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <a href="#" class="nav-logo">⚽ <span>FUTSAL 35</span></a>
            
            <div class="nav-menu">
                <a href="#beranda">Beranda</a>
                <a href="#jadwal">Jadwal</a>
                <a href="#lapangan">Lapangan</a>
                <a href="#harga">Harga</a>
                <a href="#tentang">Tentang Kami</a>
            </div>

            <div class="nav-buttons">
                <a href="{{ route('login') }}" class="nav-btn nav-btn-login">Masuk</a>
                <a href="{{ route('register') }}" class="nav-btn nav-btn-register">Daftar</a>
            </div>

            <button class="menu-toggle">☰</button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Booking Lapangan Futsal Jadi Lebih <span class="highlight">Mudah</span></h1>
                <p>Pesan lapangan favorit mu kapan saja dan di mana saja. Cepat, mudah, dan tanpa antri!</p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn btn-primary">Booking Sekarang</a>
                    <a href="#" class="btn btn-secondary">Lihat Jadwal</a>
                </div>
            </div>

            <div class="hero-image">
                <div class="hero-image-box">⚽</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="lapangan">
        <div class="features-container">
            <h2 class="section-title">Kenapa Memilih <span class="highlight">FUTSAL 35?</span></h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⏱️</div>
                    <h3>Booking Cepat</h3>
                    <p>Proses booking yang cepat dan mudah hanya dalam beberapa klik saja</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Harga Terjangkau</h3>
                    <p>Kami menawarkan harga yang kompetitif dengan kualitas lapangan terbaik</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📅</div>
                    <h3>Jadwal Fleksibel</h3>
                    <p>Pilih jadwal yang sesuai dengan waktu luang Anda sepanjang hari</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🏟️</div>
                    <h3>Lapangan Berkualitas</h3>
                    <p>Lapangan futsal premium dengan fasilitas lengkap dan terawat dengan baik</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Komunitas</h3>
                    <p>Bergabunglah dengan komunitas futsal terbesar dan temukan tim baru</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Keamanan</h3>
                    <p>Sistem pembayaran aman dan data pribadi Anda terjaga dengan baik</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <h2>Siap untuk Bermain?</h2>
        <p>Daftar sekarang dan dapatkan diskon khusus untuk booking pertama Anda!</p>
        <a href="{{ route('register') }}" class="btn btn-primary" style="background: white; color: #4CAF50;">Daftar Gratis Sekarang</a>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>FUTSAL 35</h3>
                <p style="margin-bottom: 10px;">Platform booking lapangan futsal online terpercaya dan mudah digunakan.</p>
                <p>📍 Jakarta, Indonesia</p>
            </div>

            <div class="footer-section">
                <h3>Navigasi</h3>
                <a href="#beranda">Beranda</a>
                <a href="#jadwal">Jadwal</a>
                <a href="#lapangan">Lapangan</a>
                <a href="#harga">Harga</a>
            </div>

            <div class="footer-section">
                <h3>Akun</h3>
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}">Daftar</a>
                <a href="#">Lupa Password</a>
            </div>

            <div class="footer-section">
                <h3>Bantuan</h3>
                <a href="#">FAQ</a>
                <a href="#">Hubungi Kami</a>
                <a href="#">Syarat & Ketentuan</a>
                <a href="#">Kebijakan Privasi</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 FUTSAL 35. Semua hak dilindungi. | Dibuat dengan ❤️ untuk pecinta futsal</p>
        </div>
    </footer>
</body>
</html>
