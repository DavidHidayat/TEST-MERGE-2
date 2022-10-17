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
		
        if($action == 'UpdateReceiving')
		{
			$itemReceived       = array();
            $itemReceivedTemp   = array();
            
            $itemReceived       = ($_SESSION['roDetail']);
            $actionBtn          = strtoupper(trim($_GET['actionPrm']));
            $poNo               = trim($_GET['poNoPrm']);
            $refTransferId      = trim($_GET['refTransferIdPrm']);
            $roNo               = trim($_GET['roNoPrm']);
            $qty                = trim($_GET['qtyPrm']);
            $totalReceivedQty   = trim($_GET['totalReceivedQtyPrm']);
            $receivedQty        = trim($_GET['receivedQtyPrm']);
            $receivedDate       = trim($_GET['receivedDatePrm']);
            $roSeq              = trim($_GET['roSeqPrm']);
            $initialReceivedQty = trim($_GET['initialReceivedQtyPrm']);
            $newTotalReceivedQty = ($totalReceivedQty - $initialReceivedQty) + $receivedQty;
            
            if($receivedQty == '' || $receivedDate == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($receivedQty <= 0)
            {
                $msg = 'Mandatory_2';
            }
            else if($newTotalReceivedQty > $qty)
            {
                $msg = 'Mandatory_3';
            }
            else
            {
                if($actionBtn == 'ADD'){
                    if(count($itemReceived) == 0){
                        $itemReceivedTemp[] = array(
                                                    'roNo'=> $roNo
                                                    ,'roSeq'=> 1
                                                    ,'poNo'=> $poNo
                                                    ,'refTransferId'=> $refTransferId
                                                    ,'transactionQty'=> $receivedQty
                                                    ,'transactionFlag'=> 'A'
                                                    ,'transactionDate'=> $receivedDate
                                                );
                        $addItemReceived = $itemReceivedTemp;
                        $_SESSION['roDetail']       = $addItemReceived;
                    }else{
                         $itemReceivedTemp[] = array(
                                                    'roNo'=> $roNo
                                                    ,'roSeq'=> count($itemReceived) + 1
                                                    ,'poNo'=> $poNo
                                                    ,'refTransferId'=> $refTransferId
                                                    ,'transactionQty'=> $receivedQty
                                                    ,'transactionFlag'=> 'A'
                                                    ,'transactionDate'=> $receivedDate
                                                );
                        $addItemReceived = $itemReceivedTemp;
                        $result = array_merge($itemReceived,$addItemReceived);
                        $_SESSION['roDetail']       = $result;
                    }
                    $msg = 'Success';
                }
            }
            /*$itemReceived       = array();
            $itemReceivedTemp   = array();
            
            $itemReceived       = ($_SESSION['roDetail']);
            $actionBtn          = strtoupper(trim($_GET['actionPrm']));
            $poNo               = trim($_GET['poNoPrm']);
            $refTransferId      = trim($_GET['refTransferIdPrm']);
            $roNo               = trim($_GET['roNoPrm']);
            $qty                = trim($_GET['qtyPrm']);
            $totalReceivedQty   = trim($_GET['totalReceivedQtyPrm']);
            $receivedQty        = trim($_GET['receivedQtyPrm']);
            $receivedDate       = trim($_GET['receivedDatePrm']);
            $receivedSeq        = trim($_GET['receivedSeqPrm']);
            $initialReceivedQty = trim($_GET['initialReceivedQtyPrm']);
            $newTotalReceivedQty = ($totalReceivedQty - $initialReceivedQty) + $receivedQty;
            $msg                = '';
            
            if($receivedQty == '' || $receivedDate == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($receivedQty <= 0){
                $msg = 'Mandatory_2';
            }
            else if($newTotalReceivedQty > $qty)
            {
                $msg = 'Mandatory_3';
            }
            else
            {
                if($actionBtn == 'ADD'){
                    if(count($itemReceived) == 0){
                        $itemReceivedTemp[] = array(
                                                    'roNo'=> $roNo
                                                    ,'poNo'=> $poNo
                                                    ,'refTransferId'=> $refTransferId
                                                    ,'receivedQty'=> $receivedQty
                                                    ,'receivedDate'=> $receivedDate
                                                    ,'receivedSeq'=> 1
                                                );
                        $addItemReceived = $itemReceivedTemp;
                        $_SESSION['roDetail']       = $addItemReceived;
                    }else{
                         $itemReceivedTemp[] = array(
                                                    'roNo'=> $roNo
                                                    ,'poNo'=> $poNo
                                                    ,'refTransferId'=> $refTransferId
                                                    ,'receivedQty'=> $receivedQty
                                                    ,'receivedDate'=> $receivedDate
                                                    ,'receivedSeq'=> count($itemReceived) + 1
                                                );
                        $addItemReceived = $itemReceivedTemp;
                        $result = array_merge($itemReceived,$addItemReceived);
                        $_SESSION['roDetail']       = $result;
                    }
                    $msg = 'Success';
                }
                else
                {
                    if($actionBtn == 'EDIT'){
                        $indexReceived = $receivedSeq - 1;
                        $itemReceived[$indexReceived]['roNo']           = $roNo;
                        $itemReceived[$indexReceived]['poNo']           = $poNo;
                        $itemReceived[$indexReceived]['refTransferId']  = $refTransferId;
                        $itemReceived[$indexReceived]['receivedQty']    = $receivedQty;
                        $itemReceived[$indexReceived]['receivedDate']   = $receivedDate;
                        $itemReceived[$indexReceived]['receivedSeq']    = $receivedSeq;
                        $_SESSION['roDetail'] = array_values($itemReceived);
                        $msg = 'Success';
                    }
                    else
                    {
                        $msg = 'Mandatory_4';
                    }
                }
            }*/
        }
		
        if($action == 'UpdateCancelReceiving')
        {
            $itemReceived       = array();
            $itemReceivedTemp   = array();
            
            $itemReceived       = ($_SESSION['roDetail']);
            $actionBtn          = strtoupper(trim($_GET['actionPrm']));
            $poNo               = trim($_GET['poNoPrm']);
            $refTransferId      = trim($_GET['refTransferIdPrm']);
            $roNo               = trim($_GET['roNoPrm']);
            $qty                = trim($_GET['qtyPrm']);
            $totalReceivedQty   = trim($_GET['totalReceivedQtyPrm']);
            $cancelQty          = trim($_GET['cancelQtyPrm']);
            $cancelDate         = trim($_GET['cancelDatePrm']);
            $cancelRemark       = strtoupper(trim($_GET['cancelRemarkPrm']));
            $roSeq              = trim($_GET['roSeqPrm']);
            $newTotalReceivedQty= $totalReceivedQty - $cancelQty;
            
            if($cancelQty == '' || $cancelRemark == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($cancelQty <= 0)
            {
                $msg = 'Mandatory_2';
            }
            else if($cancelQty > $totalReceivedQty)
            {
                $msg = 'Mandatory_3';
            }
            else
            {
                $itemReceivedTemp[] = array(
                                        'roNo'=> $roNo
                                        ,'roSeq'=> count($itemReceived) + 1
                                        ,'poNo'=> $poNo
                                        ,'refTransferId'=> $refTransferId
                                        ,'transactionQty'=> $cancelQty
                                        ,'transactionFlag'=> 'C'
                                        ,'transactionDate'=> $cancelDate
                                        ,'roRemark'=> $cancelRemark
                                    );
                $cancelItemReceived = $itemReceivedTemp;
                $result = array_merge($itemReceived,$cancelItemReceived);
                $_SESSION['roDetail']       = $result;
                $msg = 'Success';          
            }
        }
		
		if($action == 'UpdateOpenReceiving')
        {
            $itemReceived       = array();
            $itemReceivedTemp   = array();
            
            $itemReceived       = ($_SESSION['roDetail']);
            $actionBtn          = strtoupper(trim($_GET['actionPrm']));
            $poNo               = trim($_GET['poNoPrm']);
            $refTransferId      = trim($_GET['refTransferIdPrm']);
            $roNo               = trim($_GET['roNoPrm']);
            $qty                = trim($_GET['qtyPrm']);
            $totalReceivedQty   = trim($_GET['totalReceivedQtyPrm']);
            $openQty            = trim($_GET['openQtyPrm']);
            $openDate           = trim($_GET['openDatePrm']);
            $openRemark         = strtoupper(trim($_GET['openRemarkPrm']));
            $roSeq              = trim($_GET['roSeqPrm']);
            $newTotalReceivedQty= $totalReceivedQty - $openQty;
            
            if($openQty == '' || $openRemark == '')
            {
                $msg = 'Mandatory_1';
            }
            else if($openQty <= 0)
            {
                $msg = 'Mandatory_2';
            }
            else if($openQty > $totalReceivedQty)
            {
                $msg = 'Mandatory_3';
            }
            else
            {
                $itemReceivedTemp[] = array(
                                        'roNo'=> $roNo
                                        ,'roSeq'=> count($itemReceived) + 1
                                        ,'poNo'=> $poNo
                                        ,'refTransferId'=> $refTransferId
                                        ,'transactionQty'=> $openQty
                                        ,'transactionFlag'=> 'O'
                                        ,'transactionDate'=> $openDate
                                        ,'roRemark'=> $openRemark
                                    );
                $openItemReceived = $itemReceivedTemp;
                $result = array_merge($itemReceived,$openItemReceived);
                $_SESSION['roDetail']       = $result;
                $msg = 'Success';          
            }
        }
		
        if($action == 'DeleteReceiving'){
            $receivedSeq        = trim($_GET['receivedSeqPrm']);
            $itemReceived       = array();
            $indexReceiving     = $receivedSeq;
            
            $itemReceived       = ($_SESSION['roDetail']);
            unset($itemReceived[$indexReceiving]);
            $_SESSION['roDetail'] = array_values($itemReceived);
        }
        
    }
    else
    {
        $msg = 'SessionExpired';
    }
    echo $msg;
}else{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../db/Login/Logout.php"; </script>
<?
}
?>
