<?
session_start(); 
if(isset($_SESSION['sNPK']))
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
        <link rel="stylesheet" type="text/css" href="../css/eps.css"></link>
        <!--  Ext Js library -->
        <script type="text/javascript" src="../extjs/bootstrap.js"></script>
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Store_Master.js"></script>
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
                            window.location='../WCOM002.php'
                        }   
                    }]
                },{
                    xtype: 'buttongroup',
                    title: 'PR',
                    items: [{
                        xtype: 'button',
                        text: 'PR List',
                        tooltip: 'PR List with status PR Waiting Acceptance by Procurement',
                        handler: function(){
                            window.location='WEPO001.php'
                        }
                    },{
                        xtype: 'button',
                        text: 'Accepted PR List',
                        tooltip: 'PR List with status Accepted and already input to AS400',
                        handler: function(){
                            window.location='WEPO002.php'
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
            var winPrSearch;
            var winPrDetail;
            var winPrAttachmentView;
            var myAttachmentTemp    = [];
            var urlPrAttachmentView = '<?php echo "../db/PR/READ_PR.php?param=prAttachment" ?>';
            var urlPrTransfer       = '<?php echo "../db/PR/READ_PR.php?param=prTransfer" ?>';
            var prNoView;
            var itemNameView;
            var itemCdView;
            var prNoSearchKey;
            var requesterSearchKey;
            var procAcceptSearchKey;
            var procInChargeSearchKey;
            /** 
             * ========================================================================================================
             * **************************************** DEFINE INITIAL VALUE ******************************************
             * ========================================================================================================
             **/ 
            
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
             * Define Window View PR Attachment
             * =======================================
             **/
            function showWinViewPrAttachment(prTransfer,rowIndex,colIndex){
                prNoView = dsPrTransfer.getAt(rowIndex).get('PR_NO');
                itemCdView = dsPrTransfer.getAt(rowIndex).get('ITEM_CD');
                itemNameView = dsPrTransfer.getAt(rowIndex).get('ITEM_NAME');
                dsPrAttachmentView.load({
                    params: {
                        prNo : prNoView,
                        itemCd : itemCdView,
                        itemName: itemNameView
                    }
                });
                
                if (!winPrAttachmentView){	
                    winPrAttachmentView=Ext.widget('window',{
                        closeAction: 'hide',
                        title: 'PR Attachment',
                        width: 700,
                        height: 260,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [prAttachmentView],
                        buttons: [{
                            text: 'Close',
                            handler: function(){
                                winPrAttachmentView.hide();
                            }
                        }]
                    });
                }
                winPrAttachmentView.show();
            }
            /** 
             * =======================================
             * Define Window Pr Search
             * =======================================
             **/
            function showWinPrSearch(){
                resetPrSearch();
                if(!winPrSearch){
                    winPrSearch = Ext.widget('window',{
                        closeAction: 'hide',
                        title: 'Search PR Transfer',
                        width: 550,
                        height: 200,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [prSearchForm]
                    });
                }
                winPrSearch.show();
            }
            /** 
             * =======================================
             * Define Window PR Detail
             * =======================================
             **/
            function showWinPrDetail(){
                if (!winPrDetail){	
                    winPrDetail = Ext.widget('window',{
                        closeAction: 'hide',
                        width: 880,
                        height: 520,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        autoScroll: true,
                        items: [{
                            xtype: 'panel',
                            autoScroll: true,
                            width: 850,
                            height: 620,
                            items: [prDetail]
                        }]
                    });
                }
                winPrDetail.show();
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FUNCTION ***********************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define Reset Pr Search
             * =======================================
             **/
            function resetPrSearch(){
                prNoSearchForm.reset();
                requesterSearchForm.reset();
                procAcceptSearchForm.reset();
                procInChargeSearchForm.reset();
            }
            /** 
             * =======================================
             * Define Cancel Pr Search
             * =======================================
             **/
            function cancelPrSearch(){
                resetPrSearch();
                winPrSearch.hide();    
            }
            /** 
             * =======================================
             * Define Search Pr Search
             * =======================================
             **/
            function searchPrSearch(){
                prNoSearchKey       = Ext.String.trim(prNoSearchForm.getValue());
                requesterSearchKey  = Ext.String.trim(requesterSearchForm.getValue());
                procAcceptSearchKey = procAcceptSearchForm.getRawValue();
                procInChargeSearchKey= Ext.String.trim(procInChargeSearchForm.getValue());
                if(dsPrTransfer.currentPage != 1){
                    dsPrTransfer.loadPage(1);
                }
                dsPrTransfer.load({
                    params: {
                        start               : 0,
                        limit               : 15,
                        prNoSearchVal       : prNoSearchKey,
                        requesterSearchVal  : requesterSearchKey,
                        procAcceptSearchVal : procAcceptSearchKey,
                        procInChargeSearchVal: procInChargeSearchKey
                    }
                });
                winPrSearch.hide(); 
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/
            Ext.define('PrTransfer',{
                extend: 'Ext.data.Model',
                fields:[
                    {name: 'REQUESTER'},
                    {name: 'REQUESTER_NAME'},
                    {name: 'BU_CD'},
                    {name: 'REQ_BU_CD'},
                    {name: 'CHARGED_BU_CD'},
                    {name: 'PR_NO'},
                    {name: 'ITEM_CD'},
                    {name: 'ITEM_NAME'},
                    {name: 'DELIVERY_DATE',type:'date', dateFormat: 'd/m/Y'},
                    {name: 'QTY'},
                    {name: 'ITEM_PRICE'},
                    {name: 'AMOUNT'},
                    {name: 'ITEM_TYPE_CD'},
                    {name: 'ACCOUNT_NO'},
                    {name: 'RFI_NO'},
                    {name: 'UNIT_CD'},
                    {name: 'SUPPLIER_CD'},
                    {name: 'SUPPLIER_NAME'},
                    {name: 'PROC_ACCEPT_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
                    {name: 'ATTACHMENT_COUNT'},
                    {name: 'CREATE_BY'},
                    {name: 'CREATE_DATE'}
                ]
            });
            var dsPrTransfer = 
                Ext.create('Ext.data.Store', {
                    model: 'PrTransfer',
					groupField: 'PR_NO',
					sorters: ['PR_NO','ITEM_NAME'],
                    proxy:{
                        type: 'ajax',
                        url: urlPrTransfer,
                        reader: {
                            type: 'json',
                            root: 'rows'
                        }
                    }//,
                    //autoLoad: true
                });
            dsPrTransfer.load({
                params: {
                    start               : 0,
                    limit               : 15,
                    prNoSearchVal       : prNoSearchKey,
                    requesterSearchVal  : requesterSearchKey,
                    procAcceptSearchVal : procAcceptSearchKey,
                    procInChargeSearchVal: procInChargeSearchKey
                }
            });
            var dsPrAttachmentTemp = Ext.create('Ext.data.ArrayStore',{
                fields: [
                    {name: 'itemCd'},
                    {name: 'itemName'},
                    {name: 'itemNameOld'},
                    {name: 'fileName'},
                    {name: 'fileSize'},
                    {name: 'fileType'}
                ],
                data: myAttachmentTemp 
            });
            Ext.define('PrAttachmentView',{
                extend: 'Ext.data.Model',
                fields:[
                    {name: 'PR_NO'},
                    {name: 'ITEM_CD'},
                    {name: 'ITEM_NAME'},
                    {name: 'FILE_NAME'},
                    {name: 'FILE_TYPE'},
                    {name: 'FILE_SIZE'}
                ]
            });
            var dsPrAttachmentView = Ext.create('Ext.data.Store', {
                model: 'PrAttachmentView',
                proxy:{
                    type: 'ajax',
                    url: urlPrAttachmentView,
                    reader: {
                        type: 'json',
                        root: 'rows'
                    }
                }
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
            var itemStatus = Ext.create('Ext.form.RadioGroup',{
                fieldLabel: 'Item Status',
                id: 'itemStatus',
                name: 'itemStatus',
                allowBlank: false,
                items: [
                    {boxLabel: 'PO', name: 'itemStatus', inputValue: 'PO'},
                    {boxLabel: 'Outstanding PO', name: 'itemStatus', inputValue: 'POS'}
                ]
            });
            /** 
             * =======================================
             * Define PR Search Form Component
             * =======================================
             **/
            var prNoSearchForm = new Ext.form.TextField({
                fieldStyle: {
                    textTransform: "uppercase"
                },
                fieldLabel: 'PR NO.',
                name: 'prNoSearchForm',
                flex: 2
            });
            var requesterSearchForm = new Ext.form.TextField({
                fieldStyle: {
                    textTransform: "uppercase"
                },
                fieldLabel: 'NAME',
                name: 'requesterSearchForm',
                flex: 2 
            });
            var procAcceptSearchForm = new Ext.form.field.Date({
                fieldLabel: 'ACCEPTED DATE',
                name: 'procAcceptSearchForm',
                format: 'd/m/Y',
                flex: 2
            });
            var procInChargeSearchForm = new Ext.form.TextField({
                fieldStyle: {
                    textTransform: "uppercase"
                },
                fieldLabel: 'IN CHARGE',
                name: 'procInChargeSearchForm',
                flex: 2
            });
            /** 
             * =======================================
             * Define PR Header Form Component
             * =======================================
             **/
            var prNo = new Ext.form.TextField({
                fieldLabel: 'PR Number',
                name: 'prNo',
                readOnly: true,
                flex: 2
            });
            var prDate = new Ext.form.TextField({
                fieldLabel: 'PR Date',
                name: 'prDate',
                readOnly: true,
                flex: 2
            });
            var specialType = Ext.create('Ext.form.RadioGroup',{
                fieldLabel: 'Category',
                id: 'specialType',
                name: 'specialType',
                allowBlank: false,
                readOnly: true,
                items: [
                    {boxLabel: 'IT Equipment', name: 'specialType', inputValue: 'IT', readOnly: true},
                    {boxLabel: 'Non IT Equipment', name: 'specialType', inputValue: 'NIT', readOnly: true}
                ],
                flex: 3
            });
            var purpose = new Ext.form.field.TextArea({
                fieldLabel: 'Purpose',
                name: 'purpose',
                maxLength: '100',
                height: 35,
                readOnly : true,
                flex: 3
            });
            var requester = new Ext.form.TextField({
                fieldLabel: 'NPK',
                name: 'requester',
                readOnly: true,
                flex: 2
            });
            var requesterName = new Ext.form.TextField({
                fieldLabel: 'Name',
                name: 'requesterName',
                readOnly: true,
                flex: 3
            });
            var ext = new Ext.form.TextField({
                fieldLabel: 'Ext No',
                name: 'ext',
                readOnly: true,
                flex: 2
            });
             var plant=new Ext.form.TextField({
                fieldLabel: 'Plant',
                name: 'plant',
                readOnly: true,
                flex: 2
            });
            var plantCd=new Ext.form.TextField({
                name: 'plantCd',
                hidden: true
            });
            var company=new Ext.form.TextField({
                fieldLabel: 'Company',
                name: 'company',
                readOnly: true,
                flex: 3
            });
            var companyCd=new Ext.form.TextField({
                name: 'companyCd',
                hidden: true
            });
            var buCd=new Ext.form.TextField({
                fieldLabel: 'BU Code',
                name: 'buCd',
                readOnly: true,
                flex: 2
            });
            var sectionCd=new Ext.form.TextField({
                name: 'sectionCd',
                hidden: true
            });
            var prIssuer = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Issuer BU',
                name: 'prIssuer',
                store: dsBuCode,
                displayField: 'BU_CD_NAME',
                valueField: 'BU_CD',
                queryMode: 'local',
                editable: false,
                allowBlank: false,
                readOnly: true,
                flex: 2
            });
            var prCharged = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Charged BU',
                name: 'prCharged',
                store: dsBuCode,
                displayField: 'BU_CD_NAME',
                valueField: 'BU_CD',
                queryMode: 'local',
                editable: false,
                allowBlank: false,
                flex: 2
            });
            /** 
             * =======================================
             * Define PR Detail Form Component
             * =======================================
             **/
            var itemTypeForm = Ext.create('Ext.form.RadioGroup',{
                fieldLabel: 'Item Type',
                allowBlank: false,
                defaults: {xtype: 'radio',name: 'itemTypeForm'},
                items:[ {boxLabel:'Expense', inputValue:'1'},
                        {boxLabel:'Investment', inputValue:'2'},
                        {boxLabel:'Inventory', inputValue:'3'}],
                flex: 2,
                listeners: {
                    change: function(){
                        var valItemType=itemTypeForm.items.get(0).getGroupValue();
                        if(valItemType=='1'){
                            accountNoForm.setVisible(true);
                            rfiNoForm.setVisible(false);
                            rfiNoForm.reset();
                        }else if(valItemType=='2'){
                            accountNoForm.setVisible(false);
                            rfiNoForm.setVisible(true);
                            accountNoForm.reset();
                        }else{
                            accountNoForm.setVisible(false);
                            rfiNoForm.setVisible(false);
                            rfiNoForm.reset();
                            accountNoForm.reset();
                        }
                    }
                }
            });
            var accountNoForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Expense',
                name: 'accountNo',
                store: dsAccount,
                valueField: 'ACCOUNT_NO',
                displayField: 'ACCOUNT_CD_NAME',
                queryMode: 'local',
                editable: true,
                typeAhead: true,
                forceSelection: true,
                hidden: true,
                flex: 2
            });
            var rfiNoForm=new Ext.form.TextField({
                fieldLabel: 'RFI',
                name: 'rfiNo',
                hidden: true
            });
            var itemCdForm = new Ext.form.TextField({
                fieldLabel: 'Item Code',
                name: 'itemCd',
                hidden: true
            });
            var itemNameOldForm = new Ext.form.TextField({
                fieldLabel: 'Item Name Old',
                name: 'itemNameNew',
                hidden: true
            });
            var itemNameForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Name',
                name: 'itemName',
                store: dsItem,
                displayField: 'ITEM_NAME',
                valueField: 'ITEM_NAME',
                queryMode: 'local',
                editable: true,
                hideTrigger: true,
                allowBlank: false,
                typeAhead: true,
                flex: 2,
                listeners: {
                    change: function(combo, record, index){
                        var valItemName = Ext.String.trim(combo.getRawValue());
                        if(valItemName != ''){
                            Ext.Ajax.request({
                                method: 'GET',
                                url: '../db/Master_Data/EPS_M_ITEM.php?action=searchItemPrice',
                                params: {
                                    itemName: valItemName
                                },
                                success: function(response,action){
                                    var msg=Ext.decode(response.responseText).msg.message;
                                    var obj=Ext.decode(response.responseText);
                                    var action=Ext.String.trim(winPrDetail.title.substr(0,4));
                                    unitCdForm.setReadOnly(false);
                                    priceForm.setReadOnly(false);
                                    supplierNameForm.setReadOnly(false);
                                    if(msg=='Exist'){
                                        var valItemCd       = obj.rows[0]['itemCd'];
                                        var valItemName     = obj.rows[0]['itemName'];
                                        var valUnitCd       = obj.rows[0]['unitCd'];
                                        var valPrice        = obj.rows[0]['price'];
                                        var valSupplierCd   = obj.rows[0]['supplierCd'];
                                        var valSupplierName = obj.rows[0]['supplierName'];
                                        var valCurrenctCd   = obj.rows[0]['currencyCd'];

                                        itemCdForm.setValue(valItemCd);
                                        unitCdForm.setValue(valUnitCd);
                                        priceForm.setValue(valPrice);
                                        supplierCdForm.setValue(valSupplierCd);
                                        supplierNameForm.setValue(valSupplierName);
                                        currencyCdForm.setValue(valCurrenctCd);
                                        unitCdForm.setReadOnly(true);
                                        priceForm.setReadOnly(true);
                                        supplierNameForm.setReadOnly(true);
                                    }else{
                                        if(action=='Add'){
                                            itemCdForm.reset();
                                            unitCdForm.reset();
                                            priceForm.reset();
                                            supplierCdForm.reset();
                                            supplierNameForm.reset();
                                        }
                                    }
                                    if(action=='Add'){
                                        itemNameOldForm.setValue(valItemName);
                                    }else{
                                        itemNameOldForm.setValue(itemNameGet);
                                    }
                                }
                            });
                        }
                        itemCdForm.reset();
                        unitCdForm.reset();
                        priceForm.reset();
                        supplierCdForm.reset();
                        supplierNameForm.reset();
                        unitCdForm.setReadOnly(false);
                        priceForm.setReadOnly(false);
                        supplierNameForm.setReadOnly(false);
                    }
                }
            });
            var unitCdForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'U M',
                name: 'unitCd',
                store: dsUnit,
                valueField: 'UNIT_CD',
                displayField: 'UNIT_NAME',
                queryMode: 'local',
                editable: true,
                typeAhead: true,
                forceSelection: true,
                allowBlank: false,
                flex: 2
            });
            var qtyForm = new Ext.form.TextField({
                fieldLabel: 'Qty',
                name: 'qty',
                maskRe: /\d/,
                fieldStyle: 'text-align: right;',
                allowBlank: false,
                flex: 2
            });
            var currencyCdForm = new Ext.form.TextField({
                fieldLabel: 'Currency',
                name: 'currency',
                readOnly: true,
                hidden: true,
                value: 'IDR'
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
            var deliveryDateForm = new Ext.form.field.Date({
                fieldLabel: 'Delivery Date',
                name: 'deliveryDate',
                disabledDays:[0],
                disabledDates: ["25/12/2014","26/12/2014","31/12/2014"],
                format: 'd/m/Y',
                minValue : new Date(),
                allowBlank: false,
                flex: 2
            });
            var supplierCdForm = new Ext.form.TextField({
                fieldLabel: 'Supplier Code',
                name: 'supplierCd',
                readOnly: true,
                flex: 2
            });
           var supplierNameForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Estimate Supplier',
                name: 'supplierName',
                store: dsSupplier,
                displayField: 'SUPPLIER_NAME',
                valueField: 'SUPPLIER_NAME',
                queryMode: 'local',
                editable: true,
                hideTrigger: true,
                typeAhead: true,
                flex: 3,
                listeners: {
                    change: function(combo, record, index){
                        var valSupplierName = combo.getRawValue();
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
                    }
                }
            });
            var remarkForm = new Ext.form.TextField({
                fieldLabel: 'Remark',
                name: 'remark'
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE GRID ***************************************************
             * ========================================================================================================
             **/ 
            /** 
             * =======================================
             * Define PR Attachment Grid - View
             * =======================================
             **/
            var prAttachmentView = Ext.create('Ext.grid.Panel', {
                frame: false,
                border: true,
                autoScroll: true,
                columnLines: true,
                height: 195,
                store: dsPrAttachmentView,
                columns: [{
                    header: 'PR NO',
                    align: 'center',
                    dataIndex: 'PR_NO',
                    hidden: true,
                    hideable: false
                },{
                    header: 'ITEM CODE',
                    width: 75,
                    align: 'center',
                    dataIndex: 'ITEM_CD',
                    hidden: true,
                    hideable: false
                },{
                    header: 'ITEM NAME',
                    align: 'center',
                    flex: 1,
                    dataIndex: 'ITEM_NAME'
                },{
                    header: 'ITEM NAME OLD',
                    align: 'center',
                    dataIndex: 'ITEM_NAME',
                    hidden: true,
                    hideable: false
                },{
                    header: 'FILE NAME',
                    align: 'center',
                    dataIndex: 'FILE_NAME',
                    flex: 1,
                    renderer: function (val, metaData, record){
                        var prNoVal = prNoView;
                        return '<a href="../db/Attachment/Fixed/'+prNoVal+'/'+val+'" target="_blank">'+val+'</a>';
                    }
                },{
                    header: 'SIZE',
                    width: 70,
                    align: 'center',
                    dataIndex: 'FILE_SIZE'
                },{
                    header: 'TYPE',
                    align: 'center',
                    dataIndex: 'FILE_TYPE',
                    flex: 1
                }]
            });
            /** 
             * =======================================
             * Define PR Attachment Grid 
             * =======================================
             **/
            var prAttachment = Ext.create('Ext.grid.Panel', {
                frame: false,
                border: true,
                autoScroll: true,
                columnLines: true,
                height: 215,
                store: dsPrAttachmentTemp,
                columns: [{
                    header: 'ITEM CODE',
                    width: 75,
                    align: 'center',
                    dataIndex: 'itemCd',
                    hidden: true,
                    hideable: false
                },{
                    header: 'ITEM NAME',
                    align: 'center',
                    dataIndex: 'itemName',
                    hidden: true,
                    hideable: false
                },{
                    header: 'ITEM NAME OLD',
                    align: 'center',
                    dataIndex: 'itemNameOld',
                    hidden: true,
                    hideable: false
                },{
                    header: 'FILE NAME',
                    align: 'center',
                    dataIndex: 'fileName',
                    flex: 1,
                    renderer: function (val, metaData, record){
                        var prNoVal = prNo.getValue();
                        return '<a href="../db/Attachment/Temporary/'+prNoVal+'-temp/'+val+'" target="_blank">'+val+'</a>';
                    }
                },{
                    header: 'SIZE',
                    width: 70,
                    align: 'center',
                    dataIndex: 'fileSize'
                },{
                    header: 'TYPE',
                    align: 'center',
                    dataIndex: 'fileType',
                    flex: 1
                }]
            });
            
            /** 
             * =======================================
             * Define PR Detail Grid
             * =======================================
             **/
            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 1
            });
			var groupingFeature = Ext.create ('Ext.grid.feature.Grouping',{
				groupHeaderTpl: 'PR NO: {name} ({rows.length} Item{[values.rows.length > 1? "s" : ""]})'
			});
            var prTransfer = new Ext.grid.GridPanel({
                title: 'PR List ( *Status: Accepted and already input to AS400 )',
                plugins: [cellEditing],
                autoScroll: true,
                border: false,
                store: dsPrTransfer,
                columnLines: true,
                stripeRows: true,
				features: [groupingFeature],
                columns :[{
                    header: 'NO',
                    width: 30,
                    align: 'right',
                    xtype: 'rownumberer',
                    sortable: true
                },{
                    header: 'ACTION',
                    align: 'center',
                    columns: [/*{
                        header: 'OPEN',
                        width: 50,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/edit16.png',
                        tooltip: 'Open PR',
                        handler: function(prTransfer,rowIndex,colIndex){
                            showWinEditPrDetail(prTransfer,rowIndex,colIndex);
                        }
                    },*/{
                        header: 'DOWNLOAD',
                        width: 50,
                        align: 'center',
                        xtype: 'actioncolumn',
                        tooltip: 'Download PR',
                        icon: '../images/download16.png',
                        handler: function(gridListPP,rowIndex){
                            var record = dsPrTransfer.getAt(rowIndex);
                            var prNo = record.get('PR_NO');
                            var buCd = record.get('BU_CD').substr(0,1);
                            if(buCd == 'H'){
                                window.open('../lib/pdf/PR_HDI.php?prNo='+prNo+'&userId='+<? echo $sNPK;?>);   
                            }else{
                                window.open('../lib/pdf/PR_DENSO.php?prNo='+prNo+'&userId='+<? echo $sNPK;?>);   
                            }
                        }
                    },{
                        header: 'VIEW',
                        width: 50,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/attach16.png',
                        tooltip: 'View Attachment',
                        getClass: function(v,meta,rec){
                            var countAttachment = rec.get('ATTACHMENT_COUNT');
                            if(countAttachment == 0){
                                return 'x-hide-display';
                            }
                        },
                        handler: function(dsPrTransfer,rowIndex,colIndex){
                            showWinViewPrAttachment(prTransfer,rowIndex,colIndex);
                        }
                    }]
                },{
                    header: 'COUNT',
                    dataIndex : 'ATTACHMENT_COUNT',
                    hidden: true,
                    hideable: false
                },{
                    header: 'NAME',
                    dataIndex: 'REQUESTER_NAME',
                    width: 150,
                    editor: {
                        readOnly: true
                    }   
                },{
                    header: 'BU',
                    columns: [{
                        header: 'ISSUER',
                        dataIndex: 'REQ_BU_CD',
                        align: 'center',
                        width: 60,
                        editor: {
                            readOnly: true
                        }
                    },{
                        header: 'CHARGED',
                        dataIndex: 'CHARGED_BU_CD',
                        align: 'center',
                        width: 60,
                        editor: {
                            readOnly: true
                        }
                    }]
                },{
                    header: 'PR NO',
                    dataIndex: 'PR_NO',
                    align: 'center',
                    width: 100,
                    editor: {
                        readOnly: true
                    }
                },{
                    header: 'ACCEPTED DATE',
                    dataIndex: 'PROC_ACCEPT_DATE',
                    align: 'center',
                    width: 150,
                    renderer: Ext.util.Format.dateRenderer('m/d/Y H:i:s A')
                },{
                    header: 'ITEM',
                    columns: [{
                        header: 'CODE',
                        dataIndex: 'ITEM_CD',
                        align: 'center',
                        width: 75,
                        editor: {
                            readOnly: true
                        }
                    },{
                        header: 'NAME ( Maker, Part No, Spec, Size, Color etc )',
                        dataIndex: 'ITEM_NAME',
                        width: 300,
                        editor: {
                            readOnly: true
                        }
                    }]
                },{
                    header: 'DELIVERY DATE',
                    dataIndex: 'DELIVERY_DATE',
                    renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                    align: 'center',
                    width: 95
                },{
                    header: 'ITEM TYPE CODE',
                    align: 'center',
                    dataIndex: 'ITEM_TYPE_CD',
                    hidden: true,
                    hideable: false
                },{
                    header: 'ITEM TYPE',
                    dataIndex: 'ITEM_TYPE_CD',
                    align: 'center',
                    width: 90,
                    renderer:  function jnsinvest(val){
                        if(val == '1'){
                            return 'Expense';
                        }else if(val == '2'){
                            return 'Investment';
                        }else if(val == '3'){
                            return 'Inventory';
                        }else{
                            return '';
                        }
                        return val;
                    }
                },{
                    header: 'RFI',
                    dataIndex: 'RFI_NO',
                    align: 'center',
                    width: 60,
                    editor: {
                        readOnly: true
                    }
                },{
                    header: 'EXPENSE',
                    dataIndex: 'ACCOUNT_NO',
                    align: 'center',
                    width: 70,
                    editor: {
                        readOnly: true
                    }
                },{
                    header: 'U M',
                    dataIndex: 'UNIT_CD',
                    align: 'center',
                    width: 60,
                    editor: {
                        readOnly: true
                    }
                },{
                    header: 'QTY',
                    dataIndex: 'QTY',
                    align: 'right',
                    width: 60,
                    renderer: Ext.util.Format.numberRenderer('0'),
                    editor: {
                        readOnly: true
                    }
                },{
                    header: 'USER REFERENCE (ESTIMATE)',
                    columns: [{
                        header: 'UNIT PRICE',
                        dataIndex: 'ITEM_PRICE',
                        align: 'right',
                        width: 80,
                        xtype: 'numbercolumn',
                        format: '0,000',
                        editor: {
                            readOnly: true
                        }
                    },{
                        header: 'SUPPLIER NAME',
                        dataIndex: 'SUPPLIER_NAME',
                        width: 200,
                        editor: {
                            readOnly: true
                        }
                    }]
                },{
                    header: 'AMOUNT',
                    dataIndex: 'AMOUNT',
                    align: 'right',
                    width: 80,
                    renderer: Ext.util.Format.numberRenderer('0,000/i'),
                    editor: {
                        readOnly: true
                    }
                },{
                    header: 'CREATE BY',
                    dataIndex: 'CREATE_BY',
                    align: 'center',
                    width: 150
                },{
                    header: 'CREATE DATE',
                    dataIndex: 'CREATE_DATE',
                    align: 'center',
                    width: 150
                }],
                tbar: [{
                    text: 'Search PR',
                    tooltip: 'Search PR',
                    iconCls: 'search_button',
                    scale: 'medium',
                    handler: function(me){
                        showWinPrSearch();
                    }
                }]
            });
             /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define PR Transfer Search Form 
             * =======================================
             **/
            var prSearchForm = Ext.widget('form',{
                border: false, 
                frame: true,
                bodyPadding: '2',
                height: 163,
                fieldDefaults: {
                    anchor: '100%',
                    labelWidth: 90,
                    msgTarget: 'side',
                    labelAlign: 'right'
                },
                items: [{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [prNoSearchForm,requesterSearchForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [procAcceptSearchForm,procInChargeSearchForm]
                }],
                buttons: [{
                    text: 'Save',
                    name: 'save',
                    handler: function(){
                        searchPrSearch();
                    }
                },{
                    text: 'Reset',
                    name: 'cancel',
                    handler: function(){
                        resetPrSearch();
                    }
                },{
                    text: 'Cancel',
                    name: 'cancel',
                    handler: function(){
                        cancelPrSearch();
                    }
                }]
            });
            /** 
             * =======================================
             * Define PR Detail Form 
             * =======================================
             **/
            var prDetail = Ext.widget('form',{
                border: false, 
                frame: true,
                bodyPadding: '2',
                height: 583,
                fieldDefaults: {
                    anchor: '100%',
                    labelWidth: 100,
                    msgTarget: 'side',
                    labelAlign: 'right'
                },
                items: [{
                    xtype: 'fieldset',
                    title: '<b>Item Status</b>',
                    height: 45,
                    items: [itemStatus]
                },{
                    xtype: 'fieldset',
                    title: '<b>PR Information</b>',
                    height: 85,
                    items: [{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [prNo,prDate,specialType]
                    },purpose]
                },{
                    xtype: 'fieldset',
                    title: '<b>Requester Information</b>',
                    height: 100,
                    items: [{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [requester,requesterName,ext]
                    },{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [plant,company,buCd]
                    },{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [prIssuer,prCharged]
                    }]
                },{
                    xtype: 'fieldset',
                    title: '<b>PR Item Description</b>',
                    height: 182,
                    items: [{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [itemTypeForm,accountNoForm,rfiNoForm]
                    },{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [itemCdForm,itemNameOldForm,itemNameForm]
                    },{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [unitCdForm,deliveryDateForm]
                    },{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [priceForm,qtyForm]
                        
                    },{
                        xtype: 'container',
                        layout: 'hbox',
                        items: [supplierCdForm,supplierNameForm]
                    },remarkForm]
                },{
                    xtype: 'fieldset',
                    title: '<b>Attachment</b>',
                    height: 248,
                    items: [prAttachment]
                }]
             });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE MISCELLANEOUS ******************************************
             * ========================================================================================================
             **/
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
                    items: [prTransfer]
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

