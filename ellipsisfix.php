<?php

// …
foreach (glob("data/media/*.csv") as $filename) {
    $file = file_get_contents($filename);
    if(strpos($file, "\u2026") !== FALSE) {
    	echo "YES ";
    }



    echo "$filename size " . filesize($filename) . "\n";
}

echo  json_decode('"\u2026"');


ea-php71 -r '$f = file_get_contents("data/media/c5ad409b7c389a24245b9b7daf5cc83793ad554c5ac78c2c6adc01523026988.csv"); var_dump(strpos(mb_convert_encoding($f, "UTF-8"), "\u2026"));'


ea-php71 -r '$f = file_get_contents("data/media/c5ad409b7c389a24245b9b7daf5cc83793ad554c5ac78c2c6adc01523026988.csv"); var_dump(strpos(iconv("ISO-8859-1", "UTF-8", $f), "\u2026"));'