<?php
include '../components/connect.php';

if(isset($_POST['submit'])){

   $id = unique_id();
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $profession = filter_var($_POST['profession'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ?");
   $select_tutor->execute([$email]);
   
   if($select_tutor->rowCount() > 0){
      $message[] = 'Email sudah digunakan!';
   }else{
      if($pass != $cpass){
         $message[] = 'Konfirmasi kata sandi tidak cocok!';
      }else{
         $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, profession, email, password, image) VALUES(?,?,?,?,?,?)");
         $insert_tutor->execute([$id, $name, $profession, $email, $cpass, $rename]);
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'Pendaftaran tutor berhasil! Silakan login sekarang.';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Daftar Admin Baru</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- File CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body style="padding-left: 0;">

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message form">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- Form Registrasi -->
<section class="form-container">
   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Daftar Admin Baru</h3>
      <div class="flex">
         <div class="col">
            <p>Nama Lengkap <span>*</span></p>
            <input type="text" name="name" placeholder="Masukkan nama lengkap" maxlength="50" required class="box">

            <p>Jabatan <span>*</span></p>
            <select name="profession" class="box" required>
               <option value="" disabled selected>-- Pilih Jabatan --</option>
               <option value="developer">Developer</option>
               <option value="designer">Desainer</option>
               <option value="pemilik">Pemilik</option>
            </select>

            <p>Email <span>*</span></p>
            <input type="email" name="email" placeholder="Masukkan email aktif" maxlength="40" required class="box">
         </div>

         <div class="col">
            <p>Kata Sandi <span>*</span></p>
            <input type="password" name="pass" placeholder="Masukkan kata sandi" maxlength="20" required class="box">

            <p>Konfirmasi Kata Sandi <span>*</span></p>
            <input type="password" name="cpass" placeholder="Ulangi kata sandi" maxlength="20" required class="box">

            <p>Unggah Foto Profil <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
         </div>
      </div>

      <p class="link">Sudah punya akun? <a href="admin_list.php">Kembali</a></p>
      <input type="submit" name="submit" value="Tambah" class="btn">
   </form>
</section>

<script>
let darkMode = localStorage.getItem('dark-mode');
let body = document.body;

const enableDarkMode = () =>{
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}

const disableDarkMode = () =>{
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}

if(darkMode === 'enabled'){
   enableDarkMode();
}else{
   disableDarkMode();
}
</script>

</body>
</html>
