<?php

namespace Stripe;

require_once( 'Connections/transcribe.php' );
include( "includes/auth.php" );
include( "includes/appstatus.php" );
require_once( 'stripe/stripe-php-3.12.0/vendor/autoload.php' );
require_once( 'stripe/stripe-php-3.12.0/lib/Stripe.php' );
include( "functions.php" );
include( "includes/nav-query.php" );

$colname_rsUserInfo = "-1";
if ( isset( $_SESSION[ 'uid' ] ) ) {
    $colname_rsUserInfo = $_SESSION[ 'uid' ];
}
mysql_select_db( $database_transcribe, $transcribe );
$query_rsUserInfo = sprintf( "SELECT * FROM users WHERE userid = %s", GetSQLValueString( $colname_rsUserInfo, "int" ) );
$rsUserInfo = mysql_query( $query_rsUserInfo, $transcribe )or die( mysql_error() );
$row_rsUserInfo = mysql_fetch_assoc( $rsUserInfo );
$totalRows_rsUserInfo = mysql_num_rows( $rsUserInfo );

$credits = $row_rsUserInfo[ 'credits' ];

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}

$transPercentage = .032;
$transCost = .30;

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {

    $date = date( "Y-m-d H:i:s" );

    if ( strlen( $_POST[ 'card_number' ] ) < 1 ) {
        $status = "Please enter credit card number.";
        $submitstatus = 0;
    } else if ( strlen( $_POST[ 'address' ] ) < 1 ) {
        $status = "Please enter street address.";
        $submitstatus = 0;
    } else if ( strlen( $_POST[ 'city' ] ) < 1 ) {
        $status = "Please enter city.";
        $submitstatus = 0;
    } else if ( strlen( $_POST[ 'state' ] ) < 1 ) {
        $status = "Please enter state.";
        $submitstatus = 0;
    } else if ( strlen( $_POST[ 'zip' ] ) < 1 ) {
        $status = "Please enter zip.";
        $submitstatus = 0;
    } else if ( strlen( $_POST[ 'exp' ] ) < 1 || strlen( $_POST[ 'exp2' ] ) < 1 ) {
        $status = "Please enter card expiration.";
        $submitstatus = 0;
    } else if ( strlen( $_POST[ 'cvc_number' ] ) < 1 ) {
        $status = "Please enter card CVC.";
        $submitstatus = 0;
    } else {
        $submitstatus = 1;
    }

    if ( $submitstatus == 1 ) {
        $query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stripe", "text" ) );
        $rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
        $row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

        $apiKey = $row_rsKeyInfo[ 'apikey' ];

        //echo "key: {$apiKey}";

        if ( isset( $apiKey ) ) {

            Stripe::setApiKey( $apiKey );

            $_POST[ 'stripe' ] = true;

            if ( isset( $_POST[ 'credits' ] ) ) {

                $amount = floatval( $_POST[ 'credits' ] ) * 100;

                if ( isset( $_POST[ 'stripe' ] ) ) {

                    try {

                        $token = Token::create(
                            array(
                                "card" => array(
                                    "name" => $_POST[ 'firstname' ] . " " . $_POST[ 'lastname' ],
                                    "number" => $_POST[ 'card_number' ],
                                    "exp_month" => $_POST[ 'exp' ],
                                    "exp_year" => $_POST[ 'exp2' ],
                                    "cvc" => $_POST[ 'cvc_number' ],
                                    "address_city" => $_POST[ 'city' ],
                                    "address_country" => $_POST[ 'country' ],
                                    "address_line1" => $_POST[ 'address' ],
                                    "address_state" => $_POST[ 'state' ],
                                    "address_zip" => $_POST[ 'zip' ]
                                )
                            )
                        );

                        // Create the charge on Stripe's servers - this will charge the user's card

                        try {

                            //echo "try";

                            //https://stripe.com/docs/api/charges/create

                            $int = intval( $_POST[ 'credits' ] );
                            $inttrans = ( $int * $transPercentage ) + $transCost;
                            $fee = intval( $inttrans * 100 );
                            $amount = $amount + $fee;

                            $debug = false;

                            if ( $debug ) {
                                echo "credits: {$int}<br>";
                                echo "transCost: {$transCost}<br>";
                                echo "transPercentage: {$transPercentage}<br>";
                                echo "fee: {$fee}<br>";
                                echo "amount: {$amount}";

                                return;
                            }

                            $charge = Charge::create( array(

                                "amount" => $amount, // amount in cents, again
                                "currency" => "usd",
                                "source" => $token,
                                //"application_fee_amount"=> $fee,
                                "description" => "AIScribe credits purchase"
                            ) );

                            //echo '<pre>' , var_dump($charge) , '</pre>';

                            $transactioncode = $charge->id;

                            $status = "payment successful";

                            //echo $status;

                            $credits2 = floatval( $credits ) + ( floatval( $_POST[ 'credits' ] ) );

                            $updateSQL = sprintf( "UPDATE users SET credits=%s WHERE userid=%s",
                                GetSQLValueString( number_format( $credits2, 2 ), "text" ),
                                GetSQLValueString( $_POST[ 'uid' ], "int" ) );

                            mysql_select_db( $database_transcribe, $transcribe );
                            $Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );

                            //insert into transactions

                            $insertSQL = sprintf( "INSERT INTO transactions (transactioncode, userid,amount,datecreated) VALUES (%s, %s, %s, %s)",
                                GetSQLValueString( mysql_real_escape_string( $transactioncode ), "text" ),
                                GetSQLValueString( mysql_real_escape_string( $_POST[ 'uid' ] ), "int" ),
                                GetSQLValueString( mysql_real_escape_string( $_POST[ 'credits' ] ), "text" ),
                                GetSQLValueString( mysql_real_escape_string( $date ), "date" ) );

                            mysql_select_db( $database_transcribe, $transcribe );
                            $Result2 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

                            $updateGoTo = "my-account.php";
                            if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
                                $updateGoTo .= ( strpos( $updateGoTo, '?' ) ) ? "&" : "?";
                                $updateGoTo .= $_SERVER[ 'QUERY_STRING' ];
                            }
                            header( sprintf( "Location: %s", $updateGoTo ) );

                        } catch ( Error\ Card $e ) {

                            $body = $e->getJsonBody();

                            //var_dump($body);

                            $err = $body[ 'error' ];
                            $status = $err[ 'message' ];

                            //echo $status;
                            //var_dump($e);
                        }

                    } catch ( Error\ Card $e ) {

                        $body = $e->getJsonBody();

                        //var_dump($body);

                        $err = $body[ 'error' ];
                        $status = $err[ 'message' ];
                    }
                }

            } else {

                $status = "Missing params";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="my-files.css">
    <meta charset="utf-8">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="buy-credits.js"></script>
    <script src="validate.js"></script>
    <script src="side-nav.js"></script>
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <title>Buy Credits</title>
</head>

<body>
    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <span style="font-size:30px;cursor:pointer"><img id="navIcon" name="navIcon" src="img/Hamburger_icon.png" class="image"/></span>

            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
        </div>
        <?php include("includes/nav.php");?>
        <div id="titleDiv" class="clearfix">
            <div id="headerTxtBG" class="clearfix">
                <p id="headerLbl">Buy Credits</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">

                <p>&nbsp;</p>
                <table width="100%" cellpadding="5" cellspacing="5">
                    <tbody>
                        <?php if (isset($status)) { ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td style="color: red">
                                <?php echo $status?>
                            </td>
                        </tr>

                        <?php } ?>
                        <tr>
                            <td>Credits</td>
                            <td><input name="credits" type="text" id="credits" value="5.00">
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td id="subtotal">
                                <?php

                                //calculate transaction cost

                                $fl = floatval( 5 );
                                $per = ( $fl * $transPercentage ) + $transCost;
                                $total = $fl + $per;

                                echo "Subtotal: $" . number_format( $total, 2 );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="13%">First Name</td>
                            <td width="87%"><input name="firstname" type="text" id="firstname" value="<?php echo $row_rsUserInfo['firstname']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Last Name</td>
                            <td><input name="lastname" type="text" id="lastname" value="<?php echo $row_rsUserInfo['lastname']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Card Number</td>
                            <td><input name="card_number" type="text" id="card_number" value="<?php if(isset($_POST['card_number'])) {echo $_POST['card_number'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td><input name="address" type="text" id="address" value="<?php if(isset($_POST['address'])) {echo $_POST['address'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td><input name="city" type="text" id="city" value="<?php if(isset($_POST['city'])) {echo $_POST['city'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>State</td>
                            <td><input name="state" type="text" id="state" value="<?php if(isset($_POST['state'])) {echo $_POST['state'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Zip</td>
                            <td><input name="zip" type="text" id="zip" value="<?php if(isset($_POST['zip'])) {echo $_POST['zip'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Country</td>
                            <td><input name="country" type="text" id="country" value="<?php if(isset($_POST['country'])) {echo $_POST['country'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Expiration</td>
                            <td><input name="exp" type="text" id="exp" placeholder="MM" value="<?php if(isset($_POST['exp'])) {echo $_POST['exp'];} ?>">
                                <input name="exp2" type="text" id="exp2" placeholder="YYYY" value="<?php if(isset($_POST['exp2'])) {echo $_POST['exp2'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>CCV</td>
                            <td><input name="cvc_number" type="text" id="cvc_number" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" name="submit" id="submit" value="Submit">
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="MM_update" value="form1">
                <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']; ?>">
                </td>
            </form>
            <p id="docContentDiv">&nbsp;</p>
        </div>
    </div>
    <?php include("includes/side-nav.php");?>
</body>
</html>
<?php
mysql_free_result( $rsUserInfo );
?>