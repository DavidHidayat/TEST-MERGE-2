<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    
    if($sUserId != ''){
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

        if($action == 'EditItemPo'){
            $itemPo         = array();
            $indexArray     = trim($_GET['$seqItem']);

            $poNo           = strtoupper(trim($_GET['poNoPrm']));
            $refTransferId  = strtoupper(trim($_GET['refTransferIdPrm']));
            $itemCd         = strtoupper(trim($_GET['itemCdPrm']));
            $itemName       = strtoupper(trim($_GET['itemNamePrm']));
            $cip            = strtoupper(trim($_GET['cipPrm'])); 
            $qty            = strtoupper(trim($_GET['qtyPrm']));
            $itemPrice      = strtoupper(trim($_GET['itemPricePrm']));
            $amount         = strtoupper(trim($_GET['amountPrm']));
            $supplierCd     = strtoupper(trim($_GET['supplierCdPrm']));
            $supplierName   = strtoupper(trim($_GET['supplierNamePrm']));
            $currencyCd     = strtoupper(trim($_GET['currencyCdPrm']));
            $unitCd         = strtoupper(trim($_GET['unitCdPrm']));
            $seqItem        = strtoupper(trim($_GET['seqItemPrm']));
            $supplierCdSet  = strtoupper(trim($_GET['supplierCdSetPrm']));
            $prItemPrice    = strtoupper(trim($_GET['prItemPricePrm'])); 
            //$limitPrice     = strtoupper(trim($_GET['limitPricePrm']));  
			$addPrItemPrice = (20 * $prItemPrice) / 100;
            $split_add   = explode('.', $addPrItemPrice);
            if($split_add[1] == 0)
            {
                $addPrItemPrice = number_format($addPrItemPrice);
            }
            else
            {
                $addPrItemPrice = number_format($addPrItemPrice, 2);
            }
            $addPrItemPrice      = str_replace(',', '',$addPrItemPrice);
            $addPrItemPrice      = rtrim(rtrim(number_format($addPrItemPrice, 2, ".", ""), '0'), '.');
            $limitPrice    = $prItemPrice + $addPrItemPrice;
			
			/**
             * SELECT EPS_T_PO_DETAIL
             */
            $query_select_t_po_detail = "select
                                            QTY
                                         from
                                            EPS_T_PO_DETAIL
                                         where
                                            REF_TRANSFER_ID = '$refTransferId'
                                            and PO_NO = '$poNo'";
            $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
            $row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
            $initialQtyPo           = $row_select_t_po_detail['QTY'];
            if($itemName == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($itemPrice <= 0 || $qty <= 0)
            {
                    //echo $qty;
                $msg = 'Mandatory_2';
            }
            else if($supplierCdSet != $supplierCd)
            {
                $msg = 'Mandatory_3';
            }
            else if($qty > $initialQtyPo)
            {
            
                $msg = 'Mandatory_5';
            }
            //sementara di tutup 7/2/2020
//            else if($currencyCd == "IDR" && $itemPrice > $limitPrice 
//                    && ($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_05' 
//                            || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_08' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_11'))
//            {
//                $msg = 'Mandatory_6';
//            }
            else
            {
                $query = "select count(*)
                            as TOTAL_SUPPLIER
                        from          
                            EPS_T_TRANSFER_SUPPLIER
                        where      
                            EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID = '$refTransferId'";
                $sql = $conn->query($query);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $totalSupplier = $row['TOTAL_SUPPLIER'];

                $itemPo         = ($_SESSION['poDetail']);
                $indexArray     = $seqItem - 1;

                /** 
                * UPDATE ARRAY VALUES by INDEX
                **/
                $itemPo[$indexArray]['refTransferId'] = $refTransferId;
                $itemPo[$indexArray]['itemCd'] = $itemCd;
                $itemPo[$indexArray]['itemName'] = $itemName;
                $itemPo[$indexArray]['qty'] = $qty;
                $itemPo[$indexArray]['itemPrice'] = $itemPrice;
                $itemPo[$indexArray]['amount'] = $amount;
                $itemPo[$indexArray]['unitCd'] = $unitCd;
                $itemPo[$indexArray]['currencyCd'] = $currencyCd;
                $itemPo[$indexArray]['seqPoItem'] = $seqItem;
                $itemPo[$indexArray]['totalSupplier'] = $totalSupplier;
                $itemPo[$indexArray]['itemStatus'] = '1270';
                $itemPo[$indexArray]['cip'] = $cip;    
                $_SESSION['poDetail'] = array_values($itemPo);

                $msg = 'Success';

            }
        }

        if($action == 'EditItemPoSent'){
            $itemPo         = array();
            $indexArray     = trim($_GET['$seqItem']);

            $refTransferId  = strtoupper(trim($_GET['refTransferIdPrm']));
            $itemCd         = strtoupper(trim($_GET['itemCdPrm']));
            $itemName       = strtoupper(trim($_GET['itemNamePrm']));
            $qty            = strtoupper(trim($_GET['qtyPrm']));
            $itemPrice      = strtoupper(trim($_GET['itemPricePrm']));
            $amount         = strtoupper(trim($_GET['amountPrm']));
            $supplierCd     = strtoupper(trim($_GET['supplierCdPrm']));
            $supplierName   = strtoupper(trim($_GET['supplierNamePrm']));
            $currencyCd     = strtoupper(trim($_GET['currencyCdPrm']));
            $unitCd         = strtoupper(trim($_GET['unitCdPrm']));
            $seqItem        = strtoupper(trim($_GET['seqItemPrm']));
            $supplierCdSet  = strtoupper(trim($_GET['supplierCdSetPrm']));
            $itemStatus     = strtoupper(trim($_GET['itemStatusPrm']));
            $totalReceived  = strtoupper(trim($_GET['totalReceivedPrm']));

            if($itemName == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($itemPrice <= 0 || $qty <= 0)
            {
                $msg = 'Mandatory_2';
            }
            else if($supplierCdSet != $supplierCd)
            {
                $msg = 'Mandatory_3';
            }
            elseif($qty < $totalReceived)
            {
                $msg = 'Mandatory_4';
            }
            else
            {
                if($qty == $totalReceived){
                    $itemStatus = '1320';
                }
                else
                {
                    $itemStatus = '1310';
                }
                $query = "select count(*)
                            as TOTAL_SUPPLIER
                        from          
                            EPS_T_TRANSFER_SUPPLIER
                        where      
                            EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID = '$refTransferId'";
                $sql = $conn->query($query);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $totalSupplier = $row['TOTAL_SUPPLIER'];

                $query2 = "select 
                            APP_STATUS_NAME
                        from
                            EPS_M_APP_STATUS
                        where
                            APP_STATUS_CD = '$itemStatus'";
                $sql2 = $conn->query($query2);
                $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                $itemStatusName = $row2['APP_STATUS_NAME'];

                $itemPo         = ($_SESSION['poDetail']);
                $indexArray     = $seqItem - 1;

                /** 
                * UPDATE ARRAY VALUES by INDEX
                **/
                $itemPo[$indexArray]['refTransferId']   = $refTransferId;
                $itemPo[$indexArray]['itemCd']          = $itemCd;
                $itemPo[$indexArray]['itemName']        = $itemName;
                $itemPo[$indexArray]['qty']             = $qty;
                $itemPo[$indexArray]['itemPrice']       = $itemPrice;
                $itemPo[$indexArray]['amount']          = $amount;
                $itemPo[$indexArray]['unitCd']          = $unitCd;
                $itemPo[$indexArray]['currencyCd']      = $currencyCd;
                $itemPo[$indexArray]['seqPoItem']       = $seqItem;
                $itemPo[$indexArray]['totalSupplier']   = $totalSupplier;
                $itemPo[$indexArray]['itemStatus']      = $itemStatus;
                $itemPo[$indexArray]['itemStatusName']  = $itemStatusName;

                $_SESSION['poDetail'] = array_values($itemPo);

                $msg = 'Success';

            }
        }

        if($action == 'DeleteItemPo'){
            $itemPo         = array();
            $indexArray     = trim($_GET['indexArrayPrm']);
            $transferId     = strtoupper(trim($_GET['refTransferIdValPrm']));
            $itemCd         = strtoupper(trim($_GET['itemCdValPrm']));
            $itemName       = stripslashes(strtoupper(trim($_GET['itemNameValPrm']))); 
            $qty            = strtoupper(trim($_GET['qtyValPrm']));
            $itemPrice      = strtoupper(trim($_GET['priceValPrm']));
            $amount         = strtoupper(trim($_GET['amountValPrm']));
            $currencyCd     = strtoupper($_GET['currencyCdValPrm']);
            $unitCd         = strtoupper($_GET['unitCdValPrm']);
            $seqPoItem      = strtoupper($_GET['seqPoItemValPrm']);
            $totalSupplier  = strtoupper($_GET['totalSupplierValPrm']);
            $itemPo         = ($_SESSION['poDetail']);

            $query = "select count(*)
                        as TOTAL_SUPPLIER
                    from          
                        EPS_T_TRANSFER_SUPPLIER
                    where      
                        EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID = '$transferId'";
            $sql = $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $totalSupplier = $row['TOTAL_SUPPLIER'];

            /** 
            * DELETE ARRAY VALUES by INDEX
            **/
            //unset($itemPo[$seqItemPo]);

            /** 
            * UPDATE ARRAY VALUES by INDEX
            **/
            $itemPo[$indexArray]['refTransferId'] = $transferId;
            $itemPo[$indexArray]['itemCd'] = $itemCd;
            $itemPo[$indexArray]['itemName'] = $itemName;
            $itemPo[$indexArray]['qty'] = $qty;
            $itemPo[$indexArray]['itemPrice'] = $itemPrice;
            $itemPo[$indexArray]['amount'] = $amount;
            $itemPo[$indexArray]['unitCd'] = $unitCd;
            $itemPo[$indexArray]['currencyCd'] = $currencyCd;
            $itemPo[$indexArray]['seqPoItem'] = $seqPoItem;
            $itemPo[$indexArray]['totalSupplier'] = $totalSupplier;
            $itemPo[$indexArray]['itemStatus'] = '1130';

            $_SESSION['poDetail'] = array_values($itemPo);

            $msg = 'Success_Delete';
        }
    }
    else
    {
        $msg = 'SessionExpired';
    }
}
else
{	
	$msg = 'SessionExpired';
}
echo $msg;
?>
