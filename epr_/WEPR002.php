<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
//include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb_ERFI.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PR/EPS_T_PR_SEQUENCE.php";

if(isset($_SESSION['sUserId']))
{      
    $sUserId            = $_SESSION['sUserId'];
    $sActiveFlag		= $_SESSION['sactiveFlag'];
    $sActiveFlagLogin	= $_SESSION['sactiveFlagLogin'];
    
    if($sUserId != '' && $sActiveFlag == 'A' && $sActiveFlagLogin == 'A')
    {
        $sNPK           = $_SESSION['sNPK'];
        $sNama          = $_SESSION['sNama'];
        $sBunit         = $_SESSION['sBunit'];
        $sSeksi         = $_SESSION['sSeksi'];
        $sKdper         = $_SESSION['sKdper'];
        $sNmPer         = $_SESSION['sNmper'];
        $sKdPlant       = $_SESSION['sKDPL'];
        $sNmPlant       = $_SESSION['sNMPL'];
        $sRoleId        = $_SESSION['sRoleId'];
        $sInet          = $_SESSION['sinet'];
        $sNotes         = $_SESSION['snotes'];
        $sBuLogin       = $_SESSION['sBuLogin'];
        $sBuLoginName   = $_SESSION['sBuLoginName'];
        $sUserType      = $_SESSION['sUserType'];
        $sInvType       = $_SESSION['sInvType'];
        $sPrScreen      = $_SESSION['prScreen'];
        $sPrStatus      = $_SESSION['prStatusSession'];
        $currentDate    = date("d/m/Y");
        $currentDateYmd = date(Ymd);
        $prNo           = getPrNo($sUserId, trim($sBuLogin), 'getPrNo');
      
        /**************
         * UNSET SESSION
         **************/
        unset($_SESSION['prStatus']);
        unset($_SESSION['poStatus']);
        unset($_SESSION['prItem']);
        unset($_SESSION['prAttachment']);
        
        /*$currentMonth = date(Ymd);
        $dirName = $_SERVER['DOCUMENT_ROOT']."/EPS/db/ATTACHMENT/TEMPORARY/$currentMonth/$sUserId/";
        /** Check existing Fix folder to remove Fix folder **/ 
        /*if(is_dir($dirName)){
            $files = scandir($dirName);
            unset($files[array_search(".",$files)]);
            unset($files[array_search("..",$files)]);
			
            if(count($files) == 0) {
                @unlink($dirName.'/'.'Thumbs.db');
                rmdir($dirName);
            }
            else{
                $dh = opendir($dirName);
                while($file = readdir($dh)){
                    if(!is_dir($file)){
                        @unlink($dirName.'/'.$file);
                    }
                }
                @unlink($dirName.'/'.'Thumbs.db');
                closedir($dh);
                rmdir($dirName);
            }
        }*/
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
	<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 02 May 2015 21:00:00 GMT">
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
        <script type="text/javascript" src="../js/Common_epr.js"></script>
        <script src="../js/epr/WEPR002.js"></script>
        <script>
            maximize();
            //Check if the current URL contains '#' 
            if(document.URL.indexOf("#")==-1)
            {
                // Set the URL to whatever it was plus "#".
                url = document.URL+"#";
                location = "#";

                //Reload the page
                location.reload(true);
            }
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
                        <i class="icon-user"></i> Welcome, <?php echo stripslashes($sNama); ?> (#User ID: <?php echo $sUserId; ?> #BU Code: <?php echo trim($sBuLogin); ?>) 
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
                <li>
                    <a href="WEPR013.php">
                        <i class="icon-th"></i><span>PR Waiting</span> 
                    </a> 
                </li>
                <li class="active">
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
                    <input type="hidden" id="userIdLoginHidden" value="<?php echo $sUserId;?>" />
                    <input type="hidden" id="buLoginHidden"  value="<?php echo $sBuLogin;?>" />
                    <input type="hidden" id="userTypeHidden"  value="<?php echo $sUserType;?>" />
                    <input type="hidden" id="npkHidden" value="<?php echo $sNPK;?>" />
                    <input type="hidden" id="buCdHidden" value="<?php echo $sBunit;?>" />
                    <input type="hidden" id="sectionCdHidden" value="<?php echo $sSeksi;?>" />
                    <input type="hidden" id="companyCdHidden" value="<?php echo $sKdper;?>" />
                    <input type="hidden" id="plantCdHidden" value="<?php echo $sKdPlant;?>" />
                    <input type="hidden" id="invTypeHidden" value="<?php echo $sInvType;?>" />
                    <input type="hidden" id="actionFormHidden" value="CREATE" />
                    <input type="hidden" id="prItemTotalHidden" name="prItemTotalHidden" value=0>
                    
                    <!---------------------------------- PR Header --------------------------------->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-file"></i>
                            <h3>PR Information</h3>
                        </div>
                        <div class="widget-content">
                            <div class="control-group">	
                                <table class="table-no-border">
                                    <tr>
                                        <td>
                                            <div>	
                                                <label class="control-label" for="prNo">PR No : </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control" id="prNo" value="<?php echo $prNo;?>" readonly="readonly">
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                        <td>
                                            <div>											
                                                <label class="control-label" for="issuerBu">Issuer BU Code/Name : </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control" id="issuerBu" value="<?php echo $sBuLogin." - ".trim($sBuLoginName);?>" readonly />
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                        <td>
                                            <div>											
                                                <label class="control-label" for="specialTypeId">Category : </label>
                                                <div class="controls">
                                                    <select id="specialTypeId" class="form-control" name="specialTypeId">
                                                        <option value="">Choose..</option>
                                                        <option value="IT">IT EQUIPMENT</option>
                                                        <option value="NIT">NON IT EQUIPMENT</option>
                                                    </select>	
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>											
                                                <label class="control-label" for="prDate">PR Date : </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control" id="prDate" value="<?php echo $currentDate;?>" readonly="readonly">	
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                        <td>
                                            <div>											
                                                <label class="control-label" for="chargedBu">Charged BU Code/Name : </label>
                                                <div class="controls">
                                                    <select id="chargedBu" class="form-control"  name="chargedBu">
                                                        <option value="">Choose...</option>
                                                        <?php
                                                            $query_select_m_bunit_company = "select 
                                                                                                COMPANY_CD
                                                                                             from
                                                                                                EPS_M_BUNIT
                                                                                             where
                                                                                                BU_CD = '$sBuLogin' ";
                                                            $sql_select_m_bunit_company = $conn->query($query_select_m_bunit_company);
                                                            while($row_select_m_bunit_company = $sql_select_m_bunit_company->fetch(PDO::FETCH_ASSOC)){
                                                                $companyBuLogin = $row_select_m_bunit_company['COMPANY_CD'];
                                                            }
                                                            
                                                            $businessUnit = array("3221 "
                                                                                , "3222 "
                                                                                , "3910 "
                                                                                , "3911 "
                                                                                , "4211 "
                                                                                , "4221 "
                                                                                , "4222 "
                                                                                , "4410 "
                                                                                , "4411 "
                                                                                , "4420 "
                                                                                , "4241 "
                                                                                , "4242 "
                                                                                , "4243 "
                                                                                , "4610 "
                                                                                , "4620 "
                                                                                , "4922 "
                                                                                , "4710 "
                                                                                , "4720 "
                                                                                , "4731 "
                                                                                , "4732 "
                                                                                , "4733 "); 

                                                            $query = "select 
                                                                        BU_CD
                                                                        ,BU_NAME
                                                                        ,BU_CD + '- ' + BU_NAME as BU_CD_NAME
                                                                    from 
                                                                        EPS_M_BUNIT
                                                                    where
                                                                        BU_CD like 'T%'
                                                                        and ACTIVE_FLAG = 'A'";
															// HDI			
                                                            if($sKdper == "T")
                                                            {
                                                                $query .= "and COMPANY_CD = 'T'";
                                                            }
                                                            // AINE
                                                            else if($sKdper == "M")
                                                            {
                                                                $query .= "and COMPANY_CD = 'M'";
                                                            }
                                                            // DSIA
                                                            // DSIA BU Code
                                                            else if($sKdper == "S" || ($sKdper == "D" && $companyBuLogin == "S"))
                                                            {
                                                                $query .= "and COMPANY_CD = 'S'";
                                                            }
                                                            // DNIA (non cross company)
                                                            else if(!in_array($sBuLogin, $businessUnit)) 
                                                            {
                                                                $query .= "and COMPANY_CD = 'D'";
                                                            }
                                                            // DNIA (cross company - exclude AINE)
                                                            else
                                                            {
                                                                $query .= "and COMPANY_CD != 'M'";
                                                            }
                                                            $query .= "order by 
                                                                        BU_CD";
                                                            $sql = $conn->query($query);
                                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                                $buCd   = $row['BU_CD'];
                                                                $buCdName = $row['BU_CD_NAME'];
                                                            ?>
                                                                <option value="<?php echo $buCd;?>" <?php if($buCd == $sBuLogin) echo "selected"; ?>><?php echo $buCdName;?></option>
                                                        <?php         
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                        <td>
                                       
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <div>											
                                                <label class="control-label" for="purpose">Purpose : </label>
                                                <div class="controls">
                                                     <textarea rows="3" class="form-control" id="purpose"></textarea>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div><!-- /widget -->
                        
                    <!---------------------------------- PR Requester --------------------------------->
                    <div class="widget ">
                        <div class="widget-header">
                            <i class="icon-user"></i>
                            <h3>Requester Information</h3>
                        </div>
                        <div class="widget-content">
                            <div class="control-group">	
                                <table class="table-no-border">
                                    <tr>
                                        <td>
                                            <div>											
                                                <label class="control-label" for="requesterName">Name : </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control" id="requesterName" value="<?php echo trim($sNama);?>" readonly="readonly">	
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                        <td class="col-td-70">
                                            <div>											
                                                <label class="control-label" for="buCd">BU Code : </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control" id="buCd" value="<?php echo $sBunit;?>" readonly="readonly">	
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                        <td class="col-td-140">
                                            <div>											
                                                <label class="control-label" for="plant">Plant : </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control" id="plant" value="<?php echo trim($sNmPlant);?>" readonly="readonly">	
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                        <td>
                                            <div>											
                                                <label class="control-label" for="company">Company : </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control" id="company" value="<?php echo trim($sNmPer);?>" readonly="readonly">	
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                        <td class="col-td-70">
                                            <div>											
                                                <label class="control-label" for="niceNet">Ext : </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control" id="niceNet"  maxlength="8">	
                                                </div>
                                            </div> <!-- /controls -->
                                        </td>
                                    </tr>
                                </table>
                            </div><!-- /control-group -->
                        </div><!-- /widget-content -->
                    </div><!-- /widget -->
                        
                       
                    <!---------------------------------- PR Detail --------------------------------->
                    <div class="widget widget-table action-table">
                        <div class="widget-header"> <i class="icon-th-list"></i>
                            <h3>PR Detail</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered" id="prItemTable">
                                <thead>
                                    <tr id="tr-first-item">
                                        <th class="td-actions" colspan="14" style="text-align: left">
                                            <a href="#" class="btn btn-small btn-warning" id="btn-add-item">
                                                <i class="btn-icon-only icon-plus"> </i> ADD
                                            </a>
                                            <a href="#" class="btn btn-small btn-success" id="btn-edit-item">
                                                <i class="btn-icon-only icon-pencil"> </i> EDIT
                                            </a>
                                            <a href="#" class="btn btn-small btn-danger" id="btn-del-item">
                                                <i class="btn-icon-only icon-remove"> </i> DELETE
                                            </a>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">OPTION</th>
                                        <th colspan="2">ITEM</th>
                                        <th rowspan="2" style="display: none">ITEM NAME REF</th>
                                        <th rowspan="2">DUE DATE</th>
                                        <th rowspan="2" style="display: none">TYPE</th>
                                        <th rowspan="2">EXP.</th>
                                        <th colspan="2">INVESTMENT</th>
                                        <th rowspan="2">UM</th>
                                        <th rowspan="2">QTY</th>
                                        <th colspan="2">USER REFERENCE (ESTIMATE)</th>
                                        <th rowspan="2">AMOUNT</th>
                                        <th rowspan="2">REMARK</th>
                                    </tr>
                                    <tr>
                                        <th>CODE</th>
                                        <th>NAME</th>
                                        <th>RFI NO.</th>
                                        <th>FA CODE</th>
                                        <th>UNIT PRICE</td>
                                        <th style="display: none">SUPPLIER CD</td>
                                        <th>SUPPLIER</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                   <!---------------------------------- Upload Attachment --------------------------------->
                    <div class="widget">
                        <div class="widget-header"> 
                            <i class="icon-upload"></i>
                            <h3>Upload Attachment</h3>
                        </div>
                        <div class="widget-content">
                            <div class="control-group">	
                                <table class="table-no-border">
                                    <tr>
                                        <td>
                                            <div>
                                                <label class="control-label" for="itemName">Item Name :</label>
                                                <div class="controls">
                                                    <select id="itemNameFile" class="form-control">
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-td-70">
                                            <div>
                                                <label class="control-label" for="itemCd">Item Code: </label>
                                                <div class="dialog-controls">
                                                    <input type="text" id="itemCdFile" class="form-control" maxlength="10" readonly />
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-td-350">
                                            <div>
                                                <label class="control-label" for="fileUpload">File :</label>
                                                <div class="controls">
                                                    <input type="file" class="form-control" id="fileUpload" name="fileUpload">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <br/>
                                <div id="status"></div>
                            </div>
                        </div>
                    </div>
                        
                    <!---------------------------------- PR Attachment --------------------------------->
                    <div class="widget widget-table action-table">
                        <div class="widget-header"> <i class="icon-link"></i>
                            <h3>PR Attachment</h3>
                        </div>
                        <div class="widget-content">
                            <table class="table table-striped table-bordered" id="prAttachmentTable">
                                <thead>
                                    <tr id="tr-first-attachment">
                                        <th class="td-actions" colspan="14" style="text-align: left">
                                            <a href="#" class="btn btn-small btn-danger" id="btn-del-attachment">
                                                <i class="btn-icon-only icon-remove">  </i>Delete
                                            </a>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">OPTION</th>
                                        <th colspan="2">ITEM</th>
                                        <th colspan="3">FILE</th>
                                    </tr>
                                    <tr>
                                        <th>CODE</th>
                                        <th>NAME</th>
                                        <th>NAME</th>
                                        <th>TYPE</th>
                                        <th>SIZE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                        
                        <!---------------------------------- PR Approver --------------------------------->
                        <div class="widget">
                            <div class="widget-header"> 
                                <i class="icon-list-ol "></i>
                                <h3>PR Approver</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">
                                    <table class="table-no-border" id="prAppTable">
                                    <?php
                                    
                                        $query_select_m_approver_by_max_app = "select 
                                                                                    MAX(APPROVER_NO) as MAX_APPROVER
                                                                                from
                                                                                    EPS_M_PR_APPROVER
                                                                                where
                                                                                    BU_CD = '$sBuLogin'";
                                        $sql_select_m_approver_by_max_app = $conn->query($query_select_m_approver_by_max_app);
                                        $row_select_m_approver_by_max_app = $sql_select_m_approver_by_max_app->fetch(PDO::FETCH_ASSOC);
                                        $max_app = $row_select_m_approver_by_max_app['MAX_APPROVER'];
                                        
                                        for($i = 0; $i < $max_app; $i++){
                                            $approverNo =  $i+1;
                                    ?>
                                    <tr>
                                        <td class="col-td-210">
                                            <div>
                                                <label class="control-label" for="approver">Approver No. <?php echo $approverNo;?>: </label>
                                                <select id="approverDept<?php echo $approverNo;?>" name="approverDept" class="form-control">
                                                    <option value="">Choose..</option>
                                                    <?php
                                                    /** 
                                                    * SELECT APPROVER DEPT BY APPROVER NO & BU
                                                    **/
                                                    $query_select_m_app_dept_by_appno = "select 
                                                                                                EPS_M_PR_APPROVER.NPK
                                                                                                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                                                            from 
                                                                                                EPS_M_PR_APPROVER
                                                                                            left join
                                                                                                EPS_M_EMPLOYEE
                                                                                            on 
                                                                                                EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
																							left join
																								EPS_M_USER
																							on 
																								EPS_M_USER.USERID = EPS_M_EMPLOYEE.NPK
                                                                                            where 
                                                                                                APPROVER_NO = '$approverNo'
                                                                                                and EPS_M_PR_APPROVER.BU_CD = '$sBuLogin'
																								and ACTIVE_FLAG = 'A'";
                                                    $sql_select_m_app_dept_by_appno = $conn->query($query_select_m_app_dept_by_appno);
//                                                    echo $query_select_m_app_dept_by_appno;
                                                    while($row_select_m_app_dept_by_appno = $sql_select_m_app_dept_by_appno->fetch(PDO::FETCH_ASSOC))
                                                    {
                                                        $appNpkDept  = $row_select_m_app_dept_by_appno['NPK'];
                                                        $appNameDept = $row_select_m_app_dept_by_appno['APPROVER_NAME'];
                                                    ?>
                                                        <option value="<?php echo $appNpkDept;?>"><?php echo trim($appNameDept);?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                
                                                <?php
                                                if($approverNo < $max_app)
                                                {
                                                ?>
                                                    <input type="checkbox" name="checkedBypass[]" disabled="disabled" id="setBypassNoDept<?php echo $approverNo;?>">&nbsp;Bypass Approver No.&nbsp;<?php echo $approverNo;?>
                                                <?php    
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            if($approverNo < $max_app)
                                            {
                                            ?>
                                                <label>Remark :</label>
                                                <input type="text" id="setRemarkBypassDept<?php echo $approverNo;?>"  class="form-control" name="remarkBypass" maxlength="100" readonly="readonly" />
                                            <?php    
                                            }
                                            ?>
                                        <br/>
                                        </td>
                                    </tr>
                                            
                                    <?php        
                                        }
                                    ?>
                                    </table>
                                    <input type="hidden" id="countAppDept"  value="<?php echo $max_app;?>" />
                                </div>
                            </div>
                        </div>
                        
                        <!---------------------------------- IS Approver --------------------------------->
                        <div class="widget">
                            <div class="widget-header"> 
                                <i class="icon-list-ol "></i>
                                <h3>IS Approver</h3>
                            </div>
                            <div class="widget-content">
                                <div class="control-group">
                                    <table class="table-no-border">
                                        <tr>
                                            <td class="col-td-210">
                                                <div>
                                                    <label class="control-label" for="approver">IT Approver: </label>
                                                    <div class="controls">
                                                        <select id="approverIT" class="form-control" disabled>
                                                            <option value="">Choose..</option>
                                                            <?php
                                                            /** 
                                                            * SELECT APPROVER DEPT BY APPROVER NO & BU
                                                            **/
                                                            $query_select_m_app_special_it = "select 
                                                                                                EPS_M_PR_SPECIAL_APPROVER.NPK
                                                                                                ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                                                                            from 
                                                                                                EPS_M_PR_SPECIAL_APPROVER
                                                                                            left join
                                                                                                EPS_M_EMPLOYEE
                                                                                            on 
                                                                                                EPS_M_PR_SPECIAL_APPROVER.NPK = EPS_M_EMPLOYEE.NPK ";
                                                            if($sKdper == "M")
                                                            {
                                                                $query_select_m_app_special_it .= "where 
                                                                                                    SPECIAL_APPROVER_CD = '004'"; 
                                                            }
                                                            else
                                                            {
                                                                $query_select_m_app_special_it .= "where 
                                                                                                    SPECIAL_APPROVER_CD = '001'";
                                                            }
                                                            $sql_select_m_app_special_it = $conn->query($query_select_m_app_special_it);
                                                            while($row_select_m_app_special_it = $sql_select_m_app_special_it->fetch(PDO::FETCH_ASSOC))
                                                            {
                                                                $appNpkIt  = $row_select_m_app_special_it['NPK'];
                                                                $appNameIt = $row_select_m_app_special_it['APPROVER_NAME'];
                                                            ?>
                                                                <option value="<?php echo $appNpkIt;?>"><?php echo trim($appNameIt);?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!---------------------------------- Button --------------------------------->
                        <div class="form-actions"> 
                            <button class="btn btn-primary" id="btn-send" name="btn-save-send">Send for Approval</button>
                            <button class="btn btn-success" id="btn-save" name="btn-save-send">Save As Draft</button> 
                            <button class="btn" id="btn-back">Back</button>
                        </div>
                        
                        <!---------------------------------- Message --------------------------------->
                        <div class="alert alert-danger" id="undefined-msg" style="display: none;">
                            <b>Undefined Error!</b> System Error occurs. Please report to system administrator.
                        </div>
                        <div class="alert alert-danger" id="mandatory-msg-1" style="display: none;">
                            <b>Mandatory!</b> Please fill all the field.
                        </div>
                        <div class="alert alert-danger" id="mandatory-msg-2" style="display: none;">
                            <b>Mandatory!</b> Please register for PR approver (Department) because approver information does not exist.
                        </div>
                        <div class="alert alert-danger" id="mandatory-msg-3" style="display: none;">
                            <b>Mandatory!</b> Please select PR approver (Department).
                        </div>
                        <div class="alert alert-danger" id="mandatory-msg-4" style="display: none;">
                            <b>Mandatory!</b> Please input "Remark" of bypass approval (Department).
                        </div>  
                        <div class="alert alert-danger" id="mandatory-msg-5" style="display: none;">
                            <b>Mandatory!</b> Please select approver (IT) for category IT Equipment.
                        </div>
                        <div class="alert alert-danger" id="mandatory-msg-6" style="display: none;">
                            <b>Mandatory!</b> Please input PR Detail information.
                        </div>
                        <div class="alert alert-info" id="mandatory-msg-7" style="display: none;">
                            <b>Duplicate!</b> PR No already exist and updated to new PR No. Please continue click Save as Draft or Send for Approval.
                        </div>
                        <div class="alert alert-danger" id="mandatory-msg-8" style="display: none;">
                            <strong>Mandatory!</strong> Please specify delivery date in PR Detail in 15 days from today.
                        </div>
                        <div class="alert alert-success" id="save-msg" style="display: none">
                            <strong>Success!</strong> Save PR finished.
                        </div>
                        <div class="alert alert-success" id="send-msg" style="display: none">
                            <strong>Success!</strong> Send PR finished.
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
<div id="dialog-confirm-save-send" title="Confirm" style="display: none;"></div>
<div id="dialog-confirm-cancel" title="Confirm" style="display: none;"></div>

<div id="dialog-form-item-delete" title="Confirm" style="display: none;"></div>
<div id="dialog-form-item" title="Add PR Item" style="display: none;">
    <div class="widget ">
        <form id="WEPR002Form-dialog">
            <div class="widget-content">
                <div class="control-group">
                    <input type="hidden" id="itemSeqHidden" />
                    <input type="hidden" id="itemNameRefHidden" />
                    <table class="table-no-border">
                        <tr>
                            <td>
                                <div>	
                                    <label class="control-label" for="itemType">Type : </label>
                                    <div class="controls">
                                        <select id="itemType" class="form-control">
                                            <option value=""></option>
                                            <?php
												$businessUnit = array("3221 "
                                                                                , "3222 "
                                                                                , "3910 "
                                                                                , "4211 "
                                                                                , "4410 "
                                                                                , "4411 "
                                                                                , "4420 "
                                                                                , "4510 "
                                                                                , "4610 "
                                                                                , "4620 "
                                                                                , "4710 "
                                                                                , "4720 "
                                                                                , "4731 ");
                                                $query = "select 
                                                            ITEM_TYPE_CD
                                                            ,ITEM_TYPE_NAME
                                                          from 
                                                            EPS_M_ITEM_TYPE
                                                          where
                                                            ACTIVE_FLAG = 'A'";
                                                // Inventory Machinery
                                                if($sInvType == "N1000" && $sBuLogin == "T3223 ")
                                                {
                                                    $query .= " and ITEM_TYPE_CD in ('1','2','5','7')";
                                                }
                                                 // Machinery
                                                else if($sInvType != "N1000" && in_array($sBuLogin, $businessUnit))
                                                {
                                                    $query .= " and ITEM_TYPE_CD in ('1','2','7')";
                                                }
                                                // Inventory PTIC
                                                else if($sInvType == "N1000" && ($sBuLogin == "T3223" || $sBuLogin = "T4430"|| $sBuLogin = "T4420"|| $sBuLogin = "T4421" || $sBuLogin = "T4720" || $sBuLogin = "T7133"))
                                                {
                                                    $query .= " and ITEM_TYPE_CD in ('1','2','3')";
                                                }
                                                // Machinery HDI, Sparepart ASMO
                                                else if(($sKdper == "H" && $sInvType == "N1000") || ($sKdper == "M" && $sInvType == "N1000"))
                                                {
                                                    $query .= " and ITEM_TYPE_CD in ('1','6','7')";
                                                }
                                                // HDI, ASMO
                                                else if($sKdper == "H" || $sKdper == "M" )
                                                {
                                                    $query .= " and ITEM_TYPE_CD in ('1','7')";
                                                }
                                                // Inventory DSIA
                                                else if($sInvType == "N1001")
                                                {
                                                    $query .= " and ITEM_TYPE_CD in ('1','2','4')";
                                                }
                                                else
                                                {
                                                    $query .= " and ITEM_TYPE_CD in ('1','2')";
                                                }
                                                $sql = $conn->query($query);
                                                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                    $itemTypeCd     = $row['ITEM_TYPE_CD'];
                                                    $itemTypeName   = $row['ITEM_TYPE_NAME'];
                                            ?>
                                            <option value="<?php  echo $itemTypeCd;?> "><?php  echo $itemTypeName;?></option>
                                            <?php         
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div> <!-- /controls -->
                            </td>
                            <td colspan="3">
                                <div>
                                    <label class="control-label" for="expNo">Expense No : </label>
                                    <div class="controls">
                                        <select id="expNo" class="form-control">
                                            <option value="<?php // echo $unitCd;?>"><?php // echo $unitName;?></option>
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <label class="control-label" for="rfiNo">RFI No : </label>
                                    <div class="controls">
                                        <input type="text" id="rfiNo" class="form-control" maxlength="6" readonly="readonly"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
<!--                        <tr>
                            <td>
                                <div>
                                    <label class="control-label" for="rfiNo">RFI No : </label>
                                    <div class="controls">
                                        <input type="text" id="rfiNo-D" class="form-control" maxlength="6" readonly="readonly"/>
                                    </div>
                                </div>
                            </td>
                            <td colspan="2">
                                <div>
                                    <label class="control-label" for="faCd">FA Code : </label>
                                    <div class="controls">
                                        <select id="faCd" class="form-control" disabled>
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <label class="control-label" for="rfiNo">RFI No - Non EFAM : </label>
                                    <div class="controls">
                                        <input type="text" id="rfiNo-H" class="form-control" maxlength="6" readonly="readonly" />
                                    </div>
                                </div>
                            </td>
                            <td colspan="2">
                                <div>
                                    <label class="control-label" for="faCd">FA Code - Non EFAM : </label>
                                    <div class="controls">
                                        <input type="text" id="faCd-H" class="form-control" maxlength="14" readonly="readonly" />
                                    </div>
                                </div>
                            </td>
                        </tr>-->
                        <tr>
                            <td>
                                <div>
                                    <label class="control-label" for="itemCd">Item Code : </label>
                                    <div class="dialog-controls">
                                        <input type="text" id="itemCd" class="form-control" readonly />
                                    </div>
                                </div>
                            </td>
                            <td colspan="4">
                                <div>
                                    <label class="control-label" for="itemName">Item Name : </label>
                                    <div class="controls">
                                        <input type="text" id="itemName" class="form-control" maxlength="200" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                         <tr>
                            <td>
                                <div>
                                    <label class="control-label" for="supplierCd">Supplier Code : </label>
                                    <div class="controls">
                                        <input type="text" id="supplierCd" class="form-control" readonly />
                                    </div>
                                </div>
                            </td>
                            <td colspan="4">
                                <div>
                                    <label class="control-label" for="supplierName">Supplier Name : </label>
                                    <div class="controls">
                                        <input type="text" id="supplierName" class="form-control" maxlength="200" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <label class="control-label" for="deliveryDate">Due Date : </label>
                                    <div class="dialog-controls">
                                        <input type="text" id="deliveryDate" class="form-control" style="background-color: #fff;" readonly="readonly"  />
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <label class="control-label" for="um">U M : </label>
                                    <div class="dialog-controls">
                                        <select id="um" class="form-control">
                                        <?php
                                            $query = "select 
                                                        UNIT_CD
                                                        ,UNIT_NAME 
                                                    from 
                                                        EPS_M_UNIT_MEASURE";
                                            $sql = $conn->query($query);
                                            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                                                $unitCd   = $row['UNIT_CD'];
                                                $unitName   = $row['UNIT_NAME'];
                                        ?>
                                            <option value="<?php echo $unitCd;?>"><?php echo $unitName;?></option>
                                        <?php         
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <label class="control-label" for="price">Price : </label>
                                    <div class="dialog-controls">
                                        <input type="text" id="price" class="form-control input-align-right" maxlength="14" />
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <label class="control-label" for="qty">Qty : </label>
                                    <div class="dialog-controls">
                                        <input type="text" id="qty" class="form-control input-align-right" maxlength="6" readonly="true" />
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <label class="control-label" for="amount">Amount : </label>
                                    <div class="dialog-controls">
                                        <input type="text" id="amount" class="form-control input-align-right" readonly="true" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <div>
                                    <label class="control-label" for="remark">Remark : </label>
                                    <div class="controls">
                                        <input type="text" id="remark" class="form-control" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div> 
                <div style="margin: 6px;">
                <div class="alert alert-danger" id="dialog-mandatory-msg-1" style="display: none;">
                    <b>Mandatory!</b> Please fill all the field.
                </div>
                <div class="alert alert-danger" id="dialog-mandatory-msg-2" style="display: none;">
                    <b>Mandatory!</b> Please input value > 0.
                </div>
                <div class="alert alert-danger" id="dialog-mandatory-msg-3" style="display: none;">
                    <b>Mandatory!</b> Please check action Add or Edit.
                </div>
                <div class="alert alert-danger" id="dialog-mandatory-msg-4" style="display: none;">
                    <b>Mandatory!</b> Please fill Expense No. for Type "Expense".
                </div>
                <div class="alert alert-danger" id="dialog-mandatory-msg-5" style="display: none;">
                    <b>Mandatory!</b> Please fill RFI No. for Type "Investment".
                </div>
                <div class="alert alert-danger" id="dialog-mandatory-msg-6" style="display: none;">
                    <b>Mandatory!</b> Please check FA Code format (xx-xxxx-xx-xxx).
                </div>
                <div class="alert alert-danger" id="dialog-mandatory-msg-7" style="display: none;">
                    <strong>Format Error!</strong> Please check RFI No format (** xx-xxx for DNIA/DSIA and xxx-xx for HDI).
                </div>
                <div class="alert alert-danger" id="dialog-mandatory-msg-8" style="display: none;">
                    <strong>Format Error!</strong> Please remove special character (** i.e ||).
                </div>
                <div class="alert alert-danger" id="dialog-duplicate-msg" style="display: none">
                    <b>Duplicate!</b> Item Name already exist in PR Detail table.
                </div>
                <div class="alert alert-danger" id="dialog-undefined-msg" style="display: none">
                    <b>Undefined Error!</b> System Error occurs. Please report to system administrator.
                </div>
            </div>
            </div>   
        </form>
            
    </div>
</div>
<script src="../js/bootstrap.js"></script>

<script src="../js/jquery.form.js"></script>        
<script src="../js/validator/validator.js"></script>
<script type="text/javascript" src="../js/ajaxupload.3.5.js" ></script>
<script>
        // validate a field on "blur" event, a 'select' on 'change' event & a '.reuired' classed multifield on 'keyup':
        $('form')
            .on('blur', 'input[required], input.optional, select.required', validator.checkField)
            .on('change', 'select.required', validator.checkField)
            .on('keypress', 'input[required][pattern]', validator.keypress);
            
        $('#alerts').change(function () {
            validator.defaults.alerts = (this.checked) ? false : true;
            if (this.checked)
                $('form .alert').remove();
        }).prop('checked', false);
        
        $(function(){
            var btnUpload   = $('#fileUpload');
            var status      = $('#status');
            
            new AjaxUpload(btnUpload, {
		action: '../db/PR_SESSION/UPDATE_ATTACHMENT_TEMP.php',
		name: 'uploadfile',
		onSubmit: function(file, ext){
                    var itemNameFile    = $.trim($('select#itemNameFile.form-control option:selected').text()).toUpperCase();
                    var itemCdFile      = $.trim($('#itemCdFile.form-control').val()).toUpperCase();
                 //alert(ext);
                    if(itemNameFile == "")
                    {
                        status.addClass('alert alert-danger');
                        status.text('Mandatory! Please select item name.');
                        return false;
                    }
                    if(!(ext && /^(jpg|png|jpeg|gif|pdf|doc|xls|ppt|docx|xlsx|pptx|tif)$/.test(ext))){ 
                        //extension is not allowed 
                        status.addClass('alert alert-danger');
                        status.text('Type Error! Only JPG, PNG, GIF, TIF, PDF, DOC, XLS, PPT, DOCX, XLSX, PPTX files are allowed.');
                        return false;
                    }
                    this.setData({ 
                        itemNameFile: itemNameFile,
                        itemCdFile: itemCdFile,
                        btnPrm: "ADD",
                        actionPrm: $.trim($("#actionFormHidden").val().toUpperCase())
                    }); 
                    //status.text('Uploading...');
		},
		onComplete: function(file, response){
                    var msg         = response.split('||');
                    //Add uploaded file to list
                    if(msg[0]==="Success_Add")
                    {
                        var countRowFile= msg[1];
                        var rowFile     = msg[2];
                        var getRowFile  = JSON.parse(rowFile);
                        //On completion clear the status
                        status.removeClass('alert alert-danger');
                        status.text('');
                        $('select#itemNameFile.form-control').val('');
                        $('#itemCdFile').val('');
                        $("#fileUpload").attr('disabled', true);
                        $("#prAttachmentTable tbody").html("");
                          
                        for(var i=0; i < countRowFile; i++)
                        {
                            var fileSeqHiddenVal= getRowFile[i].fileSeqHidden;
                            var itemCdFileVal   = getRowFile[i].itemCdFile; 
                            var itemNameFileVal = getRowFile[i].itemNameFile; 
                            var fileNameVal     = getRowFile[i].fileNameVal; 
                            var fileTypeVal     = getRowFile[i].fileTypeVal;
                            var fileSizeVal     = getRowFile[i].fileSizeVal; 
                            var addRowFile = "<tr>"
                                                +"<td style='text-align: right;'>"+fileSeqHiddenVal+"</td>"
                                                +"<td><input type='radio' name='radioFile' value="+fileSeqHiddenVal+"></td>"
                                                +"<td>"+itemCdFileVal+"</td>"
                                                +"<td>"+itemNameFileVal+"</td>"
                                                +"<td><a href='../db/Attachment/Temporary/<?php echo $currentDateYmd;?>/<?php echo $sUserId;?>/"+fileNameVal+"' target='_blank'>"+fileNameVal+"</a></td>"
                                                +"<td>"+fileTypeVal+"</td>"
                                                +"<td>"+fileSizeVal+"</td>"
                                                +"</tr>";
                            $("#prAttachmentTable tbody").append(addRowFile);
                        } 
                    }
                    else if(msg[0]==="FileNameError")
                    {
                        status.addClass('alert alert-danger');
                        status.text('File Name Error! Please upload file with name without containts characters # (hash).');
                    }
                    else if(msg[0]==="FileSizeError")
                    {
                        status.addClass('alert alert-danger');
                        status.text('File Size Error! Please upload file size less than 2MB.');
                    }
                    else if(msg[0]==="DuplicateFile")
                    {
                        status.addClass('alert alert-danger');
                        status.text('Duplicate! File name already exist. Please rename file name.');
                    }
                    else if(msg[0]==="SessionExpired")
                    {
                        status.addClass('alert alert-danger');
                        status.text('Session Expired! System Error occurs. Please report to system administrator.');
                        //window.location = "../ecom/WCOM010.php";
                    }
                    else
                    {
                        status.addClass('alert alert-danger');
                        status.text('Undefined Error! System Error occurs. Please report to system administrator.');
                    }
		}
            });	
	});
</script>
    </body>
</html>
