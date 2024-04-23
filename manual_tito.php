<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <style>
        body {
            background-image: url('images/bgc1.png');
            background-size: cover;
            background-attachment: fixed;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-image 1s ease-in-out; /* Added transition for smoother image changes */
        }

        .container {
            background-color: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            transition: background-color 0.3s ease-in-out;
            margin-left: 210px; /* Adjust margin-left to move the container to the left */
        }

        .login-logo img {
            max-width: 100%;
        }
         h1 {
            font-weight: bold;
            font-size: 31px;
            margin-top: 10px;
            margin-bottom: 50px;
            font-family: 'Argent CF', sans-serif;
        }

        h3 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }

        select,
        input {
            margin-bottom: 20px;
            width: 100%;
            font-size: 18px;
            padding: 10px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: 1px solid #007bff;
            width: 100%;
            font-size: 18px;
            padding: 10px;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
            text-align: center;
        }

        .alert-danger {
            background-color: red;
            border: 1px solid #f5c6cb;
            color: white;
        }

        .alert-success{
            background-color: white;
            border: 1px solid white;
            color: black;
        }

        #date,
        #time {
            color: black;
            font-family: Arial, sans-serif;
            font-size: 25px;
            margin-bottom: 20px;
            text-align: center;
        }
        .container1{
            background-color: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 430px; /* Adjust maximum width */
            height: 500px; /* Adjust height */
            width: 50%; /* Adjust width */
            transition: background-color 0.3s ease-in-out;
            margin-right: 200px; /* Adjust margin-left to move the container to the left */
        }
          /* Circle styles */
        .circle {
            width: 79px;
            height: 79px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            position: absolute;
            top: 92%;
            left: 95%;
            transform: translate(-50%, -50%);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 2px solid maroon;
            background-image: url('images/programmer.gif');
            background-size: cover;
            transition: background-image 1s ease-in-out; /* Added transition for smoother image changes */
            z-index: 0; /* Ensure it's behind the content */
        }
        h1 {
    font-size: 16px; /* Smaller font size */
    margin: 0; /* Remove margins */
    padding: 0; /* Remove padding */
    margin-bottom: 10px; /* Add margin bottom */
    margin-top: 10px;
  }
    </style>
</head>
<body>
<div class="container">
    <form id="attendance">
        <div class="form-group">
            <select class="form-control" name="status">
                <option value="in">Time In</option>
            </select>
        </div>
        <div class="input-group form-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Time to Clock In</span>
            </div>
            <input type="time" class="form-control" id="time_to_clock_in" name="time_to_clock_in">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="employee" name="employee" placeholder="Employee ID" required>
        </div>
        <button type="submit" class="btn btn-primary">Sign In</button>
    </form>
</div>
    <div class="container1" style="text-align: center;">
        <div class="alert alert-success" style="display:none;">
            <span class="result"><i class="icon fa fa-check"></i> <span class="message"></span></span>
        </div>
        <div class="alert alert-danger" style="display:none;">
            <span class="result"><i class="icon fa fa-warning"></i> <span class="message"></span></span>
        </div>
        <img class="img-fluid1" src="images/honest.gif" alt="Logo" style="display: block; margin: 0 auto; width: 190px; height: auto; margin-top: 30px;">
        <h1 style="font-weight: bold; font-size: 31px; margin-top: 10px; margin-bottom: 50px; font-family: 'Argent CF', sans-serif;">Please be honest on putting your time in!</h1>
    </div>
    <!-- <div class="circle"></div> -->
    <?php include 'scripts.php' ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script type="text/javascript">

    // Variable to track if a submission is allowed
    var submissionAllowed = true;

    // Function to enable submission after a delay
    function enableSubmission() {
    submissionAllowed = true;
    }

        setInterval(function() {
              $('#employee').focus();
        }, 1000); 

        $(function() {
    // var images = ['images/bgc1.png', 'images/night2.jpg', 'images/night3.jpg', 'images/night4.jpg']; // List of background images
    var currentIndex = 0; // Index of the current background image

    function changeBackground() {
        $('body').css('background-image', 'url(' + images[currentIndex] + ')');
        currentIndex = (currentIndex + 1) % images.length; 
        // Move to the next image, loop back to the start if necessary
    }
    // Change background every 5 seconds
    setInterval(changeBackground, 5000);

    var interval = setInterval(function() {
        var momentNow = moment();
        $('#date').html(momentNow.format('dddd').substring(0, 3).toUpperCase() + ' - ' + momentNow.format('MMMM DD, YYYY'));
        $('#time').html(momentNow.format('hh:mm:ss A'));
    }, 1000);
    
    // Function to reset the page state after 10 seconds
    function resetPage() {
        $('.alert').hide();
        $('.img-fluid1').show();
        $('h1').show();
    }

    $('#attendance').submit(function(e) {
        e.preventDefault();

        // Check if submission is allowed
        if (!submissionAllowed) {
            return;
        }

        // Disable submission
        submissionAllowed = false;

        // Enable submission after 3 seconds
        setTimeout(enableSubmission, 3000);

        // Capture the value entered in the "Time to Clock In" field
        var timeToClockIn = $('#time_to_clock_in').val();
        // Construct the form data manually
        var attendance = {
            status: $('select[name="status"]').val(),
            employee: $('#employee').val(),
            time_to_clock_in: timeToClockIn
        };
        $.ajax({
            type: 'POST',
            url: 'manual_attendance.php',
            data: attendance,
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('.alert').hide();
                    $('.alert-danger').show();
                    $('.message').html(response.message);
                    $('#employee').val('');
                    $('.img-fluid1').hide();
                    $('h1').hide();
                } else {
                    $('.alert').hide();
                    $('.alert-success').show();
                    $('.message').html(response.message);
                    $('#employee').val('');
                    $('#employee').focus();
                    // Hide welcome text only
                    $('h1').hide();
                    // Optionally hide the GIF
                    $('.img-fluid1').hide();
                    
                    // Set a timer to reset the page state after 10 seconds
                    setTimeout(resetPage, 90000);

                    setTimeout(function() {
                            window.location.href = 'http://localhost/attendtrack/';
                        }, 5000);
                }
            }
        });
    });

    $('#employee').focus();
    $('select[name="status"]').change(function() {
        var selectedOption = $(this).val();
        if (selectedOption === "in") {
            $(this).css({
                'background-color': 'green',
                'color': 'white'
            });
            $('#employee').focus();
            $('button[type="submit"]').text("Sign In"); // Change button text to "Sign In"
        } else if (selectedOption === "out") {
            $(this).css({
                'background-color': 'red',
                'color': 'white'
            });
            $('#employee').focus();
            $('button[type="submit"]').text("Sign Out"); // Change button text to "Sign Out"
        }
    }).change();
});
setTimeout(function() {
    window.location.href = 'http://localhost/attendtrack/';
  }, 60000);

</script>
</body>
</html>