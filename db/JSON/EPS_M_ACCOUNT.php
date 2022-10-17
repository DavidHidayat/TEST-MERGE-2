<?php
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";

//if(isset($_SESSION['sUserId']))
//{
    //$sUserId    = $_SESSION['sUserId'];
    
    //if($sUserId != '')
    //{   
        $sNPK       = $_SESSION['sNPK'];
        $sNama      = $_SESSION['sNama'];
        $sBunit     = $_SESSION['sBunit'];
        $sSeksi     = $_SESSION['sSeksi'];
        $sKdper     = $_SESSION['sKdper'];
        $sNmPer     = $_SESSION['sNmper'];
        $sKdPlant   = $_SESSION['sKDPL'];
        $sNmPlant   = $_SESSION['sNMPL'];
        $sRoleId    = $_SESSION['sRoleId'];
        $sInet      = $_SESSION['sinet'];
        $sNotes     = $_SESSION['snotes'];
        $sBuLogin   = $_SESSION['sBuLogin'];
        $sUserType  = $_SESSION['sUserType']; 
        
        $action = $_GET['action'];

        if($action == "searchByUserType")
        {
            $companyCd = $_GET['companyCd'];
            $itemTypeCd = $_GET['itemTypeCd'];
            if($itemTypeCd == "6")
            {
                $itemTypeCd = "3";
            }
            $query_select_eps_m_account = "select
                                            CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                                            ,ACCOUNT_CD
                                            ,ACCOUNT_NAME
                                            ,(ACCOUNT_NO + ' - '+ACCOUNT_CD + ' - ' + ACCOUNT_NAME)as ACCOUNT_CD_NAME 
                                           from
                                            EPS_M_ACCOUNT
                                           where
                                            ITEM_TYPE_CD = '$itemTypeCd'
                                            and ACTIVE_FLAG = 'A'";
            if($companyCd == "M")
            {
                $query_select_eps_m_account .= "and len(ACCOUNT_NO) = '4'";
            }
            else
            {
                $query_select_eps_m_account .= "and len(ACCOUNT_NO) != '4'";
            }
            $query_select_eps_m_account .= " order by
                                                ACCOUNT_NO";
        }
        
        if($action == "searchByInvDmia")
        {
            $query_select_eps_m_account = "select
                                            CONVERT(int, ACCOUNT_NO) as ACCOUNT_NO
                                            ,ACCOUNT_CD
                                            ,ACCOUNT_NAME
                                            ,(ACCOUNT_NO + ' - '+ACCOUNT_CD + ' - ' + ACCOUNT_NAME)as ACCOUNT_CD_NAME 
                                           from
                                            EPS_M_ACCOUNT
                                           where
                                            ITEM_TYPE_CD = '2'
                                            and ACCOUNT_CD = '21711'";
        }
        $sql_select_eps_m_account = $conn->query($query_select_eps_m_account);
        $row_select_eps_m_account = $sql_select_eps_m_account->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($row_select_eps_m_account);
   // }
//}
?>
