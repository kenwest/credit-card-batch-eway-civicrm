#!/usr/bin/php
<?php

$input = fopen('/tmp/1-credit-card-batch.csv', 'w');
$columns = array('Amount','City', 'Description', 'CiviCRM Id', 'Card Holder', 'Card Number', 'MM', 'YY');
fputcsv($input, $columns);
$columns = array('20','', 'Test 1', '123', 'Fred', '4444333322221111', '02', '15');
fputcsv($input, $columns);
$columns = array(25.0,'Sydney', 'Test 2', 124, 'Wilma', 4444333322221111, 2, 15);
fputcsv($input, $columns);
fclose($input);

$lookup = fopen('/tmp/2-lookup.csv', 'w');
$columns = array('Lines with two columns are interpreted as Variable/Value pairs');
fputcsv($lookup, $columns);
$columns = array('Lines with three columns are interpreted as Variable/Key/Value pairs');
fputcsv($lookup, $columns);
$columns = array('Currency','AUD');
fputcsv($lookup, $columns);
$columns = array('Status','Completed');
fputcsv($lookup, $columns);
$columns = array('Financial Type','Donation');
fputcsv($lookup, $columns);
$columns = array('Instrument','Credit Card');
fputcsv($lookup, $columns);
$columns = array('Pledge','No');
fputcsv($lookup, $columns);
$columns = array('Suppress Xero invoice','No');
fputcsv($lookup, $columns);
fclose($lookup);

echo "To test, run ...\n";
echo "  ./eWAY-credit-card-batch.php /tmp/1-credit-card-batch.csv /tmp/2-lookup.csv /tmp/3-civicrm-import.csv /tmp/4-eway-import.csv\n";
echo "  more /tmp/3-civicrm-import.csv /tmp/4-eway-import.csv\n";
echo "    - line 1 should be ignored\n";
echo "    - line 2 uses text values for numeric fields and should process OK\n";
echo "    - line 3 uses number values for numeric fields and should process OK\n";
