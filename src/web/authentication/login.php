<?php
namespace tryout_google_auth\web\authentication;

require_once (__DIR__ . '/LoginHandler.php');

if(!session_id()){
    session_start();
}

$returnUrl = $_SESSION[LoginHandler::REDIRECT_URL_KEY];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1" />
		<title>Login</title>	
	</head>
	
  <body>
	    <script src="https://accounts.google.com/gsi/client" async defer></script>
	    
		<div id="g_id_onload" 
			data-client_id="884346004784-2b1v345e8fcgubppetoand01o2frnvqu.apps.googleusercontent.com" 
			data-login_uri="<?= $returnUrl ?>"
			data-auto_prompt="false">
		</div>
		      	
		<div class="g_id_signin"
		   data-type="standard"
		   data-size="large"
		   data-theme="outline"
		   data-text="sign_in_with"
		   data-shape="rectangular"
		   data-logo_alignment="left">
		</div>
		
		
  </body>
</html>
