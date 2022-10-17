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
            $supplierCdCriteria    = trim($_GET['supplierCdCriteria']);
            $supplierNameCriteria  = trim($_GET['supplierNameCriteria']);
            $currencyCdCriteria    = trim($_GET['currencyCdCriteria']);
            $vatCriteria           = trim($_GET['vatCriteria']);
            $outCriteria           = trim($_GET['outCriteria']);
        
            $whereSupplierMaster = array();  
            if($supplierCdCriteria)
            {
                $whereSupplierMaster[] = "EPS_M_SUPPLIER.SUPPLIER_CD = '".$supplierCdCriteria."'";
            }
            if($supplierNameCriteria)
            {
                $whereSupplierMaster[] = "EPS_M_SUPPLIER.SUPPLIER_NAME like '%".$supplierNameCriteria."%'";
            }
            if($currencyCdCriteria)
            {
                $whereSupplierMaster[] = "EPS_M_SUPPLIER.CURRENCY_CD = '".$currencyCdCriteria."'";
            }              
            if($vatCriteria)
            {
                $whereSupplierMaster[] = "EPS_M_SUPPLIER.VAT = '".$vatCriteria."'";
            }           
            if($outCriteria)
            {
                $whereSupplierMaster[] = "EPS_M_SUPPLIER.OUTSTANDING_FLAG = '".$outCriteria."'";
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
             * SELECT COUNT EPS_T_PR_HEADER
             **/
            $query_select_count_m_supplier = "select 
                                                count (*) as COUNT_SUPPLIER
                                            from         
                                                EPS_M_SUPPLIER
                                            left join
                                                EPS_M_EMPLOYEE
                                            on
                                                EPS_M_SUPPLIER.UPDATE_BY = EPS_M_EMPLOYEE.NPK ";
            if(count($whereSupplierMaster)) {
                $query_select_count_m_supplier .= "where " . implode('and ', $whereSupplierMaster);
            }
            $sql_select_count_m_supplier = $conn->query($query_select_count_m_supplier);
            $row_select_count_m_supplier = $sql_select_count_m_supplier->fetch(PDO::FETCH_ASSOC);
            $countSupplier   = $row_select_count_m_supplier['COUNT_SUPPLIER'];
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
        <script src="../js/emst/WMST006.js"></script>
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
                    <a href="WMST003.php">
                        <i class="icon-asterisk"></i><span>Item Group</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST001.php">
                        <i class="icon-shopping-cart"></i><span>Item</span> 
                    </a> 
                </li> 
                <li>
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
                <li class="active">
                    <a href="WMST006.php">
                        <i class="icon-truck"></i><span>Supplier</span> 
                    </a> 
                </li> 
                <li>
                    <a href="WMST010.php">
                        <i class="icon-key"></i><span>User ID</span> 
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
                    if($supplierCdCriteria || $supplierNameCriteria || $currencyCdCriteria || $vatCriteria || $outCriteria)
                    {
                        if($countSupplier == 0)
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
                            <form id="WMST006Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="supplierCd">Supplier Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="supplierCd" name="supplierCdCriteria" maxlength="4" value="<?php echo $supplierCdCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="supplierName">Supplier Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" id="supplierName" name="supplierNameCriteria" value="<?php echo $supplierNameCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="currencyCd">Currency Code: </label>
                                                <div class="controls">
                                                    <select id="currencyCd" class="span2" name="currencyCdCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_currency = "select 
                                                                                            CURRENCY_CD
                                                                                        from 
                                                                                            EPS_M_CURRENCY
                                                                                        order by 
                                                                                            CURRENCY_CD ";
                                                            $sql_select_m_currency = $conn->query($query_select_m_currency);
                                                            while($row_select_m_currency = $sql_select_m_currency->fetch(PDO::FETCH_ASSOC)){
                                                                $currencyCdSelect   = $row_select_m_currency['CURRENCY_CD'];
                                                        ?>
                                                        <option value="<?php echo $currencyCdSelect;?>" <?php if($currencyCdCriteria == $currencyCdSelect) echo "selected"; ?>><?php echo $currencyCdSelect;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="vat">VAT: </label>
                                                <div class="controls">
                                                    <select id="vat" class="span2" name="vatCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_supplier_byvat = "select 
                                                                                                    VAT
                                                                                                from 
                                                                                                    EPS_M_SUPPLIER
                                                                                                group by 
                                                                                                    VAT";
                                                            $sql_select_m_supplier_byvat = $conn->query($query_select_m_supplier_byvat);
                                                            while($row_select_m_supplier_byvat = $sql_select_m_supplier_byvat->fetch(PDO::FETCH_ASSOC)){
                                                                $vatSelect   = $row_select_m_supplier_byvat['VAT'];
                                                        ?>
                                                        <option value="<?php echo $vatSelect;?>" <?php if($vatCriteria == $vatSelect) echo "selected"; ?>><?php echo $vatSelect;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </td>
											<td>
                                                <label class="control-label" for="vat">Outstanding Flag: </label>
                                                <div class="controls">
                                                    <select id="out" class="span2" name="outCriteria">
                                                        <option value=""></option>
                                                        <?php 
                                                            $query_select_m_supplier_byout = "select 
                                                                                                    OUTSTANDING_FLAG
                                                                                                from 
                                                                                                    EPS_M_SUPPLIER
                                                                                                group by 
                                                                                                    OUTSTANDING_FLAG";
                                                            $sql_select_m_supplier_byout = $conn->query($query_select_m_supplier_byout);
                                                            while($row_select_m_supplier_byout = $sql_select_m_supplier_byout->fetch(PDO::FETCH_ASSOC)){
                                                                $outSelect   = $row_select_m_supplier_byout['OUTSTANDING_FLAG'];
                                                        ?>
                                                        <option value="<?php echo $outSelect;?>" <?php if($outCriteria == $outSelect) echo "selected"; ?>><?php echo $outSelect;?></option>
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
                                <?if ($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_04')
                                {
                                ?>
                                    <a href="#" class="news-item-title" id="link-register">REGISTER</a>
                                <?php    
                                }?>
                            </div> 
                        </div>
                    </div>
                    
                    <!----- Supplier Master ---->
                    <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-truck"></i>
                                <h3>Supplier Master</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="13" style="text-align: left">
                                                <a href="../db/REPORT/MASTER_SEARCH.php?criteria=Supplier&supplierCd=<?php echo $supplierCdCriteria;?>&supplierName=<?php echo $supplierNameCriteria;?>&currencyCd=<?php echo $currencyCdCriteria;?>&vat=<?php echo $vatCriteria;?>&out=<?php echo $outCriteria;?>" target="_blank" class="btn btn-small btn-linkedin-alt" id="btn-download">
                                                    Download
                                                    <i class="btn-icon-only icon-download-alt"> </i>
                                                </a>
                                            </th>
                                        </tr> 
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th colspan="2">SUPPLIER</th>
                                            <th rowspan="2">CUR</th>
                                            <th rowspan="2">VAT</th>
                                            <th rowspan="2">NPWP</th>
                                            <th rowspan="2">CONTACT</th>
                                            <th rowspan="2">PHONE</th>
                                            <th rowspan="2">ADDRESS</th>
                                            <th rowspan="2">EMAIL</th>
                                            <th rowspan="2">OUT<br>STANDING<br>FLAG</th>
                                            <th rowspan="2">ACTIVE<br>FLAG</th>
                                            <th rowspan="2">SUPPLIER<br>NUMBER</th>
                                            <th rowspan="2" style="display: none">EMAIL<br>CC</th>
                                            <th rowspan="2" style="display: none">EMAIL<br>CC UP</th>
                                        </tr>  
                                        <tr>
                                            <th>CODE</th>
                                            <th>NAME</th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($start > $countSupplier)
                                    {
                                        $lgenap     = $start - $max_per_page;
                                        $max_per_pages = $countSupplier - $lgenap;
                                        $start      = $countSupplier;
                                    }
                                    else
                                    {
                                        $max_per_pages = $max_per_page;
                                    }
                                    
                                    /**
                                     * SELECT EPS_M_ITEM
                                     **/
                                    $query_select_m_supplier = "select 
                                                                    * 
                                                                from 
                                                                    (select top  $max_per_pages  
                                                                        * 
                                                                    from      
                                                                        (select top $start 
                                                                            EPS_M_SUPPLIER.SUPPLIER_CD
                                                                            ,EPS_M_SUPPLIER.SUPPLIER_NAME
                                                                            ,EPS_M_SUPPLIER.SUPPLIER_NUMBER
                                                                            ,EPS_M_SUPPLIER.CURRENCY_CD
                                                                            ,EPS_M_SUPPLIER.VAT
                                                                            ,EPS_M_SUPPLIER.NPWP
                                                                            ,EPS_M_SUPPLIER.CONTACT
                                                                            ,EPS_M_SUPPLIER.EMAIL
                                                                            ,EPS_M_SUPPLIER.EMAIL_CC
                                                                            ,EPS_M_SUPPLIER.EMAIL_CC_UP
                                                                            ,EPS_M_SUPPLIER.PHONE
                                                                            ,EPS_M_SUPPLIER.ADDRESS
                                                                            ,EPS_M_SUPPLIER.OUTSTANDING_FLAG
                                                                            ,EPS_M_SUPPLIER.ACTIVE_FLAG
                                                                            ,EPS_M_SUPPLIER.UPDATE_BY
                                                                            ,CONVERT(VARCHAR(24), UPDATE_DATE, 103) as UPDATE_DATE
                                                                            ,CONVERT(VARCHAR(24), UPDATE_DATE, 108) as UPDATE_TIME
                                                                            ,EPS_M_EMPLOYEE.NAMA1 as UPDATE_BY_NAME
                                                                        from
                                                                            EPS_M_SUPPLIER
                                                                        left join
                                                                            EPS_M_EMPLOYEE
                                                                        on
                                                                            EPS_M_SUPPLIER.UPDATE_BY = EPS_M_EMPLOYEE.NPK ";
                                    if(count($whereSupplierMaster)) {
                                        $query_select_m_supplier .= "where " . implode(' and ', $whereSupplierMaster);
                                    }
                                    $query_select_m_supplier .= "
                                                                        order by 
                                                                            SUPPLIER_CD asc) 
                                                                        as T1 
                                                                    order by 
                                                                        SUPPLIER_CD desc) 
                                                                    as T2 
                                                                order by 
                                                                    SUPPLIER_CD ";
                                    $sql_select_m_supplier = $conn->query($query_select_m_supplier);
                                    while($row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $supplierCd     = $row_select_m_supplier['SUPPLIER_CD'];
                                        $supplierName   = $row_select_m_supplier['SUPPLIER_NAME'];
                                        $supplierNumber = $row_select_m_supplier['SUPPLIER_NUMBER'];
                                        $currencyCd     = $row_select_m_supplier['CURRENCY_CD'];
                                        $vat            = $row_select_m_supplier['VAT'];
                                        $npwp           = $row_select_m_supplier['NPWP'];
                                        $contact        = $row_select_m_supplier['CONTACT'];
                                        $email          = $row_select_m_supplier['EMAIL'];
                                        $emailCc        = $row_select_m_supplier['EMAIL_CC'];
                                        $emailCcUp      = $row_select_m_supplier['EMAIL_CC_UP'];
                                        $phone          = $row_select_m_supplier['PHONE'];
                                        $address        = $row_select_m_supplier['ADDRESS'];
                                        $outstandingFlag= $row_select_m_supplier['OUTSTANDING_FLAG'];
                                        $activeFlag     = $row_select_m_supplier['ACTIVE_FLAG'];
                                        $updateDate     = $row_select_m_supplier['UPDATE_DATE'];
                                        $updateTime     = $row_select_m_supplier['UPDATE_TIME'];
                                        $updateBy       = $row_select_m_supplier['UPDATE_BY_NAME'];
                                        
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
                                            <?if ($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_04')
                                            {
                                            ?>
                                                <a href="#"><?php echo $supplierCd;?></a>
                                            <?php
                                            }
                                            else
                                            {
                                                echo $supplierCd;
                                            }
                                            ?>
                                            </td>
                                            <td>
                                                <?php echo $supplierName;?>
                                            </td>
                                            <td>
                                                <?php echo $currencyCd;?>
                                            </td>
                                            <td>
                                                <?php echo $vat;?>
                                            </td>
                                            <td>
                                                <?php echo $npwp;?>
                                            </td>
                                            <td>
                                                <?php echo $contact;?>
                                            </td>
                                            <td>
                                                <?php echo $phone;?>
                                            </td>
                                            <td>
                                                <?php echo $address;?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $email = explode(",", $email);
                                                    $email = implode(', ', $email);
                                                    $email= preg_replace('/( )+/', '<br>', $email);
                                                    echo $email;
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $outstandingFlag;?>
                                            </td>
                                            <td>
                                                <?php echo $activeFlag;?>
                                            </td>
                                            <td>
                                                <?php echo $supplierNumber;?>
                                            </td>
                                            <td style="display: none;">
                                                <?php 
                                                    echo $emailCc;
                                                ?>
                                            </td>
                                            <td style="display: none;">
                                                <?php 
                                                    echo $emailCcUp;
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>    
                                    <tr>
                                        <th colspan="13">
                                        <?php
                                            if($countSupplier > $max_per_page)
                                            {         
                                                echo "<div id=\"pagination\" >";
                                                if ($query_select_m_supplier != "")
                                                {
                                                    $vatCriteria = urlencode($vatCriteria);
                                                    $fld = "supplierCdCriteria=$supplierCdCriteria&supplierNameCriteria=$supplierNameCriteria&currencyCdCriteria=$currencyCdCriteria&vatCriteria=$vatCriteria&outCriteria=$outCriteria&mpage";
                                                }
                                                else
                                                {
                                                    $fld = "mpage";
                                                }
                                                paging($query_select_m_supplier,$max_per_page,$num,$mpage,$fld,$countSupplier);
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
                    &copy; 2018 PT. TD AUTOMOTIVE COMPRESSOR INDONESIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>

<div id="dialog-form" title="Add Supplier" style="display: none;">
    <div class="alert" id="dialog-mandatory-msg-1" style="display: none;">
        <strong>Mandatory!</strong> Please fill all the field.
    </div>
    <div class="alert" id="dialog-duplicate-msg" style="display: none;">
        <strong>Duplicate!</strong> Supplier code already exist in master data.
    </div>
    <div class="alert" id="dialog-notexist-msg" style="display: none;">
        <strong>Existence Error!</strong> Supplier code does not exist in master data.
    </div>
    <div class="alert" id="dialog-notallowedit-msg" style="display: none;">
        <strong>Existence Error!</strong> Supplier code still using in transaction.
    </div>
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    <div class="widget ">
        <form id="WMST006Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <table class="table-non-bordered">
                        <tr>
                            <td>
                                <label class="control-label" for="supplierCd-dialog">Code: </label>
                                <div class="controls">
                                    <input type="text" class="span1" id="supplierCd-dialog" name="supplierCd-dialog"  maxlength="4" readonly />
                                </div>
                            </td>
                            <td colspan="2">
                                <label class="control-label" for="supplierName-dialog">Name: </label>
                                <div class="controls">
                                    <input type="text" class="full-width-input" id="supplierName-dialog" name="supplierName-dialog"  maxlength="250" readonly />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label class="control-label" for="contact-dialog">Contact: </label>
                                <div class="controls">
                                    <input type="text" class="span4" id="contact-dialog" name="contact-dialog" maxlength="100" />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="npwp-dialog">NPWP: </label>
                                <div class="controls">
                                    <input type="text" class="span3" id="npwp-dialog" name="npwp-dialog" maxlength="50" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="activeFlag-dialog">Active Flag: </label>
                                <div class="controls">
                                    <!--<input type="text" class="span1" id="vat-dialog" name="vat-dialog" maxlength="10" readonly />-->
                                    <select id="activeFlag-dialog" class="span1" name="activeFlag-dialog" style="width: 65px;">
                                        <option value="A">A</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="supplierNumber-dialog">Supplier Number: </label>
                                <div class="controls">
                                    <input type="text" class="span3" id="supplierNumber-dialog" name="supplierNumber-dialog" maxlength="6" readonly />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="phone-dialog">Phone: </label>
                                <div class="controls">
                                    <input type="text" class="span3" id="phone-dialog" name="phone-dialog" maxlength="50" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label" for="outstandingFlag-dialog">O/S Flag: </label>
                                <div class="controls">
                                    <!--<input type="text" class="span1" id="vat-dialog" name="vat-dialog" maxlength="10" readonly />-->
                                    <select id="outstandingFlag-dialog" class="span1" name="outstandingFlag-dialog" style="width: 65px;">
                                        <option value="N">N</option>
                                        <option value="Y">Y</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="currencyCd-dialog">Currency: </label>
                                <div class="controls">
                                    <!--<input type="text" class="span1" id="currencyCd-dialog" name="currencyCd-dialog" readonly />-->
                                        <select id="currencyCd-dialog" class="span3" name="currencyCd-dialog">
                                            <option value=""></option>
                                        <?php 
                                            $query_select_m_currency_2 = "select 
                                                                            CURRENCY_CD
                                                                        from 
                                                                            EPS_M_CURRENCY
                                                                        order by 
                                                                            CURRENCY_CD ";
                                            $sql_select_m_currency_2 = $conn->query($query_select_m_currency_2);
                                            while($row_select_m_currency_2 = $sql_select_m_currency_2->fetch(PDO::FETCH_ASSOC)){
                                                $currencyCdSelect_2   = $row_select_m_currency_2['CURRENCY_CD'];
                                        ?>
                                            <option value="<?php echo $currencyCdSelect_2;?>"><?php echo $currencyCdSelect_2;?></option>
                                        <?php         
                                            }
                                        ?>
                                        </select>
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="vat-dialog">VAT: </label>
                                <div class="controls">
                                    <!--<input type="text" class="span1" id="vat-dialog" name="vat-dialog" maxlength="10" readonly />-->
                                    <select id="vat-dialog" class="span3" name="vat-dialog">
                                        <option value=""></option>
                                        <option value="VAT">VAT</option>
                                        <option value="NON VAT">NON VAT</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="address-dialog">Address: </label>
                                <div class="controls">
                                    <input type="text" class="full-width-input" id="address-dialog" name="address-dialog" maxlength="300" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="email-dialog">Email: </label>
                                <div class="controls">
                                    <input type="text" class="full-width-input" id="email-dialog" name="email-dialog" maxlength="300" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="email-cc-dialog">Email (GM): </label>
                                <div class="controls">
                                    <input type="text" class="full-width-input" id="email-cc-dialog" name="email-cc-dialog" maxlength="200" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="email-cc-up-dialog">Email (Director): </label>
                                <div class="controls">
                                    <input type="text" class="full-width-input" id="email-cc-up-dialog" name="email-cc-up-dialog" maxlength="200" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
    </body>
</html>