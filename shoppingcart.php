<?php
require("PHPMailer-master/src/PHPMailer.php");
require("PHPMailer-master/src/SMTP.php");
require("PHPMailer-master/src/Exception.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

if (filter_input(INPUT_GET, 'action') == 'delete') {
    foreach ($_SESSION['shopping_cart'] as $key => $product) {
        if ($product['id'] == filter_input(INPUT_GET, 'id')) {
            unset($_SESSION['shopping_cart'][$key]);
        }
    }
    $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}

// Includes The Init File And The Header
$noSessionStart = '';
include 'includes/init.php';
include $tpl . 'header.php';

function SendMail($selleremail, array $products, $username, $address, $paymentType) {
    global $host, $email, $password, $port, $websiteName, $reply;
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

        $mail->addAddress($selleremail); 
        $mail->setFrom($email, $websiteName);
        $mail->addReplyTo($reply, $websiteName);

        $mail->isHTML(true);
        $mail->Subject = 'New Order From ' . $websiteName;
        $mail->Body .= '<div class="email" style="text-align: right">';
        foreach ($products as $key => $product) {
            $mail->Body .= '
                <table class="styled-table" style="border-collapse: collapse; margin: auto; font-size: 0.9em; font-family: sans-serif; min-width: 400px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.15); right: 0; left: 0; width: 1200px;">
                    <thead>
                        <tr style="background-color: #009879; color: #ffffff; text-align: left;">
                            <th scope="col" style="padding: 12px 15px;">العنوان</th>
                            <th scope="col" style="padding: 12px 15px;">اسم المشتري</th>
                            <th scope="col" style="padding: 12px 15px;">السعر</th>
                            <th scope="col" style="padding: 12px 15px;">اسم المنتج</th>
                        </tr>
                    </thead>
                    <tbody class="active-row">
                        <tr style="border-bottom: 1px solid #dddddd;">
                            <td style="padding: 12px 15px;">' . $address . '</td>
                            <td style="padding: 12px 15px;">' . $username . '</td>
                            <td style="padding: 12px 15px;">$' . $product['price'] . '</td>
                            <td style="padding: 12px 15px;">' . $key . '</td>
                        </tr>
                    </tbody>
                </table>
            ';
        }
        $mail->Body .= "</div>";
        $mail->AltBody = 'Please Enable HTML Email Client Service';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function ReceiptMail($clientemail) {
    global $host, $email, $password, $port, $websiteName, $reply;
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

        $mail->addAddress($clientemail); 
        $mail->setFrom($email, $websiteName);
        $mail->addReplyTo($reply, $websiteName);

        $mail->isHTML(true);
        $mail->Subject = 'Your Order From' . $websiteName;
        $mail->Body    = '<div class="email" style="text-align: right">';
        $mail->Body .= '
            <table class="styled-table" style="border-collapse: collapse; margin: auto; font-size: 0.9em; font-family: sans-serif; min-width: 400px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.15); right: 0; left: 0; width: 1200px;">
            <thead>
                <tr style="background-color: #009879; color: #ffffff; text-align: left;">
                    <th scope="col" style="padding: 12px 15px;">السعر</th>
                    <th scope="col" style="padding: 12px 15px;">اسم المنتج</th>
                </tr>
            </thead>
            <tbody class="active-row">
        ';
        foreach ($_SESSION['shopping_cart'] as $key => $product) {
            $mail->Body .= '
                <tr style="border-bottom: 1px solid #dddddd;">
                    <td style="padding: 12px 15px;">$' . $product['price'] . '</td>
                    <td style="padding: 12px 15px;">' . $product['name'] . '</td>
                </tr>
            ';
        }
        $mail->Body .= "
                    </tbody>
                </table>
            </div>
        ";
        $mail->AltBody = 'Please Enable HTML Email Client Service';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_SESSION['username'])) {
        if (!empty($_SESSION['shopping_cart'])) {
            $paymentType = filter_var($_POST['payment'], FILTER_SANITIZE_STRING);
            $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
        
            $seller = new Seller();
            $userinfo = new UserInfo();
            $ordered = array();
        
            $user = $userinfo->GetInfo(filter_var($_SESSION['username'], FILTER_SANITIZE_STRING));
        
            foreach ($_SESSION['shopping_cart'] as $key => $product) {
                $selleremail = $seller->GetSellerEmail($product['id']);
                if (array_key_exists($selleremail, $ordered)) {
                    $productName = $ordered[$selleremail]['oneproduct'];
                    $quantity = $ordered[$selleremail]['quantity'];
                    $size = $ordered[$selleremail]['size'];
                    $price = $ordered[$selleremail]['price'];
                    $ordered[$selleremail]['products'] = array(
                        $productName => array(
                            "quantity" => $quantity,
                            "size" => $size,
                            "price" => $price
                        ),
                        $product['name'] => array(
                            "quantity" => $product['quantity'],
                            "size" => $product['size'],
                            "price" => $product['price']
                        ),
                    );
                } else {
                    $ordered[$selleremail] = array(
                        'selleremail' => $selleremail,
                        'oneproduct' => $product['name'],
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                        'size' => $product['size'],
                        'products' => array(
                            $product['name'] => array(
                                'quantity' => $product['quantity'],
                                'price' => $product['price'],
                                'size' => $product['size'],
                            ),
                        ),
                        'username' => $user['Username'],
                        'useremail' => $user['Email'],
                        'address' => $address,
                    );
                }
                SendMail($selleremail, $ordered[$selleremail]['products'], $ordered[$selleremail]['username'], $ordered[$selleremail]['address'], $paymentType);
                $order = new OrderSubmit();
                $order->StoreToDB($product['name'], $product['price'], $product['size'], $seller->GetSellerData($product['id'])['ID'], $userinfo->GetInfo(filter_var($_SESSION['username'], FILTER_SANITIZE_STRING))['ID']);
            }
            ReceiptMail($user['Email']);
            $_SESSION['orderplaced'] = true;
            header("location: orderplaced.php");
        } else {
            echo "<div class='error'>لايمكنك ان تترك عربة التسوق فارغة</div>";
        }
    }
}
?>
<div class="shoppingcart">
    <div class="container">
        <div class="cart-content">
            <?php
                $total = 0;

                if (!empty($_SESSION['shopping_cart'])) {
                    foreach ($_SESSION['shopping_cart'] as $key => $product) {
                        $total += $product['price'] * $product['quantity'];
                        $cart = new Cart($product['id']);
                        $getImage = $cart->GetImage();
            ?>
            <div class="cart-product">
                <img class="product-img" src="uploads/products/<?php echo $getImage['ProductImage'] ?>" alt="ProductName" />
                <div class="text">
                    <h2 class="product-name"><?php echo $product['name'] ?></h2>
                    <p class="quantity"><?php echo $product['quantity'] ?> :الكمية</p>
                    <p class="size"><?php echo $product['size'] ?> :الحجم</p>
                    <p class="price">$<?php echo ($product['price']) ?> :السعر</p>
                    <button onclick="window.open('?action=delete&id=<?php echo $product['id'] ?>', '_self')">حذف</button>
                </div>
            </div>
            <?php
                    }
                } else {
                    echo "<div class='empty-shopping-cart'>عربة التسوق فارغة</div>";
                }
            ?>
        </div>
        <div class="checkout">
            <h2>$<?php echo $total; ?></h2>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                <input type="hidden" name="total" value="<?php echo $total ?>" />
                <div class="payment-type">
                    <label for="cash">الدفع عند الاستلام</label>
                    <input class="payment-type" id="cash" type="radio" name="payment" value="cash" checked />
                </div>
                <div class="payment-type">
                    <label for="credit-card">الدفع ببطاقة الأئتمان</label>
                    <input class="payment-type" id="credit-card" type="radio" name="payment" value="Credit Card" />
                </div>
                <div class="adress">
                    <textarea style="float: right; width: 100%; margin-top: 15px" name="address" placeholder="عنوانك" required></textarea>
                </div>
                <div style="display: none" class="creditcard">
                    <input type="text" name="cardnumber" placeholder="رقم البطاقة الأئتمانية" />
                    <input type="date" name="expiration" />
                    <input type="number" minlength="3" maxlength="4" placeholder="الرمز السري" />
                    </div>
                <button type="submit" <?php if (!isset($_SESSION['username'])) { ?> style="background: rgb(113 113 113);" <?php } ?> <?php echo isset($_SESSION['username']) ? '' : 'disabled'; ?>><?php echo isset($_SESSION['username']) ? 'الدفع' : 'سجل دخولك اولا'; ?></button>
            </form>
        </div>
    </div>
</div>
<?php
// Including The Footer
include $tpl . 'footer.php';