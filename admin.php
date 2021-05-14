<?php
require "config.php";
$conn = new Mysqli($db_config["host"], $db_config["usr"], $db_config["pwd"], $db_config["name"]) or exit();
$conn->set_charset($db_config["charset"]);

if($_GET['type']=='add' && $_POST['uid']  != ''){
    $rs = $conn->query("SELECT * FROM `user` WHERE `bili_uid` = '".$_POST['uid']."' limit 1");
    if (mysqli_num_rows($rs) != 0){
        $conn->query("UPDATE user SET shoujianren = '".$_POST['shoujianren']."',telephone='".$_POST['telephone']."',province='".$_POST['Province']."',city='".$_POST['City']."',district='".$_POST['District']."',xiangxi='".$_POST['xiangxi']."' WHERE bili_uid = '".$_POST['uid']."'");
        exit('<script>alert("修改信息成功!");window.history.go(-1);</script>');
    }
    $conn->query("INSERT INTO `user` (`bili_uid`) VALUES ('".$_POST['uid']."')");
    $conn->query("UPDATE user SET shoujianren = '".$_POST['shoujianren']."',telephone='".$_POST['telephone']."',province='".$_POST['Province']."',city='".$_POST['City']."',district='".$_POST['District']."',xiangxi='".$_POST['xiangxi']."' WHERE bili_uid = '".$_POST['uid']."'");
    exit('<script>alert("添加用户成功!");window.location.href="admin.php";</script>');
}elseif($_GET['type']=='set'){
    $conn->query("UPDATE config SET v = '".$_POST['gg']."' WHERE k = 'announce'");
    exit('<script>window.location.href="admin.php";</script>');
}

