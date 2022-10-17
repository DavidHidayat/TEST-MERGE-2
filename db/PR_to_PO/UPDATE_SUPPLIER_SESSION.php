<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
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
        $action     = $_GET['action'];
        
        if($action == 'UpdateSupplier')
		{
            $itemSupplier       = array();
            $itemSupplierTemp   = array();

            $itemSupplier       = ($_SESSION['transferSupplier']);
            $transferId         = strtoupper(trim($_GET['transferIdPrm']));
            $supplierCd         = strtoupper(trim($_GET['supplierCdPrm']));
            $currencyCd         = strtoupper(trim($_GET['currencyCdPrm'])); 
            $itemPrice          = trim($_GET['itemPricePrm']);    
            $limitPrice         = trim($_GET['limitPricePrm']);   
            $leadTime           = trim($_GET['leadTimePrm']);
            $unitTime           = strtoupper(trim($_GET['unitTimePrm']));
            $attachmentLoc      = strtoupper(trim($_GET['attachmentLocPrm']));
            $attachmentLoc      = str_replace("'", "''", $attachmentLoc);
//            $attachmentCip      = strtoupper(trim($_GET['attachmentCipPrm']));
//            $attachmentCip      = str_replace("'", "''", $attachmentCip);
            $attachmentCip      = strtoupper(trim($_GET['attachmentCipPrm']));
            $remark             = strtoupper(trim($_GET['remarkPrm']));
            $actionBtn          = strtoupper(trim($_GET['actionPrm']));
            $supplierCdGet      = strtoupper(trim($_GET['supplierCdGetPrm']));
            $seqSupplier        = trim($_GET['seqSupplierPrm']);
            $msg                = '';
    
           

            if($supplierCd == '' || $leadTime == ''|| $unitTime == ''|| $itemPrice == '' || $currencyCd == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($itemPrice < 0)
            {
                $msg = 'Mandatory_2';
            }
            /*else if($currencyCd == "IDR" && $itemPrice > $limitPrice)
            {
                $msg = 'Mandatory_4';
            }*/
            else
            {
                if($actionBtn == 'ADD')
                {
                    if(count($itemSupplier) > 0)
                    {
                        foreach ($itemSupplier as $itemSuppliers) 
                        {
                            $supplierCdArray = strtoupper(trim($itemSuppliers['supplierCd']));
                            if($supplierCd == $supplierCdArray)
                            {
                                $msg = 'Duplicate';
                                break;
                            }
                            else
                            {
                                $msg = 'Success_Add';
                            }
                        }
                    }
                    else
                    {
                        $msg = 'Success_Add';
                    }
                }
                else
                {
                    if($actionBtn == 'EDIT')
                    {
                        if($supplierCdGet == $supplierCd)
                        {
                            $msg = 'Success_Edit';
                        }
                        else
                        {
                            foreach ($itemSupplier as $itemSuppliers) 
                            {
                                $supplierCdArray = strtoupper(trim($itemSuppliers['supplierCd']));
                                if($supplierCd == $supplierCdArray)
                                {
                                    $msg = 'Duplicate';
                                    break;
                                }
                                else
                                {
                                    $msg = 'Success_Edit';
                                }
                            }
                        }
                    }
                    else
                    {
                        $msg = 'Mandatory_3';
                    }
                }
            }
            
            if($msg == 'Success_Add'){
                $query = "select 
                            SUPPLIER_NAME
                        from 
                            EPS_M_SUPPLIER
                        where
                            SUPPLIER_CD = '$supplierCd'";
                $sql = $conn->query($query);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $supplierName = $row['SUPPLIER_NAME'];

                if(count($itemSupplier) == 0){
                    $itemSupplierTemp[] = array(
                                            'transferId'=> $transferId
                                            ,'supplierCd'=> $supplierCd
                                            ,'supplierName'=> $supplierName
                                            ,'currencyCd' => $currencyCd
                                            ,'itemPrice'=> $itemPrice
                                            ,'leadTime'=> $leadTime
                                            ,'unitTime'=> $unitTime
                                            ,'attachmentLoc'=> $attachmentLoc
                                            ,'remark'=> $remark
                                            ,'seqSupplier'=> 1
                                            ,'attachmentCip'=> $attachmentCip
                                        );
                    $addItemSupplier = $itemSupplierTemp;
                    $_SESSION['transferSupplier']       = $addItemSupplier;
                }else{
                    $itemSupplierTemp[] = array(
                                                'transferId'=> $transferId
                                                ,'supplierCd'=> $supplierCd
                                                ,'supplierName'=> $supplierName
                                                ,'currencyCd' => $currencyCd
                                                ,'itemPrice'=> $itemPrice
                                                ,'leadTime'=> $leadTime
                                                ,'unitTime'=> $unitTime
                                                ,'attachmentLoc'=> $attachmentLoc
                                                ,'remark'=> $remark
                                                ,'seqSupplier'=> count($itemSupplier) + 1
                                                ,'attachmentCip'=> $attachmentCip
                                            );
                    $addItemSupplier = $itemSupplierTemp;
                    $result = array_merge($itemSupplier,$addItemSupplier);
                    $_SESSION['transferSupplier']       = $result;
                }
            }
            
            if($msg == 'Success_Edit'){
                $query = "select 
                            SUPPLIER_NAME
                        from 
                            EPS_M_SUPPLIER
                        where
                            SUPPLIER_CD = '$supplierCd'";
                $sql = $conn->query($query);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $supplierName = $row['SUPPLIER_NAME'];
                $indexSupplier = $seqSupplier - 1;

                $itemSupplier[$indexSupplier]['transferId']   = $transferId;
                $itemSupplier[$indexSupplier]['supplierCd']   = $supplierCd;
                $itemSupplier[$indexSupplier]['supplierName'] = $supplierName;
                $itemSupplier[$indexSupplier]['currencyCd']   = $currencyCd;
                $itemSupplier[$indexSupplier]['itemPrice']    = $itemPrice;
                $itemSupplier[$indexSupplier]['leadTime']     = $leadTime;
                $itemSupplier[$indexSupplier]['unitTime']     = $unitTime;
                $itemSupplier[$indexSupplier]['attachmentLoc']= $attachmentLoc;
                $itemSupplier[$indexSupplier]['remark']       = $remark;
                $itemSupplier[$indexSupplier]['attachmentCip']= $attachmentCip;
                $itemSupplier[$indexSupplier]['seqSupplier']  = $seqSupplier;

                $_SESSION['transferSupplier'] = array_values($itemSupplier);
            }
        }
        
        if($action == 'DeleteSupplier'){
            $seqSupplier        = trim($_GET['seqSupplierPrm']);
            $itemSupplier       = array();
            $indexSupplier      = $seqSupplier;
            
            $itemSupplier       = ($_SESSION['transferSupplier']);
            unset($itemSupplier[$indexSupplier]);
            $_SESSION['transferSupplier'] = array_values($itemSupplier);
            
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
