<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/CONTROLLER/PAGING.php";
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
            $npkCriteria        = stripslashes(strtoupper(trim($_GET['npkCriteria'])));
            $npkCriteria        = str_replace("'", "''", $npkCriteria);
            $empNameCriteria    = stripslashes(strtoupper(trim($_GET['empNameCriteria'])));
            $empNameCriteria    = str_replace("'", "''", $empNameCriteria);
            $buCdCriteria       = stripslashes(strtoupper(trim($_GET['buCdCriteria'])));
            $buCdCriteria       = str_replace("'", "''", $buCdCriteria);
            $userIdCriteria     = stripslashes(strtoupper(trim($_GET['userIdCriteria'])));
            $userIdCriteria     = str_replace("'", "''", $userIdCriteria);
            $buUserCriteria     = stripslashes(strtoupper(trim($_GET['buUserCriteria'])));
            $buUserCriteria     = str_replace("'", "''", $buUserCriteria);
            $mailNameCriteria   = stripslashes(strtoupper(trim($_GET['mailNameCriteria'])));
            $mailNameCriteria   = str_replace("'", "''", $mailNameCriteria);
            $emailCriteria      = stripslashes(strtoupper(trim($_GET['emailCriteria'])));
            $emailCriteria      = str_replace("'", "''", $emailCriteria);
            
            $whereUserMaster = array();   
            $whereUserMaster[] = "EPS_M_EMPLOYEE.AKTIF = 'A'";
            $whereUserMaster[] = "EPS_M_USER.ACTIVE_FLAG = 'A'";
            $whereUserMaster[] = "EPS_M_DSCID.IBUN like 'T%'";
            
            if($npkCriteria)
            {
                $whereUserMaster[] = "ltrim(EPS_M_USER.NPK) = ltrim('".$npkCriteria."')";
            }
            if($empNameCriteria)
            {
                $whereUserMaster[] = "EPS_M_EMPLOYEE.NAMA1 like '%".$empNameCriteria."%'";
            }
            if($buCdCriteria)
            {
                $whereUserMaster[] = "EPS_M_EMPLOYEE.LKDP = '".$buCdCriteria."'";
            }
            if($userIdCriteria)
            {
                $whereUserMaster[] = "EPS_M_USER.USERID = '".$userIdCriteria."'";
            }
            if($buUserCriteria)
            {
                $whereUserMaster[] = "EPS_M_USER.BU_CD = '".$buUserCriteria."'";
            }
            if($mailNameCriteria)
            {
                $whereUserMaster[] = "EPS_M_DSCID.INMAIL like '%".$mailNameCriteria."%'";
            }
            if($emailCriteria)
            {
                $whereUserMaster[] = "EPS_M_DSCID.INETML like '%".$emailCriteria."%'";
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
            $query_select_count_m_user = "select 
                                            count (*) as COUNT_DSCID
                                          from         
                                            EPS_M_EMPLOYEE
                                          inner join
                                            EPS_M_DSCID
                                          on
                                            EPS_M_DSCID.INOPOK = EPS_M_EMPLOYEE.NPK
                                          inner join
                                            EPS_M_USER
                                          on
                                            EPS_M_EMPLOYEE.NPK = EPS_M_USER.NPK
                                          inner join
                                            EPS_M_COMPANY
                                          on
                                            EPS_M_EMPLOYEE.PERSH = EPS_M_COMPANY.COMPANY_CD ";
            if(count($whereUserMaster)) {
                $query_select_count_m_user .= "where " . implode('and ', $whereUserMaster);
            }
            $sql_select_count_m_user = $conn->query($query_select_count_m_user);
            $row_select_count_m_user = $sql_select_count_m_user->fetch(PDO::FETCH_ASSOC);
            $countIUser = $row_select_count_m_user['COUNT_DSCID'];
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
        <script src="../js/emst/WMST010.js"></script>
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
                <li>
                    <a href="WMST006.php">
                        <i class="icon-truck"></i><span>Supplier</span> 
                    </a> 
                </li> 
                <li class="active">
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
                    if($npkCriteria || $empNameCriteria || $buCdCriteria || $userIdCriteria || $buUserCriteria)
                    {
                        if($countIUser == 0)
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
                            <form id="WMST010Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="npk">NPK: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="npk" name="npkCriteria" maxlength="7" value="<?php echo $npkCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="employeeName">Employee Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" id="empName" name="empNameCriteria" maxlength="100" value="<?php echo htmlspecialchars($empNameCriteria);?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="buCd">BU Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span1" id="buCd" name="buCdCriteria" maxlength="5" value="<?php echo htmlspecialchars($buCdCriteria);?>" />
                                                </div>
                                            </td>      
                                            <td>
                                                <label class="control-label" for="userId">User ID: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="userId" name="userIdCriteria" maxlength="8" value="<?php echo htmlspecialchars($userIdCriteria);?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="buUser">BU User: </label>
                                                <div class="controls">
                                                    <input type="text" class="span1" id="buUser" name="buUserCriteria" maxlength="5" value="<?php echo htmlspecialchars($buUserCriteria);?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="mailName">Mail Name: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="mailName" name="mailNameCriteria" maxlength="100" value="<?php echo htmlspecialchars($mailNameCriteria);?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="email">Email: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="email" name="emailCriteria" maxlength="100" value="<?php echo htmlspecialchars($emailCriteria);?>" />
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
                    
                    <!----- Item Master ---->
                    <div class="widget widget-table action-table">
                            <div class="widget-header"> 
                                <i class="icon-key"></i>
                                <h3>User ID Master</h3>
                            </div>
                            <div class="widget-content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="10" style="text-align: left">
                                                <a href="../db/REPORT/MASTER_SEARCH.php?criteria=UserID&npk=<?php echo $npkCriteria;?>&empName=<?php echo $empNameCriteria;?>&buCd=<?php echo $buCdCriteria;?>&userId=<?php echo $userIdCriteria;?>&buUser=<?php echo $buUserCriteria;?>&mailName=<?php echo $mailNameCriteria;?>&email=<?php echo $emailCriteria;?>" target="_blank" class="btn btn-small btn-linkedin-alt" id="btn-download">
                                                    Download
                                                    <i class="btn-icon-only icon-download-alt"> </i>
                                                </a>
                                            </th>
                                        </tr> 
                                        <tr>
                                            <th rowspan="2">NO</th>
                                            <th colspan="4">EMPLOYEE</th>
                                            <th colspan="2">USER ID</th>
                                            <th colspan="2">MAIL</th>
                                            <th rowspan="2">LAST UPDATE</th>
                                        </tr> 
                                        <tr>
                                            <th>NPK</th>
                                            <th>NAME</th>
                                            <th>BU CODE</th>
                                            <th>COMPANY</th>
                                            <th>USERID</th>
                                            <th>BU USER</th>
                                            <th>MAIL NAME</th>
                                            <th>EMAIL</th>
                                        </tr>   
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($start > $countIUser)
                                    {
                                        $lgenap     = $start - $max_per_page;
                                        $max_per_pages = $countIUser - $lgenap;
                                        $start      = $countIUser;
                                    }
                                    else
                                    {
                                        $max_per_pages = $max_per_page;
                                    }
                                    
                                    /**
                                     * SELECT EPS_M_ITEM
                                     **/
                                    $query_select_m_dscid = "select 
                                                                    * 
                                                                  from 
                                                                    (select top  $max_per_pages  
                                                                        * 
                                                                    from      
                                                                        (select top $start 
                                                                            EPS_M_EMPLOYEE.NPK
                                                                            ,EPS_M_EMPLOYEE.NAMA1
                                                                            ,EPS_M_EMPLOYEE.LKDP
                                                                            ,EPS_M_USER.USERID
                                                                            ,EPS_M_USER.BU_CD
                                                                            ,EPS_M_DSCID.INMAIL
                                                                            ,EPS_M_DSCID.INETML
                                                                            ,EPS_M_COMPANY.COMPANY_NAME_ALIAS
                                                                            ,CONVERT(VARCHAR(24), EPS_M_USER.LAST_UPDATE, 120) as LAST_UPDATE
                                                                        from
                                                                            EPS_M_EMPLOYEE
                                                                        inner join
                                                                            EPS_M_DSCID
                                                                        on
                                                                            EPS_M_DSCID.INOPOK = EPS_M_EMPLOYEE.NPK
                                                                        inner join
                                                                            EPS_M_USER
                                                                        on
                                                                            EPS_M_EMPLOYEE.NPK = EPS_M_USER.NPK
                                                                        inner join
                                                                            EPS_M_COMPANY
                                                                        on
                                                                            EPS_M_EMPLOYEE.PERSH = EPS_M_COMPANY.COMPANY_CD ";
                                    if(count($whereUserMaster)) {
                                        $query_select_m_dscid .= "where " . implode(' and ', $whereUserMaster);
                                    }
                                    $query_select_m_dscid .= "
                                                                        order by 
                                                                            EPS_M_USER.USERID asc) 
                                                                        as T1 
                                                                    order by 
                                                                        USERID desc) 
                                                                    as T2 
                                                                order by 
                                                                    USERID asc ";
                                    $sql_select_m_dscid = $conn->query($query_select_m_dscid);
                                    while($row_select_m_dscid = $sql_select_m_dscid->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $npk                = $row_select_m_dscid['NPK'];
                                        $nama               = $row_select_m_dscid['NAMA1'];
                                        $lkdp               = $row_select_m_dscid['LKDP'];
                                        $companyNameAlias   = $row_select_m_dscid['COMPANY_NAME_ALIAS'];
                                        $userId             = $row_select_m_dscid['USERID'];
                                        $buCd               = $row_select_m_dscid['BU_CD'];
                                        $inmail             = $row_select_m_dscid['INMAIL'];
                                        $inetml             = $row_select_m_dscid['INETML'];
                                        $lastUpdate         = $row_select_m_dscid['LAST_UPDATE'];
                                        
                                        $itemNo++;
                                    ?>
                                        <tr>
                                            <td class="td-number">
                                                <?php echo $itemNo;?>.
                                            </td>
                                            <td>
                                            <?php
                                            
                                                echo $npk;
                                            
                                            ?>
                                            </td>
                                            <td>
                                                <?php echo $nama;?>
                                            </td>
                                            <td>
                                                <?php echo $lkdp;?>
                                            </td>
                                            <td>
                                                <?php echo $companyNameAlias;?>
                                            </td>
                                            <td>
                                                <?php echo $userId;?>
                                            </td>
                                            <td>
                                                <?php echo $buCd;?>
                                            </td>
                                            <td>
                                                <?php echo $inmail;?>
                                            </td>
                                            <td>
                                                <?php echo $inetml;?>
                                            </td>
                                            <td>
                                                <?php echo $lastUpdate;?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>    
                                    <tr>
                                        <th colspan="10">
                                        <?php
                                            if($countIUser > $max_per_page)
                                            {         
                                                echo "<div id=\"pagination\" >";
                                                if ($query_select_m_dscid != "")
                                                {
                                                    $fld = "npkCriteria=$npkCriteria&empNameCriteria=$empNameCriteria&buCdCriteria=$buCdCriteria&userIdCriteria=$userIdCriteria&buUserCriteria=$buUserCriteria&mailNameCriteria=$mailNameCriteria&emailCriteria=$emailCriteria&mpage";
                                                }
                                                else
                                                {
                                                    $fld = "mpage";
                                                }
                                                paging($query_select_m_dscid,$max_per_page,$num,$mpage,$fld,$countIUser);
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
<div id="dialog-form" title="Add Item Group" style="display: none;">
    <div class="alert" id="dialog-mandatory-msg-1" style="display: none;">
        <strong>Mandatory!</strong> Please fill all the field.
    </div>
    <div class="alert" id="dialog-duplicate-msg" style="display: none;">
        <strong>Duplicate!</strong> Item group code already exist in master data.
    </div>
    <div class="alert" id="dialog-notexist-msg" style="display: none;">
        <strong>Existence Error!</strong> Item group code does not exist in master data.
    </div>
    <div class="alert" id="dialog-undefined-msg" style="display: none">
        <strong>Undefined Error!</strong> System Error occurs. Please report to system administrator.
    </div>
    <div class="widget ">
        <form id="WMST003Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <table class="table-non-bordered">
                        <tr>
                            <td>
                                <label class="control-label" for="itemGroupCd">Item Group Code: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="itemGroupCd-dialog" class="span2"  maxlength="5" readonly />
                                </div>
                            </td>
                            <td>
                                <label class="control-label" for="itemGroupName">Item Group Name: </label>
                                <div class="dialog-controls">
                                    <input type="text" id="itemGroupName-dialog" class="span4" maxlength="100"  />
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