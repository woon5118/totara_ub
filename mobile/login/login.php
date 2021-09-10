<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Holon Learning | Login</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="stylesheet" href="../assets/css/bootstrap.css" />
<link rel="stylesheet" href="../assets/css/style.css" />
<style>

</style>
<script src="assets/js/bootstrap.js"></script>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm x-log">
            <h1><?php echo "Holon Learning";?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm xx-div">
            <div class="col-sm text-left">
                <h2><?php echo "Login";?></h2>
                <hr />
            </div>
            <form method="post" action="">
                <div class="col-auto">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><img src="../assets/img/envelope.svg" /></div>
                        </div>
                        <input type="email" class="form-control" placeholder="E-mail address">
                    </div>
                </div> 
                <div class="col-auto">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><img src="../assets/img/unlock.svg" /></div>
                        </div>
                        <input type="password" class="form-control" placeholder="Password">
                    </div>
                </div>  
                <div class="col-auto">
                    <input type="submit" class="btn btn-primary btn-lg btn-block" value="Log-in" />
                </div>
            </form>
            <div class="col-sm text-right">
                <a href="#" class=""><?php echo "Forgot your password?";?></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm yy-log">
            <span class="text-center"><?php echo "Create your account?";?></span>
            <a href="singup.php" class="btn btn-primary btn-lg btn-block"><?php echo "Sing-up";?></a>
        </div>
    </div>
</div>
<?php
include '../foot.php';
?>