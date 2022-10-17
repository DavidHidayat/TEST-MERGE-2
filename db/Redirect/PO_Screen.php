<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
if(isset($_SESSION['sUserId']))
{     
    $sUserId    = $_SESSION['sUserId'];
    $sNPK       = $_SESSION['sNPK'];
    $sRoleId    = $_SESSION['sRoleId'];
    
    if($sUserId != ''){
        $criteria   = $_GET['criteria'];
        $paramPrNo  = $_GET['paramPrNo'];
        $transferId = $_GET['transferId'];

        /**************************************************************
        * PR WAITING
        **************************************************************/
        if($criteria == 'prDetail')
        {
            $query = "select 
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
                        ,EPS_M_ITEM_PRICE.ITEM_CATEGORY
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
                    left join
                        EPS_M_ITEM_PRICE
                    on
                        EPS_T_PR_DETAIL.ITEM_CD = EPS_M_ITEM_PRICE.ITEM_CD 
                    where 
                        EPS_T_PR_HEADER.PR_NO ='".$paramPrNo."'
                    and 
                        (EPS_T_PR_DETAIL.ITEM_STATUS = '1060') 
                    order by 
                        INDEX_ITEM_NAME, EPS_T_PR_DETAIL.ITEM_NAME";
            //echo $query;
            $sql = $conn->query($query);
            $i = 0;
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $prStatus            = $row['PR_STATUS'];
                $procInCharge        = $row['PROC_IN_CHARGE'];
                $prNo                = $row['PR_NO'];
                $itemCd              = $row['ITEM_CD'];
                $itemName            = trim($row['ITEM_NAME']);
                $itemCategory        = $row['ITEM_CATEGORY'];
                $deliveryDate        = $row['DELIVERY_DATE'];
                $qty                 = $row['QTY'];
                $itemPrice           = $row['ITEM_PRICE'];
                $amount              = $row['AMOUNT'];
                $currencyCd          = $row['CURRENCY_CD'];
                $itemType            = $row['ITEM_TYPE_CD'];
                $accountCd           = $row['ACCOUNT_NO'];
                $rfiNo               = $row['RFI_NO'];
                $unitCd              = $row['UNIT_CD'];
                $supplierCd          = $row['SUPPLIER_CD'];
                $supplierName        = trim($row['SUPPLIER_NAME']);
                $remark              = $row['REMARK'];
                $itemStatus          = $row['ITEM_STATUS'];
                $itemStatusAlias     = trim($row['APP_STATUS_ALIAS']);
                $reasonToReject      = $row['REASON_TO_REJECT_ITEM'];
                $rejectItemBy        = $row['REJECT_ITEM_BY'];
                $rejectItemNameBy    = $row['REJECT_ITEM_NAME_BY'];
                $attachmentItemCount = $row['ATTACHMENT_ITEM_COUNT'];
                $procPrUpdateDate    = $row['UPDATE_DATE'];
                $itemNameSet         = str_replace(array("\n", "\r"), '', $itemNameSet);

                if($itemStatus == '1060'){
                    $itemStatus = '1110';
                }
                if($itemStatusAlias == 'APP'){
                    $itemStatusAlias = 'WAI';
                }
                if(strlen(trim($accountCd)) == 1)
                {
                    $accountCd = '0'.$accountCd;
                }
                $prDetail[] = array(
                                  'itemCd'              => $itemCd
                                , 'itemName'            => $itemName
                                , 'remark'              => $remark
                                , 'deliveryDate'        => $deliveryDate
                                , 'itemType'            => $itemType
                                , 'itemCategory'        => $itemCategory
                                , 'rfiNo'               => $rfiNo
                                , 'accountNo'           => $accountCd
                                , 'supplierCd'          => $supplierCd
                                , 'supplierName'        => $supplierName
                                , 'unitCd'              => $unitCd
                                , 'qty'                 => $qty
                                , 'itemPrice'           => $itemPrice
                                , 'amount'              => $amount
                                , 'currencyCd'          => $currencyCd
                                , 'itemStatus'          => $itemStatus
                                , 'prNo'                => $prNo
                                , 'refItemName'         => $itemName
                                , 'seqItem'             => $i
                                , 'itemStatusAlias'     => $itemStatusAlias
                                , 'attachmentItemCount' => $attachmentItemCount
                            );
                $i++;
            }
//            echo $paramPrNo."<br/>";
//            echo $prNo."<br/>";
            $redirectPage = "../../epo/WEPO002_.php?paramPrNo=".$prNo;
            $_SESSION['prNoSession']	= $prNo;
            $_SESSION['prDetail']   	= $prDetail;
            $_SESSION['prScreen']   	= 'DetailPrScreen';
            $_SESSION['prStatus']   	= $prStatus;
            $_SESSION['procInCharge']	= $procInCharge;
            $_SESSION['procPrUpdateDate']   = $procPrUpdateDate;
            
        }
		/**************************************************************
         * PR IN CHARGE
         **************************************************************/
        if($criteria == 'prInCharge')
        {
            $query_t_pr_header_proc = "select
                                        EPS_T_PR_HEADER.PR_STATUS
                                        ,EPS_T_PR_HEADER.UPDATE_DATE
                                        ,EPS_T_PR_HEADER.PROC_IN_CHARGE
                                       from
                                        EPS_T_PR_HEADER
                                       where
                                        PR_NO = '$paramPrNo'";
            $sql_t_pr_header_proc = $conn->query($query_t_pr_header_proc);
            $row_t_pr_header_proc = $sql_t_pr_header_proc->fetch(PDO::FETCH_ASSOC);
            $prStatus           = $row_t_pr_header_proc['PR_STATUS'];
            $procPrUpdateDate   = $row_t_pr_header_proc['UPDATE_DATE'];
            
            $redirectPage = "../../epo/WEPO016.php?paramPrNo=".$paramPrNo;
            $_SESSION['prNoSession']        = $paramPrNo;
            $_SESSION['prScreen']           = 'EditWaitingPrScreen';
            $_SESSION['prStatus']           = $prStatus;
            $_SESSION['procInCharge']       = $procInCharge;
            $_SESSION['procPrUpdateDate']   = $procPrUpdateDate;
        }
        /**************************************************************
        * OUTSTANDING PO
        **************************************************************/
        if($criteria == 'outstandingPo')
        {
			$query_select_t_transfer = "select 
                                            ITEM_STATUS
                                            ,UPDATE_DATE
                                        from
                                            EPS_T_TRANSFER
                                        where
                                            TRANSFER_ID = '$transferId'";
            $sql_select_t_transfer = $conn->query($query_select_t_transfer);
            $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
            $itemStatus = $row_select_t_transfer['ITEM_STATUS'];
            $updateDate = $row_select_t_transfer['UPDATE_DATE'];
			
            $query = "select 
                        EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID
                        ,EPS_T_TRANSFER_SUPPLIER.SUPPLIER_CD
                        ,EPS_M_SUPPLIER.SUPPLIER_NAME
                        ,EPS_T_TRANSFER_SUPPLIER.CURRENCY_CD
                        ,EPS_T_TRANSFER_SUPPLIER.ITEM_PRICE
                        ,EPS_T_TRANSFER_SUPPLIER.LEAD_TIME
                        ,EPS_T_TRANSFER_SUPPLIER.UNIT_TIME
                        ,EPS_T_TRANSFER_SUPPLIER.ATTACHMENT_LOC
                        ,EPS_T_TRANSFER_SUPPLIER.ATTACHMENT_CIP
                        ,EPS_T_TRANSFER_SUPPLIER.REMARK
                    from 
                        EPS_T_TRANSFER_SUPPLIER
                    left join
                        EPS_M_SUPPLIER
                    on
                        EPS_T_TRANSFER_SUPPLIER.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                    where 
                        TRANSFER_ID ='".$transferId."'";
            $sql = $conn->query($query);
            $seqSupplier = 1;
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $transferId     = $row['TRANSFER_ID'];
                $supplierCd     = $row['SUPPLIER_CD'];
                $supplierName   = $row['SUPPLIER_NAME'];
                $currencyCd     = $row['CURRENCY_CD'];
                $leadTime       = $row['LEAD_TIME'];
                $unitTime       = $row['UNIT_TIME'];
                $itemPrice      = $row['ITEM_PRICE'];
                $attachmentLoc  = $row['ATTACHMENT_LOC'];
                $attachmentCip  = $row['ATTACHMENT_CIP'];
                $remark         = $row['REMARK'];

                $transferSupplier[] = array(
                                        'transferId'=> $transferId
                                        ,'supplierCd'=> $supplierCd
                                        ,'supplierName'=> $supplierName
                                        ,'currencyCd' => $currencyCd
                                        ,'itemPrice'=> $itemPrice
                                        ,'leadTime'=> $leadTime
                                        ,'unitTime'=> $unitTime
                                        ,'attachmentLoc'=> $attachmentLoc
                                        ,'attachmentCip'=> $attachmentCip
                                        ,'remark'=> $remark
                                        ,'seqSupplier'=> $seqSupplier
                                    );
                $seqSupplier++;
            }
            $redirectPage = "../../epo/WEPO011.php?transferId=".$transferId;
            $_SESSION['transferSupplier']   = $transferSupplier;
            $_SESSION['transferIdSession']  = $transferId;
            $_SESSION['poScreen']           = 'OutstandingPoDetailScreen';
            $_SESSION['itemStatusSession']  = $itemStatus;
            $_SESSION['updateDateSession']  = $updateDate;
        }
        /**************************************************************
        * WAITING GENERATE PO NO
        **************************************************************/
        if($criteria == 'waitingGeneratePoNo')
        {
			$query_select_t_transfer = "select 
                                            ITEM_STATUS
                                        from
                                            EPS_T_TRANSFER
                                        where
                                            TRANSFER_ID = '$transferId'";
            $sql_select_t_transfer = $conn->query($query_select_t_transfer);
            $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
            $itemStatus = $row_select_t_transfer['ITEM_STATUS'];
			
            $redirectPage = "../../epo/WEPO010.php?transferId=".$transferId;
            $_SESSION['transferIdSession']  = $transferId;
            $_SESSION['poScreen']           = 'GeneratePoDetailScreen';
            $_SESSION['itemStatusSession']  = $itemStatus;
        }
        /**************************************************************
        * PO LIST
        **************************************************************/
        if($criteria == 'poDetail')
        {
            $paramPoNo  = $_GET['paramPoNo'];

            $query_po_header = "select 
                                    EPS_T_PO_HEADER.PO_STATUS
                                    ,EPS_T_PO_HEADER.APPROVER
                                    ,EPS_T_PO_HEADER.ISSUED_BY
                                    ,EPS_M_EMPLOYEE.PLANT
                                    ,isnull(
                                        (select 
                                            sum(TRANSACTION_QTY)
                                        from         
                                            EPS_T_RO_DETAIL
                                        where     
                                            EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO 
                                            and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO)
                                    ,0) 
                                    as TOTAL_QTY
                                from
                                    EPS_T_PO_HEADER
                                left join
                                    EPS_M_EMPLOYEE
                                on
                                    EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                                where
                                    EPS_T_PO_HEADER.PO_NO = '$paramPoNo'";
            $sql_po_header = $conn->query($query_po_header);
            while($row_po_header = $sql_po_header->fetch(PDO::FETCH_ASSOC)){
                $poStatus   = $row_po_header['PO_STATUS'];
                $poApprover = $row_po_header['APPROVER'];
                $issuedBy   = $row_po_header['ISSUED_BY'];
                $issuedPlant= $row_po_header['PLANT'];
                $totalQty   = $row_po_header['TOTAL_QTY'];
            }
            $_SESSION['poNoSession'] = $paramPoNo;

            if($poStatus == constant('1210') && $issuedBy == $sUserId)
            {
                $query = "select 
                        EPS_T_PO_DETAIL.PO_NO
                        ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                        ,EPS_T_PO_DETAIL.ITEM_CD
                        ,EPS_T_PO_DETAIL.ITEM_NAME
                        ,EPS_T_PO_DETAIL.QTY
                        ,EPS_T_PO_DETAIL.ITEM_PRICE
                        ,EPS_T_PO_DETAIL.AMOUNT
                        ,EPS_T_PO_HEADER.CURRENCY_CD
                        ,EPS_T_PO_DETAIL.UNIT_CD
                        ,EPS_T_PO_DETAIL.ITEM_TYPE_CD
                        ,EPS_T_PO_DETAIL.ACCOUNT_NO
                        ,EPS_T_PO_DETAIL.RFI_NO
                        ,(select count(*)
                        from          
                            EPS_T_TRANSFER_SUPPLIER
                        where      
                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                        as TOTAL_SUPPLIER
                        ,EPS_T_TRANSFER.ITEM_STATUS
                        ,EPS_T_TRANSFER.PR_NO
                        ,EPS_T_TRANSFER.NEW_QTY
                        ,EPS_T_PR_DETAIL.ITEM_PRICE AS PR_ITEM_PRICE
                        ,EPS_T_PO_DETAIL.ATTACHMENT AS CIP
                        ,EPS_M_ITEM_PRICE.ITEM_CATEGORY
                    from 
                        EPS_T_PO_DETAIL
                    left join
                        EPS_T_PO_HEADER
                    on 
                        EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                    left join
                        EPS_T_TRANSFER_SUPPLIER 
                    on 
                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID 
                        and EPS_T_PO_HEADER.SUPPLIER_CD = EPS_T_TRANSFER_SUPPLIER.SUPPLIER_CD
                    left join
                        EPS_T_TRANSFER 
                    on 
                        EPS_T_TRANSFER.TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                    left join
                        EPS_T_PR_DETAIL 
                    on 
                        EPS_T_TRANSFER.PR_NO = EPS_T_PR_DETAIL.PR_NO
                        and (REPLACE(REPLACE(REPLACE(EPS_T_TRANSFER.ITEM_NAME, CHAR(13), ''), CHAR(9), ''), ' ', '') = REPLACE(EPS_T_PR_DETAIL.ITEM_NAME , ' ', ''))
                    left join
                        EPS_M_ITEM_PRICE
                    on
                        EPS_T_PO_DETAIL.ITEM_CD = EPS_M_ITEM_PRICE.ITEM_CD 
                    where 
                        EPS_T_PO_DETAIL.PO_NO ='".$paramPoNo."'";
                $sql = $conn->query($query);
                $seqPoItem=1;
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $poNo           = $row['PO_NO'];
                    $refTransferId  = $row['REF_TRANSFER_ID'];
                    $itemCd         = $row['ITEM_CD'];
                    $itemName       = $row['ITEM_NAME'];
                    $itemCategory   = $row['ITEM_CATEGORY'];
                    $qty            = $row['QTY'];
                    $itemPrice      = $row['ITEM_PRICE'];
                    $amount         = $row['AMOUNT'];
                    $currencyCd     = $row['CURRENCY_CD'];
                    $unitCd         = $row['UNIT_CD'];
                    $totalSupplier  = $row['TOTAL_SUPPLIER'];
                    $itemStatus     = $row['ITEM_STATUS'];
                    $prNo           = $row['PR_NO'];
                    $initialQty     = $row['NEW_QTY'];
                    $itemTypeCd     = $row['ITEM_TYPE_CD'];
                    $accountNo      = $row['ACCOUNT_NO'];
                    $rfiNo          = $row['RFI_NO'];
                    $prItemPrice    = $row['PR_ITEM_PRICE'];
                    $cip            = $row['CIP'];

                    $poDetail[] = array(
                                            'poNo'=> $poNo
                                            ,'refTransferId'=> $refTransferId
                                            ,'itemCd'=> $itemCd
                                            ,'itemName' => $itemName
                                            ,'itemCategory' => $itemCategory
                                            ,'qty'=> $qty
                                            ,'itemPrice'=> $itemPrice
                                            ,'amount'=> $amount
                                            ,'unitCd'=> $unitCd
                                            ,'currencyCd'=> $currencyCd
                                            ,'seqPoItem'=> $seqPoItem
                                            ,'totalSupplier'=> $totalSupplier
                                            ,'itemStatus'=> $itemStatus
                                            ,'prNo'=> $prNo
                                            ,'initialQty'=> $initialQty
                                            ,'itemTypeCd'=> $itemTypeCd
                                            ,'accountNo'=> $accountNo
                                            ,'rfiNo'=> $rfiNo
                                            ,'prItemPrice'=> $prItemPrice
                                            ,'cip' => $cip
                                        );
                    $seqPoItem++;
                }
                $redirectPage = "../../epo/WEPO006.php?paramPoNo=".$paramPoNo;
                $_SESSION['poDetail']   = $poDetail;
                $_SESSION['poScreen']   = 'CreatePoScreen';
                $_SESSION['poStatus']   = $poStatus;
            }
            else if($poStatus == constant('1220') && trim($poApprover) == trim($sUserId))
            {
                $redirectPage = "../../epo/WEPO007.php?paramPoNo=".$paramPoNo;
                $_SESSION['poScreen']   = 'ApprovalPoScreen';
                $_SESSION['poStatus']   = $poStatus;
            }
            /*else if($poStatus == constant('1240') && $issuedBy == $sUserId)
            {
                $query = "select 
                        EPS_T_PO_DETAIL.PO_NO
                        ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                        ,EPS_T_PO_DETAIL.ITEM_CD
                        ,EPS_T_PO_DETAIL.ITEM_NAME
                        ,EPS_T_PO_DETAIL.QTY
                        ,EPS_T_PO_DETAIL.ITEM_PRICE
                        ,EPS_T_PO_DETAIL.AMOUNT
                        ,EPS_T_PO_HEADER.CURRENCY_CD
                        ,EPS_T_PO_DETAIL.UNIT_CD
                        ,(select count(*)
                        from          
                            EPS_T_TRANSFER_SUPPLIER
                        where      
                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                        as TOTAL_SUPPLIER
                        ,EPS_T_TRANSFER.ITEM_STATUS
                    from 
                        EPS_T_PO_DETAIL
                    left join
                        EPS_T_PO_HEADER
                    on 
                        EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                    left join
                        EPS_T_TRANSFER_SUPPLIER 
                    on 
                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID 
                        and EPS_T_PO_HEADER.SUPPLIER_CD = EPS_T_TRANSFER_SUPPLIER.SUPPLIER_CD
                    left join
                        EPS_T_TRANSFER 
                    on 
                        EPS_T_TRANSFER.TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                    where 
                        EPS_T_PO_DETAIL.PO_NO ='".$paramPoNo."'";
                $sql = $conn->query($query);
                $seqPoItem=1;
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $poNo           = $row['PO_NO'];
                    $refTransferId  = $row['REF_TRANSFER_ID'];
                    $itemCd         = $row['ITEM_CD'];
                    $itemName       = $row['ITEM_NAME'];
                    $qty            = $row['QTY'];
                    $itemPrice      = $row['ITEM_PRICE'];
                    $amount         = $row['AMOUNT'];
                    $currencyCd     = $row['CURRENCY_CD'];
                    $unitCd         = $row['UNIT_CD'];
                    $totalSupplier  = $row['TOTAL_SUPPLIER'];
                    $itemStatus     = $row['ITEM_STATUS'];

                    $poDetail[] = array(
                                            'poNo'=> $poNo
                                            ,'refTransferId'=> $refTransferId
                                            ,'itemCd'=> $itemCd
                                            ,'itemName' => $itemName
                                            ,'qty'=> $qty
                                            ,'itemPrice'=> $itemPrice
                                            ,'amount'=> $amount
                                            ,'unitCd'=> $unitCd
                                            ,'currencyCd'=> $currencyCd
                                            ,'seqPoItem'=> $seqPoItem
                                            ,'totalSupplier'=> $totalSupplier
                                            ,'itemStatus'=> $itemStatus
                                        );
                    $seqPoItem++;
                }
                $redirectPage = "../../epo/WEPO014.php?paramPoNo=".$paramPoNo;
                $_SESSION['poDetail']   = $poDetail;
                $_SESSION['poScreen']   = 'EditPoScreen';
            }*/
            else if( ($poStatus == constant('1250') && $issuedBy == $sUserId)
                        || ($poStatus == constant('1250') && $totalQty == 0
                                && ($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06'))
                   )
            {
                $query = "select 
                        EPS_T_PO_DETAIL.PO_NO
                        ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                        ,EPS_T_PO_DETAIL.ITEM_CD
                        ,EPS_T_PO_DETAIL.ITEM_NAME
                        ,EPS_T_PO_DETAIL.QTY
                        ,EPS_T_PO_DETAIL.ITEM_PRICE
                        ,EPS_T_PO_DETAIL.AMOUNT
                        ,EPS_T_PO_HEADER.CURRENCY_CD
                        ,EPS_T_PO_DETAIL.UNIT_CD
                        ,EPS_T_PO_DETAIL.ITEM_TYPE_CD
                        ,EPS_T_PO_DETAIL.ACCOUNT_NO
                        ,EPS_T_PO_DETAIL.RFI_NO
                        ,(select count(*)
                        from          
                            EPS_T_TRANSFER_SUPPLIER
                        where      
                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                        as TOTAL_SUPPLIER
                        ,EPS_T_TRANSFER.ITEM_STATUS
                        ,EPS_T_TRANSFER.PR_NO
                        ,EPS_M_APP_STATUS.APP_STATUS_NAME as ITEM_STATUS_NAME
                        ,(select 
                            count(*)
                          from
                            EPS_T_RO_DETAIL
                          where
                            EPS_T_PO_DETAIL.PO_NO = EPS_T_RO_DETAIL.PO_NO
                            and EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_RO_DETAIL.REF_TRANSFER_ID)
                        as COUNT_RECEIVING
                        ,EPS_T_PO_HEADER.UPDATE_DATE
                    from 
                        EPS_T_PO_DETAIL
                    left join
                        EPS_T_PO_HEADER
                    on 
                        EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                    left join
                        EPS_T_TRANSFER_SUPPLIER 
                    on 
                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID 
                        and EPS_T_PO_HEADER.SUPPLIER_CD = EPS_T_TRANSFER_SUPPLIER.SUPPLIER_CD
                    left join
                        EPS_T_TRANSFER 
                    on 
                        EPS_T_TRANSFER.TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                    left join
                        EPS_M_APP_STATUS
                    on
                        EPS_T_PO_DETAIL.RO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                    where 
                        EPS_T_PO_DETAIL.PO_NO ='".$paramPoNo."'";
                $sql = $conn->query($query);
                $seqPoItem=1;
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $poNo           = $row['PO_NO'];
                    $refTransferId  = $row['REF_TRANSFER_ID'];
                    $itemCd         = $row['ITEM_CD'];
                    $itemName       = $row['ITEM_NAME'];
                    $qty            = $row['QTY'];
                    $itemPrice      = $row['ITEM_PRICE'];
                    $amount         = $row['AMOUNT'];
                    $currencyCd     = $row['CURRENCY_CD'];
                    $unitCd         = $row['UNIT_CD'];
                    $totalSupplier  = $row['TOTAL_SUPPLIER'];
                    $itemStatus     = $row['ITEM_STATUS'];
                    $itemStatusName = $row['ITEM_STATUS_NAME'];
                    $prNo           = $row['PR_NO'];
                    $countReceiving = $row['COUNT_RECEIVING'];
                    $poHeaderUpdateDate = $row['UPDATE_DATE'];
                    $itemTypeCd     = $row['ITEM_TYPE_CD'];
                    $accountNo      = $row['ACCOUNT_NO'];
                    $rfiNo          = $row['RFI_NO'];

                    $poDetail[] = array(
                                            'poNo'=> $poNo
                                            ,'refTransferId'=> $refTransferId
                                            ,'itemCd'=> $itemCd
                                            ,'itemName' => $itemName
                                            ,'qty'=> $qty
                                            ,'itemPrice'=> $itemPrice
                                            ,'amount'=> $amount
                                            ,'unitCd'=> $unitCd
                                            ,'currencyCd'=> $currencyCd
                                            ,'seqPoItem'=> $seqPoItem
                                            ,'totalSupplier'=> $totalSupplier
                                            ,'itemStatus'=> $itemStatus
                                            ,'itemStatusName'=> $itemStatusName
                                            ,'prNo'=> $prNo
                                            ,'countReceiving'=> $countReceiving
                                            ,'itemTypeCd'=> $itemTypeCd
                                            ,'accountNo'=> $accountNo
                                            ,'rfiNo'=> $rfiNo
                                        );
                    $seqPoItem++;
                }
                $redirectPage = "../../epo/WEPO015.php?paramPoNo=".$paramPoNo;
				$_SESSION['poDetail']           = $poDetail;
                $_SESSION['poScreen']           = 'EditPoAfterSentScreen';
                $_SESSION['poStatus']           = $poStatus;
                $_SESSION['poHeaderUpdateDate'] = $poHeaderUpdateDate;
				/*$redirectPage = "../../epo/WEPO009.php?paramPoNo=".$paramPoNo;
				$_SESSION['poScreen']   = 'DetailPoScreen';*/
            }
			else if($poStatus == constant('1280') && ($sRoleId == 'ROLE_03' || $sNPK == " 871002" || $sNPK == " 891016"))
            {
                $query = "select 
                        EPS_T_PO_DETAIL.PO_NO
                        ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                        ,EPS_T_PO_DETAIL.ITEM_CD
                        ,EPS_T_PO_DETAIL.ITEM_NAME
                        ,EPS_T_PO_DETAIL.QTY
                        ,EPS_T_PO_DETAIL.ITEM_PRICE
                        ,EPS_T_PO_DETAIL.AMOUNT
                        ,EPS_T_PO_HEADER.CURRENCY_CD
                        ,EPS_T_PO_DETAIL.UNIT_CD
                        ,EPS_T_PO_DETAIL.ITEM_TYPE_CD
                        ,EPS_T_PO_DETAIL.ACCOUNT_NO
                        ,EPS_T_PO_DETAIL.RFI_NO
                        ,(select count(*)
                        from          
                            EPS_T_TRANSFER_SUPPLIER
                        where      
                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                        as TOTAL_SUPPLIER
                        ,EPS_T_TRANSFER.ITEM_STATUS
                        ,EPS_T_TRANSFER.PR_NO
                        ,EPS_M_APP_STATUS.APP_STATUS_NAME as ITEM_STATUS_NAME
                        ,(select 
                            count(*)
                          from
                            EPS_T_RO_DETAIL
                          where
                            EPS_T_PO_DETAIL.PO_NO = EPS_T_RO_DETAIL.PO_NO
                            and EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_RO_DETAIL.REF_TRANSFER_ID)
                        as COUNT_RECEIVING
                        ,EPS_T_PO_HEADER.UPDATE_DATE
                    from 
                        EPS_T_PO_DETAIL
                    left join
                        EPS_T_PO_HEADER
                    on 
                        EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                    left join
                        EPS_T_TRANSFER_SUPPLIER 
                    on 
                        EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID 
                        and EPS_T_PO_HEADER.SUPPLIER_CD = EPS_T_TRANSFER_SUPPLIER.SUPPLIER_CD
                    left join
                        EPS_T_TRANSFER 
                    on 
                        EPS_T_TRANSFER.TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                    left join
                        EPS_M_APP_STATUS
                    on
                        EPS_T_PO_DETAIL.RO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                    where 
                        EPS_T_PO_DETAIL.PO_NO ='".$paramPoNo."'";
                $sql = $conn->query($query);
                $seqPoItem=1;
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $poNo           = $row['PO_NO'];
                    $refTransferId  = $row['REF_TRANSFER_ID'];
                    $itemCd         = $row['ITEM_CD'];
                    $itemName       = $row['ITEM_NAME'];
                    $qty            = $row['QTY'];
                    $itemPrice      = $row['ITEM_PRICE'];
                    $amount         = $row['AMOUNT'];
                    $currencyCd     = $row['CURRENCY_CD'];
                    $unitCd         = $row['UNIT_CD'];
                    $totalSupplier  = $row['TOTAL_SUPPLIER'];
                    $itemStatus     = $row['ITEM_STATUS'];
                    $itemStatusName = $row['ITEM_STATUS_NAME'];
                    $prNo           = $row['PR_NO'];
                    $countReceiving = $row['COUNT_RECEIVING'];
                    $poHeaderUpdateDate = $row['UPDATE_DATE'];
                    $itemTypeCd     = $row['ITEM_TYPE_CD'];
                    $accountNo      = $row['ACCOUNT_NO'];
                    $rfiNo          = $row['RFI_NO'];

                    $poDetail[] = array(
                                            'poNo'=> $poNo
                                            ,'refTransferId'=> $refTransferId
                                            ,'itemCd'=> $itemCd
                                            ,'itemName' => $itemName
                                            ,'qty'=> $qty
                                            ,'itemPrice'=> $itemPrice
                                            ,'amount'=> $amount
                                            ,'unitCd'=> $unitCd
                                            ,'currencyCd'=> $currencyCd
                                            ,'seqPoItem'=> $seqPoItem
                                            ,'totalSupplier'=> $totalSupplier
                                            ,'itemStatus'=> $itemStatus
                                            ,'itemStatusName'=> $itemStatusName
                                            ,'prNo'=> $prNo
                                            ,'countReceiving'=> $countReceiving
                                            ,'itemTypeCd'=> $itemTypeCd
                                            ,'accountNo'=> $accountNo
                                            ,'rfiNo'=> $rfiNo
                                        );
                    $seqPoItem++;
                }
                
                $redirectPage = "../../epo/WEPO017.php?paramPoNo=".$paramPoNo;
                $_SESSION['poDetail']           = $poDetail;
                $_SESSION['poScreen']           = 'EditPoAfterClosedScreen';
                $_SESSION['poStatus']           = $poStatus;
                $_SESSION['poHeaderUpdateDate'] = $poHeaderUpdateDate;
            }
            else
            {
                $redirectPage = "../../epo/WEPO009.php?paramPoNo=".$paramPoNo;
                $_SESSION['poScreen']   = 'DetailPoScreen';
            }

        }
        //echo $redirectPage;
        echo "<script>document.location.href='".$redirectPage."';</script>";
    }
?>
    <script language="javascript"> alert("Sorry, your session to EPS has expired. Please login again.");
     document.location="../Login/Logout.php"; </script>
<?php
}
?>
<script language="javascript"> alert("Sorry, you are has not login. Please login first.");
 document.location="../Login/Logout.php"; </script>