<?php
require "config.php";
require "excel.php";
$conn = new Mysqli($db_config["host"], $db_config["usr"], $db_config["pwd"], $db_config["name"]) or exit();
$conn->set_charset($db_config["charset"]);
$rs = $conn->query("SELECT * FROM `user` WHERE `qq_openid` = '".$_COOKIE['social_uid']."' limit 1");
if (mysqli_num_rows($rs) != 1){
    setcookie("social_uid", '', time() - 604800, '/');
    exit('<script>window.location.href="index.php"</script>');
}else{
    $row = $rs->fetch_array(MYSQLI_ASSOC);
    if($row['id']!=1)exit('<script>window.location.href="user.php"</script>');
}

$json = json_decode(file_get_contents('https://api.live.bilibili.com/xlive/app-room/v2/guardTab/topList?roomid=1&page=1&ruid='.$row['bili_uid'].'&page_size=30'),true);
$page = $json['data']['info']['page'];
$now = 1;
$xls = new \Excel_XML();
$array[] = ['绑定ID','用户UID','用户昵称','用户排名','舰队类型','勋章等级','收件人','手机号','地址','备注'];
foreach($json['data']['top3'] as $bin){
        if($bin['guard_level']==1){
            $guard_level = '总督';
        }elseif($bin['guard_level']==2){
            $guard_level = '提督';
        }else{
            $guard_level = '舰长';
        }
        $rs = $conn->query("SELECT * FROM `user` WHERE `bili_uid` = '".$bin['uid']."' limit 1");
        if (mysqli_num_rows($rs) == 1){
            $row2 = $rs->fetch_array(MYSQLI_ASSOC);
            $bdid = $row2['id'];
            $shoujianren = $row2['shoujianren'];
            $telephone = $row2['telephone'];
            $province = $row2['province'];
            $city = $row2['city'];
            $district = $row2['district'];
            $xiangxi =$row2['xiangxi'];
        }else{
            $bdid = '未绑定';
            $shoujianren = '';
            $telephone = '';
            $province = '';
            $city = '';
            $district = '';
            $xiangxi = '';
        }
        $array[] = [$bdid,$bin['uid'],$bin['username'],$bin['rank'],$guard_level,$bin['medal_info']['medal_level'],$shoujianren,$telephone,$province.$city.$district.$xiangxi];
}
while ($now <= $page){
    foreach($json['data']['list'] as $bin){
        if($bin['guard_level']==1){
            $guard_level = '总督';
        }elseif($bin['guard_level']==2){
            $guard_level = '提督';
        }else{
            $guard_level = '舰长';
        }
        $rs = $conn->query("SELECT * FROM `user` WHERE `bili_uid` = '".$bin['uid']."' limit 1");
        if (mysqli_num_rows($rs) == 1){
            $row2 = $rs->fetch_array(MYSQLI_ASSOC);
            $bdid = $row2['id'];
            $shoujianren = $row2['shoujianren'];
            $telephone = $row2['telephone'];
            $province = $row2['province'];
            $city = $row2['city'];
            $district = $row2['district'];
            $xiangxi =$row2['xiangxi'];
        }else{
            $bdid = '未绑定';
            $shoujianren = '';
            $telephone = '';
            $province = '';
            $city = '';
            $district = '';
            $xiangxi = '';
        }
        $array[] = [$bdid,$bin['uid'],$bin['username'],$bin['rank'],$guard_level,$bin['medal_info']['medal_level'],$shoujianren,$telephone,$province.$city.$district.$xiangxi];
    }
    if($now == $page)break;
    $now = $now + 1;
    $json = json_decode(file_get_contents('https://api.live.bilibili.com/xlive/app-room/v2/guardTab/topList?roomid=1&page='.$now.'&ruid='.$row['bili_uid'].'&page_size=30'),true);
    $page = $json['data']['info']['page'];
    $now = $json['data']['info']['now'];
}
$xls->addWorksheet('Sheet1', $array);
$xls->sendWorkbook('list.xls');