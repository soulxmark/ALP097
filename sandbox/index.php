<?php 
    session_start(); 
    require_once 'connection.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Test</title>
</head>
<body>
    <form method="post" action="index.php">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"><br><br>    
        <label for="password">Password:</label>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" name="login" value="Login"> 
    </form>

    <?php
        if(isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if(empty($username) || empty($password)) {
                echo "Please fill in all fields.";
            } else {
            
                $q_login = $mysqli->query("SELECT uid, username, password_us FROM users_tbl1 WHERE username='$username' AND password_us='$password'");
                
                if(!$q_login) {
                    echo "Query Error: " . $mysqli->error;
                } else {
                    if($q_login->num_rows == 0) {
                        echo "Invalid username or password.";
                    } else {
                        $row = $q_login->fetch_assoc();
                        
                       
                        if($row['username'] == $username && $row['password_us'] == $password) {
                            $_SESSION['uid'] = $row['uid'];
                            $_SESSION['session_status'] = 1;
                            echo '<script type="text/javascript">window.location.href = "account.php";</script>';
                        } else {
                            echo "Invalid username or password.";
                        }
                    }
                }
            }
        }
    ?>
</body>    
</html>