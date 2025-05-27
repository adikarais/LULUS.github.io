<?php
include '../components/connect.php';

// Cek apakah admin sudah login
if (isset($_COOKIE['tutor_id'])) {
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   header('location:login.php');
   exit;
}

// Proses hapus tutor
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // Ambil dan hapus file gambar
   $select_image = $conn->prepare("SELECT image FROM tutors WHERE id = ?");
   $select_image->execute([$delete_id]);
   if ($select_image->rowCount() > 0) {
      $fetch_image = $select_image->fetch(PDO::FETCH_ASSOC);
      $image_path = '../uploaded_files/' . $fetch_image['image'];
      if (file_exists($image_path)) {
         unlink($image_path);
      }
   }

   // Hapus data tutor dari database
   $delete_tutor = $conn->prepare("DELETE FROM tutors WHERE id = ?");
   $delete_tutor->execute([$delete_id]);

   header('location:admin_list.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard Admin</title>

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
         integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <!-- Leaflet CSS -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
         integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
         crossorigin="" />

   <style>
      img.profile-pic {
         width: 60px;
         height: 60px;
         object-fit: cover;
         border-radius: 50%;
      }
      .table-bordered td {
         border: 1px solid #dee2e6;
         text-align: center;
         vertical-align: middle;
      }
   </style>
</head>
<body class="bg-light">

<?php include '../components/admin_header.php'; ?>

<div class="container py-5">
   <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Daftar Admin</h2>
      <a href="register.php" class="p-3 mb-2 bg-primary text-white">+ Tambah Admin Baru</a>
   </div>

   <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center bg-white shadow-sm">
         <thead class="table-dark">
            <tr>
               <th scope="col">Foto</th>
               <th scope="col">Nama</th>
               <th scope="col">Profesi</th>
               <th scope="col">Email</th>
               <th scope="col">Aksi</th>
            </tr>
         </thead>
         <tbody>
            <?php
            $select_tutors = $conn->prepare("SELECT * FROM tutors ORDER BY name ASC");
            $select_tutors->execute();

            if ($select_tutors->rowCount() > 0) {
               while ($row = $select_tutors->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
               <td><img src="../uploaded_files/<?= htmlspecialchars($row['image']); ?>" class="profile-pic" alt="Foto Admin"></td>
               <td><?= htmlspecialchars($row['name']); ?></td>
               <td><?= htmlspecialchars($row['profession']); ?></td>
               <td><?= htmlspecialchars($row['email']); ?></td>
               <td>
                  <a href="edit_admin.php?id=<?= $row['id']; ?>" class="p-2 btn btn-primary btn-sm">Ubah</a>
                  <a href="admin_list.php?delete=<?= $row['id']; ?>"
                     onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?');"
                     class="p-2 btn btn-danger btn-sm">Hapus</a>
               </td>
            </tr>
            <?php
               }
            } else {
               echo '<tr><td colspan="5">Tidak ada admin yang ditemukan.</td></tr>';
            }
            ?>
         </tbody>
      </table>
   </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Script -->
<script src="../js/admin_script.js"></script>

</body>
</html>
