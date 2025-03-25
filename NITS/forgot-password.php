<?php
// Initialize the session
session_start();
 
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$phone = "";
$phone_err =  "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if phone is empty
    if(empty(trim($_POST["phone"]))){
        $phone_err = "Please enter phone.";
    } else{
        $phone = trim($_POST["phone"]);
    }
    
    // Validate credentials
    if(empty($phone_err) ){
        // Prepare a select statement
        $sql = "SELECT id, phone, email FROM users WHERE phone = :phone";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":phone", $param_phone, PDO::PARAM_STR);
            
            // Set parameters
            $param_phone = trim($_POST["phone"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if phone exists, if yes then start session
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $phone = $row["phone"];
                        $email = $row["email"];
                            // Pin is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                    }
                } else{
                    // phone doesn't exist, display a generic error message
                    $login_err = "Invalid phone.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="style.css" rel="stylesheet" type="text/css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
	.container {
		width: 302px;
		height: 175px;
		position: absolute;
		left: 0px;
		right: 0px;
		top: 0px;
		bottom: 0px;
		margin: auto;
	}
	#phone, #verificationcode {
		width: calc(100% - 24px);
		padding: 10px;
		font-size: 20px;
		margin-bottom: 5px;
		outline: none;
	}
	#recaptcha-container {
		margin-bottom: 5px;
	}
	#send, #verify {
		width: 100%;
		height: 40px;
		outline: none;
	}
	.p-conf, .n-conf {
		width: calc(100% - 22px);
		border: 2px solid green;
		border-radius: 4px;
		padding: 8px 10px;
		margin: 4px 0px;
		background-color: rgba(0, 249, 12, 0.5);
		display: none;
	}
	.n-conf {
		border-color: red;
		background-color: rgba(255, 0, 4, 0.5);
	}
    </style>
</head>
<body>
<section class="vh-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
    <div class="col-lg-12 col-xl-11">
    <div class="card text-black" style="border-radius: 25px;">
    <div class="card-body p-md-5">
    <div class="row justify-content-center">
    <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

    <div class="wrapper">
        <h2>Forgot Password</h2>
        <p>Please Enter your Mobile Number to reset.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
            <div id="sender">
			<input type="text" name="phone" id="phone" placeholder="+91...">
			<div id="recaptcha-container"></div>
			<input type="button" id="send" value="Send" onClick="phoneAuth()">
		</div>
            </div>    
            <div class="form-group">
            <div id="verifier" style="display: none">
			<input type="text" id="verificationcode" placeholder="OTP Code">
			<input type="button" id="verify"  value="Verify" onClick="codeverify()">
			<div class="p-conf">Number Verified</div>
			<div class="n-conf">OTP ERROR</div>
		</div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </section>
    <!--	add firebase SDK-->
<script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
<script>
	// For Firebase JS SDK v7.20.0 and later, measurementId is optional

// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyCIpuEW5hxjsfk34UHKCqQKR-irH_WcbJ8",
  authDomain: "nitcse-80de6.firebaseapp.com",
  projectId: "nitcse-80de6",
  storageBucket: "nitcse-80de6.appspot.com",
  messagingSenderId: "328019176571",
  appId: "1:328019176571:web:0d94f91fa2d114333406de",
  measurementId: "G-TL24Z08129"
};
	firebase.initializeApp(firebaseConfig);
render();
function render(){
	window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container');
	recaptchaVerifier.render();
}
	// function for send message
function phoneAuth(){
	var phone = document.getElementById('phone').value;
	firebase.auth().signInWithPhoneNumber(phone, window.recaptchaVerifier).then(function(confirmationResult){
		window.confirmationResult = confirmationResult;
		coderesult = confirmationResult;
		document.getElementById('sender').style.display = 'none';
		document.getElementById('verifier').style.display = 'block';
	}).catch(function(error){
		alert(error.message);
	});
}
	// function for code verify
function codeverify(){
	var code = document.getElementById('verificationcode').value;
	coderesult.confirm(code).then(function(){
        window.location.href = "reset-password.php";
		document.getElementsByClassName('p-conf')[0].style.display = 'block';
		document.getElementsByClassName('n-conf')[0].style.display = 'none';
	}).catch(function(){
		document.getElementsByClassName('p-conf')[0].style.display = 'none';
		document.getElementsByClassName('n-conf')[0].style.display = 'block';
	})
}
</script>

</body>
</html>