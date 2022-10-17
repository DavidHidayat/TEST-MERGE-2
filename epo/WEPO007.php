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
        $sPoStatus  = $_SESSION['poStatus'];
        
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06')
        {
            if($poScreen == 'ApprovalPoScreen')
            {
                $poNoSession    = $_SESSION['poNoSession'];
                $paramPoNo      = $_GET['paramPoNo'];

                if($poNoSession == $paramPoNo)
                {
					if($sPoStatus == '1220')
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
                                    ,EPS_M_SUPPLIER.EMAIL_CC
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
							$addRemark      = stripslashes(trim($row['ADDITIONAL_REMARK']));
							$contactName    = $row['CONTACT'];
							$phone          = $row['PHONE'];
							$email          = $row['EMAIL'];
                            $emailCC        = $row['EMAIL_CC'];
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
                                $deliveryPlant = 'DENSO Fajar Plant';
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
                        <script language="javascript"> document.location="../ecom/WCOM013.php"; </script> 
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
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.js"></script>
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.js"></script> 
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Common_JQuery.js"></script>
        <script src="../js/epo/WEPO007.js"></script>
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
                    <!---------------------------------- Form --------------------------------->
                    <!--<form>-->
                        <!---------------------------------- Hidden Parameter --------------------------------->
                        <input type="hidden" id="countApp" value="<?php echo $getCountApprover; ?>" />
                        
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
                                                <label class="control-label" for="sendPoDate">Sent PO Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="deliveryDate">Delivery Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $deliveryDate;?>" id="deliveryDate" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="deliveryTo">Delivery Plant: </label>
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
                                                <label class="control-label" for="closedPoDate">Closed PO Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="closingMonth">Closing Month: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="" readonly />
                                                </div>
                                            </td>
                                            <td colspan="2">
                                                <label class="control-label" for="addRemark">** Additional Remark: </label>
                                                <div class="controls">
                                                    <input type="text" id="addRemark" class="span8" maxlength="200" value="<?php echo $addRemark; ?>" />
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
                                            <th rowspan="2">Attachment Proc</th>
                                            <th rowspan="2">PR NO</th>
                                            <th rowspan="2">EXP/<br>RFI NO</th>
                                            <th rowspan="2">Attachment User</th>
                                            <th rowspan="2">CIP</th>
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
                                                ,EPS_T_PO_DETAIL.ITEM_TYPE_CD
                                                ,EPS_T_PO_DETAIL.ACCOUNT_NO
                                                ,EPS_T_PO_DETAIL.RFI_NO
                                                ,(select count(*)
                                                from          
                                                    EPS_T_TRANSFER_SUPPLIER
                                                where      
                                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                                                as TOTAL_SUPPLIER
                                                ,EPS_T_TRANSFER.PR_NO
                                                ,EPS_T_PO_DETAIL.ATTACHMENT AS CIP
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
                                                EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
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
                                            $prNoSession            = $row['PR_NO'];
                                            $itemTypeCdSession      = $row['ITEM_TYPE_CD'];
                                            $accountNoSession       = $row['ACCOUNT_NO'];
                                            $rfiNoSession           = $row['RFI_NO'];
                                            $cipSession           = $row['CIP'];
											
											if($itemTypeCdSession == '1' || $itemTypeCdSession == '3' || $itemTypeCdSession == '4')
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
                                            <td>
                                                <a href="#" class="faq-list">
                                                    <b><?php echo $prNoSession;?></b>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo $objectAccount;?>
                                            </td>
                                            <td class="td-actions">
                                                <a href="#" class="btn btn-small btn-success" id="window-attach">
                                                    <i class="btn-icon-only icon-paper-clip "> </i>
                                                </a>
                                            </td>
                                            <td>
                                                <a href='file://///10.82.101.2/EPS/Rfi/<?php echo $cipSession?>' target='_blank'  style='color: #19BC9C'><?php echo $cipSession?></a>
                                                <!--<a href='file://///10.82.101.31/tacifss02/TACI General Database/C000 - Supporting/C020 - Indirect/C020-T4100 - Procurement/PROCUREMENT/EPS/Rfi/<?php echo $cipSession?>' target='_blank'  style='color: #19BC9C'><?php echo $cipSession?></a>-->
                                            </td>
                                        </tr>
                                        <?
                                        $seqPoItem++;
                                        }
                                        ?>    
                                        <tr>
                                            <th colspan="9" class="td-align-right">
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
                                            <th colspan="3">
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
                                                    <input type="text" id="mailCC" class="span8" value="<?php echo $emailCC; ?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                        
                        <!---------------------------------- PO Approver --------------------------------->
                        <div class="widget">
                            <div class="widget-header"> 
                                <i class="icon-list-ol "></i>
                                <h3>PO Approver</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                    <?php
                                        $query_max_app = "select 
                                                            MAX(APPROVER_NO) as MAX_APPROVER
                                                          from 
                                                            EPS_T_PO_APPROVER
                                                          where
                                                            PO_NO = '$paramPoNo'";
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
                                                    <?php
                                                        $query_set_app_val = "select 
                                                                                EPS_T_PO_APPROVER.NPK
                                                                                ,EPS_T_PO_APPROVER.APPROVER_NO
                                                                                ,EPS_T_PO_APPROVER.APPROVAL_DATE
                                                                                ,EPS_T_PO_APPROVER.APPROVAL_REMARK
                                                                                ,EPS_T_PO_APPROVER.APPROVAL_STATUS
                                                                                ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                                                              from
                                                                                EPS_T_PO_APPROVER
                                                                              left join
                                                                                EPS_M_APPROVAL_STATUS
                                                                              on
                                                                                EPS_T_PO_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
                                                                              where
                                                                                PO_NO = '$paramPoNo'
                                                                                and APPROVER_NO = '$approverNo'";
                                                        $sql_set_app_val = $conn->query($query_set_app_val);
                                                        $row_set_app_val = $sql_set_app_val->fetch(PDO::FETCH_ASSOC)
                                                    ?>
                                                    <input type="hidden" class="span2" id="setApproverNpk<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['NPK']; ?>" />
                                                    <input type="hidden" class="span2" id="setApproverNo<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['APPROVER_NO']; ?>" />
                                                    <input type="hidden" class="span2" id="setApproverSts<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['APPROVAL_STATUS']; ?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="reason">Status: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="setStatusNo<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['APPROVAL_STATUS_NAME']?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="reason">Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="setReasonNo<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['APPROVAL_DATE']?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="remark">Remark: </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" id="setRemarkNo<?php echo $approverNo;?>" value="<?php echo $row_set_app_val['APPROVAL_REMARK']?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    <?php        
                                        }
                                    ?>  
                                    </table>
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
						
                        <!---------------------------------- Approver Information --------------------------------->
                        <div class="widget" id="approver-information">
                            <div class="widget-header">
                                <i class="icon-info-sign"></i>
                                <h3>Approver Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="remarkApprover">Comment: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" id="remarkApprover" maxlength="200" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
									
									<!---------------------------------- Message --------------------------------->
									<div class="alert" id="mandatory-msg-1" style="display: none">
										<strong>Mandatory!</strong> Please fill "Comment" for Reject PO.
									</div>
									<div class="alert" id="undefined-msg" style="display: none">
										<strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
									</div>
									<div class="alert" id="session-msg" style="display: none">
										<strong>Session expired!</strong> Session timeout or user has not login. Please login again.
									</div>
									<div class="alert alert-success" id="success-msg" style="display: none">
										<strong>Success!</strong> Approve PO finished.
									</div>
									<div class="alert alert-success" id="reject-msg" style="display: none">
										<strong>Success!</strong> Reject PO finished.
									</div>
					
									<!---------------------------------- Button --------------------------------->
									<div class="form-actions">
										<button class="btn btn-primary" id="btn-approve">Approve</button> 
										<button class="btn btn-danger" id="btn-reject">Reject</button> 
										<button class="btn" id="btn-back">Back</button>
									</div> <!-- /form-actions -->
									
                                </div><!-- /control-group -->
                            </div><!-- /widget-content -->
                        </div><!-- /widget -->
                        
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

<div id="dialog-confirm-session" title="Message" style="display: none;"></div>
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-approve" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-reject" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-back" title="Confirm" style="display: none;"></div>
<div id="dialog-refsupplier-table" title="Reference Supplier List" style="display: none;">
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
<div id="dialog-attach-table" title="PR Attachment" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-attach">
            </div>
        </div>
    </div>
</div>
    </body>
</html>
