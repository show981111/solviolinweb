<?php
/* Log out process, unsets and destroys session variables */
session_start();
session_unset();
session_destroy(); 
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Error</title>
  <?php include 'css/css.html'; ?>
</head>

<body style = "background: url(http://show981111.cafe24.com/sol.jpg)">
    <div class="form">
          <h1>Violin is Vital!</h1>
              
          <p><?= 'you have been logged out.'; ?></p>
          
          <a href="index.php"><button class="button button-block"/>Home</button></a>

    </div>
</body>
</html>
