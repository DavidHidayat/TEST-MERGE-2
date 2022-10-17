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
                        });
                    }
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE VARIABLE ***********************************************
             * ========================================================================================================
             **/
            var myPrTransfer    = [];
            var winPrHeader;
            var winPrAttachment;
            //var urlPrHeader     = '<?php echo "../db/PR/READ_PR.php?param=prHeader&prStatus=1030" ?>';
            var urlPrAttachment = '<?php echo "../db/PR/READ_PR.php?param=prAttachment" ?>';
            var prNoGet;
            var inChargeKey;
            var plantKey;
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
             * =======================================
             * Define Function
             * =======================================
             **/
            function prTransfer(me){
                if(dsPrHeader.getCount() > 0){
                    if(myPrTransfer.length > 0){
                        me = 'Transfer PR by selection PR No.';
                    }else{
                        me = me + ' (by Procurement in charge)';
                    }
                    Ext.Msg.confirm('Confirm', 'Are you sure want to '+me+' ?', function(btn, text){
                        if (btn == 'yes'){
                            Ext.Ajax.request({
                                method: 'POST',
                                url: '../db/PR/UPDATE_PR.php?action=transferPr',
                                params: {
                                    prTransfer: Ext.encode(myPrTransfer)
                                },
                                success: function(response){
                                    myPrTransfer.splice(0, myPrTransfer.length);
                                    var msg = Ext.decode(response.responseText).msg.message;
                                    Ext.MessageBox.alert('Message', me+' succeed.');
                                    dsPrHeader.load();
                                }
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.alert('Message', 'There is no data to processed.');
                }
            }
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
             * Define Window Pr Header Search
             * =======================================
             **/
            function showWinSearchPrHeader(itemGrid,rowIndex,colIndex){
                resetPrHeader();
                showWinPrHeader();
                winPrHeader.setTitle('Search PR List');
            }
            /** 
             * =======================================
             * Define Window View PR Attachment
             * =======================================
             **/
            function showWinViewPrAttachment(prHeader,rowIndex,colIndex){
                prNoGet = dsPrHeader.getAt(rowIndex).get('PR_NO');
                dsPrAttachment.load({
                    params: {
                        prNo : prNoGet 
                    }
                });
                
                if (!winPrAttachment){	
                    winPrAttachment=Ext.widget('window',{
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
                                winPrAttachment.hide();
                            }
                        }]
                    });
                }
                winPrAttachment.show();
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
                inChargeForm.reset();
                plantForm.reset();   
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
                inChargeKey     = Ext.String.trim(inChargeForm.getValue());
                plantKey        = plantForm.getValue();
                if(dsPrHeader.currentPage != 1){
                    dsPrHeader.loadPage(1);
                }
                dsPrHeader.load({
                    params: {
                        start       : 0,
                        limit       : 15,
                        inChargeVal : inChargeKey,
                        plantVal    : plantKey
                    }
                });
                winPrHeader.hide(); 
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/
            Ext.define('PrHeader',{
                extend: 'Ext.data.Model',
                fields:[
                    {name: 'BU_CD'},
                    {name: 'ISSUED_DATE', type:'date', dateFormat:'d/m/Y'},
                    {name: 'REQUESTER_NAME'},
                    {name: 'PR_NO'},
                    {name: 'CHARGED_BU_CD'},
                    {name: 'SPECIAL_TYPE_ID'},
                    {name: 'PR_STATUS_NAME'},
                    {name: 'PROC_IN_CHARGE_NAME'},
                    {name: 'ATTACHMENT_COUNT'}
                ]
            });
            var dsPrHeader = 
                Ext.create('Ext.data.Store', {
                    model: 'PrHeader',
                    proxy:{
                        type: 'ajax',
                        url: '../db/PR/READ_PR.php?param=prHeader&prStatus=1030',
                        reader: {
                            type: 'json',
                            root: 'rows'
                        }
                    }//,
                    //autoLoad: true
                });
            dsPrHeader.load({
                params: {
                    start       : 0,
                    limit       : 15,
                    inChargeVal : inChargeKey,
                    plantVal    : plantKey
                }
            });
            Ext.define('PrAttachment',{
                extend: 'Ext.data.Model',
                fields:[
                    {name: 'ITEM_CD'},
                    {name: 'ITEM_NAME'},
                    {name: 'FILE_NAME'},
                    {name: 'FILE_TYPE'},
                    {name: 'FILE_SIZE'}
                ]
            });
            var dsPrAttachment = Ext.create('Ext.data.Store', {
                model: 'PrAttachment',
                proxy:{
                    type: 'ajax',
                    url: urlPrAttachment,
                    reader: {
                        type: 'json',
                        root: 'rows'
                    }
                }//,
                //autoLoad: true
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
            var inChargeForm = new Ext.form.TextField({
                fieldLabel: 'IN CHARGE',
                name: 'inChargeForm',
                listeners: {
                    change: function(field, newValue, oldValue){
                        field.setValue(newValue.toUpperCase());
                    }
                },
                flex: 2
            }); 
            var plantForm = Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'PLANT',
                name: 'plantForm',
                store: dsPlant,
                displayField: 'PLANT_NAME',
                valueField: 'PLANT_CD',
                queryMode: 'local',
                editable: true,
                flex: 2
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
                store: dsPrAttachment,
                columns: [{
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
                        var prNoVal = prNoGet;
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
             * Define PR Header Grid
             * =======================================
             **/
            var sm = Ext.create('Ext.selection.CheckboxModel',{
                checkOnly: true,
                //mode: 'single',
                //allowDeselect: true,
                listeners:{
                    selectionchange: function(selectionModel, selected, options){
                        //myPrTransfer.splice(0, myPrTransfer.length);
                        for(var j = 0; j < selected.length; j++){
                            var prNoVal = selected[j].data.PR_NO;
                            if (myPrTransfer.indexOf(prNoVal) == -1) {
                                myPrTransfer.push(prNoVal);
                            }
                        }
                    }
                } 
            });
            if (!Array.prototype.indexOf)
            {
                Array.prototype.indexOf = function(elt /*, from*/)
                {
                    var len = this.length >>> 0;

                    var from = Number(arguments[1]) || 0;
                    from = (from < 0)
                        ? Math.ceil(from)
                        : Math.floor(from);
                    if (from < 0)
                    from += len;

                    for (; from < len; from++)
                    {
                    if (from in this &&
                        this[from] === elt)
                        return from;
                    }
                    return -1;
                };
            }
            var prHeader = new Ext.grid.GridPanel({
                title: 'PR List ( *Status: Waiting Acceptance by Procurement )',
                autoScroll: true,
                border: false,
                store: dsPrHeader,
                columnLines: true,
                stripeRows: true,
                selModel: sm,
                columns :[{
                    header: 'ACTION',
                    align: 'center',
                    columns: [{
                        header: 'DOWNLOAD',
                        width: 50,
                        align: 'center',
                        xtype: 'actioncolumn',
                        tooltip: 'Download PR',
                        icon: '../images/download16.png',
                        handler: function(gridListPP,rowIndex){
                            var record = dsPrHeader.getAt(rowIndex);
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
                        width: 45,
                        align: 'center',
                        dataIndex: 'itemName',
                        xtype: 'actioncolumn',
                        icon: '../images/attach16.png',
                        tooltip: 'View Attachment',
                        getClass: function(v,meta,rec){
                            var countAttachment = rec.get('ATTACHMENT_COUNT');
                            if(countAttachment == 0){
                                return 'x-hide-display';
                            }
                        },
                        handler: function(dsPrHeader,rowIndex,colIndex){
                            showWinViewPrAttachment(prHeader,rowIndex,colIndex);
                        }
                    }]
                },{
                    header: 'COUNT',
                    dataIndex: 'ATTACHMENT_COUNT',
                    hidden: true,
                    hideable: false
                },{
                    header: 'BU CODE',
                    dataIndex: 'BU_CD',
                    align: 'center',
                    width: 60,
                    hidden: true,
                    hideable: false
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
                    width: 80
                },{
                    header: 'CHARGED BU',
                    dataIndex: 'CHARGED_BU_CD',
                    align: 'center',
                    width: 75
                },{
                    header: 'SPECIAL TYPE<br>(IT Equipment)',
                    dataIndex: 'SPECIAL_TYPE_ID',
                    align: 'center',
                    width: 120,
                    renderer: prSpecialTypeVal
                },{
                    header: 'STATUS',
                    dataIndex: 'PR_STATUS_NAME',
                    align: 'center',
                    width: 220,
                    renderer: prStatusVal
                },{
                    header: 'PROCUREMENT IN CHARGE',
                    dataIndex: 'PROC_IN_CHARGE_NAME',
                    align: 'center',
                    width: 150
                }],
                tbar: [{ 
                    text: 'Accept PR',
                    tooltip: 'Accept PR',
                    iconCls: 'transfer_button',
                    scale: 'medium',
                    handler: function(me){
                        prTransfer(me.getText());
                    }
                },{
                    text: 'Search PR',
                    tooltip: 'Search PR',
                    iconCls: 'search_button',
                    scale: 'medium',
                    handler: function(me){
                        showWinSearchPrHeader();
                    }
                }]
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
                    items: [inChargeForm,plantForm]
                }],
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
                    autoScroll: true,
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

