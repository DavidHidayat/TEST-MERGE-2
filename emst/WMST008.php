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
            var winUnitMaster;
            var unitCdKey;
            var unitNameKey;
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
             * Define Window Unit Measure Master
             * =======================================
             **/
            function showWinUnitMaster(){
                if(!winUnitMaster){
                    winUnitMaster = Ext.widget('window',{
                        closeAction: 'hide',
                        width: 450,
                        height: 200,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [unitForm]
                    });
                }
                winUnitMaster.show();
            }
            /** 
             * =======================================
             * Define Window Unit Measure Master Add
             * =======================================
             **/
            function showWindAddUnitMaster(){
                resetUnitMaster('Add');
                showWinUnitMaster();
                winUnitMaster.setTitle('Add Unit Measure Master');
                unitCdForm.setReadOnly(false);
            }
            /** 
             * =======================================
             * Define Window Unit Measure Master Edit
             * =======================================
             **/
            function showWindEditUnitMaster(itemGrid,rowIndex,colIndex){
                resetUnitMaster('Edit');
                showWinUnitMaster();
                winUnitMaster.setTitle('Edit Unit Measure Master');
                var valUnitCd  = dsUnitMeasurePaging.getAt(rowIndex).get('UNIT_CD');
                var valUnitName= dsUnitMeasurePaging.getAt(rowIndex).get('UNIT_NAME');
                unitCdForm.setValue(valUnitCd);
                unitNameForm.setValue(valUnitName);
                unitCdForm.setReadOnly(true);
            }
            /** 
             * =======================================
             * Define Window Unit Measure Master Search
             * =======================================
             **/
            function showWinSearchUnitMaster(itemGrid,rowIndex,colIndex){
                resetUnitMaster('Search');
                showWinUnitMaster();
                winUnitMaster.setTitle('Search Unit Measure Master');
                unitCdForm.setReadOnly(false);
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
            function resetUnitMaster(action){
                // action Sear because substr (0,4).
                if(action == 'Add' || action == 'Cancel' || action == 'Search' || action == 'Sear'){
                    unitCdForm.reset();
                }
                unitNameForm.reset();
            }
            /** 
             * =======================================
             * Define Cancel 
             * =======================================
             **/
            function cancelUnitMaster(){
                resetUnitMaster('Cancel');
                winUnitMaster.hide();    
            }
            /** 
             * =======================================
             * Define Save 
             * =======================================
             **/
            function saveUnitMaster(){
                var action      = Ext.String.trim(winUnitMaster.title.substr(0,4));
                if(Ext.String.trim(action) == 'Edit' || Ext.String.trim(action) == 'Add'){
                    if(unitForm.getForm().isValid()) {
                        var unitCd      = Ext.String.trim(unitCdForm.getValue());
                        var unitName    = Ext.String.trim(unitNameForm.getValue());
                        var actoinId    = '';
                        if(Ext.String.trim(action)=='Edit'){
                            actoinId = '2';
                        }else{
                            if(Ext.String.trim(action)=='Add'){
                                var indexUnitCd = unitGrid.getStore().findExact('UNIT_CD',unitCd);
                                if(unitGrid.store.totalCount==0){
                                    actoinId = '2';
                                }else{
                                    if(indexUnitCd == -1){
                                        actoinId = '2';
                                    }else{
                                        actoinId = '1';
                                        Ext.MessageBox.alert('Message','Unit Measure code already exists in Unit Measure Master.');
                                    }
                                }
                            }
                        }
                        if(actoinId == '2'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_UNIT.php?action='+action,
                                params:{
                                    unitCdVal       : unitCd,
                                    unitNameVal     : unitName
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Unit Measure Master succeed.');
                                    resetUnitMaster(action);
                                    winUnitMaster.hide(); 
                                    if(dsUnitMeasurePaging.currentPage != 1){
                                        dsUnitMeasurePaging.loadPage(1);
                                    }
                                    dsUnitMeasurePaging.load({
                                        params: {
                                            start      : 0, 
                                            limit      : 15,
                                            unitCdVal  : unitCd
                                        }
                                    });
                                }
                            });     
                        }
                    }
                }else{
                    searchUnitMaster();
                    winUnitMaster.hide();
                }
            }
            /** 
             * =======================================
             * Define Delete 
             * =======================================
             **/
            function deleteUnitMaster(itemGrid,rowIndex,colIndex){
                var action = 'Delete';
                var row = dsUnitMeasurePaging.getAt(rowIndex);
                var rowUnitCd = row.get('UNIT_CD');
                Ext.Msg.show({
                    title: 'Confirm',
                    msg: 'Do you confirm to delete this item ?',
                    icon: Ext.Msg.QUESTION,
                    buttons:Ext.MessageBox.YESNO,
                    fn: function(btn){
                        if(btn=='yes'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_UNIT.php?action='+action,
                                params: {
                                    unitCdVal       : rowUnitCd
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Unit Measure Master succeed.');
                                    if(dsUnitMeasurePaging.currentPage != 1){
                                        dsUnitMeasurePaging.loadPage(1);
                                    }
                                    dsUnitMeasurePaging.load({
                                        params: {
                                            start      : 0, 
                                            limit      : 15,
                                            unitCdVal  : rowUnitCd
                                        }
                                    });
                                }
                            });
                        }
                    }
                });
            }
            /** 
             * =======================================
             * Define Search 
             * =======================================
             **/
            function searchUnitMaster(){
                unitCdKey  = Ext.String.trim(unitCdForm.getValue());
                unitNameKey= Ext.String.trim(unitNameForm.getValue());
                if(dsUnitMeasurePaging.currentPage != 1){
                    dsUnitMeasurePaging.loadPage(1);
                }
                dsUnitMeasurePaging.load({
                    params: {
                        start      : 0, 
                        limit      : 15,
                        unitCdVal  : unitCdKey,
                        unitNameVal: unitNameKey
                    }
                });
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/
            dsUnitMeasurePaging.load({
                params: {
                    start           : 0, 
                    limit           : 15,
                    unitCdVal  : unitCdKey,
                    unitNameVal: unitNameKey
                }
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
            var unitCdForm = new Ext.form.TextField({
                fieldLabel: 'Code',
                name: 'itemCd',
                maxLength: '20',
                allowBlank: false,
                uppercaseValue: true,
				fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            });
            var unitNameForm = new Ext.form.TextField({
                fieldLabel: 'Name',
                name: 'itemName',
                maxLength: '200',
                allowBlank: false,
				fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 3
            }); 
            /** 
             * ========================================================================================================
             * **************************************** DEFINE GRID ***************************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define Item Group Master Grid
             * =======================================
             **/
            var unitGrid = new Ext.grid.GridPanel({
                title: 'Unit Measure Master',
                store: dsUnitMeasurePaging,
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
                    header: 'ACTION',
                    columns: [{
                        header: 'EDIT',
                        width: 50,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/edit16.png',
                        tooltip: 'Edit Item',
                        handler: function(itemGrid,rowIndex,colIndex){
                            showWindEditUnitMaster(itemGrid,rowIndex,colIndex);
                        }
                    }/*,{
                        header: 'DELETE',
                        width: 50,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/delete16.png',
                        tooltip: 'Delete Item',
                        handler: function(itemGrid,rowIndex,colIndex){
                            deleteUnitMaster(itemGrid,rowIndex,colIndex);
                        }
                    }*/]
                },{
                    header: 'UNIT MEASURE',
                    columns: [{
                        header: 'CODE',
                        dataIndex: 'UNIT_CD',
                        align: 'center',
                        width: 75
                    },{
                        header: 'NAME',
                        dataIndex: 'UNIT_NAME',
                        width: 120
                    }]
                },{
                    header: 'CREATE DATE',
                    dataIndex: 'CREATE_DATE',
                    align: 'center',
                    renderer: Ext.util.Format.dateRenderer('m/d/Y H:i:s A'),
                    width: 150
                },{
                    header: 'CREATE BY',
                    dataIndex: 'CREATE_BY',
                    align: 'center',
                    width: 100
                },{
                    header: 'UPDATE DATE',
                    dataIndex: 'UPDATE_DATE',
                    align: 'center',
                    renderer: Ext.util.Format.dateRenderer('m/d/Y H:i:s A'),
                    width: 150
                },{
                    header: 'UPDATE BY',
                    dataIndex: 'UPDATE_BY',
                    align: 'center',
                    width: 100
                }],
                bbar: new Ext.PagingToolbar({
                    pageSize: 15,
                    store: dsUnitMeasurePaging,
                    displayInfo: true,
                    displayMsg:'View {0}-{1} dari {2}',
                    EmptyMsg: "No data to display",
                    listeners: {
                        beforechange: function (paging, params) {
                            paging.store.proxy.extraParams.unitCdVal    = unitCdKey;
                            paging.store.proxy.extraParams.unitNameVal  = unitNameKey;
                        }
                    }
                }),
                tbar: [{
                    text: 'Add',
                    handler: function(){
                        showWindAddUnitMaster();
                    }
                },{
                    text: 'Search',
                    handler: function(){
                        showWinSearchUnitMaster();
                    }
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            var unitForm = Ext.widget('form',{
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
                    items: [unitCdForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [unitNameForm]
                }],
                buttons: [{
                    text: 'Save',
                    handler: function(){
                        saveUnitMaster();
                    }
                },{
                    text: 'Reset',
                    handler: function(){
                        var action = winUnitMaster.title.substr(0,4);
                        resetUnitMaster(Ext.String.trim(action));
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        cancelUnitMaster();
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
                    items: [unitGrid]
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
