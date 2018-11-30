<?php
ob_clean();
require_once "functions.php";

if(!isset($_GET['c_id']) || !isset($_GET['if_praise'])){
	send_json(3,'非法操作');
}

if($_GET['if_praise']!=1){//未点过赞
	//改数据库
	$conn=cc_mysqli_connect();
	$ret2=mysqli_query($conn,'select praise_num from tb_content where id = '.$_GET['c_id']);
	if($ret2->num_rows<1){
    	send_json(1,'点赞失败1');
    }
	$res2=mysqli_fetch_assoc($ret2);
	$praise_num=$res2['praise_num']+1;
    $values='praise_num = '.$praise_num;
    $res=mysqli_query($conn,'update tb_content set '.$values.' where id = '.$_GET['c_id']);
    if($res==false){
    	send_json(1,'点赞失败2');
    }
    //改cookie
    setcookie('praise',1,time()+86400);
    send_json(0,'点赞成功',$praise_num);
}
else{//已经点过赞
	//改数据库
	$conn=cc_mysqli_connect();
	$ret2=mysqli_query($conn,'select praise_num from tb_content where id = '.$_GET['c_id']);
	if($ret2->num_rows<1){
    	send_json(1,'取消点赞失败');
    }
	$res2=mysqli_fetch_assoc($ret2);
	$praise_num=$res2['praise_num']-1;
    $values='praise_num = '.$praise_num;
    $res=mysqli_query($conn,'update tb_content set '.$values.' where id = '.$_GET['c_id']);
    if($res==false){
    	send_json(1,'取消点赞失败');
    }
    //改cookie
    setcookie('praise',1,time()-86400);
    send_json(0,'取消点赞成功',$praise_num);
}


?>