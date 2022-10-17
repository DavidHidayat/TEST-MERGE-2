<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";

if(isset($_SESSION['sNPK']) && isset($_SESSION['sUserId']) && isset($_GET['prNo'])){
    $userId 	= $_SESSION['sUserId'];
    $npk    	= $_SESSION['sNPK'];
    $warga      = $_SESSION['sWarga'];
    $prNo   	= $_GET['prNo'];
    $chargedBu  = $_GET['chargedBu'];
}
/**
 * SELECT EPS_M_EMPLOYEE
 */
$query_select_m_employee = "select
                                LEMBG
                            from
                                EPS_M_EMPLOYEE
                            where
                                NPK = '$npk'";
$sql_select_m_employee = $conn->query($query_select_m_employee);
$row_select_m_employee = $sql_select_m_employee->fetch(PDO::FETCH_ASSOC);
$lembg = $row_select_m_employee['LEMBG'];

/**
 * Search in EPS_T_PR_HEADER
 **/
$query = "select 
            PR_STATUS
            ,REQUESTER
            ,BU_CD
            ,REQ_BU_CD
            ,APPROVER
            ,USERID
            ,SPECIAL_TYPE_ID
            ,PROC_IN_CHARGE
          from
            EPS_T_PR_HEADER
          where
            PR_NO = '".$prNo."'";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);
if($row){
    $prStatus   = $row['PR_STATUS'];
    $requester  = $row['REQUESTER'];
    $prBuCd     = $row['BU_CD'];
    $prIssuer   = $row['REQ_BU_CD'];
    $approver   = $row['APPROVER'];
    $prUserId   = $row['USERID'];
    $specialType= $row['SPECIAL_TYPE_ID'];
    $procInCharge= $row['PROC_IN_CHARGE'];
}

/**
 * Search in EPS_M_PR_APPROVER
 **/
$query="select 
            APPROVER_NO
        from
            EPS_M_PR_APPROVER
        where
            NPK = '$userId'
            and BU_CD = '$prIssuer'";
$sql = $conn->query($query);
$row2 = $sql->fetch(PDO::FETCH_ASSOC);
$approverNo = $row2['APPROVER_NO'];
/**
 * Search in EPS_T_PR_APPROVER
 * Check approver number by next approver (EPS_T_PR_HEADER)
 **/
$query="select 
            APPROVER_NO
        from
            EPS_T_PR_APPROVER
        where
            NPK = '$approver'
            and PR_NO = '$prNo'";
$sql = $conn->query($query);
$row3 = $sql->fetch(PDO::FETCH_ASSOC);
$prApproverNo = $row3['APPROVER_NO'];
/**
 * Search in EPS_T_PR_APPROVER
 * Check NPK exist or not as approver in EPS_T_PR_APPROVER
 **/
$query = "select 
            count(NPK)
          as 
            APPROVER
          from
            EPS_T_PR_APPROVER
          where
            NPK = '$npk'
            and PR_NO = '$prNo'";
$sql = $conn->query($query);
$row4 = $sql->fetch(PDO::FETCH_ASSOC);
$approverPr = $row4['APPROVER'];

/**
 * Search count in EPS_T_PR_APPROVER
**/
$query = "select count(*)
            as COUT_APPROVER
          from 
            EPS_T_PR_APPROVER 
          where
            PR_NO = '$prNo'";
$sql = $conn->query($query);
$row5 = $sql->fetch(PDO::FETCH_ASSOC);
$countApprover = $row5['COUT_APPROVER'];

/** Check last approver status */
$query = "select 
            APPROVAL_STATUS
            ,NPK
            ,BU_CD
          from
            EPS_T_PR_APPROVER
          where
            PR_NO = '$prNo'
            and APPROVER_NO = '$countApprover'";
$sql = $conn->query($query);
$row6 = $sql->fetch(PDO::FETCH_ASSOC);
$lastApprovalStatus = $row6['APPROVAL_STATUS'];
$lastApprover = $row6['NPK'];
$prApproverBu = $row6['BU_CD'];

$_SESSION['sSpecialType']    = $specialType;
$_SESSION['sPrApproverBu']   = $prApproverBu;

