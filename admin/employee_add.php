<?php
    include 'includes/session.php';

    if(isset($_POST['add'])){
        $employee_id = $_POST['employee_id'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $address = $_POST['address'];
        $birthdate = $_POST['birthdate'];
        $contact = $_POST['contact'];
        $gender = $_POST['gender'];
        $position = $_POST['position'];
        $schedule = $_POST['schedule'];
        $filename = $_FILES['photo']['name'];
        
        // Check if the employee ID already exists
        $check_sql = "SELECT * FROM employees WHERE employee_id = '$employee_id'";
        $check_result = $conn->query($check_sql);
        if($check_result->num_rows > 0){
            $_SESSION['error'] = 'Employee ID already exists';
            header('location: employee.php'); // Redirect back to the form page
            exit(); // Exit the script
        }
        
        // If the employee ID doesn't exist, proceed with the insertion
        if(!empty($filename)){
            move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);   
        }
        
        $sql = "INSERT INTO employees (employee_id, firstname, middlename, lastname, address, birthdate, contact_info, gender, position_id, schedule_id, photo, created_on) VALUES ('$employee_id', '$firstname', '$middlename', '$lastname', '$address', '$birthdate', '$contact', '$gender', '$position', '$schedule', '$filename', NOW())";
        if($conn->query($sql)){
            $_SESSION['success'] = 'Employee added successfully';
        }
        else{
            $_SESSION['error'] = $conn->error;
        }

    }
    else{
        $_SESSION['error'] = 'Fill up add form first';
    }

    header('location: employee.php');
?>
