<?php
    include 'includes/session.php';

    if(isset($_POST['edit'])){
        $empid = $_POST['id'];
        $employee_id = $_POST['employee_id']; // Added line for employee ID
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $address = $_POST['address'];
        $birthdate = $_POST['birthdate'];
        $contact = $_POST['contact'];
        $gender = $_POST['gender'];
        $position = $_POST['position'];
        $schedule = $_POST['schedule'];

        $sql = "UPDATE employees SET 
            employee_id = '$employee_id', 
            firstname = '$firstname', 
            middlename = '$middlename',  
            lastname = '$lastname', 
            address = '$address', 
            birthdate = '$birthdate', 
            contact_info = '$contact', 
            gender = '$gender', 
            position_id = '$position', 
            schedule_id = '$schedule' 
            WHERE id = '$empid'";
        if($conn->query($sql)){
            $_SESSION['success'] = 'Employee updated successfully';
        }
        else{
            $_SESSION['error'] = $conn->error;
        }

    }
    else{
        $_SESSION['error'] = 'Select employee to edit first';
    }

    header('location: employee.php');
?>
