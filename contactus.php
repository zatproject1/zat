<?php
/* PHP Mailer Required Files */
require("PHPMailer-master/src/PHPMailer.php");
require("PHPMailer-master/src/SMTP.php");
require("PHPMailer-master/src/Exception.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/* Required Files */
include 'includes/init.php';
include $tpl . 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	/* User Data */
	$message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
	$username = $_SESSION['username'];
	$userinfo = new UserInfo();
	$row = $userinfo->GetInfo($username);
	$Useremail = $row['Email'];

	/* Send Message If It is Not Empty */
	if (!empty($message)) {
		try {
			$mail = new PHPMailer(true);
			$mail->SMTPDebug = 0;
			$mail->isSMTP();
			$mail->Host       = $host;
			$mail->SMTPAuth   = true;
			$mail->Username   = $email;
			$mail->Password   = $password;
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port       = $port;
			$mail->CharSet = 'UTF-8';
	
			$mail->addAddress($admin_email); 
			$mail->setFrom($email, $websiteName);
			$mail->addReplyTo($reply, $websiteName);
	
			$mail->isHTML(true);
			$mail->Subject = 'New Message From ' . $websiteName;
			$mail->Body = '<div style="text-align: right">';
			$mail->Body .= $username . ' اسم المستخدم';
			$mail->Body .= '<div style="display:block"></div>';
			$mail->Body .= $Useremail . ' البريد الالكتروني';
			$mail->Body .= '<div style="display:block"></div>';
			$mail->Body .= $message;
			$mail->Body .= '</div>';
			$mail->AltBody = 'Please Enable HTML Email Client Service';
	
			$mail->send();
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	} else {
		echo '<div class="error">لا يمكنك ان ترسل رسالة فارغة</div>';
	}
}
?>
<div id="login" class="contact-us">
	<div class="strip"></div>
	<div class="login-content">
		<div class="logo">
			<div class="contactus-icon"><i class="fal fa-paper-plane"></i></div>
		</div>
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">
			<textarea class="contactus-textarea" name="message" placeholder="نحن في انتظار رسالتك"></textarea>
			<button class="submit-btn" type="submit">ارسال</button>
		</form>
	</div>
</div>
<?php
include $tpl . 'footer.php';
?>