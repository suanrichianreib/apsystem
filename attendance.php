<?php

$output = array('error' => false);

if (isset($_POST['employee'])) {
    include 'conn.php';
    include 'timezone.php';

    $employeeId = $_POST['employee'];
    $status = $_POST['status'];

    // Fetch employee details
    $employeeQuery = "SELECT * FROM employees WHERE employee_id = '$employeeId'";
    $employeeResult = $conn->query($employeeQuery);

    if ($employeeResult->num_rows > 0) {
        $employeeRow = $employeeResult->fetch_assoc();
        $currentDate = date('Y-m-d');

        if ($status == 'in') {
            // Check if employee has already timed in for today
            $attendanceQuery = "SELECT * FROM attendance WHERE employee_id = '{$employeeRow['id']}' AND date = '$currentDate' AND time_in IS NOT NULL";
            $attendanceResult = $conn->query($attendanceQuery);

            if ($attendanceResult->num_rows > 0) {
                $output['error'] = true;
                $output['message'] = 'You have already timed in for today';
            } else {
                // Perform necessary operations for time in
                // Fetch the schedule information
                $sched = $employeeRow['schedule_id'];
                $scheduleQuery = "SELECT * FROM schedules WHERE id = '$sched'";
                $scheduleResult = $conn->query($scheduleQuery);
                $scheduleRow = $scheduleResult->fetch_assoc();

                // Check if auto time out is enabled for the schedule
                $autoTime = $scheduleRow['auto_time'];

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
                        $updateQuery = "UPDATE attendance SET time_out = '$autoTimeout', status = 1 WHERE employee_id = '{$employeeRow['id']}' AND date = '$currentDate'";
                        if ($conn->query($updateQuery)) {
                            // Calculate num_hr
                            // Fetch the updated attendance record
                            $attendanceQuery = "SELECT * FROM attendance WHERE employee_id = '{$employeeRow['id']}' AND date = '$currentDate'";
                            $attendanceResult = $conn->query($attendanceQuery);
                            $attendanceRow = $attendanceResult->fetch_assoc();

                            // Calculate num_hr based on time_in and time_out
                            $timeIn = new DateTime($attendanceRow['time_in']);
                            $timeOut = new DateTime($attendanceRow['time_out']);
                            $interval = $timeIn->diff($timeOut);
                            $hrs = $interval->format('%h');
                            $mins = $interval->format('%i');
                            $mins = $mins / 60;
                            $numHr = $hrs + $mins;

                            // Update the num_hr column in the attendance table
                            $updateNumHrQuery = "UPDATE attendance SET num_hr = '$numHr' WHERE id = '".$attendanceRow['id']."'";
                            $conn->query($updateNumHrQuery);
                        }
                    }
                } else {
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
            } else {
                $attendanceRow = $attendanceResult->fetch_assoc();

                if ($attendanceRow['time_out'] != '00:00:00') {
                    $output['error'] = true;
                    $output['message'] = 'You have already timed out for today';
                } else {
                    // Get the current time for time out
                    $currentTime = date('M-d-y h:i:s A');

                    // Update the time_out column with the current time
                    $updateQuery = "UPDATE attendance SET time_out = '$currentTime', status = 1";
                    
                    // Retrieve 'late' value stored when clocking in
                    $late = $attendanceRow['late'];
                    $updateQuery .= ", late = '$late'";
                    
                    $updateQuery .= " WHERE id = '{$attendanceRow['uid']}'";

                    if ($conn->query($updateQuery)) {
                        $output['message'] = '<span style="font-size: 22px; font-weight: bold">Hi! '.$employeeRow['firstname'].' '.$employeeRow['middlename'].' '.$employeeRow['lastname'].'</span><br>';
                        $output['message'] .= '<span style="font-size: 21px; font-weight: bold; color: red;">You have successfully timed out</span><br>';
                        $output['message'] .= '<span style="font-size: 23px; font-weight: bold; color: black;">'.$currentTime.'</span><br>';                        // Fetch updated attendance record
                        $attendanceQuery = "SELECT * FROM attendance WHERE id = '{$attendanceRow['uid']}'";
                        $attendanceResult = $conn->query($attendanceQuery);
                        $updatedAttendanceRow = $attendanceResult->fetch_assoc();

                        $timeIn = $updatedAttendanceRow['time_in'];
                        $timeOut = $updatedAttendanceRow['time_out'];

                        // Fetch employee's schedule
                        $scheduleQuery = "SELECT * FROM employees LEFT JOIN schedules ON schedules.id=employees.schedule_id WHERE employees.id = '{$employeeRow['id']}'";
                        $scheduleResult = $conn->query($scheduleQuery);
                        $scheduleRow = $scheduleResult->fetch_assoc();

                        if ($scheduleRow['time_in'] > $updatedAttendanceRow['time_in']) {
                            $timeIn = $scheduleRow['time_in'];
                        }

                        if ($scheduleRow['time_out'] < $updatedAttendanceRow['time_in']) {
                            $timeOut = $scheduleRow['time_out'];
                        }

                        $timeIn = new DateTime($timeIn);
                        $timeOut = new DateTime($timeOut);
                        $interval = $timeIn->diff($timeOut);
                        $hrs = $interval->format('%h');
                        $mins = $interval->format('%i');
                        $mins = $mins / 60;
                        $int = $hrs + $mins;

                        // Calculate undertime
                        $schedTimeIn = new DateTime($scheduleRow['time_in']);
                        $schedTimeOut = new DateTime($scheduleRow['time_out']);
                        if ($timeOut < $schedTimeOut) {
                            // If time out is before scheduled time out
                            $output['message'] .= '<span style="font-size: 23px; font-weight: bold; color: black;">undertime detected</span>';
                            $updateAttendanceQuery = "UPDATE attendance SET num_hr = '$int', under_day = 1 WHERE id = '{$attendanceRow['uid']}'";
                        } else {
                            $updateAttendanceQuery = "UPDATE attendance SET num_hr = '$int', under_day = 0 WHERE id = '{$attendanceRow['uid']}'";
                        }

                        $conn->query($updateAttendanceQuery);
                    } else {
                        $output['error'] = true;
                        $output['message'] = $conn->error;
                    }
                }
            }
        }
    } else {
        $output['error'] = true;
        $output['message'] = 'Employee ID not found';
    }
} else {
    $output['error'] = true;
    $output['message'] = 'Employee ID not provided';
}

echo json_encode($output);

?>
