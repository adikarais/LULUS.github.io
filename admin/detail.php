<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$database = "ta_wgis";

$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . htmlspecialchars($conn->connect_error));
}

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $query = $conn->query("SELECT * FROM lokasi WHERE id = $id");

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
    <style>
        .btn {
            --bs-btn-width: 200px;
        }
        .mt-5 {
            margin-top: 3rem !important;
            margin-bottom: 3rem !important;
}
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Data</h1>
        <hr>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Proses update data
            $lat_long = $conn->real_escape_string($_POST['lat_long'] ?? '');
            $nama_tempat = $conn->real_escape_string($_POST['nama_tempat'] ?? '');
            $kategori = $conn->real_escape_string($_POST['kategori'] ?? '');
            $nama_kecamatan = $conn->real_escape_string($_POST['nama_kecamatan'] ?? '');
            $keterangan = $conn->real_escape_string($_POST['keterangan'] ?? '');
            $link_lokasi = $conn->real_escape_string($_POST['link_lokasi'] ?? '');
            $link_sekolah = $conn->real_escape_string($_POST['link_sekolah'] ?? '');

            $updateQuery = "UPDATE lokasi SET
                lat_long = '$lat_long',
                nama_tempat = '$nama_tempat', 
                kategori = '$kategori',
                nama_kecamatan = '$nama_kecamatan', 
                keterangan = '$keterangan',
                link_lokasi = '$link_lokasi',
                link_sekolah = '$link_sekolah'
                WHERE id = $id";

            if ($conn->query($updateQuery)) {
                echo '<div class="alert alert-success">Data berhasil diperbarui.</div>';
                $query = $conn->query("SELECT * FROM lokasi WHERE id = $id");
                $data = $query->fetch_assoc();
            } else {
                echo '<div class="alert alert-danger">Gagal memperbarui data: ' . htmlspecialchars($conn->error) . '</div>';
            }
        }
        ?>
        <!-- Formulir untuk mengedit data lokasi -->
        <form method="POST">
            <!-- Input untuk koordinat lat_long -->
            <div class="mb-3">
                <label for="lat_long" class="form-label">Latitude Longitude</label>
                <input type="text" class="form-control" id="lat_long" name="lat_long"
                    value="<?= htmlspecialchars($data['lat_long'] ?? ''); ?>" required>
            </div>

            <!-- Input untuk nama tempat -->
            <div class="mb-3">
                <label for="nama_tempat" class="form-label">Nama Tempat</label>
                <input type="text" class="form-control" id="nama_tempat" name="nama_tempat"
                    value="<?= htmlspecialchars($data['nama_tempat'] ?? ''); ?>" required>
            </div>

            <!-- Dropdown kategori SMA/SMK/MA -->
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-control" id="kategori" name="kategori" required>
                    <option value="SMA" <?= ($data['kategori'] ?? '') === 'SMA' ? 'selected' : ''; ?>>SMA</option>
                    <option value="SMK" <?= ($data['kategori'] ?? '') === 'SMK' ? 'selected' : ''; ?>>SMK</option>
                    <option value="MA" <?= ($data['kategori'] ?? '') === 'MA' ? 'selected' : ''; ?>>MA</option>
                </select>
            </div>

            <!-- Dropdown kecamatan (perbaiki: nama atribut harus sesuai dengan PHP backend) -->
            <div class="mb-3">
                <label for="nama_kecamatan" class="form-label">Nama Kecamatan</label>
                <select class="form-control" name="nama_kecamatan" id="nama_kecamatan" required>
                    <option value="">-- Pilih Kecamatan --</option>
                    <?php
                    $kecamatanList = ["Bayat","Cawas","Ceper","Delanggu","Gantiwarno","Jatinom","Jogonalan",
                        "Juwiring","Kalikotes","Karanganom","Karangdowo","Karangnongko","Kebonarum","Kemalang",
                        "Klaten Utara","Klaten Tengah","Klaten Selatan","Manisrenggo","Ngawen","Pedan",
                        "Polanharjo","Prambanan","Trucuk","Tulung","Wedi","Wonosari"];

                    foreach ($kecamatanList as $kecamatan) {
                        $selected = ($data['nama_kecamatan'] ?? '') === $kecamatan ? 'selected' : '';
                        echo "<option value=\"$kecamatan\" $selected>$kecamatan</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Input keterangan -->
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="5" required><?= htmlspecialchars($data['keterangan'] ?? ''); ?></textarea>
            </div>

            <!-- Input dan tombol untuk link lokasi -->
            <div class="mb-3">
                <label for="link_lokasi" class="form-label">Link Lokasi</label>
                <div class="row g-2">
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="link_lokasi" name="link_lokasi" value="<?= htmlspecialchars($data['link_lokasi'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= htmlspecialchars($data['link_lokasi'] ?? '#'); ?>" target="_blank" class="btn btn-outline-primary w-100">Kunjungi Lokasi</a>
                    </div>
                </div>
            </div>

            <!-- Input dan tombol untuk link sekolah -->
            <div class="mb-3">
                <label for="link_sekolah" class="form-label">Link Sekolah</label>
                <div class="row g-2">
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="link_sekolah" name="link_sekolah" value="<?= htmlspecialchars($data['link_sekolah'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= htmlspecialchars($data['link_sekolah'] ?? '#'); ?>" target="_blank" class="btn btn-outline-primary w-100">Kunjungi Sekolah</a>
                    </div>
                </div>
            </div>

            <!-- Tombol simpan dan kembali -->
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>

    </div>
</body>
</html>
