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
