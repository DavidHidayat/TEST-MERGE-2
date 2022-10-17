<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
if(isset($_SESSION['sNPK']))
{       
    $sNPK       = $_SESSION['sNPK'];
    $sNama      = $_SESSION['sNama'];
    $sBunit     = $_SESSION['sBunit'];
    $sBuLogin   = $_SESSION['sBuLogin'];
    $sSeksi     = $_SESSION['sSeksi'];
    $sKdPlant   = $_SESSION['sKDPL'];
    $sNmPlant   = $_SESSION['sNMPL'];
    $sKdper     = $_SESSION['sKdper'];
    $sNmPer     = $_SESSION['sNmper'];
    $sInet      = $_SESSION['sinet'];
    $sNotes     = $_SESSION['snotes'];
    $sApproval  = $_SESSION['sapproval']; 
    $_SESSION['sMax'];
    
    if($sNPK != ''){
        if(isset($_GET['action'])){
            $action= $_GET['action'];
        }
        if($action=='count'){
            if(isset($_POST['maxAmount'])){
                $maxAmount = $_POST['maxAmount'];
                if($maxAmount > 0) {
                    $query = "select count(*) as COUNT_APP from EPS_M_PR_APPROVER where BU_CD = '". $sBuLogin."'";
                    $sql = $conn->query($query);
                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                    if($row['COUNT_APP'] == 0){
                        echo '{success: true, msg:'.json_encode(array('message'=>'NotDefine')).'}';
                    }else{
                        $rcd    = array();
                        $x      = '';
                        $count  = 0;
                        $flag   = '';
                        $y      = 0;
                        $query = "select 
                                    EPS_M_PR_APPROVER.APPROVER_NO
                                    ,EPS_M_PR_APPROVER.NPK
                                    ,EPS_M_LIMIT.LIMIT_AMOUNT
                                from 
                                    EPS_M_PR_APPROVER 
                                inner join
                                    EPS_M_LIMIT 
                                on
                                    EPS_M_PR_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID
                                where
                                    EPS_M_PR_APPROVER.BU_CD = '". $sBuLogin."'
                                    and EPS_M_LIMIT.CURRENCY_CD = 'IDR'
                                order by
                                    EPS_M_PR_APPROVER.APPROVER_NO";
                        $sql = $conn->query($query);
                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                            $y++;
                            if($maxAmount > $row['LIMIT_AMOUNT']){
                                if($x == $row['APPROVER_NO']){
                                    $count++;
                                }else{
                                    $arcd[] = $row['APPROVER_NO'];
                                    $x = $row['APPROVER_NO'];
                                    $count = 1;
                                }
                                array_push($rcd, $row);
                            }else{
                                array_push($rcd, $row);
                                $x = $row['APPROVER_NO'];
                                break;
                            }
                        }
                        $arcd[] = $x;
                        $query = "select max(APPROVER_NO) 
                                    as MAX_APP
                                from         
                                    EPS_M_PR_APPROVER
                                where     
                                    BU_CD = '". $sBuLogin."'";
                        $sql = $conn->query($query);
                        $row = $sql->fetch(PDO::FETCH_ASSOC);
                        echo '{success: true, maxApp: '.$row['MAX_APP'].', countApp: '.count($arcd).', rows:'.json_encode($rcd).', msg:'.json_encode(array('message'=>'Defined')).'}';
                    }
                }
            }
        }
        if($action=='view'){
            if(isset($_GET['appno'])){
                $appno=$_GET['appno'];
            }else{
                $appno=1;	
            }
            $rcd=array();
            $query ="select 
                        EPS_M_PR_APPROVER.APPROVER_NO
                        ,EPS_M_PR_APPROVER.NPK
                        ,EPS_M_EMPLOYEE.NAMA1
                        ,EPS_M_LIMIT.LIMIT_AMOUNT
                    from 
                        EPS_M_PR_APPROVER 
                    inner join
                        EPS_M_LIMIT 
                    on
                        EPS_M_PR_APPROVER.APPROVER_LEVEL=EPS_M_LIMIT.LEVEL_ID
                    inner join
                        EPS_M_EMPLOYEE
                    on 
                        EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                    where
                        EPS_M_PR_APPROVER.BU_CD = '". $sBuLogin."'
                        and EPS_M_PR_APPROVER.APPROVER_NO = '". $appno. "'
                        and EPS_M_LIMIT.CURRENCY_CD = 'IDR'
                    order by
                        EPS_M_PR_APPROVER.APPROVER_NO";
            $sql =$conn->query($query);
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                array_push($rcd, $row);
            }
            echo '{success: true, rows:'.json_encode($rcd).'}';
        }
    }else{
        echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
    }
}else{	
    echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
}
?>
