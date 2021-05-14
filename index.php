<?php
require "config.php";
if($_COOKIE['social_uid']==""){
    if($_GET['code'] == ''){
        if($_SERVER['HTTPS']=='on')$s='s';
        $json = json_decode(file_get_contents('http://bbs.moebili.com/connect.php?appid='.getSystemConfig('qq_appid').'&appkey='.getSystemConfig('qq_appkey').'&type=qq&act=login&redirect_uri=http'.$s.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
        if($json->code != '0')exit('<script>alert("'.$json->msg.'");window.location.href="./index.php"</script>');
        exit('<script>window.location.href="'.$json->url.'"</script>');
    }else{
        $json = json_decode(file_get_contents('http://bbs.moebili.com/connect.php?appid='.getSystemConfig('qq_appid').'&appkey='.getSystemConfig('qq_appkey').'&type=qq&act=callback&code='.$_GET['code']));
        if($json->code != '0')exit('<script>alert("'.$json->msg.'");window.location.href="./index.php"</script>');
        $conn = new Mysqli($db_config["host"], $db_config["usr"], $db_config["pwd"], $db_config["name"]) or exit();
        $conn->set_charset($db_config["charset"]);
        $rs = $conn->query("SELECT * FROM `user` WHERE `qq_openid` = '".$json->social_uid."' limit 1");
        if (mysqli_num_rows($rs) == 1){
            $conn->query("UPDATE user SET last_login = now() WHERE qq_openid = '".$json->social_uid."'");
            setcookie("social_uid", $json->social_uid, time() + 604800, '/');
            exit('<script>window.location.href="user.php"</script>');
        }
    }
}else{
    $conn = new Mysqli($db_config["host"], $db_config["usr"], $db_config["pwd"], $db_config["name"]) or exit();
    $conn->set_charset($db_config["charset"]);
    $rs = $conn->query("SELECT * FROM `user` WHERE `qq_openid` = '".$_COOKIE['social_uid']."' limit 1");
    if (mysqli_num_rows($rs) == 1){
        $conn->query("UPDATE user SET last_login = now() WHERE qq_openid = '".$_COOKIE['social_uid']."'");
        exit('<script>window.location.href="user.php"</script>');
    }else{
        setcookie("social_uid", "", time() - 604800, '/');
        exit('<script>window.location.href="index.php"</script>');
    }
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>舰队领奖系统</title>
  <link href="//lib.baomitu.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="//lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
  <script src="//lib.baomitu.com/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="./gt/gt.js"></script>
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
        <a class="navbar-brand">舰队领奖系统</a>
      </div><!-- /.navbar-header -->
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li class="active">
            <a href="./"><span class="glyphicon glyphicon-user"></span> 首页</a>
          </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
  </nav><!-- /.navbar -->
  <div class="container" style="padding-top:70px;">
  	<div class="col-xs-12 col-sm-8 col-lg-8 center-block" style="float: none;">
  	<div class="alert alert-info"><h4>公告</h4><br><?php echo getSystemConfig('announce'); ?></div>
   <div class="alert alert-warning"><h4>领取步骤</h4><br/>1.修改B站<b>个性签名</b>，包含<?php echo $json->social_uid ?><br>2.在下方输入你的B站UID<br>3.完成账号绑定操作<br/>4.填写收货地址</div>
   </div>
    </div>
    </div>
  <div class="container" style="padding-top:0px;">
    <div class="col-xs-12 col-sm-8 col-lg-8 center-block" style="float: none;">
  <div class="panel panel-danger">
            <div class="panel-heading"><h3 class="panel-title">绑定B站账号</h3></div>
  <div class="panel-body">
  <form action="blind.php?code=<?php echo $_GET['code']?>&social_uid=<?php echo $json->social_uid ?>" method="POST">
      <div class="input-group">
      请先<a href="https://space.bilibili.com/" target="_blank">修改</a>你的B站个性签名，包含【<font color="red"><?php echo $json->social_uid ?></font>】
      </div><br/>
       <div class="input-group">
              <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
              <input type="text" name="uid" class="form-control" placeholder="你的B站UID" required="required" onkeyup="value=value.replace(/[^\d]/g,'') " ng-pattern="/[^a-zA-Z]/" value="<?php echo $_GET['uid']?>"/>
       </div><br/>
         <div id="embed-captcha" class="small"></div>
         <p id="wait" >正在加载验证码,若长时间无反应请刷新页面</p><br/>
        <!---</div>---><br/>
       <div class="form-group">
              <div class="col-xs-12"><input type="submit" value="立即绑定" class="btn btn-danger form-control"/></div>
       </div>
    </form>
        </div>
      </div>
    </div>
  </div>

  <p style="text-align:center"><span style="font-weight:bold">©2021 Powered by <a href="https://space.bilibili.com/18913956" target="_blank">Blokura</a></span></p>
      <script>
    var handlerEmbed = function (captchaObj) {
        $("#submit").click(function (e) {
            var validate = captchaObj.getValidate();
            if (!validate) {
                $("#notice")[0].style = "";
                setTimeout(function () {
                    $("#notice")[0].style = "display:none;";
                }, 2000);
                e.preventDefault();
            }
        });
        captchaObj.appendTo('#embed-captcha');
        captchaObj.onReady(function () {
            $("#wait")[0].style = "display:none;";
        });
    };
    $.ajax({
        url: "./gt/StartCaptchaServlet.php?t=" + (new Date()).getTime(), // 加随机数防止缓存
        type: "get",
        dataType: "json",
        success: function (data) {
            console.log(data);
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                new_captcha: data.new_captcha,
                product: "float", 
                https: true ,
                offline: !data.success, 
                width: '100%',
                lang: 'zh-cn',
            }, handlerEmbed
    );
        }
    });
</script>
  </body>