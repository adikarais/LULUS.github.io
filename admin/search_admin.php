<?php

// Menghubungkan file ini dengan file connect.php untuk koneksi ke database
include '../components/connect.php';

// Ensure $conn is defined and connected
if (!isset($conn)) {
    die("Database connection error.");
}

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

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

   <!-- Menyertakan file CSS kustom -->
   <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php 
// Menyertakan header pengguna
include '../components/admin_header.php'; 
?>

<!-- Bagian untuk menampilkan hasil pencarian dimulai -->

<section class="courses">

<h1 class="heading">search results</h1>

<div class="box-container">

   <?php
      if(isset($_POST['search_course']) or isset($_POST['search_course_btn'])){
         $search_course = isset($_POST['search_course']) ? $_POST['search_course'] : '';
         $select_courses = $conn->prepare("SELECT * FROM `lokasi` WHERE nama_tempat LIKE :search_param");
         $search_param = "%{$search_course}%";
         $select_courses->bindValue(':search_param', $search_param, PDO::PARAM_STR);
         $select_courses->execute();
         if($select_courses->rowCount() > 0){
            while($row = $select_courses->fetch(PDO::FETCH_ASSOC)){
               echo "<div class='box' onclick=\"location.href='detail.php?id={$row['id']}'\">{$row['nama_tempat']}</div>";
            }
         } else {
            echo "<div class='box'>No results found.</div>";
         }
         $select_courses = null;
      }
   ?>

</div>

</section>

<!-- Bagian untuk menampilkan hasil pencarian berakhir -->

<?php include '../components/footer.php'; ?>

<!-- Menyertakan file JavaScript kustom -->
<script src="../js/script.js"></script>
   
</body>
</html>