<?php

// Menghubungkan file ini dengan file connect.php untuk koneksi database
include 'components/connect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>courses</title>

   <!-- Menyertakan link ke Font Awesome untuk ikon -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Menyertakan file CSS custom -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php 
// Menyertakan header pengguna dari file user_header.php
include 'components/user_header.php'; 
?>

<!-- Bagian untuk menampilkan hasil pencarian kursus -->
<section class="courses">

   <h1 class="heading">search results</h1>

   <div class="box-container">

      <?php
         // Mengecek apakah tombol pencarian telah ditekan
         if(isset($_POST['search_course']) or isset($_POST['search_course_btn'])){
            // Mengambil input pencarian dari form
            $search_course = $_POST['search_course'];

            // Menyiapkan query untuk mencari data di tabel `lokasi` berdasarkan nama tempat
            $select_courses = $conn->prepare("SELECT * FROM `lokasi` WHERE nama_tempat LIKE :search_param");

            // Menambahkan wildcard (%) untuk pencarian fleksibel
            $search_param = "%{$search_course}%";

            // Mengikat parameter pencarian ke query
            $select_courses->bindValue(':search_param', $search_param, PDO::PARAM_STR);

            // Menjalankan query
            $select_courses->execute();

            // Mengecek apakah ada hasil dari query
            if($select_courses->rowCount() > 0){
               // Menampilkan setiap hasil pencarian
               while($row = $select_courses->fetch(PDO::FETCH_ASSOC)){
                  echo "<div class='box' onclick=\"location.href='detail.php?id={$row['id']}'\">{$row['nama_tempat']}</div>";
               }
            } else {
               // Menampilkan pesan jika tidak ada hasil ditemukan
               echo "<div class='box'>No results found.</div>";
            }

            // Menutup statement untuk membebaskan sumber daya
            $select_courses = null;
         }
      ?>

   </div>

</section>

<!-- Akhir dari bagian pencarian kursus -->

<?php 
// Menyertakan footer dari file footer.php
include 'components/footer.php'; 
?>

<!-- Menyertakan file JavaScript custom -->
<script src="js/script.js"></script>
   
</body>
</html>