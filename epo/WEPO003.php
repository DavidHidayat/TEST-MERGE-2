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
        <script src="../js/epo/WEPO003.js"></script>
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
                <li class="active">
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
                    <div class="form-actions">
                        <button class="btn btn-primary" id="btn-generate">Generate PO Number</button> 
                    </div> <!-- /form-actions -->
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please select at least one item to Generate PO Number.
                    </div>
                    <div class="alert" id="undefined-msg" style="display: none">
                        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
                    </div>
                    <div class="alert alert-success" id="success-msg" style="display: none">
                        <strong>Success!</strong> Generate PO Number finished.
                    </div>
                    <div class="widget widget-table action-table">
                        <div class="widget-header">
                            <i class="icon-tags"></i>
                            <h3>Generate PO Number</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered" id="itemPOListTable">
                                <thead>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2"><!--<input type="checkbox" id="selectAll" />--></th>
                                        <th rowspan="2" style="display: none">TRANSFER ID</th>
                                        <th rowspan="2">PR NO</th>
                                        <th colspan="2">CHARGED</th>
                                        <th colspan="2">SUPPLIER</th>
                                        <th rowspan="2">DUE DATE</th>
                                        <th colspan="2">ITEM </th>
                                        <th rowspan="2">QTY</th>
                                        <th rowspan="2">UM</th>
                                        <th rowspan="2">CUR</th>
                                        <th rowspan="2">PRICE</th>
                                        <th rowspan="2">AMOUNT</th>
                                    </tr>
                                    <tr>
                                        <th>BU</td>
                                        <th>PLANT</td>
                                        <th>CODE</td>
                                        <th>NAME</td>
                                        <th>CODE</td>
                                        <th>NAME</td>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $wherePrHeader  = array();
                                $itemNo = 0;
                                
                                $wherePrHeader[] = "(EPS_T_TRANSFER.ITEM_STATUS = '".constant('1130')."'
                                                    or EPS_T_TRANSFER.ITEM_STATUS = '".constant('1170')."'
                                                    or EPS_T_TRANSFER.ITEM_STATUS = '".constant('1260')."') ";
                                if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11'){
                                    $wherePrHeader[] = "EPS_T_TRANSFER.CREATE_BY = '".$sUserId."'";
                                }
                                $query = "select
                                            EPS_T_TRANSFER.TRANSFER_ID
                                            ,EPS_T_TRANSFER.PR_NO
                                            ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                            ,EPS_T_TRANSFER.ITEM_STATUS
                                            ,EPS_T_TRANSFER.ITEM_NAME
                                            ,EPS_T_TRANSFER.NEW_SUPPLIER_CD
                                            ,EPS_T_TRANSFER.NEW_SUPPLIER_NAME
                                            ,substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,7,2)+'/'+substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,5,2)+'/'+substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,1,4) as NEW_DELIVERY_DATE
                                            ,EPS_T_TRANSFER.NEW_ITEM_CD
                                            ,EPS_T_TRANSFER.NEW_ITEM_NAME
                                            ,EPS_T_TRANSFER.NEW_QTY
                                            ,EPS_T_TRANSFER.ACTUAL_QTY
                                            ,EPS_T_TRANSFER.NEW_UNIT_CD
                                            ,EPS_T_TRANSFER.NEW_ITEM_PRICE
                                            ,EPS_T_TRANSFER.NEW_AMOUNT
                                            ,EPS_T_TRANSFER.NEW_CURRENCY_CD
                                            ,(select count(*)
                                                from         
                                                    EPS_T_PR_ATTACHMENT
                                                where      
                                                    EPS_T_TRANSFER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                                                    and EPS_T_TRANSFER.ITEM_NAME = EPS_T_PR_ATTACHMENT.ITEM_NAME) 
                                              as ATTACHMENT_ITEM_COUNT
                                            ,EPS_M_BUNIT.PLANT_ALIAS
                                            ,EPS_T_TRANSFER.CREATE_BY
                                          from
                                            EPS_T_TRANSFER
                                          left join
                                            EPS_M_BUNIT
                                          on
                                            EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD ";
                                if(count($wherePrHeader)) {
                                    $query .= "where " . implode('and ', $wherePrHeader);
                                }
                                $query .= "order by
                                            NEW_SUPPLIER_CD
											,NEW_DELIVERY_DATE
											,PLANT_ALIAS
                                            ,NEW_ITEM_NAME ";
                                $sql = $conn->query($query);
                                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                    $transferId     = $row['TRANSFER_ID'];
                                    $prNo           = $row['PR_NO'];
                                    $newChargedBu   = $row['NEW_CHARGED_BU'];
                                    $itemStatus     = $row['ITEM_STATUS'];
                                    $itemName       = $row['ITEM_NAME'];
                                    $newSupplierCd  = $row['NEW_SUPPLIER_CD'];
                                    $newSupplierName= $row['NEW_SUPPLIER_NAME'];
                                    $newDeliveryDate= $row['NEW_DELIVERY_DATE'];
                                    $newItemCd      = $row['NEW_ITEM_CD'];
                                    $newItemName    = $row['NEW_ITEM_NAME'];
                                    $newQty         = $row['NEW_QTY'];
                                    $actualQty      = $row['ACTUAL_QTY'];
                                    $newUnitCd      = $row['NEW_UNIT_CD'];
                                    $newItemPrice   = $row['NEW_ITEM_PRICE'];
                                    $newAmount      = $row['NEW_AMOUNT'];
                                    $newCurrencyCd  = $row['NEW_CURRENCY_CD'];
                                    $attachmentItemCount= $row['ATTACHMENT_ITEM_COUNT'];
                                    $plantAlias     = $row['PLANT_ALIAS'];
                                    $createBy       = $row['CREATE_BY'];  
                                    $itemNo++;
									
                                    $split_item_price = explode('.', $newItemPrice);
                                    if($split_item_price[1] == 0)
                                    {
                                        $newItemPrice = number_format($newItemPrice);
                                    }
                                    else
                                    {
                                        $newItemPrice = number_format($newItemPrice, 2);
                                    }
                                    $newItemPrice       = str_replace(',', '',$newItemPrice);
                                    
                                    $split_new_qty = explode('.', $newQty);
                                    if($split_new_qty[1] == 0)
                                    {
                                        $newQty = number_format($newQty);
                                    }
                                    $newQty       = str_replace(',', '',$newQty);
                        
                                    $split_act_qty = explode('.', $actualQty);
                                    if($split_act_qty[1] == 0)
                                    {
                                        $actualQty = number_format($actualQty);
                                    }
                                    $actualQty       = str_replace(',', '',$actualQty);
                                    
                                    if($newQty == $actualQty)
                                    {
                                        $qty = $newQty;
                                    }
                                    else
                                    {
                                        $qty = $newQty - $actualQty;
                                    }
                                    
                                    $newAmount      = $newItemPrice * $qty;
                                ?>
                                    <tr id="<?php echo $itemNo;?>">
                                        <td class="td-number">
                                            <?php echo $itemNo;?>.
                                        </td>
                                        <td class="td-actions" id="getSeq<?php echo $itemNo;?>">
                                            <?php
                                            if($sUserId == $createBy || $sRoleId == 'ROLE_03')
                                            {
                                            ?>
                                                <input type="checkbox" class="selectItem" />
                                            <?php    
                                            }
                                            ?>
                                        </td>
                                        <td id="getTransferId<?php echo $itemNo; ?>" style="display: none">
                                            <?php echo $transferId;?>
                                        </td>
                                        <td>
                                            <?php
                                            if($sUserId == $createBy || $sRoleId == 'ROLE_03')
                                            {
                                            ?>
                                                <a href="../db/Redirect/PO_Screen.php?criteria=waitingGeneratePoNo&transferId=<?php echo $transferId;?>" class="faq-list">
                                                    <?php echo $prNo;?>
                                                </a>
                                            <?php    
                                            }
                                            else
                                            {
                                                echo $prNo;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $newChargedBu;?>
                                        </td>
                                        <td>
                                            <?php echo $plantAlias;?>
                                        </td>
                                        <td id="getSupplierCd<?php echo $itemNo;?>">
                                            <?php echo $newSupplierCd;?>
                                        </td>
                                        <td>
                                            <?php echo $newSupplierName;?>
                                        </td>
                                        <td id="getDeliveryDate<?php echo $itemNo;?>" class="td-date-column">
                                            <?php echo $newDeliveryDate;?>
                                        </td>
                                        <td>
                                            <?php echo $newItemCd;?>
                                        </td>
                                        <td>
                                            <?php
                                            if($newQty != $actualQty)
                                            {
                                            ?>
                                                <u><?php echo $newItemName;?></u>
                                            <?php    
                                            }
                                            else if($itemStatus == '1260')
                                            {
                                            ?>
                                                <font style="color:red;"><?php echo $newItemName;?></font>
                                            <?php    
                                            }
                                            else
                                            {
                                                echo $newItemName;
                                            }
                                            ?>
                                        </td>
                                        <td class="td-align-right">
                                            <?php
                                                echo $qty;
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $newUnitCd;?>
                                        </td>
                                        <td>
                                            <?php echo $newCurrencyCd;?>
                                        </td>
                                        <td class="td-align-right">
                                            <?php 
												$split_item_price = explode('.', $newItemPrice);
                                                if($split_item_price[1] == 0)
                                                {
                                                    $newItemPrice = number_format($newItemPrice);
                                                }
                                                else
                                                {
                                                    $newItemPrice = number_format($newItemPrice, 2);
                                                }
                                                echo $newItemPrice;
                                            ?>
                                        </td>
                                        <td class="td-align-right">
                                            <?php 
                                                $split_item_amount = explode('.', $newAmount);
                                                if($split_item_amount[1] == 0)
                                                {
                                                    $newAmount = number_format($newAmount);
                                                }
                                                else
                                                {
                                                    $newAmount = number_format($newAmount, 2);
                                                }
                                                echo $newAmount;
                                            ?>
                                        </td>
                                    </tr>
                                <?php    
                                }
                                ?>
                                </tbody>
                            </table>
                        </div><!-- /widget-content --> 
                    </div><!-- /widget --> 
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
                    &copy; 2014 PT.TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-confirm-session" title="Message" style="display: none;"></div>
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
    </body>
</html>
