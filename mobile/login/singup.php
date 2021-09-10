<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Holon Learning | Sing-up</title>
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
            <h1><?php echo "Holon Learning";?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm xx-div">
            <div class="col-sm text-left">
                <h2><?php echo "Create your account";?></h2>
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
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><img src="../assets/img/arrow-clockwise.svg" /></div>
                        </div>
                        <input type="password" class="form-control" placeholder="Re-Password">
                    </div>
                </div>
                
                <div class="col-auto">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="autoSizingCheck">
                        <label class="form-check-label" for="autoSizingCheck">
                        <?php echo "I accept <a href='#'>Terms of Use</a> & Info collection & usage Agreement";?>
                        </label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="autoSizingCheck">
                        <label class="form-check-label" for="autoSizingCheck">
                        <?php echo "Receive ads & promotional contents";?>
                        </label>
                    </div>
                </div>  
                <div class="col-auto">
                    <input type="submit" class="btn btn-primary btn-lg btn-block" value="Sign-up" />
                </div>
            </form>
            <br/>
            <a href="login.php" ><?php echo "Log-in";?></a>
        </div>
    </div>
</div>
<?php
include '../foot.php';
?>