<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
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
        
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06')
        {
            if($poScreen == 'EditPoScreen')
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
                                ,(select count(*)
                                    from
                                        EPS_T_PO_APPROVER
                                    where
                                        PO_NO = '$paramPoNo')
                                as COUNT_APPROVER
                                ,EPS_M_APP_STATUS.APP_STATUS_NAME
                                ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_NAME
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
                        $poStatus       = $row['PO_STATUS'];
                        $issuedDate     = $row['ISSUED_DATE'];
                        $supplierCd     = $row['SUPPLIER_CD'];
                        $supplierName   = $row['SUPPLIER_NAME'];
                        $currencyCd     = $row['CURRENCY_CD'];
                        $deliveryDate   = $row['DELIVERY_DATE'];
                        $deliveryPlant  = $row['DELIVERY_PLANT'];
                        $addRemark      = trim($row['ADDITIONAL_REMARK']);
                        $contactName    = $row['CONTACT'];
                        $phone          = $row['PHONE'];
                        $email          = $row['EMAIL'];
                        $getCountApprover= $row['COUNT_APPROVER'];
                        $poStatusName   = $row['APP_STATUS_NAME'];
                        $issuedName     = $row['ISSUED_NAME'];
                        
                        if($deliveryPlant == 'JK'){
                            $deliveryPlant = 'DENSO Sunter Plant';
                        }
                        if($deliveryPlant == 'GT'){
                            $deliveryPlant = 'TACI NEW PLANT';
                        }
                        if($deliveryPlant == 'JF'){
                            $deliveryPlant = 'DENSO 3rd Plant';
                        }
                        if($deliveryPlant == 'SI'){
                            $deliveryPlant = 'DENSO SALES Sunter Plant';
                        }
                        if($deliveryPlant == 'HD'){
                            $deliveryPlant = 'HAMADEN Sunter Plant';
                        }
                    }
                }
                else
                {
                ?>
                    <script language="javascript"> alert("Sorry, you are not authorized to access this PO No.");
                    document.location="../db/Login/Logout.php"; </script>
                <?php
                }
            
            }
            else
            {
            ?>
                <script language="javascript"> alert("Sorry, you are not authorized to access Edit PO screen.");
                document.location="../db/Login/Logout.php"; </script>
            <?php
            }
        }
        else
        {
        ?>
            <script language="javascript"> alert("Sorry, this page only can be accessed by Procurement.");
            document.location="../db/Login/Logout.php"; </script>
        <?php
        }
    }
    else
    {
    ?>
        <script language="javascript"> alert("Sorry, your session to EPS has expired. Please login again..");
        document.location="../db/Login/Logout.php"; </script>
    <?php
    }
}
else
{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../db/Login/Logout.php"; </script>
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
        <script src="../js/epo/WEPO014.js"></script>
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
                $( "#deliveryDate" ).datepicker({
                    dateFormat: 'dd/mm/yy',
                    defaultDate: "+1w",
                    minDate: dateToday,
                    autoClose: true,
                    beforeShowDay: $.datepicker.noWeekends
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
                <li class="active">
                    <a href="WEPO005.php">
                        <i class="icon-list-ul"></i><span>PO List</span> 
                    </a>
                </li>
                <li>
                    <a href="WEPO018.php">
                        <i class="icon-th"></i><span>PO Waiting</span> 
                    </a> 
                </li>
                <li>
                    <a href="WEPO013.php">
                        <i class="icon-copy"></i><span>PO Sent</span> 
                    </a>
                </li>
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
                    <form>
                        <!---------------------------------- Hidden Parameter --------------------------------->
                        <input type="hidden" id="countApp" value="<?php echo $getCountApprover; ?>" />
                        <input type="hidden" id="currencyCd" value="<?php echo $currencyCd; ?>" />
                        <input type="hidden" id="poStatus" value="<?php echo $poStatus; ?>" />
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
                                                <label class="control-label" for="poDate">PO Date: </label>
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
                                            <th rowspan="2">REF SUPPLIER</th>
                                            <th rowspan="2" style="display: none">SEQ ITEM</th>
                                        </tr>
                                        <tr>
                                            <th style="display: none">STATUS</th>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $poDetailNo = 0;
                                    $poTotal = 0;
                                    $countItemDel = 0;
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
                                        
                                        if($itemStatusSession == '1130'){
                                            $countItemDel++;
                                        }
                                        if($itemStatusSession == '1270'){
                                            $poDetailNo++;
                                            $poTotal = $poTotal + $amountSession;
                                        ?>
                                        <tr id="<?php echo $seqPoItemSession;?>">
                                            <td class="td-number">
                                                <?php echo $poDetailNo;?>.
                                            </td>
                                            <td class="td-actions-2columns">
                                                <a href="#" class="btn btn-small btn-success" id="window-edit">
                                                    <i class="btn-icon-only icon-edit "> </i>
                                                </a>
                                                <?php
                                                if(count($_SESSION['poDetail']) > 1 && $countItemDel == 0){
                                                ?>
                                                    <a href="#" class="btn btn-small btn-danger" id="window-delete">
                                                        <i class="btn-icon-only icon-remove"> </i>
                                                    </a>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $refTransferIdSession;?>
                                            </td>
                                            <td style="display: none" id="getItemStatus<?php echo $seqPoItemSession;?>">
                                                <?php echo $itemStatusSession;?>
                                            </td>
                                            <td>
                                                <?php echo $itemCdSession;?>
                                            </td>
                                            <td>
                                                <?php echo $itemNameSession;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo $qtySession;?>
                                            </td>
                                            <td>
                                                <?php echo $unitCdSession;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo number_format($itemPriceSession);?>
                                            </td>
                                            <td>
                                                <?php echo $currencyCdSession;?>
                                            </td>
                                            <td class="td-align-right amount">
                                                <?php echo number_format($amountSession);?>
                                            </td>
                                            <td style="width: 50px; text-align: center">
                                            <?php 
                                            if($totalSupplierSession == 0){
                                                echo '';
                                            }else{
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
                                        </tr>
                                        <?
                                            }
                                        }
                                        ?>    
                                        <tr>
                                            <th colspan="8" class="td-align-right">
                                                Total
                                            </th>
                                            <th style="text-align: right">
                                            <?php
                                                // Get total of amount item
                                                echo number_format($poTotal);
                                            ?>
                                            </th>
                                            <th>
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
                                                <label class="control-label" for="contact">U.p: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $contactName;?>" readonly />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="supplierName">Supplier: </label>
                                                <div class="controls">
                                                    <input type="text" class="span8" value="<?php echo $supplierName;?>" readonly />
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
                                                <label class="control-label" for="mailSupplier">Email: </label>
                                                <div class="controls">
                                                    <input type="text" class="span8" value="<?php echo $email;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poDate">To: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $deliveryPlant;?>" readonly />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="addRemark">** Additional Remark: </label>
                                                <div class="controls">
                                                    <input type="text" id="addRemark" class="span8" maxlength="200" value="<?php echo $addRemark; ?>" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="deliveryDate">Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $deliveryDate;?>" id="deliveryDate" readonly />
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
                        
                        <!---------------------------------- New PO Approver --------------------------------->
                        <div class="widget">
                            <div class="widget-header"> 
                                <i class="icon-list-ol "></i>
                                <h3>New PO Approver</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                    <?php
                                        $query_max_app = "select 
                                                            MAX(APPROVER_NO) as MAX_APPROVER
                                                          from 
                                                            EPS_M_PO_APPROVER ";
                                        $sql_max_app = $conn->query($query_max_app);
                                        $row_max_app = $sql_max_app->fetch(PDO::FETCH_ASSOC);
                                        $max_app = $row_max_app['MAX_APPROVER'];
                                        $approverNo = 0;
                                        for($i = 0; $i < $max_app; $i++){
                                            $approverNo++;
                                    ?>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="approver">Approver No. <?php echo $approverNo;?>: </label>
                                                <div class="controls">
                                                    <select id="approverNo<?php echo $approverNo;?>" class="span3" disabled>
                                                        <option value=""></option>
                                                    <?php
                                                        $query = "select 
                                                                    EPS_M_PO_APPROVER.APPROVER_NO
                                                                    ,EPS_M_PO_APPROVER.NPK
                                                                    ,EPS_M_LIMIT.LIMIT_AMOUNT
                                                                    ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                                from 
                                                                    EPS_M_PO_APPROVER 
                                                                inner join
                                                                    EPS_M_LIMIT 
                                                                on
                                                                    EPS_M_PO_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID
                                                                left join
                                                                    EPS_M_EMPLOYEE
                                                                on
                                                                    EPS_M_PO_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                                                where
                                                                    EPS_M_PO_APPROVER.APPROVER_NO = '$approverNo'
                                                                    and EPS_M_LIMIT.CURRENCY_CD = '$currencyCd'
                                                                order by
                                                                    APPROVER_SEQ";
                                                        $sql = $conn->query($query);
                                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                            $approverNpk  = $row['NPK'];
                                                            $approverName = $row['APPROVER_NAME'];
                                                    ?>
                                                        <option value="<?php echo $approverNpk;?>"><?php echo $approverName;?></option>
                                                    <?php 
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                                <?php
                                                    $query_set_app_val = "select 
                                                                            NPK
                                                                            ,APPROVER_NO
                                                                            ,APPROVAL_REMARK
                                                                            ,APPROVAL_STATUS
                                                                          from
                                                                            EPS_T_PO_APPROVER
                                                                          where
                                                                            PO_NO = '$paramPoNo'
                                                                            and APPROVER_NO = '$approverNo'";
                                                    $sql_set_app_val = $conn->query($query_set_app_val);
                                                    $row_set_app_val = $sql_set_app_val->fetch(PDO::FETCH_ASSOC);
                                                ?>
                                                <input type="hidden" class="span2" id="setApproverNpk<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['NPK']; ?>" />
                                                <input type="hidden" class="span2" id="setApproverNo<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['APPROVER_NO']; ?>" />
                                                <input type="hidden" class="span2" id="setApproverSts<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['APPROVAL_STATUS']; ?>" />
                                                <?php
                                                    if($approverNo != $max_app){
                                                ?>
                                                <div class="control-group">
                                                    <label class="checkbox inline">
                                                        <input type="checkbox" name="checkBypass[]" id="setBypassNo<?php echo $approverNo;?>" disabled>&nbsp;Bypass Approver No.&nbsp;<?php echo $approverNo;?>
                                                    </label>
                                                </div>
                                                <?php
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                    if($approverNo != $max_app){
                                                ?>
                                                <label class="control-label" for="remark">Remark: </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" id="setRemarkNo<?php echo $approverNo;?>" readonly />
                                                </div>
                                                <div class="control-group">
                                                    &nbsp;
                                                </div>
                                                <?php
                                                   }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        }
                                    ?>   
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                        
                    </form>
					
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory Select!</strong> Please select PO Approver.
                    </div>
                    <div class="alert" id="mandatory-msg-2" style="display: none">
                        <strong>Mandatory!</strong> Please input Reason for bypass approver.
                    </div>
                    <div class="alert" id="mandatory-msg-3" style="display: none">
                        <strong>Mandatory!</strong> Please select approver before bypass approval.
                    </div>
                    <div class="alert" id="mandatory-msg-4" style="display: none">
                        <strong>Mandatory!</strong> Please specify due date > current date.
                    </div>
                    <div class="alert" id="undefined-msg" style="display: none">
                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                    </div>
                    <div class="alert" id="session-msg" style="display: none">
                        <strong>Session expired!</strong> Session timeout or user has not login.<br>Please login again.
                    </div>
                    <div class="alert alert-success" id="save-msg" style="display: none">
                        <strong>Success!</strong> Save PO finished.
                    </div>
                    <div class="alert alert-success" id="send-msg" style="display: none">
                        <strong>Success!</strong> Send PO finished.
                    </div>
                    <div class="alert alert-success" id="cancel-msg" style="display: none">
                        <strong>Success!</strong> Cancel PO finished.
                    </div>
                    
                    <!---------------------------------- Button --------------------------------->
                    <div class="form-actions">
                        <button class="btn btn-primary" id="btn-send" disabled>Send PO</button> 
                        <button class="btn btn-success" id="btn-save" disabled>Save PO</button> 
                        <button class="btn btn-warning" id="btn-calculate">Calculate Approver</button> 
                        <button class="btn btn-danger" id="btn-cancel">Cancel PO</button> 
                        <button class="btn" id="btn-back">Back</button>
                    </div> <!-- /form-actions -->
					
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
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    
    <div class="widget ">
        <form id="WEPO014Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <input type="hidden" id="seqItem" />
                    <input type="hidden" id="refTransferIdHidden" />
                    <input type="hidden" id="currencyCdHidden" />
                    <input type="hidden" id="totalSupplierHidden" />
                    
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

<div id="dialog-confirm-session" title="Message" style="display: none;"></div>
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-send" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-save" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-back" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-cancel" title="Confirm" style="display: none;"></div>
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
