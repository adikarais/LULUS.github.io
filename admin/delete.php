<?php
session_start();
include '../components/connect.php';

// Check if user is logged in
if(!isset($_COOKIE['tutor_id'])){
    $_SESSION['error'] = "Anda harus login terlebih dahulu!";
    header('location:../login.php');
    exit();
}

// Check if request method is POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $_SESSION['error'] = "Metode request tidak valid!";
    header('location: locations.php');
    exit();
}

// Validate and sanitize input
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if(!$id || $id <= 0){
    $_SESSION['error'] = "ID lokasi tidak valid!";
    header('location: locations.php');
    exit();
}

try {
    // Begin transaction
    $conn->beginTransaction();
    
    // Check if location exists
    $check = $conn->prepare("SELECT id FROM lokasi WHERE id = ?");
    $check->execute([$id]);
    
    if($check->rowCount() === 0){
        $_SESSION['error'] = "Lokasi tidak ditemukan!";
        header('location: locations.php');
        exit();
    }
    
    // First delete from penjurusan (child table)
    $delete_penjurusan = $conn->prepare("DELETE FROM penjurusan WHERE lokasi_id = ?");
    $delete_penjurusan->execute([$id]);
    
    // Then delete from lokasi (parent table)
    $delete_lokasi = $conn->prepare("DELETE FROM lokasi WHERE id = ?");
    $delete_lokasi->execute([$id]);
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['success'] = "Lokasi berhasil dihapus!";
    header('location: locations.php');
    exit();

} catch(PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    
    // Log the error (in production, log to a file)
    error_log("Delete Error: " . $e->getMessage());
    
    $_SESSION['error'] = "Terjadi kesalahan saat menghapus lokasi. Silakan coba lagi.";
    header('location: locations.php');
    exit();
}
?>