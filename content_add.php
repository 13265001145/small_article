<?php
ob_clean();
header('content-type:text/html;charset=utf-8');
require_once "functions.php";
check_login();
if(cc_empty($_POST)){
    if(isset($_GET['id'])){//有get的id就是修改
        $conn=cc_mysqli_connect();
        $ret=mysqli_query($conn,'select * from tb_content where id = '.$_GET['id']);
        $res=mysqli_fetch_assoc($ret);
    }
	$st=session_token();
	$view=pathinfo(__FILE__);
	include './'.$view['filename'].'.html';
}
else{
	//令牌验证
	session_token(1,$_POST['st']);  
    unset($_POST['st']);

    //数据验证
    
    foreach ($_POST as $k => $v) {
    	switch ($k) {
    		case 'title':
    			if(cc_empty($v)==true || mb_strlen($v)<2 || mb_strlen($v)>30){
    				send_json(1,'标题不能为空，且请控制长度在2至10以内');
    			}
    			break;
    		case 'sender':
                if(cc_empty($v)==true){
                    $_POST[$k]='';
                }
                else if(mb_strlen($v)<1 || mb_strlen($v)>10){
                    send_json(1,'请控制发送者长度在1至10以内');
                }
    			break;
    		case 'sender_link':
                if(cc_empty($v)==true){
                    $_POST[$k]='#';
                }
                else if(mb_strlen($v)>100 || preg_match_all("|(\w+):\/\/([^/:]+)(:\d*)?([^#]*)|", $v)==0){
                    send_json(1,'路径格式错误，或请控制发送者链接长度在100以内');
                }
    			break;
            case 'readall_link':
                if(cc_empty($v)==true){
                    $_POST[$k]='#';
                }
                else if(mb_strlen($v)>100 || preg_match_all("|(\w+):\/\/([^/:]+)(:\d*)?([^#]*)|", $v)==0){
                    send_json(1,'路径格式错误，或请控制阅读全文链接长度在100以内');
                }
                break;
            case 'details':
                if(cc_empty($v)==true){
                    send_json(1,'文章内容不能为空');
                }
                break;
            case 'default_advertising':
                if(mb_strlen($v)>70 || preg_match_all("|^[\w\\\/\.]*$|", $v)==0){
                    send_json(1,'路径格式错误，且请控制长度在70以内');
                }
                break;
    		default:
                send_json(3,'非法操作');
    			break;
    	}
    }

    //数据处理
    if(isset($_GET['id'])){//修改
        $data['title']=addslashes(trim($_POST['title']));
        $data['sender']=addslashes(trim($_POST['sender']));
        $data['sender_link']=addslashes(trim($_POST['sender_link']));
        $data['readall_link']=addslashes(trim($_POST['readall_link']));
        $data['details']=addslashes(trim($_POST['details']));
        $data['default_advertising']=addslashes(trim($_POST['default_advertising']));

        //数据入库
        $conn=cc_mysqli_connect();

        $values='title="'.$data["title"].'",sender="'.$data["sender"].'",sender_link="'.$data["sender_link"].'",readall_link="'.$data["readall_link"].'",details="'.$data["details"].'",default_advertising="'.$data["default_advertising"].'"';
        $res=mysqli_query($conn,'update tb_content set '.$values.' where id = '.$_GET['id']);
        
        $res==true?send_json(0,'修改成功'):send_json(1,'修改失败');
    }
    else{//增加
        $data['title']=addslashes(trim($_POST['title']));
        $data['sender']=addslashes(trim($_POST['sender']));
        $data['sender_link']=addslashes(trim($_POST['sender_link']));
        $data['readall_link']=addslashes(trim($_POST['readall_link']));
        $data['details']=addslashes(trim($_POST['details']));
        $data['default_advertising']=addslashes(trim($_POST['default_advertising']));
        $data['id']=time();
        $data['click_num']=0;
        $data['praise_num']=0;

        //数据入库
        $conn=cc_mysqli_connect();

        $field='title,sender,sender_link,readall_link,details,default_advertising,id,click_num,praise_num';
        $values='"'.$data["title"].'","'.$data["sender"].'","'.$data["sender_link"].'","'.$data["readall_link"].'","'.$data["details"].'","'.$data["default_advertising"].'","'.$data["id"].'","'.$data["click_num"].'","'.$data["praise_num"].'"';
        $res=mysqli_query($conn,'insert into tb_content ('.$field.') values ('.$values.')');
        
        $res==true?send_json(0,'发布成功',$data):send_json(1,'发布失败');
    }
    
}


?>