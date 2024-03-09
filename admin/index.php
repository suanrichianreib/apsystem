<?php
session_start();
if (isset($_SESSION['admin'])) {
    header('location:home.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'includes/header.php'; ?>

<body class="hold-transition login-page">
    <div class="background-container" style="background-image: url('/attendtrack/images/bgc1.png'); background-size: cover; display: flex; align-items: center; justify-content: center; min-height: 100vh;">
        <div class="login-box max-width-600 mx-auto" style="background-color: #ffffff; border-radius: 5px; width: 80%; max-width: 400px; padding: 20px;">

            <div class="login-logo font-family-Bahnschrift-Light-SemiCondensed font-size-24 text-align-center">
                <img src="/attendtrack/images/logo.png" alt="Your Image" style="width: 100%; max-width: 200px;">
                <p class="margin-top-10"><b>Admin</b></p>
            </div>

            <div class="text-align-center margin-top-20">
                <p class="login-box-msg">Sign in to start your session</p>
            </div>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control border-radius-10" name="username" placeholder="Input Username" required autofocus>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control border-radius-10" name="password" placeholder="Input Password" required>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block border-radius-10" name="login">
                            <i class="fa fa-sign-in"></i> Sign In
                        </button>
                    </div>
                </div>
            </form>

            <?php
            if (isset($_SESSION['error'])) {
                echo "
                        <div class='alert alert-danger text-center mt-3' role='alert'>
                            <p>" . $_SESSION['error'] . "</p> 
                        </div>
                    ";
                unset($_SESSION['error']);
            }
            ?>
        </div>
    </div>

    <?php include 'includes/scripts.php' ?>
</body>

</html>