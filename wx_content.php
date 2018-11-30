<?php
ob_clean();
header('content-type:text/html;charset=utf-8');
require_once "functions.php";


    if(!isset($_GET['c_id'])){
        die('非法操作');
    }
    $conn=cc_mysqli_connect();
    $ret=mysqli_query($conn,'select * from tb_content where id = '.$_GET['c_id']);
    $res=mysqli_fetch_assoc($ret);
    $res['click_num']+=1;
    mysqli_query($conn,'update tb_content set click_num = '.($res['click_num']).' where id = '.$_GET['c_id']);

    if(isset($_GET['i_id'])){
        $ret2=mysqli_query($conn,'select * from tb_tail_advertising where id='.$_GET['i_id']);
        $res2=mysqli_fetch_assoc($ret2);
        $res['default_advertising']=$res2['img'];
    }

    $res['default_advertising']=cc_domain().$res['default_advertising'];


	$st=session_token();
	$view=pathinfo(__FILE__);
	include './'.$view['filename'].'.html';




?>