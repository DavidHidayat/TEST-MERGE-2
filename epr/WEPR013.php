<?php session_start(); 
if(isset($_SESSION['sNPK']))
{          
	/** Unset SESSION */
    unset($_SESSION['prStatusSession']);
	  
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
    $sUserId    = $_SESSION['sUserId'];
    $sBuLogin   = $_SESSION['sBuLogin'];
    $sUserType  = $_SESSION['sUserType'];
    
}else{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../index.php"; </script>
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
        <link rel="stylesheet" type="text/css" href="../css/eps-image.css"></link>
        <!--  Ext Js library -->
        <script type="text/javascript" src="../extjs/bootstrap.js"></script>
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Store_Master.js"></script>
        <script type="text/javascript" src="../js/Store_Paging.js"></script>
        <script type="text/javascript" src="../js/epr/WEPR009.js"></script>
        <script>
        if (Ext.BLANK_IMAGE_URL.substr(0, 5) != 'data:') {
            Ext.BLANK_IMAGE_URL = '../extjs/resources/images/default/s.gif';
        }
        Ext.QuickTips.init();
        var mainLayout = function(){
            var roleIdLogin = '<?php echo $_SESSION['sRoleId'];?>';
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
                    title: 'PR',
                    items: [{
                        xtype: 'button',
                        text: 'PR List',
                        handler: function(){
                            window.location='WEPR001.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'PR Waiting',
                        handler: function(){
                            window.location='../epr_/WEPR013.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'Create New PR',
                        handler: function(){
                            window.location='WEPR002.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'Upload PR',
                        handler: function(){
                            window.location='WEPR007.php'
                        }
                    }]
                },{
                    xtype: 'buttongroup',
                    title: 'Search',
                    items: [{
                        xtype: 'button',
                        text: 'PR Search',
                        handler: function(){
                            window.location='../epr_/WEPR090.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'PO Search',
                        handler: function(){
                            if(roleIdLogin == "ROLE_03" || roleIdLogin == "ROLE_08")
                            {
                                window.location='../epr_/WEPR091.php'
                            }
                            else
                            {
                                Ext.Msg.alert('Message','Sorry, you are not auhotrized to access this menu.');
                            }
                        }
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
            var winPrHeader;
            var prDateKey, prNoKey, requesterKey ,approverKey, prStatusKey;
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
            function showWinPrHeader(){
                if(!winPrHeader){
                    winPrHeader = Ext.widget('window',{
                        closeAction: 'hide',
                        width: 550,
                        height: 200,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [prHeaderForm]
                    });
                }
                winPrHeader.show();
            }
            /** 
             * =======================================
             * Define Window Item Master Search
             * =======================================
             **/
            function showWinSearchPrHeader(itemGrid,rowIndex,colIndex){
                resetPrHeader();
                showWinPrHeader();
                winPrHeader.setTitle('Search');
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
            function resetPrHeader(){
                prDateForm.reset();
                prNoForm.reset();   
                requesterForm.reset();
                prApproverForm.reset();  
                prStatusForm.reset();  
            }
            /** 
             * =======================================
             * Define Cancel 
             * =======================================
             **/
            function cancelPrHeader(){
                resetPrHeader();
                winPrHeader.hide();    
            }
            /** 
             * =======================================
             * Define Search 
             * =======================================
             **/
            function searchPrHeader(){
                prDateKey       = prDateForm.getRawValue();
                prNoKey         = Ext.String.trim(prNoForm.getValue());
                requesterKey    = Ext.String.trim(requesterForm.getValue());
                approverKey     = Ext.String.trim(prApproverForm.getValue());
                prStatusKey     = prStatusForm.getValue();
                if(dsPrWaiting.currentPage != 1){
                    dsPrWaiting.loadPage(1);
                }
                dsPrWaiting.load({
                    params: {
                        start       : 0,
                        limit       : 15,
                        prDateVal   : prDateKey,
                        prNoVal     : prNoKey,
                        requesterVal: requesterKey,
                        approverVal : approverKey,
                        prStatusVal : prStatusKey
                    }
                });
                winPrHeader.hide(); 
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/ 
            dsPrWaiting.load({
                params: {
                    start       : 0,
                    limit       : 15,
                    prDateVal   : prDateKey,
                    prNoVal     : prNoKey,
                    requesterVal: requesterKey,
                    approverVal : approverKey,
                    prStatusVal : prStatusKey
                }
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
            var prDateForm = new Ext.form.field.Date({
                fieldLabel: 'ISSUED DATE',
                name: 'prDateForm',
                format: 'd/m/Y',
                flex: 2
            }); 
            var requesterForm = new Ext.form.TextField({
                fieldStyle: {
                    textTransform: "uppercase"
                },
                fieldLabel: 'REQUESTER',
                name: 'requesterForm',
                flex: 2
            });
            var prNoForm = new Ext.form.TextField({
                fieldStyle: {
                    textTransform: "uppercase"
                },
                fieldLabel: 'PR NO.',
                name: 'prNoForm',
                flex: 2
            });
            var prStatusForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'STATUS',
                name: 'prStatusForm',
                store: dsAppStatus,
                displayField: 'APP_STATUS_NAME',
                valueField: 'APP_STATUS_CD',
                queryMode: 'local',
                editable: true
            });
            var prApproverForm = new Ext.form.TextField({
                fieldLabel: 'APPROVER',
                name: 'prApproverForm',
                listeners: {
                    change: function(field, newValue, oldValue){
                        field.setValue(newValue.toUpperCase());
                    }
                },
                flex: 2
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE GRID ***************************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define PR Header Grid
             * =======================================
             **/
            var prHeader = new Ext.grid.GridPanel({
                title: 'PR Waiting Approval',
                autoScroll: true,
                border: false,
                store: dsPrWaiting,
                columnLines: true,
                stripeRows: true,
                columns :[{
                    header: 'ACTION',
                    align: 'center',
                    columns: [{
                        header: 'OPEN',
                        width: 40,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/edit16.png',
                        tooltip: 'Open PR',
                        handler: function(gridListPP,rowIndex){
                            var pr = dsPrWaiting.getAt(rowIndex);
                            var criteriaIssuedDate  = prDateForm.getRawValue();
                            var criteriaRequester   = requesterForm.getValue();
                            var criteriaApprover    = prApproverForm.getValue();
                            var criteriaStatus      = prStatusForm.getValue();
                            var currentPage         = dsPrWaiting.currentPage;
                            window.location='../db/Login/Redirect_Login.php?prNo='+pr.get('PR_NO');
                        }
                    },/*{
                        header: 'DELETE',
                        width: 40,
                        align: 'center',
                        xtype: 'actioncolumn',
                        tooltip: 'Delete PR',
                        icon: '../images/delete16.png'
                    },*/{
                        header: 'DOWNLOAD',
                        width: 40,
                        align: 'center',
                        xtype: 'actioncolumn',
                        tooltip: 'Download PR',
                        icon: '../images/download16.png',
                        handler: function(gridListPP,rowIndex){
                            var record = dsPrWaiting.getAt(rowIndex);
                            var prNo = record.get('PR_NO');
                            var buCd = record.get('BU_CD').substr(0,1);
                            if(buCd == 'H'){
                                window.open('../lib/pdf/PR_HDI.php?prNo='+prNo);   
                            }else{
                                window.open('../lib/pdf/PR_TACI.php?prNo='+prNo);   
                            }
                        }
                    }]
                },{
                    header: 'BU CODE',
                    dataIndex: 'BU_CD',
                    align: 'center',
                    width: 60
                },{
                    header: 'ISSUED DATE',
                    dataIndex: 'ISSUED_DATE',
                    renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                    align: 'center',
                    width: 85
                },{
                    header: 'REQUESTER NAME',
                    dataIndex: 'REQUESTER_NAME',
                    align: 'center',
                    width: 150
                },{
                    header: 'PR NO.',
                    dataIndex: 'PR_NO',
                    align: 'center',
                    width: 90
                },{
                    header: 'STATUS',
                    dataIndex: 'PR_STATUS_NAME',
                    align: 'center',
                    width: 210,
                    renderer: prStatusVal
                },{
                    header: 'APPROVER',
                    dataIndex: 'APPROVER_NAME',
                    align: 'center',
                    width: 150
                },{
                    header: 'PROCUREMENT',
                    columns: [{
                        header: 'IN CHARGE',
                        dataIndex: 'PROC_IN_CHARGE_NAME',
                        align: 'center',
                        width: 150
                    },{
                        header: 'DATE',
                        dataIndex: 'PROC_ACCEPT_DATE',
                        align: 'center',
                        renderer: Ext.util.Format.dateRenderer('d/m/Y H:i:s A'),
                        width: 150
                    }]
                }],
                bbar: new Ext.PagingToolbar({
                    pageSize: 19,
                    store: dsPrWaiting,
                    displayInfo: true,
                    displayMsg:'View {0}-{1} dari {2}',
                    EmptyMsg: "No data to display",
                    listeners: { 
                        beforechange: function (paging, params) {
                            paging.store.proxy.extraParams.prDateVal    = prDateKey;
                            paging.store.proxy.extraParams.prNoVal      = prNoKey;
                            paging.store.proxy.extraParams.requesterVal = requesterKey;
                            paging.store.proxy.extraParams.approverVal  = approverKey;
                            paging.store.proxy.extraParams.prStatusVal  = prStatusKey;
                        }
                    }
                })/*,
                tbar: [{
                    text: 'Search',
                    tooltip: 'Search',
                    iconCls: 'search_button',
                    scale: 'medium',
                    handler: function(){
                        showWinSearchPrHeader();
                    }
                }]*/
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            var prHeaderForm = Ext.widget('form',{
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
                    items: [prDateForm,prNoForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [requesterForm,prApproverForm]
                },prStatusForm],
                buttons: [{
                    text: 'Save',
                    handler: function(){
                        searchPrHeader();
                    }
                },{
                    text: 'Reset',
                    handler: function(){
                        resetPrHeader();
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        cancelPrHeader();
                    }
                }]
            });
            
            var mainView = new Ext.create('Ext.Viewport',{
                layout: 'border', 
                padding: '5',
                items: [{
                    region: 'north',
                    split: true, 
                    border: false, 
                    items: [toolbarTop]
                },{
                    region: 'center',
                    id: 'content', 
                    layout: 'fit',
                    items: [prHeader]
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