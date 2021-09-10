<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Holon Learning | Main</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/bootstrap.css" />
<link rel="stylesheet" href="../assets/css/style.css" />
<script src="assets/js/bootstrap.js"></script>
<body>
<div class="container-fluid text-center">
    <div class="row no-gutters">
        <div class="col-2 i-xx-div">
            <nav class="navbar navbar-light">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </nav>
        </div>
        <div class="col-8 i-xx-div">
            <h1><?php echo "Holon Learning"; ?></h1>
        </div>
        <div class="col-2 i-xx-div">
            <img src="../assets/img/user.png" alt="User" class="profil"/>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-xs-12 col-sm-12">
            <h3><?php echo "Mypage";?></h3>
            <hr />
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-sm-12">
            <h3><?php echo "Popular learning circle list";?></h3>
            <?php
                for($i=1; $i<=2; $i++)
                {
            ?>
            <div class="row i-course">
                <div class="col-3">
                    <img src="../assets/img/course1.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 1";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course_1.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 2";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course3.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 3";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course2.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 4";?></a>
                    </span>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
        <div class="col-sm-12">
            <a href="#"><?php echo "More";?></a>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-sm i-banner">
            <?php echo "Promotion Banner"; ?> 
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-sm">
            <h3><?php echo "Popular  competency learning list";?></h3>
            <?php
                for($i=1; $i<=2; $i++)
                {
            ?>
            <div class="row i-course">
                <div class="col-3">
                    <img src="../assets/img/course1.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 1";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course_1.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 2";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course3.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 3";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course2.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 4";?></a>
                    </span>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-sm i-banner">
            <?php echo "Promotion Banner"; ?> 
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-sm">
            <h3><?php echo "Recently  user's plaining list";?></h3>
            <?php
                for($i=1; $i<=2; $i++)
                {
            ?>
            <div class="row i-users">
                <div class="col-3">
                    <img src="../assets/img/user.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "User name";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/user.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "User name";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/user.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "User name";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/user.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "User name";?></a>
                    </span>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-sm i-banner">
            <?php echo "Promotion Banner"; ?> 
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            <h3><?php echo "Popular course list";?></h3>
            <?php
                for($i=1; $i<=2; $i++)
                {
            ?>
            <div class="row i-course">
                <div class="col-3">
                    <img src="../assets/img/course1.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 1";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course_1.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 2";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course3.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 3";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="../assets/img/course2.png" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 4";?></a>
                    </span>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
</div>
<?php
include '../foot.php';
?>