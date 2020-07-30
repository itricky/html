<?php
require_once("conndb.php");
session_start();

if(!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){
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

if(isset($_GET["action"]) && ($_GET["action"]=="delete")){
    $query_delMember="DELETE FROM memberdata WHERE m_id=?";
    $stmt=$db_link->prepare($query_delMember);
    $stmt->execute();
    $stmt->close();
}

$query_RecAdmin = "SELECT m_id, m_name, m_logintime FROM memberdata WHERE m_username=?";
$stmt=$db_link->prepare($query_RecAdmin);
$stmt->bind_parm("s", $_SESSION["loginMember"]);
$stmt->execute();
$stmt->bind_result($mid,$mname, $mlogintime);
$stmt->fetch();
$stmt->close();

$pageRow_records =5;
$num_pages = 1;
if(isset($_GET['page'])){
    $num_pages = $_GET['apge'];
}

$startRow_records=($num_pages -1)*$pageRow_records;

$query_RecMember="SELECT * FROM memberdata WHERE m_level<>'admin' ORDER BY m_jointime DESC";

$query_limit_RecMember = $query_RecMember."LIMIT{$startRow_records},{$pageRow_records}";

$RecMember = $db_link->query($query_limit_RecMember);
$all_RecMember=$db_link->query($query_RecMember);
$total_records=$allRecMember->num_rows;


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