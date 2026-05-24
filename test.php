<?php
require 'app/Services/SimpleXLSX.php';
$xlsx = \Shuchkin\SimpleXLSX::parse('public/template/example/data_produk_dan_category.xlsx');
if ($xlsx) {
    foreach (array_slice($xlsx->rows(1), 5, 2) as $r) {
        print_r($r);
    }
} else {
    echo \Shuchkin\SimpleXLSX::parseError();
}
