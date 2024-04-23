<?php
	include 'includes/session.php';

	function generateRow($from, $to, $conn, $deduction){
		$contents = '';
	 	
		$sql = "SELECT 
		employees.employee_id, 
		employees.lastname, 
		employees.firstname, 
		employees.middlename, 
		position.description AS position_description, 
		SUM(CASE WHEN attendance.status = 1 THEN 1 ELSE 0 END) AS present_count, 
		SUM(attendance.late_hours) AS late_count,
		COALESCE(overtime.total_overtime, 0) AS overtime_count,
		SUM(attendance.num_hr) + COALESCE(overtime.total_overtime, 0) AS total_hr, 
		SUM(under_hours) AS total_undertime
	FROM attendance 
	LEFT JOIN employees ON employees.id = attendance.employee_id 
	LEFT JOIN position ON position.id = employees.position_id 
	LEFT JOIN (
		SELECT employee_id, SUM(hours) AS total_overtime
		FROM overtime
		WHERE date_overtime BETWEEN '$from' AND '$to'  -- Filter overtime records by date range
		GROUP BY employee_id
	) AS overtime ON overtime.employee_id = employees.id
	WHERE date BETWEEN '$from' AND '$to'  -- Filter attendance records by date range
	GROUP BY attendance.employee_id 
	ORDER BY employees.lastname ASC, employees.firstname ASC";


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
			    <td>'.$row['employee_id'].'</td>
				<td>'.$row['lastname'].', '.$row['firstname'].' '.$row['middlename'].'</td>
				<td>'.$row['position_description'].'</td> <!-- Display position description -->
				<td>'.$row['present_count'].'</td> <!-- Include the Present column -->
				<td>'.number_format($row['overtime_count'], 2).'</td> <!-- Include the Present column -->
				<td>'.number_format($row['late_count'], 2).'</td> <!-- Include the Present column -->
				<td>'.number_format($row['total_undertime'], 2).'</td> <!-- Include the Undertime Days column -->
				<td>'.number_format($row['total_hr'], 2).'</td> <!-- Include the Total Hours Work column -->
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
           		<th width="15%" align="center"><b>Employee Name</b></th>
           		<th width="12%" align="center"><b>Position</b></th> <!-- New column header for Position -->
           		<th width="10%" align="center"><b>Present Days</b></th> <!-- Include the Present column header -->
				<th width="12%" align="center"><b>Overtime Hours</b></th> <!-- Include the Present column header -->
				<th width="10%" align="center"><b>Late Hours</b></th> <!-- Include the Present column header -->
				<th width="12%" align="center"><b>Undertime Hours</b></th> <!-- Include the Undertime Days column header -->
				<th width="15%" align="center"><b>Total Hours Work</b></th> <!-- Include the Total Hours Work column header -->
           </tr>  
      ';  
    $content .= generateRow($from, $to, $conn, $deduction);  
    $content .= '</table>';  
    $pdf->writeHTML($content);  
    $pdf->Output('payroll.pdf', 'I');

?>
