<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
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
            var winItemGroupMaster;
            var itemGroupCdKey;
            var itemGroupNameKey;
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
             * Define Window Item Group Master
             * =======================================
             **/
            function showWinItemGroupMaster(){
                if(!winItemGroupMaster){
                    winItemGroupMaster = Ext.widget('window',{
                        closeAction: 'hide',
                        width: 450,
                        height: 200,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [itemGroupForm]
                    });
                }
                winItemGroupMaster.show();
            }
            /** 
             * =======================================
             * Define Window Item Group Master Add
             * =======================================
             **/
            function showWindAddItemGroupMaster(){
                resetItemGroupMaster('Add');
                showWinItemGroupMaster();
                winItemGroupMaster.setTitle('Add Item Group Master');
                itemGroupCdForm.setReadOnly(false);
            }
            /** 
             * =======================================
             * Define Window Item Group Master Edit
             * =======================================
             **/
            function showWindEditItemGroupMaster(itemGrid,rowIndex,colIndex){
                resetItemGroupMaster('Edit');
                showWinItemGroupMaster();
                winItemGroupMaster.setTitle('Edit Item Group Master');
                var valItemGroupCd  = dsItemGroupPaging.getAt(rowIndex).get('ITEM_GROUP_CD');
                var valItemGroupName= dsItemGroupPaging.getAt(rowIndex).get('ITEM_GROUP_NAME');
                itemGroupCdForm.setValue(valItemGroupCd);
                itemGroupNameForm.setValue(valItemGroupName);
                itemGroupCdForm.setReadOnly(true);
            }
            /** 
             * =======================================
             * Define Window Item Group Master Search
             * =======================================
             **/
            function showWinSearchItemGroupMaster(itemGrid,rowIndex,colIndex){
                resetItemGroupMaster('Search');
                showWinItemGroupMaster();
                winItemGroupMaster.setTitle('Search Item Group Master');
                itemGroupCdForm.setReadOnly(false);
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
            function resetItemGroupMaster(action){
                // action Sear because substr (0,4).
                if(action == 'Add' || action == 'Cancel' || action == 'Search' || action == 'Sear'){
                    itemGroupCdForm.reset();
                }
                itemGroupNameForm.reset();
            }
            /** 
             * =======================================
             * Define Cancel 
             * =======================================
             **/
            function cancelItemGroupMaster(){
                resetItemGroupMaster('Cancel');
                winItemGroupMaster.hide();    
            }
            /** 
             * =======================================
             * Define Save 
             * =======================================
             **/
            function saveItemGroupMaster(){
                var action      = Ext.String.trim(winItemGroupMaster.title.substr(0,4));
                if(Ext.String.trim(action) == 'Edit' || Ext.String.trim(action) == 'Add'){
                    if(itemGroupForm.getForm().isValid()) {
                        var itemGroupCd      = Ext.String.trim(itemGroupCdForm.getValue());
                        var itemGroupName    = Ext.String.trim(itemGroupNameForm.getValue());
                        var actoinId    = '';
                        if(Ext.String.trim(action)=='Edit'){
                            actoinId = '2';
                        }else{
                            if(Ext.String.trim(action)=='Add'){
                                var indexItemGroupCd = itemGroupGrid.getStore().findExact('ITEM_GROUP_CD',itemGroupCd);
                                if(itemGroupGrid.store.totalCount==0){
                                    actoinId = '2';
                                }else{
                                    if(indexItemGroupCd == -1){
                                        actoinId = '2';
                                    }else{
                                        actoinId = '1';
                                        Ext.MessageBox.alert('Message','Item Group code already exists in Item Group Master.');
                                    }
                                }
                            }
                        }
                        if(actoinId == '2'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_ITEM_GROUP.php?action='+action,
                                params:{
                                    itemGroupCdVal       : itemGroupCd,
                                    itemGroupNameVal     : itemGroupName
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Item Group Master succeed.');
                                    resetItemGroupMaster(action);
                                    winItemGroupMaster.hide(); 
                                    if(dsItemGroupPaging.currentPage != 1){
                                        dsItemGroupPaging.loadPage(1);
                                    }
                                    dsItemGroupPaging.load({
                                        params: {
                                            start           : 0, 
                                            limit           : 15,
                                            itemGroupCdVal  : itemGroupCd
                                        }
                                    });
                                }
                            });     
                        }
                    }
                }else{
                    searchItemGroupMaster();
                    winItemGroupMaster.hide();
                }
            }
            /** 
             * =======================================
             * Define Delete 
             * =======================================
             **/
            function deleteItemGroupMaster(itemGrid,rowIndex,colIndex){
                var action = 'Delete';
                var row = dsItemGroupPaging.getAt(rowIndex);
                var rowItemGroupCd = row.get('ITEM_GROUP_CD');
                Ext.Msg.show({
                    title: 'Confirm',
                    msg: 'Do you confirm to delete this item ?',
                    icon: Ext.Msg.QUESTION,
                    buttons:Ext.MessageBox.YESNO,
                    fn: function(btn){
                        if(btn=='yes'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_ITEM_GROUP.php?action='+action,
                                params: {
                                    itemGroupCdVal       : rowItemGroupCd
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Item Group Master succeed.');
                                    if(dsItemGroupPaging.currentPage != 1){
                                        dsItemGroupPaging.loadPage(1);
                                    }
                                    dsItemGroupPaging.load({
                                        params: {
                                            start           : 0, 
                                            limit           : 15,
                                            itemGroupCdVal  : rowItemGroupCd
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
            function searchItemGroupMaster(){
                itemGroupCdKey  = Ext.String.trim(itemGroupCdForm.getValue());
                itemGroupNameKey= Ext.String.trim(itemGroupNameForm.getValue());
                if(dsItemGroupPaging.currentPage != 1){
                    dsItemGroupPaging.loadPage(1);
                }
                dsItemGroupPaging.load({
                    params: {
                        start           : 0, 
                        limit           : 15,
                        itemGroupCdVal  : itemGroupCdKey,
                        itemGroupNameVal: itemGroupNameKey
                    }
                });
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/
            dsItemGroupPaging.load({
                params: {
                    start           : 0, 
                    limit           : 15,
                    itemGroupCdVal  : itemGroupCdKey,
                    itemGroupNameVal: itemGroupNameKey
                }
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
            var itemGroupCdForm = new Ext.form.TextField({
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
            var itemGroupNameForm = new Ext.form.TextField({
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
            var itemGroupGrid = new Ext.grid.GridPanel({
                title: 'Item Group Master',
                store: dsItemGroupPaging,
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
                            showWindEditItemGroupMaster(itemGrid,rowIndex,colIndex);
                        }
                    }/*,{
                        header: 'DELETE',
                        width: 50,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/delete16.png',
                        tooltip: 'Delete Item',
                        handler: function(itemGrid,rowIndex,colIndex){
                            deleteItemGroupMaster(itemGrid,rowIndex,colIndex);
                        }
                    }*/]
                },{
                    header: 'ITEM GROUP',
                    columns: [{
                        header: 'CODE',
                        dataIndex: 'ITEM_GROUP_CD',
                        width: 105
                    },{
                        header: 'NAME',
                        dataIndex: 'ITEM_GROUP_NAME',
                        width: 300
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
                    store: dsItemGroupPaging,
                    displayInfo: true,
                    displayMsg:'View {0}-{1} dari {2}',
                    EmptyMsg: "No data to display",
                    listeners: {
                        beforechange: function (paging, params) {
                            paging.store.proxy.extraParams.itemGroupCdVal    = itemGroupCdKey;
                            paging.store.proxy.extraParams.itemGroupNameVal  = itemGroupNameKey;
                        }
                    }
                }),
                tbar: [{
                    text: 'Add',
                    handler: function(){
                        showWindAddItemGroupMaster();
                    }
                },{
                    text: 'Search',
                    handler: function(){
                        showWinSearchItemGroupMaster();
                    }
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            var itemGroupForm = Ext.widget('form',{
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
                    items: [itemGroupCdForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [itemGroupNameForm]
                }],
                buttons: [{
                    text: 'Save',
                    handler: function(){
                        saveItemGroupMaster();
                    }
                },{
                    text: 'Reset',
                    handler: function(){
                        var action = winItemGroupMaster.title.substr(0,4);
                        resetItemGroupMaster(Ext.String.trim(action));
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        cancelItemGroupMaster();
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
                    items: [itemGroupGrid]
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
