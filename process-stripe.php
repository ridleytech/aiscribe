<?php

namespace Stripe;

require_once( 'Connections/transcribe.php' );
require_once( 'stripe/stripe-php-3.12.0/vendor/autoload.php' );
require_once( 'stripe/stripe-php-3.12.0/lib/Stripe.php' );

include( "functions.php" );

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


if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
    
    $date = date("Y-m-d H:i:s");
    
    
    if(!isset($_POST[ 'card_number' ]))
    {
        $status = "Please enter credit card number.";
        return;
    }
    
    if(!isset($_POST[ 'exp' ]))
    {
        $status = "Please enter card expiration.";
        return;
    }
    
    if(!isset($_POST[ 'cvc_number' ]))
    {
        $status = "Please enter card CVC.";
        return;
    }
    

    Stripe::setApiKey( "sk_test_HL9lJGFZkdAomtO5PXrHkJ5k" ); //ridleytech@gmail.com BB

    $_POST[ 'stripe' ] = true;
    //$_POST['amount'] = 10;


    if ( isset( $_POST[ 'credits' ] ) ) {
        
        $amount = floatval( $_POST[ 'credits' ] ) * 100;

        //debug payments

        //$amount = 100; //in cents. $1

        //debug info
        
        $exp = explode("/",$_POST[ 'exp' ]);
        
//        var_dump($_POST);
//        var_dump($exp);
//        
//        return;
//
//        $_POST[ 'name' ] = "Randall";
//        $_POST[ 'card_number' ] = "4242424242424242";
//        $_POST[ 'month' ] = "12";
//        $_POST[ 'year' ] = "2019";
//        $_POST[ 'cvc_number' ] = "124";


        if ( isset( $_POST[ 'stripe' ] ) ) {
            
            $token = Token::create(
                array(
                    "card" => array(
                        "name" => $_POST['firstname'] . " " . $_POST['lastname'],
                        "number" => $_POST[ 'card_number' ],
                        "exp_month" => $exp[0],
                        "exp_year" => $exp[1],
                        "cvc" => $_POST[ 'cvc_number'],
                        "address_city"=> $_POST[ 'city' ],
                        "address_country"=> $_POST[ 'country' ],
                        "address_line1"=> $_POST[ 'address' ],
                        "address_state"=> $_POST[ 'state' ],
                        "address_zip"=> $_POST[ 'zip' ]
                    )
                )
            );
                        
//            $token = Token::create(
//                array(
//                    "card" => array(
//                        "name" => $_POST[ 'name' ],
//                        "number" => $_POST[ 'card_number' ],
//                        "exp_month" => $_POST[ 'month' ],
//                        "exp_year" => $_POST[ 'year' ],
//                        "cvc" => $_POST[ 'cvc_number' ]
//                    )
//                )
//            );
        } else if ( isset( $_POST[ 'applepay' ] ) ) {
            
            $token = $_POST[ 'stripeToken' ];
        }

        // Create the charge on Stripe's servers - this will charge the user's card

        try {

            //echo "try";
            
            //https://stripe.com/docs/api/charges/create
            
            //$subtotal = $amount + 30 + ($amount  * .029);
            
            $fee = intval(30 + ($amount  * .029));
            
            $amount = $amount + $fee;

            $charge = Charge::create( array(

                "amount" => $amount, // amount in cents, again
                "currency" => "usd",
                "source" => $token,
                //"application_fee_amount"=> $fee,
                "description" => "AIScribe credits purchase"
            ) );
            
//            "billing_details": {
//    "address": {
//      "city": null,
//      "country": null,
//      "line1": null,
//      "line2": null,
//      "postal_code": null,
//      "state": null
//    },
//    "email": null,
//    "name": "Randall",
//    "phone": null
//  },
                        
            //echo "id: {$charge->id}<br>";
            
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
            
            
            //insert into charges
            
            $insertSQL = sprintf("INSERT INTO transactions (transactioncode, userid,amount,datecreated) VALUES (%s, %s, %s, %s)",
                    GetSQLValueString(mysql_real_escape_string($transactioncode), "text"),
                    GetSQLValueString(mysql_real_escape_string($_POST[ 'uid' ]), "int"),
                    GetSQLValueString(mysql_real_escape_string($_POST[ 'credits' ]), "text"),
                    GetSQLValueString(mysql_real_escape_string($date), "date"));

            mysql_select_db($database_transcribe, $transcribe);
            $Result2 = mysql_query($insertSQL, $transcribe) or die(mysql_error());	
            
            //return;

            $updateGoTo = "my-account.php";
            if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
                $updateGoTo .= ( strpos( $updateGoTo, '?' ) ) ? "&" : "?";
                $updateGoTo .= $_SERVER[ 'QUERY_STRING' ];
            }
            header( sprintf( "Location: %s", $updateGoTo ) );

        } catch ( Error\ Card $e ) {

            //echo "error/card";

            //echo "error: " . $e;

            $status = "Payment error: " . $e;

            //echo $status;

        } catch ( Error\ Card $e ) {

            $status = "Payment declined: " . $e;

            //echo $status;
        }
    } else {
        
        $status = "Missing params";

        //echo $status;
    }
}
?>