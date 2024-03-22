<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-red sidebar-mini "> <!-- Change skin-blue to skin-red -->
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        QR Code Generator
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">QR Code</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <?php

use chillerlan\QRCode\QRCode;

include '../vendor/autoload.php';

$result = '';

if (isset($_GET['content']) && !empty($_GET['content'])) {
    $result = (new QRCode())->render($_GET['content']);
}

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>PHP QR Code Generator</title>
</head>

<body class="h-screen w-full flex flex-col items-center justify-center gap-10">

<!-- <h1 class="text-5xl font-bold font-serif">
    PHP QR Code Generator
</h1> -->

<div class="w-full px-28 grid grid-cols-2 gap-4">
    <div class="border border-gray-300 p-6 rounded-lg">
        <form action="qr_code.php" method="get">
        <div class="form-group">
					<label for="employeeSelect" class="col-sm-3 control-label">Employee List Guide</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="employeeSelect" id="employeeSelect">
                            <option value="" selected disabled>Select Employee</option>
                            <?php
                              $sql = "SELECT * FROM employees";
                              $query = $conn->query($sql);
                              while($prow = $query->fetch_assoc()){
                                echo "
                                  <option value='".$prow['employee_id']."'>".$prow['lastname'].", ".$prow['firstname']." - [".$prow['employee_id']."]</option>
                                ";
                              }
                            ?>
                        </select>
                    </div>
                </div>
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Content</label>
                <textarea type="text"
                          name="content"
                          class="block p-4 w-full text-gray-900 bg-gray-50 rounded-lg border border-gray-300 sm:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
            </div>

            <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Generate
            </button>
        </form>
    </div>

    <div class="border border-gray-300 p-6 rounded-lg flex flex-col items-center justify-center">
        <?php if (isset($result) && !empty($result)): ?>
            <img id="qrcode" src="<?= $result ?>"/>
            <button onclick="saveQRCode()" class="mt-4 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                Save
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
// JavaScript to update the Content input field based on the selected employee
document.getElementById('employeeSelect').addEventListener('change', function() {
    var selectedEmployeeID = this.value;
    // Update the Content input field with the selected employee's ID
    document.getElementsByName('content')[0].value = selectedEmployeeID;
});
</script>

<script>
    function saveQRCode() {
        var qrCodeImage = document.getElementById('qrcode');
        var content = '<?php echo isset($_GET["content"]) ? $_GET["content"] : "" ?>';
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');
        canvas.width = qrCodeImage.width;
        canvas.height = qrCodeImage.height;
        context.drawImage(qrCodeImage, 0, 0, qrCodeImage.width, qrCodeImage.height);
        var downloadLink = document.createElement('a');
        downloadLink.href = canvas.toDataURL('image/png');
        downloadLink.download = content + '.png';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>

</body>
</html>

    </section>   
  </div>
    
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/employee_schedule_modal.php'; ?>
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
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'schedule_employee_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.employee_name').html(response.lastname+', '+response.firstname+' '+response.middlename);
      $('#schedule_val').val(response.schedule_id);
      $('#schedule_val').html(response.time_in+' '+response.time_out);
      $('#empid').val(response.empid);
    }
  });
}
</script>
<?php include 'includes/datatable_initializer.php'; ?>
</body>
</html>
