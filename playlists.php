<?php

include 'components/connect.php';

$filter_pelajaran = isset($_GET['pelajaran_id']) ? intval($_GET['pelajaran_id']) : 0;
$filter_lokasi = isset($_GET['id_lokasi']) ? intval($_GET['id_lokasi']) : 0;
$conditions = [];

if ($filter_pelajaran > 0) {
    $conditions[] = "pj.pelajaran_id = " . $filter_pelajaran;
}
if ($filter_lokasi > 0) {
    $conditions[] = "pj.lokasi_id = " . $filter_lokasi;
}

$where = "";
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

$query = "
SELECT l.id, l.nama_tempat, l.lat_long, l.kategori, l.keterangan, p.nama_pelajaran
FROM penjurusan pj
JOIN lokasi l ON pj.lokasi_id = l.id
JOIN pelajaran p ON pj.pelajaran_id = p.id
$where
";

$statement = $conn->prepare($query);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlists</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="playlists">
   <div class="row">
      <div class="col-md-9">
         <div class="table-responsive">
            <table class="table table-striped">
               <thead class="thead-dark">
                  <tr>
                     <th>ID</th>
                     <th>Latitude</th>
                     <th>Nama Sekolah</th>
                     <th>Kategori</th>
                     <th>Keterangan</th>
                     <th>Pelajaran</th>
                     <th>Actions</th>
                  </tr>
               </thead>
               <tbody id="tableBody">
                  <?php if(count($result) > 0): ?>
                     <?php foreach ($result as $fetch_location): ?>
                     <tr data-category="<?= htmlspecialchars($fetch_location['kategori']); ?>">
                        <td><?= $fetch_location['id']; ?></td>
                        <td><?= htmlspecialchars($fetch_location['lat_long']); ?></td>
                        <td><?= htmlspecialchars($fetch_location['nama_tempat']); ?></td>
                        <td><?= htmlspecialchars($fetch_location['kategori']); ?></td>
                        <td><?= htmlspecialchars($fetch_location['keterangan']); ?></td>
                        <td><?= htmlspecialchars($fetch_location['nama_pelajaran']); ?></td>
                        <td>
                           <a href="detail.php?id=<?= $fetch_location['id']; ?>" class="btn btn-primary btn-sm">Detail</a>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <tr><td colspan="7" class="text-center">No locations found!</td></tr>
                  <?php endif; ?>
               </tbody>
            </table>
         </div>
      </div>

      <div class="col-md-3">
         <h5>Filter by Category</h5>
         <form id="filterForm" method="GET">
            <div class="form-check">
               <input class="form-check-input" type="checkbox" value="SMA" id="filterSMA" name="category[]">
               <label class="form-check-label" for="filterSMA">SMA</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" value="SMK" id="filterSMK" name="category[]">
               <label class="form-check-label" for="filterSMK">SMK</label>
            </div>
            <div class="form-check">
               <input class="form-check-input" type="checkbox" value="MA" id="filterMA" name="category[]">
               <label class="form-check-label" for="filterMA">MA</label>
            </div>

            <h5 class="mt-3">Filter Pelajaran:</h5>
            <select name="pelajaran_id" onchange="submitWithCategories()" class="form-control">
               <option value="0">-- Semua Pelajaran --</option>
               <?php
               $pelajaran_result = $conn->query("SELECT id, nama_pelajaran FROM pelajaran");
               while ($pelajaran = $pelajaran_result->fetch(PDO::FETCH_ASSOC)):
               ?>
               <option value="<?= $pelajaran['id'] ?>" <?= ($pelajaran['id'] == $filter_pelajaran) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($pelajaran['nama_pelajaran']) ?>
               </option>
               <?php endwhile; ?>
            </select>

            <button type="button" class="btn btn-primary btn-sm mt-2" id="applyFilter">Apply Filter</button>
         </form>
      </div>
   </div>
</section>

<script>
   document.getElementById('applyFilter').addEventListener('click', function() {
      const selectedCategories = Array.from(document.querySelectorAll('input[name="category[]"]:checked')).map(cb => cb.value);
      const rows = document.querySelectorAll('#tableBody tr');

      rows.forEach(row => {
         const category = row.getAttribute('data-category');
         if (selectedCategories.length === 0 || selectedCategories.includes(category)) {
            row.style.display = '';
         } else {
            row.style.display = 'none';
         }
      });
   });

   function submitWithCategories() {
      document.querySelectorAll('input[name="category[]"]').forEach(input => input.remove());

      const selectedCategories = Array.from(document.querySelectorAll('input[name="category[]"]:checked')).map(cb => cb.value);
      const form = document.getElementById('filterForm');
      selectedCategories.forEach(category => {
         const input = document.createElement('input');
         input.type = 'hidden';
         input.name = 'category[]';
         input.value = category;
         form.appendChild(input);
      });
      form.submit();
   }
</script>

<?php include 'components/footer.php'; ?>
<script src="js/admin_script.js"></script>

</body>
</html>