<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '
      <div class="message">
         <span>' . $msg . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">
   <section class="flex">
      <a href="dashboard.php" class="logo">Admin.</a>

      <form action="../admin/search_admin.php" method="post" class="search-form">
         <input type="text" name="search" placeholder="search here..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <?php
         if (isset($tutor_id) && !empty($tutor_id)) {
            $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_profile->execute([$tutor_id]);
            if ($select_profile->rowCount() > 0) {
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
               <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
               <h3><?= $fetch_profile['name']; ?></h3>
               <span><?= $fetch_profile['profession']; ?></span>
               <a href="../components/admin_logout.php" onclick="return confirm('Logout from this website?');" class="delete-btn">Logout</a>
         <?php
            }
         } else {
         ?>
            <h3>Please login or register</h3>
            <div class="flex-btn">
               <a href="login.php" class="option-btn">Login</a>
               <a href="register.php" class="option-btn">Register</a>
            </div>
         <?php } ?>
      </div>
   </section>
</header>

<div class="side-bar">
   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <?php
      if (isset($tutor_id) && !empty($tutor_id)) {
         $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
         $select_profile->execute([$tutor_id]);
         if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>
            <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
            <h3><?= $fetch_profile['name']; ?></h3>
            <span><?= $fetch_profile['profession']; ?></span>
            <a href="profile.php" class="btn">View Profile</a>
      <?php
         }
      } else {
      ?>
         <h3>Please login or register</h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
      <?php } ?>
   </div>

   <nav class="navbar">
      <a href="dashboard.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="playlists.php"><i class="fa-solid fa-bars-staggered"></i><span>Playlists</span></a>
      <a href="../components/admin_logout.php" onclick="return confirm('Logout from this website?');"><i class="fas fa-right-from-bracket"></i><span>Logout</span></a>
   </nav>
</div>
