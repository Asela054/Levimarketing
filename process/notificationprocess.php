<?php
session_start();
if(!isset($_SESSION['userid'])){header('Location:../index.php');}
if($_SESSION['privatetype']==1){ 
    $newvalue = 2;
    $_SESSION['privatetype'] = $newvalue;
    {header("Location:../dashboard.php");}
}
else{
    $newvalue = 1;
    $_SESSION['privatetype'] = $newvalue;
    {header("Location:../dashboard.php");}
}
?>