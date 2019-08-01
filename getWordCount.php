<?php

$debug = false;

$content = file_get_contents( "lorem.txt" );

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

echo $total;

?>