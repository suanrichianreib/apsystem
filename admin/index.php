<?php
session_start();
if (isset($_SESSION['admin'])) {
    header('location:home.php');
}
?>
<!DOCTYPE html>
<link rel="icon" href="/attendtrack/images/UC1.png" type="image/x-icon" />
<html lang="en"> <?php include 'includes/header.php'; ?> <style>
    /* .circle {
        width: 69px;
        height: 69px;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 50%;
        position: absolute;
        top: 166%;
        left: 136%;
        transform: translate(-50%, -50%);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: 2px solid violet;
        background-image: url('/attendtrack/images/programmer.gif');
        background-size: cover;
        transition: background-image 1s ease-in-out; /* Added transition for smoother image changes */
    z-index: 0;
    /* Ensure it's behind the content */
    }

    */ .login-form {
        max-width: 300px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .btn-primary {
        background-color: #5E17EB;
        color: #fff;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #8C52FF;
    }
</style>

<body class="hold-transition login-page">
    <img src="/attendtrack/images/UC1.png" alt="Your Image" style="width: 100%; max-width: 500px; position: absolute; top: 265px; left: 180px; z-index: 9999;">
    <div class="background-container" style="background-image: url('/attendtrack/images/violet1.jpg'); background-size: full; display: flex; align-items: center; justify-content: center; min-height: 100vh;">
        <div class="login-box max-width-600 mx-auto" style="background-image: url('/attendtrack/images/purple.jpg'); border-radius: 10px; width: 80%; max-width: 500px; padding: 20px; position: relative; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5); margin-left: 710px; ">
            <div class="circle"></div>
            <div class="row" style="margin-bottom: 5px; margin-right: -10px;">
                <div class="col-xs-12 col-md-5">
                    <div class="login-logo font-family-Bahnschrift-Light-SemiCondensed font-size-10 text-align-center">
                    </div>
                </div>
                <div> <img src="/attendtrack/images/admin.png" alt="Your Image" style="width: 100%; max-width: 200px; margin-top: 20px; margin-left: -170px; "> </div>
            </div>
        </div>
        <div style="position: fixed; top: 55%; left: 50%; transform: translate(-50%, -50%);">
            <div class="row" style="margin-top: -70px; margin-left: 250px;">
                <div class="col-xs-12 col-md-20 col-md-offset-12">
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control border-radius-10" name="username" placeholder="Username" required autofocus>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control border-radius-10" name="password" placeholder="Password" required>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-primary btn-block border-radius-10" name="login">
                                    <i class="fa fa-sign-in"></i> Sign In </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div> <?php
    if (isset($_SESSION['error'])) {
        echo "
                <div class='alert alert-danger text-center mt-3' role='alert'>
                    <p>" . $_SESSION['error'] . "</p> 
                </div>
            ";
        unset($_SESSION['error']);
    }
    ?> <?php include 'includes/scripts.php' ?> <div style="position: fixed; bottom: 10px; left: 50%; transform: translateX(-50%);">
      <p>&copy; PAWER RENJERS 2024</p>
    </div>
</body>
</body>

</html>