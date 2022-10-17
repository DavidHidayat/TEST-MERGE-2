<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/Supplier_Email.php";

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
        
        $action         	= strtoupper(trim($_GET['action']));
        $supplierCdPrm      = stripslashes(strtoupper(trim($_GET['supplierCdPrm'])));
        $supplierCdPrm      = str_replace("'", "''", $supplierCdPrm);
        $supplierCdPrm      = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $supplierCdPrm);
        $supplierCdPrm      = preg_replace('/\s+/', ' ',$supplierCdPrm);
        
        $supplierNamePrm    = stripslashes(strtoupper(trim($_GET['supplierNamePrm'])));
        $supplierNamePrm    = str_replace("'", "''", $supplierNamePrm);
        $supplierNamePrm    = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $supplierNamePrm);
        $supplierNamePrm    = preg_replace('/\s+/', ' ',$supplierNamePrm);
        
        $supplierNumberPrm  = stripslashes(strtoupper(trim($_GET['supplierNumberPrm'])));
        $currencyCdPrm      = stripslashes(strtoupper(trim($_GET['currencyCdPrm'])));
        $vatPrm             = stripslashes(strtoupper(trim($_GET['vatPrm'])));
        $npwpPrm            = stripslashes(strtoupper(trim($_GET['npwpPrm'])));
        $contactPrm         = stripslashes(strtoupper(trim($_GET['contactPrm'])));
        $phonePrm           = stripslashes(strtoupper(trim($_GET['phonePrm'])));
        $outstandingFlagPrm = stripslashes(strtoupper(trim($_GET['outstandingFlagPrm'])));
        $activeFlagPrm      = stripslashes(strtoupper(trim($_GET['activeFlagPrm'])));
        $addressPrm         = stripslashes(strtoupper(trim($_GET['addressPrm'])));
        $emailPrm           = stripslashes(strtoupper(trim($_GET['emailPrm'])));
        $emailCcPrm         = stripslashes(strtoupper(trim($_GET['emailCcPrm'])));
        $emailCcUpPrm       = stripslashes(strtoupper(trim($_GET['emailCcUpPrm'])));
        
        /**
         * SELECT EPS_M_SUPPLIER
         */
        $query_select_m_supplier = "select
                                        SUPPLIER_CD
                                        ,SUPPLIER_NUMBER
                                        ,ACTIVE_FLAG
                                    from
                                        EPS_M_SUPPLIER
                                    where
                                        SUPPLIER_CD = '$supplierCdPrm'
                                       ".
