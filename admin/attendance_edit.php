<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$date = $_POST['edit_date'];
		$time_in = $_POST['edit_time_in'];
		$time_in = date('H:i:s', strtotime($time_in));
		$time_out = $_POST['edit_time_out'];
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

			// Update undertime in the database
			$under_day = ($time_out >= $srow['time_out']) ? 0 : 1; // Check if time out is after or exactly at scheduled time out
			$sql = "UPDATE attendance SET under_day = '$under_day', late = '$is_late' WHERE id = '$id'";
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
