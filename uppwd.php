<?php
ob_clean();
header('content-type:text/html;charset=utf-8');
require_once "functions.php";

check_login();

    if(cc_empty($_POST)){
        $conn=cc_mysqli_connect();
        $ret=mysqli_query($conn,'select * from tb_config where e_name = "username"');
        $res=mysqli_fetch_assoc($ret);
    	$view=pathinfo(__FILE__);
    	include './'.$view['filename'].'.html';
    }
    else{

        //数据接收
        $data['username']=trim($_POST['u']);
        $data['old_p']=trim($_POST['old_p']);
        $data['password']=trim($_POST['p']);

        //数据验证
        foreach ($data as $k => $v) {
        	switch ($k) {
                case 'username':
                    if(cc_empty($v)==true || mb_strlen($v)<6 || mb_strlen($v)>15 || preg_match_all("/^[\w]*$/", $v)==0){
                        send_json(1,'账号不能为空，只允许数字和字母，且请控制名称长度在6至15以内');
                    }
                    break;
        		case 'old_p':
        			if(cc_empty($v)==true || mb_strlen($v)<6 || mb_strlen($v)>15 || preg_match_all("/^[\w]*$/", $v)==0){
        				send_json(1,'旧密码不能为空，只允许数字和字母，且请控制名称长度在6至15以内');
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
        $data['old_p']=addslashes(trim($data['old_p']));
        $data['password']=addslashes(trim($data['password']));

    	//数据入库
        $conn=cc_mysqli_connect();

    	$ret=mysqli_query($conn,'select * from tb_config');
        $res=mysqli_fetch_all($ret,MYSQLI_ASSOC);
        
        $need_fuild=array('username','password');
        foreach($res as $k=>$v){
            if(in_array($v['e_name'], $need_fuild)){
                $need[$v['e_name']]=$v['value'];
            }
        }
    	
        if(strcmp($_POST['old_p'],$need['password'])!=0){
            send_json(1,'旧密码错误');
        }   
        else{
            mysqli_query($conn,'update tb_config 
                                set value = case e_name 
                                when "username" then "'.$data['username'].'" 
                                when "password" then "'.$data['password'].'"
                                end 
                                where e_name in ("username","password")
                            ');
            send_json(0,'修改成功',cc_domain().'/wx_content/content_list.php'); 
        }
    	
    }


?>