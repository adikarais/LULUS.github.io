<?php
// Koneksi ke database
$connect = mysqli_connect('localhost', 'root', '', 'ta_wgis');

// Periksa koneksi
if (!$connect) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Set variabel dari input POST
$lat_long = $_POST['latlong'] ?? '';
$nama_tempat = $_POST['nama_tempat'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$link_sekolah = $_POST['link_sekolah'] ?? '';
$link_lokasi = $_POST['link_lokasi'] ?? '';
$nama_kecamatan = $_POST['nama_kecamatan'] ?? '';

// Input data ke database
$query = "INSERT INTO lokasi (lat_long, nama_tempat, kategori, keterangan, link_sekolah, link_lokasi, nama_kecamatan) 
          VALUES ('$lat_long', '$nama_tempat', '$kategori', '$keterangan', '$link_sekolah', '$link_lokasi', '$nama_kecamatan')";

if (mysqli_query($connect, $query)) {
    // Redirect ke halaman dashboard
    header("Location: dashboard.php");
    exit();
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($connect);
}

// Tutup koneksi
mysqli_close($connect);
?>