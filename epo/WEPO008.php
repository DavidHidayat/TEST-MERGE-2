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
        
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06')
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
                    $addRemark      = $row['ADDITIONAL_REMARK'];
                    $contactName    = $row['CONTACT'];
                    $phone          = $row['PHONE'];
                    $email          = $row['EMAIL'];
                    $getCountApprover= $row['COUNT_APPROVER'];
                    $poStatusName   = $row['APP_STATUS_NAME'];
                    $issuedName     = $row['ISSUED_NAME'];
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
        <script src="../js/epo/WEPO008.js"></script>
        <script>
            maximize();
        </script>
        <title>EPS</title>
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="../js/html5.js"></script>
        <![endif]-->
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
                    <!---------------------------------- Button --------------------------------->
                    <div class="form-actions">
                        <button class="btn btn-primary" id="btn-takeover">Takeover</button> 
                        <button class="btn" id="btn-back">Back</button>
                    </div> <!-- /form-actions -->
                    <form>
                        
                        <!---------------------------------- PO Header --------------------------------->
                        <div class="widget ">
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
                                                <label class="control-label" for="poStatus">PO Status: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $poStatusName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="issuedBy">Issued By: </label>
                                                <div class="controls">
                                                    <input type="text" id="issuedBy" class="span3" maxlength="200" value="<?php echo $issuedName; ?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                        
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
                                                    <input type="text" id="addRemark" class="span8" maxlength="200" value="<?php echo $addRemark; ?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="poDate">Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" value="<?php echo $deliveryDate;?>" readonly />
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
                                            <th rowspan="2" style="display: none">TRANSFER ID</th>
                                            <th colspan="2">ITEM</th>
                                            <th rowspan="2">QTY</th>
                                            <th rowspan="2">UM</th>
                                            <th rowspan="2">PRICE</th>
                                            <th rowspan="2">CUR</th>
                                            <th rowspan="2">TOTAL</th>
                                            <th rowspan="2">REF SUPPLIER</th>
                                        </tr>
                                        <tr>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $poTotal = 0;
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
                                            where 
                                                EPS_T_PO_DETAIL.PO_NO ='".$paramPoNo."'";
                                        $sql = $conn->query($query);
                                        $seqPoItem=1;
                                        while($row = $sql->fetch(PDO::FETCH_ASSOC)){ 
                                            $poNoSession            = $row['PO_NO'];
                                            $refTransferIdSession   = $row['REF_TRANSFER_ID'];  
                                            $itemCdSession          = $row['ITEM_CD'];
                                            $itemNameSession        = $row['ITEM_NAME'];  
                                            $qtySession             = $row['QTY'];
                                            $itemPriceSession       = $row['ITEM_PRICE'];  
                                            $amountSession          = $row['AMOUNT']; 
                                            $currencyCdSession      = $row['CURRENCY_CD'];
                                            $unitCdSession          = $row['UNIT_CD'];  
                                            $seqPoItemSession       = $seqPoItem;
                                            $totalSupplierSession   = $row['TOTAL_SUPPLIER'];
                                            $poTotal = $poTotal + $amountSession;
                                        ?>
                                        <tr id="<?php echo $seqPoItemSession;?>">
                                            <td class="td-number">
                                                <?php echo $seqPoItemSession;?>.
                                            </td>
                                            <td style="display: none">
                                                <?php echo $refTransferIdSession;?>
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
                                        </tr>
                                        <?
                                        $seqPoItem++;
                                        }
                                        ?>    
                                        <tr>
                                            <th colspan="7" class="td-align-right">
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
                        
                    </form>
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
