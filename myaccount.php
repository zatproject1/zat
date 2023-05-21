<?php
// Includes The Init File And The Header
include 'includes/init.php';
include $tpl . 'header.php';

if (!isset($_SESSION['username'])) {
	header("location: index.php");
}

$page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_SANITIZE_STRING) : 'home';
?>
<div class="account">
	<?php include $tpl . "accountsidebar.php"; ?>
	<div class="account-content">
		<?php if ($page == "home") {
			$userinfo = new UserInfo();
			$row = $userinfo->GetInfo($_SESSION['username']);
			$seller = new Seller();
			if (!$seller->CheckCompleted($row['ID']) && $row['GroupID'] == '1') {
				echo '<div class="success">You Have To Complete Some More Steps To Start Selling Products <a style="font-size: 16px;" href="?page=complete">Click Here</a></div>';	
			}
		?>
		<div class="centered-content">
			<div class="user-info">
				<img class="avatar" src="<?php echo $img . 'pepper.jpg' ?>">
				<h2 class="username"><?php echo $row['Username'] ?></h2>
				<p class="email"><?php echo $row['Email'] ?></p>
				<a class="edit" href="?page=editaccount">تعديل</a>
				<a class="delete" href="?page=removeaccount">الغاء</a>
			</div>
		</div>
		<?php } elseif ($page == "products") { ?>
		<div class="padding"></div>
		<div class="adding-actions">
		<?php
			$userInfo = new UserInfo();
			$getuser = $userInfo->GetInfo($_SESSION['username']);
			$seller = new Seller();

			if ($seller->CheckCompleted($getuser['ID'])) { ?>
				<button onclick="window.open('?page=add', '_self')" class="addproduct">اضافة</button>
		<?php
			} else { ?>
				<button onclick="window.open('?page=complete', '_self')" class="addproduct">اكمال الخطوات</button>
		<?php }
		?>
		</div>
		<table class="styled-table">
			<thead>
				<tr>
					<th scope="col">المهام</th>
					<th scope="col">السعر</th>
					<th scope="col">اسم المنتج</th>
					<th scope="col">#</th>
				</tr>
			</thead>
			<tbody class="active-row">
				<?php
					$getproducts = new Product();
					$rows = $getproducts->UserProducts($_SESSION['username']);
					if (!empty($rows)) {
						foreach ($rows as $row) {
				?>
				<tr>
					<th class="actions"><button onclick="window.open('?page=edit&id=<?php echo $row['ID'] ?>', '_self')" class="editproduct">تعديل</button><button onclick="window.open('?page=delete&id=<?php echo $row['ID'] ?>', '_self')" class="delete">حذف</button></th>
					<td>$<?php echo $row['ProductSmallPrice'] ?>, $<?php echo $row['ProductPrice'] ?>, $<?php echo $row['ProductLargePrice'] ?></td>
					<td><?php echo $row['ProductName'] ?></td>
					<td><?php echo $row['ID'] ?></td>
				</tr>
				<?php
						}
					}
				?>
			</tbody>
		</table>
		<?php } elseif ($page == "add") {
				$userInfo = new UserInfo();
				$getuser = $userInfo->GetInfo($_SESSION['username']);
				$seller = new Seller();

				if (!$seller->CheckCompleted($getuser['ID'])) {
					header('?page=complete');
				}

				if ($_SERVER['REQUEST_METHOD'] == "POST") {
					$errors = array();
					/* Form POST Data */
					$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
					$smallprice = filter_var($_POST['smallprice'], FILTER_SANITIZE_STRING);
					$mediumprice = filter_var($_POST['mediumprice'], FILTER_SANITIZE_STRING);
					$largeprice = filter_var($_POST['largeprice'], FILTER_SANITIZE_STRING);
					$username = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
					$filename = filter_var($_FILES['file']['name'], FILTER_SANITIZE_STRING);
					$category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

					if (empty($name)) {
						echo '<div class="error">لايمكن لاسم المنتج ان يكون فارغا</div>';
					} elseif (empty($smallprice)) {
						echo '<div class="error">ادخل سعر المنتج</div>';
					} elseif (empty($mediumprice)) {
						echo '<div class="error">ادخل سعر المنتج</div>';
					} elseif (empty($largeprice)) {
						echo '<div class="error">ادخل سعر المنتج</div>';
					} elseif (empty($category)) {
						echo '<div class="error">ادخل صنف المنتج</div>';
					} elseif (empty($_FILES['file'])) {
						echo '<div class="error">ادخل صورة للمنتج</div>';
					} else {
						/* File Upload Variables */
						$filesize = $_FILES['file']['size'];
						$filetmp = $_FILES['file']['tmp_name'];
						$filetype = $_FILES['file']['type'];
						$extentions = array('image/jpeg', 'image/png', 'image/jpg');

						/* File Uploading */
						if (in_array($filetype, $extentions)) {
							$file = rand(0, 10000) . '_' . $filename;

							move_uploaded_file($filetmp, "uploads\products\\" . $file);
							$product = new Product();
							$add = $product->Add($file, $name, $smallprice, $mediumprice, $largeprice, $category, $username);
						} else {
							echo '<h2 class="error">امتداد هذا الملف غير مسموح به</h2>';
						}
						echo '<h2 class="success">تمت اضافة المنتج بنجاح</h2>';
					}
				}
			?>
			<div class="add-prodcut">
				<div class="centered-content">
					<h2 class="add-title">اضافة منتج</h2>
					<form action="?page=add" method="post" autocomplete="off" enctype="multipart/form-data">
						<input type="text" name="name" placeholder="اسم المنتج" required />
						<input type="file" name="file" required />
						<input type="number" name="smallprice" placeholder="سعر الحجم الصغير" step="0.01" required />
						<input type="number" name="mediumprice" placeholder="سعر الحجم الوسط" step="0.01" required />
						<input type="number" name="largeprice" placeholder="سعر الحجم الكبير" step="0.01" required />
						<select name="category" required>
							<option disabled selected value="">الصنف</option>
							<option value="foods">المأكولات</option>
							<option value="drinks">المشروبات</option>
						</select>
						<button class="addproduct-button">اضافة المنتج</button>
					</form>
				</div>
			</div>
		<?php } elseif ($page == "delete") {
				$delete = new Product();
				$delete->Delete(filter_var($_GET['id'], FILTER_SANITIZE_STRING));
				header("location: ?page=products");

			} elseif ($page == "editaccount") {
				$userinfo = new UserInfo();
				$row = $userinfo->GetInfo($_SESSION['username']);

				if ($_SERVER['REQUEST_METHOD'] == "POST") {
					/* Form POST Data */
					$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
					$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
					$oldpassword = filter_var($_POST['oldpassword'], FILTER_SANITIZE_STRING);
					$newpassword = filter_var($_POST['newpassword'], FILTER_SANITIZE_STRING);

					if ($username == $row['Username']) {
						$ready_username = $row['Username'];
					} else {
						$ready_username = $username;
					}
					if ($email == $row['Email']) {
						$ready_email = $row['Email'];
					} else {
						$ready_email = $email;
					}

					if (empty($oldpassword)) {
						echo "<div class='error'>لا يمكن لحقل كلمة المرور القديمة ان يكون فارغا</div>";
					} else {
						if (password_verify($oldpassword, $row['Password'])) {
							if (empty($newpassword)) {
								$password = $row['Password'];
							} else {
								$password = password_hash($newpassword, PASSWORD_DEFAULT);
							}
							$userinfo->EditInfo($ready_username, $ready_email, $password);
							header("location: ?page=home");
						} else {
							echo "<div class='error'>كلمة المرور غير صحيحة</div>";
						}
					}
				}
			?>
				<div class="add-prodcut">
					<div class="centered-content">
						<h2 class="add-title">تعديل البيانات</h2>
						<form action="?page=editaccount" method="post" autocomplete="off" enctype="multipart/form-data">
							<input type="text" name="username" placeholder="اسم المستخدم" value="<?php echo $row['Username'] ?>" required />
							<input type="email" name="email" placeholder="البريد الالكتروني" value="<?php echo $row['Email'] ?>" required />
							<input type="password" name="oldpassword" placeholder="الكلمة المرور القديمة" required />
							<input type="password" name="newpassword" placeholder="الكلمة المرور الجديدة" />
							<button class="addproduct-button">تعديل</button>
						</form>
					</div>
				</div>
		<?php } elseif ($page == "edit") {
			$product = new Product();
			$row = $product->GetProductById(filter_var($_GET['id'], FILTER_SANITIZE_STRING));

			if ($row == false) {
				header("location: ?page=products");
			}

			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				/* Form POST Data */
				$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
				$smallprice = filter_var($_POST['smallprice'], FILTER_SANITIZE_STRING);
				$mediumprice = filter_var($_POST['mediumprice'], FILTER_SANITIZE_STRING);
				$largeprice = filter_var($_POST['largeprice'], FILTER_SANITIZE_STRING);
				$category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

				if (empty($name)) {
					$name = $row['ProductName'];
				}
				if (empty($smallprice)) {
					$smallprice = $row['ProductSmallPrice'];
				}
				if (empty($mediumprice)) {
					$mediumprice = $row['ProductMediumPrice'];
				}
				if (empty($largeprice)) {
					$largeprice = $row['ProductLargePrice'];
				}
				if (empty($category)) {
					$category = $row['ProductCategory'];
				}

				if (!empty($_FILES['file']['name'])) {
					/* File Upload Variables */
					$filename = filter_var($_FILES['file']['name'], FILTER_SANITIZE_STRING);
					$filesize = $_FILES['file']['size'];
					$filetmp = $_FILES['file']['tmp_name'];
					$filetype = $_FILES['file']['type'];
					$extentions = array('image/jpeg', 'image/png', 'image/jpg');

					/* File Uploading */
					if (in_array($filetype, $extentions)) {
						$file = rand(0, 10000) . '_' . $filename;

						move_uploaded_file($filetmp, "uploads\products\\" . $file);
						$edit = $product->EditProduct($file, $name, $smallprice, $mediumprice, $largeprice, $category, $row['UserID'], filter_var($_GET['id'], FILTER_SANITIZE_STRING));
						echo $edit;
						echo '<h2 class="success">تم تعديل المنتج بنجاح</h2>';
					} else {
						echo '<h2 class="error">امتداد هذا الملف غير مسموح به</h2>';
					}
				} else {
					$edit = $product->EditProduct($row['ProductImage'], $name, $smallprice, $mediumprice, $largeprice, $category, $row['UserID'], filter_var($_GET['id'], FILTER_SANITIZE_STRING));
					echo '<h2 class="success">تم تعديل المنتج بنجاح</h2>';
				}
			}

		?>
			<div class="add-prodcut">
				<div class="centered-content">
					<h2 class="add-title">تعديل (<?php echo $row['ProductName'] ?>)</h2>
					<form action="?page=edit&id=<?php echo $row['ID'] ?>" method="post" autocomplete="off" enctype="multipart/form-data">
						<input type="text" name="name" value="<?php echo $row['ProductName'] ?>" placeholder="اسم المنتج" required />
						<input type="file" name="file" />
						<input type="number" name="smallprice" value="<?php echo $row['ProductSmallPrice'] ?>" placeholder="سعر الحجم الصغير" step="0.01" required />
						<input type="number" name="mediumprice" value="<?php echo $row['ProductPrice'] ?>" placeholder="سعر الحجم الوسط" step="0.01" required />
						<input type="number" name="largeprice" value="<?php echo $row['ProductLargePrice'] ?>" placeholder="سعر الحجم الكبير" step="0.01" required />
						<select name="category" required>
							<option selected value="<?php echo $row['ProductCategory'] ?>">
								<?php
									if ($row['ProductCategory'] == "foods") {
										$categoryType = "المأكولات";
									} elseif ($row['ProductCategory'] == "drinks") {
										$categoryType = "المشروبات";
									}
								?>
								<?php echo $categoryType ?>
							</option>
							<option disabled value="">الصنف</option>
							<option value="foods">المأكولات</option>
							<option value="drinks">المشروبات</option>
						</select>
						<button class="addproduct-button">تعديل المنتج</button>
					</form>
				</div>
			</div>
		<?php } elseif ($page == "complete") {
				$userInfo = new UserInfo();
				$getuser = $userInfo->GetInfo($_SESSION['username']);
				$seller = new Seller();

				if ($seller->CheckCompleted($getuser['ID']) || $getuser['GroupID'] == '0') {
					header("location: ?page=products");
				}

				if ($_SERVER['REQUEST_METHOD'] == "POST") {
					$phonenumber = filter_var($_POST['phonenumber'], FILTER_SANITIZE_STRING);
					$address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
					$bio = filter_var($_POST['bio'], FILTER_SANITIZE_STRING);

					$complete = $seller->Complete($phonenumber, $address, $bio, $getuser['ID']);

					header("location: ?page=products");
				}
			?>
			<div class="add-prodcut">
				<div class="centered-content">
					<h2 class="add-title">اكمال حسابك كبائع</h2>
					<form action="?page=complete" method="post" autocomplete="off" enctype="multipart/form-data">
						<input type="number" name="phonenumber" placeholder="رقم الهاتف" />
						<textarea placeholder="العنوان" name="address"></textarea>
						<textarea placeholder="الوصف" name="bio"></textarea>
						<button class="addproduct-button">اكمال الخطوات</button>
					</form>
				</div>
			</div>
	<?php } elseif ($page == "removeaccount") {
				$userinfo = new UserInfo();
				$userinfo->RemoveAccount(filter_var($_SESSION['username'], FILTER_SANITIZE_STRING));
				header("location: logout.php");
		} ?>
	</div>
</div>
<?php
// Including The Footer
include $tpl . 'footer.php';