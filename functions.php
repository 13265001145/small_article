<?php
ob_clean();

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

function alertjump($alert='',$jump=''){
    if($alert!='')echo '<script>alert("'.$alert.'");</script>';
    
    if(strcmp($jump,'back')==0){
        echo '<script type="text/javascript">location.href=history.back();</script>';
    }
    else{
        echo '<script>location.href="'.$jump.'"</script>';
    }
    die();
}

function cc_empty($param){
    if(!isset($param) || empty($param) || $param=='' || $param==null || $param=='null')return true;
    else return false;
}

//session令牌，防止表单重复提交
function session_token($handon=0,$handon_token=''){
    if(!isset($_SESSION)){ 
        session_start(); 
    }
    if($handon==0){
        $_SESSION['token']=md5(time());
        return $_SESSION['token'];
    }
    else if($handon==1){
        //表单提交时，如果没有token或者不等于session就die
        if(!isset($_SESSION['token']) || $_SESSION['token']==null || (strcmp($_SESSION['token'], $handon_token)!=0)){
            is_ajax()?send_json(1,'nothing'):die();
        }
        //否则置空session
        $_SESSION['token']=null;   
    }
    else{
        die('非法操作');
    }
}


function is_ajax() {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
}


function send_json( $err_code = 0 , $msg = 'ok' , $data = array() )
{
    die(json_encode(array(
        'err_code' => $err_code,
        'msg' => $msg , 
        'data' => $data , 
    )));
}


function deal_uploadone($input_name,$arr_type=array('jpg','jpeg','png','pjpeg','gif','bmp'),$size=1048576){

        if($_FILES[$input_name]['error']!=0)
        {
            is_ajax()==true?send_json(1,'图片上传失败'):alertjump('图片上传失败','back');
        }
        //图片格式检查
        $allow_type=$arr_type;
        $gettype=pathinfo($_FILES[$input_name]['name']);
        $type=$gettype['extension'];

        if(!in_array(strtolower($type),$allow_type))
        {
            is_ajax()==true?send_json(1,'图片格式不允许'):alertjump('图片格式不允许','back');
        }
        //图片大小检查
        if($_FILES[$input_name]['size']>$size)
        {
            is_ajax()==true?send_json(1,'图片大小只允许在'.$size.'字节内'):alertjump('图片大小只允许在'.$size.'字节内','back');
        }

        //文件夹判断
        $top_upload_dir=$_SERVER['DOCUMENT_ROOT'].'/Upload/image';
        if(!is_dir($top_upload_dir)){
            if(!mkdir($top_upload_dir)){
                is_ajax()==true?send_json(1,'上传目录创建失败'):alertjump('上传目录创建失败','back');
            };
        }

        $today_dir=date('Ymd');
        if(!is_dir($top_upload_dir.'/'.$today_dir)){
            if(!mkdir($top_upload_dir.'/'.$today_dir)){
                is_ajax()==true?send_json(1,'上传目录创建失败'):alertjump('上传目录创建失败','back');
            };
        }

        $file_url='/Upload/image/'.$today_dir.'/'.time().'.'.$type;
        $tag_file=$_SERVER['DOCUMENT_ROOT'].$file_url;

        if(move_uploaded_file($_FILES[$input_name]['tmp_name'], $tag_file)==false){
            is_ajax()==true?send_json(1,'图片移动失败'):alertjump('图片移动失败','back');
        };
                
        return $file_url;
       
}

function cc_domain(){
    if(strncasecmp($_SERVER['HTTP_HOST'],'http://',6)==0){
        return $_SERVER['HTTP_HOST'];
    }
    else{
        return 'http://'.$_SERVER['HTTP_HOST'];
    }
}

function cc_page($tb,$many=10){
    //当前页
    isset($_GET['p'])?$p=$_GET['p']:$p=1;

    //单页显示数量
    //$many=10;

    //总条目数
    $cc_page_conn=cc_mysqli_connect();
    $total=mysqli_fetch_assoc(mysqli_query($cc_page_conn,'SELECT COUNT(*) as total FROM '.$tb));
    mysqli_close($cc_page_conn);
    $total=$total['total'];

    //页数
    $num_p=ceil($total/$many);

    //获取去除p后的url
    $query_str=$_SERVER['QUERY_STRING'];
    $cut_p=strrchr($query_str,'&p=');//$cut_p=substr($str,strrpos($str,'&p='));
    $af_cut_p=str_ireplace($cut_p,'',$query_str);  
    $url=cc_domain().$_SERVER['SCRIPT_NAME'].'?'.$af_cut_p;

    //生成html
    $html=' <a href="'.$url.'&p=1">首页</a>&nbsp&nbsp&nbsp';
    if($p>1){
        $html.='<a href="'.$url.'&p='.($p-1).'">上一页</a>&nbsp&nbsp&nbsp';
    }
    if($p<$num_p){
        $html.='<a href="'.$url.'&p='.($p+1).'">下一页</a>&nbsp&nbsp&nbsp';
    }
    $html.=' <a href="'.$url.'&p='.$num_p.'">最尾页</a>&nbsp&nbsp&nbsp';

    //返回
    $arr=array(
        'many'=>$many,
        'start'=>($p-1)*$many,
        'html'=>$html,
    );

    return $arr;
}



function cc_mysqli_connect(){
    include './config.php';
    $host=$config['db']['host'];
    $user=$config['db']['user'];
    $pwd=$config['db']['pwd'];
    $db=$config['db']['dbname'];
    $conn=mysqli_connect($host,$user,$pwd,$db);
    mysqli_set_charset($conn,"utf8");

    return $conn;
}


function check_login(){
    if(!isset($_SESSION)){ 
        session_start(); 
    }
    if(!isset($_SESSION['id']) || cc_empty($_SESSION['id'])){
        is_ajax()==true?send_json(8,'请先登录'):alertjump('请先登录',cc_domain().'/wx_content/login.php');
    }

}


?>


