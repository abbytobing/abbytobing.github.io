<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prioritas Barang</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    </style>
     @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}

body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    background-image: url("/api/placeholder/400/400");
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #333;
    position: relative;
    overflow-x: hidden;
    padding-top: 80px;
}

body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url("/api/placeholder/50/50"),
                    url("/api/placeholder/60/60"),
                    url("/api/placeholder/40/40"),
                    url("/api/placeholder/55/55");
    background-repeat: repeat;
    opacity: 0.1;
    z-index: -1;
    animation: float 6s ease-in-out infinite;
}

.nav-banner {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #9370db, #8e44ad);
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transition: all 0.3s ease;
}

.nav-banner.hidden {
    transform: translateY(-100%);
}

.nav-logo {
    color: white;
    font-size: 1.2em;
    font-weight: bold;
}

.nav-buttons {
    display: flex;
    gap: 15px;
}

.nav-button {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.nav-button:hover {
    background-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.container {
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    padding: 30px;
    margin: 20px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
    width: 90%;
    max-width: 1000px;
    z-index: 1;
}

h1, h2 {
    color: #8e44ad;
    text-align: center;
    margin-bottom: 30px;
}

h1 { font-size: 2.5em; }
h2 { font-size: 2em; }

input[type="file"],
input[type="text"],
input[type="password"],
input[type="email"],
input[type="url"] {
    margin-bottom: 20px;
    background-color: #f0f0f0;
    color: #333;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    width: 100%;
    box-sizing: border-box;
}

button {
    background-color: #9370db;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #8a2be2;
}

table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
}

th {
    background-color: #9370db;
    color: white;
}

tr:nth-child(even) { background-color: #f2f2f2; }
tr:hover { background-color: #e6e6fa; }

#priorityItem {
    background-color: #ffb6c1;
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
    text-align: center;
}

#priorityItem h3 {
    color: #8e44ad;
    margin-bottom: 10px;
}

.hidden { display: none; }

.rank {
    font-weight: bold;
    color: #8e44ad;
}

.guidelines-section {
    background-color: #f9f5ff;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
}

.guidelines-section h3 {
    color: #8e44ad;
    border-bottom: 2px solid #9370db;
    padding-bottom: 10px;
}

.guidelines-section ul {
    list-style-type: disc;
    padding-left: 30px;
    line-height: 1.6;
}

/* Tambahan untuk responsivitas */
@media screen and (max-width: 600px) {
    .container {
        width: 95%;
        padding: 15px;
    }

    h1 { font-size: 2em; }
    h2 { font-size: 1.5em; }

    table {
        font-size: 12px;
    }

    th, td {
        padding: 8px;
    }
}
</style>
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

<script>
    // Fungsi untuk menampilkan halaman berdasarkan ID
function navigateTo(page) {
    // Sembunyikan semua halaman
    document.querySelectorAll('.container').forEach(p => p.style.display = 'none');
    
    // Tampilkan halaman yang dipilih
    const targetPage = document.getElementById(page);
    if (targetPage) {
        targetPage.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const navButtons = document.querySelectorAll('.nav-button');
    navButtons.forEach(button => {
        button.addEventListener('click', function() {
            const page = this.getAttribute('data-page');
            if (page) {
                navigateTo(page);
            }
        });
    });
});

function login(event) {
    event.preventDefault();
    const username = document.querySelector('input[name="username"]').value;
    const password = document.querySelector('input[name="password"]').value;

    const users = JSON.parse(localStorage.getItem('users') || '[]');
    const user = users.find(u => u.username === username && u.password === password);

    if (user) {
        localStorage.setItem('loggedIn', 'true');
        localStorage.setItem('currentUser', username);

        // Langsung ke halaman guidelines
        navigateTo('guidelinesPage');
        document.querySelector('.nav-banner').style.display = 'flex';
    } else {
        alert('Username atau password salah. Silakan coba lagi.');
    }
}

// Fungsi registrasi
function register(event) {
    event.preventDefault();
    const username = document.querySelector('input[name="regUsername"]').value;
    const password = document.querySelector('input[name="regPassword"]').value;
    const email = document.querySelector('input[name="regEmail"]').value;

    // Ambil data pengguna dari localStorage
    let users = JSON.parse(localStorage.getItem('users') || '[]');

    // Cek apakah username sudah ada
    if (users.some(u => u.username === username)) {
        alert('Username sudah digunakan.');
        return;
    }

    // Tambahkan pengguna baru
    users.push({ username, password, email });
    localStorage.setItem('users', JSON.stringify(users));

    alert('Registrasi berhasil! Silakan login.');
    showPage('loginPage');
}

// Fungsi logout
function logout() {
    localStorage.removeItem('loggedIn');
    localStorage.removeItem('currentUser');
    localStorage.removeItem('uploadedData');

    showPage('loginPage');
    document.getElementById('navBar').classList.add('hidden');
}

// Fungsi untuk menghitung prioritas menggunakan Metode SAW
function calculatePriority(harga, kualitas, ketersediaan, durabilitas, kebutuhan, keuntungan) {
    // Normalisasi kriteria
    const weights = {
        harga: 0.2,
        kualitas: 0.3,
        ketersediaan: 0.15,
        durabilitas: 0.15,
        kebutuhan: 0.1,
        keuntungan: 0.1
    };

    // Mencari nilai maksimum dan minimum untuk normalisasi
    const maxValues = {
        harga: Math.max(...JSON.parse(localStorage.getItem('uploadedData') || '[]').map(item => parseFloat(item['Harga']))),
        kualitas: Math.max(...JSON.parse(localStorage.getItem('uploadedData') || '[]').map(item => parseFloat(item['Kualitas']))),
        ketersediaan: Math.max(...JSON.parse(localStorage.getItem('uploadedData') || '[]').map(item => parseFloat(item['Ketersediaan']))),
        durabilitas: Math.max(...JSON.parse(localStorage.getItem('uploadedData') || '[]').map(item => parseFloat(item['Durabilitas']))),
        kebutuhan: Math.max(...JSON.parse(localStorage.getItem('uploadedData') || '[]').map(item => parseFloat(item['Kebutuhan']))),
        keuntungan: Math.max(...JSON.parse(localStorage.getItem('uploadedData') || '[]').map(item => parseFloat(item['Keuntungan'])))
    };

    // Normalisasi dan perhitungan bobot
    const normalizedHarga = maxValues.harga / harga;  // Kebalikan untuk harga karena semakin rendah harga, semakin baik
    const normalizedKualitas = kualitas / maxValues.kualitas;
    const normalizedKetersediaan = ketersediaan / maxValues.ketersediaan;
    const normalizedDurabilitas = durabilitas / maxValues.durabilitas;
    const normalizedKebutuhan = kebutuhan / maxValues.kebutuhan;
    const normalizedKeuntungan = keuntungan / maxValues.keuntungan;

    // Hitung total prioritas dengan bobot
    return (
        weights.harga * normalizedHarga +
        weights.kualitas * normalizedKualitas +
        weights.ketersediaan * normalizedKetersediaan +
        weights.durabilitas * normalizedDurabilitas +
        weights.kebutuhan * normalizedKebutuhan +
        weights.keuntungan * normalizedKeuntungan
    );
}

// Fungsi untuk memproses file Excel
function processFile() {
    const fileInput = document.getElementById('fileInput');
    if (!fileInput.files.length) {
        alert('Pilih file terlebih dahulu!');
        return;
    }

    const file = fileInput.files[0];
    const reader = new FileReader();

    reader.onload = function (event) {
        const data = new Uint8Array(event.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(sheet);

        // Validasi kolom yang dibutuhkan
        const requiredColumns = ["Nama Barang", "Harga", "Kualitas", "Ketersediaan", "Durabilitas", "Kebutuhan", "Keuntungan"];
        const missingColumns = requiredColumns.filter(col => !Object.keys(jsonData[0] || {}).includes(col));

        if (missingColumns.length > 0) {
            alert(`Kolom berikut tidak ditemukan di file Excel: ${missingColumns.join(", ")}`);
            return;
        }

        // Konversi data numerik
        const processedData = jsonData.map(item => ({
            ...item,
            Harga: parseFloat(item["Harga"]),
            Kualitas: parseFloat(item["Kualitas"]),
            Ketersediaan: parseFloat(item["Ketersediaan"]),
            Durabilitas: parseFloat(item["Durabilitas"]),
            Kebutuhan: parseFloat(item["Kebutuhan"]),
            Keuntungan: parseFloat(item["Keuntungan"])
        }));

        // Hitung nilai prioritas
        const prioritizedData = processedData.map(item => ({
            ...item,
            "Nilai Prioritas": calculatePriority(
                item["Harga"],
                item["Kualitas"],
                item["Ketersediaan"],
                item["Durabilitas"],
                item["Kebutuhan"],
                item["Keuntungan"]
            )
        }));

        // Urutkan data berdasarkan nilai prioritas
        prioritizedData.sort((a, b) => b["Nilai Prioritas"] - a["Nilai Prioritas"]);

        // Simpan di localStorage
        localStorage.setItem('uploadedData', JSON.stringify(prioritizedData));

        // Redirect ke halaman hasil
        window.location.href = '?page=result';
    };

    reader.readAsArrayBuffer(file);
}

// Fungsi untuk menampilkan hasil di tabel
function displayResults() {
    const container = document.getElementById('resultTable');
    const storedData = localStorage.getItem('uploadedData');
    
    if (!storedData) {
        container.innerHTML = '<p>Tidak ada data yang tersedia. Silakan unggah file terlebih dahulu.</p>';
        return;
    }

    const data = JSON.parse(storedData);
    
    // Kosongkan kontainer sebelumnya
    container.innerHTML = "";

    // Buat tabel
    const table = document.createElement("table");
    table.border = "1";
    table.style.width = "100%";
    table.style.textAlign = "center";

    // Buat header tabel
    const headerRow = document.createElement("tr");
    const headers = ["Peringkat", "Nama Barang", "Harga", "Kualitas", "Ketersediaan", "Durabilitas", "Kebutuhan", "Keuntungan", "Nilai Prioritas"];
    headers.forEach(headerText => {
        const headerCell = document.createElement("th");
        headerCell.textContent = headerText;
        headerRow.appendChild(headerCell);
    });
    table.appendChild(headerRow);

    // Tambahkan data ke tabel
    data.forEach((item, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item["Nama Barang"]}</td>
            <td>${item["Harga"]}</td>
            <td>${item["Kualitas"]}</td>
            <td>${item["Ketersediaan"]}</td>
            <td>${item["Durabilitas"]}</td>
            <td>${item["Kebutuhan"]}</td>
            <td>${item["Keuntungan"]}</td>
            <td>${item["Nilai Prioritas"].toFixed(4)}</td>
        `;
        table.appendChild(row);
    });

    // Tampilkan tabel di dalam kontainer
    container.appendChild(table);
}

// Panggil displayResults saat halaman result dimuat
window.onload = function() {
    // Cek apakah di halaman result
    if (window.location.href.includes('page=result')) {
        displayResults();
    }
};
    </script>
</body>
</html>