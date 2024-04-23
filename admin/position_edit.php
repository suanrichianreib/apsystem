<?php
    include 'includes/session.php';

    if(isset($_POST['edit'])){
        $id = $_POST['id'];
        $title = $_POST['title'];
        $rate = $_POST['rate'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("UPDATE position SET description = ?, rate = ? WHERE id = ?");
        
        // Bind parameters and execute the statement
        $stmt->bind_param("ssi", $title, $rate, $id);
        if($stmt->execute()){
            $_SESSION['success'] = 'Position updated successfully';
        }
        else{
            $_SESSION['error'] = $conn->error;
        }
        $stmt->close();
    }
    else{
        $_SESSION['error'] = 'Fill up edit form first';
    }

    header('location:position.php');
?>
