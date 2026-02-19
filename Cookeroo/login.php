<?php
session_start();
require_once("db.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); //encodes
    return $data;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $errors = array();
    $dataOK = TRUE;
    
    $email = test_input($_POST["email"]);
    $emailRegex = "/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/";
    if (!preg_match($emailRegex, $email)) {
        $errors["email"] = "Invalid Email";
        $dataOK = FALSE;
    }

    $password = test_input($_POST["password"]);
    $passwordRegex = "/^.{8}$/";
    if (!preg_match($passwordRegex, $password)) {
        $errors["password"] = "Invalid Password";
        $dataOK = FALSE;
    }

    if ($dataOK) {

        try {
          $conn = new PDO("mysql:host=localhost; dbname=ejo252", "ejo252", "Cannamon101@");
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }

        $query = "SELECT user_id,first_name,last_name,avatar_url FROM Users WHERE email = '$email' AND password ='$password'";
        $result = $conn->query($query);

        if (!$result) {
            $errors["Database Error"] = "Could not retrieve user information";
        } elseif ($row = $result->fetch()) {
            $_SESSION["user_id"] =$row["user_id"];
            $_SESSION["first_name"] =$row["first_name"];
            $_SESSION["last_name"] =$row["last_name"];
            $_SESSION["avatar_url"] =$row["avatar_url"];

            $conn = null;
            header("Location: recipelist.php");
            exit();
        } else {
            // login unsuccessful
            $errors["Login Failed"] = "That email/password combination does not exist.";
        }

        $db = null;

    } else {

        $errors['Login Failed'] = "You entered an invalid email or password while logging in.";
    }
    if(!empty($errors)){
        foreach($errors as $type => $message) {
            echo "$type: $message <br />\n";
        }
    }

}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Cookeroo Login</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script src="js/eventHandlers.js"></script>
</head>

<body>
    <div id="header">
        <header class="recipe-list">
            <h1>COOKEROO RECIPIES</h1>
         </header>
    </div>
    <div id="container">
        <header id="header-auth">
            <h1>Cookeroo Login</h1>
        </header>
        <main id="main-center">
            <form class="auth-form" action="login.php" method="post" id="login">
                <div class="form-input-grid">
                    <label for="email">Email:</label>
                    <input type="text"  name="email" id="email" autocomplete="email"/> 
                    &nbsp;
                    <div id="error-text-email" class="error-text hidden">
                        Email is invalid.
                    </div> 

                    <label for="password">Password:</label>
                    <input type="password"  name="password" id="password" autocomplete="current-password"/> 
                    &nbsp;
                    <div id="error-text-password" class="error-text hidden">
                        Password is invalid.
                    </div> 
                </div>

                <div class="align-right">
                    <button type="submit" class="submit">Login</button>
                </div>
            </form>
            <div class="form-note">
                <p>Don't have an account? <a href="signup.php">Signup</a></p>
            </div>
        </main>
    </div>
</body>

</html>