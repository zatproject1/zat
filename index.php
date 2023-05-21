<?php
session_start();

/* Shopping Cart */
$product_ids = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (isset($_SESSION['shopping_cart'])) {
		$count = count($_SESSION['shopping_cart']);
		$product_ids = array_column($_SESSION['shopping_cart'], 'id');

		if (!in_array(filter_input(INPUT_POST, 'id'), $product_ids)) {
			$_SESSION['shopping_cart'][$count] = array(
				'id' => filter_input(INPUT_POST, 'id'),
				'name' => filter_input(INPUT_POST, 'name'),
				'price' => filter_input(INPUT_POST, 'price'),
				'quantity' => filter_input(INPUT_POST, 'quantity'),
				'size' => filter_input(INPUT_POST, 'size-' . filter_input(INPUT_POST, 'id')),
			);
		} else {
			for ($i = 0; $i < count($product_ids); $i++) {
				if ($product_ids[$i] == filter_input(INPUT_POST, 'id')) {
					$_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
					$_SESSION['shopping_cart'][$i]['size'] = $_SESSION['shopping_cart'][$i]['size'] . ',' . filter_input(INPUT_POST, 'size-' . filter_input(INPUT_POST, 'id'));
					$_SESSION['shopping_cart'][$i]['price'] += filter_input(INPUT_POST, 'price');
				}
			}
		}
	} else {
		$_SESSION['shopping_cart'][0] = array(
			'id' => filter_input(INPUT_POST, 'id'),
			'name' => filter_input(INPUT_POST, 'name'),
			'price' => filter_input(INPUT_POST, 'price'),
			'quantity' => filter_input(INPUT_POST, 'quantity'),
			'size' => filter_input(INPUT_POST, 'size-' . filter_input(INPUT_POST, 'id')),
		);
	}
}

// Includes The Init File And The Header
$noSessionStart = '';
include 'includes/init.php';
include $tpl . 'header.php';
$category = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_SANITIZE_STRING) : 'all';
?>
<div class="shopheader">
	<h2>جميع منتجاتنا تجدها هنا</h2>
</div>
<div class="container">
	<div class="categories">
		<div class="category <?php if (isset($category) && $category == "drinks") { echo "checked"; } ?>">
			<a href="?category=drinks">المشروبات</a>
		</div>
		<div class="category <?php if (isset($category) && $category == "foods") { echo "checked"; } ?>">
			<a href="?category=foods">المأكولات</a>
		</div>
		<div class="category <?php if (isset($category) && $category == "all") { echo "checked"; } ?>">
			<a href="?category=all">جميع المنتجات</a>
		</div>
	</div>
	<div class="products-page">
		<?php
			$getProducts = new Product();
			$rows = $getProducts->Fetch($category);

			if (!empty($rows)) {
				foreach($rows as $row) {
		?>
		<div class="product-card">
			<div class="overlay">
				<div class="add-to-cart">
					<i class="icon"><button form="order-form-<?php echo $row['ID'] ?>"><i class="fas fa-cart-plus"></i></button></i>
				</div>
				<div class="seller-info">
					<i class="icon"><button onclick="ShowInfo('#first-layer-<?php echo $row['ID'] ?>', '#second-layer-<?php echo $row['ID'] ?>')"><i class="fas fa-info"></i></button></i>
				</div>
				<div class="first-layer" id="first-layer-<?php echo $row['ID'] ?>">
					<div class="sizes">
						<div class="size">
							<label for="small-<?php echo $row['ID'] ?>" class="size-label">
								<i id="small-icon-<?php echo $row['ID'] ?>" class="fas fa-dot-circle"></i>
								Small
							</label>
						</div>
						<div class="size">
							<label for="medium-<?php echo $row['ID'] ?>" class="size-label">
								<i id="medium-icon-<?php echo $row['ID'] ?>" class="fas fa-dot-circle checked"></i>
								Medium
							</label>
						</div>
						<div class="size">
							<label for="large-<?php echo $row['ID'] ?>" class="size-label">
								<i id="large-icon-<?php echo $row['ID'] ?>" class="fas fa-dot-circle"></i>
								Large
							</label>
						</div>
					</div>
				</div>
				<div class="second-layer" style="display: none" id="second-layer-<?php echo $row['ID'] ?>">
					<div class="infos">
						<div class="info">
							Username: <?php echo $row['username'] ?>
						</div>
						<div class="info">
							Phone: <?php echo $row['PhoneNumber'] ?>
						</div>
						<div class="info">
							Address: <?php echo $row['Address'] ?>
						</div>
						<div class="info">
							Bio: <?php echo $row['Bio'] ?>
						</div>
					</div>
				</div>
			</div>
			<form id="order-form-<?php echo $row['ID'] ?>" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
				<input type="hidden" name="id" value="<?php echo $row['ID'] ?>" />
				<input type="hidden" name="name" value="<?php echo $row['ProductName'] ?>" />
				<input id="priceinput-<?php echo $row['ID'] ?>" type="hidden" name="price" value="<?php echo $row['ProductPrice'] ?>" />
				<input type="hidden" name="quantity" value="1" />
				<input class="size-radio" id="small-<?php echo $row['ID'] ?>" type="radio" name="size-<?php echo $row['ID'] ?>" onchange="SizeSelect('#small-icon-<?php echo $row['ID'] ?>', '#medium-icon-<?php echo $row['ID'] ?>', '#large-icon-<?php echo $row['ID'] ?>'); ChangePrice('<?php echo $row['ProductSmallPrice'] ?>', '#priceinput-<?php echo $row['ID'] ?>', '#price-text-<?php echo $row['ID'] ?>')" value="small" />
				<input class="size-radio" id="medium-<?php echo $row['ID'] ?>" type="radio" name="size-<?php echo $row['ID'] ?>" onchange="SizeSelect('#medium-icon-<?php echo $row['ID'] ?>', '#small-icon-<?php echo $row['ID'] ?>', '#large-icon-<?php echo $row['ID'] ?>'); ChangePrice('<?php echo $row['ProductPrice'] ?>', '#priceinput-<?php echo $row['ID'] ?>', '#price-text-<?php echo $row['ID'] ?>')" value="medium" checked />
				<input class="size-radio" id="large-<?php echo $row['ID'] ?>" type="radio" name="size-<?php echo $row['ID'] ?>" onchange="SizeSelect('#large-icon-<?php echo $row['ID'] ?>', '#small-icon-<?php echo $row['ID'] ?>', '#medium-icon-<?php echo $row['ID'] ?>'); ChangePrice('<?php echo $row['ProductLargePrice'] ?>', '#priceinput-<?php echo $row['ID'] ?>', '#price-text-<?php echo $row['ID'] ?>')" value="large" />
			</form>
			<img src="uploads/products/<?php echo $row['ProductImage'] ?>" />
			<div class="text">
				<h2><?php echo $row['ProductName'] ?></h2>
			</div>
			<div id="price-text-<?php echo $row['ID'] ?>" class="price"><?php echo $row['ProductPrice'] ?></div>
		</div>
		<?php
				}
			}
		?>
	</div>
</div>
<?php
// Including The Footer
include $tpl . 'footer.php';