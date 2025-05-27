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

// Ambil daftar jurusan yang sudah ada untuk lokasi ini
$jurusan_query = $conn->query("
    SELECT pj.id, pl.id as pelajaran_id, pl.nama_pelajaran 
    FROM penjurusan pj
    JOIN pelajaran pl ON pj.pelajaran_id = pl.id
    WHERE pj.lokasi_id = $id
");
$existing_jurusan = [];
while ($row = $jurusan_query->fetch_assoc()) {
    $existing_jurusan[$row['pelajaran_id']] = $row['nama_pelajaran'];
}

// Ambil daftar semua pelajaran yang tersedia
$pelajaran_query = $conn->query("SELECT id, nama_pelajaran FROM pelajaran ORDER BY nama_pelajaran");
$all_pelajaran = [];
while ($row = $pelajaran_query->fetch_assoc()) {
    $all_pelajaran[$row['id']] = $row['nama_pelajaran'];
}

// Proses form jika ada submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses update data lokasi
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
        echo '<div class="alert alert-success">Data lokasi berhasil diperbarui.</div>';
        $query = $conn->query("SELECT * FROM lokasi WHERE id = $id");
        $data = $query->fetch_assoc();
    } else {
        echo '<div class="alert alert-danger">Gagal memperbarui data lokasi: ' . htmlspecialchars($conn->error) . '</div>';
    }

    // Proses jurusan yang dipilih
    $selected_jurusan = $_POST['jurusan'] ?? [];
    $new_jurusan = $_POST['new_jurusan'] ?? '';
    
    // Hapus semua jurusan yang ada untuk lokasi ini
    $conn->query("DELETE FROM penjurusan WHERE lokasi_id = $id");
    
    // Tambahkan jurusan yang dipilih dari checkbox
    foreach ($selected_jurusan as $pelajaran_id) {
        $pelajaran_id = intval($pelajaran_id);
        if ($pelajaran_id > 0) {
            $conn->query("INSERT INTO penjurusan (lokasi_id, pelajaran_id) VALUES ($id, $pelajaran_id)");
        }
    }
    
    // Tambahkan jurusan baru jika ada
    if (!empty($new_jurusan)) {
        $new_jurusan = $conn->real_escape_string(trim($new_jurusan));
        
        // Cek apakah jurusan sudah ada di database
        $check_query = $conn->query("SELECT id FROM pelajaran WHERE nama_pelajaran = '$new_jurusan'");
        
        if ($check_query->num_rows > 0) {
            // Jurusan sudah ada, gunakan ID yang ada
            $pelajaran_id = $check_query->fetch_assoc()['id'];
        } else {
            // Tambahkan jurusan baru ke tabel pelajaran
            $conn->query("INSERT INTO pelajaran (nama_pelajaran) VALUES ('$new_jurusan')");
            $pelajaran_id = $conn->insert_id;
        }
        
        // Hubungkan jurusan dengan lokasi
        $conn->query("INSERT INTO penjurusan (lokasi_id, pelajaran_id) VALUES ($id, $pelajaran_id)");
        
        // Perbarui daftar jurusan
        $all_pelajaran[$pelajaran_id] = $new_jurusan;
        $existing_jurusan[$pelajaran_id] = $new_jurusan;
    }
    
    echo '<div class="alert alert-success">Data jurusan berhasil diperbarui.</div>';
    
    // Refresh data jurusan yang ada
    $jurusan_query = $conn->query("
        SELECT pj.id, pl.id as pelajaran_id, pl.nama_pelajaran 
        FROM penjurusan pj
        JOIN pelajaran pl ON pj.pelajaran_id = pl.id
        WHERE pj.lokasi_id = $id
    ");
    $existing_jurusan = [];
    while ($row = $jurusan_query->fetch_assoc()) {
        $existing_jurusan[$row['pelajaran_id']] = $row['nama_pelajaran'];
    }
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
        .jurusan-section {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .jurusan-list {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .form-check {
            margin-bottom: 8px;
            padding-left: 1.5em;
        }
        .form-check-input {
            margin-top: 0.2em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Data</h1>
        <hr>
        
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

            <!-- Dropdown kecamatan -->
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

            <!-- Section untuk jurusan -->
            <div class="jurusan-section mb-4">
                <h3>Jurusan</h3>
                
                <div class="mb-3">
                    <label class="form-label">Pilih Jurusan yang Tersedia:</label>
                    <div class="jurusan-list">
                        <?php if (empty($all_pelajaran)): ?>
                            <div class="text-muted">Tidak ada jurusan tersedia</div>
                        <?php else: ?>
                            <?php foreach ($all_pelajaran as $id_pelajaran => $nama_pelajaran): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                        id="jurusan_<?= $id_pelajaran ?>" 
                                        name="jurusan[]" 
                                        value="<?= $id_pelajaran ?>"
                                        <?= isset($existing_jurusan[$id_pelajaran]) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="jurusan_<?= $id_pelajaran ?>">
                                        <?= htmlspecialchars($nama_pelajaran) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="new_jurusan" class="form-label">Tambahkan Jurusan Baru:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="new_jurusan" name="new_jurusan" 
                            placeholder="Masukkan nama jurusan baru">
                        <button type="button" class="btn btn-outline-secondary" onclick="addNewJurusan()">
                            Tambah
                        </button>
                    </div>
                    <small class="text-muted">Gunakan ini untuk menambahkan jurusan yang belum ada dalam daftar</small>
                </div>
            </div>

            <!-- Tombol simpan dan kembali -->
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-danger" onclick="deleteLocation(<?= $id ?>)">Hapus</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>

        function deleteLocation(id) {
            if (confirm('Apakah Anda yakin ingin menghapus lokasi ini?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_location.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            alert('Lokasi berhasil dihapus.');
                            window.location.href = 'dashboard.php'; // Redirect ke dashboard setelah hapus
                        } else {
                            alert('Terjadi kesalahan saat menghapus lokasi.');
                        }
                    }
                };
                xhr.send('id=' + encodeURIComponent(id));
            }
        }

        function addNewJurusan() {
            const newJurusanInput = document.getElementById('new_jurusan');
            const newJurusanName = newJurusanInput.value.trim();
            
            if (newJurusanName === '') {
                alert('Silakan masukkan nama jurusan terlebih dahulu');
                return;
            }
            
            // Cek apakah jurusan sudah ada dalam daftar checkbox
            const checkboxes = document.querySelectorAll('input[name="jurusan[]"]');
            let alreadyExists = false;
            
            checkboxes.forEach(checkbox => {
                const label = document.querySelector(`label[for="${checkbox.id}"]`);
                if (label.textContent.trim() === newJurusanName) {
                    alreadyExists = true;
                    checkbox.checked = true;
                    label.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    label.parentElement.style.backgroundColor = '#f8f9fa';
                    setTimeout(() => {
                        label.parentElement.style.backgroundColor = '';
                    }, 2000);
                }
            });
            
            if (alreadyExists) {
                alert('Jurusan ini sudah ada dalam daftar dan telah dipilih');
                newJurusanInput.value = '';
                return;
            }
            
            // Konfirmasi sebelum menambahkan
            if (confirm(`Tambahkan jurusan "${newJurusanName}"?`)) {
                // Submit form untuk menambahkan jurusan baru
                document.querySelector('form').submit();
            }
        }
    </script>
</body>
</html>