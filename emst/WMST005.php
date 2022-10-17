<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
if(isset($_SESSION['sUserId']))
{         
    $sUserId    = $_SESSION['sUserId'];
    if($sUserId != '')
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

        if($sRoleId == 'ROLE_03' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_06' || $sRoleId == 'ROLE_07')
        {
            
        }
        else
        {
        ?>
            <script language="javascript"> alert("Sorry, this page only can be accessed by Procurement.");
            document.location="../db/Login/Logout.php"; </script>
        <?php
        }
    }
    else
    {
    ?>
        <script language="javascript"> alert("Sorry, your session to EPS has expired. Please login again.");
        document.location="../db/Login/Logout.php"; </script>
    <?php
    }
}
else
{	
?>
    <script language="javascript"> alert("Sorry, you are has not login. Please login first.");
    document.location="../db/Login/Logout.php"; </script>
<?
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>EPS</title>
        <!--  CSS -->
        <link rel="stylesheet" type="text/css" href="../extjs/resources/css/ext-all.css"></link>
        <link rel="stylesheet" type="text/css" href="../css/eps.css"></link>
        <!--  Ext Js library -->
        <script type="text/javascript" src="../extjs/bootstrap.js"></script>
        <script type="text/javascript" src="../js/Store_Paging.js"></script>
        <script>
        if (Ext.BLANK_IMAGE_URL.substr(0, 5) != 'data:') {
            Ext.BLANK_IMAGE_URL = '../extjs/resources/images/default/s.gif';
        }
        Ext.QuickTips.init();
        var mainLayout = function(){
            var toolbarTop = new Ext.Toolbar ({
                id: 'toolbar', 
                items: [{
                    xtype: 'buttongroup',
                    title: 'Miscellaneous',
                    items: [{
                        xtype: 'button',
                        text: 'Main Screen',
                        handler: function(){
                            window.location='../ecom/WCOM002.php'
                        }   
                    }]
                },{
                    xtype: 'buttongroup',
                    title: 'Master',
                    items: [{
                        xtype: 'splitbutton',
                        text: 'Master Data',
                        menu: [{
                            text: 'Item',
                            handler: function(){
                                window.location='../emst_/WMST001.php'
                            }
                        },{
                            text: 'Item Group',
                            handler: function(){
                                window.location='../emst_/WMST003.php'
                            }
                        },{
                            text: 'Item Price',
                            handler: function(){
                                window.location='../emst_/WMST004.php'
                            }
                        },{
                            text: 'Approver',
                            menu: {
                                xtype: 'menu',
                                items: [{
                                    text: 'PR Approver',
                                    handler: function(){
                                        window.location='../emst_/WMST005.php'
                                    }
                                },{
                                    text: 'PR Procurement PIC',
                                    handler: function(){
                                        window.location='../emst_/WMST007.php'
                                    }
                                }]
                            }
                        },{
                            text: 'Supplier',
                            handler: function(){
                                window.location='../emst_/WMST006.php'
                            }
                        },{
                            text: 'Unit Measure',
                            handler: function(){
                                window.location='WMST008.php'
                            }
                        }]
                    },{
                        xtype: 'splitbutton',
                        text: 'Upload',
                        menu: [{
                            text: 'Register Item Price',
                            handler: function(){
                                window.location='WMST009.php'
                            }
                        },{
                            text: 'Update Item Price',
                            handler: function(){
                                window.location='WMST010.php'
                            }
                        }]
                    }]
                },'->',
                {
                    xtype: 'tbtext', //Logged is as:
                    text: '<h2>Welcome, <?php echo stripslashes(addslashes($sNama)); ?></h2>#USER ID: <?php echo $sUserId; ?> #BU: <?php echo $sBuLogin?>'
                },'-',{
                    xtype: 'button',
                    text: 'Logout',
                    handler:function(){  
                        Ext.Msg.confirm('Confirm', 'Do you want to Log out?', function(btn, text){
                            if (btn == 'yes'){
                                window.location='../db/Login/Logout.php';
                            }
                        })
                    }
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE VARIABLE ***********************************************
             * ========================================================================================================
             **/
            var winPrApproverMst;
            var buCdKey, buNameKey, approverNoKey, approverNpkKey, approverNameKey, approverLimitKey;
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FUNCTION ***********************************************
             * ========================================================================================================
             **/
             
            /** 
             * ========================================================================================================
             * **************************************** DEFINE WINDOW *************************************************
             * ========================================================================================================
             **/
             /** 
             * =======================================
             * Define Window Pr Header
             * =======================================
             **/
            function showWinPrApproverMst(){
                if(!winPrApproverMst){
                    winPrApproverMst = Ext.widget('window',{
                        closeAction: 'hide',
                        width: 550,
                        height: 200,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [prApproverMstForm]
                    });
                }
                winPrApproverMst.show();
            } 
            /** 
             * =======================================
             * Define Window Item Master Search
             * =======================================
             **/
            function showWinSearchApproverMst(prApproverMstGrid,rowIndex,colIndex){
                resetPrApproverMst();
                showWinPrApproverMst();
                winPrApproverMst.setTitle('Search PR Approver Master');
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FUNCTION ***********************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define Reset 
             * =======================================
             **/
            function resetPrApproverMst(){
                buCdForm.reset();
                buNameForm.reset();   
                approverNoForm.reset();
                approverNpkForm.reset();  
                approverNameForm.reset();  
                approverLimitForm.reset();
            }
            /** 
             * =======================================
             * Define Cancel 
             * =======================================
             **/
            function cancelPrApproverMst(){
                resetPrApproverMst();
                winPrApproverMst.hide();    
            }
            /** 
             * =======================================
             * Define Search var buCdKey, buNameKey, approverNoKey, approverNpkKey, approverNameKey, approverLimitKey
             * =======================================
             **/
            function searchPrApproverMst(){
                buCdKey         = Ext.String.trim(buCdForm.getValue());
                buNameKey       = Ext.String.trim(buNameForm.getValue());
                approverNoKey   = Ext.String.trim(approverNoForm.getValue());
                approverNpkKey  = Ext.String.trim(approverNpkForm.getValue());
                approverNameKey = Ext.String.trim(approverNameForm.getValue());
                approverLimitKey= Ext.String.trim(approverLimitForm.getValue());
                if(dsPrApproverMstPaging.currentPage != 1){
                    dsPrApproverMstPaging.loadPage(1);
                }
                dsPrApproverMstPaging.load({
                    params: {
                        buCdVal         : buCdKey,
                        buNameVal       : buNameKey,
                        approverNoVal   : approverNoKey,
                        approverNpkVal  : approverNpkKey,
                        approverNameVal : approverNameKey,
                        approverLimitVal: approverLimitKey
                    }
                });
                winPrApproverMst.hide(); 
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/ 
            /*dsPrApproverMstPaging.load({
                    params: {
                        buCdVal         : buCdKey,
                        buNameVal       : buNameKey,
                        approverNoVal   : approverNoKey,
                        approverNpkVal  : approverNpkKey,
                        approverNameVal : approverNameKey,
                        approverLimitVal: approverLimitKey
                    }
                });*/
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
            var buCdForm = new Ext.form.TextField({
                fieldLabel: 'BU CODE',
                name: 'buCdForm',
                maxLength: '5',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            }); 
            var buNameForm = new Ext.form.TextField({
                fieldLabel: 'BU NAME',
                name: 'buNameForm',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            }); 
            var approverNoForm = new Ext.form.TextField({
                fieldLabel: 'APPROVER NO',
                name: 'approverNoForm',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            }); 
            var approverNpkForm = new Ext.form.TextField({
                fieldLabel: 'APPROVER NPK',
                name: 'approverNpkForm',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            });
            var approverNameForm = new Ext.form.TextField({
                fieldLabel: 'APPROVER NAME',
                name: 'approverNameForm',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            });
            var approverLimitForm = new Ext.form.TextField({
                fieldLabel: 'APPROVER LIMIT',
                name: 'approverLimitForm',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE GRID ***************************************************
             * ========================================================================================================
             **/
            var groupingFeature = Ext.create ('Ext.grid.feature.Grouping',{
                groupHeaderTpl: 'BU CODE: {name} ({rows.length} Item{[values.rows.length > 1? "s" : ""]})'
            });
            /** 
             * =======================================
             * Define Pr Approver Master Grid
             * =======================================
             **/
            var PrApproverMstGrid = new Ext.grid.GridPanel({
                title: 'PR Approver Master',
                store: dsPrApproverMstPaging,
                features: [groupingFeature],
                autoScroll: true,
                border: false,
                columnLines: true,
                stripeRows: true,
                columns :[{
                    text: 'NO.',
                    width: 50,
                    sortable: true,
                    xtype: 'rownumberer'
                },{
                    header: 'BU',
                    columns: [{
                        header: 'CODE',
                        dataIndex: 'BU_CD'
                    },{
                        header: 'NAME',
                        dataIndex: 'BU_NAME',
                        width: 200
                    }]
                },{
                    header: 'APPROVER',
                    columns: [{
                        header: 'NO',
                        dataIndex: 'APPROVER_NO',
                        width: 75
                    },{
                        header: 'NPK',
                        dataIndex: 'NPK',
                        width: 100
                    },{
                        header: 'NAME',
                        dataIndex: 'APPROVER_NAME',
                        width: 150
                    },{
                        header: 'LIMIT AMOUNT',
                        dataIndex: 'LIMIT_AMOUNT',
                        width: 120,
                        align: 'right',
                        xtype: 'numbercolumn',
                        format: '0,000'
                    }]
                }],
                tbar: [{
                    text: 'Search',
                    tooltip: 'Search',
                    iconCls: 'search_button',
                    scale: 'medium',
                    handler: function(){
                        showWinSearchApproverMst();
                    }
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            var prApproverMstForm = Ext.widget('form',{
                border: false,
                frame: true,
                height: 163,
                fieldDefaults: {
                    anchor: '100%',
                    labelWidth: 70,
                    msgTarget: 'side',
                    labelAlign: 'right'
                },
                items: [{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [buCdForm,buNameForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [approverNoForm,approverNameForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [approverNpkForm,approverLimitForm]
                }],
                buttons: [{
                    text: 'Save',
                    handler: function(){
                        searchPrApproverMst();
                    }
                },{
                    text: 'Reset',
                    handler: function(){
                        resetPrApproverMst();
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        cancelPrApproverMst();
                    }
                }]
            });
            
            var mainView = new Ext.create('Ext.Viewport',{
                layout: 'border', 
                padding: '5',
                items: [{
                    region: 'north',
                    split:true, 
                    border:false, 
                    items: [toolbarTop]
                },{
                    region: 'center',
                    id: 'content', 
                    layout: 'fit',
                    items: [PrApproverMstGrid]
                }],
                renderTo: Ext.getBody()
            });
        }
        Ext.onReady(mainLayout);
        </script>
    </head>
    <body>
    </body>
</html>
