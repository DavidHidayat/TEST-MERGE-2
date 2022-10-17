<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
if(isset($_SESSION['sUserId']))
{
    $sUserId    = $_SESSION['sUserId'];
    
    if($sUserId != '')
    {   
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
        
        $action         = strtoupper(trim($_GET['action']));
        $itemCdPrm      = stripslashes(strtoupper(trim($_GET['itemCdPrm'])));
        $itemCdPrm      = str_replace("'", "''", $itemCdPrm);
        $itemCdPrm      = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemCdPrm);
        $itemCdPrm      = preg_replace('/\s+/', ' ',$itemCdPrm);
        
        $itemNamePrm    = stripslashes(strtoupper(trim($_GET['itemNamePrm'])));
        $itemNamePrm    = str_replace("'", "''", $itemNamePrm);
        $itemNamePrm    = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemNamePrm);
        $itemNamePrm    = preg_replace('/\s+/', ' ',$itemNamePrm);
        
        $itemGroupCdPrm = stripslashes(strtoupper(trim($_GET['itemGroupCdPrm'])));
        $itemGroupCdPrm = str_replace("'", "''", $itemGroupCdPrm);
        $itemGroupCdPrm = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemGroupCdPrm);
        $itemGroupCdPrm = preg_replace('/\s+/', ' ',$itemGroupCdPrm);
        
        $objectAccountPrm    = stripslashes(strtoupper(trim($_GET['objectAccountPrm'])));
        $objectAccountPrm    = str_replace("'", "''", $objectAccountPrm);
        $objectAccountPrm    = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $objectAccountPrm);
        $objectAccountPrm    = preg_replace('/\s+/', ' ',$objectAccountPrm);
        
       // echo " Keluar datanya ga yaa ?  $objectAccountPrm";
        
        
        $activeFlagPrm  = stripslashes(strtoupper(trim($_GET['activeFlagPrm'])));
        /**
         * SELECT EPS_M_ITEM
         */
        $query_select_m_item = "select
                                    ITEM_CD
                                from
                                    EPS_M_ITEM
                                where
                                    ITEM_CD = '$itemCdPrm' ";
        $sql_select_m_item = $conn->query($query_select_m_item);
        $row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC);
           
        if($action == 'ADD')
        {
            if($itemCdPrm != '' && $itemNamePrm != '' && $itemGroupCdPrm != '' && !$row_select_m_item)
            {
                /**
                 * INSERT EPS_M_ITEM
                 */
                $query_insert_m_item = "insert into
                                            EPS_M_ITEM
                                            (
                                                ITEM_CD
                                                ,ITEM_NAME
                                                ,ITEM_GROUP_CD
                                                ,OBJECT_ACCOUNT_CD
                                                ,ACTIVE_FLAG
                                                ,CREATE_DATE
                                                ,CREATE_BY
                                                ,UPDATE_DATE
                                                ,UPDATE_BY
                                                
                                            )
                                        values
                                            (
                                                '$itemCdPrm'
                                                ,'$itemNamePrm'
                                                ,'$itemGroupCdPrm'
                                                ,'$objectAccountPrm'
                                                ,'A'
                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                ,'$sUserId'
                                                ,convert(VARCHAR(24), GETDATE(), 120)
                                                ,'$sUserId'
                                                
                                            )";
                $conn->query($query_insert_m_item);
                $msg = "Success";
            }
            else if($itemCdPrm == '' || $itemNamePrm == '' || $itemGroupCdPrm == '')
            {
                $msg = "Mandatory_1";
            }
            else if($row_select_m_item)
            {
                $msg = "Duplicate";
            }
            else
            {
                $msg = "Undefined";
            }
        }
        
        if($action == 'EDIT')
        {
            if($itemCdPrm != '' && $itemNamePrm != '' && $itemGroupCdPrm != '' && $row_select_m_item)
            {
                /**
                 * CHECK ITEM STATUS by ITEM CD
                 */
                $query_select_count_t_transfer = "select 
                                                    count(*) as COUNT_EXIST_ITEM
                                                  from 
                                                    EPS_T_TRANSFER
                                                  where 
                                                    (ITEM_STATUS <> '1320')
                                                    and (ITEM_STATUS <> '1310')
                                                    and (ITEM_STATUS <> '1150')
                                                    and (ITEM_STATUS <> '1140')
                                                    and (NEW_ITEM_CD = '$itemCdPrm')";
                $sql_select_count_t_transfer = $conn->query($query_select_count_t_transfer);
                $row_select_count_t_transfer = $sql_select_count_t_transfer->fetch(PDO::FETCH_ASSOC);
                $countExistItem = $row_select_count_t_transfer['COUNT_EXIST_ITEM'];
                
                if($countExistItem == 0)
                {
                   /**
                    * UPDATE EPS_M_ITEM
                    */
                    $query_update_m_item = "update
                                                EPS_M_ITEM
                                            set
                                                ITEM_NAME = '$itemNamePrm'
                                                ,ITEM_GROUP_CD = '$itemGroupCdPrm'
                                                ,OBJECT_ACCOUNT_CD = '$objectAccountPrm'
                                                ,ACTIVE_FLAG = '$activeFlagPrm'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                            where
                                                ITEM_CD = '$itemCdPrm'";
                    $conn->query($query_update_m_item);
                    $msg = "Success";
                }
                else
                {
                    $msg = "NotAllowEdit";
                }
            }
            else if($itemCdPrm == '' || $itemNamePrm == '' || $itemGroupCdPrm == '' || $activeFlagPrm == '')
            {
                $msg = "Mandatory_1";
            }
            else if(!$row_select_m_item)
            {
                $msg = "NotExist";
            }
            else
            {
                $msg = "Undefined";
            }
        }
    }
    else
    {	
        $msg = "SessionExpired";
    }
    
}
else
{	
    $msg = "SessionExpired";
}
echo $msg;
?>
