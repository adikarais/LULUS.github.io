<?php
session_start();
include '../components/connect.php';

// Authentication check
if(!isset($_COOKIE['tutor_id'])){
    header('location:../login.php');
    exit();
}
$tutor_id = $_COOKIE['tutor_id'];

// Filter handling
$filter_pelajaran = isset($_GET['pelajaran_id']) ? intval($_GET['pelajaran_id']) : 0;
$filter_lokasi = isset($_GET['id_lokasi']) ? intval($_GET['id_lokasi']) : 0;
$filter_categories = isset($_GET['category']) ? $_GET['category'] : [];

// Build query conditions
$conditions = [];
$params = [];

if ($filter_pelajaran > 0) {
    $conditions[] = "pj.pelajaran_id = ?";
    $params[] = $filter_pelajaran;
}

if ($filter_lokasi > 0) {
    $conditions[] = "pj.lokasi_id = ?";
    $params[] = $filter_lokasi;
}

if (!empty($filter_categories)) {
    $placeholders = implode(',', array_fill(0, count($filter_categories), '?'));
    $conditions[] = "l.kategori IN ($placeholders)";
    $params = array_merge($params, $filter_categories);
}

$where = empty($conditions) ? "" : "WHERE " . implode(" AND ", $conditions);

// Main query
$query = "
SELECT l.id, l.nama_tempat, l.lat_long, l.kategori, l.keterangan, p.nama_pelajaran
FROM penjurusan pj
JOIN lokasi l ON pj.lokasi_id = l.id
JOIN pelajaran p ON pj.pelajaran_id = p.id
$where
ORDER BY l.nama_tempat
";

$statement = $conn->prepare($query);
$statement->execute($params);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

// Get all subjects for filter dropdown
$pelajaran_result = $conn->query("SELECT id, nama_pelajaran FROM pelajaran ORDER BY nama_pelajaran");
$all_pelajaran = $pelajaran_result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lokasi Sekolah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style>
        .playlists {
            padding: 2rem;
        }
        .table-responsive {
            margin-bottom: 2rem;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlists">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-9">
            <h1 class="heading">Daftar Lokasi Sekolah</h1>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Koordinat</th>
                            <th>Nama Sekolah</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Pelajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($result) > 0): ?>
                            <?php foreach ($result as $location): ?>
                            <tr>
                                <td><?= htmlspecialchars($location['id']); ?></td>
                                <td><?= htmlspecialchars($location['lat_long']); ?></td>
                                <td><?= htmlspecialchars($location['nama_tempat']); ?></td>
                                <td><?= htmlspecialchars($location['kategori']); ?></td>
                                <td><?= htmlspecialchars($location['keterangan']); ?></td>
                                <td><?= htmlspecialchars($location['nama_pelajaran']); ?></td>
                                <td class="action-buttons">
                                    <div class="d-flex flex-column">
                                        <a href="detail.php?id=<?= $location['id']; ?>" class="btn btn-primary btn-sm mb-2">Detail</a>
                                        <form method="POST" action="delete.php">
                                            <input type="hidden" name="id" value="<?= $location['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus lokasi ini?');">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada lokasi yang ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-3">
            <div class="filter-section">
                <h5>Filter</h5>
                <form id="filterForm" method="GET">
                    <div class="form-group">
                        <label>Kategori:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="SMA" id="filterSMA" name="category[]" 
                                <?= in_array('SMA', $filter_categories) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="filterSMA">SMA</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="SMK" id="filterSMK" name="category[]"
                                <?= in_array('SMK', $filter_categories) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="filterSMK">SMK</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="MA" id="filterMA" name="category[]"
                                <?= in_array('MA', $filter_categories) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="filterMA">MA</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pelajaranFilter">Pelajaran:</label>
                        <select id="pelajaranFilter" name="pelajaran_id" class="form-control">
                            <option value="0">Semua Pelajaran</option>
                            <?php foreach ($all_pelajaran as $pelajaran): ?>
                            <option value="<?= $pelajaran['id'] ?>" 
                                <?= ($pelajaran['id'] == $filter_pelajaran) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pelajaran['nama_pelajaran']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-3">Terapkan Filter</button>
                    <?php if($filter_pelajaran || !empty($filter_categories)): ?>
                        <a href="?" class="btn btn-outline-secondary btn-block mt-2">Reset Filter</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

</body>
</html>