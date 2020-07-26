<?php
require_once("conndb.php");
session_start();

if( !isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){
    header("Location: index.php");
}


if(isset($_GET["logout"]) && ($_GET["logout"]=="true")){
    unset($_SESSION["loginMember"]);
    unset($_SESSION["memberLevel"]);
    header("Location: index.php");
}


?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>歡迎光臨</title>
</head>
<body>
<p>member_center</p><br>
<p>普通會員登入畫面</p><br>




<a href="?logout=true">登出</a>
<a href="member_updata.php">修改資料</a>
</body>
</html>