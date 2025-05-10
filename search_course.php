<?php

include 'components/connect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>courses</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- courses section starts  -->

<section class="courses">

   <h1 class="heading">search results</h1>

   <div class="box-container">

      <?php
         if(isset($_POST['search_course']) or isset($_POST['search_course_btn'])){
            $search_course = $_POST['search_course'];
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

<!-- courses section ends -->

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>