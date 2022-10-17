<?php
session_start();
if(isset($_SESSION['sNPK']))
{       
    $sNPK=$_SESSION['sNPK'];
    $sNama=$_SESSION['sNama'];	
    $sBunit=$_SESSION['sBunit'];
    $sInet=$_SESSION['sinet'];
    $sINMAIL=$_SESSION['snotes'];
    $sApproval=$_SESSION['sapproval']; 
    $_SESSION['sMax'];
}else{	
    header("Location: ../../epr/WEPR001.php");
}

/** PR Special Approver */
$query="select 
            EPS_M_PR_SPECIAL_APPROVER.NPK
            ,EPS_M_EMPLOYEE.NAMA1 
        from 
            EPS_M_PR_SPECIAL_APPROVER
        inner join
            EPS_M_EMPLOYEE
        on 
            EPS_M_PR_SPECIAL_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
        where
            EPS_M_PR_SPECIAL_APPROVER.SPECIAL_APPROVER_CD = '001'";
$sql=$conn->query($query);
$row=$sql->fetch(PDO::FETCH_ASSOC);
if($row){
    $nameSpecial=$row['NAMA1'];
}
?>