//" or SUPPLIER_NUMBER = '$supplierNumberPrm'"
                 " ";
        $sql_select_m_supplier = $conn->query($query_select_m_supplier);
        $row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC);
        
		if($action == 'ADD')
        {
            if($supplierCdPrm != "" && $supplierNamePrm != "" && $currencyCdPrm != "" && $vatPrm != "" && $emailPrm != ""
                      && !$row_select_m_supplier)
            {
               /**
                * CREATE EPS_M_SUPPLIER
                */
                $query_insert_m_supplier = "insert into
                                                EPS_M_SUPPLIER
                                                (
                                                    SUPPLIER_CD
                                                    ,SUPPLIER_NAME
                                                    ,SUPPLIER_NUMBER
                                                    ,CURRENCY_CD
                                                    ,VAT
                                                    ,NPWP
                                                    ,CONTACT
                                                    ,EMAIL
                                                    ,EMAIL_CC
                                                    ,EMAIL_CC_UP
                                                    ,PHONE
                                                    ,ADDRESS
                                                    ,OUTSTANDING_FLAG
                                                    ,ACTIVE_FLAG
                                                    ,CREATE_DATE
                                                    ,CREATE_BY
                                                    ,UPDATE_DATE
                                                    ,UPDATE_BY
                                                )
                                             values
                                                (
                                                    '$supplierCdPrm'
                                                    ,'$supplierNamePrm'
                                                    ,'$supplierNumberPrm'
                                                    ,'$currencyCdPrm'
                                                    ,'$vatPrm'
                                                    ,'$npwpPrm'
                                                    ,'$contactPrm'
                                                    ,'$emailPrm'
                                                    ,'$emailCcPrm'
                                                    ,'$emailCcUpPrm'
                                                    ,'$phonePrm'
                                                    ,'$addressPrm'
                                                    ,'$outstandingFlagPrm'
                                                    ,'$activeFlagPrm'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                    ,'$sUserId'
                                                )";
                $conn->query($query_insert_m_supplier);
                $msg = "Success";
               // sendMailSupplier($supplierCdPrm, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailMessage);
            }
            else if($supplierCdPrm == "" || $supplierNamePrm == "" || $currencyCdPrm == "" || $vatPrm == "")
            {
                $msg = "Mandatory_1";
            }
            else if($row_select_m_supplier)
            {
                $msg = "Duplicate";
            }
            else
            {
                $msg = "Undefined ";
            }
        }
		
        if($action == 'EDIT')
        {
            if($row_select_m_supplier)
            { 
				$activeFlagMst = $row_select_m_supplier['ACTIVE_FLAG'];
                
				/**
                 * SELECT EPS_T_PO_HEADER
                 */
                $query_select_t_po_header = "select
                                                PO_NO
                                                ,PO_STATUS
                                             from
                                                EPS_T_PO_HEADER
                                             where
                                                PO_STATUS in ('1210','1220','1230','1250','1280')
                                                and SUPPLIER_CD = '$supplierCdPrm' ";
                $sql_select_t_po_header = $conn->query($query_select_t_po_header);
                $row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC);
				
				/**
                * SELECT EPS_M_ITEM_PRICE
                */
                $query_select_m_item_price = "select
                                                SUPPLIER_CD
                                            from
                                                EPS_M_ITEM_PRICE
                                            left join 
                                                EPS_M_ITEM
                                            on
                                                EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD
                                            where
                                                EPS_M_ITEM_PRICE.SUPPLIER_CD = '$supplierCdPrm'
                                                and EPS_M_ITEM.ACTIVE_FLAG = 'A' ";
                $sql_select_m_item_price = $conn->query($query_select_m_item_price);
                $row_select_m_item_price = $sql_select_m_item_price->fetch(PDO::FETCH_ASSOC);
				
				 if($supplierNumberPrm != "")
                {
                    $query_select_m_supplier_by_number = "select
                                                            SUPPLIER_CD
                                                            ,SUPPLIER_NUMBER
                                                            ,ACTIVE_FLAG
                                                        from
                                                            EPS_M_SUPPLIER
                                                        where
                                                            SUPPLIER_NUMBER = '$supplierNumberPrm' ";
                    $sql_select_m_supplier_by_number = $conn->query($query_select_m_supplier_by_number);
                    $row_select_m_supplier_by_number = $sql_select_m_supplier_by_number->fetch(PDO::FETCH_ASSOC);
                    $supplierCdCheck = $row_select_m_supplier_by_number['SUPPLIER_CD'];
                }
				
				if($activeFlagMst != $activeFlagPrm && $activeFlagPrm == 'D' && $row_select_t_po_header && $row_select_m_item_price)
                {
                   $msg = "NotAllowEdit";
                }
                else if($row_select_m_supplier_by_number && $supplierNumberPrm != "" && $supplierCdPrm != $supplierCdCheck)
                {
                    $msg = "Duplicate";
                }
                else
                {
                   /**
                    * UPDATE EPS_M_SUPPLIER
                    */
                    $query_update_m_supplier = "update
                                                    EPS_M_SUPPLIER
                                                set
                                                    NPWP = '$npwpPrm'
													,SUPPLIER_NAME = '$supplierNamePrm'
                                                    ,SUPPLIER_NUMBER = '$supplierNumberPrm'
                                                    ,CONTACT = '$contactPrm'
                                                    ,PHONE = '$phonePrm'
                                                    ,OUTSTANDING_FLAG = '$outstandingFlagPrm'
                                                    ,ACTIVE_FLAG = '$activeFlagPrm'
                                                    ,ADDRESS = '$addressPrm'
                                                    ,EMAIL = '$emailPrm'
                                                    ,EMAIL_CC = '$emailCcPrm'
                                                    ,EMAIL_CC_UP = '$emailCcUpPrm'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                    ,VAT = '$vatPrm'
                                                where
                                                    SUPPLIER_CD = '$supplierCdPrm'";
                    $conn->query($query_update_m_supplier);
                    $msg = "Success";
                }
            }
            else if(!$row_select_m_supplier)
            {
                $msg = "NotExist";
            }
            else
            {
                $msg = "Undefined";
            }
        }
		if($action == "SEARCHAUTOSUPPLIER")
        {
            $whereSupplier  = array();
            $supplierNameCriteria    = trim(stripslashes($_REQUEST['term']));
            $whereSupplier[] = "EPS_M_SUPPLIER.ACTIVE_FLAG = 'A'";
            if($supplierNameCriteria)
            {
                $whereSupplier[] = "EPS_M_SUPPLIER.SUPPLIER_NAME like '%".$supplierNameCriteria."%'";
            }
            $query_select_m_supplier = "select     
                                            EPS_M_SUPPLIER.SUPPLIER_CD
                                            ,EPS_M_SUPPLIER.SUPPLIER_NAME
                                            ,EPS_M_SUPPLIER.CURRENCY_CD
                                        from         
                                            EPS_M_SUPPLIER ";
            if(count($whereSupplier)) 
            {
                $query_select_m_supplier .= "where " . implode('and ', $whereSupplier);
            }
            $sql_select_m_supplier = $conn->query($query_select_m_supplier);
            while($row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC))
            {
                $itemCd = $row_select_m_supplier['SUPPLIER_CD'];
                $supplierName = $row_select_m_supplier['SUPPLIER_NAME'];
                $result[] = array(
                        'id'=> $itemCd
                        ,'value'=> $supplierName
                        ,'supplierCd'=>$itemCd
                        ,'supplierName'=>$supplierName
                    );
                
            }
            $msg = json_encode($result);
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
