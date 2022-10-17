<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function encodeDate($getDate){
    $tab=explode("/", $getDate);
    $r=$tab[2].$tab[1].$tab[0];
    return $r;
}
?>
