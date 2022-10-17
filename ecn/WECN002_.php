<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    if($sUserId != '')
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
                    <a href="../WCOM002.php">
                        <i class="icon-chevron-up"></i><span>Main</span> 
                    </a> 
                </li>
                <li class="active">
                    <a href="WECN001.php">
                        <i class=" icon-reorder"></i><span>PO Closed</span> 
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
                     <!----- PO Open Delivery ---->
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
                                                <label class="control-label" for="poNo">Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="poNo" name="poNo" maxlength="8" value="" />
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
                     <!----- PO Item List ---->
                        <div class="widget widget-table action-table">
                            <div class="widget-header"> <i class="icon-reorder"></i>
                                <h3>PO Closed</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th colspan="12">PO</th>
                                            <th rowspan="2">PR NO</th>
                                            <th colspan="3">RO</th>
                                        </tr>
                                        <tr>
                                            <th>CO.</th>
                                            <th>SUPPLIER</th>
                                            <th>PO NO</th>
                                            <th>BU</th>
                                            <th>LOC</th>
                                            <th>ITEM</th>
                                            <th style="display: none">ID</th>
                                            <th>QTY</th>
                                            <th>UM</th>
                                            <th>PRICE</th>
                                            <th>CUR</th>
                                            <th>TOTAL</th>
                                            <th>OBJ.AC</th>
                                            <th style="display: none">STATUS</th>
                                            <th>QTY</th>
                                            <th>DATE</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $itemNo = 0;
                                        $query_select_t_po = "select 
                                                                EPS_T_PO_HEADER.PO_NO
                                                                ,EPS_T_PO_HEADER.COMPANY_CD
                                                                ,EPS_T_PO_HEADER.PO_STATUS
                                                                ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                                                ,EPS_T_PO_HEADER.APPROVER
                                                                ,substring(EPS_T_PO_HEADER.DELIVERY_DATE, 7, 2) 
                                                                + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 5, 2) 
                                                                + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                                                                ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                ,EPS_T_PO_DETAIL.ITEM_CD
                                                                ,EPS_T_PO_DETAIL.ITEM_NAME
                                                                ,EPS_T_PO_DETAIL.QTY
                                                                ,EPS_T_PO_DETAIL.ITEM_PRICE
                                                                ,EPS_T_PO_DETAIL.AMOUNT
                                                                ,EPS_T_PO_HEADER.CURRENCY_CD
                                                                ,EPS_T_PO_DETAIL.UNIT_CD
                                                                ,EPS_T_PO_DETAIL.RO_STATUS
                                                                ,EPS_T_PO_DETAIL.ITEM_TYPE_CD
                                                                ,EPS_T_PO_DETAIL.ACCOUNT_NO
                                                                ,EPS_T_PO_DETAIL.RFI_NO
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
                                                                ,EPS_T_TRANSFER.PR_NO
                                                                ,EPS_M_ACCOUNT.ACCOUNT_CD
                                                                ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                                                ,EPS_T_PO_HEADER.DELIVERY_PLANT
                                                                ,EPS_T_RO_DETAIL.TRANSACTION_QTY
                                                                ,EPS_T_RO_DETAIL.CREATE_DATE
                                                                ,EPS_T_RO_DETAIL.TRANSACTION_FLAG
                                                                ,convert(VARCHAR(24), EPS_T_PO_HEADER.CLOSED_PO_DATE, 103) as CLOSED_PO_DATE
                                                            from
                                                                EPS_T_PO_DETAIL
                                                            left join
                                                                EPS_T_PO_HEADER
                                                            on 
                                                                EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                                                            left join
                                                                EPS_T_RO_DETAIL
                                                            on 
                                                                EPS_T_PO_DETAIL.PO_NO = EPS_T_RO_DETAIL.PO_NO
                                                                and EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_RO_DETAIL.REF_TRANSFER_ID
                                                            left join
                                                                EPS_T_TRANSFER
                                                            on
                                                                EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                                            left join
                                                                EPS_M_ACCOUNT
                                                            on
                                                                EPS_T_PO_DETAIL.ACCOUNT_NO = EPS_M_ACCOUNT.ACCOUNT_NO
                                                            where
                                                                EPS_T_PO_HEADER.PO_STATUS = '1280'
                                                            order by
                                                                EPS_T_PO_HEADER.SUPPLIER_NAME
                                                                ,EPS_T_PO_HEADER.COMPANY_CD
                                                                ,EPS_T_PO_HEADER.PO_NO
                                                                ,EPS_T_RO_DETAIL.CREATE_DATE ";
                                        $sql_select_t_po = $conn->query($query_select_t_po);
                                        while($row_select_t_po = $sql_select_t_po->fetch(PDO::FETCH_ASSOC)){
                                            $poNo           = $row_select_t_po['PO_NO'];
                                            $companyCd      = $row_select_t_po['COMPANY_CD'];
                                            $refTransferId  = $row_select_t_po['REF_TRANSFER_ID'];
                                            $poStatus       = $row_select_t_po['PO_STATUS'];
                                            $supplierName   = $row_select_t_po['SUPPLIER_NAME'];
                                            $deliveryDate   = $row_select_t_po['DELIVERY_DATE'];
                                            $approver       = $row_select_t_po['APPROVER'];
                                            $currencyCd     = $row_select_t_po['CURRENCY_CD'];
                                            $itemCd         = $row_select_t_po['ITEM_CD'];
                                            $itemName       = $row_select_t_po['ITEM_NAME'];
                                            $qty            = $row_select_t_po['QTY'];
                                            $itemPrice      = $row_select_t_po['ITEM_PRICE'];
                                            $amount         = $row_select_t_po['AMOUNT'];
                                            $unitCd         = $row_select_t_po['UNIT_CD'];
                                            $roStatus       = $row_select_t_po['RO_STATUS'];
                                            $totalReceivedQty= $row_select_t_po['TOTAL_RECEIVED_QTY'];
                                            $totalCanceledQty= $row_select_t_po['TOTAL_CANCELED_QTY'];
                                            $totalOpenedQty = $row_select_t_po['TOTAL_OPENED_QTY'];
                                            $transactionQty = $row_select_t_po['TRANSACTION_QTY'];
                                            $transactionFlag= $row_select_t_po['TRANSACTION_FLAG'];
                                            $createDate     = $row_select_t_po['CREATE_DATE'];
                                            $prNo           = $row_select_t_po['PR_NO'];
                                            $itemTypeCd     = $row_select_t_po['ITEM_TYPE_CD'];
                                            $accountNo      = $row_select_t_po['ACCOUNT_NO'];
                                            $accountCd      = $row_select_t_po['ACCOUNT_CD'];
                                            $rfiNo          = $row_select_t_po['RFI_NO'];
                                            $chargedBu      = $row_select_t_po['NEW_CHARGED_BU'];
                                            $deliveryPlant  = $row_select_t_po['DELIVERY_PLANT'];
                                            $closedPoDate   = $row_select_t_po['CLOSED_PO_DATE'];
                                            $totalActualReceivedQty = $totalReceivedQty - $totalCanceledQty - $totalOpenedQty;
                                            
                                            $itemNo++;
                                            if($itemNo == 1)
                                            {
                                                $initialCompanyCd       = $row_select_t_po['COMPANY_CD'];
                                                $initialSupplierName    = $row_select_t_po['SUPPLIER_NAME'];
                                                $initialPoNo            = $row_select_t_po['PO_NO'];
                                                $companyCd              = $initialCompanyCd;
                                                $supplierName           = $initialSupplierName;
                                                $poNo                   = $initialPoNo;
                                            }
                                            else
                                            {
                                                if($initialSupplierName == $row_select_t_po['SUPPLIER_NAME']
                                                    && $initialCompanyCd == $row_select_t_po['COMPANY_CD']
                                                    )
                                                {
                                                    $companyCd      = '';
                                                    $supplierName   = '';
                                                    
                                                }
                                                else
                                                {
                                                    $supplierName       = $row_select_t_po['SUPPLIER_NAME'];
                                                    $initialSupplierName= $row_select_t_po['SUPPLIER_NAME'];
                                                    
                                                    $companyCd          = $row_select_t_po['COMPANY_CD'];
                                                    $initialCompanyCd   = $row_select_t_po['COMPANY_CD'];
                                                    
                                                    
                                                }
                                                if($initialPoNo == $row_select_t_po['PO_NO'])
                                                {
                                                    $poNo           = '';
                                                }
                                                else
                                                {
                                                    $poNo = $row_select_t_po['PO_NO'];
                                                    $initialPoNo = $row_select_t_po['PO_NO'];
                                                }
                                                
                                            }
                                            
                                            if($itemTypeCd == '1' || $itemTypeCd == '3' || $itemTypeCd == '4')
                                            {
                                                $objectAccount = $accountCd;
                                            }
                                            if($itemTypeCd == '2')
                                            {
                                                $objectAccount = $rfiNoSession;
                                            }
                                    ?>
                                        <tr>
                                            <td class="td-number">
                                                <?php echo $itemNo;?>.
                                            </td>
                                            <td>
                                                <?php echo $companyCd;?>
                                            </td>
                                            <td>
                                                <?php echo $supplierName;?>
                                            </td>
                                            <td>
                                                <?php echo $poNo;?>
                                            </td>
                                            <td>
                                                <?php echo $chargedBu;?>
                                            </td>
                                            <td>
                                                <?php echo $deliveryPlant;?>
                                            </td>
                                            <td>
                                                <?php echo $itemName;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $refTransferId;?>
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
                                                <?php echo $unitCd;?>
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
                                            <td>
                                                <?php echo $currencyCd;?>
                                            </td>
                                            <td class="td-align-right">
                                            <?php 
                                                $split_amount = explode('.', $amount);
                                                if($split_amount[1] == 0)
                                                {
                                                    echo number_format($amount);
                                                }
                                                else
                                                {
                                                    echo number_format($amount,2);
                                                }
                                            ?>
                                            </td>
                                            <td>
                                                <?php echo $objectAccount;?>
                                            </td>
                                            <td>
                                                <?php echo $prNo;?>
                                            </td>
                                            <td style="display: none">
                                                <?php echo $roStatus;?>
                                            </td>
                                            <td class="td-align-right">
                                            <?php 
                                                $split = explode('.', $transactionQty);
                                                if($split[1] == 0)
                                                {
                                                    $transactionQty = number_format($transactionQty);
                                                }
                                                else
                                                {
                                                    $transactionQty = $transactionQty;
                                                }
                                                if($transactionFlag == 'C' || $transactionFlag == 'O')
                                                {
                                                    echo '-'.$transactionQty; 
                                                }
                                                else
                                                {
                                                    echo $transactionQty; 
                                                }
                                            ?>
                                            </td>
                                            <td>
                                            <?php 
                                                echo $createDate;
                                            ?>
                                            </td>
                                            <td>
                                            <?php 
                                                echo $transactionFlag;
                                            ?>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

    </body>
</html>
