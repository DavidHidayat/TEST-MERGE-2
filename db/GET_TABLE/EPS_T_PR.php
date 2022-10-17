<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
if(isset($_SESSION['sNPK']))
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
    $sUserId    = $_SESSION['sUserId'];
    $sBuLogin   = $_SESSION['sBuLogin'];
    $sUserType  = $_SESSION['sUserType'];
    $action     = 'Edit';
    
}else{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../db/Login/Logout.php"; </script>
<?
}
$criteria       = trim($_GET['criteria']);
$prNoPrm        = strtoupper(trim($_GET['prNoPrm']));
$itemNamePrm    = strtoupper(trim($_GET['itemNamePrm']));
$itemNamePrm    = stripslashes(trim($_GET['itemNamePrm']));
$refTransferId  = strtoupper(trim($_GET['refTransferIdPrm']));

if($criteria== 'PrDetail')
{
    $htmlTableHeader = "<table class='table table-striped table-bordered' id='table-prheader'>
                        <thead>
                            <tr>
                                <th colspan='6'>PR HEADER</th>
                            </tr>
                            <tr>
                                <th>NAME</th>
                                <th>EXT</th>
                                <th>PR DATE</th>
                                <th>ISSUER</th>
                                <th>CHARGED</th>
                                <th>PURPOSE</th>
                            </tr>
                        </thead>
                        <tbody>";
    $query2 = "select 
                EPS_T_PR_HEADER.PR_NO
                ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                ,EPS_T_PR_HEADER.PURPOSE
                ,EPS_T_PR_HEADER.REQUESTER
                ,EPS_T_PR_HEADER.EXT_NO
                ,EPS_T_PR_HEADER.REQ_BU_CD
                ,EPS_T_PR_HEADER.CHARGED_BU_CD
                ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
               from
                EPS_T_PR_HEADER
               left join
                EPS_M_EMPLOYEE
               on
                EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
               left join
                EPS_T_TRANSFER
               on
                 EPS_T_PR_HEADER.PR_NO = EPS_T_TRANSFER.PR_NO
               where
                EPS_T_TRANSFER.TRANSFER_ID = '$refTransferId'";
    $sql2 = $conn->query($query2);
    $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
    $prNo           = $row2['PR_NO'];
    $issuedDate     = $row2['ISSUED_DATE'];
    $requesterName  = $row2['REQUESTER_NAME'];
    $extNo          = $row2['EXT_NO'];
    $purpose        = $row2['PURPOSE'];
    $reqBuCd        = $row2['REQ_BU_CD'];
    $chargedBuCd    = $row2['CHARGED_BU_CD'];
    
    $htmlTableHeader .= "<tr>
                    <td>
                        $requesterName
                    </td>
                    <td width='10%'>
                        $extNo
                    </td>
                    <td>
                        $issuedDate
                    </td>
                    <td>
                        $reqBuCd
                    </td>
                    <td>
                        $chargedBuCd
                    </td>
                    <td width='70%'>
                        $purpose
                    </td>";
    $htmlTableHeader .= "</tr>
                </tbody>
              </table>";
			  
    $htmlTable      = 
                "<table class='table table-striped table-bordered' id='table-pritem'>
                        <thead>
                            <tr>
                                <th colspan='12'>PR DETAIL</th>
                            </tr>
                            <tr>
                                <th>CODE</th>
                                <th>NAME</th>
                                <th>DUE DATE</th>
                                <th>EXP</th>
                                <th>RFI</th>
                                <th>QTY</th>
                                <th>UM</th>
                                <th>UNIT PRICE</th>
                                <th>SUPPLIER</th>
                                <th>AMOUNT</th>
                                <th>REMARK</th>
                                <th>PR ATTACH</th>
                            </tr>
                        </thead>
                        <tbody>";
    $query = "select 
                EPS_T_PR_DETAIL.ITEM_CD
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
                ,EPS_T_PR_HEADER.REQUESTER
            from 
                EPS_T_PR_DETAIL
            left join
                EPS_T_TRANSFER
            on
                EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO
                and replace(replace(replace(EPS_T_PR_DETAIL.ITEM_NAME, char(13), ''), char(9), ''), ' ', '') = replace(EPS_T_TRANSFER.ITEM_NAME, ' ', '')
            left join
                EPS_T_PR_HEADER
            on
                EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
            where
                EPS_T_TRANSFER.TRANSFER_ID = '$refTransferId'";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $itemCd         = $row['ITEM_CD'];
        $itemName       = trim($row['ITEM_NAME']);
        $deliveryDate   = $row['DELIVERY_DATE'];
        $qty            = $row['QTY'];
        $itemPrice      = number_format($row['ITEM_PRICE']);
        $amount         = number_format($row['AMOUNT']);
        $currencyCd     = $row['CURRENCY_CD'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountCd      = $row['ACCOUNT_NO'];
        $rfiNo          = $row['RFI_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = trim($row['SUPPLIER_NAME']);
        $remark         = $row['REMARK'];
        
        if(strlen($accountCd) == 1 && trim($accountCd) !='')
        {
            $accountCd = "0".$accountCd;
        }
        $htmlTable .= "<tr>
                    <td>
                        $itemCd
                    </td>
                    <td>
                        $itemName
                    </td>
                    <td>
                        $deliveryDate
                    </td>
                    <td>
                        $accountCd
                    </td>
                    <td>
                        $rfiNo
                    </td>
                    <td style='text-align: right'>
                        $qty
                    </td>
                    <td>
                        $unitCd
                    </td>
                    <td style='text-align: right'>
                        $itemPrice
                    </td>
                    <td>
                        $supplierName
                    </td>
                    <td style='text-align: right'>
                        $amount
                    </td>
                    <td>
                        $remark
                    </td>
                    <td>
                        Attach
                    </td>
                </tr>";
    }
    $htmlTable      .= "
            </tbody>
        </table>";
    $htmlTableApprover  = 
                    "<table class='table table-striped table-bordered' id='table-pr-approver'>
                        <thead>
                            <tr>
                                <th colspan='11'>PR APPROVER</th>
                            </tr>
                            <tr>
                                <th>NO</th>
                                <th>NAME</th>
                                <th>APPROVAL DATE</th>
                                <th>STATUS</th>
                                <th>REMARK</th>
                            </tr>
                        </thead>
                        <tbody>";
    $query_select_t_pr_approver = "select 
                                    EPS_T_PR_APPROVER.PR_NO
                                    ,EPS_T_PR_APPROVER.BU_CD
                                    ,EPS_T_PR_APPROVER.APPROVER_NO
                                    ,EPS_T_PR_APPROVER.NPK
                                    ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                    ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                                    ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                    ,convert(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                                    ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                                    ,EPS_T_PR_APPROVER.DATE_OF_BYPASS
                                    ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                                   from 
                                     EPS_T_PR_APPROVER 
                                   left join
                                     EPS_M_EMPLOYEE
                                   on
                                     EPS_T_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                   left join
                                     EPS_M_APPROVAL_STATUS
                                   on
                                     EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
                                   left join
                                     EPS_T_PR_HEADER 
                                   on 
                                     EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                   where 
                                     EPS_T_PR_APPROVER.PR_NO ='".$prNo."'
                                   order by
                                     EPS_T_PR_APPROVER.APPROVER_NO 
                                   asc ";
    $sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
    while($row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC)){
        $approverNo         = $row_select_t_pr_approver['APPROVER_NO'];
        $buCd               = $row_select_t_pr_approver['BU_CD'];
        $npk                = $row_select_t_pr_approver['NPK'];
        $approverName       = stripslashes($row_select_t_pr_approver['APPROVER_NAME']);
        $approvalStatus     = $row_select_t_pr_approver['APPROVAL_STATUS'];
        $approvalStatusName = $row_select_t_pr_approver['APPROVAL_STATUS_NAME'];
        $approvalDate       = $row_select_t_pr_approver['APPROVAL_DATE'];
        $approvalRemark     = $row_select_t_pr_approver['APPROVAL_REMARK'];
        $dateByPass         = $row_select_t_pr_approver['DATE_OF_BYPASS'];
        $specialType        = $row_select_t_pr_approver['SPECIAL_TYPE_ID'];
        if(strlen(trim($approvalDate)) != 0){
            date_default_timezone_set('Asia/Jakarta');
            $approvalDate   = date("d/m/Y H:i:s A", strtotime($approvalDate));
        }
        if(strlen(trim($dateByPass)) != 0){
            date_default_timezone_set('Asia/Jakarta');
            if(strlen($dateByPass) == 22){
                $newMonth = substr($dateByPass,0,2);
                $newDate = substr($dateByPass,3,2).'/';
                $newYear = substr($dateByPass,5);
            }
            if(strlen($dateByPass) == 21){
                if(substr($dateByPass,0,1) != 0){
                    $newMonth = '0'.substr($dateByPass,0,1).'/';
                }
                $newDate = substr($dateByPass,2,2).'/';
                $newYear = substr($dateByPass,5);
            }
            if(strlen($dateByPass) == 20){
                if(substr($dateByPass,0,1) != 0){
                    $newMonth = '0'.substr($dateByPass,0,1).'/';
                }
                $newDate = '0'.substr($dateByPass,2,1).'/';
                $newYear = substr($dateByPass,4);
            }   
                                                
            $dateByPass = $newDate.$newMonth.$newYear;
            $approvalDate = $dateByPass;
        }
        $htmlTableApprover .= "<tr>
                                    <td>
                                        $approverNo
                                    </td>
                                    <td>
                                        $approverName
                                    </td>
                                    <td>
                                        $approvalDate
                                    </td>
                                    <td>
                                        $approvalStatusName
                                    </td>
                                    <td>
                                        $approvalRemark
                                    </td>
                              </tr>";
    }
    
    $htmlTableApprover .= "
                        </tbody>
                 </table>";
}

if($criteria== 'PrDetailByItemName')
{
    $htmlTableHeader = "<table class='table table-striped table-bordered' id='table-prheader'>
                        <thead>
                            <tr>
                                <th colspan='4'>PR HEADER</th>
                            </tr>
                            <tr>
                                <th>NAME</th>
                                <th>PR DATE</th>
                                <th>EXT</th>
                                <th>PURPOSE</th>
                            </tr>
                        </thead>
                        <tbody>";
    $query2 = "select 
                EPS_T_PR_HEADER.PR_NO
                ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                ,EPS_T_PR_HEADER.PURPOSE
                ,EPS_T_PR_HEADER.REQUESTER
                ,EPS_T_PR_HEADER.EXT_NO
                ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
               from
                EPS_T_PR_HEADER
               left join
                EPS_M_EMPLOYEE
               on
                EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
               left join
                EPS_T_TRANSFER
               on
                 EPS_T_PR_HEADER.PR_NO = EPS_T_TRANSFER.PR_NO
               where
                EPS_T_PR_HEADER.PR_NO = '$prNoPrm'";
    $sql2 = $conn->query($query2);
    $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
    $prNo           = $row2['PR_NO'];
    $issuedDate     = $row2['ISSUED_DATE'];
    $requesterName  = $row2['REQUESTER_NAME'];
    $extNo          = $row2['EXT_NO'];
    $purpose        = $row2['PURPOSE'];
    
    $htmlTableHeader .= "<tr>
                    <td>
                        $requesterName
                    </td>
                    <td>
                        $issuedDate
                    </td>
                    <td width='10%'>
                        $extNo
                    </td>
                    <td width='70%'>
                        $purpose
                    </td>";
    $htmlTableHeader .= "</tr>
                </tbody>
              </table>";
    $htmlTable      = 
                "<table class='table table-striped table-bordered' id='table-pritem'>
                        <thead>
                            <tr>
                                <th colspan='11'>PR DETAIL</th>
                            </tr>
                            <tr>
                                <th>CODE</th>
                                <th>NAME</th>
                                <th>DUE DATE</th>
                                <th>EXP</th>
                                <th>RFI</th>
                                <th>QTY</th>
                                <th>UM</th>
                                <th>UNIT PRICE</th>
                                <th>SUPPLIER</th>
                                <th>AMOUNT</th>
                                <th>REMARK</th>
                            </tr>
                        </thead>
                        <tbody>";
    $query = "select 
                EPS_T_PR_DETAIL.ITEM_CD
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
                ,EPS_T_PR_HEADER.REQUESTER
            from 
                EPS_T_PR_DETAIL
            left join
                EPS_T_TRANSFER
            on
                EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO
                and replace(replace(replace(EPS_T_PR_DETAIL.ITEM_NAME, char(13), ''), char(9), ''), ' ', '') = replace(EPS_T_TRANSFER.ITEM_NAME, ' ', '')
            left join
                EPS_T_PR_HEADER
            on
                EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
            where
                EPS_T_PR_DETAIL.PR_NO = '$prNoPrm'
                and EPS_T_PR_DETAIL.ITEM_NAME = '$itemNamePrm'";
                
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $itemCd         = $row['ITEM_CD'];
        $itemName       = trim($row['ITEM_NAME']);
        $deliveryDate   = $row['DELIVERY_DATE'];
        $qty            = $row['QTY'];
        $itemPrice      = number_format($row['ITEM_PRICE']);
        $amount         = number_format($row['AMOUNT']);
        $currencyCd     = $row['CURRENCY_CD'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountCd      = $row['ACCOUNT_NO'];
        $rfiNo          = $row['RFI_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = trim($row['SUPPLIER_NAME']);
        $remark         = $row['REMARK'];
        
        if(strlen($accountCd) == 1 && trim($accountCd) !='')
        {
            $accountCd = "0".$accountCd;
        }
        $htmlTable .= "<tr>
                    <td>
                        $itemCd
                    </td>
                    <td>
                        $itemName
                    </td>
                    <td>
                        $deliveryDate
                    </td>
                    <td>
                        $accountCd
                    </td>
                    <td>
                        $rfiNo
                    </td>
                    <td style='text-align: right'>
                        $qty
                    </td>
                    <td>
                        $unitCd
                    </td>
                    <td style='text-align: right'>
                        $itemPrice
                    </td>
                    <td>
                        $supplierName
                    </td>
                    <td style='text-align: right'>
                        $amount
                    </td>
                    <td>
                        $remark
                    </td>
                </tr>";
    }
    $htmlTable      .= "
            </tbody>
        </table>";
    $htmlTableApprover  = 
                    "<table class='table table-striped table-bordered' id='table-pr-approver'>
                        <thead>
                            <tr>
                                <th colspan='11'>PR APPROVER</th>
                            </tr>
                            <tr>
                                <th>NO</th>
                                <th>NAME</th>
                                <th>APPROVAL DATE</th>
                                <th>STATUS</th>
                                <th>REMARK</th>
                            </tr>
                        </thead>
                        <tbody>";
    $query_select_t_pr_approver = "select 
                                    EPS_T_PR_APPROVER.PR_NO
                                    ,EPS_T_PR_APPROVER.BU_CD
                                    ,EPS_T_PR_APPROVER.APPROVER_NO
                                    ,EPS_T_PR_APPROVER.NPK
                                    ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                    ,EPS_T_PR_APPROVER.APPROVAL_STATUS
                                    ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                    ,convert(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                                    ,EPS_T_PR_APPROVER.APPROVAL_REMARK
                                    ,EPS_T_PR_APPROVER.DATE_OF_BYPASS
                                    ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                                   from 
                                     EPS_T_PR_APPROVER 
                                   left join
                                     EPS_M_EMPLOYEE
                                   on
                                     EPS_T_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                   left join
                                     EPS_M_APPROVAL_STATUS
                                   on
                                     EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
                                   left join
                                     EPS_T_PR_HEADER 
                                   on 
                                     EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                   where 
                                     EPS_T_PR_APPROVER.PR_NO ='".$prNoPrm."'
                                   order by
                                     EPS_T_PR_APPROVER.APPROVER_NO 
                                   asc ";
    $sql_select_t_pr_approver = $conn->query($query_select_t_pr_approver);
    while($row_select_t_pr_approver = $sql_select_t_pr_approver->fetch(PDO::FETCH_ASSOC)){
        $approverNo         = $row_select_t_pr_approver['APPROVER_NO'];
        $buCd               = $row_select_t_pr_approver['BU_CD'];
        $npk                = $row_select_t_pr_approver['NPK'];
        $approverName       = stripslashes($row_select_t_pr_approver['APPROVER_NAME']);
        $approvalStatus     = $row_select_t_pr_approver['APPROVAL_STATUS'];
        $approvalStatusName = $row_select_t_pr_approver['APPROVAL_STATUS_NAME'];
        $approvalDate       = $row_select_t_pr_approver['APPROVAL_DATE'];
        $approvalRemark     = $row_select_t_pr_approver['APPROVAL_REMARK'];
        $dateByPass         = $row_select_t_pr_approver['DATE_OF_BYPASS'];
        $specialType        = $row_select_t_pr_approver['SPECIAL_TYPE_ID'];
        if(strlen(trim($approvalDate)) != 0){
            date_default_timezone_set('Asia/Jakarta');
            $approvalDate   = date("d/m/Y H:i:s A", strtotime($approvalDate));
        }
        if(strlen(trim($dateByPass)) != 0){
            date_default_timezone_set('Asia/Jakarta');
            if(strlen($dateByPass) == 22){
                $newMonth = substr($dateByPass,0,2);
                $newDate = substr($dateByPass,3,2).'/';
                $newYear = substr($dateByPass,5);
            }
            if(strlen($dateByPass) == 21){
                if(substr($dateByPass,0,1) != 0){
                    $newMonth = '0'.substr($dateByPass,0,1).'/';
                }
                $newDate = substr($dateByPass,2,2).'/';
                $newYear = substr($dateByPass,5);
            }
            if(strlen($dateByPass) == 20){
                if(substr($dateByPass,0,1) != 0){
                    $newMonth = '0'.substr($dateByPass,0,1).'/';
                }
                $newDate = '0'.substr($dateByPass,2,1).'/';
                $newYear = substr($dateByPass,4);
            }   
                                                
            $dateByPass = $newDate.$newMonth.$newYear;
            $approvalDate = $dateByPass;
        }
        $htmlTableApprover .= "<tr>
                                    <td>
                                        $approverNo
                                    </td>
                                    <td>
                                        $approverName
                                    </td>
                                    <td>
                                        $approvalDate
                                    </td>
                                    <td>
                                        $approvalStatusName
                                    </td>
                                    <td>
                                        $approvalRemark
                                    </td>
                              </tr>";
    }
    
    $htmlTableApprover .= "
                        </tbody>
                 </table>";
}

echo $htmlTable.$htmlTableHeader.$htmlTableApprover; 
?>
