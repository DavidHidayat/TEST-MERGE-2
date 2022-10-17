<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";

if(isset($_SESSION['sUserId']))
{    
    $sUserId    = $_SESSION['sUserId'];
    if(trim($sUserId) != '')
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
        $action     = $_GET['action'];
        $deviceId   = $_SERVER['REMOTE_ADDR'];
        
        $poNo               = trim($_GET['poNoPrm']);
        $roDetailArray      = $_GET['roDetailArray'];
        $newRoDetailArray   = explode(",", $roDetailArray);
        $countStatusOpen    = 0;
        
        /** 
         *  UPDATE EPS_T_PO_APPROVER TO NEW APPROVER
         **/
        for($x = 0; $x < count($newRoDetailArray); $x++){
            $roStatus       = trim(substr($newRoDetailArray[$x],8,4));
            $refTransferId  = substr($newRoDetailArray[$x],12);
            
            if($roStatus == '1310'){
                /**
                 * UPDATE EPS_T_PO_DETAIL (** Open Item)
                 */
                $query_update_eps_t_po_detail = "update 
                                                    EPS_T_PO_DETAIL
                                                set
                                                    RO_STATUS = '$roStatus'
                                                    ,UPDATE_BY = '$sUserId' 
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                where
                                                    PO_NO = '$poNo'
                                                    and REF_TRANSFER_ID = '$refTransferId'";
                $conn->query($query_update_eps_t_po_detail);
                
				/**
                 * SELECT EPS_T_TRANSFER
                 **/
                $query_select_t_transfer = "select
                                                NEW_QTY
                                                ,ACTUAL_QTY
                                            from
                                                EPS_T_TRANSFER
                                            where
                                                TRANSFER_ID = '$refTransferId'";
                $sql_select_t_transfer = $conn->query($query_select_t_transfer);
                $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
                $qtyTransfer = $row_select_t_transfer['NEW_QTY'];
                $actualQty = $row_select_t_transfer['ACTUAL_QTY'];
				
				/**
                 * SELECT EPS_T_PO_DETAIL
                 **/
                $query_select_t_po_detail = "select
                                                QTY
                                            from
                                                EPS_T_PO_DETAIL
                                            where
                                                PO_NO = '$poNo'
                                                and REF_TRANSFER_ID = '$refTransferId'";
                $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                $row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
                $poQty = $row_select_t_po_detail['QTY'];
				
				if($poQty == $qtyTransfer)
                {
					/**
					 * UPDATE EPS_T_TRANSFER (** Open Item)
					 */
					$query_update_eps_t_transfer = "update 
														EPS_T_TRANSFER
													set
														ITEM_STATUS = '$roStatus'
														,UPDATE_BY = '$sUserId' 
														,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
													where
														TRANSFER_ID = '$refTransferId'";
					$conn->query($query_update_eps_t_transfer);
				}
                
                /**
                 * DELETE EPS_T_RO_DETAIL
                 */
                $query_del_t_ro_detail = "delete 
                                          from
                                            EPS_T_RO_DETAIL
                                          where
                                            PO_NO = '$poNo'
                                            and REF_TRANSFER_ID = '$refTransferId'";
                $conn->query($query_del_t_ro_detail);
                
                $countStatusOpen++;
            }
            
            if($countStatusOpen > 0){
                /**
                 * UPDATE EPS_T_PO_HEADER
                 */
                $query_update_t_po_header = "update
                                                EPS_T_PO_HEADER
                                             set
                                                PO_STATUS = '1250'
                                                ,CLOSED_PO_MONTH = ''
                                                ,CLOSED_PO_DATE = NULL
                                                ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                             where
                                                PO_NO = '$poNo'";
                $conn->query($query_update_t_po_header);
            }
            $msg = 'Success';
        }
    }
    else
    {
        $msg = 'SessionExpired';
    }
    echo $msg;
}
else
{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../db/Login/Logout.php"; </script>
<?php 
}
?>
