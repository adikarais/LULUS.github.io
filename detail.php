<?php
// Koneksi ke database
// Bagian ini digunakan untuk mengatur koneksi ke database MySQL
$host = "localhost";
$user = "root";
$password = "";
$database = "ta_wgis"; // Ganti dengan nama database Anda

$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi
// Jika koneksi gagal, tampilkan pesan error dan hentikan eksekusi
if ($conn->connect_error) {
    die("Koneksi gagal: " . htmlspecialchars($conn->connect_error));
}

// Ambil ID dari parameter URL
// Bagian ini mengambil parameter 'id' dari URL dan mengonversinya menjadi integer
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Persiapkan query untuk mengambil data berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM lokasi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah data ditemukan
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        die("Data tidak ditemukan.");
    }
    $stmt->close();
} else {
    // Jika ID tidak diberikan atau tidak valid
    die("ID tidak diberikan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Data</title>
    <!-- Menggunakan Bootstrap untuk styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Detail Data</h1>
        <hr>

        <!-- Menampilkan nama tempat -->
        <h3><?= !empty(trim($data['nama_tempat'])) ? htmlspecialchars($data['nama_tempat']) : 'Nama tempat tidak tersedia'; ?></h3>

        <!-- Menampilkan latitude dan longitude -->
        <h5>Latitude Longitude:</h5>
        <p><?= !empty(trim($data['lat_long'])) ? nl2br(htmlspecialchars($data['lat_long'])) : 'Latitude longitude tidak tersedia'; ?></p>

        <!-- Menampilkan link website sekolah -->
        <h5>Website Sekolah:</h5>
        <p>
            <?= !empty(trim($data['link_sekolah'])) ? 
                '<a href="' . htmlspecialchars($data['link_sekolah']) . '" target="_blank">' . htmlspecialchars($data['link_sekolah']) . '</a>' 
                : 'Website sekolah tidak tersedia'; ?>
        </p>

        <!-- Menampilkan link lokasi sekolah di Google Maps -->
        <h5>Lokasi Sekolah (Google Maps):</h5>
        <p>
            <?= !empty(trim($data['link_lokasi'])) ? 
                '<a href="' . htmlspecialchars($data['link_lokasi']) . '" target="_blank">' . htmlspecialchars($data['link_lokasi']) . '</a>' 
                : 'Link lokasi tidak tersedia'; ?>
        </p>

        <!-- Menampilkan jenis sekolah -->
        <h5>Jenis Sekolah:</h5>
        <p><?= !empty(trim($data['kategori'])) ? nl2br(htmlspecialchars($data['kategori'])) : 'Jenis sekolah tidak tersedia'; ?></p>

        <!-- Menampilkan program pada sekolah -->
        <h5>Program pada Sekolah:</h5>
        <p><?= !empty(trim($data['keterangan'])) ? nl2br(htmlspecialchars($data['keterangan'])) : 'Program sekolah tidak tersedia'; ?></p>

        <!-- Tombol kembali ke halaman utama -->
        <a href="home.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>
</html>
