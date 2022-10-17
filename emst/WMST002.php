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
        <script type="text/javascript" src="../js/Store_Master.js"></script>
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
            var winItemMaster;
            var itemCdKey;
            var itemNameKey;
            var itemGroupKey;
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
             * Define Window Item Master
             * =======================================
             **/
            function showWinItemMaster(){
                if(!winItemMaster){
                    winItemMaster = Ext.widget('window',{
                        closeAction: 'hide',
                        width: 550,
                        height: 200,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [itemForm]
                    });
                }
                winItemMaster.show();
            }
            /** 
             * =======================================
             * Define Window Item Master Add
             * =======================================
             **/
            function showWindAddItemMaster(){
                resetItemMaster('Add');
                showWinItemMaster();
                winItemMaster.setTitle('Add Item Master');
                itemCdForm.setReadOnly(false);
            }
            /** 
             * =======================================
             * Define Window Item Master Edit
             * =======================================
             **/
            function showWindEditItemMaster(itemGrid,rowIndex,colIndex){
                resetItemMaster('Edit');
                showWinItemMaster();
                winItemMaster.setTitle('Edit Item Master');
                var valItemCd   = dsItemPaging.getAt(rowIndex).get('ITEM_CD');
                var valItemName = dsItemPaging.getAt(rowIndex).get('ITEM_NAME');
                var valItemGroup= dsItemPaging.getAt(rowIndex).get('ITEM_GROUP_CD');
                itemCdForm.setValue(valItemCd);
                itemNameForm.setValue(valItemName);
                itemGroupForm.setValue(valItemGroup);
                itemCdForm.setReadOnly(true);
            }
            /** 
             * =======================================
             * Define Window Item Master Search
             * =======================================
             **/
            function showWinSearchItemMaster(itemGrid,rowIndex,colIndex){
                resetItemMaster('Search');
                showWinItemMaster();
                winItemMaster.setTitle('Search Item Master');
                itemCdForm.setReadOnly(false);
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
            function resetItemMaster(action){
                // action Sear because substr (0,4).
                if(action == 'Add' || action == 'Cancel' || action == 'Search' || action == 'Sear'){
                    itemCdForm.reset();
                }
                itemNameForm.reset();
                itemGroupForm.reset();    
            }
            /** 
             * =======================================
             * Define Cancel 
             * =======================================
             **/
            function cancelItemMaster(){
                resetItemMaster('Cancel');
                winItemMaster.hide();    
            }
            /** 
             * =======================================
             * Define Save 
             * =======================================
             **/
            function saveItemMaster(){
                var action      = Ext.String.trim(winItemMaster.title.substr(0,4));
                if(Ext.String.trim(action) == 'Edit' || Ext.String.trim(action) == 'Add'){
                    if(itemForm.getForm().isValid()) {
                        var itemCd      = Ext.String.trim(itemCdForm.getValue());
                        var itemName    = Ext.String.trim(itemNameForm.getValue());
                        var itemGroupCd = itemGroupForm.getValue();
                        var actoinId    = '';
                        if(Ext.String.trim(action)=='Edit'){
                            actoinId = '2';
                        }else{
                            if(Ext.String.trim(action)=='Add'){
                                var indexItemCd = itemGrid.getStore().findExact('ITEM_CD',itemCd);
                                if(itemGrid.store.totalCount==0){
                                    actoinId = '2';
                                }else{
                                    if(indexItemCd == -1){
                                        actoinId = '2';
                                    }else{
                                        actoinId = '1';
                                        Ext.MessageBox.alert('Message','Item code already exists in Item Master.');
                                    }
                                }
                            }
                        }
                        if(actoinId == '2'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_ITEM.php?action='+action+'Item',
                                params:{
                                    itemCdVal       : itemCd,
                                    itemNameVal     : itemName,
                                    itemGroupCdVal  : itemGroupCd
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Item Master succeed.');
                                    resetItemMaster(action);
                                    winItemMaster.hide(); 
                                    if(dsItemPaging.currentPage != 1){
                                        dsItemPaging.loadPage(1);
                                    }
                                    dsItemPaging.load({
                                        params: {
                                            start       : 0, 
                                            limit       : 15,
                                            itemCdVal   : itemCd
                                        }
                                    });
                                }
                            });     
                        }
                    }
                }else{
                    searchItemMaster();
                    winItemMaster.hide(); 
                }
            }
            /** 
             * =======================================
             * Define Delete 
             * =======================================
             **/
            function deleteItemMaster(itemGrid,rowIndex,colIndex){
                var action = 'Delete';
                var row = dsItemPaging.getAt(rowIndex);
                var rowItemCd = row.get('ITEM_CD');
                Ext.Msg.show({
                    title: 'Confirm',
                    msg: 'Do you confirm to delete this item ?',
                    icon: Ext.Msg.QUESTION,
                    buttons:Ext.MessageBox.YESNO,
                    fn: function(btn){
                        if(btn=='yes'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_ITEM.php?action='+action+'Item',
                                params: {
                                    itemCdVal       : rowItemCd
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Item Master succeed.');
                                    if(dsItemPaging.currentPage != 1){
                                        dsItemPaging.loadPage(1);
                                    }
                                    dsItemPaging.load({
                                        params: {
                                            start       : 0, 
                                            limit       : 15,
                                            itemCdVal   : rowItemCd
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
            function searchItemMaster(){
                itemCdKey      = Ext.String.trim(itemCdForm.getValue());
                itemNameKey    = Ext.String.trim(itemNameForm.getValue());
                itemGroupKey   = itemGroupForm.getValue();
                if(dsItemPaging.currentPage != 1){
                    dsItemPaging.loadPage(1);
                }
                dsItemPaging.load({
                    params: {
                        start       : 0, 
                        limit       : 15,
                        itemCdVal   : itemCdKey,
                        itemNameVal : itemNameKey,
                        itemGroupVal: itemGroupKey
                    }
                });
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/
            dsItemPaging.load({
                params: {
                    start       : 0, 
                    limit       : 15,
                    itemCdVal   : itemCdKey,
                    itemNameVal : itemNameKey,
                    itemGroupVal: itemGroupKey
                }
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
            var itemCdForm = new Ext.form.TextField({
                fieldLabel: 'Code',
                name: 'itemCd',
                maxLength: '5',
                allowBlank: false,
                uppercaseValue: true,
                flex: 2,
                listeners: {
                    change: function(field, newValue, oldValue){
                        field.setValue(newValue.toUpperCase());
                    }
                }
            });
            var itemNameForm = new Ext.form.TextField({
                fieldLabel: 'Name',
                name: 'itemName',
                maxLength: '200',
                allowBlank: false,
                flex: 1,
                listeners: {
                    change: function(field, newValue, oldValue){
                        field.setValue(newValue.toUpperCase());
                    }
                }
            });
            var itemGroupForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Group',
                name: 'itemGroup',
                store: dsItemGroup,
                displayField: 'ITEM_GROUP_CD',
                valueField: 'ITEM_GROUP_CD',
                queryMode: 'local',
                editable: false,
                allowBlank: false,
                flex: 2
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE GRID ***************************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define Item Master Grid
             * =======================================
             **/
            var itemGrid = new Ext.grid.GridPanel({
                title: 'Item Master',
                autoScroll: true,
                border: false,
                store: dsItemPaging,
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
                        width: 45,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/edit16.png',
                        tooltip: 'Edit Item',
                        handler: function(itemGrid,rowIndex,colIndex){
                            showWindEditItemMaster(itemGrid,rowIndex,colIndex);
                        }
                    }/*,{
                        header: 'DELETE',
                        width: 45,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/delete16.png',
                        tooltip: 'Delete Item',
                        handler: function(itemGrid,rowIndex,colIndex){
                            deleteItemMaster(itemGrid,rowIndex,colIndex);
                        }
                    }*/]
                },{
                    header: 'ITEM',
                    columns: [{
                        header: 'CODE',
                        dataIndex: 'ITEM_CD',
                        align: 'center',
                        width: 75
                    },{
                        header: 'NAME ( Maker, Part No, Spec, Size, Color etc )',
                        dataIndex: 'ITEM_NAME',
                        width: 370
                    },{
                        header: 'GROUP',
                        dataIndex: 'ITEM_GROUP_CD'
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
                    store: dsItemPaging,
                    displayInfo: true,
                    displayMsg:'View {0}-{1} dari {2}',
                    EmptyMsg: "No data to display",
                    listeners: {
                        beforechange: function (paging, params) {
                            paging.store.proxy.extraParams.itemCdVal    = itemCdKey;
                            paging.store.proxy.extraParams.itemNameVal  = itemNameKey;
                            paging.store.proxy.extraParams.itemGroupVal = itemGroupKey;
                        }
                    }
                }),
                tbar: [{
                    text: 'Add',
                    handler: function(){
                        showWindAddItemMaster();
                    }
                },{
                    text: 'Search',
                    handler: function(){
                        showWinSearchItemMaster();
                    }
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            var itemForm = Ext.widget('form',{
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
                    items: [itemCdForm,itemGroupForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [itemNameForm]
                }],
                buttons: [{
                    text: 'Save',
                    handler: function(){
                        saveItemMaster();
                    }
                },{
                    text: 'Reset',
                    handler: function(){
                        var action = winItemMaster.title.substr(0,4);
                        resetItemMaster(Ext.String.trim(action));
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        cancelItemMaster();
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
                    items: [itemGrid]
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
