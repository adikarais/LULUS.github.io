<?php
include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   header('location:login.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   
</head>
<style>
   .search-results .box-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, 35rem);
    gap: 1.5rem;
    align-items: flex-start;
    justify-content: center;
   }
   .search-results .box-container .box {
    border-radius: 0.5rem;
    background-color: var(--white);
    padding: 2rem;
   }
</style>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="search-results">
   <h1 class="heading">Hasil Pencarian Lokasi</h1>

   <div class="box-container">
      <?php
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search_course = $_POST['search'];
         $select_courses = $conn->prepare("SELECT * FROM `lokasi` WHERE nama_tempat LIKE :search_param");
         $search_param = "%{$search_course}%";
         $select_courses->bindValue(':search_param', $search_param, PDO::PARAM_STR);
         $select_courses->execute();
         
         if($select_courses->rowCount() > 0){
            while($row = $select_courses->fetch(PDO::FETCH_ASSOC)){
               echo "<div class='box' onclick=\"location.href='detail.php?id={$row['id']}'\">";
               echo "<h3>{$row['nama_tempat']}</h3>";
               echo "</div>";
            }
         } else {
            echo "<div class='box'>Tidak ada lokasi ditemukan!</div>";
         }
         $select_courses = null;
      } else {
         echo "<div class='box'>Silakan masukkan nama tempat yang ingin dicari!</div>";
      }
      ?>
   </div>
</section>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
</body>
</html>