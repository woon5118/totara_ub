<div class="container">
    <div class="row no-gutters">
        <div class="col x-log">
            <h1><?php echo "Holon Learning";?>
            <hr />
        </div>
    </div>
    <div class="row no-gutters justify-content-center">
        <div class="col-md-5 xx-div">
            <div class="col text-left">
                <h2><?php echo "Create your account";?></h2>
            </div>
            <form method="post" action="">
                <div class="col-auto">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><img src="<?php echo IMG_ICONS."envelope.svg";?>" /></div>
                        </div>
                        <input type="email" class="form-control" placeholder="E-mail address">
                    </div>
                </div> 
                <div class="col-auto">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><img src="<?php echo IMG_ICONS."unlock.svg";?>" /></div>
                        </div>
                        <input type="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><img src="<?php echo IMG_ICONS."arrow-clockwise.svg";?>" /></div>
                        </div>
                        <input type="password" class="form-control" placeholder="Re-Password">
                    </div>
                </div>
                <div class="col-auto text-left">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="term_of_use">
                        <label class="form-check-label" for="term_of_use">
                        <?php echo "I accept <a href='#'>Terms of Use</a> & Info collection & usage Agreement";?>
                        </label>
                    </div>
                </div>
                <div class="col-auto text-left">
                    <div class="form-check mb-3">
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
            <a href="index.php?vyz=login" ><?php echo "Log-in";?></a>
        </div>
    </div>
</div>