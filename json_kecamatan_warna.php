<?php
header('Content-Type: application/json');

// Koneksi database
$mysqli = new mysqli('localhost', 'root', '', 'ta_wgis');
if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Koneksi database gagal: ' . $mysqli->connect_error]);
    exit;
}

// Query jumlah sekolah per kecamatan
$query = "SELECT nama_kecamatan, COUNT(*) AS jumlah FROM lokasi GROUP BY nama_kecamatan";
$result = $mysqli->query($query);

if (!$result) {
    echo json_encode(['error' => 'Gagal mengambil data: ' . $mysqli->error]);
    exit;
}

$kecamatanData = [];
while ($row = $result->fetch_assoc()) {
    $kecamatanData[] = [
        'nama' => trim($row['nama_kecamatan']),
        'jumlah' => (int)$row['jumlah']
    ];
}

// Tentukan warna berdasarkan jumlah sekolah
$finalColors = [];
foreach ($kecamatanData as $kecamatan) {
    $jumlah = $kecamatan['jumlah'];
    
    if ($jumlah <= 1) {
    $warna = "#caf0f8"; // Biru sangat muda
} elseif ($jumlah <= 2) {
    $warna = "#90e0ef"; // Biru muda
} elseif ($jumlah <= 3) {
    $warna = "#48cae4"; // Biru medium
} elseif ($jumlah <= 4) {
    $warna = "#0077b6"; // Biru tua
} else {
    $warna = "#03045e"; // Biru sangat tua
}
    
    $finalColors[$kecamatan['nama']] = $warna;
}

echo json_encode($finalColors, JSON_PRETTY_PRINT);
?>