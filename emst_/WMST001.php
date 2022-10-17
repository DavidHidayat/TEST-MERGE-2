<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/CONTROLLER/PAGING.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag	= $_SESSION['sactiveFlag'];
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
            $itemCdCriteria      = stripslashes(strtoupper(trim($_GET['itemCdCriteria'])));
            $itemCdCriteria      = str_replace("'", "''", $itemCdCriteria);
            $itemNameCriteria    = stripslashes(strtoupper(trim($_GET['itemNameCriteria'])));
            $itemNameCriteria    = str_replace("'", "''", $itemNameCriteria);
            $itemGroupCdCriteria = stripslashes(strtoupper(trim($_GET['itemGroupCdCriteria'])));
            $itemGroupCdCriteria = str_replace("'", "''", $itemGroupCdCriteria);
            $activeFlagCriteria  = stripslashes(strtoupper(trim($_GET['activeFlagCriteria'])));
            $activeFlagCriteria  = str_replace("'", "''", $activeFlagCriteria);
            $objectAccountCdCriteria    = stripslashes(strtoupper(trim($_GET['objectAccountCdCriteria'])));
            $objectAccountCdCriteria    = str_replace("'", "''", $objectAccountCdCriteria);
            
            
            $whereItemMaster = array();  
            //$whereItemMaster[] = "EPS_M_ITEM.ACTIVE_FLAG = 'A'";
            if($itemCdCriteria)
            {
                $whereItemMaster[] = "EPS_M_ITEM.ITEM_CD like '%".$itemCdCriteria."%'";
            }
            if($objectAccountCdCriteria)
            {
                $whereItemMaster[] = "EPS_M_ITEM.OBJECT_ACCOUNT_CD = '".$objectAccountCdCriteria."'";
            }
            if($itemNameCriteria)
            {
                $whereItemMaster[] = "EPS_M_ITEM.ITEM_NAME like '%".$itemNameCriteria."%'";
            }
            if($itemGroupCdCriteria)
            {
                $whereItemMaster[] = "EPS_M_ITEM.ITEM_GROUP_CD = '".$itemGroupCdCriteria."'";
            }
            if($activeFlagCriteria)
            {
                $whereItemMaster[] = "EPS_M_ITEM.ACTIVE_FLAG = '".$activeFlagCriteria."'";
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
            $query_select_count_m_item = "select 
                                            count (*) as COUNT_ITEM
                                          from         
                                            EPS_M_ITEM
                                          left join
                                            EPS_M_EMPLOYEE
                                          on
                                            EPS_M_ITEM.UPDATE_BY = EPS_M_EMPLOYEE.NPK ";
            if(count($whereItemMaster)) {
                $query_select_count_m_item .= "where " . implode('and ', $whereItemMaster);
            }
            $sql_select_count_m_item = $conn->query($query_select_count_m_item);
            $row_select_count_m_item = $sql_select_count_m_item->fetch(PDO::FETCH_ASSOC);
            $countItem    = $row_select_count_m_item['COUNT_ITEM'];
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
        <script src="../js/emst/WMST001.js"></script>
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
                <li class="active">
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
                <li>
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
                    if($itemCdCriteria || $itemNameCriteria || $itemGroupCdCriteria || $activeFlagCriteria)
                    {
                        if($countItem == 0)
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
                            <form id="WMST001Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="itemCd">Item Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="itemCd" name="itemCdCriteria" maxlength="15" value="<?php echo htmlspecialchars($itemCdCriteria);?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="itemName">Item Name </label>
                                                <div class="controls">
                                                    <input type="text" class="span5" id="itemName" name="itemNameCriteria" maxlength="200" value="<?php echo htmlspecialchars($itemNameCriteria);?>" />
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
                                            <td>
                                                <label class="control-label" for="activeFlag">Active Flag: </label>
                                                <div class="controls">
                                                    <select id="activeFlag" class="span2" name="activeFlagCriteria">
                                                        <option value=""></option>
                                                        <option value="A" <?php if($activeFlagCriteria == "A") echo "selected"; ?>>A</option>
                                                        <option value="D" <?php if($activeFlagCriteria == "D") echo "selected"; ?>>D</option>
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
                                <?if ($sRoleId == 'ROLE_03')
                                {
                                ?>
                                    <a href="#" class="news-item-title" id="link-register">REGISTER</a>
                                <?php    
                                }?>
                            </div> 
                        </div>
                    </div>
                    
                    <!----- Item Master ---->
                    <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-shopping-cart"></i>
                                <h3>Item Master</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="8" style="text-align: left">
                                                <a href="../db/REPORT/MASTER_SEARCH.php?criteria=Item&itemCd=<?php echo $itemCdCriteria;?>&itemName=<?php echo $itemNameCriteria;?>&itemGroupCd=<?php echo urlencode($itemGroupCdCriteria);?>&activeFlag=<?php echo $activeFlagCriteria;?>" target="_blank" class="btn btn-small btn-linkedin-alt" id="btn-download">
                                                    Download
                                                    <i class="btn-icon-only icon-download-alt"> </i>
                                                </a>
                                            </th>
                                        </tr> 
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th rowspan="2">ITEM CODE</th>
                                            <th rowspan="2">ITEM NAME</th>
                                            <th rowspan="2">ITEM GROUP CODE</th>
                                            <th rowspan="2">OBJECT ACCOUNT CODE</th>
                                            <th rowspan="2">CATG.</th>
                                            <th rowspan="2">ACTIVE FLAG</th>
                                            <th colspan="2">UPDATE</th>
                                        </tr>  
                                        <tr>
                                            <th>DATE</th>
                                            <th>BY</th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($start > $countItem)
                                    {
                                        $lgenap     = $start - $max_per_page;
                                        $max_per_pages = $countItem - $lgenap;
                                        $start      = $countItem;
                                    }
                                    else
                                    {
                                        $max_per_pages = $max_per_page;
                                    }
                                    
                                    /**
                                     * SELECT EPS_M_ITEM
                                     **/
                                    $query_select_m_item= "select 
                                                * 
                                              from 
                                                (select top  $max_per_pages  
                                                    * 
                                                from      
                                                    (select top $start 
                                                         EPS_M_ITEM.ITEM_CD
                                                         ,EPS_M_ITEM.ITEM_NAME
                                                         ,EPS_M_ITEM.ITEM_GROUP_CD
                                                         ,EPS_M_ITEM.OBJECT_ACCOUNT_CD
                                                         ,EPS_M_ITEM.ACTIVE_FLAG
                                                         ,EPS_M_ITEM.UPDATE_BY
                                                         ,CONVERT(VARCHAR(24), EPS_M_ITEM.UPDATE_DATE, 103) as UPDATE_DATE
                                                         ,CONVERT(VARCHAR(24), EPS_M_ITEM.UPDATE_DATE, 108) as UPDATE_TIME
                                                         ,EPS_M_EMPLOYEE.NAMA1 as UPDATE_BY_NAME
                                                         ,EPS_M_ITEM_PRICE.ITEM_CATEGORY
                                                    from
                                                        EPS_M_ITEM
                                                        left join
                                                        EPS_M_EMPLOYEE
                                                    on
                                                        EPS_M_ITEM.UPDATE_BY = EPS_M_EMPLOYEE.NPK 
                                                    left join
                                                        EPS_M_ITEM_PRICE
                                                    on
                                                    EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD 
                                                    where EPS_M_ITEM.ITEM_CD <> '' ";
                                    if(count($whereItemMaster)) {
                                        $query_select_m_item .=  " and ".implode(' and ', $whereItemMaster);
                                    }
                                    $query_select_m_item .= "
                                                    order by 
                                                    EPS_M_ITEM.ITEM_CD asc) 
                                                    as T1 
                                                order by 
                                                    ITEM_CD desc) 
                                                as T2 
                                              order by 
                                                ITEM_CD ";
                                    $sql_select_m_item = $conn->query($query_select_m_item);
                                    while($row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $itemCd          = $row_select_m_item['ITEM_CD'];
                                        $itemName        = $row_select_m_item['ITEM_NAME'];
                                        $itemGroupCd     = $row_select_m_item['ITEM_GROUP_CD'];
                                        $objectAccountCd = $row_select_m_item['OBJECT_ACCOUNT_CD'];
                                        $itemCategory    = $row_select_m_item['ITEM_CATEGORY'];
                                        $activeFlag      = $row_select_m_item['ACTIVE_FLAG'];
                                        $updateDate      = $row_select_m_item['UPDATE_DATE'];
                                        $updateTime      = $row_select_m_item['UPDATE_TIME'];
                                        $updateBy        = $row_select_m_item['UPDATE_BY_NAME'];
                                        
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
                                            <?if ($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_07' || $sRoleId == 'R0LE_04')
                                            {
                                            ?>
                                                <a href="#"><?php echo $itemCd;?></a>
                                            <?php
                                            }
                                            else
                                            {
                                             ?>
                                                <a href="#"><?php echo $itemCd;?></a>
                                            <?php
                                            }
                                            ?>
                                            </td>
                                            <td>
                                                <?php echo $itemName;?>
                                            </td>
                                            <td>
                                                <?php echo $itemGroupCd;?>
                                            </td>
                                            <td>
                                                <?php echo $objectAccountCd;?>
                                            </td>
                                            <td data-item-category="<?php echo $itemCategory;?>" class="<?php echo $itemCategory!='Y'?'':'text-primary'; ?>">
                                                    <?php echo $itemCategory!='Y'?'NON ':''; ?>ROUTINE
                                                </td>
                                            <td>
                                                <?php echo $activeFlag;?>
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
                                        <th colspan="8">
                                        <?php
                                            if($countItem > $max_per_page)
                                            {         
                                                echo "<div id=\"pagination\" >";
                                                if ($query_select_m_item != "")
                                                {
                                                    $itemGroupCdCriteria = urlencode($itemGroupCdCriteria);
                                                    $fld = "itemCdCriteria=$itemCdCriteria&objectAccountCdCriteria=$objectAccountCdCriteria&itemNameCriteria=$itemNameCriteria&itemGroupCdCriteria=$itemGroupCdCriteria&activeFlagCriteria=$activeFlagCriteria&mpage";
                                                }
                                                else
                                                {
                                                    $fld = "mpage";
                                                }
                                                paging($query_select_m_item,$max_per_page,$num,$mpage,$fld,$countItem);
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
                    &copy; 2018 PT. TD Automotive Compressor Indonesia. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->
<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
<div id="dialog-form" title="Add Item" style="display: none;">
    <div class="alert" id="dialog-mandatory-msg-1" style="display: none;">
        <strong>Mandatory!</strong> Please fill all the field.
    </div>
    <div class="alert" id="dialog-duplicate-msg" style="display: none;">
        <strong>Duplicate!</strong> Item code already exist in master data.
    </div>
    <div class="alert" id="dialog-notexist-msg" style="display: none;">
        <strong>Existence Error!</strong> Item code does not exist in master data.
    </div>
    <div class="alert" id="dialog-notallowedit-msg" style="display: none;">
        <strong>Existence Error!</strong> Item code still using in transaction.
    </div>
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    <div class="widget ">
        <form id="WMST001Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <table class="table-non-bordered">
                        <tr>
                            <td>
                                <label class="control-label" for="itemCd-dialog">Item Code: </label>
                                <div class="controls">
                                    <input type="text" class="span2" id="itemCd-dialog" name="itemCdCriteria" maxlength="5" value="<?php echo $itemCdCriteria;?>" />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="itemGroupCd-dialog">Item Group Code: </label>
                                <div class="controls">
                                    <select id="itemGroupCd-dialog" class="span3" name="itemGroupCdDialogCriteria">
                                        <option value=""></option>
                                        <?php 
                                        $query_select_m_item_group = "select 
                                                                        ITEM_GROUP_CD, ITEM_GROUP_NAME
                                                                      from 
                                                                        EPS_M_ITEM_GROUP
                                                                      order by 
                                                                        ITEM_GROUP_CD";
                                        $sql_select_m_item_group = $conn->query($query_select_m_item_group);
                                        while($row_select_m_item_group = $sql_select_m_item_group->fetch(PDO::FETCH_ASSOC)){
                                            $itemGroupSelect   = $row_select_m_item_group['ITEM_GROUP_CD'];
                                            $itemGroupName   = $row_select_m_item_group['ITEM_GROUP_NAME'];
                                        ?>
                                        <option value="<?php echo $itemGroupSelect;?>" <?php if($itemGroupCdCriteria == $itemGroupSelect) echo "selected"; ?>><?php echo $itemGroupSelect ." , ". $itemGroupName;?></option>
                                        <?php         
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="activeFlag-dialog">Active Flag : </label>
                                <div class="controls">
                                    <select id="activeFlag-dialog" class="span2" name="activeFlag-dialog">
                                        <option value="A">A</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                            <label class="control-label" for="objectAccountCd-dialog">Object Account: </label>
                                <div class="controls">
                                    <select id="objectAccountCd-dialog" class="span3" name="objectAccountCdCriteria">
                                        <option value=""></option>
                                        <?php 
                                        $query_select_m_object_account = "select 
                                                                        OBJECT_ACCOUNT_CD, OBJECT_ACCOUNT_NAME
                                                                      from 
                                                                        EPS_M_OBJECT_ACCOUNT
                                                                      order by 
                                                                        OBJECT_ACCOUNT_CD";
                                        $sql_select_m_object_account = $conn->query($query_select_m_object_account);
                                        while($row_select_m_object_account = $sql_select_m_object_account->fetch(PDO::FETCH_ASSOC)){
                                            $objectAccountCd   = $row_select_m_object_account['OBJECT_ACCOUNT_CD'];
                                            $objectAccountName   = $row_select_m_object_account['OBJECT_ACCOUNT_NAME'];
                                        ?>
                                        <option value="<?php echo $objectAccountCd;?>" <?php if("924900" == $objectAccountCd) echo "selected"; ?>><?php echo $objectAccountCd ." , ". $objectAccountName ;?></option>
                                        <?php         
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label class="control-label" for="itemName-dialog">Item Name: </label>
                                <div class="controls">
                                    <input type="text" class="full-width-input" id="itemName-dialog" name="itemNameCriteria" maxlength="200" value="<?php echo $itemNameCriteria;?>" />
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