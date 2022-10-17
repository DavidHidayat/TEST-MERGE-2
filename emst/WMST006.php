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
            var winSupplierMaster;
            var supplierCdKey;
            var supplierNameKey;
            var contactKey;
            var emailKey;
            var phoneKey;
            var faxKey;
            var addressKey;
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
            function showWinSupplierMaster(){
                if(!winSupplierMaster){
                    winSupplierMaster = Ext.widget('window',{
                        closeAction: 'hide',
                        width: 600,
                        height: 300,
                        bodyPadding: '1',
                        resizable: false,
                        closable: false,
                        modal: true,
                        items: [supplierForm]
                    });
                }
                winSupplierMaster.show();
            }
            /** 
             * =======================================
             * Define Window Supplier Master Add
             * =======================================
             **/
            function showWindAddSupplierMaster(){
                resetSupplierMaster('Add');
                showWinSupplierMaster();
                winSupplierMaster.setTitle('Add Supplier Master');
                supplierCdForm.setReadOnly(false);
            }
            /** 
             * =======================================
             * Define Window Supplier Master Edit
             * =======================================
             **/
            function showWindEditSupplierMaster(itemGrid,rowIndex,colIndex){
                resetSupplierMaster('Edit');
                showWinSupplierMaster();
                winSupplierMaster.setTitle('Edit Supplier Master');
                var valSupplierCd   = dsSupplierPaging.getAt(rowIndex).get('SUPPLIER_CD');
                var valSupplierName = dsSupplierPaging.getAt(rowIndex).get('SUPPLIER_NAME');
                var valContact      = dsSupplierPaging.getAt(rowIndex).get('CONTACT');
                var valEmail        = dsSupplierPaging.getAt(rowIndex).get('EMAIL');
                var valPhone        = dsSupplierPaging.getAt(rowIndex).get('PHONE');
                var valFax          = dsSupplierPaging.getAt(rowIndex).get('FAX');
                var valAddress      = dsSupplierPaging.getAt(rowIndex).get('ADDRESS');
                supplierCdForm.setValue(valSupplierCd);
                supplierNameForm.setValue(valSupplierName);
                contactForm.setValue(valContact);
                emailForm.setValue(valEmail);
                phoneForm.setValue(valPhone);
                faxForm.setValue(valFax);
                addressForm.setValue(valAddress);
                supplierCdForm.setReadOnly(true);
            }
            /** 
             * =======================================
             * Define Window Supplier Master Search
             * =======================================
             **/
            function showWinSearchSupplierMaster(itemGrid,rowIndex,colIndex){
                resetSupplierMaster('Search');
                showWinSupplierMaster();
                winSupplierMaster.setTitle('Search Supplier Master');
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
            function resetSupplierMaster(action){
                // action Sear because substr (0,4).
                if(action == 'Add' || action == 'Cancel' || action == 'Search' || action == 'Sear'){
                    supplierCdForm.reset();
                }
                supplierNameForm.reset();
                contactForm.reset();
                emailForm.reset();
                phoneForm.reset();
                faxForm.reset();
                addressForm.reset();    
            }
            /** 
             * =======================================
             * Define Cancel 
             * =======================================
             **/
            function cancelSupplierMaster(){
                resetSupplierMaster('Cancel');
                winSupplierMaster.hide();    
            }
            /** 
             * =======================================
             * Define Save 
             * =======================================
             **/
            function saveSupplierMaster(){
                var action      = Ext.String.trim(winSupplierMaster.title.substr(0,4));
                if(Ext.String.trim(action) == 'Edit' || Ext.String.trim(action) == 'Add'){
                    if(supplierForm.getForm().isValid()) {
                        var supplierCd       = Ext.String.trim(supplierCdForm.getValue());
                        var supplierName     = Ext.String.trim(supplierNameForm.getValue());
                        var contact          = Ext.String.trim(contactForm.getValue());
                        var email            = Ext.String.trim(emailForm.getValue());
                        var phone            = Ext.String.trim(phoneForm.getValue());
                        var fax              = Ext.String.trim(faxForm.getValue());
                        var address          = Ext.String.trim(addressForm.getValue());
                        var actoinId         = '';
                        if(Ext.String.trim(action)=='Edit'){
                            actoinId = '2';
                        }else{
                            if(Ext.String.trim(action)=='Add'){
                                var indexSupplierCd = supplierGrid.getStore().findExact('SUPPLIER_CD',supplierCd);
                                if(supplierGrid.store.totalCount==0){
                                    actoinId = '2';
                                }else{
                                    if(indexSupplierCd == -1){
                                        actoinId = '2';
                                    }else{
                                        actoinId = '1';
                                        Ext.MessageBox.alert('Message','Supplier code already exists in Supplier Master.');
                                    }
                                }
                            }
                        }
                        if(actoinId == '2'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_SUPPLIER.php?action='+action,
                                params:{
                                    supplierCdVal       : supplierCd,
                                    supplierNameVal     : supplierName,
                                    contactCdVal        : contact,
                                    emailVal            : email,
                                    phoneVal            : phone,
                                    faxCdVal            : fax,
                                    addressCdVal        : address
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Supplier Master succeed.');
                                    resetSupplierMaster(action);
                                    winSupplierMaster.hide(); 
                                    if(dsSupplierPaging.currentPage != 1){
                                        dsSupplierPaging.loadPage(1);
                                    }
                                    dsSupplierPaging.load({
                                        params: {
                                            start           : 0, 
                                            limit           : 15,
                                            supplierCdVal   : supplierCd
                                        }
                                    });
                                }
                            });
                        }
                    }
                }else{
                    searchSupplierMaster();
                    winSupplierMaster.hide(); 
                }
            }
            /** 
             * =======================================
             * Define Delete 
             * =======================================
             **/
            function deleteSupplierMaster(itemGrid,rowIndex,colIndex){
                var action = 'Delete';
                var row = dsSupplierPaging.getAt(rowIndex);
                var rowSupplierCd = row.get('SUPPLIER_CD');
                Ext.Msg.show({
                    title: 'Confirm',
                    msg: 'Do you confirm to delete this item ?',
                    icon: Ext.Msg.QUESTION,
                    buttons:Ext.MessageBox.YESNO,
                    fn: function(btn){
                        if(btn=='yes'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/Master_Data/EPS_M_SUPPLIER.php?action='+action,
                                params: {
                                    supplierCdVal       : rowSupplierCd
                                },
                                success: function(){
                                    Ext.MessageBox.alert('Message',action+' Supplier Master succeed.');
                                    if(dsSupplierPaging.currentPage != 1){
                                        dsSupplierPaging.loadPage(1);
                                    }
                                    dsSupplierPaging.load({
                                        params: {
                                            start           : 0, 
                                            limit           : 15,
                                            supplierCdVal   : rowSupplierCd
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
            function searchSupplierMaster(){
                supplierCdKey       = Ext.String.trim(supplierCdForm.getValue());
                supplierNameKey     = Ext.String.trim(supplierNameForm.getValue());
                contactKey          = Ext.String.trim(contactForm.getValue());
                emailKey            = Ext.String.trim(emailForm.getValue());
                phoneKey            = Ext.String.trim(phoneForm.getValue());
                faxKey              = Ext.String.trim(faxForm.getValue());
                addressKey          = Ext.String.trim(addressForm.getValue());
                if(dsSupplierPaging.currentPage != 1){
                    dsSupplierPaging.loadPage(1);
                }
                dsSupplierPaging.load({
                    params: {
                        start           : 0, 
                        limit           : 15,
                        supplierCdVal   : supplierCdKey,
                        supplierNameVal : supplierNameKey,
                        contactVal      : contactKey,
                        emailVal        : emailKey,
                        phoneVal        : phoneKey,
                        faxVal          : faxKey,
                        addressVal      : addressKey
                    }
                });
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/
            dsSupplierPaging.load({
                params: {
                    start           : 0, 
                    limit           : 15,
                    supplierCdVal   : supplierCdKey,
                    supplierNameVal : supplierNameKey,
                    contactVal      : contactKey,
                    emailVal        : emailKey,
                    phoneVal        : phoneKey,
                    faxVal          : faxKey,
                    addressVal      : addressKey
                }
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
            var supplierCdForm = new Ext.form.TextField({
                fieldLabel: 'Code',
                name: 'supplierCd',
                maxLength: '5',
                allowBlank: false,
                uppercaseValue: true,
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 2
            });
            var supplierNameForm = new Ext.form.TextField({
                fieldLabel: 'Name',
                name: 'supplierName',
                maxLength: '300',
                allowBlank: false,
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            });
            var contactForm = new Ext.form.TextField({
                fieldLabel: 'Contact',
                name: 'contact',
                maxLength: '20',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 2
            });
            var emailForm = new Ext.form.TextField({
                fieldLabel: 'Email',
                name: 'email',
                maxLength: '100',
                vtype: 'email',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 1
            });
            var phoneForm = new Ext.form.TextField({
                fieldLabel: 'Phone',
                name: 'phone',
                maxLength: '50',
                align: 'right',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 2
            });
            var faxForm = new Ext.form.TextField({
                fieldLabel: 'Fax',
                name: 'fax',
                maxLength: '50',
                fieldStyle: {
                    textTransform: "uppercase"
                },
                flex: 2
            });
            var addressForm = new Ext.form.field.TextArea({
                fieldLabel: 'Address',
                name: 'address',
                maxLength: '300',
                flex: 1,
                height: 35,
                enterIsSpecial : true,
                fieldStyle: {
                    textTransform: "uppercase"
                },
                listeners: {
                    specialkey: function(f,e){  
                        if(e.getKey()==e.ENTER){  
                            e.stopEvent();
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
             * Define Supplier Master Grid
             * =======================================
             **/
            var supplierGrid = new Ext.grid.GridPanel({
                title: 'Supplier Master',
                autoScroll: true,
                border: false,
                store: dsSupplierPaging,
                columnLines: true,
                stripeRows: true,
                columns :[{
                    text: 'NO.',
                    width: 50,
                    sortable: true,
                    xtype: 'rownumberer'
                },/*{
                    header: 'ACTION',
                    columns: [{
                        header: 'EDIT',
                        width: 45,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/edit16.png',
                        tooltip: 'Edit Supplier',
                        handler: function(itemGrid,rowIndex,colIndex){
                            showWindEditSupplierMaster(itemGrid,rowIndex,colIndex);
                        }
                    },{
                        header: 'DELETE',
                        width: 45,
                        align: 'center',
                        xtype: 'actioncolumn',
                        icon: '../images/delete16.png',
                        tooltip: 'Delete Supplier',
                        handler: function(itemGrid,rowIndex,colIndex){
                            deleteSupplierMaster(itemGrid,rowIndex,colIndex);
                        }
                    }]
                },*/{
                    header: 'SUPPLIER',
                    columns: [{
                        header: 'CODE',
                        dataIndex: 'SUPPLIER_CD',
                        width: 75
                    },{
                        header: 'NAME',
                        dataIndex: 'SUPPLIER_NAME',
                        width: 300
                    },{
                        header: 'CUR',
                        dataIndex: 'CURRENCY_CD',
                        width: 50
                    },{
                        header: 'VAT/NON',
                        dataIndex: 'VAT',
                        width: 70
                    },{
                        header: 'CONTACT',
                        dataIndex: 'CONTACT',
                        width: 120
                    },{
                        header: 'EMAIL',
                        dataIndex: 'EMAIL',
                        width: 170
                    },{
                        header: 'CC',
                        dataIndex: 'EMAIL_CC',
                        width: 170
                    },{
                        header: 'PHONE',
                        dataIndex: 'PHONE',
                        width: 120
                    },{
                        header: 'FAX',
                        dataIndex: 'FAX',
                        width: 90
                    },{
                        header: 'ADDRESS',
                        dataIndex: 'ADDRESS',
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
                    store: dsSupplierPaging,
                    displayInfo: true,
                    displayMsg:'View {0}-{1} dari {2}',
                    EmptyMsg: "No data to display",
                    listeners: {
                        beforechange: function (paging, params) {
                            paging.store.proxy.extraParams.supplierCdVal    = supplierCdKey;
                            paging.store.proxy.extraParams.supplierNameVal  = supplierNameKey;
                            paging.store.proxy.extraParams.contactVal       = contactKey;
                            paging.store.proxy.extraParams.emailVal         = emailKey;
                            paging.store.proxy.extraParams.phoneVal         = phoneKey;
                            paging.store.proxy.extraParams.faxVal           = faxKey;
                            paging.store.proxy.extraParams.addressVal       = addressKey;
                        }
                    }
                }),
                tbar: [/*{
                    text: 'Add',
                    handler: function(){
                        showWindAddSupplierMaster();
                    }
                },*/{
                    text: 'Search',
                    handler: function(){
                        showWinSearchSupplierMaster();
                    }
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            var supplierForm = Ext.widget('form',{
                border: false,
                frame: true,
                height: 263,
                fieldDefaults: {
                    anchor: '100%',
                    labelWidth: 70,
                    msgTarget: 'side',
                    labelAlign: 'right'
                },
                items: [{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [supplierCdForm,contactForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [supplierNameForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [phoneForm,faxForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [emailForm]
                },{
                    xtype: 'container',
                    layout: 'hbox',
                    items: [addressForm]
                }],
                buttons: [{
                    text: 'Save',
                    handler: function(){
                        saveSupplierMaster();
                    }
                },{
                    text: 'Reset',
                    handler: function(){
                        var action = winSupplierMaster.title.substr(0,4);
                        resetSupplierMaster(Ext.String.trim(action));
                    }
                },{
                    text: 'Cancel',
                    handler: function(){
                        cancelSupplierMaster();
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
                    items: [supplierGrid]
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
