<?php

// Set session variables to be used on profile.php page
    $_SESSION['userID'] = $_POST['userName'];
    $_SESSION['userName'] = $_POST['userName'];
    $_SESSION['userBranch'] = $_POST['userBranch'];
    $_SESSION['userType'] = $_POST['userType'];
    $_SESSION['userDuration'] = $_POST['userDuration'];
    $_SESSION['userPhone'] = $_POST['userPhone'];


    $userName = $mysqli->escape_string($_POST['userName']);
    $userBranch = $mysqli->escape_string($_POST['userBranch']);
    $userID = $mysqli->escape_string($_POST['userName']);
    $userPassword = $mysqli->escape_string($_POST['userPhone']);
    $userType = $mysqli->escape_string($_POST['userType']);
    $userDuration = $_POST['userDuration'];
    $registercode = $mysqli->escape_string($_POST['registercode']);
    $userPhone = $mysqli->escape_string($_POST['userPhone']);
    $userCredit = 2;
    $userPassword = substr($userPassword, 9); 


    $result = $mysqli->query("SELECT * FROM USER WHERE userID='$userID'") or die($mysqli->error());

    if ( $result->num_rows > 0 ) {
            
        $_SESSION['message'] = 'User with this email already exists!';
        header("location: error.php");
            
    }elseif($_POST['userBranch'] != '잠실' && $_POST['userBranch'] != '여의도' && $_POST['userBranch'] != '시청' && $_POST['userBranch'] != '교대' && $_POST['userBranch'] != '광화문'&& $_POST['userBranch'] != '마곡')
    {
        $_SESSION['message'] = "지점을 다시한번 확인해 주세요!";
        header("location: error.php");
        
    }elseif ($_POST['userDuration'] != '30' && $_POST['userDuration'] != '60' && $_POST['userDuration'] != '45') {
        $_SESSION['message'] = "수업시간을 다시한번 확인해 주세요!";
        header("location: error.php");
        
    }elseif ($_POST['userType'] != '강사' && $_POST['userType'] != '학생') {
        $_SESSION['message'] = "강사인지 학생인지 다시한번 확인해 주세요!";
        header("location: error.php");
        
    }elseif ($_POST['registercode'] != 'cjsol0731') {
        $_SESSION['message'] = "가입코드가 틀렸습니다.";
        header("location: error.php");
    }elseif ( strlen($_POST['userPhone']) != 13) {
        $_SESSION['message'] = "전화번호를 정확히 입력해주세요.";
        header("location: error.php");
        
    }
    else { 

          
        $sql = "INSERT INTO USER (userID, userName, userPassword, userBranch, userType, userDuration, userCredit, userPhone) " 
                    . "VALUES ('$userID','$userName','$userPassword','$userBranch', '$userType', '$userDuration', '$userCredit', '$userPhone')";

            // Add user to the database
        if ( $mysqli->query($sql) ){

        }

        else {
            $_SESSION['message'] = 'Registration failed!';
            header("location: error.php");
        }

    }

?>

