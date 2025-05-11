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
            $lat_long = isset($_POST['lat_long']) ? $conn->real_escape_string($_POST['lat_long']) : '';
            $nama_tempat = isset($_POST['nama_tempat']) ? $conn->real_escape_string($_POST['nama_tempat']) : '';
            $kategori = isset($_POST['kategori']) ? $conn->real_escape_string($_POST['kategori']) : '';
            $keterangan = isset($_POST['keterangan']) ? $conn->real_escape_string($_POST['keterangan']) : '';
            $link_lokasi = isset($_POST['link_lokasi']) ? $conn->real_escape_string($_POST['link_lokasi']) : '';
            $link_sekolah = isset($_POST['link_sekolah']) ? $conn->real_escape_string($_POST['link_sekolah']) : '';

            $updateQuery = "UPDATE lokasi SET
                lat_long = '$lat_long',
                nama_tempat = '$nama_tempat', 
                kategori = '$kategori', 
                keterangan = '$keterangan',
                link_lokasi = '$link_lokasi',
                link_sekolah = '$link_sekolah'
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
            <label for="lat_long" class="form-label">latitude longitude</label>
            <input type="text" class="form-control" id="lat_long" name="lat_long" value="<?= isset($data['lat_long']) ? htmlspecialchars($data['lat_long']) : ''; ?>" required>
            </div>
            <div class="mb-3">
            <label for="nama_tempat" class="form-label">Nama Tempat</label>
            <input type="text" class="form-control" id="nama_tempat" name="nama_tempat" value="<?= isset($data['nama_tempat']) ? htmlspecialchars($data['nama_tempat']) : ''; ?>" required>
            </div>
            <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <select class="form-control" id="kategori" name="kategori" required>
                <option value="SMA" <?= isset($data['kategori']) && $data['kategori'] === 'SMA' ? 'selected' : ''; ?>>SMA</option>
                <option value="SMK" <?= isset($data['kategori']) && $data['kategori'] === 'SMK' ? 'selected' : ''; ?>>SMK</option>
                <option value="MA" <?= isset($data['kategori']) && $data['kategori'] === 'MA' ? 'selected' : ''; ?>>MA</option>
            </select>
            </div>
            <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan" rows="5" required><?= isset($data['keterangan']) ? htmlspecialchars($data['keterangan']) : ''; ?></textarea>
            </div>
            <div class="mb-3">
            <label for="link_lokasi" class="form-label">Link Lokasi</label>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control me-2" id="link_lokasi" name="link_lokasi" value="<?= isset($data['link_lokasi']) ? htmlspecialchars($data['link_lokasi']) : ''; ?>" required>
                <a href="<?= isset($data['link_lokasi']) ? htmlspecialchars($data['link_lokasi']) : '#'; ?>" target="_blank" class="btn btn-primary">Link Lokasi</a>
            </div>
            </div>
            <div class="mb-3">
            <label for="link_sekolah" class="form-label">Link Sekolah</label>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control me-2" id="link_sekolah" name="link_sekolah" value="<?= isset($data['link_sekolah']) ? htmlspecialchars($data['link_sekolah']) : ''; ?>" required>
                <a href="<?= isset($data['link_sekolah']) ? htmlspecialchars($data['link_sekolah']) : '#'; ?>" target="_blank" class="btn btn-primary">Link Sekolah</a>
            </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
