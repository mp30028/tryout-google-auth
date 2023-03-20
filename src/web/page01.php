<?php
use tryout_google_auth\web\authentication\LoginHandler;

require_once (__DIR__ . '/authentication/LoginHandler.php');

$handler = new LoginHandler();
$username = NULL;
$message = NULL;
if ($handler->authenticate()){
    $username = $handler->getUsername();
    $message = "You have been successfully authenticated and identified as user {$username}";        
}else{
    $message = "Unfortunately, you could NOT be authenticated. ";
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1" />
		<title>Page-1</title>	
	</head>
	
  <body>
		<div>
			<h3>Welcome to Page-1</h3>
			<p><?= $message ?></p>			
		</div>
		<div><a href='?logout'>Logout</a></div>
  </body>
</html>
