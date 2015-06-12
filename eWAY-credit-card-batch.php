#!/usr/bin/php
<?php

/*
 * OVERVIEW
 *
 * Read the lookup file as a CSV
 *  Lines with two columns are interpreted as Variable/Value pairs
 *  Lines with three columns are interpreted as Variable/Key/Value pairs
 *
 * Read the input file as a CSV
 *  The input is  Amount, City, Description, CiviCRM Id, Card Holder, Card Number, Month, Year
 *  Ignore lines that don't start with a number
 *  Insert a header line in the CiviCRM output but not the eWAY output
 *  Repeat for each line...
 *   Get Id
 *   Get Date from today's date, 
 *   Get Amount
 *   Assume Currency is 'AUD'
 *   Get Source from Description
 *   Assume Status is 'Completed'
 *   Assume Financial Type is 'Donation'
 *   Assume Payment Instrument is 'Credit Card'
 *   Assume Pledge is 'No'
 *   Assume Suppress Xero Invoice is 'No'
 *   Put Date, Id, Amount, Currency, City, Source, Status, Financial Type, Payment Instrument, Pledge, suppress Xero invoice => CiviCRM output
 *   Put Amount*100,N/A,N/A,,N/A,N/A,City,Source,Card Holder,Card Number,Month,Year,N/A,N/A,N/A,N/A => eWAY output
 *  Continue until the end-of-file
 * Exit
 */

$input = fopen($argv[1], 'r');
$lookup = fopen($argv[2], 'r');
$civicrm = fopen($argv[3], 'w');
$eway = fopen($argv[4], 'w');

if ($input === FALSE || $lookup === FALSE || $civicrm === FALSE || $eway === FALSE) {
  return;
}

$variables = array();
while ( ($columns = fgetcsv($lookup)) !== FALSE ) {
  if (empty($columns[0]) || empty($columns[1])) {
    continue;
  } elseif (empty($columns[2])) {
    $variables[$columns[0]] = $columns[1];
  } else {
    if (!isset($variables[$columns[0]])) {
      $variables[$columns[0]] = array();
    }
    $variables[$columns[0]][$columns[1]] = $columns[2];
  }
}

fputcsv($civicrm, array('Date', 'Contact Id', 'Amount', 'Currency', 'City', 'Source', 'Status', 'Financial Type', 'Instrument', 'Pledge', 'Suppress Xero Invoice'));

$lineNumber = 1;
while ( ($columns = fgetcsv($input)) !== FALSE ) {
  if (is_numeric($columns[0])) {
    $line = array();
    $line[] = date('Y-m-d');
    $line[] = $columns[3];
    $line[] = $columns[0];
    $line[] = $variables['Currency'];
    $line[] = $columns[1];
    $line[] = $columns[2];
    $line[] = $variables['Status'];
    $line[] = $variables['Financial Type'];
    $line[] = $variables['Instrument'];
    $line[] = $variables['Pledge'];
    $line[] = $variables['Suppress Xero invoice'];
    fputcsv($civicrm, $line);
    fputs($eway,
      round(100*$columns[0]) . ',N/A,N/A,,N/A,N/A,'
      . $columns[1] . ','
      . $columns[2] . ','
      . $columns[4] . ','
      . $columns[5] . ','
      . sprintf('%02d', $columns[6]) . ','
      . sprintf('%02d', $columns[7]) . ',N/A,N/A,N/A,N/A'
      . "\n");
  }
  $lineNumber++;
}
