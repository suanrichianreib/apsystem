<?php
    include 'includes/session.php';

    if(isset($_POST['add'])){
        $time_in = $_POST['time_in'];
        $time_in = date('H:i:s', strtotime($time_in));
        $time_out = $_POST['time_out'];
        $time_out = date('H:i:s', strtotime($time_out));
        $auto_time = isset($_POST['auto_time']) ? $_POST['auto_time'] : 0; // Check if 'Auto Time' is set, default to 0 if not

        $sql = "INSERT INTO schedules (time_in, time_out, auto_time) VALUES ('$time_in', '$time_out', '$auto_time')";
        if($conn->query($sql)){
            $_SESSION['success'] = 'Schedule added successfully';
        }
        else{
            $_SESSION['error'] = $conn->error;
        }
    }   
    else{
        $_SESSION['error'] = 'Fill up add form first';
    }

    header('location: schedule.php');
?>
