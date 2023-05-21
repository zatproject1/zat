<?php
// Includes The Init File And The Header
$noNavbar = '';
include 'includes/init.php';
include $tpl . 'header.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$errors = array();
	$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	$isvendor = filter_var($_POST['isvendor'], FILTER_SANITIZE_STRING);

	if (isset($_POST['isvendor'])) {
		if ($isvendor == "yes") {
			$groupid = "1";
		} else {
			$groupid = "0";
		}
	} else {
		$groupid = "0";
	}

	if (empty($username)) {
		array_push($errors, "لا يمكن لاسم المستخدم ان يكون فارغا");
	}
	if (empty($email)) {
		array_push($errors, "لا يمكن للبريد الالكتروني ان يكون فارغا");
	} 
	if (empty($password)) {
		array_push($errors, "لا يمكن لكلمة المرور ان تكون فارغة");
	} 
	if (empty($errors)) {
		$register = new Register($username, $email, $password, $groupid);
		if ($register->UsernameExist()) {
			array_push($errors, "اسم المستخدم غير صالح للاستخدام");
		} 
		if ($register->EmailExist()) {
			array_push($errors, "هذا البريد الالكتروني مسجل لدينا بالفعل");
		}
		if (empty($errors)) {
			$register->createAccount();
			header("Location: login.php");
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
			<input type="text" name="username" placeholder="اسم المستخدم" />
			<input type="text" name="email" placeholder="البريد الالكتروني" />
			<input type="password" name="password" placeholder="كلمة المرور" />
			<div class="vendor-signup">
				<label for="vendorsignup">التسجيل كبائع</label>
				<input id="vendorsignup" value="yes" type="checkbox" name="isvendor" />
			</div>
			<button class="submit-btn" type="submit">انشاء حساب جديد</button>
		</form>
		<p class="link">لديك حساب بالفعل؟ <a href="login.php">تسجيل دخول</a></p>
	</div>
</div>
<?php
// Including The Footer
include $tpl . 'footer.php';