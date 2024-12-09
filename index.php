<?php
session_start();

// Data dummy pengguna (seharusnya menggunakan database)
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ['username' => 'admin', 'password' => 'admin123', 'email' => 'admin@example.com']
    ];
}

// Fungsi login
function login($username, $password, $users)
{
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            return true;
        }
    }
    return false;
}

// Fungsi registrasi
function register($username, $password, $email, &$users)
{
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return false; // Username sudah digunakan
        }
    }
    $users[] = ['username' => $username, 'password' => $password, 'email' => $email];
    $_SESSION['users'] = $users;
    return true;
}

// Proses logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $users = $_SESSION['users'];
    if (login($username, $password, $users)) {
        header("Location: {$_SERVER['PHP_SELF']}?page=guidelines");
        exit();
    } else {
        $error = "Username atau password salah.";
    }
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['regUsername'];
    $password = $_POST['regPassword'];
    $email = $_POST['regEmail'];
    $users = $_SESSION['users'];
    if (register($username, $password, $email, $users)) {
        $success = "Registrasi berhasil, silakan login.";
    } else {
        $error = "Username sudah digunakan.";
    }
}

// Halaman yang ditampilkan
$page = $_GET['page'] ?? (isset($_SESSION['logged_in']) ? 'guidelines' : 'login');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prioritas Barang</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigasi -->
    <nav class="nav-banner" <?php echo (!isset($_SESSION['logged_in']) ? 'style="display:none;"' : ''); ?>>
    <div class="nav-logo">Prioritas Barang</div>
    <div class="nav-buttons">
        <button class="nav-button" onclick="window.location.href='?page=guidelines'">Guidelines</button>
        <button class="nav-button" onclick="window.location.href='?page=input'">Unggah File</button>
        <button class="nav-button" onclick="window.location.href='?page=result'">Hasil Prioritas</button>
        <a href="?logout=true" class="nav-button">Logout</a>
    </div>
</nav>

    <!-- Halaman Login -->
    <?php if ($page === 'login' && !isset($_SESSION['logged_in'])): ?>
    <div class="container" id="loginPage">
        <h1>Login</h1>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" name="login">Login</button>
        </form>
        <p>Belum punya akun? <a href="?page=register">Daftar di sini</a>.</p>
    </div>
    <?php endif; ?>

    <!-- Halaman Registrasi -->
    <?php if ($page === 'register'): ?>
    <div class="container">
        <h1>Registrasi</h1>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="regUsername" placeholder="Username" required />
            <input type="password" name="regPassword" placeholder="Password" required />
            <input type="email" name="regEmail" placeholder="Email" required />
            <button type="submit" name="register">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="?page=login">Login di sini</a>.</p>
    </div>
    <?php endif; ?>

    <!-- Halaman Guidelines -->
    <?php if ($page === 'guidelines' && isset($_SESSION['logged_in'])): ?>
    <div class="container" id="guidelinesPage">
        <h1>Guidelines</h1>
        <div class="guidelines-section">
            <h3>Petunjuk Unggah File Excel</h3>
            <p>Pastikan file Excel Anda memiliki kolom sebagai berikut:</p>
            <ul>
                <li>Nama Barang</li>
                <li>Harga</li>
                <li>Kualitas</li>
                <li>Ketersediaan</li>
                <li>Durabilitas</li>
                <li>Kebutuhan</li>
                <li>Keuntungan</li>
            </ul>
            <p>Contoh link Google Spreadsheet:</p>
            <a href="https://docs.google.com/spreadsheets/d/1lxrEYZ_pIhTZavAif8s_er9CV4hryVPMyVxyyTZNxWo/edit?usp=sharing" 
               target="_blank" 
               style="color: #ba68c8; text-decoration: none; font-weight: bold;">
               Buka Contoh Spreadsheet
            </a>
            <p>Salin struktur tersebut sebelum mengunggah file Excel Anda.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Halaman Input -->
    <?php if ($page === 'input' && isset($_SESSION['logged_in'])): ?>
    <div class="container" id="inputPage">
        <h1>Unggah File</h1>
        <input type="file" id="fileInput" accept=".xlsx" />
        <button onclick="processFile()">Proses File</button>
    </div>
    <?php endif; ?>

    <script src="js/script.js"></script>

    <!-- Input Page -->
    <div class="container hidden" id="inputPage">
        <h1>Unggah File</h1>
        <input type="file" id="fileInput" accept=".xlsx" />
        <button onclick="processFile()">Proses File</button>
    </div>

    <!-- Result Page -->
    <?php if ($page === 'result' && isset($_SESSION['logged_in'])): ?>
<div class="container" id="resultPage">
    <h1>Hasil Analisis</h1>
    <div id="resultTable"></div>
</div>
<?php endif; ?>

    <script src="js/script.js"></script>
</body>
</html>