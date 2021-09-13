<div class="container text-center">
    <div class="row no-gutters text-center">
        <div class="col xx-log">
            <h2 ><?php echo "Choose the keys words you are interested in.";?></h2>
            <hr/>
        </div>
    </div>
    <?php
        for($i=1; $i <= 6; $i++){
    ?>
    <div class="row x-keyword no-gutters justify-content-center">
        <div class="col-md-3"><button class="btn">key word</button></div>
        <div class="col-md-3"><button class="btn">key word</button></div>
        <div class="col-md-3"><button class="btn">key word</button></div>
    </div>
    <?php 
        }
    ?>
    <div class="row">
        <div class="col-sm x-log">
            <a href="index.php?vyz=stage_3" class="btn btn-primary"><?php echo "Next";?> </a>
        </div>
    </div>
</div>