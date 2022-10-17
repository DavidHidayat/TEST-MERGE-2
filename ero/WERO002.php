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
        $roScreen   = $_SESSION['roScreen'];
        $sRoStatus  = $_SESSION['roStatus'];
        $poHeaderUpdateDate=$_SESSION['poHeaderUpdateDate'];
        
        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_05' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_11')
        {
            if($roScreen == 'EditOpenRoScreen')
            {
                $refTransferIdSession   = $_SESSION['refTransferIdSession'];
                $poNoSession            = $_SESSION['poNoSession'];
                $transferId             = $_GET['paramRefTransferId'];
                $poNoPrm                = $_GET['xParamPoNo'];
                
                if($refTransferIdSession == $transferId && $poNoPrm == $poNoSession)
                {
                    if($sRoStatus != '')
                    {
                        $wherePoDetail = array();
                        if($transferId){
                            $wherePoDetail[] = "EPS_T_PO_DETAIL.REF_TRANSFER_ID = '".$transferId."'";
                        }
						if($poNoPrm){
                            $wherePoDetail[] = "EPS_T_PO_DETAIL.PO_NO = '".$poNoPrm."'";
                        }
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
                                                        ,EPS_T_PO_HEADER.CURRENCY_CD
                                                        ,EPS_T_TRANSFER.PR_NO
                                                        ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                        ,EPS_T_PR_HEADER.EXT_NO
                                                        ,isnull(
                                                            (select 
                                                                sum(TRANSACTION_QTY)
                                                             from 
                                                                EPS_T_RO_DETAIL
                                                             where   
                                                                EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                                and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'A')
                                                            ,0
                                                         )
                                                         as TOTAL_RECEIVED_QTY
                                                        ,EPS_T_PO_DETAIL.RO_STATUS
                                                        ,EPS_M_APP_STATUS.APP_STATUS_NAME as RO_STATUS_NAME
                                                        ,EPS_T_PO_HEADER.UPDATE_DATE
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
                                                        EPS_M_EMPLOYEE
                                                    on 
                                                        EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                                    left join 
                                                        EPS_T_PR_HEADER
                                                    on
                                                        EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                                    left join
                                                        EPS_M_APP_STATUS 
                                                    on 
                                                        EPS_M_APP_STATUS.APP_STATUS_CD = EPS_T_PO_DETAIL.RO_STATUS ";
                        if(count($wherePoDetail)) {
                            $query_select_t_po_detail .= "where " . implode('and ', $wherePoDetail);
                        }

                        $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                        while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC)){
                            $refTransferId  = $row_select_t_po_detail['REF_TRANSFER_ID'];
                            $poNo           = $row_select_t_po_detail['PO_NO'];
                            $supplierName   = $row_select_t_po_detail['SUPPLIER_NAME'];
                            $deliveryDate   = $row_select_t_po_detail['DELIVERY_DATE'];
                            $itemCd         = $row_select_t_po_detail['ITEM_CD'];
                            $itemName       = $row_select_t_po_detail['ITEM_NAME'];
                            $qty            = $row_select_t_po_detail['QTY'];
                            $unitCd         = $row_select_t_po_detail['UNIT_CD'];
                            $itemPrice      = $row_select_t_po_detail['ITEM_PRICE'];
                            $currencyCd     = $row_select_t_po_detail['CURRENCY_CD'];
                            $prNo           = $row_select_t_po_detail['PR_NO'];
                            $requesterName  = $row_select_t_po_detail['REQUESTER_NAME'];
                            $extNo          = $row_select_t_po_detail['EXT_NO'];
                            $totalReceivedQty= $row_select_t_po_detail['TOTAL_RECEIVED_QTY'];
                            $roStatus       = $row_select_t_po_detail['RO_STATUS'];
                            $roStatusName   = $row_select_t_po_detail['RO_STATUS_NAME'];
                            $updateDate     = $row_select_t_po_detail['UPDATE_DATE'];
							
							$split_qty = explode('.', $qty);
                            if($split_qty[1] == 0)
                            {
                                $qty = number_format($qty);
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
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script> 
        <script src="../lib/jquery/jquery-ui-1.11.2.custom/jquery-ui.js"></script> 
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Common_JQuery.js"></script>
        <script src="../js/ero/WERO002.js"></script>
        <script>
            $(function() {
                var dateToday = new Date();
                $("#receivedDate").datepicker({
                    dateFormat: 'dd/mm/yy',
                    //defaultDate: "+1w",
					minDate: '0',
                    maxDate: '0',
                    autoClose: true,
                    beforeShowDay: $.datepicker.noWeekends
                });
                
            });
        </script>
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
                    
                    <!---------------------------------- Form --------------------------------->
                    <form> 
                        <!---------------------------------- Hidden Parameter --------------------------------->
                        <input type="hidden" id="umHidden" value="<?php echo $unitCd;?>" />
                        <input type="hidden" id="refTransferIdHidden" value="<?php echo $refTransferId;?>" />
                        <input type="hidden" id="roStatusHidden" value="<?php echo $roStatus;?>" />
                        <input type="hidden" id="updateDate" value="<?php echo $poHeaderUpdateDate;?>" readonly />
                        
                        <!---------------------------------- PO Detail --------------------------------->
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
                                            <td colspan="2">
                                                <label class="control-label" for="supplierName">Supplier: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" value="<?php echo $supplierName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="currencyCd">Currency: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $currencyCd;?>" id="currencyCd" maxlength="5" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="itemCd">Item Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $itemCd;?>" id="itemCd" maxlength="5" readonly />
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                <label class="control-label" for="itemName">Item Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="full-width-input" value="<?php echo $itemName;?>" id="itemName" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="qty">Qty: </label>
                                                <div class="controls">
                                                    <input type="text" id="qty" class="span2 input-align-right" value="<?php echo $qty;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="um">UM: </label>
                                                <div class="controls">
                                                    <select id="um" class="span2" disabled >
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
                                                <label class="control-label" for="itemPrice">Item Price: </label>
                                                <div class="controls">
                                                    <input type="text" id="itemPrice" class="input-align-right" value="<?php echo number_format($itemPrice);?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="deliveryDate">Due Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $deliveryDate;?>" id="currencyCd" maxlength="5" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!--<label class="control-label" for="receivedStatus">Status: </label>
                                                <div class="controls">
                                                    <input type="text" value="<?php echo $roStatusName; ?>" id="receivedStatus" maxlength="5" readonly />
                                                </div>-->
                                            </td>
                                            <td>
                                                <!--<label class="control-label" for="receivedQty">Total Received Qty: </label>
                                                <div class="controls">
                                                    <input type="text" id="totalReceivedQty" class="span2 input-align-right" value="<?php echo $totalReceivedQty;?>" readonly />
                                                </div>-->
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!---------------------------------- PR Header --------------------------------->
                        <div class="widget">
                            <div class="widget-header">
                                <i class="icon-file"></i>
                                <h3>PR Information</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prNo">PR No: </label>
                                                <div class="controls">
                                                    <input type="text" id="prNo" class="span2" value="<?php echo $prNo;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="$requesterName">Requester: </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" value="<?php echo $requesterName;?>" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="extNo">Nice-net: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" value="<?php echo $extNo;?>" readonly />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!---------------------------------- Receiving List --------------------------------->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-th-list"></i>
                                <h3>Receiving Detail</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered" id="receivingListTable">
                                    <thead>
                                        <tr>
                                            <th class="td-actions" colspan="10" style="text-align: left">
                                                <a href="#" class="btn btn-small btn-warning" id="window-add">
                                                    <i class="btn-icon-only icon-plus"> </i>
                                                </a>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>NO</th>
                                            <!--<th class="td-actions-3columns">ACTION</th>-->
                                            <th style="display: none">RO NO</th>
                                            <th style="display: none">PO NO</th>
                                            <th style="display: none">TRANSFER ID</th>
                                            <th>DATE</th>
                                            <th>QTY</th>
                                            <th style="display: none">SEQ RECEIVED</th>
                                            <th style="width: 60%">REMARK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $itemNo         = 0;
                                    $totalReceived  = 0;
                                    
                                    if(strlen($_SESSION['roDetail']) > 0){
                                        foreach ($_SESSION['roDetail'] as $roDetail ) 
                                        { 
                                            $roNoSession            = $roDetail['roNo'];
                                            $roSeqSession           = $roDetail['roSeq'];
                                            $poNoSession            = $roDetail['poNo']; 
                                            $refTransferIdSession   = $roDetail['refTransferId'];  
                                            $transactionQtySession  = $roDetail['transactionQty'];
                                            $transactionFlagSession = $roDetail['transactionFlag'];
                                            $transactionDateSession = $roDetail['transactionDate'];
                                            $roRemarkSession        = $roDetail['roRemark'];
                                            if($transactionFlagSession == 'A')
                                            {
                                                $totalReceived          = $totalReceived + $transactionQtySession;
                                            }
                                            if($transactionFlagSession == 'C' || $transactionFlagSession == 'O')
                                            {
                                                $totalReceived          = $totalReceived - $transactionQtySession;
                                            }
                                            $itemNo++;
                                    ?>
                                        <tr id="<?php echo $receivedSeqSession;?>">
                                            <td class="td-number">
                                                <?php echo $itemNo; ?>.
                                            </td>
                                            <!--<td class="td-actions-3columns">
                                                <a href="#" class="btn btn-small btn-success" id="window-edit">
                                                    <i class="btn-icon-only icon-edit"> </i>
                                                </a>
                                                <a href="#" class="btn btn-small btn-danger" id="window-delete">
                                                    <i class="btn-icon-only icon-remove"> </i>
                                                </a>
                                            </td>-->
                                            <td style="display: none">
                                                <?php echo $roNoSession; ?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $poNoSession; ?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $refTransferIdSession; ?>
                                            </td>
                                            <td>
                                                <?php echo $transactionDateSession; ?>
                                            </td>
                                            <td class="td-align-right">
                                            <?php 
                                                $split_qty = explode('.', $transactionQtySession);
                                                if($split_qty[1] == 0)
                                                {
                                                    $transactionQtySession = number_format($transactionQtySession);
                                                }
                                                if($transactionFlagSession == 'C' || $transactionFlagSession == 'O')
                                                {
                                                    echo '-'.$transactionQtySession; 
                                                }
                                                else
                                                {
                                                    echo $transactionQtySession; 
                                                }
                                            ?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $roSeqSession; ?>
                                            </td>
                                            <td>
                                                <?php echo $roRemarkSession; ?>  
                                            </td>
                                        </tr>
                                    <?php
                                    
                                        }
                                    ?>
                                        <tr>
                                            <th colspan="2">
                                                Total
                                            </th>
                                            <th>
                                                <?php echo $totalReceived;?>
                                            </th>
                                            <th>
                                                
                                            </th>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>       
                        </div>
                    </form>
                    
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Receiving detail empty. Please input receiving detail at least one data before save.
                    </div>
                    <div class="alert" id="mandatory-msg-2" style="display: none">
                        <strong>Failure!</strong> Data already updated by another user.
                    </div>
                    <div class="alert" id="mandatory-msg-3" style="display: none">
                        <strong>Failure!</strong> Please input receiving qty <= PO qty.
                    </div>
                    <div class="alert" id="undefined-msg" style="display: none">
                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                    </div>
                    <div class="alert" id="session-msg" style="display: none">
                        <strong>Session expired!</strong> Session timeout or user has not login.<br>Please login again.
                    </div>
                    <div class="alert alert-success" id="success-msg" style="display: none">
                        <strong>Success!</strong> Save item finished.
                    </div>
                    <div class="alert alert-success" id="success-msg-close-item" style="display: none">
                        <strong>Success!</strong> Save item finished and delivery already close.
                    </div>
                    <div class="alert alert-success" id="success-msg-close-po" style="display: none">
                        <strong>Success!</strong> Save item finished and PO Close.
                    </div>
                    
                   <!---------------------------------- Button --------------------------------->
                    <div class="form-actions">
                        <button class="btn btn-primary" id="btn-save-item">Save Item</button> 
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

<div id="dialog-form" title="Add Received Qty" style="display: none;">
    <div class="alert" id="dialog-mandatory-msg-1" style="display: none;">
        <strong>Mandatory!</strong> Please fill all the field.
    </div>
    <div class="alert" id="dialog-mandatory-msg-2" style="display: none;">
        <strong>Mandatory!</strong> Please input qty > 0.
    </div>
    <div class="alert" id="dialog-mandatory-msg-3" style="display: none;">
        <strong>Mandatory!</strong> Please input received qty <= total received qty
    </div>
    <div class="alert" id="dialog-mandatory-msg-4" style="display: none;">
        <strong>Mandatory!</strong> Please check action Add or Edit.
    </div>
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    <div class="widget">
        <form id="WERO002Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <input type="hidden" id="roSeq" />
                    <input type="hidden" id="roNo" />
                    <input type="hidden" id="initialReceivedQty" />
                    <input type="hidden" id="tempTotalReceivedQty" value="<?php echo $totalReceived; ?>" />
                    
                    <table class="table-non-bordered" id="table-supplier-ref">
                        <tr>
                            <td>
                                <label class="control-label" for="receivedDate">Date: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="receivedDate" class="span3"  tabindex="-1" readonly />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="receivedQty">Qty: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="receivedQty" class="span2" maxlength="5" />
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
<div id="dialog-confirm-save" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-back" title="Confirm" style="display: none;"></div>
    </body>
</html>