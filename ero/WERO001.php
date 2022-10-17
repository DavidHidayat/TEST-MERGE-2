<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
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
            $poListNo 		= 0;
            $poNoCriteria   = trim($_GET['poNo']);
            
            if($poNoCriteria)
            {
                $query2 = "select 
                            EPS_T_PO_HEADER.PO_NO
                            ,EPS_T_PO_HEADER.SUPPLIER_CD
                            ,EPS_T_PO_HEADER.SUPPLIER_NAME
                            ,EPS_T_PO_HEADER.DELIVERY_PLANT
                            ,substring(EPS_T_PO_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                            ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 103) as SEND_PO_DATE
                            ,convert(VARCHAR(24), EPS_T_PO_HEADER.CLOSED_PO_DATE, 103) as CLOSED_PO_DATE
                            ,EPS_T_PO_HEADER.CLOSED_PO_MONTH
                            ,EPS_T_PO_HEADER.ADDITIONAL_REMARK
                            ,EPS_M_APP_STATUS.APP_STATUS_NAME
                            ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_NAME
                            ,EPS_M_SUPPLIER.CONTACT
                            ,EPS_M_SUPPLIER.PHONE
                            ,EPS_M_SUPPLIER.EMAIL
                            ,EPS_M_SUPPLIER.EMAIL_CC
                           from
                            EPS_T_PO_HEADER
                           left join
                            EPS_M_APP_STATUS
                           on
                            EPS_T_PO_HEADER.PO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                           left join
                            EPS_M_EMPLOYEE
                           on
                            EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                           left join
                            EPS_M_SUPPLIER
                           on
                            EPS_M_SUPPLIER.SUPPLIER_CD = EPS_T_PO_HEADER.SUPPLIER_CD
                           where
                            EPS_T_PO_HEADER.PO_NO = '$poNoCriteria'
                            and EPS_T_PO_HEADER.PO_STATUS = '1250'";
                $sql2 = $conn->query($query2);
                $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                $poNoVal            = $row2['PO_NO'];
                $supplierCdVal      = $row2['SUPPLIER_CD'];
                $supplierNameVal    = $row2['SUPPLIER_NAME'];
                $issuedDateVal      = $row2['ISSUED_DATE']; 
                $deliveryDateVal    = $row2['DELIVERY_DATE']; 
                $deliveryPlantVal   = $row2['DELIVERY_PLANT'];
                $addRemarkVal       = $row2['ADDITIONAL_REMARK'];
                $closedPoDateVal    = $row2['CLOSED_PO_DATE'];
                $sendPoDateVal      = $row2['SEND_PO_DATE'];
                $closedPoMonthVal   = $row2['CLOSED_PO_MONTH'];
                $poStatusNameVal    = $row2['APP_STATUS_NAME'];
                $issuedNameVal      = $row2['ISSUED_NAME'];
                $contactName        = $row2['CONTACT'];
                $phone              = $row2['PHONE'];
                $email              = $row2['EMAIL'];
                $emailCC            = $row2['EMAIL_CC'];
                
                if($deliveryPlantVal == 'JK'){
                    $deliveryPlantVal = 'DENSO Sunter Plant';
                }
                if($deliveryPlantVal == 'GT'){
                    $deliveryPlantVal = 'TACI PLANT';
                }
                if($deliveryPlantVal == 'JF'){
                    $deliveryPlantVal = 'DENSO 3rd Plant';
                }
                if($deliveryPlantVal == 'SI'){
                    $deliveryPlantVal = 'DENSO SALES Sunter Plant';
                }
                if($deliveryPlantVal == 'HD'){
                    $deliveryPlantVal = 'HAMADEN Sunter Plant';
                }
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
        <script src="../js/ero/WERO001.js"></script>
        <title>EPS</title>
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
                <li class="active">
                    <a href="WERO001.php">
                        <i class=" icon-plus-sign"></i><span>Open Receiving</span> 
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
                <li>
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
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please fill the search criteria.
                    </div>
                    <?php
                    if($poNoCriteria)
                    {
                        if(!$row2)
                        {
                    ?>
                    <div class="alert" id="mandatory-msg-2">
                        <strong>Data not found!</strong> No results match with your search.
                    </div>
                    <?php    
                        }
                    }
                    ?>
                    
                    <!----- PO Sent ---->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WERO001Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poNo">PO No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="poNo" name="poNo" maxlength="8"  value="<?php echo $poNoCriteria;?>" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                            <div>
                                <button class="btn btn-primary" id="btn-search">Search</button> 
                                <button class="btn" id="btn-reset">Reset</button>
                            </div> 
                        </div>
                    </div>
                    
                    <?php
                    if($row2)
                    {
                    ?>
                        <!----- PO Header ---->
                        <div class="widget">
                            <div class="widget-header">
                                <i class="icon-file"></i>
                                <h3>PO Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poNo">PO No</label>
                                                <div class="controls">
                                                    <input type="text" id="poNo" class="span2" value="<?php echo $poNoVal;?>" readonly />
                                            </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="poDate">Issued Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $issuedDateVal;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="issuedBy">Issued By: </label>
                                                <div class="controls">
                                                    <input type="text" id="issuedBy" class="span3" maxlength="200" value="<?php echo $issuedNameVal; ?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="poStatus">PO Status: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $poStatusNameVal;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poNo">Sent PO Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $sendPoDateVal;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="deliveryDate">Delivery Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $deliveryDateVal;?>" id="deliveryDate" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="issuedBy">Delivery Plant: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $deliveryPlantVal;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="supplierName">Supplier: </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" value="<?php echo $supplierNameVal.' ('.$supplierCdVal.')';?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poDate">Closed PO Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $closedPoDateVal;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="poDate">Closing Month: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $closedPoMonthVal;?>" readonly />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="addRemark">** Additional Remark: </label>
                                                <div class="controls">
                                                    <input type="text" id="addRemark" class="span8" maxlength="200" value="<?php echo $addRemarkVal; ?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
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
                                                    <input type="text" id="mailCC" class="span8" value="<?php echo $emailCC; ?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
						
                        <!----- PO Item List ---->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> <i class="icon-plus-sign"></i>
                                <h3>Open Receiving</h3>
                            </div>
                            <div class="widget-content">

                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2" style="display: none">REF TRANSFER ID</th>
                                            <th rowspan="2" style="display: none">PO NO</th>
                                            <th rowspan="2" style="display: none">SUPPLIER</th>
                                            <th rowspan="2" style="display: none">DUE DATE</th>
                                            <th rowspan="2">PR NO</th>
                                            <th rowspan="2">REQUESTER</th>
                                            <th colspan="4">ITEM</th>
                                            <th colspan="3">QTY</th>
                                            <th colspan="2">RECEIVING</th>
                                            <th rowspan="2">SUPPLIER<br>REF</th>
                                        </tr>
                                        <tr>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                            <th>PRICE</th>
                                            <th>UM</th>
                                            <th>ORDER</th>
                                            <th>TOTAL<br>RECEIVED</th>
                                            <th style="display: none">TOTAL<br>CANCELED</th>
                                            <th style="display: none">TOTAL<br>OPENED</th>
                                            <th>OPEN</th>
                                            <th>STATUS</th>
                                            <th>REF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                        if($poNoCriteria)
                                        {
                                            $query_select_t_po_detail = "select
                                                                            EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                            ,EPS_T_PO_DETAIL.PO_NO
                                                                            ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                                                            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                                                                            ,EPS_T_PO_DETAIL.ITEM_CD
                                                                            ,EPS_T_PO_DETAIL.ITEM_NAME
                                                                            ,EPS_T_PO_DETAIL.QTY
                                                                            ,EPS_T_PO_DETAIL.UNIT_CD
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
                                                                            ,EPS_T_PO_DETAIL.RO_STATUS
                                                                            ,EPS_M_APP_STATUS.APP_STATUS_NAME as RO_STATUS_NAME
                                                                            ,EPS_T_TRANSFER.PR_NO
                                                                            ,EPS_T_TRANSFER.REQUESTER
                                                                            ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                                            ,(select count(*)
                                                                                from          
                                                                                    EPS_T_TRANSFER_SUPPLIER
                                                                                where      
                                                                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                                                                            as TOTAL_SUPPLIER
                                                                        from
                                                                            EPS_T_PO_DETAIL 
                                                                        left join
                                                                            EPS_T_PO_HEADER
                                                                        on
                                                                            EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                                                                        left join
                                                                            EPS_M_APP_STATUS 
                                                                        on 
                                                                            EPS_M_APP_STATUS.APP_STATUS_CD = EPS_T_PO_DETAIL.RO_STATUS
                                                                        left join
                                                                            EPS_T_TRANSFER
                                                                        on
                                                                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                                                        left join
                                                                            EPS_T_PR_HEADER 
                                                                        on 
                                                                            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO 
                                                                        left join
                                                                            EPS_M_EMPLOYEE 
                                                                        on 
                                                                            EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                                                        where
                                                                            EPS_T_PO_HEADER.PO_STATUS = '1250'
                                                                            and EPS_T_PO_DETAIL.PO_NO = '$poNoCriteria'
                                                                        order by
                                                                            EPS_T_PO_DETAIL.REF_TRANSFER_ID ";
                                            $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                                            while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC)){
                                                $refTransferId  = $row_select_t_po_detail['REF_TRANSFER_ID'];
                                                $poNo           = $row_select_t_po_detail['PO_NO'];
                                                $supplierName   = $row_select_t_po_detail['SUPPLIER_NAME'];
                                                $deliveryDate   = $row_select_t_po_detail['DELIVERY_DATE'];
                                                $itemCd         = $row_select_t_po_detail['ITEM_CD'];
                                                $itemName       = $row_select_t_po_detail['ITEM_NAME'];
                                                $qty            = $row_select_t_po_detail['QTY'];
                                                $totalReceivedQty= $row_select_t_po_detail['TOTAL_RECEIVED_QTY'];
                                                $totalCanceledQty= $row_select_t_po_detail['TOTAL_CANCELED_QTY'];
                                                $totalOpenedQty = $row_select_t_po_detail['TOTAL_OPENED_QTY'];
                                                $unitCd         = $row_select_t_po_detail['UNIT_CD'];
                                                $itemPrice      = $row_select_t_po_detail['ITEM_PRICE'];
                                                $roStatus       = $row_select_t_po_detail['RO_STATUS'];
                                                $roStatusName   = $row_select_t_po_detail['RO_STATUS_NAME'];
                                                $prNo           = $row_select_t_po_detail['PR_NO'];
                                                $requesterName  = $row_select_t_po_detail['REQUESTER_NAME'];
                                                $totalSupplier  = $row_select_t_po_detail['TOTAL_SUPPLIER'];
                                                $totalOpenQty   = ($qty - $totalReceivedQty) + $totalCanceledQty + $totalOpenedQty;
                                                $totalActualReceivedQty = $totalReceivedQty - $totalCanceledQty - $totalOpenedQty;
                                                $poListNo++;  
												
												$split_item_price = explode('.', $itemPrice);
                                                if($split_item_price[1] == 0)
                                                {
                                                    $itemPrice = number_format($itemPrice);
                                                }
                                                else
                                                {
                                                    $itemPrice = number_format($itemPrice,2);
                                                }
                                    ?>
                                        <tr>
                                            <td class="td-number">
                                                <?php echo $poListNo;?>.
                                            </td>
                                            <td style="display: none">
                                                <?php echo $refTransferId;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $poNo;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $supplierName;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $deliveryDate;?>
                                            </td>
                                            <td>
                                                <?php echo $prNo;?>
                                            </td>
                                            <td>
                                                <?php echo $requesterName;?>
                                            </td>
                                            <td>
                                                <?php echo $itemCd;?>
                                            </td>
                                            <td>
                                            <?php
                                                if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_05' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_11')
												{
                                                    if($totalOpenQty <= $qty && $totalOpenQty >= 0 &&  $roStatusName == 'Open')
                                                    {
                                            ?>
                                                        <a href="../db/Redirect/RO_Screen.php?criteria=roDetail&paramRefTransferId=<?php echo $refTransferId;?>&paramPoNo=<?php echo $poNo?>" class="faq-list">
                                                            <?php echo $itemName;?>
                                                        </a>
                                            <?php             
                                                    }
                                                    else
                                                    {
                                                        echo $itemName;
                                                    }
                                                }
                                                else
                                                {
                                                    echo $itemName;
                                                }
                                            ?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo $itemPrice;?>
                                            </td>
                                            <td>
                                                <?php echo $unitCd;?>
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
                                            <td class="td-align-right">
                                            <?php 
                                                $split = explode('.', $totalActualReceivedQty);
                                                if($split[1] == 0)
                                                {
                                                    echo number_format($totalActualReceivedQty);
                                                }
                                                else
                                                {
                                                    echo $totalActualReceivedQty;
                                                }
                                            ?>
                                            </td>
                                            <td class="td-align-right" style="display: none">
                                                <?php echo $totalCanceledQty;?>
                                            </td>
                                            <td class="td-align-right" style="display: none">
                                                <?php echo $totalOpenedQty;?>
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
                                            <td>
                                                <?php echo $roStatusName;?>
                                            </td>
                                            <td>
                                            <?php
                                                if($totalReceivedQty > 0 ){
                                            ?>
                                                <a href="#" class="btn btn-small btn-inverse" id="window-receiving">
                                                    <i class="btn-icon-only icon-external-link "> </i>
                                                </a>
                                            <?php        
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
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php    
                    }
                    ?>
                    <input type="hidden" value="<?php echo $poListNo;?>" id="countSearch" />
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
                    &copy; 2018 PT.TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-refsupplier-table" title="Reference Supplier List" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-refsupplier">
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
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
    </body>
</html>