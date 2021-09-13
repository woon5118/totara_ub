<div class="container text-center">
    <div class="row no-gutters">
        <div class="col-4 i-xx-div">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" href="#"></a>
                </li>
                </ul>
            </div>
            
        </nav>
        </div>
        <div class="col-6">
            <h1><?php echo "Holon Learning";?></h1>
        </div>
        <div class="col-2 i-xx-div">
            <a href="mypage.php"><img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="User" class="profil"/></a>
        </div>
    </div>
    <hr /> 
    <div class="row no-gutters">
        <div class="col-xs-12 col-sm-12 ii-xx-div">
            <form method="method" class="form-inline">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Serch">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><img src="<?php echo IMG_ICONS."search.svg";?>" /></div>
                    </div>
                </div> 
            </form>
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
                    <img src="<?php echo IMG."course1.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 1";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course_1.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 2";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course3.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 3";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course2.png";?>" alt="course" class="img-thumbnail"/>
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
                    <img src="<?php echo IMG."course1.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 1";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course_1.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 2";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course3.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 3";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course2.png";?>" alt="course" class="img-thumbnail"/>
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
                    <img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "User name";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "User name";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "User name";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="course" class="img-thumbnail"/>
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
                    <img src="<?php echo IMG."course1.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 1";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course_1.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 2";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course3.png";?>" alt="course" class="img-thumbnail"/>
                    <span>
                    <a href="#"><?php echo "Course 3";?></a>
                    </span>
                </div>
                <div class="col-3">
                    <img src="<?php echo IMG."course2.png";?>" alt="course" class="img-thumbnail"/>
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