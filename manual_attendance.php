<?php
if(isset($_POST['employee'])){
    $output = array('error'=>false);

    include 'conn.php';
    include 'timezone.php';

    $employee = $_POST['employee'];
    $status = $_POST['status'];
    $time_to_clock_in = isset($_POST['time_to_clock_in']) ? $_POST['time_to_clock_in'] : null; // Add this line to get the time to clock in

    // Check if the time to clock in is provided
    if($time_to_clock_in == null || $time_to_clock_in === "12:00 am"){
        $output['error'] = true;
        $output['message'] = 'Please provide your clock-in time';
    } else {
        $sql = "SELECT * FROM employees WHERE employee_id = '$employee'";
        $query = $conn->query($sql);

        if($query->num_rows > 0){
            $row = $query->fetch_assoc();
            $id = $row['id'];

            $date_now = date('Y-m-d');

            if($status == 'in'){
                // Check if the employee has already timed in for today
                $sql_check_timed_in = "SELECT * FROM attendance WHERE employee_id = '$id' AND date = '$date_now' AND time_in IS NOT NULL";
                $query_check_timed_in = $conn->query($sql_check_timed_in);
                if($query_check_timed_in->num_rows > 0){
                    $output['error'] = true;
                    $output['message'] = 'You have already timed in for today';
                }
                else{
                    // Fetch the schedule information
                    $sched = $row['schedule_id'];
                    $sql = "SELECT * FROM schedules WHERE id = '$sched'";
                    $squery = $conn->query($sql);
                    $srow = $squery->fetch_assoc();
                    
                    // Check if auto time out is enabled for the schedule
                    $auto_time = $srow['auto_time'];
                    
                    // Convert the provided time to clock in to timestamp
                    $time_in_sched = strtotime($time_to_clock_in);

                    // Define lunch break start and end times
                    $lunch_break_start = strtotime('12:00 PM');
                    $lunch_break_end = strtotime('1:00 PM');

                    // Calculate late minutes, considering the lunch break
                    if ($time_in_sched > $lunch_break_end && strtotime($srow['time_in']) < $lunch_break_end) {
                        // Subtract 1 hour if clock-in is after lunch break but scheduled time in is before lunch break
                        $late_minutes = max(0, ($time_in_sched - strtotime($srow['time_in']) - 3600) / 60); // Late minutes, if negative set to 0
                    } else {
                        $late_minutes = max(0, ($time_in_sched - strtotime($srow['time_in'])) / 60); // Late minutes, if negative set to 0
                    }

                    // Determine if the employee is late
                    $late = ($time_in_sched > strtotime($srow['time_in'])) ? 1 : 0;

                    // Calculate late hours
                    $late_hours = $late_minutes / 60; // Late hours
                    $late_hours = round($late_hours, 2); // Round to two decimal places
                    
                    // Insert the time in record
                    $sql = "INSERT INTO attendance (employee_id, date, time_in, status, late, late_hours) VALUES ('$id', '$date_now', '$time_to_clock_in', 0, '$late', '$late_hours')";
                    if($conn->query($sql)){
                        $output['message'] = '<img src="/attendtrack/images/'.$row['photo'].'" width="210" height="210" style="margin-top: 10px;"><br>';
                        $output['message'] .= '<span style="font-size: 22px; font-weight: bold; margin-top: 40px;">Hi! '.$row['firstname'].' '.$row['middlename'].' '.$row['lastname'].'</span><br>';
                        $output['message'] .= '<span style="font-size: 21px; font-weight: bold; color: green; margin-top: 30px;">You have successfully timed in</span><br>';
                        $output['message'] .= '<span style="font-size: 23px; font-weight: bold; color: black; margin-top: 30px;">'.$time_to_clock_in.'</span><br>';

                        // Handle auto timeout logic here if needed
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
                    } else {
                        $output['error'] = true;
                        $output['message'] = $conn->error;
                    }
                }
            } else {
                // Handle clock out logic here
            }
        } else {
            $output['error'] = true;
            $output['message'] = 'Employee ID not found';
        }
    }
}

echo json_encode($output);

?>
