<?php
  require_once("conndb.php");
  session_start();
  
  if(!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){
      header("Location: index.php");
  }
  
  if(isset($_GET["logout"]) && ($_GET["logout"]=="true")){
      unset($_SESSION["loginMember"]);
      unset($_SESSION["memberLevel"]);
      header("Location: index.php");
  }
  
  if(isset($_GET["action"]) && ($_GET["action"]=="delete")){
      $query_delMember="DELETE FROM memberdata WHERE m_id=?";
      $stmt=$db_link->prepare($query_delMember);
      $stmt->bind_param("i", $_GET["id"]);
      $stmt->execute();
      $stmt->close();
      header("Loaction: member_admin.php");
  }
  
  $query_RecAdmin = "SELECT m_id, m_name, m_logintime FROM memberdata WHERE m_username=?";
  $stmt = $db_link -> prepare($query_RecAdmin);
  $stmt -> bind_param("s", $_SESSION["loginMember"]);
  $stmt->execute();
  $stmt->bind_result($mid, $mname, $mlogintime);
  $stmt->fetch();
  $stmt->close();
  
  $pageRow_records = 10;
  $num_pages = 1;
  if (isset($_GET['page'])){
    $num_pages = $_GET['page'];
  }
  
  $startRow_records = ($num_pages -1) * $pageRow_records;
  $query_RecMember = "SELECT * FROM memberdata WHERE m_level<>'admin' ORDER BY m_jointime DESC";
  $query_limit_RecMember = $query_RecMember." LIMIT {$startRow_records}, {$pageRow_records}";
  $RecMember = $db_link->query($query_limit_RecMember);
  $all_RecMember = $db_link->query($query_RecMember);
  $total_records = $all_RecMember->num_rows;
  $total_pages = ceil($total_records/$pageRow_records);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- <meta http-equiv="refresh" content="1" /> -->
<title>網站會員系統</title>
<link href="style.css" rel="stylesheet" type="text/css">
<style>
    .title {
        width:50%; 
        height:80px; 
        float:left; 
        display:inline;
    }
    a {
        text-decoration:none;
    }
</style>
<script>
function deletesure(){
    if (confirm('\n您確認要刪除這個會員嗎？\n刪除後無法恢復！\n'))
    return true;
    return false;
}
</script>

</html>
<body>
<table width="800" border="0" align="center" cellpadding="4" cellspacing="0">

<tr>
    <td>
        <div class="title">
            <p  style="color:red; font-size:25px; ">會員資料列表</p>
          </div>
        <div class="title">
            <p style="line-height:60px; text-align:right;">管理者：<?php echo $_SESSION["loginMember"] ;?></p>
        </div>
    </td>
</tr>
<tr>
    <td>
    <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#F0F0F0"> 
        <tr>
            <th width="10%" bgcolor="#CCCCCC">&nbsp;</th>
            <th width="20%" bgcolor="#CCCCCC"><p>姓名</p></th>
            <th width="20%" bgcolor="#CCCCCC"><p>帳號</p></th>
            <th width="20%" bgcolor="#CCCCCC"><p>加入時間</p></th>
            <th width="20%" bgcolor="#CCCCCC"><p>上次登入</p></th>
            <th width="10%" bgcolor="#CCCCCC"><p>登入</p></th>
        </tr>

    <?php while($row_RecMember=$RecMember->fetch_assoc()){ ;?>
        <tr>
            <td width="10%" align="center" bgcolor="#FFFFFF">
                <p>
                    <a href="member_adminupdate.php?id=<?php echo $row_RecMember["m_id"];?>">修改</a>
                <br>
                    <a href="?action=delete&id=<?php echo $row_RecMember["m_id"];?>" onClick="return deletesure();">刪除</a>
                </p>
            </td>
            <td width="20%" align="center" bgcolor="#FFFFFF">
                <p><?php echo $row_RecMember["m_name"];?></p>
            </td>
            <td width="20%" align="center" bgcolor="#FFFFFF">
                <p><?php echo $row_RecMember["m_username"];?></p>
            </td>
            <td width="20%" align="center" bgcolor="#FFFFFF">
                <p><?php echo $row_RecMember["m_jointime"];?></p>
            </td>
            <td width="20%" align="center" bgcolor="#FFFFFF">
                <p><?php echo $row_RecMember["m_logintime"];?></p>
            </td>
            <td width="10%" align="center" bgcolor="#FFFFFF">
                <p><?php echo $row_RecMember["m_login"];?></p>
            </td>
        </tr>
    <?php };?>
    </table>
    <p>&nbsp;</p>
    <hr>
    <table width="100%" align="center" cellpadding="3" cellspacing="0">
        <tr>
            <td valign="middle">
                <p>總共筆數:<?php echo $total_records;?> &nbsp;
                    <a href="?logout=true">登出系統</a>
                </p>
            </td>
            <td align="right">
                <?php if ($num_pages > 1) { ;?>
                    <a href="?page=1">第一頁</a> | <a href="?page=<?php echo $num_pages-1;?>">上一頁</a> |
                <?php };?>
                <?php if ($num_pages < $total_pages) { ;?>
                    <a href="?page=<?php echo $num_pages+1;?>">下一頁</a> | <a href="?page=<?php echo $total_pages;?>">最末頁</a> |
                <?php };?>
            </td>
        </tr>
    </table>
    </td>
</tr>

</table>
</body>
</html>
<?php
    $db_link->close();
?>