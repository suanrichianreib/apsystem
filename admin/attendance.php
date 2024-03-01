<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Attendance
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Attendance</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Filter by Month and Year Form -->
      <form method="GET" action="">
        <label for="filter_month">Filter by Month:</label>
        <select id="filter_month" name="filter_month">
            <option value="">All Months</option>
            <option value="01" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '01') echo 'selected'; ?>>January</option>
            <option value="02" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '02') echo 'selected'; ?>>February</option>
            <option value="03" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '03') echo 'selected'; ?>>March</option>
            <option value="04" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '04') echo 'selected'; ?>>April</option>
            <option value="05" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '05') echo 'selected'; ?>>May</option>
            <option value="06" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '06') echo 'selected'; ?>>June</option>
            <option value="07" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '07') echo 'selected'; ?>>July</option>
            <option value="08" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '08') echo 'selected'; ?>>August</option>
            <option value="09" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '09') echo 'selected'; ?>>September</option>
            <option value="10" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '10') echo 'selected'; ?>>October</option>
            <option value="11" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '11') echo 'selected'; ?>>November</option>
            <option value="12" <?php if(isset($_GET['filter_month']) && $_GET['filter_month'] === '12') echo 'selected'; ?>>December</option>
        </select>

        <label for="filter_year">&nbsp;&nbsp;&nbsp;&nbsp;Filter by Year:</label>
        <select id="filter_year" name="filter_year">
          <option value="">All Years</option>
          <?php
            // Get the current year
            $current_year = date('Y');
            
            // Loop through the years, starting from 5 years ago to 5 years from now
            for ($year = $current_year - 5; $year <= $current_year + 5; $year++) {
                echo "<option value=\"$year\"";
                if(isset($_GET['filter_year']) && $_GET['filter_year'] == $year) {
                    echo " selected";
                }
                echo ">$year</option>";
            }
          ?>
        </select>
        <button type="submit">Apply</button>
      </form>
      <!-- End of Filter by Month and Year Form -->

      <?php
        // Default to current month if no filter is set
        $selected_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';

        // Default to current year if no filter is set
        $selected_year = isset($_GET['filter_year']) ? $_GET['filter_year'] : '';

        // Construct the SQL query based on the selected month and year
        $sql = "SELECT *, employees.employee_id AS empid, attendance.id AS attid 
                FROM attendance 
                LEFT JOIN employees ON employees.id = attendance.employee_id";

        // Add WHERE clause if a specific month is selected
        if ($selected_month !== '') {
            $sql .= " WHERE MONTH(attendance.date) = $selected_month";
        }

        // Add WHERE clause if a specific year is selected
        if ($selected_year !== '') {
            // Check if there's already a WHERE clause in the query
            if ($selected_month !== '') {
                $sql .= " AND";
            } else {
                $sql .= " WHERE";
            }
            $sql .= " YEAR(attendance.date) = $selected_year";
        }

        // Add ORDER BY clause
        $sql .= " ORDER BY attendance.date DESC, attendance.time_in DESC";

        // Execute the SQL query
        $query = $conn->query($sql);

        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Date</th>
                  <th>Employee ID</th>
                  <th>Name</th>
                  <th>Time In</th>
                  <th>Time Out</th>
                  <th>Tools</th>
                </thead>
                <tbody>
                  <?php
                    while($row = $query->fetch_assoc()){
                      $status_time_in = ($row['status']) ? '<span class="label label-warning pull-right">ontime</span>' : '<span class="label label-danger pull-right">late</span>';
                      $status_time_out = ''; // Initialize status for time out
              
                      // Determine status for time out
                      if ($row['time_out'] === '00:00:00' || date('h:i A', strtotime($row['time_out'])) === '12:00 AM') {
                          $status_time_out = '<span class="label label-danger pull-right">not yet</span>';
                      } else {
                          $status_time_out = date('h:i A', strtotime($row['time_out']));
                      }

                      // Update status if time out has occurred
                      if ($status_time_out !== '<span class="label label-danger pull-right">not yet</span>') {
                          $status_time_out = '<span class="label label-success pull-right">done</span>';
                      }

                      echo "
                        <tr>
                          <td class='hidden'></td>
                          <td>".date('M d, Y', strtotime($row['date']))."</td>
                          <td>".$row['empid']."</td>
                          <td>".$row['firstname'].' '.$row['lastname']."</td>
                          <td>".date('h:i A', strtotime($row['time_in'])).$status_time_in."</td>
                          <td>".date('h:i A', strtotime($row['time_out'])).$status_time_out."</td>
                          <td>
                            <button class='btn btn-success btn-sm btn-flat edit' data-id='".$row['attid']."'><i class='fa fa-edit'></i> Edit</button>
                            <button class='btn btn-danger btn-sm btn-flat delete' data-id='".$row['attid']."'><i class='fa fa-trash'></i> Delete</button>
                          </td>
                        </tr>
                      ";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
    
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/attendance_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $('.edit').click(function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $('.delete').click(function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'attendance_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('#datepicker_edit').val(response.date);
      $('#attendance_date').html(response.date);
      $('#edit_time_in').val(response.time_in);
      $('#edit_time_out').val(response.time_out);
      $('#attid').val(response.attid);
      $('#employee_name').html(response.firstname+' '+response.lastname);
      $('#del_attid').val(response.attid);
      $('#del_employee_name').html(response.firstname+' '+response.lastname);
    }
  });
}
</script>
<?php include 'includes/datatable_initializer.php'; ?>
</body>
</html>
