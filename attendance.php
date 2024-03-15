<?php
if(isset($_POST['employee'])){
    $output = array('error'=>false);

    include 'conn.php';
    include 'timezone.php';

    $employee = $_POST['employee'];
    $status = $_POST['status'];

    $sql = "SELECT * FROM employees WHERE employee_id = '$employee'";
    $query = $conn->query($sql);

    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
        $id = $row['id'];

        $date_now = date('Y-m-d');

        if($status == 'in'){
            $sql = "SELECT * FROM attendance WHERE employee_id = '$id' AND date = '$date_now' AND time_in IS NOT NULL";
            $query = $conn->query($sql);
            if($query->num_rows > 0){
                $output['error'] = true;
                $output['message'] = 'You have timed in for today';
            }
            else{
                // Fetch the schedule information
                $sched = $row['schedule_id'];
                $sql = "SELECT * FROM schedules WHERE id = '$sched'";
                $squery = $conn->query($sql);
                $srow = $squery->fetch_assoc();
                
                // Check if auto time out is enabled for the schedule
                $auto_time = $srow['auto_time'];
                
                // Calculate if the employee is late
                $lognow = date('H:i:s');
                $late = ($lognow > $srow['time_in']) ? 1 : 0;
                
                // Insert the time in record
                $sql = "INSERT INTO attendance (employee_id, date, time_in, status, late) VALUES ('$id', '$date_now', NOW(), 0, '$late')";
                if($conn->query($sql)){
                    // Set status to 0 indicating only time in
                    $output['message'] = 'Time in: '.$row['firstname'].' '.$row['middlename'].' '.$row['lastname'];
                    
                    // If auto time out is enabled, calculate time out and update attendance record
// If auto time out is enabled, calculate time out and update attendance record
if($auto_time == 1){
    // Use the scheduled time out for automatic time out
    $auto_timeout = $srow['time_out'];

    // Update the time_out column in the attendance table
    $sql = "UPDATE attendance SET time_out = '$auto_timeout', status = 1 WHERE employee_id = '$id' AND date = '$date_now'";
    if($conn->query($sql)) {
        // Calculate num_hr
        // Fetch the updated attendance record
        $sql = "SELECT * FROM attendance WHERE employee_id = '$id' AND date = '$date_now'";
        $query = $conn->query($sql);
        $row = $query->fetch_assoc();

        // Calculate num_hr based on time_in and time_out
        $time_in = new DateTime($row['time_in']);
        $time_out = new DateTime($row['time_out']);
        $interval = $time_in->diff($time_out);
        $hrs = $interval->format('%h');
        $mins = $interval->format('%i');
        $mins = $mins/60;
        $num_hr = $hrs + $mins;

        // Update the num_hr column in the attendance table
        $sql = "UPDATE attendance SET num_hr = '$num_hr' WHERE id = '".$row['id']."'";
        $conn->query($sql);
    }
}
                }
                else{
                    $output['error'] = true;
                    $output['message'] = $conn->error;
                }
            }
        }

        
        
        else {
            $sql = "SELECT *, attendance.id AS uid FROM attendance LEFT JOIN employees ON employees.id=attendance.employee_id WHERE attendance.employee_id = '$id' AND date = '$date_now'";
            $query = $conn->query($sql);
            if($query->num_rows < 1){
                $output['error'] = true;
                $output['message'] = 'Cannot Timeout. No time in.';
            }
            else {
                $row = $query->fetch_assoc();
                if($row['time_out'] != '00:00:00'){
                    $output['error'] = true;
                    $output['message'] = 'You have timed out for today';
                }
                else {
                    // Get the current time for time out
                    $current_time = date('Y-m-d H:i:s');
                    
                    // Update the time_out column with the current time
                    $sql = "UPDATE attendance SET time_out = '$current_time', status = 1";
                    
                    // Retrieve 'late' value stored when clocking in
                    $late = $row['late'];
                    $sql .= ", late = '$late'";
                    
                    $sql .= " WHERE id = '".$row['uid']."'";
                    
                    if($conn->query($sql)) {
                        $output['message'] = 'Time out: '.$row['firstname'].' '.$row['middlename'].' '.$row['lastname'];
        
                        $sql = "SELECT * FROM attendance WHERE id = '".$row['uid']."'";
                        $query = $conn->query($sql);
                        $urow = $query->fetch_assoc();

                        $time_in = $urow['time_in'];
                        $time_out = $urow['time_out'];

                        $sql = "SELECT * FROM employees LEFT JOIN schedules ON schedules.id=employees.schedule_id WHERE employees.id = '$id'";
                        $query = $conn->query($sql);
                        $srow = $query->fetch_assoc();

                        if($srow['time_in'] > $urow['time_in']){
                            $time_in = $srow['time_in'];
                        }

                        if($srow['time_out'] < $urow['time_in']){
                            $time_out = $srow['time_out'];
                        }

                        $time_in = new DateTime($time_in);
                        $time_out = new DateTime($time_out);
                        $interval = $time_in->diff($time_out);
                        $hrs = $interval->format('%h');
                        $mins = $interval->format('%i');
                        $mins = $mins/60;
                        $int = $hrs + $mins;

                        // Calculate undertime
                        $sched_time_in = new DateTime($srow['time_in']);
                        $sched_time_out = new DateTime($srow['time_out']);
                        if ($time_out < $sched_time_out) {
                            // If time out is before scheduled time out
                            $output['message'] .= ', undertime detected';
                            $sql = "UPDATE attendance SET num_hr = '$int', under_day = 1 WHERE id = '".$row['uid']."'";
                        } else {
                            $sql = "UPDATE attendance SET num_hr = '$int', under_day = 0 WHERE id = '".$row['uid']."'";
                        }

                        $conn->query($sql);
                    }
                    else{
                        $output['error'] = true;
                        $output['message'] = $conn->error;
                    }
                }
                
            }
        }
    }
    else{
        $output['error'] = true;
        $output['message'] = 'Employee ID not found';
    }
    
}

echo json_encode($output);

?>
