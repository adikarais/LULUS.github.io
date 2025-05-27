<?php
include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
   exit();
}

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Validasi ID
    if(!is_numeric($id)) {
        die("ID tidak valid");
    }
    
    // Hapus data dari database
    $delete = $conn->prepare("DELETE FROM `lokasi` WHERE id = ?");
    $delete->execute([$id]);
    
    if($delete->rowCount() > 0) {
        echo "Lokasi berhasil dihapus";
    } else {
        echo "Gagal menghapus lokasi atau lokasi tidak ditemukan";
    }
} else {
    echo "ID tidak diterima";
}
?>