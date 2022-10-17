<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    if($sUserId != '')
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
    
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_05')
        {
            $paramPoNo      = $_GET['paramPoNo'];
                        $wherePoHeader = array();
                        if($paramPoNo){
                            $wherePoHeader[] = "EPS_T_PO_HEADER.PO_NO = '".$paramPoNo."'";
                        }
                        $query = "select 
                                EPS_T_PO_HEADER.PO_NO
                                ,substring(EPS_T_PO_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                ,EPS_T_PO_HEADER.SUPPLIER_CD
                                ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                                ,EPS_T_PO_HEADER.DELIVERY_PLANT
                                ,EPS_T_PO_HEADER.ADDITIONAL_REMARK
                                ,EPS_M_SUPPLIER.CONTACT
                                ,EPS_M_SUPPLIER.PHONE
                                ,EPS_M_SUPPLIER.EMAIL
                                ,(select count(*)
                                    from
                                        EPS_T_PO_DETAIL
                                    where
                                        PO_NO = '$paramPoNo')
                                as COUNT_ITEM
                                ,EPS_M_APP_STATUS.APP_STATUS_NAME
                                ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_NAME
                            from
                                EPS_T_PO_HEADER
                            left join
                                EPS_T_PO_DETAIL
                            on
                                EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO
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
                        $issuedDate     = $row['ISSUED_DATE'];
                        $supplierCd     = $row['SUPPLIER_CD'];
                        $supplierName   = $row['SUPPLIER_NAME'];
                        $deliveryDate   = $row['DELIVERY_DATE'];
                        $deliveryPlant  = $row['DELIVERY_PLANT'];
                        $addRemark      = $row['ADDITIONAL_REMARK'];
                        $contactName    = $row['CONTACT'];
                        $phone          = $row['PHONE'];
                        $email          = $row['EMAIL'];
                        $getCountItem   = $row['COUNT_ITEM'];
                        $poStatusName   = $row['APP_STATUS_NAME'];
                        $issuedName     = $row['ISSUED_NAME'];
                        if($deliveryPlant == 'JK'){
                            $deliveryPlant = 'DENSO Sunter Plant';
                        }
                        
                        if($deliveryPlant == 'GT'){
                            $deliveryPlant = 'TACI PLANT';
                        }
                        
                        if($deliveryPlant == 'JF'){
                            $deliveryPlant = 'DENSO 3rd Plant';
                        }
                    }
        }
        else
        {
        ?>
            <script language="javascript"> alert("Sorry, this page only can be accessed by General Supplies.");
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
        <script src="../js/ero/WERO005.js"></script>
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
                <li>
                    <a href="WERO001.php">
                        <i class=" icon-plus-sign"></i><span>Open Delivery</span> 
                    </a> 
                </li> 
                <li class="active">
                    <a href="WERO003.php">
                        <i class="icon-ok-sign"></i><span>Closed Delivery</span> 
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
                        <input type="hidden" id="countItem" value="<?php echo $getCountItem; ?>" />
                        
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
                                                <label class="control-label" for="poNo">PO No:</label>
                                                <div class="controls">
                                                    <input type="text" id="poNo" class="span2" value="<?php echo $poNo;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="poNo">PO Date:</label>
                                                <div class="controls">
                                                    <input type="text" id="poDate" class="span2" value="<?php echo $issuedDate;?>" readonly />
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
                                <table class="table table-striped table-bordered" id="poListTable">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2" style="display: none">RO NO</th>
                                            <th rowspan="2" style="display: none">PO NO</th>
                                            <th rowspan="2" style="display: none">TRANSFER ID</th>
                                            <th colspan="2">ITEM</th>
                                            <th rowspan="2">QTY</th>
                                            <th rowspan="2">UM</th>
                                            <th rowspan="2">PRICE</th>
                                            <th rowspan="2">STATUS</th>
                                            <th rowspan="2">REMARK</th>
                                            <th rowspan="2">REF<br>RECEIVING</th>
                                            <th rowspan="2" style="display: none">SEQ RECEIVED</th>
                                        </tr>
                                        <tr>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                    <?php
                                    $poTotal = 0;
                                    foreach ( $_SESSION['poDetail'] as $poDetails ) 
                                    {
                                        $poNoSession            = $poDetails['poNo'];
                                        $refTransferIdSession   = $poDetails['refTransferId'];  
                                        $itemCdSession          = $poDetails['itemCd'];
                                        $itemNameSession        = $poDetails['itemName'];  
                                        $qtySession             = $poDetails['qty'];
                                        $unitCdSession          = $poDetails['unitCd'];  
                                        $itemPriceSession       = $poDetails['itemPrice'];  
                                        $roStatusSession        = $poDetails['roStatus'];
                                        $roStatusNameSession    = $poDetails['roStatusName'];
                                        $seqPoItemSession       = $poDetails['seqPoItem'];
                                    ?>
                                        <tr id="<?php echo $seqPoItemSession;?>">
                                            <td class="td-number" id="getSeqPoItem<?php echo $seqPoItemSession;?>">
                                                <?php echo $seqPoItemSession;?>.
                                            </td>
                                            <td style="display: none">
                                                
                                            </td>
                                            <td style="display: none">
                                                <?php echo $poNoSession;?>
                                            </td>
                                            <td id="getRefTransferId<?php echo $seqPoItemSession;?>" style="display: none">
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
                                                <select id="roStatus<?php echo $seqPoItemSession;?>"  style="width: 75px">
                                            <?php
                                                $query = "select 
                                                            APP_STATUS_CD
                                                            ,APP_STATUS_NAME
                                                          from
                                                            EPS_M_APP_STATUS
                                                          where
                                                            APP_STATUS_CD = '1310'
                                                            or APP_STATUS_CD = '1320' ";
                                                $sql = $conn->query($query);
                                                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                    $appStatusCd    = $row['APP_STATUS_CD'];
                                                    $appStatusName  = $row['APP_STATUS_NAME'];
                                            ?>
                                                    <option value="<?php echo $appStatusCd;?>"><?php echo $appStatusName;?></option>
                                            <?php          
                                                 }
                                            ?>
                                                </select>
                                                <input type="hidden" class="span2" id="setRoStatus<?php echo $seqPoItemSession;?>" value="<?php echo $roStatusSession;?>">
                                            </td>
											<td>
												<input type="text" class="span3" id="setOpenRemark<?php echo $seqPoItemSession;?>">	
											</td>
                                            <td>
                                                <a href="#" class="btn btn-small btn-inverse" id="window-receiving">
                                                    <i class="btn-icon-only icon-external-link "> </i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php    
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <!--</form>-->
                    
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="undefined-msg" style="display: none">
                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                    </div>
                    <div class="alert" id="session-msg" style="display: none">
                        <strong>Session expired!</strong> Session timeout or user has not login.<br>Please login again.
                    </div>
                    <div class="alert alert-success" id="save-msg" style="display: none">
                        <strong>Success!</strong> Save PO finished.
                    </div>
                    
                    <!---------------------------------- Button --------------------------------->
                    <div class="form-actions">
                        <button class="btn btn-primary" id="btn-save">Save</button> 
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

<div id="dialog-receiving-table" title="Receiving List" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-receiving">
            </div>
        </div>
    </div>
</div>

<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-save" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-back" title="Confirm" style="display: none;"></div>
    </body>
</html>
