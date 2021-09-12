<div class="container-fluid container-customer text-center">
    <div class="row no-gutters justify-content-center">
        <div class="col xx-log">
            <h2><?php echo "Would recommend to you."; ?></h2>
            <span><?php echo "Choose what you want and enjoy.";?></span>
            </hr>
        </div>
    </div>
    <div class="row no-gutters justify-content-center">
        <div class="col-md-8 x-learning">
            <span><?php echo "Fou you ... Learning circle";?></span>
            <?php 
                for($i=1; $i <= 2; $i++)
                {
            ?>
            <div class="row no-gutters i-course justify-content-center">
                <div class="col-4 col-md-3">
                    <img src="<?php echo IMG."course1.png";?>" alt="IMG" class="img-thumbnail img-fluid"/>
                    <span><a href="#">Course 1</a></span>
                </div>
                <div class="col-4 col-md-3">
                    <img src="<?php echo IMG."course2.png";?>" alt="IMG" class="img-thumbnail img-fluid"/>
                    <span><a href="#">Course 2</a></span>
                </div>
                <div class="col-4 col-md-3">
                    <img src="<?php echo IMG."course3.png";?>" alt="IMG" class="img-thumbnail img-fluid"/>
                    <span><a href="#">Course 3</a></span>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
    <div class="row learngin no-gutters justify-content-center">
        <div class="col-md-8 x-learning">
            <span class="col-sm"><?php echo "For you ... Competency learning"; ?></span>
            <?php 
                for($i=1; $i <= 2; $i++){
            ?>
            <div class="row i-course c-learning no-gutters justify-content-center">
            <div class="col-4 col-md-3">
                <img src="<?php echo IMG."course_1.png";?>" alt="IMG" class="img-thumbnail img-fluid"/>
                <span><a href="#">Course 1</a></span>
            </div>
            <div class="col-4 col-md-3">
                <img src="<?php echo IMG."course_2.png";?>" alt="IMG" class="img-thumbnail img-fluid"/>
                <span><a href="#">Course 2</a></span>
            </div>
            <div class="col-4 col-md-3">
                <img src="<?php echo IMG."course_3.png"?>" alt="IMG" class="img-thumbnail img-fluid"/>
                <span><a href="#">Course 3</a></span>
            </div>
                </div>
            <?php
                }
            ?>
        </div>
    </div>
    <div class="row learning no-gutters justify-content-center">
        <div class="col-md-8 x-learning">    
            <span class="col-sm"><?php echo "For you ... User's learning plan";?></span>
            <?php 
                for($i=1; $i <= 2; $i++){
            ?>
            <div class="row p-learning i-users no-gutters justify-content-center">
                <div class="col-4 col-md-3">
                    <img src="<?php echo IMG_ACCESSORIES."user.png"?>" alt="IMG" class="img-rounded"/>
                    <span><a href="#">User name</a></span>
                </div>
                <div class="col-4 col-md-3">
                    <img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="IMG" class="img-rounded"/>
                    <span><a href="#">User name</a></span>
                </div>
                <div class="col-4 col-md-3">
                    <img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="IMG" class="img-rounded"/>
                    <span><a href="#">User name</a></span>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
    <div class="row no-gutters justify-content-center">
        <a href="index.php?vyz=newfeed"><?php echo "New feed";?></a>
    </div>
</div>