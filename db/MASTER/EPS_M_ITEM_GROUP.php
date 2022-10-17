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
        
        $action             = strtoupper(trim($_GET['action']));
       
        $itemGroupCdPrm     = stripslashes(strtoupper(trim($_GET['itemGroupCdPrm'])));
        $itemGroupCdPrm     = str_replace("'", "''", $itemGroupCdPrm);
        $itemGroupCdPrm     = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemGroupCdPrm);
        $itemGroupCdPrm     = preg_replace('/\s+/', ' ',$itemGroupCdPrm);
        
        $itemGroupNamePrm   = stripslashes(strtoupper(trim($_GET['itemGroupNamePrm'])));
        $itemGroupNamePrm   = str_replace("'", "''", $itemGroupNamePrm);
        $itemGroupNamePrm   = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemGroupNamePrm);
        $itemGroupNamePrm   = preg_replace('/\s+/', ' ',$itemGroupNamePrm);
        
        //TEST TAMBAH FIELD
        $itemGroupTestPrm   = stripslashes(strtoupper(trim($_GET['itemGroupTestPrm'])));
        $itemGroupTestPrm   = str_replace("'", "''", $itemGroupTestPrm);
        $itemGroupTestPrm   = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemGroupTestPrm);
        $itemGroupTestPrm   = preg_replace('/\s+/', ' ',$itemGroupTestPrm);
        
        /**
         * SELECT EPS_M_ITEM_GROUP
         */
        $query_select_m_item_group = "select
                                        ITEM_GROUP_CD
                                      from
                                        EPS_M_ITEM_GROUP
                                      where
                                        ITEM_GROUP_CD = '$itemGroupCdPrm' ";
        $sql_select_m_item_group = $conn->query($query_select_m_item_group);
        $row_select_m_item_group = $sql_select_m_item_group->fetch(PDO::FETCH_ASSOC);
           
        if($action == 'ADD')
        {
            if($itemGroupCdPrm != '' && $itemGroupNamePrm != '' && !$row_select_m_item_group)
            {
                /**
                 * INSERT EPS_M_ITEM_GROUP
                 */
                $query_insert_m_item_group = "insert into
                                                EPS_M_ITEM_GROUP
                                                (
                                                    ITEM_GROUP_CD
                                                    ,ITEM_GROUP_NAME
                                                    ,CREATE_DATE
                                                    ,CREATE_BY
                                                    ,UPDATE_DATE
                                                    ,UPDATE_BY
                                                    ,TEST
                                                )
                                              values
                                                (
                                                    '$itemGroupCdPrm'
                                                    ,'$itemGroupNamePrm'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                    ,'$itemGroupTestPrm'
                                                )";
                $conn->query($query_insert_m_item_group);
                $msg = "Success";
            }
            else if($itemGroupCdPrm == '' || $itemGroupNamePrm == '')
            {
                $msg = "Mandatory_1";
            }
            else if($row_select_m_item_group)
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
            if($itemGroupCdPrm != '' && $itemGroupNamePrm != '' && $row_select_m_item_group)
            {
                /**
                 * UPDATE EPS_M_ITEM_GROUP
                 */
                $query_update_m_item_group = "update
                                                EPS_M_ITEM_GROUP
                                              set
                                                ITEM_GROUP_NAME = '$itemGroupNamePrm'
                                                ,TEST = '$itemGroupTestPrm'
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                              where
                                                ITEM_GROUP_CD = '$itemGroupCdPrm'";
                $conn->query($query_update_m_item_group);
                $msg = "Success";
            }
            else if($itemGroupCdPrm == '' || $itemGroupNamePrm == '')
            {
                $msg = "Mandatory_1";
            }
            else if(!$row_select_m_item_group)
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
