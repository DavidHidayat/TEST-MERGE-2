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
        $action     = 'Edit';

        $getCurrentDate = date('d/m/Y');
        $itemTemp       = array();
        $itemCd         = strtoupper(trim($_GET['itemCdPrm']));
        $itemNameNew    = strtoupper(trim($_GET['itemNamePrm']));
        $remark         = strtoupper(trim($_GET['remarkPrm']));
        $deliveryDate   = trim($_GET['deliveryDatePrm']);
        $itemType       = trim($_GET['itemTypePrm']);
        $rfiNo          = strtoupper(trim($_GET['rfiNoPrm']));
        $accountNo      = trim($_GET['accountNoPrm']);
        $invNo          = trim($_GET['invNoPrm']);
        $supplierCd     = strtoupper(trim($_GET['supplierCdPrm']));
        $supplierName   = strtoupper(trim($_GET['supplierNamePrm']));
        $unitCd         = strtoupper(trim($_GET['unitCdPrm']));
        $qty            = strtoupper(trim($_GET['qtyPrm']));
        $itemPrice      = strtoupper(trim($_GET['itemPricePrm']));
        $amount         = strtoupper(trim($_GET['amountPrm']));
        $currencyCd     = strtoupper(trim($_GET['currencyCdPrm']));
        $itemSts        = trim($_GET['itemStsPrm']);
        $prNo           = strtoupper(trim($_GET['prNoPrm']));
        $itemNameRef    = strtoupper(trim($_GET['refItemNamePrm']));
        $seqItem        = trim($_GET['seqItemPrm']);
        $itemNameGet    = strtoupper(trim($_GET['itemNameGetPrm']));
        $itemStsAlias   = strtoupper(trim($_GET['itemStsAliasPrm']));
        $itemTemp       = ($_SESSION['prDetail']);
        $msg            = '';

        if($itemNameNew == '' || $deliveryDate == '' || $itemType == '' 
                || $unitCd == '' || $qty == '' || $itemPrice == '')
        {
            $msg = 'Mandatory_1';
        }
        else if($itemType == '1' && $accountNo == '')
        {
            $msg = 'Mandatory_2';
        }
        else if($itemType == '2' && $rfiNo == '')
        {
            $msg = 'Mandatory_3';
        }
        else if($itemType == '3' && $invNo == '')
        {
            $msg = 'Mandatory_8';
        }
        else if($itemSts == '1130' && $supplierName == '')
        {
            $msg = 'Mandatory_4';
        }
        else if($itemSts == '1110')
        {
            $msg = 'Mandatory_5';
        }
        else if(strtotime(str_replace('/', '-', $deliveryDate)) < strtotime(str_replace('/', '-', $getCurrentDate)))
        {
            $msg = 'Mandatory_6';
        }
        else if($itemPrice <= 0 || $qty <= 0)
        {
            $msg = 'Mandatory_7';
        }
        else
        {
            if($itemNameGet == $itemNameNew)
            {
                $msg = 'Success';
            }
            else
            {
                foreach ($itemTemp as $itemTemps) {
                    $itemNameArray = strtoupper(trim($itemTemps['itemName']));
                    if($itemNameNew == $itemNameArray){
                        $msg = 'Duplicate';
                        break;
                    }else{
                        $msg = 'Success';
                    }
                }
            }
        }
        
        if($msg == 'Success')
        {
            $query = "select 
                            SUPPLIER_NAME
                            ,CURRENCY_CD
                        from 
                            EPS_M_SUPPLIER
                        where
                            SUPPLIER_CD = '$supplierCd'";
            $sql = $conn->query($query);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $supplierName = $row['SUPPLIER_NAME'];
            $currencyCd = $row['CURRENCY_CD'];
            if($itemType == '3'){
                $accountNo = $invNo;
            }    
            $itemTemp[$seqItem]['itemCd']         = $itemCd;
            $itemTemp[$seqItem]['itemName']       = stripslashes($itemNameNew);
            $itemTemp[$seqItem]['remark']         = $remark;
            $itemTemp[$seqItem]['deliveryDate']   = $deliveryDate;
            $itemTemp[$seqItem]['itemType']       = $itemType;
            $itemTemp[$seqItem]['rfiNo']          = $rfiNo;
            $itemTemp[$seqItem]['accountNo']      = $accountNo;
            $itemTemp[$seqItem]['supplierCd']     = $supplierCd;
            $itemTemp[$seqItem]['supplierName']   = $supplierName;
            $itemTemp[$seqItem]['unitCd']         = $unitCd;
            $itemTemp[$seqItem]['qty']            = $qty;
            $itemTemp[$seqItem]['itemPrice']      = $itemPrice;
            $itemTemp[$seqItem]['amount']         = $amount;
            $itemTemp[$seqItem]['currencyCd']     = $currencyCd;
            $itemTemp[$seqItem]['itemStatus']     = $itemSts;
            $itemTemp[$seqItem]['prNo']           = $prNo;
            $itemTemp[$seqItem]['refItemName']    = stripslashes($itemNameRef);
            $itemTemp[$seqItem]['seqItem']        = $seqItem;
            $itemTemp[$seqItem]['itemStatusAlias']= $itemStsAlias;

            $_SESSION['prDetail']                 = array_values($itemTemp);
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
