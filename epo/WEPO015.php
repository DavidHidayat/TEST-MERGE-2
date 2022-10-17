<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
if(isset($_SESSION['sUserId']))
{   
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag		= $_SESSION['sactiveFlag'];
    $sActiveFlagLogin	= $_SESSION['sactiveFlagLogin'];
    
    if($sUserId != '' && $sActiveFlag == 'A' && $sActiveFlagLogin == 'A')
    {
        /** Unset SESSION */
        unset($_SESSION['prStatus']);
        
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
        $poScreen   = $_SESSION['poScreen'];
        $poHeaderUpdateDate=$_SESSION['poHeaderUpdateDate'];
        
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')
        {
            if($poScreen == 'EditPoAfterSentScreen')
            {
                $poNoSession    = $_SESSION['poNoSession'];
                $paramPoNo      = $_GET['paramPoNo'];

                if($poNoSession == $paramPoNo)
                {
                    $wherePoHeader = array();
                    if($paramPoNo){
                        $wherePoHeader[] = "EPS_T_PO_HEADER.PO_NO = '".$paramPoNo."'";
                    }
                    $query = "select 
                                EPS_T_PO_HEADER.PO_NO
                                ,EPS_T_PO_HEADER.ISSUED_BY
                                ,EPS_T_PO_HEADER.PO_STATUS
                                ,substring(EPS_T_PO_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                ,EPS_T_PO_HEADER.SUPPLIER_CD
                                ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                ,EPS_T_PO_HEADER.CURRENCY_CD
                                ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                                ,EPS_T_PO_HEADER.DELIVERY_PLANT
                                ,EPS_T_PO_HEADER.ADDITIONAL_REMARK
                                ,EPS_M_SUPPLIER.CONTACT
                                ,EPS_M_SUPPLIER.PHONE
                                ,EPS_M_SUPPLIER.EMAIL
                                ,EPS_M_SUPPLIER.EMAIL_CC
                                ,(select count(*)
                                    from
                                        EPS_T_PO_APPROVER
                                    where
                                        PO_NO = '$paramPoNo')
                                as COUNT_APPROVER
                                ,EPS_M_APP_STATUS.APP_STATUS_NAME
                                ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_NAME
                                ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 103) as SEND_PO_DATE
                                ,convert(VARCHAR(24), EPS_T_PO_HEADER.CLOSED_PO_DATE, 103) as CLOSED_PO_DATE
                                ,EPS_T_PO_HEADER.CLOSED_PO_MONTH
                            from
                                EPS_T_PO_HEADER
                            left join
                                EPS_M_SUPPLIER
                            on 
                                EPS_T_PO_HEADER.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                            left join
                                EPS_M_APP_STATUS
                            on
                                EPS_T_PO_HEADER.PO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                            left join
                                EPS_M_EMPLOYEE
                            on
                                EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK ";
                    if(count($wherePoHeader)) {
                        $query .= "where " . implode('and ', $wherePoHeader);
                    }
                    $sql = $conn->query($query);
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $poNo           = $row['PO_NO'];
                        $issuedBy       = $row['ISSUED_BY'];
                        $poStatus       = $row['PO_STATUS'];
                        $issuedDate     = $row['ISSUED_DATE'];
                        $supplierCd     = $row['SUPPLIER_CD'];
                        $supplierName   = $row['SUPPLIER_NAME'];
                        $currencyCd     = $row['CURRENCY_CD'];
                        $deliveryDate   = $row['DELIVERY_DATE'];
                        $deliveryPlant  = $row['DELIVERY_PLANT'];
                        $addRemark      = stripslashes(trim($row['ADDITIONAL_REMARK']));
                        $contactName    = $row['CONTACT'];
                        $phone          = $row['PHONE'];
                        $email          = $row['EMAIL'];
                        $emailCc        = $row['EMAIL_CC'];
                        $getCountApprover= $row['COUNT_APPROVER'];
                        $poStatusName   = $row['APP_STATUS_NAME'];
                        $issuedName     = $row['ISSUED_NAME'];
                        $closedPoDate   = $row['CLOSED_PO_DATE'];
                        $sendPoDate     = $row['SEND_PO_DATE'];
                        $closedPoMonth  = $row['CLOSED_PO_MONTH'];
                        
                        if($deliveryPlant == 'JK'){
                            $deliveryPlant = 'DENSO Sunter Plant';
                        }
                        if($deliveryPlant == 'GT'){
                            $deliveryPlant = 'TACI NEW PLANT';
                        }
                        if($deliveryPlant == 'JF'){
                            $deliveryPlant = 'DENSO Fajar Plant';
                        }
                        if($deliveryPlant == 'SI'){
                            $deliveryPlant = 'DENSO SALES Sunter Plant';
                        }
                        if($deliveryPlant == 'HD'){
                            $deliveryPlant = 'HAMADEN Sunter Plant';
                        }
						
						 $query_select_t_po_detail = "select 
                                                    UPDATE_DATE
                                                 from
                                                    EPS_T_PO_DETAIL
                                                 where
                                                    PO_NO = '$paramPoNo'
                                                 order by
                                                    UPDATE_DATE desc";
						$sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
						$row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
						$updateDatePoDetail = $row_select_t_po_detail['UPDATE_DATE'];
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
                <script language="javascript"> document.location="../ecom/WCOM012.php"; </script> 
            <?php
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
        <script src="../js/epo/WEPO015.js"></script>
        <script>
            maximize();
        </script>
        <title>EPS</title>
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="../js/html5.js"></script>
        <![endif]-->
        <script>
            maximize();
            $(function() {
                var dateToday = new Date();
                $( "#newDeliveryDate" ).datepicker({
                    dateFormat: 'dd/mm/yy',
                    defaultDate: "+1w",
                    minDate: dateToday,
                    maxDate: '+2Y',
                    autoClose: true,
                    beforeShowDay: noWeekendsOrHolidays
                });
            });
        </script>
    </head>
    <body>   
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
                    <a href="WEPO001_.php">
                        <i class="icon-list-alt"></i><span>PR Waiting</span> 
                    </a> 
                </li>
                <li>
                    <a href="WEPO012.php">
                        <i class="icon-credit-card "></i><span>PR Accepted</span> 
                    </a> 
                </li>
                <!--<li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"> 
                        <i class="icon-long-arrow-down"></i><span>PR to PO</span> <b class="caret"></b>
                    </a> 
                    <ul class="dropdown-menu">
                        <li><a href="WEPO003.php">Generate PO Number</a></li>
                        <li><a href="WEPO004.php">Outstanding PO</a></li>
                    </ul>
                </li>-->
                <li>
                    <a href="WEPO004.php">
                        <i class="icon-bookmark"></i><span>Outstanding PO</span> 
                    </a> 
                </li>
                <li>
                    <a href="WEPO003.php">
                        <i class="icon-tags"></i><span>Generate PO</span> 
                    </a> 
                </li>
                <li>
                    <a href="WEPO005.php">
                        <i class="icon-list-ul"></i><span>PO List</span> 
                    </a>
                </li>
                <li>
                    <a href="WEPO018.php">
                        <i class="icon-th"></i><span>PO Waiting</span> 
                    </a> 
                </li>
                <li class="active">
                    <a href="WEPO013.php">
                        <i class="icon-copy"></i><span>PO Sent</span> 
                    </a>
                </li>
                <!--<li>
                    <a href="WEPO017.php">
                        <i class="icon-th"></i><span>PR Item</span> 
                    </a>
                </li>-->
                <li>
                    <a href="WEPO090.php">
                        <i class="icon-search"></i><span>PO Search</span> 
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
                    <!--<form>-->
                        <!---------------------------------- Hidden Parameter --------------------------------->
                        <input type="hidden" id="updateDate" value="<?php echo $poHeaderUpdateDate; ?>" />
                        <input type="hidden" id="updateDateDetail" value="<?php echo $updateDatePoDetail; ?>" />
                        <input type="hidden" id="supplierCdHidden" value="<?php echo $supplierCd; ?>" />
                        
                        <!---------------------------------- PO Header --------------------------------->
                        <div class="widget">
                            <div class="widget-header">
                                <i class="icon-paste"></i>
                                <h3>PO Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poNo">PO No: </label>
                                                <div class="controls">
                                                    <input type="text" id="poNo" class="span2" value="<?php echo $paramPoNo;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="poDate">Issued Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $issuedDate;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="issuedBy">Issued By: </label>
                                                <div class="controls">
                                                    <input type="text" id="issuedBy" class="span3" maxlength="200" value="<?php echo $issuedName; ?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="poStatus">PO Status: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $poStatusName;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poNo">Sent PO Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $sendPoDate;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="deliveryDate">Delivery Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $deliveryDate;?>" id="deliveryDate" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="issuedBy">Delivery Plant: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $deliveryPlant;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="supplierName">Supplier: </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" value="<?php echo $supplierName.' ('.$supplierCd.')';?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poDate">Closed PO Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $closedPoDate;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="poDate">Closing Month: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $closedPoMonth;?>" readonly />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="addRemark">** Additional Remark: </label>
                                                <div class="controls">
                                                    <input type="text" id="addRemark" class="span8" maxlength="200" value="<?php echo $addRemark; ?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                        
                        <!---------------------------------- PO Detail --------------------------------->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> <i class="icon-th-list"></i>
                                <h3>PO Detail</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered" id="poItemTable">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2">ACTION</th>
                                            <th rowspan="2" style="display: none">TRANSFER ID</th>
                                            <th colspan="2">ITEM</th>
                                            <th rowspan="2">QTY</th>
                                            <th rowspan="2">UM</th>
                                            <th rowspan="2">PRICE</th>
                                            <th rowspan="2">CUR</th>
                                            <th rowspan="2">TOTAL</th>
                                            <th rowspan="2">SUPPLIER REF</th>
                                            <th rowspan="2" style="display: none">SEQ ITEM</th>
                                            <th rowspan="2">PR NO</th>
                                            <th rowspan="2">EXP/<br>RFI NO</th>
                                            <th colspan="2">RECEIVING</th>
                                            <th rowspan="2" style="display: none">TOTAL RECEIVING</th>
                                        </tr>
                                        <tr>
                                            <th style="display: none">STATUS</th>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                            <th>STATUS</th>
                                            <th>REF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $poDetailNo = 0;
                                    $poTotal = 0;
                                    $countItemDel = 0;
                                    $countItemNonMaster = 0;
									
                                    foreach ( $_SESSION['poDetail'] as $poDetails ) 
                                    {  
                                        $poNoSession            = $poDetails['poNo'];
                                        $refTransferIdSession   = $poDetails['refTransferId'];  
                                        $itemCdSession          = $poDetails['itemCd'];
                                        $itemNameSession        = $poDetails['itemName'];  
                                        $qtySession             = $poDetails['qty'];
                                        $itemPriceSession       = $poDetails['itemPrice'];  
                                        $amountSession          = $poDetails['amount']; 
                                        $currencyCdSession      = $poDetails['currencyCd'];
                                        $unitCdSession          = $poDetails['unitCd'];  
                                        $seqPoItemSession       = $poDetails['seqPoItem'];
                                        $totalSupplierSession   = $poDetails['totalSupplier'];
                                        $itemStatusSession      = $poDetails['itemStatus'];
                                        $itemStatusNameSession  = $poDetails['itemStatusName'];
                                        $prNoSession            = $poDetails['prNo'];
                                        $countReceivingSession  = $poDetails['countReceiving'];
                                        $itemTypeCdSession      = $poDetails['itemTypeCd'];
                                        $accountNoSession       = $poDetails['accountNo'];
                                        $rfiNoSession           = $poDetails['rfiNo'];
                                        
                                        if($itemTypeCdSession == '1' || $itemTypeCdSession == '3' || $itemTypeCdSession == '4' || $itemTypeCdSession == '5')
                                        {
                                            $objectAccount = $accountNoSession;
                                        }
                                        if($itemTypeCdSession == '2')
                                        {
                                            $objectAccount = $rfiNoSession;
                                        }
                                        if(strlen($objectAccount) == 1)
                                        {
                                            $objectAccount = '0'.$objectAccount;
                                        }
                                            
										if($itemCdSession == "99")
										{
											$countItemNonMaster++;
										}
                                            
                                        $poTotal = $poTotal + $amountSession;
                                        $poDetailNo++;
                                        ?>
                                        <tr id="<?php echo $seqPoItemSession;?>">
                                            <td class="td-number">
                                                <?php echo $poDetailNo;?>.
                                            </td>
                                            <td>
                                                <?php
                                                if($itemCdSession == "99" && ($sRoleId == "ROLE_03" || $sRoleId == "ROLE_06"))
                                                {
												?>
                                                    <a href="#" class="btn btn-small btn-success" id="window-edit">
                                                        <i class="btn-icon-only icon-edit "> </i>
                                                    </a>
                                                <?php
												}
                                                //if(count($_SESSION['poDetail']) > 1 && $countItemDel == 0){
                                                ?>
                                                    <!--<a href="#" class="btn btn-small btn-danger" id="window-delete">
                                                        <i class="btn-icon-only icon-remove"> </i>
                                                    </a>-->
                                                <?php
                                                //}
                                                ?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $refTransferIdSession;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $itemStatusSession;?>
                                            </td>
                                            <td>
                                                <?php echo $itemCdSession;?>
                                            </td>
                                            <td>
                                                <?php echo $itemNameSession;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php 
                                                $split = explode('.', $qtySession);
                                                if($split[1] == 0)
                                                {
                                                    echo number_format($qtySession);
                                                }
                                                else
                                                {
                                                    echo $qtySession;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $unitCdSession;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php
                                                $split_item_price = explode('.', $itemPriceSession);
                                                if($split_item_price[1] == 0)
                                                {
                                                    echo number_format($itemPriceSession);
                                                }
                                                else
                                                {
                                                    echo number_format($itemPriceSession,2);
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $currencyCdSession;?>
                                            </td>
                                            <td class="td-align-right amount">
                                                <?php 
                                                $split_amount = explode('.', $amountSession);
                                                if($split_amount[1] == 0)
                                                {
                                                    echo number_format($amountSession);
                                                }
                                                else
                                                {
                                                    echo number_format($amountSession,2);
                                                }
                                                ?>
                                            </td>
                                            <td style="width: 50px; text-align: center">
                                            <?php 
                                            if($totalSupplierSession > 0){
                                            ?>
                                                <a href="#" class="btn btn-small btn-info" id="window-refsupplier">
                                                    <i class="btn-icon-only icon-bookmark"> </i>
                                                </a>
                                            <?
                                            }
                                            ?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $seqPoItemSession;?>
                                            </td>
                                            <td>
                                                <a href="#" class="faq-list">
                                                    <b><?php echo $prNoSession;?></b>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo $objectAccount;?>
                                            </td>
                                            <td>
                                                <?php echo $itemStatusNameSession;?>
                                            </td>
                                            <td>
                                            <?php
                                            if($countReceivingSession <= 0){
                                                echo '';
                                            }else{
                                            ?>
                                                <a href="#" class="btn btn-small btn-inverse" id="window-receiving">
                                                    <i class="btn-icon-only icon-external-link "> </i>
                                                </a>
                                            <?php    
                                            }
                                            ?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $countReceivingSession;?>
                                            </td>
                                        </tr>
                                        <?
                                        }
                                        ?>    
                                        <tr>
                                            <th colspan="8" class="td-align-right">
                                                Total
                                            </th>
                                            <th style="text-align: right">
                                            <?php
                                                $split_total = explode('.', $poTotal);
                                                if($split_total[1] == 0)
                                                {
                                                    echo number_format($poTotal);
                                                }
                                                else
                                                {
                                                    echo number_format($poTotal,2);
                                                }
                                            ?>
                                            </th>
                                            <th colspan="5">
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!---------------------------------- Supplier Information --------------------------------->
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-user"></i>
                                <h3>Supplier Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="contact">PIC: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $contactName;?>" readonly />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="mailSupplier">Email: </label>
                                                <div class="controls">
                                                    <input type="text" class="span8" value="<?php echo $email;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="phone">Phone: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $phone;?>" readonly />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="mailCC">CC: </label>
                                                <div class="controls">
                                                    <input type="text" id="mailCC" class="span8" value="<?php echo $emailCc; ?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                        
                        <!---------------------------------- PO Approver --------------------------------->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-list-ol "></i>
                                <h3>PO Approver</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2">NAME</th>
                                            <th rowspan="2">STATUS</th>
                                            <th rowspan="2">DATE</th>
                                            <th rowspan="2">REMARK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        
                                        $query = "select 
                                                    EPS_T_PO_APPROVER.PO_NO
                                                    ,EPS_T_PO_APPROVER.APPROVER_NO
                                                    ,EPS_T_PO_APPROVER.NPK
                                                    ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                    ,EPS_T_PO_APPROVER.APPROVAL_STATUS
                                                    ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                                    ,convert(VARCHAR(24), EPS_T_PO_APPROVER.APPROVAL_DATE, 120) as APPROVAL_DATE
                                                    ,EPS_T_PO_APPROVER.APPROVAL_REMARK
                                                from 
                                                    EPS_T_PO_APPROVER 
                                                left join
                                                    EPS_M_EMPLOYEE
                                                on
                                                    EPS_T_PO_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                                left join
                                                    EPS_M_APPROVAL_STATUS
                                                on
                                                    EPS_T_PO_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
                                                
                                                where 
                                                    EPS_T_PO_APPROVER.PO_NO ='".$paramPoNo."'
                                                order by
                                                    EPS_T_PO_APPROVER.APPROVER_NO 
                                                asc";
                                        $sql = $conn->query($query);
                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                            $approverNo         = $row['APPROVER_NO'];
                                            $npk                = $row['NPK'];
                                            $approverName       = addslashes($row['APPROVER_NAME']);
                                            $approvalStatus     = $row['APPROVAL_STATUS'];
                                            $approvalStatusName = $row['APPROVAL_STATUS_NAME'];
                                            $approvalDate       = $row['APPROVAL_DATE'];
                                            $approvalRemark     = $row['APPROVAL_REMARK'];
                                            
                                            if(strlen(trim($approvalDate)) != 0){
                                                date_default_timezone_set('Asia/Jakarta');
                                                $approvalDate   = date("d/m/Y H:i:s A", strtotime($approvalDate));
                                            }
                                    ?>
                                        <tr>
                                            <td class="td-number"><?php echo $approverNo;?>.</td>
                                            <td><?php echo $approverName;?></td>
                                            <td><?php echo $approvalStatusName;?></td>
                                            <td><?php echo $approvalDate;?></td>
                                            <td><?php echo $approvalRemark;?></td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- widget widget-table action-table -->
                        
                        <!---------------------------------- Cancel Information --------------------------------->
                        <div class="widget" id="approver-information">
                            <div class="widget-header">
                                <i class="icon-info-sign"></i>
                                <h3>Procurement Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
										<?php
                                        if($countReceiving == 0  && ($sRoleId == "ROLE_03" || $sRoleId == "ROLE_04" || $sRoleId == "ROLE_06"))
                                        {
                                        ?>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="deliveryDate">New Delivery Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="newDeliveryDate" />
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="remarkCancelPo">Comment: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" id="remarkCancelPo" maxlength="200" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <!---------------------------------- Message --------------------------------->
                                    <div class="alert" id="mandatory-msg-1" style="display: none">
                                        <strong>Failure!</strong> Data already updated by another user.
                                    </div>
                                    <div class="alert" id="mandatory-msg-2" style="display: none">
                                        <strong>Mandatory!</strong> Please input Comment for Update or Cancel PO After Sent.
                                    </div>
                                    <div class="alert" id="mandatory-msg-3" style="display: none">
                                        <strong>Mandatory!</strong> Please input New Delivery Date for Update Due Date.
                                    </div>
                                    <div class="alert" id="undefined-msg" style="display: none">
                                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                                    </div>
                                    <div class="alert" id="session-msg" style="display: none">
                                        <strong>Session expired!</strong> Session timeout or user has not login.<br>Please login again.
                                    </div>
                                    <div class="alert alert-success" id="success-msg" style="display: none">
                                        <strong>Success!</strong> Cancel PO finished.
                                    </div>
                                    <div class="alert alert-success" id="update-msg" style="display: none">
                                        <strong>Success!</strong> Update Due Date finished.
                                    </div>
                                    <div class="alert alert-success" id="update-detail-msg" style="display: none">
                                        <strong>Success!</strong> Update PO Detail finished.
                                    </div>

                                    <!---------------------------------- Button --------------------------------->
                                    <div class="form-actions">
										<?php
                                        if(($sRoleId == "ROLE_03" || $sRoleId == "ROLE_06" ) && $countItemNonMaster > 0)
                                        {
                                        ?>
                                        <button class="btn btn-info" id="btn-update-detail">Update PO Detail</button>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        if($countReceiving == 0  && ($sRoleId == "ROLE_03" || $sRoleId == "ROLE_04" || $sRoleId == "ROLE_06"))
                                        {
                                        ?>
                                        <button class="btn btn-warning" id="btn-update">Update Due Date</button>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        if($sUserId == $issuedBy || $sRoleId == "ROLE_03")
                                        {
                                        ?>
                                        <button class="btn btn-danger" id="btn-cancel">Cancel PO</button>
                                        <?php
                                        }
                                        ?>
                                        <button class="btn" id="btn-back">Back</button>
                                    </div> <!-- /form-actions -->
                                </div>
                            </div>
                        </div>
                    <!--</form>-->
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
<div id="dialog-confirm-session" title="Message" style="display: none;"></div>
<div id="dialog-confirm-back" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-cancel" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-update" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-update-detail" title="Confirm" style="display: none;"></div>

<div id="dialog-refsupplier-table" title="Supplier Reference List" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-refsupplier">
            </div>
        </div>
    </div>
</div>
<div id="dialog-pritem-table" title="PR Item Information" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-pritem">
            </div>
        </div>
    </div>
</div>
<div id="dialog-receiving-table" title="Receiving List" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-receiving">
            </div>
        </div>
    </div>
</div>
<div id="dialog-form" title="Edit PO Item" style="display: none;">
    <div class="alert" id="dialog-mandatory-msg-1" style="display: none;">
        <strong>Mandatory!</strong> Please fill all the field.
    </div>
    <div class="alert" id="dialog-mandatory-msg-2" style="display: none">
        <strong>Range Error!</strong> Please input value > 0.
    </div>
    <div class="alert" id="dialog-mandatory-msg-3" style="display: none">
        <strong>Existence Error!</strong> Selected supplier does not match with this PO.
    </div>
    <div class="alert" id="dialog-mandatory-msg-4" style="display: none">
        <strong>Range Error!</strong> 0 is not allowed as first character for Qty.
    </div>
    <div class="alert" id="dialog-mandatory-msg-5" style="display: none">
        <strong>Range Error!</strong> Please input qty less than qty PO.
    </div>
    <div class="alert" id="dialog-mandatory-msg-6" style="display: none;">
        <strong>Digit Error!</strong> Please input price less than 20% from item price in PR.
    </div>
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    
    <div class="widget ">
        <form id="WEPO015Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <input type="hidden" id="seqItem" />
                    <input type="hidden" id="refTransferIdHidden" />
                    <input type="hidden" id="currencyCdHidden" />
                    <input type="hidden" id="totalSupplierHidden" />
                    <input type="hidden" id="initialQty" />
                    <input type="hidden" id="prItemPrice" />
                    
                    <table class="table-non-bordered">
                        <tr>
                            <td>
                                <label class="control-label" for="itemCd">Code: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="itemCd" class="span2" readonly />
                                </div>
                            </td>
                            <td colspan="2">
                                <label class="control-label" for="itemName">Name: </label>
                                <div class="controls">
                                    <input type="text" id="itemName" class="full-width-input" maxlength="200" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="supplierCd">Code: </label>
                                <div class="controls">
                                    <select id="supplierCd" class="span2">
                                        <option value=""></option>
                                        <?php
                                            $query = "select 
                                                        SUPPLIER_CD 
                                                        ,SUPPLIER_NAME
                                                      from 
                                                        EPS_M_SUPPLIER";
                                            $sql = $conn->query($query);
                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                $supplierCd   = $row['SUPPLIER_CD'];
                                        ?>
                                        <option value="<?php echo $supplierCd;?>"><?php echo $supplierCd;?></option>
                                        <?php         
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td colspan="2">
                                <label class="control-label" for="supplierName">Supplier: </label>
                                <div class="controls">
                                    <select id="supplierName" class="full-width-input">
                                        <option value=""></option>
                                        <?php
                                            $query2 = "select 
                                                        SUPPLIER_CD 
                                                        ,SUPPLIER_NAME
                                                        ,CURRENCY_CD
                                                      from 
                                                        EPS_M_SUPPLIER
                                                      order by
                                                        SUPPLIER_NAME";
                                            $sql2 = $conn->query($query2);
                                            while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
                                                $supplierCd2   = $row2['SUPPLIER_CD'];
                                                $supplierName2 = $row2['SUPPLIER_NAME'];
                                                $currencyCd2 = $row2['CURRENCY_CD'];
                                        ?>
                                        <option value="<?php echo $supplierCd2;?>"><?php echo $supplierCd2.' [ '.$currencyCd2.' ]'.' - '.$supplierName2;?></option>
                                        <?php         
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="um">U M: </label>
                                <div class="dialog-controls">
                                    <select id="um" class="span2">
                                    <?php
                                        $query = "select 
                                                    UNIT_CD
                                                    ,UNIT_NAME 
                                                  from 
                                                    EPS_M_UNIT_MEASURE";
                                        $sql = $conn->query($query);
                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                            $unitCd   = $row['UNIT_CD'];
                                    ?>
                                        <option value="<?php echo $unitCd;?>"><?php echo $unitCd;?></option>
                                    <?php         
                                        }
                                    ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="price">Price: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="price" class="input-align-right" maxlength="16" />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="qty">Qty: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="qty" class="span2 input-align-right" maxlength="6" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>  
            </div>   
        </form>
    </div>
</div>
    </body>
</html>
