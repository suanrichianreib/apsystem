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
                $logNow = date('H:i:s');
                $late = ($logNow > $scheduleRow['time_in']) ? 1 : 0;

                // Insert the time in record
                $insertQuery = "INSERT INTO attendance (employee_id, date, time_in, status, late) VALUES ('{$employeeRow['id']}', '$currentDate', NOW(), 0, '$late')";
                if ($conn->query($insertQuery)) {
                    // Set status to 0 indicating only time in
                    $logNowFormatted = date('h:i:s A', strtotime($logNow));

                    $output['message'] = '<span style="font-size: 22px; font-weight: bold">Hi! '.$employeeRow['firstname'].' '.$employeeRow['middlename'].' '.$employeeRow['lastname'].'</span><br>';
                    $output['message'] .= '<span style="font-size: 21px; font-weight: bold; color: green;">You have successfully timed in</span><br>';
                    $output['message'] .= '<span style="font-size: 23px; font-weight: bold; color: black;">'.$logNowFormatted.'</span><br>';
                    $output['message'] .= '<img src="/attendtrack/images/'.$employeeRow['photo'].'" width="150" height="140" style="margin-top: 25px;">';
                    
                    // If auto time out is enabled, calculate time out and update attendance record
                    if ($autoTime == 1) {
                        // Use the scheduled time out for automatic time out
                        $autoTimeout = $scheduleRow['time_out'];

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
        } else {
            // Perform operations for time out
            // Fetch attendance details
            $attendanceQuery = "SELECT *, attendance.id AS uid FROM attendance LEFT JOIN employees ON employees.id=attendance.employee_id WHERE attendance.employee_id = '{$employeeRow['id']}' AND date = '$currentDate'";
            $attendanceResult = $conn->query($attendanceQuery);

            if ($attendanceResult->num_rows < 1) {
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
                    
                    $updateQuery .= " WHERE id = '{$attendanceRow['uid']}'";

                    if ($conn->query($updateQuery)) {
                        $output['message'] = '<span style="font-size: 22px; font-weight: bold">Hi! '.$employeeRow['firstname'].' '.$employeeRow['middlename'].' '.$employeeRow['lastname'].'</span><br>';
                        $output['message'] .= '<span style="font-size: 21px; font-weight: bold; color: red;">You have successfully timed out</span><br>';
                        $output['message'] .= '<span style="font-size: 23px; font-weight: bold; color: black;">'.$currentTime.'</span><br>';                        // Fetch updated attendance record
                        $attendanceQuery = "SELECT * FROM attendance WHERE id = '{$attendanceRow['uid']}'";
                        $attendanceResult = $conn->query($attendanceQuery);
                        $updatedAttendanceRow = $attendanceResult->fetch_assoc();

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
                            $output['message'] .= '<span style="font-size: 23px; font-weight: bold; color: black;">undertime detected</span>';
                            $updateAttendanceQuery = "UPDATE attendance SET num_hr = '$int', under_day = 1 WHERE id = '{$attendanceRow['uid']}'";
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