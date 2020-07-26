<?php
function GetSQLValueString($theValue, $theType) {
	switch ($theType) {
		case "string":
			$theValue = ($theValue != "") ? filter_var($theValue, FILTER_SANITIZE_MAGIC_QUOTES) : "";
		break;
	  	case "int":
			$theValue = ($theValue != "") ? filter_var($theValue, FILTER_SANITIZE_NUMBER_INT) : "";
		break;
	  	case "email":
			$theValue = ($theValue != "") ? filter_var($theValue, FILTER_VALIDATE_EMAIL) : "";
		break;
	 	case "url":
			$theValue = ($theValue != "") ? filter_var($theValue, FILTER_VALIDATE_URL) : "";
		break;      
	}
	return $theValue;
}

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

$redirectUrl="member_center.php";

if(isset($_POST["action"])&&($_POST["action"]=="update")){
	$query_update="UPDATE memberdata SET m_passwd=?, m_name=?, m_sex=?, m_birthday=?, m_email=?, m_url=?, m_phone=?, m_address=? WHERE m_id=?";
	$stmt= $db_link->prepare($query_update);
	//檢查是否修改密碼
	$mpass=$_POST["m_passwdo"];
	if(($_POST["m_passwd"]!="")&&($_POST["m_passwd"]==$_POST["m_passwdrecheck"])){
		$mpass=password_hash($_POST["m_passwd"], PASSWORD_DEFAULT);
	}
	$stmt->bind_param("ssssssssi",
		$mpass,
		GetSQLValueString($_POST["m_name"], 'string'),
		GetSQLValueString($_POST["m_sex"], 'string'),
		GetSQLValueString($_POST["m_birthday"], 'string'),
		GetSQLValueString($_POST["m_email"], 'email'),
		GetSQLValueString($_POST["m_url"], 'url'),
		GetSQLValueString($_POST["m_phone"], 'string'),
		GetSQLValueString($_POST["m_address"], 'string'),
		GetSQLValueString($_POST["m_id"], 'int'));
	$stmt->execute();
	$stmt->close();
	if(($_POST["m_passwd"]!="")&&($_POST["m_passwd"]==$_POST["m_passwdrecheck"])){
		unset($_SESSION["loginMember"]);
	 	unset($_SESSION["memberLevel"]);
	 	$redirectUrl="index.php";
	}
	header("Location: $redirectUrl");
}

$query_RecMember="SELECT * FROM memberdata WHERE m_username='{$_SESSION["loginMember"]}'";
$RecMember=$db_link->query($query_RecMember);
$row_RecMember=$RecMember->fetch_assoc();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>會員資料</title>
<style type="text/css">
    .title { font-family: "微軟正黑體"; }
    .heading {font-family: "微軟正黑體"; }
</style>
<script language="javascript">
function checkForm(){
	if(document.formJoin.m_passwd.value!="" || document.formJoin.m_passwdrecheck.value!=""){
		if(!check_passwd(document.formJoin.m_passwd.value,document.formJoin.m_passwdrecheck.value)){
			document.formJoin.m_passwd.focus();
			return false;
		}
	}
	if(document.formJoin.m_name.value==""){
		alert("請填寫姓名!");
		document.formJoin.m_name.focus();
		return false;
	}
	if(document.formJoin.m_birthday.value==""){
		alert("請填寫生日!");
		document.formJoin.m_birthday.focus();
		return false;
	}
	if(document.formJoin.m_email.value==""){
		alert("請填寫電子郵件!");
		document.formJoin.m_email.focus();
		return false;
	}
	if(!checkmail(document.formJoin.m_email)){
		document.formJoin.m_email.focus();
		return false;
	}
	return confirm('確認送出嗎？');
	
}
function check_passwd(pw1,pw2){
	if(pw1==''){
		alert("密碼不可以空白!");
		return false;}
	for(var idx=0;idx<pw1.length;idx++){
		if(pw1.charAt(idx) == ' ' || pw1.charAt(idx) == '\"'){
			alert("密碼不可以含有空白或雙引號 !\n");
			return false;}
		if(pw1.length<5 || pw1.length>10){
			alert( "密碼長度只能5到10個字母 !\n" );
			return false;}
		if(pw1!= pw2){
			alert("密碼二次輸入不一樣,請重新輸入 !\n");
			return false;}
	}
	return true;
}
function checkmail(myEmail) {
	var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(filter.test(myEmail.value)){
		return true;}
	alert("電子郵件格式不正確");
	return false;
}
</script>
</head>

