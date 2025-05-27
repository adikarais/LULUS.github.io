<?php
include '../components/connect.php';

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan!";
    exit;
}

$id = $_GET['id'];

// Ambil data admin berdasarkan ID dari tabel tutors
$stmt = $conn->prepare("SELECT * FROM tutors WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Data admin tidak ditemukan!";
    exit;
}

// Proses ketika form disubmit
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $profession = $_POST['profession'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE tutors SET name = ?, password = ?, email = ?, profession = ? WHERE id = ?");
        $success = $update->execute([$name, $password, $email, $profession, $id]);
    } else {
        $update = $conn->prepare("UPDATE tutors SET name = ?, email = ?, profession = ? WHERE id = ?");
        $success = $update->execute([$name, $email, $profession, $id]);
    }

    if ($success) {
        echo "<script>alert('Data admin berhasil diupdate'); window.location='admin_list.php';</script>";
    } else {
        echo "Gagal mengupdate data!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Admin</h2>
    <form method="post">
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
        </div>
        <div class="form-group">
            <label>Password (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label>Profession</label>
            <select name="profession" class="form-control" required>
                <option value="developer" <?= ($data['profession'] === 'developer') ? 'selected' : '' ?>>Developer</option>
                <option value="pemilik" <?= ($data['profession'] === 'pemilik') ? 'selected' : '' ?>>pemilik</option>
                <option value="desginer" <?= ($data['profession'] === 'desginer') ? 'selected' : '' ?>>Desginer</option>
            </select>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="admin_list.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
