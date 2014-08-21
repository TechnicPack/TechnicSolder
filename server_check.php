<?php
//Check PHP Version
if(phpversion() >= '5.3.0'){
  $phpversion = '<font color="green">Good</font>';
}elseif(phpversion() <= '5.3.0'){
  $phpversion = '<font color="red">To Old</font>';
 }

 //Check for MCrypt
 if (extension_loaded('mcrypt')) {
    $mcrypt = '<font color="green">Installed</font>';
}elseif(!extension_loaded('mcrypt')){
  $mcrypt = '<font color="red">Not Installed</font>';
}

//Check for Curl
if (extension_loaded('curl')) {
   $curl = '<font color="green">Installed</font>';
}elseif(!extension_loaded('curl')){
 $curl = '<font color="red">Not Installed</font>';
}

//Check for GD
if (extension_loaded('gd')) {
   $gd = '<font color="green">Installed</font>';
}elseif(!extension_loaded('gd')){
 $gd = '<font color="red">Not Installed</font>';
}

?>

<html>
<center>
  <h3>PHP Version Is: <?php echo($phpversion); ?></h3><br>
  <h3>MCrypt Is: <?php echo($mcrypt); ?></h3><br>
  <h3>PHP Curl Is: <?php echo($curl); ?></h3><br>
  <h3>PHP GD Is: <?php echo($gd); ?></h3><br>
