<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/CONTROLLER/PAGING.php";
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
            $prNoCriteria           = $_GET['prNoCriteria'];
            $requesterNameCriteria  = $_GET['requesterNameCriteria'];
            $prChargedCriteria      = $_GET['prChargedBuCriteria'];
            $acceptedDateCriteria   = $_GET['acceptedDateCriteria']; 
            
            if($prNoCriteria || $requesterNameCriteria || $prChargedCriteria || $acceptedDateCriteria)
            {
                $prListNo = 0;
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
                    $prListNo = 0;
                }
                else
                {
                    $prListNo = ($max_per_page * ($mpage - 1));
                }
                                    
                $wherePrHeader  = array();
                if($prNoCriteria)
                {
                    $wherePrHeader[] = "EPS_T_PR_HEADER.PR_NO = '".$prNoCriteria."'";
                }
                if($requesterNameCriteria)
                {
                    $wherePrHeader[] = "EPS_M_EMPLOYEE.NAMA1 like '".$requesterNameCriteria."%'";
                }
                if($prChargedCriteria)
                {
                    $wherePrHeader[] = "EPS_T_PR_HEADER.CHARGED_BU_CD = '".$prChargedCriteria."'";
                }
                if($acceptedDateCriteria)
                {
                    $wherePrHeader[] = "convert(varchar(24),EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) = '".$acceptedDateCriteria."'";
                }
                
                /**
                 * SELECT COUNT EPS_T_PR_HEADER
                 **/
                $query_count_t_pr_header = "select 
                                                count (*) as COUNT_PR
                                            from
                                                EPS_T_PR_HEADER
                                            left join
                                                EPS_M_EMPLOYEE
                                            on
                                                EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                            left join
                                                EPS_M_EMPLOYEE EPS_M_EMPLOYEE_1
                                            on
                                                EPS_T_PR_HEADER.PROC_IN_CHARGE = EPS_M_EMPLOYEE_1.NPK
                                            left join
                                                EPS_M_APP_STATUS
                                            on
                                                EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                            where
                                                (EPS_T_PR_HEADER.PR_STATUS = '1040' or EPS_T_PR_HEADER.PR_STATUS = '1080') ";
                if(count($wherePrHeader)) {
                    $query_count_t_pr_header .= " and " . implode('and ', $wherePrHeader);
                }
                $sql_count_t_pr_header = $conn->query($query_count_t_pr_header);
                $row_count_t_pr_header = $sql_count_t_pr_header->fetch(PDO::FETCH_ASSOC);
                $countPr    = $row_count_t_pr_header['COUNT_PR'];
                if($start > $countPr)
                {
                    $lgenap     = $start - $max_per_page;
                    $max_per_pages = $countPr - $lgenap;
                    $start      = $countPr;
                }
                else
                {
                    $max_per_pages = $max_per_page;
                }
                
                /**
                 * SELECT EPS_T_PR_HEADER
                 **/
                $query_select_t_pr_header = "select 
                                                * 
                                             from 
                                                (select top  $max_per_pages  
                                                    * 
                                                from      
                                                    (select top $start 
                                                        EPS_T_PR_HEADER.PR_NO
                                                        ,EPS_T_PR_HEADER.BU_CD
                                                        ,EPS_T_PR_HEADER.CHARGED_BU_CD
                                                        ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                        ,EPS_M_APP_STATUS.APP_STATUS_NAME
                                                        ,EPS_M_EMPLOYEE_1.NAMA1 as PROC_IN_CHARGE_NAME
                                                        ,EPS_T_PR_HEADER.PROC_ACCEPT_DATE  
                                                        ,(select count(*)
                                                            from EPS_T_PR_ATTACHMENT
                                                            where EPS_T_PR_ATTACHMENT.PR_NO = EPS_T_PR_HEADER.PR_NO) as COUNT_ATTACHMENT
                                                    from
                                                        EPS_T_PR_HEADER
                                                    left join
                                                        EPS_M_EMPLOYEE
                                                    on
                                                        EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                                    left join
                                                        EPS_M_EMPLOYEE EPS_M_EMPLOYEE_1
                                                    on
                                                        EPS_T_PR_HEADER.PROC_IN_CHARGE = EPS_M_EMPLOYEE_1.NPK
                                                    left join
                                                        EPS_M_APP_STATUS
                                                    on
                                                        EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                                    where
                                                        (EPS_T_PR_HEADER.PR_STATUS = '1040' or EPS_T_PR_HEADER.PR_STATUS = '1080') ";       
                $query_select_t_pr_header .= " and " . implode('and ', $wherePrHeader);
                $query_select_t_pr_header .= "      order by 
                                                        PROC_ACCEPT_DATE asc)
                                                    as T1
                                                order by
                                                    PROC_ACCEPT_DATE desc)
                                               as T2
                                             order by
                                               PROC_ACCEPT_DATE ";
                $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
                $row2 = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC);
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
        <script src="../js/epo/WEPO012.js"></script>
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
                $( "#acceptedDate" ).datepicker({
                    dateFormat: 'dd/mm/yy',
                    maxDate: new Date
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
                    <a href="WEPO001_.php">
                        <i class="icon-list-alt"></i><span>PR Waiting</span> 
                    </a> 
                </li>
                <li class="active">
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
                    <!---------------------------------- Message --------------------------------->
                    <div class="alert" id="mandatory-msg-1" style="display: none">
                        <strong>Mandatory!</strong> Please fill the search criteria.
                    </div>
                    <?php
                    if($prNoCriteria || $requesterNameCriteria|| $prChargedCriteria || $acceptedDateCriteria)
                    {
                        if(!$row2)
                        {
                    ?>
                    <div class="alert" id="mandatory-msg-2">
                        <strong>Data not found!</strong> No results match with your search.
                    </div>
                    <?php    
                        }
                    }
                    ?>
                    <!---------------------------------- Search -------------------------------->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WEPO012Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="prNo">PR No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="prNo" name="prNoCriteria" maxlength="10" value="<?php echo $prNoCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="requesterName">PR Requester: </label>
                                                <div class="controls">
                                                    <input type="text" class="span3" id="requesterName" name="requesterNameCriteria" value="<?php echo $requesterNameCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="acceptedDate">PR Accepted Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="acceptedDate" name="acceptedDateCriteria" value="<?php echo $acceptedDateCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="prChargedBu">PR Charged BU: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="prChargedBu" name="prChargedBuCriteria" value="<?php echo $prChargedCriteria;?>" />
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
                    <?php
                    if($row2)
                    {
                    ?>
                    <div class="widget widget-table action-table">
                        <div class="widget-header">
                            <i class="icon-credit-card"></i>
                            <h3>PR Accepted</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">DOWNLOAD</th>
                                        <th rowspan="2">PR NO</th>
                                        <th rowspan="2">REQUESTER</th>
                                        <th rowspan="2">CHARGED BU</th>
                                        <th colspan="3">PROCUREMENT</th>
                                        <th rowspan="2">USER<br>ATTACH</th>
                                    </tr>
                                    <tr>
                                        <th>STATUS</th>
                                        <th>IN CHARGE</th>
                                        <th>DATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $sql_select_t_pr_header = $conn->query($query_select_t_pr_header);
                                    while($row = $sql_select_t_pr_header->fetch(PDO::FETCH_ASSOC))
                                    {
                                        $prNo               = $row['PR_NO'];
                                        $buCd               = $row['BU_CD'];
                                        $chargedBu          = $row['CHARGED_BU_CD'];
                                        $requesterName      = $row['REQUESTER_NAME'];
                                        $prStatusName       = $row['APP_STATUS_NAME'];
                                        $procInChargeName   = $row['PROC_IN_CHARGE_NAME'];
                                        $procAcceptDate     = $row['PROC_ACCEPT_DATE'];
                                        $countAttachment    = $row['COUNT_ATTACHMENT'];
                                        $prListNo++;
                                ?>
                                    <tr>
                                        <td class="td-align-right">
                                            <?php echo $prListNo; ?>.
                                        </td>
                                        <td style="text-align: center">
                                        <?php
                                            if(substr($buCd,0,1) == 'H'){
                                        ?>
                                            <a href="../lib/pdf/PR_HDI.php?prNo=<?php echo $prNo;?>" target="_blank" class="btn btn-small btn-linkedin-alt">
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>
                                        <?php
                                            }
                                            else
                                            {
                                        ?>
                                            <a href="../lib/pdf/PR_TACI.php?prNo=<?php echo $prNo;?>" target="_blank" class="btn btn-small btn-linkedin-alt">
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>
                                        <?php        
                                            }
                                        ?>
                                        </td>
                                        <td>
                                            <?php echo $prNo; ?>
                                        </td>
                                        <td>
                                            <?php echo $requesterName; ?>
                                        </td>
                                        <td>
                                            <?php echo $chargedBu; ?>
                                        </td>
                                        <td>
                                            <?php echo $prStatusName; ?>
                                        </td>
                                        <td>
                                            <?php echo $procInChargeName; ?>
                                        </td>
                                        <td>
                                            <?php echo $procAcceptDate; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php 
                                            if($countAttachment != 0){
                                            ?>
                                            <a href="#" class="btn btn-small btn-info" id="window-attach">
                                                <i class="btn-icon-only icon-paper-clip"> </i>
                                            </a>
                                            <?php    
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>  
                                    <tr>
                                        <th colspan="9">
                                        <?php
                                            if($countPr > $max_per_page)
                                            {         
                                                echo "<div id=\"pagination\" >";
                                                if ($query_select_t_pr_header != "")
                                                {
                                                        $fld = "prNoCriteria=$prNoCriteria&requesterNameCriteria=$requesterNameCriteria&prChargedBuCriteria=$prChargedCriteria&acceptedDateCriteria=$acceptedDateCriteria&mpage";
                                                }
                                                else
                                                {
                                                        $fld = "mpage";
                                                }
                                                paging($query_select_t_pr_header,$max_per_page,$num,$mpage,$fld,$countPr);
                                                echo "</div>";
                                            }
                                        ?>
                                        </th>
                                    </tr>  
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php    
                    }
                    ?>
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
                    &copy; 2018 PT. TD AUTOMOTIVE COMPRESSOR INDOENSIA. All rights reserved. 
                </div> <!-- /span12 -->	
            </div> <!-- /row -->
	</div> <!-- /container -->		
    </div> <!-- /footer-inner -->	
</div> <!-- /footer -->

<div id="dialog-confirm-logout" title="Confirm" style="display: none;"></div>
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