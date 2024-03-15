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
            background-attachment: fixed;
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
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            transition: background-color 0.3s ease-in-out;
        }

        .container:hover {
            background-color: rgba(255, 255, 255, 1);
        }

        .login-logo img {
            max-width: 100%;
            max-height: 100%;
        }

        h4 {
            color: #007bff;
        }

        select,
        input,
        button {
            margin-bottom: 20px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: 1px solid #007bff;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
        }

        .alert-danger {
            background-color: red;
            border: 1px solid #f5c6cb;
            color: white;
        }

        #date,
        #time {
            color: black;
            font-family: Arial, sans-serif;
            font-size: 25px;
            margin-bottom: 20px;
        }
        .alert-success{
          background-color: #AFE1AF;
            border: 1px solid #AFE1AF;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4 login-logo">
            <img class="img-fluid" src="images/UC1.png" alt="Logo">
        </div>
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
        <div class="alert alert-success text-center" style="display:none;">
            <span class="result"><i class="icon fa fa-check"></i> <span class="message"></span></span>
        </div>
        <div class="alert alert-danger text-center" style="display:none;">
            <span class="result"><i class="icon fa fa-warning"></i> <span class="message"></span></span>
        </div>
    </div>
    <?php include 'scripts.php' ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script type="text/javascript">
        $(function() {
            var interval = setInterval(function() {
                var momentNow = moment();
                $('#date').html(momentNow.format('dddd').substring(0, 3).toUpperCase() + ' - ' + momentNow.format('MMMM DD, YYYY'));
                $('#time').html(momentNow.format('hh:mm:ss A'));
            }, 1000);
            $('#attendance').submit(function(e) {
                e.preventDefault();
                var attendance = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: 'attendance.php',
                    data: attendance,
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            $('.alert').hide();
                            $('.alert-danger').show();
                            $('.message').html(response.message);
                        } else {
                            $('.alert').hide();
                            $('.alert-success').show();
                            $('.message').html(response.message);
                            $('#employee').val('');
                            $('#employee').focus();
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
                } else if (selectedOption === "out") {
                    $(this).css({
                        'background-color': 'red',
                        'color': 'white'
                    });
                    $('#employee').focus();
                }
            }).change();
        });
    </script>
</body>
</html>
