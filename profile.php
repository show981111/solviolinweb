<?php
/* Displays user information and some useful messages */
session_start();

// Check if user is logged in using the session variable
if ( $_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing your profile page!";
  header("location: error.php");    
}
else {
    // Makes it easier to read
    $name = $_SESSION['userName'];
    $userBranch = $_SESSION['userBranch'];
    $userID = $_SESSION['userID'];
    $userType = $_SESSION['userType'];
    $userDuration = $_SESSION['userDuration'];
    $alert = '';
    
    
}
if($userID != "solvnj" || $userID != "solvnk" || $userID != "solvns" || $userID != "solvny" ||  $userID != "solvng")
{
  if($userBranch == "잠실")
  {
    //$url = "http://show981111.cafe24.com/login-system/jamsil.html";
    $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/jamsil.html";
  }
  if($userBranch == "여의도")
  {
    $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/Yeouido.html";
  }
  if($userBranch == "교대")
  {
    $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/cj.html";
  }
  if($userBranch == "시청")
  {
    $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/sichung.html";
  }
  if($userBranch == "광화문")
  {
    $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/gwang.html";
  }
  if($userBranch == "마곡")
  {
    $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/magok.html";
  }
}
if($userID == "solvnj")
{
  $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/jamsilAdmin.html";
}elseif ($userID == "solvnk") {
  $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/cjAdmin.html";
}elseif ($userID == "solvns") {
  $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/sichungAdmin.html";
}elseif ($userID == "solvny") {
  $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/YeouidoAdmin.html";
}elseif ($userID == "solvng") {
  $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/gwangAdmin.html";
}elseif ($userID == "solvnm") {
  $url = "http://show981111.cafe24.com/fullcalendar-scheduler-1.9.4/login-system/magokAdmin.html";
}
if($userType == '학생')
{

    $con = mysqli_connect("localhost", "show981111", "dyd1@tmdwlsfl", "show981111");
    $result = mysqli_query($con, "SELECT payStatus FROM USER WHERE userID = '$userID';");
    

    while($row = mysqli_fetch_array($result)){

      $payStatus = $row[0];
        
    }


    
    mysqli_close($con);
    if($payStatus == "unpaid")
    {
      $alert = "매월 첫주는 등록기간입니다. 입금/카드결제 모두 가능합니다. 등록하신분들은 이메세지가 뜨지 않습니다. 등록해주세요^^";
    }
}
if($name == 'admin')
{
  $alert = '';
}
$durationMessage = $userDuration."분*4주";

?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Welcome <?= $name ?></title>
  <?php include 'css/css.html'; ?>
</head>

<body style = "background: url(http://show981111.cafe24.com/sol.jpg)">
  <div class="form">

          <h1>Welcome</h1>
           
          
          <h2><?php echo $name; ?></h2>
          <p><?= $userID ?></p>
          <p><?= $userType ?></p>
          <p><?= $userBranch ?></p>
          <p><?= $durationMessage ?></p>
          <p><?= $alert ?></p>
          

          <a href=<?php echo $url; ?> ><button class="button button-block" name="reservation"/>수강신청 </button></a>
          
          <a href="logout.php"><button class="button button-block" name="logout"/>Log Out</button></a>




          


    </div>
    
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="js/index.js"></script>

</body>
</html>
