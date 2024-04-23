<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-purple sidebar-mini">
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
                <!-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">QR Code</li> -->
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
            <!-- QR Code Generator Form -->
            <div class="flex items-center justify-center mt-10">
                <div class="border border-gray-300 p-6 rounded-lg">
                    <form action="qr_code.php" method="get">
                        <div class="form-group">
                            <label for="employeeSelect" class="control-label">Employee List</label>
                            <select class="form-control" name="employeeSelect" id="employeeSelect">
                                <option value="" selected disabled>Select Employee</option>
                                <?php
                                $sql = "SELECT * FROM employees ORDER BY employees.lastname ASC";
                                $query = $conn->query($sql);
                                while($prow = $query->fetch_assoc()){
                                    echo "<option value='".$prow['employee_id']."'>".$prow['lastname'].", ".$prow['firstname']." - [".$prow['employee_id']."]</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-6">
                            <textarea type="text" name="content" class="block p-4 w-full text-gray-900 bg-gray-50 rounded-lg border border-gray-300 sm:text-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Generate</button>
                    </form>
                </div>
            </div>
            <!-- QR Code Display -->
            <div class="flex items-center justify-center mt-6">
                <?php if (isset($result) && !empty($result)): ?>
                    <div class="border border-gray-300 p-6 rounded-lg">
                       <img id="qrcode" src="<?= $result ?>" style="width: 446px; height: 446px;">
                    </div>
                    <button onclick="saveQRCode()" class="mt-4 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save</button>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/employee_schedule_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>
    // JavaScript to update the Content input field based on the selected employee
    document.getElementById('employeeSelect').addEventListener('change', function() {
        var selectedEmployeeID = this.value;
        // Update the Content input field with the selected employee's ID
        document.getElementsByName('content')[0].value = selectedEmployeeID;
    });
    // JavaScript function to save the QR code as an image
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
<?php include 'includes/datatable_initializer.php'; ?>
</body>
</html>
