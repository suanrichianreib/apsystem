<?php
    include 'includes/session.php';

    if(isset($_POST['add'])){
        $employee = $_POST['employee'];
        $date = $_POST['date'];
        $time_in = $_POST['time_in'];
        $time_in = date('H:i:s', strtotime($time_in));
        $time_out = $_POST['time_out'];
        $time_out = date('H:i:s', strtotime($time_out));

        // Check if time_out is '00:00:00' or '12:00 AM'
        if ($time_out === '00:00:00' || $time_out === '12:00 AM') {
            $_SESSION['error'] = "Invalid time out value";
            header('location: attendance.php');
            exit();
        }
        
        $time_out = date('H:i:s', strtotime($time_out));

        $sql = "SELECT * FROM employees WHERE employee_id = '$employee'";
        $query = $conn->query($sql);

        if($query->num_rows < 1){
            $_SESSION['error'] = 'Employee not found';
        }
        else{
            $row = $query->fetch_assoc();
            $emp = $row['id'];

            $sql = "SELECT * FROM attendance WHERE employee_id = '$emp' AND date = '$date'";
            $query = $conn->query($sql);

            if($query->num_rows > 0){
                $_SESSION['error'] = 'Employee attendance for the day exists';
            }
            else{
                // Fetch schedule information
                $sched = $row['schedule_id'];
                $sql = "SELECT * FROM schedules WHERE id = '$sched'";
                $squery = $conn->query($sql);
                $scherow = $squery->fetch_assoc();
                
                // Check if time out is before scheduled time out
                $under_day = ($time_out < $scherow['time_out']) ? 1 : 0;

                // Determine if employee is late
                $is_late = ($time_in > $scherow['time_in']) ? 1 : 0;

                // Calculate late minutes
                $late_minutes = max(0, strtotime($time_in) - strtotime($scherow['time_in'])) / 60;

                // Calculate late hours
                $late_hours = $late_minutes / 60;

                // Determine the status based on the presence of both time in and time out
                $status = ($time_in != '' && $time_out != '') ? 1 : 0;

                // Calculate undertime hours
                $under_hours = 0;
                if ($time_out < $scherow['time_out']) {
                    // If time out is before scheduled time out
                    $time_out_dt = new DateTime($time_out);
                    $scheduled_time_out_dt = new DateTime($scherow['time_out']);
                    $interval = $time_out_dt->diff($scheduled_time_out_dt);
                    $under_hours = $interval->h + ($interval->i / 60); // Convert minutes to hours
                }

                $sql = "INSERT INTO attendance (employee_id, date, time_in, time_out, status, under_day, late, late_hours, under_hours) VALUES ('$emp', '$date', '$time_in', '$time_out', '$status', '$under_day', '$is_late', '$late_hours', '$under_hours')";
                if($conn->query($sql)){
                    $_SESSION['success'] = 'Attendance added successfully';
                    $id = $conn->insert_id;

                    $sql = "SELECT * FROM employees LEFT JOIN schedules ON schedules.id=employees.schedule_id WHERE employees.id = '$emp'";
                    $query = $conn->query($sql);
                    $srow = $query->fetch_assoc();

                    if($srow['time_in'] > $time_in){
                        $time_in = $srow['time_in'];
                    }

                    if($srow['time_out'] < $time_out){
                        $time_out = $srow['time_out'];
                    }

                    $time_in = new DateTime($time_in);
                    $time_out = new DateTime($time_out);
                    $interval = $time_in->diff($time_out);
                    $hrs = $interval->format('%h');
                    $mins = $interval->format('%i');
                    $mins = $mins/60;
                    $int = $hrs + $mins;
                    if($int > 4){
                        $int = $int - 1;
                    }

                    $sql = "UPDATE attendance SET num_hr = '$int' WHERE id = '$id'";
                    $conn->query($sql);

                }
                else{
                    $_SESSION['error'] = $conn->error;
                }
            }
        }
    }
    else{
        $_SESSION['error'] = 'Fill up add form first';
    }
    
    header('location: attendance.php');
?>
