<?php
require_once("conndb.php");
session_start();

if(!isset
($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){
header("Location: index.php");
}

if($_SESSION["memberLevel"]=="member"){
    header("Location: member_center.php");
}

if(isset($_GET["logout"])&&($_GET["logout"]=="true")){
    unset($_SESSION["loginMember"]);
    unset($_SESSION["memberLevel"]);
    header("Location: index.php");
}




?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>網站會員系統</title>
<link href="style.css" rel="stylesheet" type="text/css">

</html>


<body>
<table width="800" border="0" align="center" cellpaddinf="4">





</body>