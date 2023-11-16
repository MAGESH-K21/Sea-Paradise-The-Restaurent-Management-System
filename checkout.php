<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
include_once 'product-action.php';
error_reporting(0);
session_start();
if(empty($_SESSION["user_id"]))
{
	header('location:login.php');
}
else{
    foreach ($_SESSION["cart_item"] as $item)
    {
        $item_total += ($item["price"]*$item["quantity"]);
        
        if(isset($_POST['submit']))
        {
            $SQL="insert into users_orders(u_id,title,quantity,price) values('".$_SESSION["user_id"]."','".$item["title"]."','".$item["quantity"]."','".$item["price"]."')";
            mysqli_query($db,$SQL);
            echo $success = "Thank you! Your Order Placed Successfully!";
        }
    }
}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="checkout.png">
    <title>Order Checkout</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <link href="css/style.css" rel="stylesheet">
    <!-- Include Bootstrap Datepicker and Timepicker CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
</head>
<body>
    <script>
        const cashfree = Cashfree({ mode: "sandbox" });

        let link = '<?php if(isset($_SESSION['payment_session'])){ echo $_SESSION['payment_session'];  } ?>';
        console.log("Initial link value:", link);

        if (link !== null && link.trim() !== '') {
            console.log("Link has content:", link);
                                        
            cashfree.checkout({
                paymentSessionId: '<?php if(isset($_SESSION['payment_session'])){ echo $_SESSION['payment_session']; unset($_SESSION['payment_session']);} ?>',
                returnUrl: "http://localhost/foodpicky/Foodpicky/checkout.php?myorder={order_id}",
                redirectTarget: "_self"
            }).then(function () {
                console.log("on going redirection");
            });
        } else {
            console.log("Link is null or empty:", link);
        }
    </script>

    <div class="site-wrapper">
        <!--header starts-->
        <header id="header" class="header-scroll top-header headrom">
            <!-- .navbar -->
            <nav class="navbar navbar-dark">
                <div class="container">
                    <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                    <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/food-picky-logo.png" alt=""> </a>
                    <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                            <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Restaurants <span class="sr-only"></span></a> </li>
                            
                            <?php
                            if(empty($_SESSION["user_id"]))
                            {
                                echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
                                <li class="nav-item"><a href="registration.php" class="nav-link active">Sign Up</a> </li>';
                            }
                            else
                            {
                                echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">Your Orders</a> </li>';
                                echo  '<li class="nav-item"><a href="logout.php" class="nav-link active">LogOut</a> </li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- /.navbar -->
        </header>
        <div class="page-wrapper">
            <div class="top-links">
                <div class="container">
                    <ul class="row links">
                        <li class="col-xs-12 col-sm-4 link-item"><span>1</span><a href="restaurants.php">Choose Restaurant</a></li>
                        <li class="col-xs-12 col-sm-4 link-item "><span>2</span><a href="#">Pick your favorite dishes</a></li>
                        <li class="col-xs-12 col-sm-4 link-item active" ><span>3</span><a href="checkout.php">Get delivered & Pay</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="container">
                <?php
                $orderId = $_GET['myorder'];
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://sandbox.cashfree.com/pg/orders/$orderId",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_HTTPHEADER => array(
                    'x-client-id: 327693bf684336fe7dbeabaafb396723', // Correct client ID
                    'x-client-secret: TEST2e75eb80d252ce6315734deb99db70c69bb626cc', // Correct client secret
                    'x-api-version: 2021-05-21'
                  ),
                ));

                 $response = curl_exec($curl);
                $err = curl_error($curl);

                if (!$err) {
                  $result = json_decode($response, true);
                  if ($result["order_status"] == 'PAID') {
                    foreach ($_SESSION["cart_item"] as $item)
                    {
                        $item_total += ($item["price"]*$item["quantity"]);
                        $SQL="insert into users_orders(u_id,title,quantity,price) values('".$_SESSION["user_id"]."','".$item["title"]."','".$item["quantity"]."','".$item["price"]."')";
                        mysqli_query($db,$SQL);
                        $success = "Thank you! Your Order Placed Successfully!";
                    }
                ?>
                <span style="color:green;">
                    <?php
                    echo "<center><b>Order Status: </b>".$success;
                    ?>
                </span>
                <?php
                  } else {
                ?>
                <span style="color:red;">
                    <?php
                    echo "<center><b>Order Status: </b>Order has not been paid!<?center>";
                    ?>
                </span>
                <?php
                  }
                } else {
                  echo $err;
                }
                ?>
            </div>
            
            <div class="container m-t-30">
                <form action="" method="post">
                    <div class="widget clearfix">
                        <div class="widget-body">
                            <form method="post" action="#">
                                <div class="row">
                                    <div class="form-group">
                                        <label for="delivery_date">Delivery Date:</label>
                                        <input type="date" id="delivery_date" name="delivery_date" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="delivery_time">Delivery Time:</label>
                                        <input type="time" id="delivery_time" name="delivery_time" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" name="submit" class="btn btn-primary" value="Place Order">
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="cart-totals margin-b-20">
                                            <div class="cart-totals-title">
                                                <h4>Cart Summary</h4>
                                            </div>
                                            <div class="cart-totals-fields">
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td>Cart Subtotal</td>
                                                            <td> &#8377;<?php echo $item_total; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Shipping &amp; Handling</td>
                                                            <td>FREE*</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-color"><strong>Total</strong></td>
                                                            <td class="text-color"><strong> &#8377;<?php echo $item_total; ?></strong></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="payment-option">
                                            <ul class=" list-unstyled">
                                                <li>
                                                    <label class="custom-control custom-radio  m-b-20">
                                                        <input name="mod" id="radioStacked1" checked value="COD" type="radio" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description">Cash on delivery</span>
                                                        <br> <span>Pay digitally with SMS Pay Link. Cash may not be accepted in COVID restricted areas.</span> </label>
                                                </li>
                                                <li>
                                                    <label class="custom-control custom-radio  m-b-10">
                                                        <input name="mod"  type="radio" value="paypal" class="custom-control-input" onClick="location.href='s.php'"> <span class="custom-control-indicator"></span> <span class="custom-control-description">Credit Card<img src="images/paypal.jpg" alt="" width="90"></span> </label>
                                                </li>
                                            </ul>
                                            <p class="text-xs-center"> <input type="submit" onclick="return confirm('Are you sure?');" name="submit"  class="btn btn-outline-success btn-block" value="Order now"></p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </form>
            </div>
            <section class="app-section">
                <div class="app-wrap">
                    <div class="container">
                        <div class="row text-img-block text-xs-left">
                            <div class="container">
                                <div class="col-xs-12 col-sm-5 right-image text-center">
                                    <figure> <img src="images/app.png" alt="Right Image" class="img-fluid"> </figure>
                                </div>
                                <div class="col-xs-12 col-sm-7 left-text">
                                    <h3>Food Picky - The Best Food Delivery App</h3>
                                    <p>Got Hungry? Get the food you want, from the restaurants you love, delivered at blinking speed.

                                    Eat what you like, where you like, when you like. Find the local flavors you crave, all at the tap of a button.</p>
                                    <div class="social-btns">
                                        <a href="#" class="app-btn apple-button clearfix">
                                            <div class="pull-left"><i class="fa fa-apple"></i> </div>
                                            <div class="pull-right"> <span class="text">Available on the</span> <span class="text-2">App Store</span> </div>
                                        </a>
                                        <a href="#" class="app-btn android-button clearfix">
                                            <div class="pull-left"><i class="fa fa-android"></i> </div>
                                            <div class="pull-right"> <span class="text">Available on the</span> <span class="text-2">Play Store</span> </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- start: FOOTER -->
            <footer class="footer">
                <div class="container">
                    <!-- top footer starts -->
                    <div class="row top-footer">
                        <div class="col-xs-12 col-sm-3 footer-logo-block color-gray">
                            <a href="#"> <img src="images/food-picky-logo.png" alt="Footer logo"> </a> <span>Choose it &amp; Enjoy your meals! </span> </div>
                        <div class="col-xs-12 col-sm-2 about color-gray">
                            <h5>About Us</h5>
                            <ul>
                                <li><a href="#">Our Mission</a></li>
                                <li><a href="#">Social Media</a></li>
                                <li><a href="#">Testimonials</a></li>
                                <li><a href="#">We are hiring</a></li>
                                <li><a href="#">Join us Today</a></li>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-2 how-it-works-links color-gray">
                            <h5>How it Works?</h5>
                            <ul>
                                <li><a href="#">Enter your location</a></li>
                                <li><a href="#">Choose the restaurant</a></li>
                                <li><a href="#">Choose your dishes</a></li>
                                <li><a href="#">Get delivered</a></li>
                                <li><a href="#">Pay on delivery</a></li>
                                <li><a href="#">Enjoy your meals :)</a></li>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-2 pages color-gray">
                            <h5>Legal</h5>
                            <ul>
                                <li><a href="#">Terms & Conditions</a> </li>
                                <li><a href="#">Refund & Cancellation</a> </li>
                                <li><a href="#">Privacy Policy</a> </li>
                                <li><a href="#">Cookie Policy</a> </li>
                                <li><a href="#">Offer Terms</a> </li>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-3 popular-locations color-gray">
                            <h5>Locations We Deliver To</h5>
                            <ul>
                                <li><a href="#">Chennai</a> </li>
                                <li><a href="#">Kanchipuram</a> </li>
                                <li><a href="#">Tiruchy</a> </li>
                                <li><a href="#">Salem</a> </li>
                                <li><a href="#">Madurai</a> </li>
                                <li><a href="#">Theni</a> </li>
                                <li><a href="#">Thiruvallur</a> </li>
                                <li><a href="#">Pondicherry</a> </li>
                                <li><a href="#">Thoothukudi</a> </li>
                                <li><a href="#">Kanyakumari</a> </li>
                            </ul>
                        </div>
                    </div>
                    <!-- top footer ends -->
                    <!-- bottom footer starts -->
                    <div class="bottom-footer">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 payment-options color-gray">
                                <h5>All Major Credit Cards Accepted</h5>
                                <ul>
                                    <li>
                                        <a href="#"> <img src="images/paypal.png" alt="Paypal"> </a>
                                    </li>
                                    <li>
                                        <a href="#"> <img src="images/mastercard.png" alt="Mastercard"> </a>
                                    </li>
                                    <li>
                                        <a href="#"> <img src="images/maestro.png" alt="Maestro"> </a>
                                    </li>
                                    <li>
                                        <a href="#"> <img src="images/stripe.png" alt="Stripe"> </a>
                                    </li>
                                    <li>
                                        <a href="#"> <img src="images/bitcoin.png" alt="Bitcoin"> </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-xs-12 col-sm-4 address color-gray">
                                <h5>Address:</h5>
                                <p>Mahatma Gandhi Salai, Chennai - 600034.</p>
                                <h5>Call us at: <a href="tel:+914450005500">+91 44 50005500</a></h5>
                            </div>
                            <div class="col-xs-12 col-sm-5 additional-info color-gray">
                                <h5>Who are we?</h5>
                                <p>Launched in 2021, Our technology platform connects customers, restaurant partners and delivery partners, serving their multiple needs. Customers use our platform to search and discover restaurants, read and write customer generated reviews and view and upload photos, order food delivery, book a table and make payments while dining-out at restaurants.</p>
                            </div>
                        </div>
                    </div>
                    <!-- bottom footer ends -->
                </div>
            </footer>
            <!-- end:Footer -->
        </div>
        <!-- end:page wrapper -->
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>


</body>

</html>
