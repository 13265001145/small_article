<?php
ob_clean();
require_once "functions.php";
//数据接收
        $data['username']='';
        $data['password']=;

        //数据验证
        foreach ($data as $k => $v) {
        	switch ($k) {
        		case 'u':
        			if(cc_empty($v)==true || mb_strlen($v)<6 || mb_strlen($v)>15 || preg_match_all("/^[\w]*$/", $v)==0){
        				send_json(1,'用户名不能为空，只允许数字和字母，且请控制名称长度在6至15以内');
        			}
        			break;
        		case 'p':
        			if(cc_empty($v)==true || mb_strlen($v)<10 || mb_strlen($v)>15 || preg_match_all("/^[\w]*$/", $v)==0){
        				send_json(1,'密码不能为空，只允许数字和字母，且请控制名称长度在10至15以内');
        			}
        			break;
        		default:
        			break;
        	}
        }


?>