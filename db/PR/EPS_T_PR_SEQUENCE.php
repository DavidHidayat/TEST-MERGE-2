<?php
    if(isset($_GET['action'])){
        include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
        $userId     = $_GET['userId'];
        $sBunit     = $_GET['buLogin'];
        $newPrNo    = getPrNo($userId, trim($sBunit), $_GET['action']);
        if($_GET['action'] == "getCurrentPrNo")
        {
            echo $newPrNo;
        }
        else
        {
            echo '{success:true, msg:'.json_encode(array('newPrNo'=>$newPrNo)).'}';
        }
    }
    
    function getPrNo($userId, $sBunit, $step){
        include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
        
        // Get Month
        $currentMonth = (int)date(m);
        $query="select MONTH_CD from EPS_M_MONTH where MONTH_NAME='$currentMonth'";
        $sql = $conn->query($query);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            $monthCd=$row['MONTH_CD'];
        }
        // Get Year
        $currentYear = (int)date(Y);
        $query="select YEAR_CD from EPS_M_YEAR where YEAR_NAME='$currentYear'";
        $sql = $conn->query($query);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            $yearCd=$row['YEAR_CD'];
        }
        // Set current month, year
        $prCurrentDate=$monthCd.$yearCd;

        // Get Running No.
        $query ="select PR_RUNNING_NO, PR_RUNNING_DATE from EPS_T_PR_SEQUENCE where BU_CD='$sBunit'";
        $sql = $conn->query($query);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            $prRunNo=$row['PR_RUNNING_NO'];
            $prRunDate=$row['PR_RUNNING_DATE'];
        }

        if($step=='getPrNo' || $step == 'getCurrentPrNo'){
            if($prCurrentDate==trim($prRunDate)){
                $prRunNo=(int)$prRunNo;
            }else{
                $prRunDate=$prCurrentDate;
                $prRunNo=1;
                $query="update 
                            EPS_T_PR_SEQUENCE 
                        set 
                            PR_RUNNING_NO='$prRunNo'
                            ,PR_RUNNING_DATE='$prRunDate'
                            ,UPDATE_BY='$userId'
                            ,UPDATE_DATE=convert(VARCHAR(24), GETDATE(), 120)
                        where ltrim(BU_CD)='$sBunit'";
                $sql = $conn->query($query);
            }
        }else{
            if($step=='updatePrNo'){
                if($prCurrentDate==trim($prRunDate)){
                    $prRunNo=(int)$prRunNo + 1;
                }else{
                    $prRunDate=$prCurrentDate;
                    $prRunNo = 1;
                }
                $query="update 
                            EPS_T_PR_SEQUENCE 
                        set 
                            PR_RUNNING_NO='$prRunNo'
                            ,PR_RUNNING_DATE='$prRunDate'
                            ,UPDATE_BY='$userId'
                            ,UPDATE_DATE=convert(VARCHAR(24), GETDATE(), 120)
                        where 
                            ltrim(BU_CD)='$sBunit'";
                $sql = $conn->query($query);
            }
        }
        /** Set sequences */
        $company = substr($sBunit, 0, 1);
        //update 1 maret 2018 at Taci company code
        if($company == 'H' || $company == 'T'){
            $sequence= str_pad($prRunNo, 3, "0", STR_PAD_LEFT);
        }else{
            $sequence= str_pad($prRunNo, 4, "0", STR_PAD_LEFT);
        }
        /** Set PR No. **/
        $prNo=$sBunit.$prRunDate.$sequence;
        return $prNo;
    }
?>
