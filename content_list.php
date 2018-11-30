<?php
ob_clean();
header('content-type:text/html;charset=utf-8');
require_once "functions.php";
//dump($_SERVER);
check_login();
$arr=cc_page('tb_content');

$conn=cc_mysqli_connect();

$ret=mysqli_query($conn,'select * from tb_content limit '.$arr['start'].','.$arr['many']);
$res=mysqli_fetch_all($ret,MYSQLI_ASSOC);

$view=pathinfo(__FILE__);
include './'.$view['filename'].'.html';



?>