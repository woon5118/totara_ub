<div class="container-fluid">
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
        <div class="col-12 s-x-div">
            <ul>
                <li><?php echo "Learning circle";?><img src="<?php echo IMG_ICONS."chevron-right.svg";?>"/>
                    <ul>
                    <?php
                        for($i=1; $i<=5; $i++)
                        {
                        ?>
                        <li><?php echo "Cycle #".$i;?></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <li><?php echo "Competency Learning";?><img src="<?php echo IMG_ICONS."chevron-right.svg";?>"/>
                    <ul>
                        <?php
                        for($i=1; $i<=5;$i++)
                        {
                        ?>
                        <li><?php echo "Competency #".$i;?></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <li><?php echo "Learning plan";?><img src="<?php echo IMG_ICONS."chevron-right.svg";?>"/>
                    <ul>
                        <?php
                        for($i=1; $i <= 5; $i++)
                        {
                        ?>
                        <li><?php echo "Plan #".$i;?></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <li><?php echo "Course";?><img src="<?php echo IMG_ICONS."chevron-right.svg";?>"/>
                    <ul>
                        <?php 
                            for($i=1; $i<=5; $i++)
                            {
                            ?>
                            <li><?php echo "Category name #".$i;?></li>
                            <?php
                            }
                            ?>
                        </li>
                    </ul>
                <li><?php echo "Community";?><img src="<?php echo IMG_ICONS."chevron-right.svg";?>"/>
                    <ul>
                    <?php 
                    $com = array('Notic', 'Q&A', 'Free board', 'Promotion', 'FAQ');
                    for($i=0; $i< count($com); $i++)
                    {
                    ?>
                    <li><?php echo $com[$i]; ?></li>
                    <?php
                    }
                    ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>