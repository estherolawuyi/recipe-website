<?php
require_once("db.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$errors = array();
$fname = "";
$lname = "";
$email = "";
$password = "";
$dob = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fname = test_input($_POST["fname"]);
    $lname = test_input($_POST["lname"]);
    $email = test_input($_POST["email"]);
    $password = test_input($_POST["password"]);
    $dob = test_input($_POST["dob"]);;
    
    $nameRegex = "/^[a-zA-Z]+$/";
    $emailRegex = "/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/";
    $passwordRegex = "/^.{8}$/";
    $dobRegex = "/^\d{4}[-]\d{2}[-]\d{2}$/";
    
    if (!preg_match($nameRegex, $fname)) {
        $errors["fname"] = "Invalid First Name";
    }
    if (!preg_match($nameRegex, $lname)) {
        $errors["lname"] = "Invalid Last Name";
    }
    if (!preg_match($emailRegex, $email)) {
        $errors["email"] = "Invalid Email Address";
    }
    if (!preg_match($passwordRegex, $password)) {
        $errors["password"] = "Invalid Password";
    }
    if (!preg_match($dobRegex, $dob)) {
        $errors["dob"] = "Invalid DOB";
    }

    $target_file = "";
    try {
        $conn = new PDO("mysql:host=localhost; dbname=ejo252", "ejo252", "Cannamon101@");
    } catch (PDOException $e) {
          throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

    $query = "SELECT email FROM Users WHERE email= '$email'";

    $result = $conn->query($query);

    $match = $result->fetch();

    if ($match) {
        $errors["Account Taken"] = "A user with that email already exists.";
    }
    
    if (empty($errors)) {

        $query = "INSERT INTO Users(first_name, last_name, email, password, dob, avatar_url) VALUES ('$fname', '$lname','$email','$password','$dob','avatar_stub')";
        $result = $conn->exec($query);
        if (!$result) {
            $errors["Database Error:"] = "Failed to insert user";
        } else {
            $target_dir = "uploads/";
            $uploadOk = TRUE;
        
            $imageFileType = strtolower(pathinfo($_FILES["profilephoto"]["name"],PATHINFO_EXTENSION));

            $uid = $conn->lastInsertId();
            $target_dir = "uploads/";
            $uploadOk = TRUE;
            $target_file = $target_dir.$uid.".".$imageFileType; 

           if (!isset($_FILES['profilephoto']) || $_FILES['profilephoto']['error'] === UPLOAD_ERR_NO_FILE) {
                $errors['profilephoto'] = "Profile photo is required.";
            } else {
                $imageFileType = strtolower(pathinfo($_FILES["profilephoto"]["name"], PATHINFO_EXTENSION));
                $target_file = $target_dir . $uid . "." . $imageFileType;

            if (file_exists($target_file)) {
                $errors["profilephoto"] = "Sorry, file already exists.";
                $uploadOk = FALSE;
            }

            if ($_FILES["profilephoto"]["size"] > 1000000) {
                $errors["profilephoto"] = "File is too large. Maximum 1MB.";
                $uploadOk = FALSE;
            }

            if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                $errors["profilephoto"] = "Bad image type. Only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = FALSE;
            }
        }
            
            if (!$uploadOk)
            {
                $query = "DELETE FROM Users WHERE email ='$email'";
                $result = $conn->exec($query);
                if (!$result) {
                    $errors["Database Error"] = "could not delete user when avatar upload failed";
                }
            } else {
                $query = "UPDATE Users SET avatar_url='$target_file' WHERE user_id ='$uid'";
                $result = $conn->exec($query);
                if (!$result) {
                    $errors["Database Error:"] = "could not update avatar_url";
                } else {
                    $conn = null;
                    header("Location: login.php");
                    exit();
                }
            } 
        } 
    } 

    if (!empty($errors)) {
        foreach($errors as $type => $message) {
            print("$type: $message \n<br />");
        }
    }

} 
?>

<!DOCTYPE html>
<html>

<head>
    <title>Cookeroo Signup</title>
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
            <h1>Cookeroo Signup</h1>
        </header>
        <main id="main-center">
        <form class="auth-form" action="signup.php" method="post" id="signup">

                <div class="form-input-grid">

                    <label for="fname">First Name:</label>
                    <input type="text"  name="fname" id="fname" value="<?=$fname?>" <?= isset($errors['fname'])?'class=\'highlight\'':'' ?>/>   
                    &nbsp;  
                    <div id="error-text-fname" class="error-text <?= isset($errors['fname'])?'':'hidden' ?>">
                        First name is invalid.
                    </div>

                    <label for="lname">Last Name:</label> 
                    <input type="text"  name="lname" id="lname" value="<?=$lname?>" <?= isset($errors['lname'])?'class=\'highlight\'':'' ?>/> 
                    &nbsp;  
                    <div id="error-text-lname" class="error-text <?= isset($errors['lname'])?'':'hidden' ?>">
                        Last name is invalid.
                    </div>
                    
                    <label for="email">Email:</label>
                    <input type="text"  name="email" id="email" value="<?=$email?>" <?= isset($errors['email'])?'class=\'highlight\'':'' ?>/> 
                    &nbsp;
                    <div id="error-text-email" class="error-text <?= isset($errors['email'])?'':'hidden' ?>">
                        Email is invalid.
                    </div>

                    <label for="password">Password:</label> 
                    <input type="password"  name="password" id="password" value="<?=$password?>" <?= isset($errors['password'])?'class=\'highlight\'':'' ?>/> 
                    &nbsp;  
                    <div id="error-text-password" class="error-text <?= isset($errors['password'])?'':'hidden' ?>">
                        Password is invalid.
                    </div>
                     
                    <label for="cpassword">Confirm Password:</label>  
                    <input type="password"  name="cpassword" id="cpassword" autocomplete="cpassword" <?= isset($errors['cpassword'])?'class=\'highlight\'':'' ?>/> 
                    &nbsp;
                    <div id="error-text-cpassword" class="error-text <?= isset($errors['cpassword'])?'':'hidden' ?>">
                        Passwords do not match.
                    </div>
                    
                    <label for="dob">Date of Birth:</label> 
                    <input type="date"  name="dob" id="dob" value="<?=$dob?>" <?= isset($errors['dob'])?'class=\'highlight\'':'' ?>/> 
                    &nbsp;
                    <div id="error-text-dob" class="error-text <?= isset($errors['dob'])?'':'hidden' ?>">
                        Date of birth is invalid.
                    </div>
                    
                    <label for="profilephoto">Profile Photo:</label> 
                    <input type="file"  name="profilephoto" id="profilephoto" <?= isset($errors['profilephoto'])?'class=\'highlight\'':'' ?>/> 
                    &nbsp;
                    <div id="error-text-profilephoto" class="error-text <?= isset($errors['profilephoto'])?'':'hidden' ?>">
                        Profile photo is invalid.
                    </div>
                </div>

                <div class="align-right">
                    <button type="submit" class="submit">Create Account</button>
                </div>
            </form>
            
            <div class="form-note">
                <p>Already have an account? <a href="login.php">Login</a></p>

            </div>
        </main>
    </div>
</body>

</html>