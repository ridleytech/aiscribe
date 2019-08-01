<?php

$debug = false;

$filename = $_FILES['file']['name'];

if ( 0 < $_FILES['file']['error'] ) {

    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
}
else 
{
    if(move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $filename))
    {
        $content = file_get_contents( 'uploads/' . $filename );

        //var_dump($content);

        $len1 = strlen( $content );

        //$0.02 USD /THOUSAND CHAR
        //$0.10 USD /THOUSAND CHAR (custom)

        $chars = ceil( $len1 / 1000 ) * 1000;

        $custom = false;

        if ( $custom ) {
            $rate = 0.10;
        } else {
            $rate = 0.02;
        }

        $markup = .5;

        $cost = ( $chars / 1000 ) * $rate;
        $total = ( $cost * $markup ) + $cost;

        if ( $total < 1 ) {
            $total = 1;
        }

        if ( $debug ) {
            echo "chars: " . $len1;
            echo "<br>rounded chars: " . $chars;
            echo "<br>rate : $" . $rate;
            echo "<br>cost: $" . $cost;
            echo "<br>total : $" . $total;
        }

        //$status .= "not done";

        $myObj = new stdClass;
        //$myObj->status = $status;
        $myObj->len1 = $len1;
        $myObj->rate = $rate;
        $myObj->chars = $chars;
        $myObj->cost = $cost;
        //$myObj->total = "$" . number_format($total,2);
        //$myObj->total = floatval(number_format($total,2));
        $myObj->total = $total;

        if ( isset( $_POST[ 'mobile' ] ) ) {
            echo "{\"data\":";
            echo "{\"wordData\":";
            echo json_encode( $myObj );
            echo "}";
            echo "}";
        } else {
            echo $total;
        }

        
    }
    else
    {
        echo "file not uploaded";
    }
}

?>