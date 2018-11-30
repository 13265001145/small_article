<?php
ob_clean();
require_once "functions.php";
    if(!isset($_GET['id'])){
        send_json(3,'非法操作');
    }
	$conn=cc_mysqli_connect();
	$res=mysqli_query($conn,'delete from tb_content where id = '.$_GET['id']);
	$res==true?send_json(0,'删除成功'):send_json(1,'删除失败');
	



?>