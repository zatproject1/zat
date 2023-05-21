<?php
// Includes The Init File And The Header
$noNavbar = '';
include 'includes/init.php';
include $tpl . 'header.php';

if (isset($_SESSION['username'])) {
	header("location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$errors = array();
	$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

	if (empty($username)) {
		array_push($errors, "لا يمكن لاسم المستخدم ان يكون فارغا");
	}
	if (empty($password)) {
		array_push($errors, "لا يمكن لكلمة المرور ان تكون فارغة");
	}
	if (empty($errors)) {
		$login = new Login($username, $password);

		if ($login->UserExist()) {
			if ($login->Authenticate()) {
				$_SESSION['username'] = $username;
				header("Location: index.php");
			} else {
				array_push($errors, "كلمة المرور غير صحيحة");
			}
		} else {
			array_push($errors, "اسم المستخدم او البريد الالكتروني غير مسجلين لدينا");
		}
	}
}
?>
<div id="login">
	<div class="strip"></div>
	<div class="login-content">
		<div class="logo">
			<img class="icon" src="<?php echo $img . 'icon.png'; ?>" />
			<div class="text">
				<h2>سجل دخولك</h2>
				<p>لتكمل معنا رحلة تسوقك</p>
			</div>
		</div>
		<div class="errors">
			<?php
				if ($_SERVER['REQUEST_METHOD'] == "POST") {
					foreach ($errors as $err) {
						echo '<div class="error">';
						echo $err;
						echo '</div>';
					}
				}
			?>
		</div>
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">
			<input type="text" name="username" placeholder="اسم المستخدم او البريد الالكتروني" />
			<input type="password" name="password" placeholder="كلمة المرور" />
			<button class="submit-btn" type="submit">تسجيل دخول</button>
		</form>
		<p class="link"> ليس لديك حساب؟ <a href="register.php">انشاء حساب</a></p>
	</div>
</div>
<?php
// Including The Footer
include $tpl . 'footer.php';