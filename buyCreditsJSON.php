<?php

namespace Stripe;

require_once( 'Connections/transcribe.php' );
require_once( 'stripe/stripe-php-3.12.0/vendor/autoload.php' );
require_once( 'stripe/stripe-php-3.12.0/lib/Stripe.php' );
include( "functions.php" );

$colname_rsUserInfo = "-1";

if ( isset( $_POST[ 'uid' ] ) ) {
    $colname_rsUserInfo = $_POST[ 'uid' ];
} else if ( isset( $_SESSION[ 'uid' ] ) ) {
    $colname_rsUserInfo = $_SESSION[ 'uid' ];
}

mysql_select_db( $database_transcribe, $transcribe );
$query_rsUserInfo = sprintf( "SELECT * FROM users WHERE userid = %s", GetSQLValueString( $colname_rsUserInfo, "int" ) );
$rsUserInfo = mysql_query( $query_rsUserInfo, $transcribe )or die( mysql_error() );
$row_rsUserInfo = mysql_fetch_assoc( $rsUserInfo );
$totalRows_rsUserInfo = mysql_num_rows( $rsUserInfo );

$credits = $row_rsUserInfo[ 'credits' ];


$transPercentage = .032;
$transCost = .30;

$object = new\ stdClass;

if ( isset( $_POST[ "uid" ] ) && isset( $_POST[ "credits" ] ) ) {

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
    } else if ( strlen( $_POST[ 'exp' ] ) < 1) {
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
                $exp = explode( "/", $_POST[ 'exp' ] );

                if ( isset( $_POST[ 'stripe' ] ) ) {

                    try {

                        $token = Token::create(
                            array(
                                "card" => array(
                                    "name" => $_POST[ 'firstname' ] . " " . $_POST[ 'lastname' ],
                                    "number" => $_POST[ 'card_number' ],
                                    "exp_month" => $exp[0],
                                    "exp_year" => $exp[1],
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

                            $object->status = $status;
                            $object->credits = number_format( $credits2, 2 );

                            echo "{\"data\":";
                            echo "{\"paymentData\":";
                            echo json_encode( $object );
                            echo "}";
                            echo "}";

                        } catch ( Error\ Card $e ) {

                            $body = $e->getJsonBody();

                            //var_dump($body);

                            $err = $body[ 'error' ];
                            $status = $err[ 'message' ];
                            
                            $object->status = $status;
                            
                            echo "{\"data\":";
                            echo "{\"paymentData\":";
                            echo json_encode( $object );
                            echo "}";
                            echo "}";
                        }

                    } catch ( Error\ Card $e ) {

                        $body = $e->getJsonBody();

                        //var_dump($body);

                        $err = $body[ 'error' ];
                        $status = $err[ 'message' ];
                        
                        $object->status = $status;
                            
                        echo "{\"data\":";
                        echo "{\"paymentData\":";
                        echo json_encode( $object );
                        echo "}";
                        echo "}";
                    }
                }

            } else {

                $status = "Missing params";
            }
        }
    }
    else
    {
        $object->status = $status;
        
        echo "{\"data\":";
        echo "{\"paymentData\":";
        echo json_encode( $object );
        echo "}";
        echo "}";
    }
}



?>