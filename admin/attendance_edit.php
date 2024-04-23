<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$date = $_POST['edit_date'];
		$time_in = $_POST['edit_time_in'];
		$time_in = date('H:i:s', strtotime($time_in));
		$time_out = $_POST['edit_time_out'];
		$time_out = date('H:i:s', strtotime($time_out));

		// Check if time_out is '00:00:00' or '12:00 AM'
		if ($time_out === '00:00:00' || $time_out === '12:00 AM') {
			$_SESSION['error'] = "Invalid time out value";
			header('location: attendance.php');
			exit();
		}
				
		$time_out = date('H:i:s', strtotime($time_out));

		$sql = "UPDATE attendance SET date = '$date', time_in = '$time_in', time_out = '$time_out' WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Attendance updated successfully';

			// Update the attendance record
			$sql = "SELECT * FROM attendance WHERE id = '$id'";
			$query = $conn->query($sql);
			$row = $query->fetch_assoc();
			$emp = $row['employee_id'];

			$sql = "SELECT * FROM employees LEFT JOIN schedules ON schedules.id=employees.schedule_id WHERE employees.id = '$emp'";
			$query = $conn->query($sql);
			$srow = $query->fetch_assoc();

			// Determine if the employee is late based on the updated time in
			$is_late = ($time_in > $srow['time_in']) ? 1 : 0;

			// Calculate late minutes
			$late_minutes = max(0, strtotime($time_in) - strtotime($srow['time_in'])) / 60;

			// Calculate late hours
			$late_hours = $late_minutes / 60;

			// Update undertime in the database
			$under_day = ($time_out >= $srow['time_out']) ? 0 : 1; // Check if time out is after or exactly at scheduled time out
            
            // Calculate undertime hours
            $under_hours = 0;
            if ($time_out < $srow['time_out']) {
                // If time out is before scheduled time out
                $time_out_dt = new DateTime($time_out);
                $scheduled_time_out_dt = new DateTime($srow['time_out']);
                $interval = $time_out_dt->diff($scheduled_time_out_dt);
                $under_hours = $interval->h + ($interval->i / 60); // Convert minutes to hours
            }

			$sql = "UPDATE attendance SET under_day = '$under_day', late = '$is_late', late_hours = '$late_hours', under_hours = '$under_hours' WHERE id = '$id'";
			$conn->query($sql);

			// Calculate other details (num_hr and status)
			$logstatus = ($time_in > $srow['time_in']) ? 0 : 1;

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

			// Set status based on the calculated time in and time out
			$logstatus = ($time_in > $srow['time_in'] && $time_out < $srow['time_out']) ? 0 : 1;

			$sql = "UPDATE attendance SET num_hr = '$int', status = '$logstatus' WHERE id = '$id'"; // Set status based on calculated time in and time out
			$conn->query($sql);
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location:attendance.php');
?>
