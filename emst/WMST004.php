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
            var winItemPriceMaster;
            var itemCdKey;
            var itemNameKey;
            var unitKey;
            var priceKey;
            var effectiveDateKey;
            var supplierCdKey;
            var supplierNameKey;
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
             * Define Window Item Price Master
             * =======================================
             **/
             function showWinItemPriceMaster(){
                if(!winItemPriceMaster){
                    winItemPriceMaster = Ext.widget('window',{
                        closeAction: 'hide',
                        width: 600,
                        height: 350,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [itemPriceForm]
                    });
                }
                winItemPriceMaster.show();
            }
            /** 
             * =======================================
             * Define Window Item Price Master Add
             * =======================================
             **/
            function showWindAddItemPriceMaster(){
                resetItemPriceMaster('Add');
                showWinItemPriceMaster();
                winItemPriceMaster.setTitle('Add Item Price Master');
                itemCdForm.setReadOnly(true);
                supplierCdForm.setReadOnly(true);
            }
            /** 
             * =======================================
             * Define Window Item Master Search
             * =======================================
             **/
            function showWinSearchItemPriceMaster(itemGrid,rowIndex,colIndex){
                resetItemPriceMaster('Search');
                showWinItemPriceMaster();
                winItemPriceMaster.setTitle('Search Item Price Master');
                itemCdForm.setReadOnly(false);
                supplierCdForm.setReadOnly(false);
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
            function resetItemPriceMaster(action){
                // action Sear because substr (0,4).
                if(action == 'Add' || action == 'Cancel'){
                    itemCdForm.reset();
                    supplierCdForm.reset();  
                    itemCdForm.setReadOnly(true);
                    supplierCdForm.setReadOnly(true);
                }
                if(action == 'Search' || action == 'Sear'){
                    itemCdForm.reset();
                    supplierCdForm.reset();  
                    itemCdForm.setReadOnly(false);
                    supplierCdForm.setReadOnly(false);
                }
                itemNameForm.reset();
                unitForm.reset();   
                priceForm.reset();   
                effectiveDateForm.reset();
                supplierNameForm.reset();   
            }
            /** 
             * =======================================
             * Define Cancel 
             * =======================================
             **/
            function cancelItemPriceMaster(){
                resetItemPriceMaster('Cancel');
                winItemPriceMaster.hide();    
            }
            /** 
             * =======================================
             * Define Save 
             * =======================================
             **/
            function saveItemPriceMaster(){
                var action      = Ext.String.trim(winItemPriceMaster.title.substr(0,4));
                if(Ext.String.trim(action) == 'Edit' || Ext.String.trim(action) == 'Add'){
                    if(itemPriceForm.getForm().isValid()) {
                        var itemCd      = Ext.String.trim(itemCdForm.getValue());
                        var supplierCd  = Ext.String.trim(supplierCdForm.getValue());
                        var unitCd      = unitForm.getValue();
                        var price       = priceForm.getValue();
                        var effectiveDate= effectiveDateForm.getRawValue();
                        var itemCd_2;
                        var effectiveDate_2;
                        var actoinId = '';
                        dsItemPricePaging.each(function (me) {
                            itemCd_2= me.data.ITEM_CD.toLowerCase();
                            effectiveDate_2= me.data.EFFECTIVE_DATE;
                            if(itemCd == itemCd_2 && effectiveDate == effectiveDate_2){
                                Ext.MessageBox.alert('Message','Duplicate Item Price.');
                                actoinId='1';
                                return false;
                            }else{
                                actoinId='2';
                            }
                        });
                        if(actoinId == '2'){ 
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_ITEM.php?action='+action+'ItemPrice',
                                params:{
                                    itemCdVal       : itemCd,
                                    supplierCdVal   : supplierCd,
                                    unitCdVal       : unitCd,
                                    priceVal        : price,
                                    effectiveDateVal: effectiveDate
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Item Price Master succeed.');
                                    resetItemPriceMaster(action);
                                    winItemPriceMaster.hide(); 
                                    if(dsItemPricePaging.currentPage != 1){
                                        dsItemPricePaging.loadPage(1);
                                    }
                                    dsItemPricePaging.load({
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
                    searchItemPriceMaster();
                    winItemPriceMaster.hide(); 
                }
            }
            /** 
             * =======================================
             * Define Search 
             * =======================================
             **/
            function searchItemPriceMaster(){
                itemCdKey      = itemCdForm.getValue();
                itemNameKey    = itemNameForm.getValue();
                unitKey        = unitForm.getValue();
                priceKey       = priceForm.getValue();
                effectiveDateKey = effectiveDateForm.getRawValue();
                supplierCdKey    = supplierCdForm.getValue();
                supplierNameKey  = supplierNameForm.getValue();
                if(dsItemPricePaging.currentPage != 1){
                    dsItemPricePaging.loadPage(1);
                }
                dsItemPricePaging.load({
                    params: {
                        start       : 0, 
                        limit       : 15,
                        itemCdVal   : itemCdKey,
                        itemNameVal : itemNameKey,
                        unitVal     : unitKey,
                        priceVal    : priceKey,
                        effectiveDateVal : effectiveDateKey,
                        supplierCdVal    : supplierCdKey,
                        supplierNameVal  : supplierNameKey
                    }
                });
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/
            dsItemPricePaging.load({
                params: {
                    start       : 0, 
                    limit       : 15,
                    itemCdVal   : itemCdKey,
                    itemNameVal : itemNameKey,
                    unitVal     : unitKey,
                    priceVal    : priceKey,
                    effectiveDateVal : effectiveDateKey,
                    supplierCdVal    : supplierCdKey,
                    supplierNameVal  : supplierNameKey
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
            var itemNameForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Name',
                name: 'itemName',
                maxLength: '200',
                flex: 1,
                store: dsItem,
                displayField: 'ITEM_NAME',
                valueField: 'ITEM_NAME',
                queryMode: 'local',
                editable: true,
                hideTrigger: true,
                allowBlank: false,
                typeAhead: true,
                listeners: {
                    change: function(combo, record, index){
                        var valItemName = Ext.String.trim(combo.getRawValue());
                        var action = Ext.String.trim(winItemPriceMaster.title.substr(0,4));
                        if(action == 'Add'){
                            if(valItemName != ''){
                                itemCdForm.setReadOnly(true);
                                Ext.Ajax.request({
                                    method: 'GET',
                                    url: '../db/Master_Data/EPS_M_ITEM.php?action=detailItem',
                                    params: {
                                        itemName: valItemName
                                    },
                                    success: function(response,action){
                                        var msg     = Ext.decode(response.responseText).msg.message;
                                        var obj     = Ext.decode(response.responseText);
                                        if(msg=='Exist'){
                                            var valItemCd = obj.rows[0]['itemCd'];
                                            itemCdForm.setValue(valItemCd);
                                        }
                                    }
                                });
                            }
                        }else{
                            itemCdForm.setReadOnly(false);
                        }
                    }
                }
            });
            var unitForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'U M',
                name: 'unitCd',
                store: dsUnit,
                displayField: 'UNIT_NAME',
                valueField: 'UNIT_CD',
                queryMode: 'local',
                editable: true,
                typeAhead: true,
                forceSelection: true,
                allowBlank: false,
                flex: 2
            });
            var priceForm = Ext.create('Ext.form.NumberField',{
                fieldLabel: 'Estimate Price',
                name: 'price',
                maxLength: '9',
                maskRe: /\d/,
                fieldStyle: 'text-align: right;',
                allowBlank: false,
                hideTrigger:true,
                keyNavEnabled: false,
                mouseWheelEnabled: false,
                flex: 2
            });
            var effectiveDateForm = new Ext.form.field.Date({
                fieldLabel: 'Effective Date',
                name: 'effectiveDate',
                format: 'd/m/Y',
                allowBlank: false,
                flex: 2
            });
            var supplierCdForm = new Ext.form.TextField({
                fieldLabel: 'Code',
                name: 'suppierCd',
                maxLength: '5',
                allowBlank: false,
                uppercaseValue: true,
                flex: 1,
                listeners: {
                    change: function(field, newValue, oldValue){
                        field.setValue(newValue.toUpperCase());
                    }
                }
            });
            var supplierNameForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Name',
                name: 'supplierName',
                maxLength: '200',
                flex: 3,
                store: dsSupplier,
                displayField: 'SUPPLIER_NAME',
                valueField: 'SUPPLIER_NAME',
                queryMode: 'local',
                editable: true,
                hideTrigger: true,
                allowBlank: false,
                typeAhead: true,
               listeners: {
                    change: function(combo, record, index){
                        var valSupplierName = combo.getRawValue();
                        var action = Ext.String.trim(winItemPriceMaster.title.substr(0,4));
                        if(action == 'Add'){
                            supplierCdForm.setReadOnly(true);
                            Ext.Ajax.request({
                                method: 'GET',
                                url: '../db/Master_Data/EPS_M_SUPPLIER.php?action=detail',
                                params: {
                                    supplierName: valSupplierName
                                },
                                success: function(response,action){
                                    var msg=Ext.decode(response.responseText).msg.message;
                                    var obj=Ext.decode(response.responseText);
                                    if(msg=='Exist'){
                                        var valSupplierCd=obj.rows[0]['supplierCd'];
                                        supplierCdForm.setValue(valSupplierCd);
                                    }else{
                                        supplierCdForm.reset();
                                    }
                                }
                            });
                        }else{
                            supplierCdForm.setReadOnly(false);
                        }
                    }
                }
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE GRID ***************************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define Item Price Master Grid
             * =======================================
             **/
            var itemPriceGrid = new Ext.grid.GridPanel({
                title: 'Item Price Master',
                autoScroll: true,
                border: false,
                store: dsItemPricePaging,
                columnLines: true,
                stripeRows: true,
                columns :[{
                    text: 'NO.',
                    width: 50,
                    sortable: true,
                    xtype: 'rownumberer'
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
                        header: 'U M',
                        dataIndex: 'UNIT_CD',
                        width: 60
                    },{
                        header: 'PRICE (IDR)',
                        dataIndex: 'ITEM_PRICE',
                        align: 'right',
                        xtype: 'numbercolumn',
                        format: '0,000',
                        width: 90
                    },{
                        header: 'EFFECTIVE DATE FROM',
                        dataIndex: 'EFFECTIVE_DATE_FROM',
                        align: 'center',
                        width: 140,
                        renderer: Ext.util.Format.dateRenderer('d/m/Y')
                    }]
                },{
                    header: 'SUPPLIER',
                    columns: [{
                        header: 'CODE',
                        dataIndex: 'SUPPLIER_CD',
                        align: 'center',
                        width: 75
                    },{
                        header: 'NAME',
                        dataIndex: 'SUPPLIER_NAME',
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
                    store: dsItemPricePaging,
                    displayInfo: true,
                    displayMsg:'View {0}-{1} dari {2}',
                    EmptyMsg: "No data to display",
                    listeners: {
                        beforechange: function (paging, params) {
                            paging.store.proxy.extraParams.itemCdVal    = itemCdKey;
                            paging.store.proxy.extraParams.itemNameVal  = itemNameKey;
                            paging.store.proxy.extraParams.unitVal      = unitKey;
                            paging.store.proxy.extraParams.priceVal     = priceKey;
                            paging.store.proxy.extraParams.effectiveDateVal = effectiveDateKey;
                            paging.store.proxy.extraParams.supplierCdVal    = supplierCdKey;
                            paging.store.proxy.extraParams.supplierNameVal  = supplierNameKey;
                        }
                    }
                }),
                tbar: [{
                    text: 'Add',
                    handler: function(){
                        showWindAddItemPriceMaster();
                    }
                },{
                    text: 'Search',
                    handler: function(){
                        showWinSearchItemPriceMaster();
                    }
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            var itemPriceForm = Ext.widget('form',{
                border: false,
                frame: true,
                height: 313,
                fieldDefaults: {
                    anchor: '100%',
                    labelWidth: 70,
                    msgTarget: 'side',
                    labelAlign: 'right'
                },
                items: [{
                    xtype: 'fieldset',
                    title: '<b>Item</b>',
                    height: 130,
                    items: [{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [itemNameForm]
                    },{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [itemCdForm,priceForm]
                    },{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [unitForm,effectiveDateForm]
                    }]
                },{
                    xtype: 'fieldset',
                    title: '<b>Supplier</b>',
                    height: 60,
                    items: [{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [supplierCdForm,supplierNameForm]
                    }]
                }],
                buttons: [{
                    text: 'Save',
                    handler: function(){
                        saveItemPriceMaster();
                    }
                },{
                    text: 'Reset',
                    handler: function(){
                        var action = winItemPriceMaster.title.substr(0,4);
                        resetItemPriceMaster(Ext.String.trim(action));
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        cancelItemPriceMaster();
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
                    items: [itemPriceGrid]
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
