<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/CONTROLLER/PAGING.php";
if(isset($_SESSION['sUserId']))
{  
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag	= $_SESSION['sactiveFlag'];
    $sActiveFlagLogin	= $_SESSION['sactiveFlagLogin'];
    
    if($sUserId != '' && $sActiveFlag == 'A' && $sActiveFlagLogin == 'A')
    {
        unset($_SESSION['roStatus']);
        
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
        
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_05' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_11')
        {
            
            if($sKdPlant == 0)
            {
                $plantCdAlias  = "JK";
            }
            else if($sKdPlant == 7)
            {
                $plantCdAlias  = "GT";
            }
            else 
            {
                if($sKdPlant == 5)
                {
                    $plantCdAlias  = "JF";
                }
            }
           /**
            * Search EPS_M_PR_PROC_APPROVER
            */
            $query_select_m_pr_proc_app = "select
                                            distinct NPK
                                        from
                                            EPS_M_PR_PROC_APPROVER
                                        where
                                            PLANT_CD = '$sKdPlant'";
            $sql_select_m_pr_proc_app = $conn->query($query_select_m_pr_proc_app);
            while($row_select_m_pr_proc_app = $sql_select_m_pr_proc_app->fetch(PDO::FETCH_ASSOC)){	
                    $npkProc = $row_select_m_pr_proc_app['NPK'];
                    $npkProcArray[] = array(
                                    'npkProc' => $npkProc
                                );
                $addNpkProcArray = $npkProcArray;
            }
            $indexArray = 1;
            foreach($addNpkProcArray as $addNpkProcArrays)
            {
                $npkProcVal = $addNpkProcArrays['npkProc'];
                if($indexArray == 1)
                {
                    $npkProcCriteria = "'".$npkProcVal."'";
                }
                else
                {
                    $npkProcCriteria = $npkProcCriteria.",'".$npkProcVal."'";
                }
                $indexArray ++;
            }
        }
        else
        {
        ?>
            <script language="javascript"> document.location="../ecom/WCOM012.php"; </script> 
        <?php
        }
    }
    else
    {
    ?>
        <script language="javascript"> document.location="../ecom/WCOM011.php"; </script> 
    <?php
    }
}
else
{	
?>
    <script language="javascript"> document.location="../ecom/WCOM010.php"; </script> 
<?
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="../css/bootstrap.min.css" ></link>
        <link rel="stylesheet" href="../css/bootstrap-responsive.min.css"></link>
        <link rel="stylesheet" href="../css/font-awesome.css">
        <link rel="stylesheet" href="../css/style.css" ></link>
        <link rel="stylesheet" href="../css/dashboard.css" ></link>
        <link rel="stylesheet" href="../css/additional.css" ></link>
        <link rel="stylesheet" href="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.css">
        
        <script src="../lib/jquery/jquery-1.11.0.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.js"></script> 
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Common_JQuery.js"></script>
        <script src="../js/ero/WERO004.js"></script>
        <title>EPS</title>
    </head>
    <body> 
    </body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container"> 
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span> 
            </a>
            <a class="brand" href="#">
                e-Purchase System
            </a>
            <div class="nav-collapse">
                <ul class="nav pull-right">
                <li>
                    <a href="#" class="dropdown-toggle">
                        <i class="icon-user"></i> Welcome, <?php echo stripslashes(addslashes($sNama)); ?> (#User ID: <?php echo $sUserId; ?> #BU Code: <?php echo trim($sBuLogin); ?>) 
                    </a>
                </li>
            </div><!--/.nav-collapse --> 
        </div><!-- /container --> 
    </div><!-- /navbar-inner --> 
</div><!-- /navbar -->
    
<div class="subnavbar">
    <div class="subnavbar-inner">
        <div class="container">
            <ul class="mainnav">
                <li>
                    <a href="../ecom/WCOM002.php">
                        <i class="icon-chevron-up"></i><span>Main</span> 
                    </a> 
                </li>
                <li>
                    <a href="WERO001.php">
                        <i class="icon-plus-sign"></i><span>Open Receiving</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WERO003.php">
                        <i class="icon-ok-sign"></i><span>Closed Receiving</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WERO006.php">
                        <i class=" icon-minus-sign"></i><span>Cancel Receiving</span> 
                    </a> 
                </li>
                <li>
                    <a href="WERO004.php">
                        <i class="icon-calendar"></i><span>Delay Delivery</span> 
                    </a> 
                </li> 
                <li class="active">
                    <a href="WERO008.php">
                        <i class="icon-table"></i><span>On Progress Delivery</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WERO090.php">
                        <i class="icon-search"></i><span>Search Receiving</span> 
                    </a> 
                </li> 
                <li id="signout">
                    <a href="#">
                        <i class="icon-signout"></i><span>Logout</span> 
                    </a>
                </li>
            </ul>
        </div> <!-- /container --> 
    </div><!-- /subnavbar-inner --> 
</div><!-- /subnavbar -->

<div class="main">
    <div class="main-inner">
        <div class="container">
            <div class="row">
                <div class="span12"> 
                    <!----- PO Item List ---->
                    <div class="widget widget-table action-table">
                        <div class="widget-header"> <i class="icon-table"></i>
                            <h3>On Progress Delivery</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="19" style="text-align: left">
                                            <!--<a href="../db/REPORT/EXCEL/DELAY_DELIVERY.php" target="_blank" class="btn btn-small btn-linkedin-alt">
                                                Download
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>-->
                                            <a href="../db/REPORT/ONPROGRESS_DELIVERY.php" target="_blank" class="btn btn-small btn-linkedin-alt">
                                                Download
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>NO</th>
                                        <th style="display: none">REF TRANSFER ID</th>
                                        <th>PR NO</th>
                                        <th>REQUESTER</th>
                                        <th>PO NO</th>
                                        <th>SUPPLIER</th>
                                        <!--<th>PO DATE</th>-->
                                        <th>SENT PO DATE</th>
                                        <th>DUE DATE</th>
                                        <th style="display: none">DATE DIFF</th>
                                        <th>OUTFLAG</th>
                                        <th>DESCRIPTION</th>
                                        <th>CATG.</th>
                                        <th>OPEN<br>QTY</th>
                                        <th>ORDER<br>QTY</th>
                                        <th>CUR</th>
                                        <th>PRICE</th>
                                        <th rowspan="2">SUPPLIER<br>REF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $roListNo = 0;
                                    if(isset($_GET['mpage']))
                                    {
                                        $mpage = trim($_GET['mpage']);
                                    }
                                    else
                                    {
                                        $mpage = 1;
                                    }
                                    $max_per_page   = constant('20');;
                                    $num            = 5;
                                    
                                    if($mpage)
                                    { 
                                        $start = ($mpage) * $max_per_page; 
                                    }
                                    else
                                    {
                                        $start  = constant('20');;	
                                        $mpage  = 1;
                                    }
                                    
                                    if($mpage == 1)
                                    {
                                        $roListNo = 0;
                                    }
                                    else
                                    {
                                        $roListNo = ($max_per_page * ($mpage - 1));
                                    }
                                    
                                    $wherePoDetail  = array();
                                    $wherePoDetail[] = "EPS_T_PO_HEADER.PO_STATUS = '1250' 
                                                        and EPS_T_PO_DETAIL.RO_STATUS != '1320'
                                                        and (DATEDIFF(day, EPS_T_PO_HEADER.DELIVERY_DATE,CONVERT(char(10), GETDATE(), 112)) <= 0)";
                                    
                                    if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_05' || $sRoleId == 'ROLE_09')
                                    {
                                        $wherePoDetail[] = "EPS_T_PO_HEADER.ISSUED_BY in ($npkProcCriteria)";
                                        $wherePoDetail[] = "case 
                                                                when(substring(EPS_T_TRANSFER.PR_NO, 1, 1)) = 'H' 
                                                                    then (substring(EPS_T_TRANSFER.PR_NO, 1, 5)) 
                                                                else (substring(EPS_T_TRANSFER.PR_NO, 1, 4)) 
                                                            end
                                                            in (select     
                                                                    BU_CD
                                                                from
                                                                    EPS_M_PR_PROC_APPROVER
                                                                where      
                                                                    PLANT_ALIAS = '$plantCdAlias')";
                                    }
                                    
                                    /**
                                     * SELECT COUNT EPS_T_PR_HEADER
                                     **/
                                    $query_count_t_po_detail = "select 
                                                                    count (*) as COUNT_PO
                                                                from         
                                                                    EPS_T_PO_DETAIL 
                                                                left join
                                                                    EPS_T_PO_HEADER 
                                                                on 
                                                                    EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO 
                                                                left join
                                                                    EPS_T_TRANSFER
                                                                on
                                                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                                                left join
                                                                    EPS_M_BUNIT
                                                                on
                                                                    EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
                                                                left join
                                                                    EPS_M_EMPLOYEE
                                                                on
                                                                    EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                                                left join
                                                                    EPS_T_PR_HEADER 
                                                                on 
                                                                    EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO ";
                                    if(count($wherePoDetail)) {
                                        $query_count_t_po_detail .= "where " . implode('and ', $wherePoDetail);
                                    }
                                    $sql_count_t_po_detail = $conn->query($query_count_t_po_detail);
                                    $row_count_t_po_detail = $sql_count_t_po_detail->fetch(PDO::FETCH_ASSOC);
                                    $countPo    = $row_count_t_po_detail['COUNT_PO'];
                                    
                                    if($start > $countPo)
                                    {
                                        $lgenap     = $start - $max_per_page;
                                        $max_per_pages = $countPo - $lgenap;
                                        $start      = $countPo;
                                    }
                                    else
                                    {
                                        $max_per_pages = $max_per_page;
                                    }
                                    
                                    /**
                                     * SELECT EPS_T_PO_DETAIL
                                     **/
                                    $query_select_t_po_detail = "select 
                                                                    * 
                                                                 from 
                                                                    (select top  $max_per_pages  
                                                                        * 
                                                                    from      
                                                                        (select top $start      
                                                                            EPS_T_TRANSFER.PR_NO
                                                                            ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                            ,EPS_T_TRANSFER.REQUESTER
                                                                            ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                                            ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) as PROC_ACCEPT_DATE
                                                                            ,EPS_T_PO_DETAIL.PO_NO
                                                                            ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                                                            ,substring(EPS_T_PO_HEADER.ISSUED_DATE, 7, 2)
                                                                            + '/' + substring(EPS_T_PO_HEADER.ISSUED_DATE, 5, 2) 
                                                                            + '/' + substring(EPS_T_PO_HEADER.ISSUED_DATE, 1, 4) as ISSUED_DATE
                                                                            ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 103) as SEND_PO_DATE
                                                                            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE, 7, 2)
                                                                            + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 5, 2) 
                                                                            + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 1, 4) as DELIVERY_DATE
                                                                            ,EPS_T_PO_DETAIL.ITEM_CD
                                                                            ,EPS_T_PO_DETAIL.ITEM_NAME
                                                                            ,EPS_T_PO_DETAIL.QTY
                                                                            ,EPS_T_PO_DETAIL.UNIT_CD
                                                                            ,EPS_T_PO_HEADER.CURRENCY_CD
                                                                            ,EPS_T_PO_DETAIL.ITEM_PRICE
                                                                            ,isnull(
                                                                                (select sum(TRANSACTION_QTY)
                                                                                from 
                                                                                    EPS_T_RO_DETAIL
                                                                                where   
                                                                                    EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                                    and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                                                    and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'A')
                                                                                ,0
                                                                            )
                                                                            as TOTAL_RECEIVED_QTY
                                                                            ,isnull(
                                                                                (select sum(TRANSACTION_QTY)
                                                                                    from 
                                                                                        EPS_T_RO_DETAIL
                                                                                    where   
                                                                                        EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                                        and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                                                        and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'C')
                                                                                ,0
                                                                            )
                                                                            as TOTAL_CANCELED_QTY
                                                                            ,isnull(
                                                                                (select sum(TRANSACTION_QTY)
                                                                                    from 
                                                                                        EPS_T_RO_DETAIL
                                                                                    where   
                                                                                        EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                                        and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                                                        and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'O')
                                                                                ,0
                                                                            )
                                                                            as TOTAL_OPENED_QTY
                                                                            ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                                                            ,EPS_T_TRANSFER.NEW_ITEM_TYPE_CD
                                                                            ,EPS_T_TRANSFER.NEW_ACCOUNT_NO
                                                                            ,EPS_T_TRANSFER.NEW_RFI_NO
                                                                            ,EPS_M_BUNIT.BU_NAME
                                                                            ,datediff
                                                                                (day, EPS_T_PO_HEADER.DELIVERY_DATE, convert(char(10), GETDATE(), 112)) 
                                                                            as COUNT_DATE_DIFF
                                                                            ,(select count(*)
                                                                                from          
                                                                                    EPS_T_TRANSFER_SUPPLIER
                                                                                where      
                                                                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                                                                            as TOTAL_SUPPLIER
                                                                            ,EPS_M_ITEM_PRICE.ITEM_CATEGORY
                                                                        from         
                                                                            EPS_T_PO_DETAIL 
                                                                        left join
                                                                            EPS_T_PO_HEADER 
                                                                        on 
                                                                            EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO 
                                                                        left join
                                                                            EPS_T_TRANSFER
                                                                        on
                                                                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                                                        left join
                                                                            EPS_M_BUNIT
                                                                        on
                                                                            EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
                                                                        left join
                                                                            EPS_M_EMPLOYEE
                                                                        on
                                                                            EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                                                        left join
                                                                            EPS_T_PR_HEADER 
                                                                        on 
                                                                            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO 
                                                                        left join
                                                                            EPS_M_ITEM_PRICE
                                                                        on
                                                                            EPS_T_PO_DETAIL.ITEM_CD = EPS_M_ITEM_PRICE.ITEM_CD ";
                                    if(count($wherePoDetail)) {
                                        $query_select_t_po_detail .= "where " . implode(' and ', $wherePoDetail);
                                    }
                                    $query_select_t_po_detail .= "      order by 
                                                                            convert(DATETIME, DELIVERY_DATE, 103)
                                                                            ,EPS_T_PO_HEADER.SUPPLIER_NAME)
                                                                        as T1
                                                                    order by
                                                                        convert(DATETIME, DELIVERY_DATE, 103) desc
                                                                        ,T1.SUPPLIER_NAME desc)
                                                                    as T2
                                                                 order by
                                                                    convert(DATETIME, DELIVERY_DATE, 103)
                                                                    ,T2.SUPPLIER_NAME  ";
                                    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                                    while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC)){
                                        $refTransferId    = $row_select_t_po_detail['REF_TRANSFER_ID'];
                                        $prNo             = $row_select_t_po_detail['PR_NO'];
                                        $requesterName    = $row_select_t_po_detail['REQUESTER_NAME'];
                                        $procAcceptDate   = $row_select_t_po_detail['PROC_ACCEPT_DATE'];
                                        $poNo             = $row_select_t_po_detail['PO_NO'];
                                        $supplierName     = $row_select_t_po_detail['SUPPLIER_NAME'];
                                        $issuedDate       = $row_select_t_po_detail['ISSUED_DATE'];
                                        $sentPoDate       = $row_select_t_po_detail['SEND_PO_DATE'];
                                        $deliveryDate     = $row_select_t_po_detail['DELIVERY_DATE'];
                                        $itemCd           = $row_select_t_po_detail['ITEM_CD'];
                                        $itemName         = $row_select_t_po_detail['ITEM_NAME'];
                                        $itemCategory     = $row_select_t_po_detail['ITEM_CATEGORY'];
                                        $qty              = $row_select_t_po_detail['QTY'];
                                        $unitCd           = $row_select_t_po_detail['UNIT_CD'];
                                        $currencyCd       = $row_select_t_po_detail['CURRENCY_CD'];
                                        $itemPrice        = $row_select_t_po_detail['ITEM_PRICE'];
                                        $totalReceivedQty = $row_select_t_po_detail['TOTAL_RECEIVED_QTY'];
                                        $totalCanceledQty = $row_select_t_po_detail['TOTAL_CANCELED_QTY'];
                                        $totalOpenedQty   = $row_select_t_po_detail['TOTAL_OPENED_QTY'];
                                        $newChargedBu     = $row_select_t_po_detail['NEW_CHARGED_BU'];
                                        $newItemTypeCd    = $row_select_t_po_detail['NEW_ITEM_TYPE_CD'];
                                        $newAccountNo     = $row_select_t_po_detail['NEW_ACCOUNT_NO'];
                                        $newRfiNo         = $row_select_t_po_detail['NEW_RFI_NO'];
                                        $buName           = $row_select_t_po_detail['BU_NAME'];
                                        $countDateDiff    = $row_select_t_po_detail['COUNT_DATE_DIFF'];
                                        $totalSupplier    = $row_select_t_po_detail['TOTAL_SUPPLIER'];
                                        $totalOpenQty     = ($qty - $totalReceivedQty) + $totalCanceledQty + $totalOpenedQty;
                                        
                                        if($newItemTypeCd == '1' || $newItemTypeCd == '3' || $newItemTypeCd == '4'){
                                            $newItemTypeCd = 'E';
                                            $newCip = $newAccountNo;
                                        }else{
                                            $newItemTypeCd = 'I';
                                            $newCip = $newRfiNo;
                                        }
                                        
                                        $roListNo++;
                                ?>
                                    <tr>
                                        <td class="td-number">
                                            <?php echo $roListNo;?>.
                                        </td>
                                         <td style="display: none">
                                            <?php echo $refTransferId;?>
                                        </td>
                                        <td>
                                            <a href="#" class="faq-list">
                                                <?php echo $prNo;?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo $requesterName;?>
                                        </td>
                                        <td>
                                            <?php echo $poNo;?>
                                        </td>
                                        <td>
                                            <?php echo $supplierName;?>
                                        </td>
                                        <!--<td>
                                            <?php echo $issuedDate;?>
                                        </td>-->
                                        <td>
                                            <?php echo $sentPoDate;?>
                                        </td>
                                        <td>
                                            <?php echo $deliveryDate;?>
                                        </td>
                                        <td style="display: none">
                                            <?php echo $countDateDiff;?>
                                        </td>
                                        <td>
                                        <?php
                                            /*if($countDateDiff < 8)
                                            {
                                                $outFlag = '';
                                            }
                                            else if($countDateDiff >= 8 && $countDateDiff < 15)
                                            {
                                                $outFlag = '*';
                                            } 
                                            else if($countDateDiff >= 15 && $countDateDiff < 22)
                                            {
                                                $outFlag = '**';
                                            }
                                            else if($countDateDiff >= 22 && $countDateDiff < 29)
                                            {
                                                $outFlag = '***';
                                            }
                                            else
                                            {
                                                $outFlag = '****';
                                            }*/
                                            if($countDateDiff < 1)
                                            {
                                                $outFlag = '';
                                            }
                                            else if($countDateDiff < 8){
                                                $outFlag = '*';
                                            }
                                            else if($countDateDiff < 15){
                                                $outFlag = '**';
                                            } 
                                            else if($countDateDiff < 22){
                                                $outFlag = '***';
                                            }
                                            else{
                                                $outFlag = '****';
                                            }
                                            echo $outFlag;
                                        ?>
                                        </td>
                                        <td>
                                            <?php echo stripslashes($itemName);?>
                                        </td>
                                        <td data-item-category="<?php echo $itemCategory;?>" class="<?php echo $itemCategory!='Y'?'':'text-primary'; ?>">
                                            <?php echo $itemCategory!='Y'?'NON ':''; ?>ROUTINE
                                        </td>
                                        <td class="td-align-right">
                                            <?php 
                                                $split = explode('.', $totalOpenQty);
                                                if($split[1] == 0)
                                                {
                                                    echo number_format($totalOpenQty);
                                                }
                                                else
                                                {
                                                    echo $totalOpenQty;
                                                }
                                            ?>
                                        </td>
                                        <td class="td-align-right">
                                            <?php 
                                                $split = explode('.', $qty);
                                                if($split[1] == 0)
                                                {
                                                    echo number_format($qty);
                                                }
                                                else
                                                {
                                                    echo $qty;
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $currencyCd;?>
                                        </td>
                                        <td class="td-align-right">
                                            <?php
                                                $split_item_price = explode('.', $itemPrice);
                                                if($split_item_price[1] == 0)
                                                {
                                                    echo number_format($itemPrice);
                                                }
                                                else
                                                {
                                                    echo number_format($itemPrice,2);
                                                }
                                            ?>
                                        </td>
                                        <td style="text-align: center">
                                        <?php 
                                            if($totalSupplier == 0)
                                            {
                                                echo '';
                                            }
                                            else
                                            {
                                        ?>
                                                <a href="#" class="btn btn-small btn-info" id="window-refsupplier">
                                                    <i class="btn-icon-only icon-bookmark"> </i>
                                                </a>
                                        <?
                                            }
                                        ?>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                                    <tr>
                                        <th colspan="14">
                                        <?php
                                            if($countPo > $max_per_page)
                                            {         
                                                echo "<div id=\"pagination\" >";
                                                if ($query_select_t_po_detail != "")
                                                {
                                                        $fld = "prNoCriteria=$prNoCriteria&requesterNameCriteria=$requesterNameCriteria&inChargeNameCriteria=$inChargeNameCriteria&plantCdCriteria=$plantCdCriteria&mpage";
                                                }
                                                else
                                                {
                                                        $fld = "mpage";
                                                }
                                                paging($query_select_t_po_detail,$max_per_page,$num,$mpage,$fld,$countPo);
                                                echo "</div>";
                                            }
                                        ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- /span12 -->
            </div><!-- /row -->
        </div><!-- /container -->
    </div><!-- /main-inner -->
</div><!-- /main -->

<div class="footer">
    <div class="footer-inner">
	<div class="container">
            <div class="row">
		<div class="span12">
                    &copy; 2018 PT.TD Automotive Compressor Indonesia. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
<div id="dialog-pritem-table" title="PR Item Information" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-pritem">
            </div>
        </div>
    </div>
</div>
<div id="dialog-refsupplier-table" title="Reference Supplier List" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-refsupplier">
            </div>
        </div>
    </div>
</div>
    </body>
</html>
