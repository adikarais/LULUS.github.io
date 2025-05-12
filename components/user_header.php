
<!-- Header Section -->
<header class="header">

   <section class="flex">

      <!-- Logo -->
      <a href="home.php" class="logo">Educa.</a>

      <!-- Form Pencarian -->
      <form action="search_course.php" method="post" class="search-form">
         <input type="text" name="search_course" placeholder="search courses..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_course_btn"></button>
      </form>

      <!-- Ikon Navigasi -->
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <!-- Profil Pengguna -->
      <div class="profile">
         <h3>please login or register</h3>
         <div class="flex-btn">
            <a href="admin/login.php" class="option-btn">login</a>
            <a href="admin/register.php" class="option-btn">register</a>
         </div>
      </div>

   </section>

</header>
<!-- Header Section Ends -->

<!-- Sidebar Section -->
<div class="side-bar">

   <!-- Tombol Tutup Sidebar -->
   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <!-- Navigasi Sidebar -->
   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="playlists.php"><i class="fa-solid fa-bars-staggered"></i><span>List Sekolah</span></a>
      <a href="json.php"><i class="fas fa-graduation-cap"></i><span>playlists</span></a>
   </nav>

</div>
<!-- Sidebar Section Ends -->
