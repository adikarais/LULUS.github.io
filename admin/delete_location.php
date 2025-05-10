<?php
$mysqli = mysqli_connect('localhost', 'root', '', 'ta_wgis');

// Validate the 'id' parameter
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM lokasi WHERE id = $id";

    if (mysqli_query($mysqli, $query)) {
        echo "Lokasi berhasil dihapus.";
    } else {
        http_response_code(500);
        echo "Terjadi kesalahan saat menghapus lokasi.";
    }
} else {
    http_response_code(400);
    echo "ID tidak valid.";
}
?>