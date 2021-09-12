<?php
include 'config.php';
// redirect the user to specific link
!empty($_GET['vyz'])? $mod = $_GET['vyz'] : $mod = 'singup';

$title = 'Home';
$meta = 'meta';
$keywords = 'keywords';

// start the outputs temposrisation
ob_start();

switch($mod){
    case 'login': include 'view/login.php'; break;

    case 'stage_1': include 'view/stage_1.php'; break;

    case 'stage_2': include 'view/stage_2.php'; break;

    case 'stage_3': include 'view/stage_3.php'; break;

    case 'newfeed': include 'view/newfeed.php'; break;

    case 'mypage': include 'view/mypage.php'; break;

    case 'mylearning': include 'view/mylearning.php'; break;

    default: include 'view/singup.php';


} 
// end of outputs temporisation
$application = ob_get_clean();

include 'global/head.php';

echo $application;

include 'global/foot.php';
