<?php
    include 'includes/session.php';

    if(isset($_POST['add'])){
        $title = $_POST['title'];
        $rate = $_POST['rate'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO position (description, rate) VALUES (?, ?)");
        
        // Bind parameters and execute the statement
        $stmt->bind_param("ss", $title, $rate);
        if($stmt->execute()){
            $_SESSION['success'] = 'Position added successfully';
        }
        else{
            $_SESSION['error'] = $conn->error;
        }
        $stmt->close();
    }   
    else{
        $_SESSION['error'] = 'Fill up add form first';
    }

    header('location: position.php');
?>
