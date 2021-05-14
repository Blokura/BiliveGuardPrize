<?php
header('Content-type:text/html;charset=utf-8');
error_reporting(0);
global $db_config;

$db_config = array(
	'host' => 'localhost', //数据库服务器
	'usr' => 'prize', //数据库用户名
	'pwd' => 'BHhchXsi2DN5TYkB', //数据库密码
	'name' => 'prize', //数据库名
    'charset' => 'utf8'//数据库字符集
);

function check_input($data) {
    //对特殊符号添加反斜杠
    $data = addslashes($data);
    //判断自动添加反斜杠是否开启
    if (get_magic_quotes_gpc()) {
        //去除反斜杠
        $data = stripslashes($data);
    }
    //把'_'过滤掉
    $data = str_replace("_", "\_", $data);
    //把'%'过滤掉
    $data = str_replace("%", "\%", $data);
    //把'*'过滤掉
    $data = str_replace("*", "\*", $data);
    //回车转换
    $data = nl2br($data);
    //去掉前后空格
    $data = trim($data);
    //将HTML特殊字符转化为实体
    $data = htmlspecialchars($data);
    return $data;
}

function getSystemConfig($name) {
	global $db_config;
    $conn = new Mysqli($db_config["host"], $db_config["usr"], $db_config["pwd"], $db_config["name"]) or exit(); //连接数据库
    $conn->set_charset($db_config["charset"]); //设置字符集
    $rs = $conn->query("SELECT * FROM `config` WHERE `k` = '".$name."' limit 1");
    $row = $rs->fetch_array(MYSQLI_ASSOC);
    return $row['v'];
}
?>