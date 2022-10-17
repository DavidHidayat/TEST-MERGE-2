<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/CONTROLLER/PAGING.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag		= $_SESSION['sactiveFlag'];
    $sActiveFlagLogin	= $_SESSION['sactiveFlagLogin'];
    
    if($sUserId != '' && $sActiveFlag == 'A' && $sActiveFlagLogin == 'A')
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
        
        if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_11')
        {
            $itemCdCriteria             = trim($_GET['itemCdCriteria']);
            $itemNameCriteria           = trim($_GET['itemNameCriteria']);
            $itemGroupCdCriteria        = trim($_GET['itemGroupCdCriteria']);
            $effectiveDateFromCriteria  = trim($_GET['effectiveDateFromCriteria']);
            $supplierCdCriteria         = trim($_GET['supplierCdCriteria']);
            
            $whereItemPriceMaster = array();  
            $whereItemPriceMaster[] = "EPS_M_ITEM.ACTIVE_FLAG = 'A'";
            if($itemCdCriteria)
            {
                $whereItemPriceMaster[] = "EPS_M_ITEM.ITEM_CD like '%".$itemCdCriteria."%'";
            }
            if($itemNameCriteria)
            {
                $whereItemPriceMaster[] = "EPS_M_ITEM.ITEM_NAME like '%".$itemNameCriteria."%'";
            }
            if($itemGroupCdCriteria)
            {
                $whereItemPriceMaster[] = "EPS_M_ITEM.ITEM_GROUP_CD = '".$itemGroupCdCriteria."'";
            }
            if($effectiveDateFromCriteria)
            {
                $whereItemPriceMaster[] = "EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM = '".encodeDate($effectiveDateFromCriteria)."'";
            }              
            if($supplierCdCriteria)
            {
                $whereItemPriceMaster[] = "EPS_M_ITEM_PRICE.SUPPLIER_CD = '".$supplierCdCriteria."'";
            }
            
            $itemNo = 0;
            if(isset($_GET['mpage']))
            {
                $mpage = trim($_GET['mpage']);
            }
            else
            {
                $mpage = 1;
            }
            $max_per_page   = constant('20');
            $num            = 5;
                                    
            if($mpage)
            { 
                $start = ($mpage) * $max_per_page; 
            }
            else
            {
                $start  = constant('20');	
                $mpage  = 1;
            }
                                    
            if($mpage == 1)
            {
                $itemNo = 0;
            }
            else
            {
                $itemNo = ($max_per_page * ($mpage - 1));
            }
                                    
            /**
             * SELECT COUNT EPS_M_ITEM_PRICE
             **/
            $query_select_count_m_item_price = "select 
                                                    count (*) as COUNT_ITEM_PRICE
                                                from         
                                                    EPS_M_ITEM_PRICE
                                                inner join
                                                    EPS_M_ITEM
                                                on
                                                    EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD
                                                left join
                                                    EPS_M_SUPPLIER
                                                on
                                                    EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                                                left join
                                                    EPS_M_EMPLOYEE
                                                on
                                                    EPS_M_ITEM_PRICE.UPDATE_BY = EPS_M_EMPLOYEE.NPK ";
            if(count($whereItemPriceMaster)) {
                $query_select_count_m_item_price .= "where " . implode('and ', $whereItemPriceMaster);
            }
            $sql_select_count_m_item_price = $conn->query($query_select_count_m_item_price);
            $row_select_count_m_item_price = $sql_select_count_m_item_price->fetch(PDO::FETCH_ASSOC);
            $countItemPrice    = $row_select_count_m_item_price['COUNT_ITEM_PRICE'];
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
        <script src="../js/emst/WMST004.js"></script>
        <script>
            maximize();
        </script>
        <title>EPS</title>
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="../js/html5.js"></script>
        <![endif]-->
        <script>
            $(function() {
                var dateToday = new Date();
                $("#effectiveDateFrom").datepicker({
                    dateFormat: 'dd/mm/yy',
                    //defaultDate: "+1w",
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
                    <a href="WMST003.php">
                        <i class="icon-asterisk"></i><span>Item Group</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST001.php">
                        <i class="icon-shopping-cart"></i><span>Item</span> 
                    </a> 
                </li> 
                <li class="active">
                    <a href="WMST004.php">
                        <i class="icon-money"></i><span>Item Price</span> 
                    </a> 
                </li>
                <li>
                    <a href="WMST005.php">
                        <i class="icon-group"></i><span>PR Approver</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST007.php">
                        <i class="icon-sitemap"></i><span>Proc. In Charge</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST006.php">
                        <i class="icon-truck"></i><span>Supplier</span> 
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
                    if($itemCdCriteria || $itemNameCriteria || $itemGroupCdCriteria || $supplierCdCriteria || $effectiveDateFromCriteria)
                    {
                        if($countItemPrice == 0)
                        {
                    ?>
                    <div class="alert" id="mandatory-msg-2">
                        <strong>Data not found!</strong> No results match with your search.
                    </div>
                    <?php    
                        }
                    }
                    ?>
                    
                    <!----- Item Master ---->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WMST004Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="itemCd">Item Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="itemCd" name="itemCdCriteria" maxlength="15" value="<?php echo $itemCdCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="itemName">Item Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" id="itemName" name="itemNameCriteria"  maxlength="200" value="<?php echo $itemNameCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="itemGroupCd">Item Group Code: </label>
                                                <div class="controls">
                                                    <select id="itemGroupCd" class="span3" name="itemGroupCdCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_item_group = "select 
                                                                                            ITEM_GROUP_CD
                                                                                        from 
                                                                                            EPS_M_ITEM_GROUP
                                                                                        order by 
                                                                                            ITEM_GROUP_CD";
                                                            $sql_select_m_item_group = $conn->query($query_select_m_item_group);
                                                            while($row_select_m_item_group = $sql_select_m_item_group->fetch(PDO::FETCH_ASSOC)){
                                                                $itemGroupSelect   = $row_select_m_item_group['ITEM_GROUP_CD'];
                                                        ?>
                                                        <option value="<?php echo $itemGroupSelect;?>" <?php if($itemGroupCdCriteria == $itemGroupSelect) echo "selected"; ?>><?php echo $itemGroupSelect;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="control-label" for="effectiveDateFrom">Effective Date From: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="effectiveDateFrom" name="effectiveDateFromCriteria" value="<?php echo $effectiveDateFromCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="supplierCd">Supplier Name: </label>
                                                <div class="controls">
                                                    <select id="supplierCd" class="span5" name="supplierCdCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_supplier = "select 
                                                                                            SUPPLIER_CD
                                                                                            ,SUPPLIER_NAME
                                                                                        from 
                                                                                            EPS_M_SUPPLIER ";
                                                            $sql_select_m_supplier = $conn->query($query_select_m_supplier);
                                                            while($row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC)){
                                                                $supplierCdSelect   = $row_select_m_supplier['SUPPLIER_CD'];
                                                                $supplierNameSelect = $row_select_m_supplier['SUPPLIER_NAME'];
                                                        ?>
                                                        <option value="<?php echo $supplierCdSelect;?>" <?php if($supplierCdCriteria == $supplierCdSelect) echo "selected"; ?>><?php echo $supplierCdSelect."-".$supplierNameSelect;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                            <div>
                                <button class="btn btn-primary" id="btn-search">Search</button> 
                                <button class="btn" id="btn-reset">Reset</button>
                                &nbsp;
                                <?if ($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_04')
                                {
                                ?>
                                    <a href="../emst_/WMST009.php" class="news-item-title" id="link-register">REGISTER</a>
                                    &nbsp;
                                    <a href="../emst_/WMST008.php" class="news-item-title" id="link-register">UPDATE</a>
                                <?php    
                                }?>
                            </div> 
                        </div>
                    </div>
                    
                    <!----- Item Master ---->
                    <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-money"></i>
                                <h3>Item Price Master</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="13" style="text-align: left">
                                                <a href="../db/REPORT/MASTER_SEARCH.php?criteria=ItemPrice&itemCd=<?php echo $itemCdCriteria;?>&itemName=<?php echo $itemNameCriteria;?>&itemGroupCd=<?php echo urlencode($itemGroupCdCriteria);?>&effectiveDateFrom=<?php echo $effectiveDateFromCriteria;?>&supplierCd=<?php echo $supplierCdCriteria;?>" target="_blank" class="btn btn-small btn-linkedin-alt" id="btn-download">
                                                    Download
                                                    <i class="btn-icon-only icon-download-alt"> </i>
                                                </a>
                                            </th>
                                        </tr> 
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th colspan="3">ITEM</th>
                                            <th rowspan="2">U M</th>
                                            <th rowspan="2">PRICE</th>
                                            <th colspan="3">SUPPLIER</th>
                                            <th rowspan="2">EFFECTIVE<br>DATE FROM</th>
                                            <th rowspan="2">LEAD<br>TIME<br>(DAY)</th>
                                            <th colspan="2">UPDATE</th>
                                        </tr>  
                                        <tr>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                            <th>GROUP</th>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                            <th>CUR</th>
                                            <th>DATE</th>
                                            <th>BY</th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($start > $countItemPrice)
                                    {
                                        $lgenap     = $start - $max_per_page;
                                        $max_per_pages = $countItemPrice - $lgenap;
                                        $start      = $countItemPrice;
                                    }
                                    else
                                    {
                                        $max_per_pages = $max_per_page;
                                    }
                                    
                                    /**
                                     * SELECT EPS_M_ITEM
                                     **/
                                    $query_select_m_item_price = "select 
                                                                        * 
                                                                    from 
                                                                        (select top  $max_per_pages  
                                                                            * 
                                                                        from      
                                                                            (select top $start 
                                                                                EPS_M_ITEM_PRICE.ITEM_CD
                                                                                ,EPS_M_ITEM.ITEM_NAME
                                                                                ,EPS_M_ITEM.ITEM_GROUP_CD
                                                                                ,EPS_M_ITEM_PRICE.UNIT_CD
                                                                                ,EPS_M_ITEM_PRICE.ITEM_PRICE
                                                                                ,EPS_M_ITEM_PRICE.CURRENCY_CD
                                                                                ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM as EFFECTIVE_DATE
                                                                                ,substring(EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM,7,2)+'/'+substring(EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM,5,2)+'/'+substring(EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM,1,4) as EFFECTIVE_DATE_FROM
                                                                                ,EPS_M_ITEM_PRICE.LEAD_TIME
                                                                                ,EPS_M_ITEM_PRICE.SUPPLIER_CD
                                                                                ,EPS_M_SUPPLIER.SUPPLIER_NAME
                                                                                ,convert(VARCHAR(24),EPS_M_ITEM_PRICE.CREATE_DATE, 120) as CREATE_DATE
                                                                                ,EPS_M_ITEM_PRICE.CREATE_BY
                                                                                ,convert(VARCHAR(24), EPS_M_ITEM_PRICE.UPDATE_DATE, 120) as UPDATE_DATE
                                                                                ,EPS_M_ITEM_PRICE.UPDATE_BY
                                                                                ,EPS_M_EMPLOYEE.NAMA1 as UPDATE_BY_NAME
                                                                            from 
                                                                                EPS_M_ITEM_PRICE
                                                                            inner join
                                                                                EPS_M_ITEM
                                                                            on
                                                                                EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD
                                                                            left join
                                                                                EPS_M_SUPPLIER
                                                                            on
                                                                                EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                                                                            left join
                                                                                EPS_M_EMPLOYEE
                                                                            on
                                                                                EPS_M_ITEM_PRICE.UPDATE_BY = EPS_M_EMPLOYEE.NPK ";
                                    if(count($whereItemPriceMaster)) {
                                        $query_select_m_item_price .= "where " . implode(' and ', $whereItemPriceMaster);
                                    }
                                    $query_select_m_item_price .= "
                                                                            order by 
                                                                                EPS_M_ITEM_PRICE.ITEM_CD 
                                                                                ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE) 
                                                                            as T1 
                                                                        order by 
                                                                            ITEM_CD desc
                                                                            ,EFFECTIVE_DATE desc) 
                                                                        as T2 
                                                                    order by 
                                                                        ITEM_CD
                                                                        ,EFFECTIVE_DATE ";
                                    $sql_select_m_item_price= $conn->query($query_select_m_item_price);
                                    
                                    //echo $query_select_m_item_price;
                                    while($row_select_m_item_price = $sql_select_m_item_price->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $itemCd         = $row_select_m_item_price['ITEM_CD'];
                                        $itemName       = $row_select_m_item_price['ITEM_NAME'];
                                        $itemGroupCd    = $row_select_m_item_price['ITEM_GROUP_CD'];
                                        $unitCd         = $row_select_m_item_price['UNIT_CD'];
                                        $itemPrice      = $row_select_m_item_price['ITEM_PRICE'];
                                        $currencyCd     = $row_select_m_item_price['CURRENCY_CD'];
                                        $effectiveDateFrom= $row_select_m_item_price['EFFECTIVE_DATE_FROM'];
                                        $leadTime       = $row_select_m_item_price['LEAD_TIME'];
                                        $supplierCd     = $row_select_m_item_price['SUPPLIER_CD'];
                                        $supplierName   = $row_select_m_item_price['SUPPLIER_NAME'];
                                        $updateDate     = $row_select_m_item_price['UPDATE_DATE'];
                                        $updateTime     = $row_select_m_item_price['UPDATE_TIME'];
                                        $updateBy       = $row_select_m_item_price['UPDATE_BY_NAME'];
                                        
                                        if(trim($updateBy) == "")
                                        {
                                            $updateBy = "Administrator";
                                        }
                                        $itemNo++;
                                    ?>
                                        <tr>
                                            <td class="td-number">
                                                <?php echo $itemNo;?>.
                                            </td>
                                            <td>
                                                <?php echo $itemCd;?>
                                            </td>
                                            <td>
                                                <?php echo $itemName;?>
                                            </td>
                                            <td>
                                                <?php echo $itemGroupCd;?>
                                            </td>
                                            <td>
                                                <?php echo $unitCd;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo number_format($itemPrice);?>
                                            </td>
                                            <td>
                                                <?php echo $supplierCd;?>
                                            </td>
                                            <td>
                                                <?php echo $supplierName;?>
                                            </td>
                                            <td>
                                                <?php echo $currencyCd;?>
                                            </td>
                                            <td>
                                                <?php echo $effectiveDateFrom;?>
                                            </td>
                                            <td class="td-align-right">
                                                <?php echo $leadTime;?>
                                            </td>
                                            <td>
                                                <?php echo $updateDate." ".$updateTime;?>
                                            </td>
                                            <td>
                                                <?php echo substr($updateBy, 0, strpos($updateBy, ' '));?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>    
                                    <tr>
                                        <th colspan="13">
                                        <?php
                                            if($countItemPrice > $max_per_page)
                                            {         
                                                echo "<div id=\"pagination\" >";
                                                if ($query_select_m_item_price != "")
                                                {
                                                    $itemGroupCdCriteria = urlencode($itemGroupCdCriteria);
                                                    $fld = "itemCdCriteria=$itemCdCriteria&itemNameCriteria=$itemNameCriteria&itemGroupCdCriteria=$itemGroupCdCriteria&effectiveDateFromCriteria=$effectiveDateFromCriteria&supplierCdCriteria=$supplierCdCriteria&mpage";
                                                }
                                                else
                                                {
                                                    $fld = "mpage";
                                                }
                                                paging($query_select_m_item_price,$max_per_page,$num,$mpage,$fld,$countItemPrice);
                                                echo "</div>";
                                            }
                                        ?>
                                        </th>
                                    </tr>
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
                    &copy; 2018 PT.TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
    </body>
</html>