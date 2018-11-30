<?php
ob_clean();
header('content-type:text/html;charset=utf-8');
require_once "functions.php";

if(isset($_GET['logout']) and $_GET['logout']==1){//退出登录
    if(!isset($_SESSION)){ 
        session_start(); 
    }
    $_SESSION['id']=null;
    alertjump('退出成功',cc_domain().'/wx_content/login.php'); 
}
else{

    if(cc_empty($_POST)){
    	$view=pathinfo(__FILE__);
    	include './'.$view['filename'].'.html';
    }
    else{

        //数据接收
        $data['username']=trim($_POST['u']);
        $data['password']=trim($_POST['p']);

        //数据验证
        foreach ($data as $k => $v) {
        	switch ($k) {
        		case 'username':
        			if(cc_empty($v)==true || mb_strlen($v)<6 || mb_strlen($v)>15 || preg_match_all("/^[\w]*$/", $v)==0){
        				send_json(1,'用户名不能为空，只允许数字和字母，且请控制名称长度在6至15以内');
        			}
        			break;
        		case 'password':
        			if(cc_empty($v)==true || mb_strlen($v)<6 || mb_strlen($v)>15 || preg_match_all("/^[\w]*$/", $v)==0){
        				send_json(1,'密码不能为空，只允许数字和字母，且请控制名称长度在10至15以内');
        			}
        			break;
        		default:
        			break;
        	}
        }

        //数据处理
        $data['username']=addslashes(trim($data['username']));
        $data['password']=addslashes(trim($data['password']));

    	//数据入库
        $conn=cc_mysqli_connect();

        $where='username="'.$data['username'].'"';
    	$ret=mysqli_query($conn,'select * from tb_config');
        $res=mysqli_fetch_all($ret,MYSQLI_ASSOC);
        
        $need_fuild=array('username','password');
        foreach($res as $k=>$v){
            if(in_array($v['e_name'], $need_fuild)){
                $need[$v['e_name']]=$v['value'];
            }
        }
    	
        if(strcmp($need['username'],$_POST['u'])!=0){
            send_json(1,'用户名错误');
        }
        else if(strcmp($_POST['p'],$need['password'])!=0){
            send_json(1,'密码错误');
        }   
        else{
            //存session
            if(!isset($_SESSION)){ 
                session_start(); 
            }
            $_SESSION['id']=md5($need['username']);
            /*
            //存cookie
            $login_cookie=array(
                'u'=>$res['username'],
                'p'=>md5($res['password'])
             );
            setcookie('login',serialize($login_cookie),time()+3600*24,'/');
            */
            send_json(0,'登陆成功',cc_domain().'/wx_content/content_list.php'); 
        }
    	
    }
}

?>