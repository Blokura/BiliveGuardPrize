<?php
require_once './gt/class.geetestlib.php';
require_once './gt/config.php';
require 'config.php';
if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]){
    $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
}elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]){
    $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
}elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]){
    $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
}elseif (getenv("HTTP_X_FORWARDED_FOR")){
    $ip = getenv("HTTP_X_FORWARDED_FOR");
}elseif (getenv("HTTP_CLIENT_IP")){
    $ip = getenv("HTTP_CLIENT_IP");
}elseif (getenv("REMOTE_ADDR")){
    $ip = getenv("REMOTE_ADDR");
}else{
    $ip = "127.0.0.1";
}

function isMobile(){  
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';  
    $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';        
    function CheckSubstrs($substrs,$text){  
        foreach($substrs as $substr)  
            if(false!==strpos($text,$substr)){  
                return true;  
            }  
            return false;  
    }
    $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
    $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');  

    $found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||  
              CheckSubstrs($mobile_token_list,$useragent);  

    if ($found_mobile){  
        return true;  
    }else{  
        return false;  
    }  
}

if(isMobile()){
$client_type = 'h5';
}else{
$client_type = 'web';
}

session_start();
$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
$data = array(
        "user_id" => $_SESSION['user_id'],
        "client_type" => $client_type,
        "ip_address" => $ip
);
if ($_SESSION['gtserver'] == 1) {
    $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
    if (!$result) {
        exit('<script type="text/javascript">alert("错误:请先完成极验验证再提交!");window.location = "https://'.$_SERVER['HTTP_HOST'].'?code='.$_GET['code'].'";</script>');
    }
}else{
    if (!$GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
        exit('<script type="text/javascript">alert("错误:请先完成极验验证再提交!");window.location = "https://'.$_SERVER['HTTP_HOST'].'?code='.$_GET['code'].'";</script>');
    }
}

$uid = $_POST['uid'];
if($uid == ""){
  exit('<script type="text/javascript">alert("请填写正确的B站UID!");window.location = "https://'.$_SERVER['HTTP_HOST'].'?code='.$_GET['code'].'";</script>');
}

$conn = new Mysqli($db_config["host"], $db_config["usr"], $db_config["pwd"], $db_config["name"]) or exit();
$conn->set_charset($db_config["charset"]);
$rs = $conn->query("SELECT * FROM `user` WHERE `bili_uid` = '".$uid."' limit 1");
if (mysqli_num_rows($rs) == 1){
    $row = $rs->fetch_array(MYSQLI_ASSOC);
    if($row['qq_openid']==""){
        $conn->query("UPDATE user SET qq_openid = '".$_GET['social_uid']."' WHERE bili_uid = ".$uid);
        setcookie("social_uid", $_GET['social_uid'], time() + 604800, '/');
        exit('<script>window.location.href="./user.php"</script>');
    }else{
        exit('<script>alert("该B站账号已被绑定,如有疑问,请联系管理员。");window.history.go(-1);</script>');
    }
}

$json = json_decode(file_get_contents("http://api.bilibili.com/x/space/acc/info?mid=".$uid."&ts=".time()));
if(strpos($json->data->sign,$_GET['social_uid']) != false || $json->data->sign == $_GET['social_uid']){ 
    //包含
    $conn->query("INSERT INTO `user` (`qq_openid`, `bili_uid`) VALUES ('".$_GET['social_uid']."', '".$_POST['uid']."')");
    setcookie("social_uid", $_GET['social_uid'], time() + 604800, '/');
    exit('<script>window.location.href="./user.php"</script>');
}else{
    exit("你的个性签名【".$json->data->sign."】没有找到绑定码，请<a href='https://space.bilibili.com/' target='_blank'>修改个性签名</a>，包含【".$_GET['social_uid']."】再试!&nbsp;&nbsp;&nbsp;<a href='index.php?code=".$_GET['code']."'>返回</a>");
}
?>