<?php
require_once "libdb.php";
require_once "libfoodstuff.php";
require_once "libmenu.php";

$tmp = new Menu( "localhost", "root", "445080946309" );
//var_dump( $tmp->GetMenuList( 0, 0, 0, "0000" ) );
var_dump( $tmp->GetFoodstuffList( 0, 0, 0, "0000" ) );
?>