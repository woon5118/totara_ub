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
            <img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="User" class="profil"/>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-xs-12 col-sm-12 my-x-div">
            <h3><?php echo "Mypage";?></h3>
            <hr />
        </div>
    </div>
    <div class="row no-gutters my-x-div">
        <div class="col-12">
            <h4><?php echo "My learning circle";?></h4>
        </div>
        <?php 
            for($i=1; $i <= 3; $i++)
            {
        ?>
        <div class="row no-gutters my-y-div">
            <div class="col-xs-4 ml-2 mr-2 mt-1 mb-1">
                <img src="<?php echo IMG_ACCESSORIES."user.png";?>" alt="IMG" class="img-thumbnail lc"/>
            </div>
            <div class="col-xs-8 ml-2 mr-2 mt-1 mb-1">
                <h4><?php echo "Learning circle title ".$i; ?></h4>
                <span><?php echo $i * 100;?> <img src="<?php echo IMG_ICONS."person.svg";?>" />/<?php echo " 13 ";?><img src="<?php echo IMG_ICONS."award.svg";?>" /></span>
            </div>
        </div>
        <?php
            }
        ?>
        <div class="col-12 more">
            <span><img src="<?php echo IMG_ICONS."chevron-down.svg";?>"/><?php echo " More";?></span>
        </div>
    </div>
    <div class="row no-gutters my-x-div">
        <div class="col-12">
            <h4><?php echo "My Competency learning";?></h4>
        </div>
        <?php 
            for($i=1; $i <= 3; $i++)
            {
        ?>
        <div class="row no-gutters my-y-div">
            <div class="col-xs-4 ml-2 mr-2 mt-1 mb-1">
                <img src="<?php echo IMG."competency1.png";?>" alt="IMG" class="img-thumbnail lc"/>
            </div>
            <div class="col-xs-8 ml-2 mr-2 mt-1 mb-1">
                <h4><?php echo "Competency title ".$i; ?></h4>
                <span><?php echo " 13 ";?><img src="<?php echo IMG_ICONS."award.svg";?>" /></span>
            </div>
        </div>
        <?php
            }
        ?>
        <div class="col-12 more">
            <span><img src="<?php echo IMG_ICONS."chevron-down.svg";?>"/><?php echo " More";?></span>
        </div>
    </div>
    <div class="row no-gutters my-x-div">
        <div class="col-12">
            <h4><?php echo "My Learning plan";?></h4>
        </div>
        <?php 
            for($i=1; $i <= 3; $i++)
            {
        ?>
        <div class="row no-gutters my-y-div p-1">
            <h4 class="col-12"><?php echo "Competency title ".$i; ?></h4>
            <span class="col-2"><?php echo " 13 ";?><img src="<?php echo IMG_ICONS."award.svg";?>" /></span>
            <div class="col-10">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $i * 30;?>%;" aria-valuenow="<?php echo $i * 30;?>" aria-valuemin="0" aria-valuemax="100"><?php echo $i * 30;?>%</div>
                </div>
            </div>
        </div>
        <?php
            }
        ?>
        <div class="col-12 more">
            <span><img src="<?php echo IMG_ICONS."chevron-down.svg";?>"/><?php echo " More";?></span>
        </div>
    </div>
    <div class="row no-gutters my-x-div">
        <div class="col-12">
            <h4><?php echo "My Course";?></h4>
        </div>
        <?php 
            for($i=1; $i <= 3; $i++)
            {
        ?>
        <div class="row no-gutters my-y-div">
            <div class="col-2 ml-2 mr-2 mt-1 mb-1">
                <img src="<?php echo IMG."cours.jpeg";?>" alt="IMG" class="img-thumbnail lc"/>
            </div>
            <div class="col-9 ml-2 mr-2 mt-1 mb-1">
                <h4><?php echo "Learning circle title ".$i; ?></h4>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $i * 30;?>%;" aria-valuenow="<?php echo $i * 30;?>" aria-valuemin="0" aria-valuemax="100"><?php echo $i * 30;?>%</div>
                </div>
            </div>
        </div>
        <?php
            }
        ?>
        <div class="col-12 more">
            <span><img src="<?php echo IMG_ICONS."chevron-down.svg";?>"/><?php echo " More";?></span>
        </div>
    </div>
</div>