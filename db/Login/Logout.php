<?php session_start();
if(isset($_SESSION['sUserId']))
{
    unset($_SESSION['sNPK']);
    unset($_SESSION['sNama']);
    unset($_SESSION['sBunit']);
    unset($_SESSION['sSeksi']);
    unset($_SESSION['sKdper']);
    unset($_SESSION['sNmper']);
    unset($_SESSION['sKDPL']);
    unset($_SESSION['sNMPL']);
    unset($_SESSION['sRoleId']);
    unset($_SESSION['sinet']);
    unset($_SESSION['snotes']);
    unset($_SESSION['sUserId']);
    unset($_SESSION['sBuLogin']);
    unset($_SESSION['sUserType']);
    //session_destroy();
?>
    <script language="javascript"> document.location="../../index.php"; </script> 
<?
}
else
{
?>
    <script language="javascript"> document.location="../../ecom/WCOM011.php"; </script> 
<?
}
?>