<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Holon Learning | Home page</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="stylesheet" href="../assets/css/bootstrap.css" />
<link rel="stylesheet" href="../assets/css/style.css" />
<style>
.container-customer img{
    border:1px solide black;
    width:100px;
    height: 100px
}
.c-learning{
    padding-top:0.4rem;
    padding-bottom:0.4rem;
}
.p-learning{
    padding-top:0.4rem;
    padding-top:0.4rem;
}
.p-learning div{
    /* border:1px solid black; */
}
.p-learning div a{
    color:black;
}
.img-rounded{
    border:1px solid #ccc;
    border-radius:100% !important;
}
.x-learning > span{
    display:block;
    border:1px solid #fff;
    background-color: #ccc;
}
</style>
<script src="assets/js/bootstrap.js"></script>
<body>
<div class="container-fluid container-customer text-center">
    <div class="row">
        <div class="col-sm xx-log">
            <h2><?php echo "Would recommend to you."; ?></h2>
            <span><?php echo "Choose what you want and enjoy.";?></span>
            </hr>
        </div>
    </div>
    <div class="row learning">
        <div class="col-sm x-learning">
            <span class="col-sm"><?php echo "Fou you ... Learning circle";?></span>
        </div>
        <?php 
            for($i=1; $i <= 2; $i++){
        ?>
        <div class="row c-learning">
        <div class="col">
            <img src="../assets/img/course1.png" alt="IMG" class="img-thumbnail img-fluid"/>
            <span><a href="#">Course 1</a></span>
        </div>
        <div class="col">
            <img src="../assets/img/course2.png" alt="IMG" class="img-thumbnail img-fluid"/>
            <span><a href="#">Course 2</a></span>
        </div>
        <div class="col">
            <img src="../assets/img/course3.png" alt="IMG" class="img-thumbnail img-fluid"/>
            <span><a href="#">Course 3</a></span>
        </div>
            </div>
        <?php
            }
        ?>
    </div>
    <div class="row learngin">
        <div class="col-sm x-learning">
            <span class="col-sm"><?php echo "For you ... Competency learning"; ?></span>
        </div>
        <?php 
            for($i=1; $i <= 2; $i++){
        ?>
        <div class="row c-learning">
        <div class="col">
            <img src="../assets/img/course_1.png" alt="IMG" class="img-thumbnail img-fluid"/>
            <span><a href="#">Course 1</a></span>
        </div>
        <div class="col">
            <img src="../assets/img/course_2.png" alt="IMG" class="img-thumbnail img-fluid"/>
            <span><a href="#">Course 2</a></span>
        </div>
        <div class="col">
            <img src="../assets/img/course_3.png" alt="IMG" class="img-thumbnail img-fluid"/>
            <span><a href="#">Course 3</a></span>
        </div>
            </div>
        <?php
            }
        ?>
    </div>
    <div class="row learning">
        <div class="col-sm x-learning">    
            <span class="col-sm"><?php echo "For you ... User's learning plan";?></span>
        </div>
        <?php 
            for($i=1; $i <= 2; $i++){
        ?>
        <div class="row p-learning">
        <div class="col">
            <img src="../assets/img/user.png" alt="IMG" class="img-rounded"/>
            <span><a href="#">User name</a></span>
        </div>
        <div class="col">
            <img src="../assets/img/user.png" alt="IMG" class="img-rounded"/>
            <span><a href="#">User name</a></span>
        </div>
        <div class="col">
            <img src="../assets/img/user.png" alt="IMG" class="img-rounded"/>
            <span><a href="#">User name</a></span>
        </div>
            </div>
        <?php
            }
        ?>
    </div>
</div>
<?php
include '../foot.php';
?>