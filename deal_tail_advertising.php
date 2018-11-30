<?php
ob_clean();
require_once "functions.php";
	$data['img']=deal_uploadone('img');//图片验证
	$data['id']=time();

	if(isset($_GET['c_id'])){//有文章id则前台换图
		//数据入库
	    $conn=cc_mysqli_connect();
		$field='img,id';
		$values='"'.$data["img"].'","'.$data["id"].'"';
		$res=mysqli_query($conn,'insert into tb_tail_advertising ('.$field.') values ('.$values.')');
		$res==true?send_json(0,'上传成功',$data['id']):send_json(1,'上传失败');
	}
	else{//无则后台上传
		$data['user_id']=0;
		(isset($data['img']) and !cc_empty($data['img']))?send_json(0,'上传成功',$data['img']):send_json(1,'上传失败');
	}
	

	
	   
	
	



?>