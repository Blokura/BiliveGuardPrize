<?php
require "config.php";
if($_POST['shoujianren'] == "" || $_POST['telephone'] == "" || $_POST['Province'] == "" || $_POST['City'] == "" || $_POST['District'] == "" || $_POST['xiangxi'] == "")exit('<script>alert("请填写完整收货信息!");window.history.go(-1);</script>');

if(substr($_POST['telephone'],0,1) != "+"){
    if(!preg_match("/^1[3456789]\d{9}$/", $_POST['telephone'])){
    exit('<script>alert("手机号格式不正确!");window.history.go(-1);</script>');
    }
}
$conn = new Mysqli($db_config["host"], $db_config["usr"], $db_config["pwd"], $db_config["name"]) or exit();
$conn->set_charset($db_config["charset"]);
$rs = $conn->query("SELECT * FROM `user` WHERE `qq_openid` = '".$_COOKIE['social_uid']."' limit 1");
if (mysqli_num_rows($rs) != 1){
    setcookie("social_uid", '', time() - 604800, '/');
    exit('<script>alert("登录过期,请重新登录!");window.location.href="index.php"</script>');
}
$row = $rs->fetch_array(MYSQLI_ASSOC);
$conn->query("UPDATE user SET shoujianren = '".$_POST['shoujianren']."',telephone='".$_POST['telephone']."',province='".$_POST['Province']."',city='".$_POST['City']."',district='".$_POST['District']."',xiangxi='".$_POST['xiangxi']."' WHERE qq_openid = '".$_COOKIE['social_uid']."'");
exit('<script>alert("保存成功!");window.location.href="user.php"</script>');
?>