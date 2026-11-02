<?php
session_start();
require_once 'inc/country-detection.php';

$country = getUserCountry();
$pricing = getPricingByCountry($country);

echo "Detected Country: " . $country . "<br>";
echo "Basic: " . $pricing['basic'] . "<br>";
echo "Premium: " . $pricing['premium'] . "<br>";
echo "Ultimate: " . $pricing['ultimate'] . "<br>";
?>