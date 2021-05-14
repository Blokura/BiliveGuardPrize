<?php
require "config.php";
if($_COOKIE['social_uid'] == '')exit('<script>window.location.href="index.php"</script>');
$json = json_decode(file_get_contents('http://bbs.moebili.com/connect.php?appid='.getSystemConfig('qq_appid').'&appkey='.getSystemConfig('qq_appkey').'&type=qq&act=query&social_uid='.$_COOKIE['social_uid']));
$conn = new Mysqli($db_config["host"], $db_config["usr"], $db_config["pwd"], $db_config["name"]) or exit();
$conn->set_charset($db_config["charset"]);
$rs = $conn->query("SELECT * FROM `user` WHERE `qq_openid` = '".$json->social_uid."' limit 1");
if (mysqli_num_rows($rs) != 1){
    setcookie("social_uid", '', time() - 604800, '/');
    exit('<script>window.location.href="index.php"</script>');
}
$row = $rs->fetch_array(MYSQLI_ASSOC);
if($row['qq_openid'] != $_COOKIE['social_uid'])exit('<script>window.location.href="index.php"</script>');
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>地址设置</title>
  <link href="//lib.baomitu.com/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="//lib.baomitu.com/jquery/2.1.4/jquery.min.js"></script>
  <script src="//lib.baomitu.com/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="distpicker.min.js"></script>
  <!--[if lt IE 9]>
    <script src="//lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
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
        <a class="navbar-brand" href="./">舰队领奖系统</a>
      </div><!-- /.navbar-header -->
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li class="active">
            <a href="./"><span class="glyphicon glyphicon-home"></span> 首页</a>
          </li>
          <?php
          if($row['id']==1)echo '<li class="">
            <a href="./admin.php"><span class="glyphicon glyphicon-user"></span> 后台</a>
          </li>'
          ?>
          <li>
              <a href="./logout.php"><span class="glyphicon glyphicon-log-out"></span> 退出</a>
          </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
  </nav><!-- /.navbar -->
  
  
<div class="container" style="padding-top:70px;">
	<div class="row">
		<div class="col-xs-11 col-sm-10 col-lg-8 center-block" style="float: none;">
					  <div class="panel panel-primary" id="recharge">
			<div class="panel-heading" style="background: linear-gradient(to right,#14b7ff,#b221ff);padding: 15px;">				
			  <div class="widget-content text-right clearfix">
				<a href="account"><img src="<?php echo $json->faceimg; ?>" alt="Avatar" width="66" class="img-circle img-thumbnail img-thumbnail-avatar pull-left"></a>
				<h3 class="widget-heading h4"><strong><?php echo $json->nickname; ?></strong></h3>
			  </div>
			</div>
	<table class="table">
	<tbody>
		<tr>
			<th class="text-center">
				<font color="#a9a9a9">用户ID</font><br><font size="4"><?php echo $row['id'];?></font>
			</th>
			<th class="text-center">
				<font color="#a9a9a9">B站UID</font><br><font size="4"><?php echo $row['bili_uid'];?></font>
			</th>
		</tr>
      </tbody>
	</table>
<div class="panel-body">
  <form action="./set.php" method="post" class="form-horizontal" role="form">
	<div class="form-group">
	  <label class="col-sm-2 control-label">收件人</label>
	  <div class="col-sm-4"><input type="text" name="shoujianren" value="<?php echo $row['shoujianren'] ?>" class="form-control" placeholder=""></div>
	  <label class="col-sm-2 control-label">手机号</label>
	  <div class="col-sm-4"><input type="text" name="telephone" value="<?php echo $row['telephone'] ?>" class="form-control" placeholder="非大陆请加区号(如+852)"></div>
	</div>
	<hr>
	<div class="form-group">
	  <label class="col-sm-2 control-label">收货地址</label>
	  <div data-toggle="distpicker">
	  <div class="col-sm-3"><select class="form-control" name="Province" id="Province" data-province="<?php echo $row['province'] ?>"></select></div>
	  <div class="col-sm-3"><select class="form-control" name="City" id="City" data-city="<?php echo $row['city'] ?>"></select></div>
	  <div class="col-sm-3"><select class="form-control" name="District" id="District" data-district="<?php echo $row['district'] ?>"></select></div>
	  </div>
	  <br>
	  <label class="col-sm-2 control-label"></label>
	  <div class="col-sm-10"><input type="text" placeholder="" name="xiangxi" value="<?php echo $row['xiangxi'] ?>" class="form-control"></div>
	</div><br>
	<div class="form-group">
	  <div class="col-sm-offset-2 col-sm-10"><input type="submit" value="保存地址" class="btn btn-primary form-control"><br>
	 </div>
	</div>
  </form>
</div>
</div>
<p style="text-align:center"><span style="font-weight:bold">©2021 Powered by <a href="https://space.bilibili.com/18913956" target="_blank">Blokura</a></span></p>

<script>
$('#target').distpicker();
</script>