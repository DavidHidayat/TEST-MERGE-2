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
            $sendPoDateCriteria = $_GET['sendPoDate'];
            $poNoCriteria       = $_GET['poNo'];
            $issuedByCriteria   = $_GET['issuedBy'];
            $supplierCdCriteria = $_GET['supplierCd'];
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
        <script src="../js/epo/WEPO013.js"></script>
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
                $("#sendPoDate").datepicker({
                    dateFormat: 'dd/mm/yy',
                    maxDate: new Date,
                    autoClose: true//,
                    //beforeShowDay: $.datepicker.noWeekends
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
                <li>
                    <a href="WEPO018.php">
                        <i class="icon-th"></i><span>PO Waiting</span> 
                    </a> 
                </li>
                <li class="active">
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
                    
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-search"></i>
                            <h3>Search</h3>
                        </div>
                        <div class="widget-content">
                            <form id="WEPO013Form">
                                <div class="control-group">	
                                    <table class="table-non-bordered">
                                        <tr>
                                            <td>
                                                <label class="control-label" for="$poNo">PO No: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="poNo" name="poNo" maxlength="8" value="<?php echo $poNoCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="sendPoDate">Sent PO Date: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="sendPoDate" name="sendPoDate" maxlength="10" value="<?php echo $sendPoDateCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="supplierCd">Supplier Code: </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="supplierCd" name="supplierCd" maxlength="5" value="<?php echo $supplierCdCriteria;?>" />
                                                </div>
                                            </td>
                                            <td>
                                                <label class="control-label" for="issuedBy">Issued By (NPK): </label>
                                                <div class="controls">
                                                    <input type="text" class="span2" id="issuedBy" name="issuedBy" maxlength="10" value="<?php echo $issuedByCriteria;?>" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                            <div>
                                <button class="btn btn-primary" id="btn-search">Search</button> 
                                <button class="btn" id="btn-reset">Reset</button>
                                <?php
                                if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06')
                                {
                                ?>
                                <!--<button class="btn btn-warning" id="btn-send-po-mail">Send PO Mail</button>-->
                                <?php
                                }
                                ?>
                            </div> 
                        </div>
                    </div>
                    
                    <!----- PO Sent ---->
                    <div class="widget widget-table action-table">
                        <div class="widget-header"> <i class="icon-copy"></i>
                            <h3>PO Sent</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">DOWNLOAD</th>
                                        <th rowspan="2">PO NO</th>
                                        <th colspan="2">ISSUED</th>
                                        <th rowspan="2">SENT PO DATE</th>
                                        <th rowspan="2">DUE DATE</th>
                                        <th rowspan="2" style="display: none;">REQUESTER NPK</th>
                                        <th colspan="5">SUPPLIER</th>
                                    </tr>
                                    <tr>
										<th>DATE</th>
										<th>BY</th>
                                        <th>CODE</th>
                                        <th>NAME</th>
                                        <th>CUR</th>
                                        <th>TOTAL<br>AMOUNT</th>
                                        <th>EMAIL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $poListNo       = 0;
                                    $wherePoHeader  = array();
                                    $wherePoHeader[] = "EPS_T_PO_HEADER.PO_STATUS = '".constant('1250')."' ";
                                    if($sendPoDateCriteria){
                                        $wherePoHeader[] = "convert (VARCHAR(10), EPS_T_PO_HEADER.SEND_PO_DATE, 103) =  '$sendPoDateCriteria' ";
                                    } 
                                    if($poNoCriteria){
                                        $wherePoHeader[] = "EPS_T_PO_HEADER.PO_NO =  '$poNoCriteria' ";
                                    }
                                    if($supplierCdCriteria){
                                        $wherePoHeader[] = "EPS_T_PO_HEADER.SUPPLIER_CD = '$supplierCdCriteria' ";
                                    }
                                    if($issuedByCriteria){
                                        $wherePoHeader[] = "ltrim(EPS_T_PO_HEADER.ISSUED_BY) = '$issuedByCriteria' ";
                                    }
                                    if(!$sendPoDateCriteria && !$poNoCriteria && !$supplierCdCriteria && !$issuedByCriteria)
                                    {
                                        $wherePoHeader[] = "convert (VARCHAR(10), EPS_T_PO_HEADER.SEND_PO_DATE, 103) =  convert(varchar(10), GETDATE(), 103)";
                                    }
                                    
                                    $query = "select 
                                                EPS_T_PO_HEADER.PO_NO
                                                ,substring(EPS_T_PO_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
                                                ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 120) as SEND_PO_DATE
                                                ,EPS_T_PO_HEADER.ISSUED_BY
                                                ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                                                ,EPS_T_PO_HEADER.SUPPLIER_CD
                                                ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                                ,EPS_T_PO_HEADER.CURRENCY_CD
                                                ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                                                ,EPS_M_SUPPLIER.EMAIL
                                                ,EPS_T_PO_HEADER.DELIVERY_PLANT
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
                                                EPS_T_PO_DETAIL
                                            on
                                                EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                            left join
                                                EPS_M_EMPLOYEE
                                            on
                                                EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE.NPK
                                            left join
                                                EPS_M_SUPPLIER
                                            on 
                                                EPS_T_PO_HEADER.SUPPLIER_CD =  EPS_M_SUPPLIER.SUPPLIER_CD ";
                                    if(count($wherePoHeader)) {
                                        $query .= "where " . implode('and ', $wherePoHeader);
                                    }
                                    $query .= " group by 
                                                    EPS_T_PO_HEADER.PO_NO
                                                    ,EPS_T_PO_HEADER.ISSUED_DATE
                                                    ,EPS_T_PO_HEADER.SEND_PO_DATE
                                                    ,EPS_T_PO_HEADER.ISSUED_BY
                                                    ,EPS_M_EMPLOYEE.NAMA1
                                                    ,EPS_T_PO_HEADER.SUPPLIER_CD
                                                    ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                                    ,EPS_T_PO_HEADER.CURRENCY_CD
                                                    ,EPS_T_PO_HEADER.DELIVERY_DATE
                                                    ,EPS_M_SUPPLIER.EMAIL
                                                    ,EPS_T_PO_HEADER.DELIVERY_PLANT
                                                order by 
                                                    SEND_PO_DATE desc
                                                    ,EPS_T_PO_HEADER.PO_NO ";
                                    $sql = $conn->query($query);
                                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                        $poNo           = $row['PO_NO'];
                                        $issuedDate     = $row['ISSUED_DATE'];
                                        $sendPoDate     = $row['SEND_PO_DATE'];
                                        $requester      = $row['ISSUED_BY'];
                                        $requesterName  = $row['REQUESTER_NAME'];
                                        $supplierCd     = $row['SUPPLIER_CD'];
                                        $supplierName   = $row['SUPPLIER_NAME'];
                                        $currencyCd     = $row['CURRENCY_CD'];
                                        $supplierMail   = $row['EMAIL'];
                                        $deliveryDate   = $row['DELIVERY_DATE'];
                                        $totalAmountPo  = $row['TOTAL_AMOUNT_PO'];
                                        $poListNo++;
                                ?>
                                    <tr>
                                        <td class="td-number">
                                            <?php echo $poListNo;?>.
                                        </td>
                                        <td style="text-align: center">
                                            <a href="../lib/pdf/PO_TACI.php?poNo=<?php echo $poNo;?>" target="_blank" class="btn btn-small btn-linkedin-alt">
                                                <i class="btn-icon-only icon-download-alt"> </i>
                                            </a>
                                        </td>
                                        <td>
                                        <?php
                                            //if($requester == $sUserId || $sRoleId == 'ROLE_03'){
                                        ?>
                                                <!--<a href="../db/Redirect/PO_Screen.php?criteria=poDetail&paramPoNo=<?php echo $poNo;?>" class="faq-list">
                                                -->    <?php //echo $poNo;?>
                                                <!--</a>-->
                                        <?php        
                                            //}else{
                                            //    echo $poNo;
                                            //}
                                        ?>
											<!--<?php echo $poNo;?>	-->
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
                                        <td>
                                            <?php echo $sendPoDate;?>
                                        </td>
                                        <td>
                                            <?php echo $deliveryDate;?>
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
                                            <?php 
                                            $supplierMail = explode(",", $supplierMail);
                                            $supplierMail = implode(', ', $supplierMail);
                                            $supplierMail = preg_replace('/( )+/', '<br>', $supplierMail);
                                            echo trim($supplierMail);
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
    </body>
</html>
