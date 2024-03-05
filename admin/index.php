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

    <div class="background-container" style="background-image: url('/apsystem/images/bgc1.png'); background-size: cover; display: flex; align-items: center; justify-content: center; min-height: 100vh;">
    <img src="/apsystem/images/logo.png" alt="Your Image" style="width: 100%; max-width: 250px; position: absolute; top: 14%; right: 50%; transform: translate(50%, -50%);">
            <div class="login-box max-width-600 mx-auto" style="border: 20px solid white; background-color: white; border-radius: 10px;">
                <div class="login-logo font-family-Bahnschrift-Light-SemiCondensed font-size-20 text-align-right margin-right-70">
                    <b>Login as an Admin user</b>
                </div>
                <div class="text-align-right margin-right-10">
                    <p class="login-box-msg">Sign in to start your session</p>
                </div>

                <form action="login.php" method="POST">
    <div class="form-group">
        <input type="text" class="form-control border-radius-10" name="username" placeholder="Input Username" required autofocus style="border-radius: 10px;">
    </div>
    <div class="form-group">
        <input type="password" class="form-control border-radius-10" name="password" placeholder="Input Password" required style="border-radius: 10px;">
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat border-radius-10" name="login" required autofocus style="border-radius: 10px;">
                <i class="fa fa-sign-in"></i> Sign In
            </button>
        </div>
    </div>
</form>


                <?php
                if (isset($_SESSION['error'])) {
                    echo "
                        <div class='callout callout-danger text-center mt-2'>
                            <p>" . $_SESSION['error'] . "</p> 
                        </div>
                    ";
                    unset($_SESSION['error']);
                }
                ?>
            </div>
        </div>
    </div>

    <?php include 'includes/scripts.php' ?>
</body>

</html>
