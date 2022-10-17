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
                <li class="active">
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
					<!----- PO List ---->
                    <div class="widget widget-table action-table">
                        <div class="widget-header"> <i class="icon-list-ul"></i>
                            <h3>PO List</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">PO NO</th>
                                        <th colspan="2">ISSUED</th>
                                        <th rowspan="2" style="display: none;">REQUESTER NPK</th>
                                        <th colspan="4">SUPPLIER</th>
                                        <th colspan="2">DELIVERY</th>
                                        <th rowspan="2">STATUS</th>
                                        <th rowspan="2">APPROVER</th>
                                    </tr>
                                    <tr>
                                        <th>DATE</th>
                                        <th width="70px">BY</th>
                                        <th>CODE</th>
                                        <th>NAME</th>
                                        <th>CUR</th>
                                        <th>TOTAL<br>AMOUNT</th>
                                        <th>DUE DATE</th>
                                        <th>TO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $poListNo = 0;
                                    $query = "select 
                                                EPS_T_PO_HEADER.PO_NO
                                                ,substring(EPS_T_PO_HEADER.ISSUED_DATE,7,2)
                                                +'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                                ,EPS_T_PO_HEADER.SUPPLIER_CD
                                                ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                                ,EPS_T_PO_HEADER.ISSUED_BY
                                                ,EPS_M_EMPLOYEE_2.NAMA1 as REQUESTER_NAME
                                                ,EPS_T_PO_HEADER.CURRENCY_CD
                                                ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                                                ,EPS_T_PO_HEADER.PO_STATUS
                                                ,EPS_M_APP_STATUS.APP_STATUS_NAME
                                                ,EPS_T_PO_HEADER.DELIVERY_PLANT
                                                ,EPS_T_PO_HEADER.APPROVER
                                                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                ,EPS_T_PO_HEADER.UPDATE_DATE
                                                ,(select 
                                                    sum(EPS_T_PO_DETAIL.AMOUNT) 
                                                 from
                                                    EPS_T_PO_DETAIL
                                                 where
                                                    EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO) 
                                                  as TOTAL_AMOUNT_PO
                                            from
                                                EPS_T_PO_HEADER
                                            left join
                                                EPS_M_APP_STATUS
                                            on
                                                EPS_T_PO_HEADER.PO_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                                            left join
                                                EPS_M_EMPLOYEE
                                            on
                                                EPS_T_PO_HEADER.APPROVER = EPS_M_EMPLOYEE.NPK
                                            left join
                                                EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                                            on
                                                EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE_2.NPK
											left join
                                                EPS_T_PO_APPROVER 
                                            on 
                                                EPS_T_PO_HEADER.PO_NO = EPS_T_PO_APPROVER.PO_NO
                                                and EPS_T_PO_HEADER.APPROVER = EPS_T_PO_APPROVER.NPK ";
                                    
                                    if($sRoleId == 'ROLE_06' && $countApp > 0){
										/*$query .= "where 
                                                    (EPS_T_PO_HEADER.PO_STATUS = '".constant('1220')."'
                                                    and EPS_T_PO_APPROVER.NPK = '".$sUserId."')
                                                    or (EPS_T_PO_HEADER.ISSUED_BY = '".$sUserId."'
                                                        and (EPS_T_PO_HEADER.PO_STATUS != '".constant('1250')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1280')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1330')."'))";*/
										$query .= "where 
                                                    (EPS_T_PO_HEADER.PO_STATUS = '".constant('1220')."'
                                                    and EPS_T_PO_APPROVER.NPK = '".$sUserId."')
                                                    or ((EPS_T_PO_HEADER.PO_STATUS = '".constant('1210')."'
                                                        or EPS_T_PO_HEADER.PO_STATUS = '".constant('1220')."'
                                                        or EPS_T_PO_HEADER.PO_STATUS = '".constant('1230')."'))";
                                    }
                                    else if($sRoleId == 'ROLE_04'){
                                        $query .= "where 
                                                    (EPS_T_PO_HEADER.PO_STATUS = '".constant('1210')."'
                                                    or EPS_T_PO_HEADER.PO_STATUS = '".constant('1220')."'
                                                    or EPS_T_PO_HEADER.PO_STATUS = '".constant('1230')."')
                                                    and (EPS_T_PO_HEADER.APPROVER = '".$sUserId."' 
                                                    or EPS_T_PO_HEADER.ISSUED_BY = '".$sUserId."') ";
                                    }
                                    else if(($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11') && $countApp == 0){
                                        $query .= "where 
                                                    (EPS_T_PO_HEADER.PO_STATUS = '".constant('1210')."'
                                                    or EPS_T_PO_HEADER.PO_STATUS = '".constant('1220')."'
                                                    or EPS_T_PO_HEADER.PO_STATUS = '".constant('1230')."')
                                                    and EPS_T_PO_HEADER.ISSUED_BY = '".$sUserId."' ";
                                    }
                                    else{
                                        if($sRoleId == 'ROLE_03'){
                                            /*$query .= "where 
                                                        (EPS_T_PO_HEADER.PO_STATUS != '".constant('1250')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1280')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1330')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1370')."')";
											$query .= "where 
														EPS_T_PO_HEADER.PO_STATUS in ('1210','1220','1230','1240','1290','1340')";*/
											
                                            $query .= "where 
                                                        (EPS_T_PO_HEADER.PO_STATUS != '".constant('1240')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1250')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1280')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1290')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1330')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1340')."'
                                                        and EPS_T_PO_HEADER.PO_STATUS != '".constant('1370')."')";			
														
                                        }
                                    }
                                    $query .= "order by EPS_T_PO_HEADER.UPDATE_DATE ";
                                    $sql = $conn->query($query);
                                    //echo $query;
                                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                        $poNo           = $row['PO_NO'];
                                        $issuedDate     = $row['ISSUED_DATE'];
                                        $supplierCd     = $row['SUPPLIER_CD'];
                                        $supplierName   = $row['SUPPLIER_NAME'];
                                        $requester      = $row['ISSUED_BY'];
                                        $requesterName  = $row['REQUESTER_NAME'];
                                        $currencyCd     = $row['CURRENCY_CD'];
                                        $deliveryDate   = $row['DELIVERY_DATE'];
                                        $poStatus       = $row['PO_STATUS'];
                                        $poStatusName   = $row['APP_STATUS_NAME'];
                                        $deliveryPlant  = $row['DELIVERY_PLANT'];
                                        $approver       = $row['APPROVER'];
                                        $approverName   = $row['APPROVER_NAME'];
                                        $totalAmountPo  = $row['TOTAL_AMOUNT_PO'];
                                        $poListNo++;
                                ?>
                                    <tr>
                                        <td class="td-number">
                                            <?php echo $poListNo;?>.
                                        </td>
                                        <td>
                                            <a href="../db/Redirect/PO_Screen.php?criteria=poDetail&paramPoNo=<?php echo $poNo;?>" class="faq-list">
                                                <?php echo $poNo;?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo $issuedDate;?>
                                        </td>
                                        <td>
											<?php echo substr($requesterName, 0, strpos($requesterName, ' '));?>	
                                        </td>
                                        <td style="display: none;">
                                            <?php echo $requester;?>
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
                                        <td class="td-align-right">
                                            <?php 
                                                $split_total_amount = explode('.', $totalAmountPo);
                                                if($split_total_amount[1] == 0)
                                                {
                                                    $totalAmountPo = number_format($totalAmountPo);
                                                }
                                                else
                                                {
                                                    $totalAmountPo = number_format($totalAmountPo, 2);
                                                }
                                                echo $totalAmountPo;
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $deliveryDate;?>
                                        </td>
                                        <td>
                                            <?php echo $deliveryPlant;?>
                                        </td>
                                        <td>
                                            <?php echo $poStatusName;?>
                                        </td>
                                        <td>
                                            <?php echo substr($approverName, 0, strpos($approverName, ' '));?>
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
    </body>
</html>
