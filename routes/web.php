<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $url    = 'https://akiab.az/';
    $base   = 'https://akiab.az';

// Pull in the external HTML contents
    $contents = file_get_contents( $url );

// Use Regular Expressions to match all <img src="???" />
    preg_match_all( '/<img[^>]*src=[\"|\'](.*)[\"|\']/Ui', $contents, $out, PREG_PATTERN_ORDER);

    foreach ( $out[1] as $k=>$v ){ // Step through all SRC's

        // Prepend the URL with the $base URL (if needed)
        if ( strpos( $v, 'http://' ) !== true ) $v = $base . $v;

        // Output a link to the URL
        echo '<a href="' . $v . '">' . $v . '</a><br/>';
    }
});

