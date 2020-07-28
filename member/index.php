<?php
require_once("conndb.php");
session_start();
if (isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){
  if($_SESSION["memberLevel"]=="member"){
    header("Location: member_center.php");
    }else{
    header("Location: member_admin.php"); 
   }
}
if(isset($_POST["username"]) && isset($_POST["passwd"])){
  $query_RecLogin = "SELECT m_username, m_passwd, m_level FROM memberdata WHERE m_username=?";
  $stmt=$db_link->prepare($query_RecLogin);
  $stmt->bind_param("s", $_POST["username"]);
  $stmt->execute();
  $stmt->bind_result($username, $passwd, $level);
  $stmt->fetch();
  $stmt->close();
  if(password_verify($_POST["passwd"],$passwd)){
    $query_RecLoginUpdate = "UPDATE memberdata SET m_login=m_login+1, m_logintime=NOW() WHERE m_username=?";
    $stmt=$db_link->prepare($query_RecLoginUpdate);
    $stmt->bind_param("s", $username);//錯誤
    $stmt->execute();
    $stmt->close();
    $_SESSION["loginMember"]=$username;
    $_SESSION["memberLevel"]=$level;
    if(isset($_POST["rememberme"])&&($_POST["rememberme"]=="true")){
      setcookie("remUser", $_POST["username"], time()+365*24*60);
      setcookie("remPass", $_POST["passwd"], time()+365*24*60);
    }else{  
        if (isset($COOKIE["remUser"])) {
          setcookie("remUser", $_POST["username"], time()-100);
          setcookie("remPass", $_POST["passwd"], time()-100);
        }
    }
    if($_SESSION["memberLevel"]=="member"){
      header("Location:member_center.php");
    }else{
      header("Location:member_admin.php");
    }
  }else{
    header("Location: index.php?errMsg=1");
  }
}

?>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>會員系統</title>
    <link href="/bootstrap-4.5.0-dist/css/bootstrap.css" rel="stylesheet">
    <link href="/member/signin.css" rel="stylesheet" type="text/css" charset="utf-8">
  </head>

  <body class="text-center">

  <form  class="form-signin" name="form1" method="post" action="">

      <h1 class="h3 mb-3 font-weight-normal">歡迎來到我的網頁</h1>

        <input name="username" type="text" class="form-control" id="username" placeholder="請輸入帳號" value="<?php if(isset($_COOKIE["remUser"]) && ($_COOKIE["remUser"]!="")) echo $_COOKIE["remUser"];?>" required autofocus>
        
        <input name="passwd" type="password" class="form-control" id="passwd" placeholder="Password" value="<?php if(isset($_COOKIE["remPass"]) && ($_COOKIE["remPass"]!="")) echo $_COOKIE["remPass"];?>" required>
      
      <div class="checkbox mb-3">
        
        <label>

          <input type="checkbox" value="remember-me"> Remember me

        </label>

      </div>     
      
      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      
      <p style="margin-top:10px;">
      <div style="float:left;"><a href="../index.html">回首頁</a></div>
      <div style="float:right;"><a href="member_join.php">申請會員</a></div>
      </p>
      <?php if(isset($_GET["errMsg"]) && ($_GET["errMsg"]=="1")){?>
        <br><div style="color:#D68B00;padding:10px; text-align: center;"> 
            登入帳號或密碼錯誤！
          </div>
      <?php }?>
      
      <!-- <p class="mt-5 mb-3 text-muted">&copy; 2020-2020</p> -->
    </form>
  </body>
</html>
<?php
  $db_link->close();
?>