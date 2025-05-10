<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$database = "ta_wgis"; // Ganti dengan nama database Anda

$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . htmlspecialchars($conn->connect_error));
}

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $query = $conn->query("SELECT * FROM lokasi WHERE id = $id"); // Replace 'your_table_name' with the actual table name

    if ($query && $query->num_rows > 0) {
        $data = $query->fetch_assoc();
    } else {
        die("Data tidak ditemukan.");
    }
} else {
    die("ID tidak diberikan.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Detail Data</h1>
        <hr>
        <h3><?= isset($data['nama_tempat']) ? htmlspecialchars($data['nama_tempat']) : 'Judul tidak tersedia'; ?></h3>
        <p><?= isset($data['kategori']) ? nl2br(htmlspecialchars($data['kategori'])) : 'kategori tidak tersedia'; ?></p>
        <p><?= isset($data['keterangan']) ? nl2br(htmlspecialchars($data['keterangan'])) : 'Deskripsi tidak tersedia'; ?></p>
        <a href="home.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>
</html>
