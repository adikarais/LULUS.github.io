<?php
include '../components/connect.php';

// Check if user is logged in
if(!isset($_COOKIE['tutor_id'])){
    header('location:login.php');
    exit();
}

// Check if ID parameter exists and is valid
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = intval($_GET['id']);
    
    try {
        // Begin transaction for multiple related deletions if needed
        $conn->beginTransaction();
        
        // First check if the location exists and user has permission to delete it
        $check_stmt = $conn->prepare("SELECT l.id FROM lokasi l 
                                    JOIN penjurusan pj ON l.id = pj.lokasi_id
                                    WHERE l.id = ?");
        $check_stmt->execute([$id]);
        $location = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$location){
            // Location doesn't exist or no permission
            $_SESSION['error_msg'] = "Location not found or you don't have permission to delete it.";
            header('location: locations.php');
            exit();
        }
        
        // First delete from penjurusan table (foreign key constraint)
        $delete_penjurusan = $conn->prepare("DELETE FROM penjurusan WHERE lokasi_id = ?");
        $delete_penjurusan->execute([$id]);
        
        // Then delete from lokasi table
        $delete_lokasi = $conn->prepare("DELETE FROM lokasi WHERE id = ?");
        $delete_lokasi->execute([$id]);
        
        // Commit the transaction
        $conn->commit();
        
        $_SESSION['success_msg'] = "Location deleted successfully!";
        header('location: locations.php');
        exit();
        
    } catch(PDOException $e) {
        // Roll back the transaction if something failed
        $conn->rollBack();
        
        // Log the error (in a real application, you'd log to a file)
        error_log("Delete Error: " . $e->getMessage());
        
        $_SESSION['error_msg'] = "An error occurred while deleting the location. Please try again.";
        header('location: locations.php');
        exit();
    }
} else {
    // Invalid or missing ID parameter
    $_SESSION['error_msg'] = "Invalid request.";
    header('location: locations.php');
    exit();
}
?>

<?php
if(isset($_SESSION['success_msg'])){
    echo '<div class="alert alert-success">'.$_SESSION['success_msg'].'</div>';
    unset($_SESSION['success_msg']);
}
if(isset($_SESSION['error_msg'])){
    echo '<div class="alert alert-danger">'.$_SESSION['error_msg'].'</div>';
    unset($_SESSION['error_msg']);
}
?>
