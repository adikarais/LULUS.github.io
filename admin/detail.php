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
    <title>Edit Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Data</h1>
        <hr>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Proses update data
            $nama_tempat = isset($_POST['nama_tempat']) ? $conn->real_escape_string($_POST['nama_tempat']) : '';
            $kategori = isset($_POST['kategori']) ? $conn->real_escape_string($_POST['kategori']) : '';
            $keterangan = isset($_POST['keterangan']) ? $conn->real_escape_string($_POST['keterangan']) : '';

            $updateQuery = "UPDATE lokasi SET 
                nama_tempat = '$nama_tempat', 
                kategori = '$kategori', 
                keterangan = '$keterangan' 
                WHERE id = $id";

            if ($conn->query($updateQuery)) {
                echo '<div class="alert alert-success">Data berhasil diperbarui.</div>';
                // Refresh data
                $query = $conn->query("SELECT * FROM lokasi WHERE id = $id");
                $data = $query->fetch_assoc();
            } else {
                echo '<div class="alert alert-danger">Gagal memperbarui data: ' . htmlspecialchars($conn->error) . '</div>';
            }
        } ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nama_tempat" class="form-label">Nama Tempat</label>
                <input type="text" class="form-control" id="nama_tempat" name="nama_tempat" value="<?= isset($data['nama_tempat']) ? htmlspecialchars($data['nama_tempat']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <input type="text" class="form-control" id="kategori" name="kategori" value="<?= isset($data['kategori']) ? htmlspecialchars($data['kategori']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="5" required><?= isset($data['keterangan']) ? htmlspecialchars($data['keterangan']) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
