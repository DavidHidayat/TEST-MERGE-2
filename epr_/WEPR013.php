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
        /** Unset SESSION */
        unset($_SESSION['prStatus']);
        unset($_SESSION['poStatus']);
        
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
        $sInvType   = $_SESSION['sInvType'];
        $sPrScreen  = $_SESSION['prScreen'];
        $sPrStatus  = $_SESSION['prStatusSession'];
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
<?php
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
                    <a href="WEPR001.php">
                        <i class="icon-list-ul"></i><span>PR List</span> 
                    </a> 
                </li>
                <li class="active">
                    <a href="WEPR013.php">
                        <i class="icon-th"></i><span>PR Waiting</span> 
                    </a> 
                </li>
                <li>
                    <a href="../epr_/WEPR002.php">
                        <i class="icon-pencil"></i><span>Create New PR</span> 
                    </a>
                </li>
                <li>
                    <a href="../epr/WEPR007.php">
                        <i class="icon-upload"></i><span>Upload PR</span> 
                    </a>
                </li>
                <li>
                    <a href="WEPR090.php">
                        <i class="icon-search"></i><span>PR Search</span> 
                    </a>
                </li>
                <?php
                if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_08')
                {
                ?>
                <li>
                    <a href="WEPR091.php">
                        <i class="icon-search"></i><span>PO Search</span> 
                    </a>
                </li>
                <?php 
                }
                ?>
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
                    
                    <!----- PR List ---->
                    <div class="widget widget-table action-table">
                        <div class="widget-header"> <i class="icon-list-alt"></i>
                            <h3>PR Waiting for Approval</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">PR NO</th>
                                        <th rowspan="2">ISSUED DATE</th>
                                        <th rowspan="2">REQUESTER</th>
                                        <th rowspan="2" style="display: none">BU CD</th>
                                        <th colspan="2">BU</th>
                                        <th rowspan="2">STATUS</th>
                                        <th rowspan="2">APPROVER</th>
                                        <th rowspan="2">DOWNLOAD</th>
                                    </tr>
                                    <tr>
                                        <th>ISSUER</th>
                                        <th>CHARGED</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
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
                                    $wherePrHeader[] = "EPS_T_PR_HEADER.PR_STATUS = '".constant('1020')."'
                                                        and EPS_T_PR_HEADER.APPROVER = '$sUserId'";
                                    /**
                                     * SELECT COUNT EPS_T_PR_HEADER
                                     **/
                                    $query_count_t_pr_header = "select count
                                                                    (*) 
                                                                as 
                                                                    COUNT_PR 
                                                                from 
                                                                    EPS_T_PR_HEADER 
                                                                left join
                                                                    EPS_M_EMPLOYEE 
                                                                on 
                                                                    EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                                                left join
                                                                    EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                                                                on
                                                                    EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE_2.NPK ";
                                    if(count($wherePrHeader)) {
                                        $query_count_t_pr_header .= "where " . implode('and ', $wherePrHeader);
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
                                    $query = "select 
                                                * 
                                              from 
                                                (select top  $max_per_pages  
                                                    * 
                                                from      
                                                    (select top $start 
                                                        EPS_T_PR_HEADER.PR_NO
                                                        ,EPS_T_PR_HEADER.BU_CD
                                                        ,EPS_T_PR_HEADER.ISSUED_DATE
                                                        ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)
                                                        +'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as NEW_ISSUED_DATE
                                                        ,EPS_M_APP_STATUS.APP_STATUS_NAME as PR_STATUS_NAME
                                                        ,EPS_T_PR_HEADER.REQUESTER
                                                        ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                        ,EPS_T_PR_HEADER.APPROVER
                                                        ,EPS_T_PR_HEADER.REQ_BU_CD
                                                        ,EPS_T_PR_HEADER.CHARGED_BU_CD
                                                        ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                                                        ,EPS_M_EMPLOYEE_2.NAMA1 as APPROVER_NAME
                                                    from 
                                                        EPS_T_PR_HEADER 
                                                    inner join
                                                        EPS_M_APP_STATUS
                                                    on
                                                        EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                                    left join 
                                                        EPS_M_EMPLOYEE 
                                                    on 
                                                        EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE.NPK
                                                    left join
                                                        EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                                                    on
                                                        EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE_2.NPK ";
                                    if(count($wherePrHeader)) {
                                        $query .= "where " . implode('and ', $wherePrHeader);
                                    }
                                    $query .= "
                                                    group by 
                                                        EPS_T_PR_HEADER.PR_NO
                                                        ,EPS_T_PR_HEADER.BU_CD
                                                        ,EPS_T_PR_HEADER.ISSUED_DATE
                                                        ,EPS_M_APP_STATUS.APP_STATUS_NAME
                                                        ,EPS_T_PR_HEADER.REQUESTER
                                                        ,EPS_M_EMPLOYEE.NAMA1
                                                        ,EPS_T_PR_HEADER.APPROVER
                                                        ,EPS_T_PR_HEADER.REQ_BU_CD
                                                        ,EPS_T_PR_HEADER.CHARGED_BU_CD
                                                        ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
                                                        ,EPS_M_EMPLOYEE_2.NAMA1
                                                    order by 
                                                        EPS_T_PR_HEADER.ISSUED_DATE asc
                                                        ,EPS_T_PR_HEADER.PR_NO desc) 
                                                    as T1 
                                                order by 
                                                    ISSUED_DATE desc
                                                    ,PR_NO asc) 
                                                as T2 
                                              order by 
                                                ISSUED_DATE asc
                                                ,PR_NO desc ";
