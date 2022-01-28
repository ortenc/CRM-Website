<?php
$a = array("1","2","3","4","5");
foreach ($a as $b){
    echo "$b ";
}
$value = '$';
array_splice($a,3,0, $value);
foreach ($a as $b){
    echo "\n $b ";
}
echo "\n";
?>