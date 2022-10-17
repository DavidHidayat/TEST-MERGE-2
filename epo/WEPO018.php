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
        unset($_SESSION['poStatus']);
        unset($_SESSION['itemStatusSession']);
        
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
        
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')
        {
            /**
             * Check in PO approver list.
             */
            $query_m_po_approver = "select 
                                        count(NPK) as COUNT_PO_APP
                                    from
                                        EPS_M_PO_APPROVER
                                    where
                                        NPK = '".$sUserId."'";
            $sql_m_po_approver = $conn->query($query_m_po_approver);
            $row_m_po_approver = $sql_m_po_approver->fetch(PDO::FETCH_ASSOC);
            $countApp = $row_m_po_approver['COUNT_PO_APP'];
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
        <script src="../js/epo/WEPO018.js"></script>
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
                <li>
                    <a href="WEPO005.php">
                        <i class="icon-list-ul"></i><span>PO List</span> 
                    </a>
                </li>
                <li class="active">
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
                    <?php
                    if($sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04'){
                    ?>
                    <div class="form-actions">
                        <button class="btn btn-primary" id="btn-approve">Approve</button> 
                        <input type="text" class="span1" id="countCheck" name="countCheck" value="0" readonly />
                        <input type="hidden" class="span2" id="userIdApprover" name="userIdApprover" value="<?php echo $sUserId;?>" readonly />
                    </div> <!-- /form-actions -->
                    <?php
                    }
                    ?>
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please select at least one PO to Approve.
                    </div>
                    <div class="alert" id="mandatory-msg-2" style="display: none">
                        <strong>Range Error!</strong> Please select PO less than equal 15 (<= 15) to Approve.
                    </div>
                    <div class="alert" id="undefined-msg" style="display: none">
                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                    </div>
                    <div class="alert alert-success" id="success-msg" style="display: none">
                        <strong>Success!</strong> Approve PO finished.
                    </div>
                    
                    <!----- PO List ---->
                    <div class="widget widget-table action-table">
                        <div class="widget-header"> <i class="icon-list-ul"></i>
                            <h3>PO List</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered" id="poListTable">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="display: none;">REF TRANSFER ID</th>
                                        <th rowspan="2" style="display: none;">NO</th>
                                        <?php
                                        if(($sRoleId == 'ROLE_06' && $sBunit == '3100 ')){
                                        ?>
                                            <th rowspan="2"><input type="checkbox" id="selectAll" /></th>
                                        <?php
                                        }
                                        else
                                       {
                                            if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06')
                                            {
                                        ?>
                                            <th rowspan="2">&nbsp;</th>
                                        <?php  
                                            }
                                        }    
                                        ?>
                                        <th rowspan="2">PO NO</th>
                                        <th rowspan="2" style="display: none;">PO NO REF</th>
                                        <th rowspan="2">SUPPLIER</th>
                                        <th rowspan="2">DUE DATE</th>
                                        <th rowspan="2">ISSUED<br>BY</th>
                                        <th rowspan="2">PR LAST DATE<br>APPROVED</th>
                                        <th colspan="2">ITEM</th>
                                        <th rowspan="2">QTY</th>
                                        <th rowspan="2">UM</th>
                                        <th rowspan="2">PRICE</th>
                                        <th rowspan="2">CUR</th>
                                        <th rowspan="2">TOTAL</th>
                                        <th rowspan="2">CATG.</th>
                                        <th colspan="2">REFERENCE</th>
                                        <th rowspan="2" style="display: none;">REQUESTER NPK</th>
                                    </tr>
                                    <tr>
                                        <th>CODE</th>
                                        <th>NAME</th>
                                        <th>SUPPLIER</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $poListNo = 0;
                                    $query = "select
                                                EPS_T_PO_DETAIL.PO_NO
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
                                                ,EPS_M_EMPLOYEE.NAMA1 as ISSUED_BY_NAME
                                                ,EPS_T_PO_DETAIL.UNIT_CD
                                                ,(select count(*)
                                                from          
                                                    EPS_T_TRANSFER_SUPPLIER
                                                where      
                                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                                                as TOTAL_SUPPLIER
                                                ,(select top 1 CONVERT(VARCHAR(24), EPS_T_PR_APPROVER.APPROVAL_DATE, 103)
                                                from         
                                                    EPS_T_PR_APPROVER
                                                where      
                                                    EPS_T_PR_APPROVER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                                order by APPROVAL_DATE DESC) as LAST_APPROVAL_DATE
                                                ,EPS_M_ITEM_PRICE.ITEM_CATEGORY
                                              from
                                                EPS_T_PO_DETAIL
                                              left join
                                                EPS_T_PO_HEADER
                                              on 
                                                EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                                              left join
                                                EPS_T_PO_APPROVER
                                              on 
                                                EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_APPROVER.PO_NO
                                                and EPS_T_PO_HEADER.APPROVER = EPS_T_PO_APPROVER.NPK
                                              left join
                                                EPS_T_TRANSFER 
                                              on 
                                                EPS_T_TRANSFER.TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID 
                                              left join
                                                EPS_T_PR_HEADER 
                                              on 
                                                EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
                                              left join
                                                EPS_M_EMPLOYEE
                                              on
                                                EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                                              left join
                                                EPS_M_ITEM_PRICE
                                              on
                                                EPS_T_PO_DETAIL.ITEM_CD = EPS_M_ITEM_PRICE.ITEM_CD ";
                                    
                                    if($sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06'){
                                        $query .= "where 
                                                    EPS_T_PO_HEADER.PO_STATUS = '".constant('1220')."'
                                                     and EPS_T_PO_HEADER.APPROVER = '".$sUserId."'  ";
                                    }
                                    else if(($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11')  && $countApp == 0){
                                        $query .= "where 
                                                    EPS_T_PO_HEADER.PO_STATUS = '".constant('1220')."'
                                                    and EPS_T_PO_HEADER.ISSUED_BY = '".$sUserId."' ";
                                    }
                                    else{
                                        if($sRoleId == 'ROLE_03'){
                                            $query .= "where 
                                                        EPS_T_PO_HEADER.PO_STATUS = '".constant('1220')."'";
                                        }
                                    }
                                    //$query .= " order by EPS_T_PO_HEADER.UPDATE_DATE, EPS_T_PO_HEADER.PO_NO ";
									$query .= " order by 
													EPS_T_PO_HEADER.CURRENCY_CD
													,EPS_T_PO_HEADER.PO_NO
													,EPS_T_PO_HEADER.UPDATE_DATE  ";
                                    $sql = $conn->query($query);
                                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                        $poNo           = $row['PO_NO'];
                                        $refTransferId  = $row['REF_TRANSFER_ID'];
                                        $poStatus       = $row['PO_STATUS'];
                                        $supplierName   = $row['SUPPLIER_NAME'];
                                        $deliveryDate   = $row['DELIVERY_DATE'];
                                        $approver       = $row['APPROVER'];
                                        $currencyCd     = $row['CURRENCY_CD'];
                                        $itemCd         = $row['ITEM_CD'];
                                        $itemName       = $row['ITEM_NAME'];
                                        $itemCategory   = $row['ITEM_CATEGORY'];
                                        $qty            = $row['QTY'];
                                        $itemPrice      = $row['ITEM_PRICE'];
                                        $amount         = $row['AMOUNT'];
                                        $unitCd         = $row['UNIT_CD'];
                                        $totalSupplier  = $row['TOTAL_SUPPLIER'];
                                        $lastApprovalDate= $row['LAST_APPROVAL_DATE'];
                                        $issuedByName   = $row['ISSUED_BY_NAME'];
                                        
                                        $poListNo++;
                                        if($poListNo == 1)
                                        {
                                            $initialPoNo = $row['PO_NO'];
                                            $poNo = $initialPoNo;
                                        }
                                        else
                                        {
                                            if($initialPoNo == $row['PO_NO'])
                                            {
                                                $poNo = '';
                                                $supplierName = '';
                                                $deliveryDate = '';
                                                $issuedByName = '';
                                            }
                                            else
                                            {
                                                $poNo = $row['PO_NO'];
                                                $initialPoNo = $row['PO_NO'];
                                            }
                                        }
                                ?>
                                    <tr id="<?php echo $poListNo;?>">
                                        <td style="display: none;">
                                            <?php echo $refTransferId;?>
                                        </td>
                                        <td class="td-number" style="display: none;">
                                            <?php echo $poListNo;?>
                                        </td>
                                        <?php
                                        if($sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04'){
                                            if($poStatus == '1220' && $approver == $sUserId && $poNo != '')
                                            {
                                        ?>
                                        <td class="td-actions" id="getSeq<?php echo $poListNo;?>">
                                            <input type="checkbox" class="selectItem" />
                                        </td>
                                        <?php
                                            }
                                            else
                                            {
                                        ?>
                                        <td>
                                            &nbsp;
                                        </td>
                                        <?php        
                                            }
                                        }
                                        ?>
                                        <td>
                                            <a href="../db/Redirect/PO_Screen.php?criteria=poDetail&paramPoNo=<?php echo $poNo;?>" class="faq-list">
                                                <?php echo $poNo;?>
                                            </a>
                                        </td>
                                        <td id="getPoNo<?php echo $poListNo;?>" style="display: none;">
                                            <?php echo $poNo;?>
                                        </td>
                                        <td>
                                            <?php echo $supplierName;?>
                                        </td>
                                        <td class="td-date-column">
                                            <?php echo $deliveryDate;?>
                                        </td>
                                        <td> 
                                            <?php echo substr($issuedByName, 0, strpos($issuedByName, ' '));?>
                                        </td>
                                        <td class="td-date-column">
                                            <?php echo $lastApprovalDate;?>
                                        </td>
                                        <td>
                                            <?php echo $itemCd;?>
                                        </td>
                                        <td>
                                            <?php echo $itemName;?>
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
                                        <td data-item-category="<?php echo $itemCategory;?>" class="<?php echo $itemCategory!='Y'?'':'text-primary'; ?>">
                                            <?php echo $itemCategory!='Y'?'NON ':''; ?>ROUTINE
                                        </td>
                                        <td style="text-align: center">
                                            <?php 
                                            if($totalSupplier == 0){
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
                                            <?php
                                            if($totalSupplier > 0)
                                            {
                                                echo $totalSupplier;
                                            }
                                            ?>
                                        </td> 
                                    </tr>
                                <?
                                    }
                                ?>
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
<div id="dialog-confirm-approve" title="Confirm" style="display: none;"></div>
<div id="dialog-refsupplier-table" title="Supplier Reference List" style="display: none;">
    <div class="widget">
        <div class='widget-content'>
            <div class='control-group' id="dialog-control-group-refsupplier">
            </div>
        </div>
    </div>
</div>
    </body>
</html>
