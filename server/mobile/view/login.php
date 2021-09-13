<div class="container">
    <div class="row no-gutters">
        <div class="col x-log">
            <h1><?php echo "Holon Learning";?></h1>
            <hr />
        </div>
    </div>
    <div class="row no-gutters justify-content-center">
        <div class="col-md-5 xx-div">
            <div class="col-sm text-left">
                <h2><?php echo "Login";?></h2>
                <hr />
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
                    <input type="submit" class="btn btn-primary btn-lg btn-block" value="Log-in" />
                </div>
            </form>
            <div class="col-sm text-right">
                <a href="#" class=""><?php echo "Forgot your password?";?></a>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-5 yy-log">
            <span class="text-center"><?php echo "Create your account?";?></span>
            <a href="?vyz=singup" class="btn btn-outline-primary btn-lg btn-block"><?php echo "Sing-up";?></a>
        </div>
    </div>
</div>