<body>

<?php if(isset($_GET["loginStats"]) && ($_GET["loginStats"]=="1")){?>
  <script language="javascript">
    alert('會員資料修改成功\n請用新密碼登入。');
    window.location.href='index.php';		  
  </script>
<?php }?>

<table width="780" border="0" align="center" cellpadding="4" cellspacing="0">

  <tr>

    <td>

      <table width="100%" border="0" cellspacing="0" cellpadding="10">

      <tr valign="top">
        <td><form action="" method="POST" name="formJoin" id="formJoin" onSubmit="return checkForm();">
          <p size="10" style="color:red; font-size:25px;">會員資料修改</p>
          <div>
            <hr size="1" />
            <p><strong>帳號資料</strong>：<?php echo $row_RecMember["m_username"];?></p>

            <p><strong>新密碼</strong>：
            <input name="m_passwd" type="password"  id="m_passwd">
			<input name="m_passwdo" type="hidden"  id="m_passwdo" value="<?php echo $row_RecMember["m_passwd"];?>">
            <font color="#FF0000">*</font><br><span>請填入5~10個字元以內的英文字母、數字、以及各種符號組合，</span></p>

            <p><strong>確認密碼</strong>：
            <input name="m_passwdrecheck" type="password"  id="m_passwdrecheck">
            <font color="#FF0000">*</font> <br><span>再輸入一次密碼</span></p>
            <hr size="1" />

            <p style="color:red; font-size:25px;">個人資料</p>
            <p><strong>真實姓名</strong>：
            <input name="m_name" type="text"  id="m_name" value="<?php echo $row_RecMember["m_name"];?>">
            <font color="#FF0000">*</font></p>

            <p><strong>性　　別</strong>：
            <input name="m_sex" type="radio" value="女" 
			<?php if ($row_RecMember["m_sex"]=="女"){echo "checked";}?>>女
            <input name="m_sex" type="radio" value="男"
			<?php if ($row_RecMember["m_sex"]=="男"){echo "checked";}?>>男
            <font color="#FF0000">*</font></p>

            <p><strong>生　　日</strong>：
            <input name="m_birthday" type="text"  id="m_birthday" value="<?php echo $row_RecMember["m_birthday"];?>">
            <font color="#FF0000">*</font> <br>
            <span>為西元格式(YYYY-MM-DD)。</span></p>

            <p><strong>電子郵件</strong>：
            <input name="m_email" type="text"  id="m_email" value="<?php echo $row_RecMember["m_email"];?>">
            <font color="#FF0000">*</font><br><span>請確定此電子郵件為可使用狀態，以方便未來系統使用，如補寄會員密碼信。</span></p>

            <p><strong>個人網頁</strong>：
            <input name="m_url" type="text"  id="m_url" value="<?php echo $row_RecMember["m_url"];?>">
            <br><span>請以「http://」 為開頭。</span></p>

            <p><strong>電　　話</strong>：
            <input name="m_phone" type="text"  id="m_phone" value="<?php echo $row_RecMember["m_phone"];?>"></p>

            <p><strong>住　　址</strong>：
            <input name="m_address" type="text"  id="m_address" size="40" value="<?php echo $row_RecMember["m_address"];?>"></p>
            <p> <font color="#FF0000">*</font> 表示為必填的欄位</p>
          </div>
          <hr size="1" />
          <p align="center">
		 	<input name="m_id" type="hidden" id="m_id" value="<?php echo $row_RecMember["m_id"];?>">
            <input name="action" type="hidden" id="action" value="update">
            <input type="submit" name="Submit2" value="修改資料">
            <input type="reset" name="Submit3" value="重設資料">
            <input type="button" name="Submit" value="回上一頁" onClick="window.history.back();">
          </p>
        </form>
		</td>

      </tr>

      </table>

    </td>
  </tr>
</table>
</body>
</html>
<?php $db_link->close();?>