if($prStatus == constant('1010') && trim($prUserId) == trim($userId)){
    $redirectPage = "../../epr/WEPR003.php?prNo=".$prNo;
    $_SESSION['EPSAuthority']='EPSEditPrScreen';
}
else if($prStatus == constant('1020') && trim($approver) == trim($userId) && $chargedBu == '' && trim($warga) == 'I'){
    $redirectPage = "../../epr/WEPR004.php?prNo=".$prNo;
    $_SESSION['EPSAuthority']='EPSApprovePrScreen';
}
else if($prStatus == constant('1020') && trim($approver) == trim($userId) && $chargedBu == '' && trim($warga) == 'A'){
    $redirectPage 				= "../../epr_/WEPR004.php?prNo=".$prNo;
	$_SESSION['prScreen']		= 'ApprovalPrScreen';
	$_SESSION['prStatusSession']= $prStatus;
    $_SESSION['prNoSession']    = $prNo;
	//$redirectPage = "../../epr/WEPR004.php?prNo=".$prNo;
    //$_SESSION['EPSAuthority']='EPSApprovePrScreen';
}
/*else if($prStatus == constant('1020') && trim($approver) == trim($userId) && $chargedBu == ''){
    $redirectPage = "../../epr/WEPR004.php?prNo=".$prNo;
    $_SESSION['EPSAuthority']='EPSApprovePrScreen';
}*/
else if($prStatus == constant('1020') && $approverNo > $prApproverNo && $approverPr != 0 && $chargedBu == ''){
    //$redirectPage = "../../epr/WEPR005.php?prNo=".$prNo;
    //$_SESSION['EPSAuthority']='EPSTakeOverPrScreen';
	$redirectPage               = "../../epr_/WEPR005.php?prNo=".$prNo;
    $_SESSION['EPSAuthority']   ='EPSTakeOverPrScreen';
    $_SESSION['prStatusSession']= $prStatus;
    $_SESSION['prNoSession']    = $prNo;
}
else if( $prStatus == '1030' && $procInCharge == $userId)
{
    $query_select_t_pr_detail = 
					"select 
                        EPS_T_PR_HEADER.PR_STATUS
                        ,EPS_T_PR_HEADER.PROC_IN_CHARGE
                        ,EPS_T_PR_DETAIL.PR_NO
                        ,EPS_T_PR_DETAIL.ITEM_CD
                        ,EPS_T_PR_DETAIL.ITEM_NAME
                        ,substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 7, 2) 
                        + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 5, 2) 
                        + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                        ,EPS_T_PR_DETAIL.QTY
                        ,EPS_T_PR_DETAIL.ITEM_PRICE
                        ,EPS_T_PR_DETAIL.AMOUNT
                        ,EPS_T_PR_DETAIL.CURRENCY_CD
                        ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
                        ,EPS_T_PR_DETAIL.ACCOUNT_NO
                        ,EPS_T_PR_DETAIL.RFI_NO
                        ,EPS_T_PR_DETAIL.UNIT_CD
                        ,EPS_T_PR_DETAIL.SUPPLIER_CD
                        ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                        ,EPS_T_PR_DETAIL.REMARK
                        ,EPS_T_PR_DETAIL.ITEM_STATUS
                        ,EPS_M_APP_STATUS.APP_STATUS_ALIAS
                        ,EPS_T_PR_DETAIL.REASON_TO_REJECT_ITEM
                        ,EPS_T_PR_DETAIL.REJECT_ITEM_BY
                        ,EPS_M_EMPLOYEE.NAMA1 as REJECT_ITEM_NAME_BY
                        ,(select count (*)
                            from          
                        EPS_T_PR_ATTACHMENT
                            where      
                        EPS_T_PR_HEADER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                            and 
                        EPS_T_PR_DETAIL.ITEM_NAME = EPS_T_PR_ATTACHMENT.ITEM_NAME) as ATTACHMENT_ITEM_COUNT
                        ,case 
                            when 
                                CHARINDEX('.', ITEM_NAME) - 1 > 0 
                            then 
                        case 
                            when 
                                isnumeric(substring(ITEM_NAME, 1, CHARINDEX('.',ITEM_NAME) - 1)) = 1 
                                then 
                                    substring(ITEM_NAME, 1, CHARINDEX('.', ITEM_NAME) - 1) 
                                else 
                                    999 
                                end 
                            else 
                                999 
                            end 
                        as INDEX_ITEM_NAME
                        ,EPS_T_PR_HEADER.UPDATE_DATE
                    from 
                        EPS_T_PR_DETAIL 
                    inner join
                        EPS_T_PR_HEADER
                    on 
                        EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
                    left join
                        EPS_M_EMPLOYEE
                    on 
                        EPS_T_PR_DETAIL.REJECT_ITEM_BY = EPS_M_EMPLOYEE.NPK
                    left join
                        EPS_M_APP_STATUS 
                    on 
                        EPS_T_PR_DETAIL.ITEM_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                    where 
                        EPS_T_PR_HEADER.PR_NO ='".$prNo."'
                    and 
                        (EPS_T_PR_DETAIL.ITEM_STATUS = '1060') 
                    order by 
                        INDEX_ITEM_NAME, EPS_T_PR_DETAIL.ITEM_NAME";
    $sql_select_t_pr_detail = $conn->query($query_select_t_pr_detail);
    $i = 0;
    while($row = $sql_select_t_pr_detail->fetch(PDO::FETCH_ASSOC)){
        $prStatus       = $row['PR_STATUS'];
        $procInCharge   = $row['PROC_IN_CHARGE'];
		$prNo           = $row['PR_NO'];
        $itemCd         = $row['ITEM_CD'];
        $itemName       = trim($row['ITEM_NAME']);
        $deliveryDate   = $row['DELIVERY_DATE'];
        $qty            = $row['QTY'];
        $itemPrice      = $row['ITEM_PRICE'];
        $amount         = $row['AMOUNT'];
        $currencyCd     = $row['CURRENCY_CD'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountCd      = $row['ACCOUNT_NO'];
        $rfiNo          = $row['RFI_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = trim($row['SUPPLIER_NAME']);
        $remark         = $row['REMARK'];
        $itemStatus     = $row['ITEM_STATUS'];
        $itemStatusAlias= trim($row['APP_STATUS_ALIAS']);
        $reasonToReject = $row['REASON_TO_REJECT_ITEM'];
        $rejectItemBy   = $row['REJECT_ITEM_BY'];
        $rejectItemNameBy= $row['REJECT_ITEM_NAME_BY'];
        $attachmentItemCount = $row['ATTACHMENT_ITEM_COUNT'];
        $procPrUpdateDate= $row['UPDATE_DATE'];
        $itemNameSet     = str_replace(array("\n", "\r"), '', $itemNameSet);
                
        if($itemStatus == '1060'){
            $itemStatus = '1110';
        }
        if($itemStatusAlias == 'APP'){
            $itemStatusAlias = 'WAI';
        }
        if(strlen($accountCd) == 1)
        {
            $accountCd = '0'.$accountCd;
        }
        $prDetail[] = array(
                                'itemCd'=> $itemCd
                                ,'itemName'=> $itemName
                                ,'remark'=> $remark
                                ,'deliveryDate'=> $deliveryDate
                                ,'itemType'=> $itemType
                                ,'rfiNo'=> $rfiNo
                                ,'accountNo'=> $accountCd
                                ,'supplierCd'=> $supplierCd
                                ,'supplierName'=> $supplierName
                                ,'unitCd'=> $unitCd
                                ,'qty'=> $qty
                                ,'itemPrice'=> $itemPrice
                                ,'amount'=> $amount
                                ,'currencyCd'=> $currencyCd
                                ,'itemStatus'=> $itemStatus
                                ,'prNo'=> $prNo
                                ,'refItemName'=>$itemName
                                ,'seqItem'=>$i
                                ,'itemStatusAlias'=>$itemStatusAlias
                                ,'attachmentItemCount'=>$attachmentItemCount
                        );
        $i++;
    }
    $redirectPage = "../../epo/WEPO002_.php?paramPrNo=".$prNo;
    $_SESSION['prNoSession']        = $prNo;
    $_SESSION['prDetail']           = $prDetail;
    $_SESSION['prScreen']           = 'DetailPrScreen';
    $_SESSION['prStatus']           = $prStatus;
    $_SESSION['procInCharge']       = $procInCharge;
    $_SESSION['procPrUpdateDate']   = $procPrUpdateDate;
}
else if($prStatus == '1030' ||  $prStatus == '1040')
{
    $redirectPage = "../../epr_/WEPR006.php?paramPrNo=".$prNo;
    $_SESSION['prScreen']           = 'DetailAcceptPrScreen';
    $_SESSION['prNoSession']        = $prNo;
    $_SESSION['prStatusSession']    = $prStatus;
}
else
{
    $redirectPage = "../../epr/WEPR006.php?prNo=".$prNo;
    
	$_SESSION['prScreen']           = 'DetailPrScreen';
    $_SESSION['EPSAuthority']       = 'EPSDetailPrScreen';
}
echo "<script>document.location.href='".$redirectPage."';</script>";
?>