$rs = $conn->query("SELECT * FROM `user` WHERE `qq_openid` = '".$_COOKIE['social_uid']."' limit 1");
if (mysqli_num_rows($rs) != 1){
    setcookie("social_uid", '', time() - 604800, '/');
    exit('<script>window.location.href="index.php"</script>');
}else{
    $row = $rs->fetch_array(MYSQLI_ASSOC);
    if($row['id']!=1)exit('<script>window.location.href="user.php"</script>');
}
$rs = $conn->query("SELECT COUNT(*) as total FROM `user` WHERE 1");
$row2 = $rs->fetch_array(MYSQLI_ASSOC);
$total = $row2['total'];
$rs = $conn->query("SELECT COUNT(*) as total FROM `user` WHERE telephone != ''");
$row2 = $rs->fetch_array(MYSQLI_ASSOC);
$total_already = $row2['total'];
?>
<!DOCTYPE html>
<html lang="zh-cn">
    
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>后台管理首页</title>
        <link href="//lib.baomitu.com/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet" />
        <script src="//lib.baomitu.com/jquery/2.1.4/jquery.min.js"></script>
        <script src="//lib.baomitu.com/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="distpicker.min.js"></script>
        <!--[if lt IE 9]>
            <script src="//lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="//lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]--></head>
    
    <body>
        <nav class="navbar navbar-fixed-top navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">导航按钮</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./">后台管理</a></div>
                <!-- /.navbar-header -->
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="./">
                                <span class="glyphicon glyphicon-home"></span>首页</a>
                        </li>
                        <li class="active">
                            <a href="./">
                                <span class="glyphicon glyphicon-user"></span>后台</a>
                        </li>
                        </li>
                        <li>
                            <a href="./logout.php">
                                <span class="glyphicon glyphicon-log-out"></span>退出</a>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse --></div>
            <!-- /.container --></nav>
        <!-- /.navbar -->
        <div class="container" style="padding-top:70px;">
            <div class="col-xs-12 col-md-8 center-block" style="float: none;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">后台管理首页</h3></div>
                    <table class="table table-bordered">
                        <tbody>
                            <tr height="25">
                                <td align="center">
                                    <font color="#808080">
                                        <b>
                                            <span class="glyphicon glyphicon-tint"></span>用户数量</b>
                                        </br>
                                        <b>
                                            <?php echo $total; ?></b>个</font>
                                </td>
                                <td align="center">
                                    <font color="#808080">
                                        <b>
                                            <i class="glyphicon glyphicon-check"></i>已填地址</b>
                                        </br>
                                        </span>
                                        <b>
                                            <?php echo $total_already; ?></b>个</font>
                                </td>
                            </tr>
                            <tr height="25">
                                <td align="center" colspan="2">
                                    <div class="form-group col-md-12">
                                        <div class="input-group">
                                            <div class="input-group-addon">主播B站UID</div>
                                            <input class="form-control" type="text" value="<?php echo $row['bili_uid'] ?>" readonly>
                                            <div class="input-group-addon">
                                                <a href="javascript:;" class="copy-btn" data-clipboard-text="<?php echo getSystemConfig('live_room') ?>" title="点击复制">
                                                    <i class="glyphicon glyphicon-copy"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr height="25">
                                <td align="center" colspan="2">
                                    <a href="./list.php" class="btn btn-sm btn-info">
                                        <i class="glyphicon glyphicon-plus"></i>立即生成</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">增加/修改用户信息</h3></div>
                    <div class="panel-body">
                        <form action="admin.php?type=add" method="post" class="form-horizontal" role="form">
                            <label class="col-sm-2 control-label">B站UID</label>
                            <div class="col-sm-10"><input type="text" name="uid" class="form-control"></div><br><br>
                            <label class="col-sm-2 control-label">收件人</label>
                            <div class="col-sm-4"><input type="text" name="shoujianren" value="" class="form-control" placeholder=""></div>
                            <label class="col-sm-2 control-label">手机号</label>
                            <div class="col-sm-4"><input type="text" name="telephone" value="" class="form-control" placeholder=""></div><br><br>
                            <label class="col-sm-2 control-label">收货地址</label>
                            <div data-toggle="distpicker">
                            <div class="col-sm-3"><select class="form-control" name="Province" id="Province" data-province="<?php echo $row['province'] ?>"></select></div>
                            <div class="col-sm-3"><select class="form-control" name="City" id="City" data-city="<?php echo $row['city'] ?>"></select></div>
                            <div class="col-sm-3"><select class="form-control" name="District" id="District" data-district="<?php echo $row['district'] ?>"></select></div>
                            </div><br><br>
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10"><input type="text" placeholder="" name="xiangxi" class="form-control"></div><br><br>
                            <div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="提交" class="btn btn-primary form-control"></div><br>
                        </form>
                    </div>
                </div>
                
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">设置公告(支持html格式)</h3></div>
                    <div class="panel-body">
                        <form action="admin.php?type=set" method="post" class="form-horizontal" role="form">
                            <textarea class="form-control" name="gg" rows="6"><?php echo getSystemConfig('announce'); ?></textarea><br><br>
                            <input type="submit" name="submit" value="保存" class="btn btn-primary form-control">
                        </form>
                    </div>
                </div>
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">服务器信息</h3></div>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <b>PHP 版本：</b>
                            <?php echo phpversion() ?>
                                <?php if(ini_get( 'safe_mode')) { echo '线程安全'; } else { echo '非线程安全'; } ?></li>
                        <li class="list-group-item">
                            <b>服务器软件：</b>
                            <?php echo $_SERVER[ 'SERVER_SOFTWARE'] ?></li>
                        <li class="list-group-item">
                            <b>程序最大运行时间：</b>
                            <?php echo ini_get( 'max_execution_time') ?>s</li>
                        <li class="list-group-item">
                            <b>POST许可：</b>
                            <?php echo ini_get( 'post_max_size'); ?></li>
                        <li class="list-group-item">
                            <b>文件上传许可：</b>
                            <?php echo ini_get( 'upload_max_filesize'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <p style="text-align:center">
            <span style="font-weight:bold">©2021 Powered by <a href="https://space.bilibili.com/18913956" target="_blank">Blokura</a></span>
        </p>
        <script src="//lib.baomitu.com/layer/3.1.1/layer.js"></script>
        <script src="//lib.baomitu.com/clipboard.js/1.7.1/clipboard.min.js"></script>
<script>
$(document).ready(function() {
                var clipboard = new Clipboard('.copy-btn');
                clipboard.on('success',
                function(e) {
                    layer.msg('复制成功！', {
                        icon: 1
                    });
                });
                clipboard.on('error',
                function(e) {
                    layer.msg('复制失败，请长按链接后手动复制', {
                        icon: 2
                    });
                });
            });
$('#target').distpicker();
</script>