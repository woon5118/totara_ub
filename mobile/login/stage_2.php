<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Holon Learning | Home page</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="stylesheet" href="../assets/css/bootstrap.css" />
<link rel="stylesheet" href="../assets/css/style.css" />
<style>

</style>
<script src="assets/js/bootstrap.js"></script>
<body>
<div class="container-fluid text-center">
    <div class="row">
        <div class="col-sm xx-log">
            <h2 ><?php echo "Choose the keys words you are interested in.";?></h2>
            <hr />
        </div>
    </div>
    <?php
        for($i=1; $i <= 6; $i++){
    ?>
    <div class="row x-keyword">
        <div class="col"><button class="btn">key word</button></div>
        <div class="col"><button class="btn">key word</button></div>
        <div class="col"><button class="btn">key word</button></div>
    </div>
    <?php 
        }
    ?>
    <div class="row">
        <div class="col-sm x-log">
            <a href="stage_3.php" class="btn btn-primary"><?php echo "Next";?> </a>
        </div>
    </div>
</div>
<?php
include '../foot.php';
?>