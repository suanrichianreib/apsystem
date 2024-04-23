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
                
// Get the current time in the desired format (H:i:s)
$lognow = date('H:i:s');

// Convert the scheduled time in to timestamp
$time_in_sched = strtotime($srow['time_in']);

// Define lunch break start and end times
$lunch_break_start = strtotime('12:00 PM');
$lunch_break_end = strtotime('1:00 PM');

// Calculate late minutes, considering the lunch break
if (strtotime($lognow) > $lunch_break_end && $time_in_sched < $lunch_break_end) {
    // Subtract 1 hour if clock-in is after lunch break but scheduled time in is before lunch break
    $late_minutes = max(0, (strtotime($lognow) - $time_in_sched - 3600) / 60); // Late minutes, if negative set to 0
} else {
    $late_minutes = max(0, (strtotime($lognow) - $time_in_sched) / 60); // Late minutes, if negative set to 0
}

// Determine if the employee is late
$late = (strtotime($lognow) > $time_in_sched) ? 1 : 0;

// Calculate late hours
$late_hours = $late_minutes / 60; // Late hours
$late_hours = round($late_hours, 2); // Round to two decimal places

// Insert the time in record
$sql = "INSERT INTO attendance (employee_id, date, time_in, status, late, late_hours) VALUES ('$id', '$date_now', '$lognow', 0, '$late', '$late_hours')";


                if($conn->query($sql)){
                    
                    $output['message'] = '<img src="/attendtrack/images/'.$row['photo'].'" width="210" height="210" style="margin-top: 10px;"><br>';
                    $output['message'] .= '<span style="font-size: 22px; font-weight: bold; margin-top: 40px;">Hi! '.$row['firstname'].' '.$row['middlename'].' '.$row['lastname'].'</span><br>';
                    $output['message'] .= '<span style="font-size: 21px; font-weight: bold; color: green; margin-top: 30px;">You have successfully timed in</span><br>';
                    $output['message'] .= '<span style="font-size: 23px; font-weight: bold; color: black; margin-top: 30px;">'.$lognow.'</span><br>';
                    
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

                            // Adjust total hours worked if the shift spans across lunch break
                            $time_in_timestamp = strtotime($time_in->format('H:i:s'));
                            $time_out_timestamp = strtotime($time_out->format('H:i:s'));
                            $lunch_break_start = strtotime('12:00 PM');
                            $lunch_break_end = strtotime('1:00 PM');

                            if ($time_in_timestamp < $lunch_break_start && $time_out_timestamp > $lunch_break_end) {
                                // If worked hours span across lunch break, deduct 1 hour
                                if($num_hr > 4){
                                    $num_hr = $num_hr - 1;
                                }
                            }

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
                    $manual_time_out = date('Y-m-d H:i:s');
                    
                    // Update the time_out column with the manual time out
                    $sql = "UPDATE attendance SET time_out = '$manual_time_out', status = 1";
                    
                    
                    // Retrieve 'late' value stored when clocking in
                    $late = $row['late'];
                    $sql .= ", late = '$late'";
                    
                    $sql .= " WHERE id = '".$row['uid']."'";
                    
                    if($conn->query($sql)) {
                        $output['message'] = '<img src="/attendtrack/images/'.$row['photo'].'" width="210" height="210" style="margin-top: 10px;"><br>';
                        $output['message'] .= '<span style="font-size: 22px; font-weight: bold">Hi! '.$row['firstname'].' '.$row['middlename'].' '.$row['lastname'].'</span><br>';
                        $output['message'] .= '<span style="font-size: 21px; font-weight: bold; color: green;">You have successfully timed out</span><br>';
                        $output['message'] .= '<span style="font-size: 23px; font-weight: bold; color: black;">'.$manual_time_out.'</span><br>';

        
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
                        $time_out = new DateTime($manual_time_out);
                        $interval = $time_in->diff($time_out);
                        $hrs = $interval->format('%h');
                        $mins = $interval->format('%i');
                        $mins = $mins/60;
                        $int = $hrs + $mins;

                        // Adjust total hours worked if the shift spans across lunch break
                        $time_in_timestamp = strtotime($time_in->format('H:i:s'));
                        $time_out_timestamp = strtotime($time_out->format('H:i:s'));
                        $lunch_break_start = strtotime('12:00 PM');
                        $lunch_break_end = strtotime('1:00 PM');
                        
                        if ($time_in_timestamp < $lunch_break_start && $time_out_timestamp > $lunch_break_end) {
                            // If worked hours span across lunch break, deduct 1 hour
                             if($int > 4){
                                $int = $int - 1;
                            }
                        }

                        // Calculate undertime
                        $sched_time_in = new DateTime($srow['time_in']);
                        $sched_time_out = new DateTime($srow['time_out']);
                        if ($time_out < $sched_time_out) {
                            // If time out is before scheduled time out
                            $output['message'] .= '<span style="font-size: 22px; font-weight: bold">Undertime Detected!</span>';
                        
                            // Calculate the remaining hours until the scheduled time out
                            $remaining_hours = ($sched_time_out->getTimestamp() - $time_out->getTimestamp()) / 3600;
                        
                            // Update the under_hours column in the attendance table
                            $sql = "UPDATE attendance SET num_hr = '$int', under_day = 1, under_hours = '$remaining_hours' WHERE id = '".$row['uid']."'";
                        } else {
                            // If time out is after or at the scheduled time out
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

