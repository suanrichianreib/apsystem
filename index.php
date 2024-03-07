<?php session_start(); ?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="path/to/your/custom/style.css">
    <style>
        body {
            background-image: url('images/bg2.png');
            background-size: cover;
            background-attachment: fixed; /* Fixed background image */
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 40px; /* Increased padding for a larger form */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Added a subtle box shadow */
            max-width: 400px; /* Set a maximum width for the container */
            transition: background-color 0.3s ease-in-out;
        }

        .container:hover {
            background-color: rgba(255, 255, 255, 1); /* Hover effect for the container */
        }

        .login-logo img {
            max-width: 100%;
            max-height: 100%;
        }

        h4 {
            color: #007bff; /* Updated heading color */
        }

        select,
        input,
        button {
            margin-bottom: 20px; /* Increased margin for better spacing */
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: 1px solid #007bff;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #0056b3; /* Hover effect for the button */
        }

        .alert {
            margin-top: 20px;
        }

        .alert-success,
        .alert-danger {
            background-color: #f8d7da; /* Updated alert colors */
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        /* Date and Time Styles */
        #date,
        #time {
    color: black;
    font-family: Arial, sans-serif;
    font-size: 25px;
    margin: 10px 0;
}

    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12">
                <div class="text-center mb-4 login-logo">
                    <img class="img-fluid" src="images/UC1.png" alt="Logo">
                </div>

                <!-- Date and Time Display -->
                <p id="date" class="text-center"></p>
                <p id="time" class="text-center"></p>

                <form id="attendance">
    <h3 class="mb-4 text-center">Enter Employee ID</h3>

    <div class="form-group">
    <select class="form-control" name="status" style="font-size: 20px; padding: 10px; height: auto;">
        <option value="in">Time In</option>
        <option value="out">Time Out</option>
    </select>
</div>



    <div class="form-group">
        <input type="text" class="form-control" id="employee" name="employee" placeholder="Employee ID" required style="font-size: 18px; padding: 10px;">
    </div>

    <button type="submit" class="btn btn-primary btn-block" style="font-size: 18px;">Sign In</button>
</form>


                <!-- Success and Error Alerts -->
                <div class="alert alert-success text-center" style="display:none;">
                    <span class="result"><i class="icon fa fa-check"></i> <span class="message"></span></span>
                </div>

                <div class="alert alert-danger text-center" style="display:none;">
                    <span class="result"><i class="icon fa fa-warning"></i> <span class="message"></span></span>
                </div>
            </div>
        </div>
    </div>

    <?php include 'scripts.php' ?>

<script type="text/javascript">
    
$(function() {
  var interval = setInterval(function() {
    var momentNow = moment();
    $('#date').html(momentNow.format('dddd').substring(0,3).toUpperCase() + ' - ' + momentNow.format('MMMM DD, YYYY'));  
    $('#time').html(momentNow.format('hh:mm:ss A'));
  }, 100);

  $('#attendance').submit(function(e){
    e.preventDefault();
    var attendance = $(this).serialize();
    $.ajax({
      type: 'POST',
      url: 'attendance.php',
      data: attendance,
      dataType: 'json',
      success: function(response){
        if(response.error){
          $('.alert').hide();
          $('.alert-danger').show();
          $('.message').html(response.message);
        }
        else{
          $('.alert').hide();
          $('.alert-success').show();
          $('.message').html(response.message);
          $('#employee').val('');
        }
      }
    });
  });
        });
    </script>
</body>

</html>