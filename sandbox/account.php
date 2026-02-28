<DOCTYPE html>
    <head>
        <title>Account Page</title>
    </head>
    <body>
        <?php
            session_start();
            require_once 'connection.php';

            if(isset($_SESSION['session_status']) && $_SESSION['session_status'] == 1) {
                $uid = $_SESSION['uid'];
                $q_user = $mysqli->query("SELECT username FROM users_tbl1 WHERE uid='$uid'");

                if($q_user && $q_user->num_rows > 0) {
                    $row = $q_user->fetch_assoc();
                    echo "<h1>Welcome, " . htmlspecialchars($row['username']) . "!</h1>";
                } else {
                    echo "User not found.";
                }
            } else {
                echo "You are not logged in. Please <a href='index.php'>login</a>.";
            }
        ?>
         <h1>Account Page</h1>
         <p>This is the account page. Only logged-in users can see this.</p>
</body>
</html>