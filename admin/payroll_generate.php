<?php
	include 'includes/session.php';

	function generateRow($from, $to, $conn, $deduction){
		$contents = '';
	 	
		$sql = "SELECT 
		*,
		SUM(num_hr) + COALESCE(overtime.total_overtime, 0) AS total_hr,
		employees.employee_id AS empid, 
		GROUP_CONCAT(attendance.status) AS present, 
		COUNT(attendance.status) AS status_count, 
		SUM(attendance.under_day) AS undertime_days, 
		position.description AS position_description 
	FROM attendance 
	LEFT JOIN employees ON employees.id = attendance.employee_id 
	LEFT JOIN position ON position.id = employees.position_id 
	LEFT JOIN (
		SELECT employee_id, SUM(hours) AS total_overtime
		FROM overtime
		GROUP BY employee_id
	) AS overtime ON overtime.employee_id = employees.id
	WHERE date BETWEEN '$from' AND '$to' 
	GROUP BY attendance.employee_id 
	ORDER BY empid ASC"; // Adjusted ORDER BY clause
	


		$query = $conn->query($sql);
		$total = 0;
		while($row = $query->fetch_assoc()){
			// $empid = $row['empid'];
                      
	      	// $casql = "SELECT *, SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";
	      
	      	// $caquery = $conn->query($casql);
	      	// $carow = $caquery->fetch_assoc();
	      	// $cashadvance = $carow['cashamount'];

			// $gross = $row['rate'] * $row['total_hr'];
			// $total_deduction = $deduction + $cashadvance;
      		// $net = $gross - $total_deduction;

			// $total += $net;
			$contents .= '
			<tr>
			    <td>'.$row['empid'].'</td>
				<td>'.$row['lastname'].', '.$row['firstname'].' '.$row['middlename'].'</td>
				<td>'.$row['position_description'].'</td> <!-- Display position description -->
				<td>'.$row['status_count'].'</td> <!-- Include the Present column -->
				<td>'.$row['undertime_days'].'</td> <!-- Include the Undertime Days column -->
				<td>'.$row['total_hr'].'</td> <!-- Include the Total Hours Work column -->
			</tr>
			';
		}

		return $contents;
	}
		
	$range = $_POST['date_range'];
	$ex = explode(' - ', $range);
	$from = date('Y-m-d', strtotime($ex[0]));
	$to = date('Y-m-d', strtotime($ex[1]));

	$sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
    $query = $conn->query($sql);
   	$drow = $query->fetch_assoc();
    $deduction = $drow['total_amount'];

	$from_title = date('M d, Y', strtotime($ex[0]));
	$to_title = date('M d, Y', strtotime($ex[1]));

	require_once('../tcpdf/tcpdf.php');  
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
    $pdf->SetCreator(PDF_CREATOR);  
    $pdf->SetTitle('Payroll: '.$from_title.' - '.$to_title);  
    $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
    $pdf->SetDefaultMonospacedFont('helvetica');  
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
    $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
    $pdf->setPrintHeader(false);  
    $pdf->setPrintFooter(false);  
    $pdf->SetAutoPageBreak(TRUE, 10);  
    $pdf->SetFont('helvetica', '', 11);  
    $pdf->AddPage();  
    $content = '';  
    $content .= '
      	<h2 align="center">Ultra Craft Summary of Attendance</h2>
      	<h4 align="center">'.$from_title." - ".$to_title.'</h4>
      	<table border="1" cellspacing="0" cellpadding="3">  
           <tr>  
		        <th width="15%" align="center"><b>Employee ID</b></th>
           		<th width="25%" align="center"><b>Employee Name</b></th>
           		<th width="15%" align="center"><b>Position</b></th> <!-- New column header for Position -->
           		<th width="15%" align="center"><b>Present</b></th> <!-- Include the Present column header -->
				<th width="15%" align="center"><b>Undertime Days</b></th> <!-- Include the Undertime Days column header -->
				<th width="15%" align="center"><b>Total Hours Work</b></th> <!-- Include the Total Hours Work column header -->
           </tr>  
      ';  
    $content .= generateRow($from, $to, $conn, $deduction);  
    $content .= '</table>';  
    $pdf->writeHTML($content);  
    $pdf->Output('payroll.pdf', 'I');

?>
