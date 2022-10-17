<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";

$buCd       = $_SESSION['sBunit'];
$buLogin    = $_SESSION['sBuLogin'];
$userId     = trim($_SESSION['sUserId']);
$npk        = trim($_SESSION['sNPK']);
$limit      = $_REQUEST['limit'];
$page       = $_REQUEST['page'];
$start      = $_REQUEST['start'];
$next       = $limit*$page;

$query = "select count
            (*) 
          as 
            APPROVER_COUNT 
          from 
            EPS_M_PR_APPROVER 
          where 
            ltrim(NPK) = '$npk'";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);
$approverCount = $row['APPROVER_COUNT'];

$query = "select 
            USERID
          from
            EPS_M_USER
          where
            NPK = '$npk'";
$sql = $conn->query($query);
while($row2 = $sql->fetch(PDO::FETCH_ASSOC)){
    $userIdVal = $row2['USERID'];
}
$json = '['; // start the json array element
$json_names = array();

    $query2 = "select count
                (*) 
              as 
                PR_COUNT 
              from 
                EPS_T_PR_HEADER 
              left join
                EPS_M_EMPLOYEE 
              on 
                EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
              left join
                EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
              on
                EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE_2.NPK
              where 
                ltrim(EPS_T_PR_HEADER.APPROVER) = ltrim('$userId')
                and EPS_T_PR_HEADER.PR_STATUS = '1020'  ";

$wherePrHeader  = array();
$prDateVal      = stripslashes($_REQUEST['prDateVal']);
$prNoVal        = stripslashes($_REQUEST['prNoVal']);
$requesterVal   = stripslashes($_REQUEST['requesterVal']);
$approverVal    = stripslashes($_REQUEST['approverVal']);
$prStatusVal    = stripslashes($_REQUEST['prStatusVal']);
if($prDateVal){
    $wherePrHeader[] = "EPS_T_PR_HEADER.ISSUED_DATE = '".encodeDate($prDateVal)."'";
}
if($prNoVal){
    $wherePrHeader[] = "EPS_T_PR_HEADER.PR_NO = '".$prNoVal."'";
}
if($requesterVal){
    $wherePrHeader[] = "EPS_M_EMPLOYEE.NAMA1 LIKE '".$requesterVal."%'";
}
if($approverVal){
    $wherePrHeader[] = "EPS_M_EMPLOYEE_2.NAMA1 LIKE '".$approverVal."%'";
}
if($prStatusVal){
    $wherePrHeader[] = "EPS_T_PR_HEADER.PR_STATUS = '".$prStatusVal."'";
}
if(count($wherePrHeader)) {
     $query .= "and " . implode('and ', $wherePrHeader);
}
$sql2 = $conn->query($query2);
$row2 = $sql2->fetch(PDO::FETCH_ASSOC);
$prCount = $row2['PR_COUNT'];

if ($page != '1'){
    if($next > $prCount){
        $limit = $prCount -(($page-1) * $limit);
    }
}

    $query = "select * from 
                (select top $limit * from          
                    (select top $next 
                        EPS_T_PR_HEADER.PR_NO
                        ,EPS_T_PR_HEADER.BU_CD
                        ,EPS_T_PR_HEADER.ISSUED_DATE
                        ,EPS_M_APP_STATUS.APP_STATUS_NAME as PR_STATUS_NAME
                        ,EPS_T_PR_HEADER.REQUESTER
                        ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                        ,EPS_T_PR_HEADER.APPROVER
                        ,EPS_M_EMPLOYEE_2.NAMA1 as APPROVER_NAME
                        ,EPS_M_EMPLOYEE_3.NAMA1 as PROC_IN_CHARGE_NAME
                        ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 120) as PROC_ACCEPT_DATE
                    from 
                        EPS_T_PR_HEADER 
                    inner join
                        EPS_M_APP_STATUS
                    on
                        EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                    left join 
                        EPS_M_EMPLOYEE 
                    on 
                        EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
                    left join
                        EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                    on
                        EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE_2.NPK
                    left join
                        EPS_M_EMPLOYEE EPS_M_EMPLOYEE_3
                    on
                        EPS_T_PR_HEADER.PROC_IN_CHARGE = EPS_M_EMPLOYEE_3.NPK
                    where 
                        ltrim(EPS_T_PR_HEADER.APPROVER) = ltrim('$userId')
                        and EPS_T_PR_HEADER.PR_STATUS = '1020'  ";
    if(count($wherePrHeader)) {
        $query .= "and " . implode('and ', $wherePrHeader);
    }
    $query .= "group by 
                        EPS_T_PR_HEADER.PR_NO
                        ,EPS_T_PR_HEADER.BU_CD
                        ,EPS_T_PR_HEADER.ISSUED_DATE
                        ,EPS_M_APP_STATUS.APP_STATUS_NAME
                        ,EPS_T_PR_HEADER.REQUESTER
                        ,EPS_M_EMPLOYEE.NAMA1
                        ,EPS_T_PR_HEADER.APPROVER
                        ,EPS_M_EMPLOYEE_2.NAMA1
                        ,EPS_M_EMPLOYEE_3.NAMA1
                        ,EPS_T_PR_HEADER.PROC_ACCEPT_DATE
                    order by EPS_T_PR_HEADER.ISSUED_DATE desc, EPS_T_PR_HEADER.PR_NO desc) as T1
                order by ISSUED_DATE asc, PR_NO asc) as T2
              order by ISSUED_DATE desc, PR_NO";
$sql = $conn->query($query);
while($row = $sql->fetch(PDO::FETCH_ASSOC)){
    $prNo           = $row['PR_NO'];
    $prBuCd         = $row['BU_CD'];
    $issuedDate     = $row['ISSUED_DATE'];
    $prStatus       = $row['PR_STATUS_NAME'];
    $requester      = $row['REQUESTER'];
    $requesterName  = addslashes($row['REQUESTER_NAME']);
    $approver       = $row['APPROVER'];
    $approverName   = addslashes($row['APPROVER_NAME']);
    $procInCharge   = $row['PROC_IN_CHARGE_NAME'];
    $prcoAcceptDate = $row['PROC_ACCEPT_DATE'];

    $json_names[] = "{ PR_NO			: '$prNo'
                       ,BU_CD			: '$prBuCd'
                       ,ISSUED_DATE		: '$issuedDate'
                       ,PR_STATUS_NAME		: '$prStatus'
                       ,REQUESTER		: '$requester'
                       ,REQUESTER_NAME		: '$requesterName'
                       ,APPROVER		: '$approver'
                       ,APPROVER_NAME		: '$approverName'
                       ,PROC_IN_CHARGE_NAME	: '$procInCharge'
                       ,PROC_ACCEPT_DATE	: '$prcoAcceptDate'}";
}
$json .= implode(',', $json_names); // join the objects by commas;
$json .= ']'; // end the json array element
echo '{success: true, total:'.$prCount.',rows:'.$json.'}';
?>
