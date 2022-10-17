<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
date_default_timezone_set('Asia/Jakarta');
$date = date('n/j/Y H:i:s A');
//echo $date;
try {
$conn = new PDO('mssql:host=172.31.3.216;dbname=EFOSDNIA','sa','P@ssw0rD');
} catch (PDOException $e) {
echo "Error: " . $e->getMessage() . "\n";
}
if(!$conn) die('Could not connect to DB');  
$query="select npk, password, role_id from efos_m_user where user_id = '2120155'";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);
echo $row["npk"]."<br/>";
echo $row["password"]."<br/>";
echo $row["role_id"];
?>