//                                    echo $query;
                                    $sql = $conn->query($query);
                                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                        $prNo           = $row['PR_NO'];
                                        $prBuCd         = $row['BU_CD'];
                                        $newIssuedDate  = $row['NEW_ISSUED_DATE'];
                                        $prStatus       = $row['PR_STATUS_NAME'];
                                        $prIssuerBu     = $row['REQ_BU_CD'];
                                        $prChargedBu    = $row['CHARGED_BU_CD'];
                                        $requester      = $row['REQUESTER'];
                                        $requesterName  = addslashes($row['REQUESTER_NAME']);
                                        $approver       = $row['APPROVER'];
                                        $approverName   = addslashes($row['APPROVER_NAME']);
                                        $specialType    = $row['SPECIAL_TYPE_ID'];
                                        if($specialType == 'IT'){
                                            $specialType = 'IT Equipment';
                                        }
                                        if($specialType == 'NIT'){
                                            $specialType = '';
                                        }
                                        
                                        $prListNo++;
                                ?>
                                    <tr>
                                        <td class="td-number">
                                            <?php echo $prListNo;?>.
                                        </td>
                                        <td>
                                            <!--<a href="../db/Login/Redirect_Login.php?prNo=<?php echo $prNo;?>" class="faq-list">
                                                <b><?php echo $prNo;?></b>
                                            </a>-->
											<a href="../db/Redirect/PR_Screen.php?paramPrNo=<?php echo $prNo;?>" class="faq-list">
                                                <b><?php echo $prNo;?></b>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo $newIssuedDate; ?>
                                        </td>
                                        <td>
                                            <?php echo stripslashes($requesterName);?>
                                        </td>
                                        <td>
                                            <?php echo $prIssuerBu; ?>
                                        </td>
                                        <td>
                                            <?php echo $prChargedBu; ?>
                                        </td>
                                        <td>
                                            <?php echo $prStatus; ?>
                                        </td>
                                        <td>
                                            <?php echo $approverName; ?>
                                        </td>
                                        <td style="text-align: center">
                                        <?php
                                            if(substr($prBuCd,0,1) == 'H'){
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
                                                $fld = "mpage";
                                                paging($query,$max_per_page,$num,$mpage,$fld,$countPr);
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
    </body>
</html>

