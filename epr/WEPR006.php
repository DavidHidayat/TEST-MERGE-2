<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
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
    $sSpecialType= $_SESSION['sSpecialType'];
    $sPrApproverBu= $_SESSION['sPrApproverBu'];
    $prNo       = $_GET['prNo'];
    if(
            $_SESSION['EPSAuthority'] != 'EPSDetailPrScreen')
    {       
    ?>
        <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
	document.location="../db/Login/Logout.php"; </script>
    <?
    }
}else{	
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
        <script type="text/javascript" src="../js/Common.js"></script>
        <script type="text/javascript" src="../js/Store_Master.js"></script>
        <script type="text/javascript" src="../js/epr/WEPR009.js"></script>
        <script>
        maximize();
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
                            window.location='../epr_/WEPR001.php'
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
            var winPrAttachment;
            var itemNameGet;
            var urlPrHeader     = '<?php echo "../db/PR/READ_PR.php?prNo=".$prNo."&param=prHeader" ?>';
            var urlPrDetail     = '<?php echo "../db/PR/READ_PR.php?prNo=".$prNo."&param=prDetail" ?>';
            var urlPrApprover;
            var urlPrApproverSpecial = '<?php echo "../db/PR/READ_PR.php?prNo=".$prNo."&param=prApproverSpecial" ?>';
            var urlPrAttachment = '<?php echo "../db/PR/READ_PR.php?prNo=".$prNo."&param=prAttachment" ?>';
			var prApproverBu    = '<?php echo $sPrApproverBu;?>'; 
            var prSpecialType   = '<?php echo $sSpecialType?>';
            
            if(Ext.String.trim(prApproverBu) != '3300' && Ext.String.trim(prApproverBu) != '3941' &&  prSpecialType == 'IT'){
                urlPrApprover   = '<?php echo "../db/PR/READ_PR.php?prNo=".$prNo."&param=prApproverDept" ?>'
            }else{
                urlPrApprover   = '<?php echo "../db/PR/READ_PR.php?prNo=".$prNo."&param=prApprover" ?>'
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE INITIAL VALUE ******************************************
             * ========================================================================================================
             **/ 
            /** 
             * =======================================
             * Define PR Header value
             * =======================================
             **/
            Ext.Ajax.request({
                url: urlPrHeader,
                success: function(response,opts,store){
                    var obj=Ext.decode(response.responseText);
                    var prNoVal         = obj.rows[0]['PR_NO'];
                    var issuedDateVal   = obj.rows[0]['ISSUED_DATE'];
                    var specialTypeVal  = obj.rows[0]['SPECIAL_TYPE_ID'];
                    var purposeVal      = Ext.String.trim(obj.rows[0]['PURPOSE']);
                    var requesterVal    = obj.rows[0]['REQUESTER'];
                    var requesterNameVal= obj.rows[0]['REQUESTER_NAME'];
                    var plantVal        = obj.rows[0]['PLANT_NAME'];
                    var plantCdVal      = obj.rows[0]['PLANT_CD'];
                    var companyVal      = obj.rows[0]['COMPANY_NAME'];
                    var companyCdVal    = obj.rows[0]['COMPANY_CD'];
                    var extNoVal        = Ext.String.trim(obj.rows[0]['EXT_NO']);
                    var buCdVal         = obj.rows[0]['BU_CD'];
                    var sectionCdVal    = obj.rows[0]['SECTION_CD'];
                    var prIssuerVal     = obj.rows[0]['REQ_BU_CD'];
                    var prStatusVal     = obj.rows[0]['PR_STATUS'];
                    var prChargedVal    = obj.rows[0]['CHARGED_BU_CD'];
                    
                    prNo.setValue(prNoVal);
                    prDate.setValue(issuedDateVal);
                    specialType.setValue({specialType:specialTypeVal});
                    purpose.setValue(purposeVal);
                    requester.setValue(requesterVal);
                    requesterName.setValue(requesterNameVal);
                    plant.setValue(plantVal);
                    plantCd.setValue(plantCdVal);
                    company.setValue(companyVal);
                    companyCd.setValue(companyCdVal);
                    ext.setValue(extNoVal);
                    buCd.setValue(buCdVal);
                    sectionCd.setValue(sectionCdVal);
                    prIssuer.setValue(prIssuerVal);
                    prCharged.setValue(prChargedVal);
                    
                    var npkLogin = '<?php echo $_SESSION['sNPK'];?>';
                    if(npkLogin == requesterVal && (prStatusVal == '1050' || prStatusVal == '1080' || prStatusVal == '1020') ){
                        Ext.getCmp('replicate-btn').enable(true);
                    }
                },
                failure: function(response, opts) {
                
                } 
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FUNCTION ***********************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Push Attachment Temporary
             * =======================================
             **/
            function pushPrAttachmentTemp(itemNameGet){
                dsPrAttachment.clearFilter();
                dsPrAttachment.load().filter('ITEM_NAME',itemNameGet);
            }
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
            function showWinViewPrAttachment(prDetailGrid,rowIndex,colIndex){
                itemNameGet = dsPrItem.getAt(rowIndex).get('ITEM_NAME');
                pushPrAttachmentTemp(itemNameGet);
                if (!winPrAttachment){	
                    winPrAttachment=Ext.widget('window',{
                        closeAction: 'hide',
                        title: 'PR Attachment',
                        width: 500,
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
             * Define Return PR
             * =======================================
             **/
            /** 
             * =======================================
             * Define Replicate PR
             * =======================================
             **/
            function replicatePr(me){
                Ext.MessageBox.confirm('Message', 'Do you want to '+me+' with the new PR No. ?<br>** <b><i>This function only replicate PR item<i><b>', function(btn, text){
                    if(btn=='yes'){
                        Ext.Ajax.request({
                            method: 'POST',
                            url: '../db/PR/EPS_T_PR_SEQUENCE.php?action=getPrNo&userId=<? echo $sUserId; ?>&buLogin=<? echo $sBuLogin; ?>',
                            success: function(response){
                                var newPrNo = Ext.decode(response.responseText).msg.newPrNo;
                                var oldPrNo = prNo.getValue();
                                window.location='WEPR011.php?prNo='+newPrNo+'&oldPrNo='+oldPrNo;
                            }
                        });
                    }
                });
            }
            function returnPr(me){
				window.location='../epr_/WEPR001.php';
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE STORE **************************************************
             * ========================================================================================================
             **/
            Ext.define('PrDetail',{
                extend: 'Ext.data.Model',
                fields:[
                    {name: 'ITEM_CD'},
                    {name: 'ITEM_NAME'},
                    {name: 'REMARK'},
                    {name: 'DELIVERY_DATE', type:'date', dateFormat: 'd/m/Y'},
                    {name: 'ITEM_TYPE_CD'},
                    {name: 'RFI_NO'},
                    {name: 'ACCOUNT_NO'},
                    {name: 'SUPPLIER_CD'},
                    {name: 'SUPPLIER_NAME'},
                    {name: 'UNIT_CD'},
                    {name: 'QTY'},
                    {name: 'CURRENCY_CD'},
                    {name: 'ITEM_PRICE'},
                    {name: 'AMOUNT'},
                    {name: 'ITEM_STATUS'},
                    {name: 'REASON_TO_REJECT_ITEM'},
                    {name: 'REJECT_ITEM_NAME_BY'},
                    {name: 'ATTACHMENT_ITEM_COUNT'}
                ]
            });
            var dsPrItem = Ext.create('Ext.data.Store', {
                model: 'PrDetail',
                proxy:{
                    type: 'ajax',
                    url: urlPrDetail,
                    reader: {
                        type: 'json',
                        root: 'rows'
                    }
                },
                autoLoad: true
            });
			
            Ext.define('PrApprover',{
                extend: 'Ext.data.Model',
                fields:[
                    {name: 'APPROVER_NO'},
                    {name: 'APPROVER_NAME'},
                    {name: 'APPROVAL_STATUS_NAME'},
                    {name: 'APPROVAL_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
                    {name: 'APPROVAL_REMARK'},
                    {name: 'DATE_OF_BYPASS'}
                ]
            });
            var dsPrApprover = Ext.create('Ext.data.Store', {
                model: 'PrApprover',
                proxy:{
                    type: 'ajax',
                    url: urlPrApprover,
                    reader: {
                        type: 'json',
                        root: 'rows'
                    }
                },
                autoLoad: true
            }); 
			
            Ext.define('PrApproverSpecial',{
                extend: 'Ext.data.Model',
                fields:[
                    {name: 'APPROVER_NO'},
                    {name: 'APPROVER_NAME'},
                    {name: 'APPROVAL_STATUS_NAME'},
                    {name: 'APPROVAL_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
                    {name: 'APPROVAL_REMARK'},
                    {name: 'DATE_OF_BYPASS'}
                ]
            });
            var dsPrApproverSpecial = Ext.create('Ext.data.Store', {
                model: 'PrApproverSpecial',
                proxy:{
                    type: 'ajax',
                    url: urlPrApproverSpecial,
                    reader: {
                        type: 'json',
                        root: 'rows'
                    }
                },
                autoLoad: true
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
                },
                autoLoad: true
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM COMPONENT *****************************************
             * ========================================================================================================
             **/
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
                items: [
                    {boxLabel: 'IT Equipment', name: 'specialType', inputValue: 'IT', readOnly: true},
                    {boxLabel: 'Non IT Equipment', name: 'specialType', inputValue: 'NIT', readOnly: true}
                ],
                flex: 3
            });
            var purpose = new Ext.form.field.TextArea({
                fieldLabel: 'Purpose',
                name: 'purpose',
                height: 35,
                readOnly: true,
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
                fieldLabel: 'Ext',
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
                readOnly: true,
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
                    dataIndex: 'ITEM_NAME',
                    hidden: true,
                    hideable: false
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
                        var prNoVal = prNo.getValue();
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
             * Define PR Detail Grid
             * =======================================
             **/
            var prDetailGrid = Ext.create ('Ext.grid.Panel',{
                columnLines: true,
                frame: true,
                height: 485,
                store: dsPrItem,
                region: 'center',
                title: 'Purchase Requisition Item',
                features: [{
                    ftype: 'summary'
                }],
                columns: [{
                    header: 'NO',
                    width: 25,
                    align: 'center',
                    xtype: 'rownumberer',
                    sortable: true
                },{
                    header: 'ACTION',
                    align: 'center',
                    columns: [{
                        header: 'ATTACH',
                        width: 48,
                        align: 'center',
                        dataIndex: 'itemName',
                        xtype: 'actioncolumn',
                        icon: '../images/attach16.png',
                        tooltip: 'View Attachment',
                        getClass: function(v,meta,rec){
                            var countAttachmentItem = rec.get('ATTACHMENT_ITEM_COUNT');
                            if(countAttachmentItem == 0){
                                return 'x-hide-display';
                            }
                        },
                        handler: function(prDetailGrid,rowIndex,colIndex){
                            showWinViewPrAttachment(prDetailGrid,rowIndex,colIndex);
                        }
                    }]
                },{
                    header: 'COUNT',
                    dataIndex : 'ATTACHMENT_ITEM_COUNT',
                    hidden: true,
                    hideable: false
                },{
					header: 'ITEM',
                    columns: [{
						header: 'STATUS',
						width: 60,
						align: 'center',
						dataIndex: 'ITEM_STATUS',
						//hidden: true,
						//hideable: false,
                        renderer: prItemStatusVal
					},{
						header: 'CODE',
						width: 55,
						align: 'center',
						dataIndex: 'ITEM_CD'
					},{
						header: 'NAME ( Maker, Part No, Spec, Size, Color etc )',
						width: 300,
						align: 'center',
						dataIndex: 'ITEM_NAME'
					}]
				},{
                    header: 'DUE DATE',
                    width: 72,
                    align: 'center',
                    dataIndex: 'DELIVERY_DATE',
                    renderer: Ext.util.Format.dateRenderer('d/m/Y')
                },{
                    header: 'TYPE',
                    width: 40,
                    align: 'center',
                    dataIndex: 'ITEM_TYPE_CD',
                    renderer:  function jnsinvest(val){
                        if(val == '1'){
                            return 'EXP';
                        }else if(val == '2'){
                            return 'RFI';
                        }else if(val == '3' || val == '4'){
                            return 'INV';
                        }else{
                            return '';
                        }
                        return val;
                    }
                },{
                    header: 'RFI',
                    width: 55,
                    align: 'center',
                    dataIndex: 'RFI_NO'
                },{
                    header: 'EXP',
                    width: 40,
                    align: 'center',
                    dataIndex: 'ACCOUNT_NO'
                },{
                    header: 'U M',
                    width: 40,
                    align: 'center',
                    dataIndex: 'UNIT_CD'
                },{
                    header: 'QTY',
                    width: 50,
                    align: 'right',
                    dataIndex: 'QTY'
                },{
                    header: 'USER REFERENCE (ESTIMATE)',
                    columns: [{
                        header: 'CURRENCY',
                        align: 'center',
                        width: 70,
                        dataIndex: 'CURRENCY_CD'
                    },{
                        header: 'UNIT PRICE',
                        width: 100,
                        align: 'right',
                        dataIndex: 'ITEM_PRICE',
                        xtype: 'numbercolumn',
                        format: '0,000'
                    }, {
                        header: 'SUPPLIER',
                        width: 220,
                        align: 'center',
                        dataIndex: 'SUPPLIER_NAME',
                        summaryType: 'count',
                        summaryRenderer: function(value, summaryData, dataIndex) {
                            return '<b>Total Amount</b>'
                        }
                    }]
                },{
                    header: 'AMOUNT',
                    align: 'right',
                    dataIndex: 'AMOUNT',
                    renderer: Ext.util.Format.numberRenderer('0,000/i'),
                    summaryType: function(records){
                        var total = 0, record;
                        for(var i=0; i<records.length;i++){
                            var record=records[i];
                            total += parseInt(record.get('AMOUNT'));
                        }
                        return Ext.util.Format.number(total,'0,000/i');
                    }
                },{
                    header: 'REMARK',
                    width: 200,
                    align: 'center',
                    dataIndex: 'REMARK'
                },{
                    header: 'REJECT INFORMATION',
                    columns: [{
                        header: 'REASON',
                        width: 200,
                        align: 'center',
                        dataIndex: 'REASON_TO_REJECT_ITEM'
                    },{
                        header: 'REJECT BY',
                        width: 160,
                        align: 'center',
                        dataIndex: 'REJECT_ITEM_NAME_BY'
                    }]
                }]
            });
            /** 
             * =======================================
             * Define PR Approver Grid
             * =======================================
             **/
            var prApprover = Ext.create ('Ext.grid.Panel',{
                columnLines: true,
                frame: true,
                height: 225,
                store: dsPrApprover,
                region: 'center',
                title: 'Approval Information',
                columns: [{
                    header: 'NO',
                    width: 40,
                    align: 'center',
                    dataIndex: 'APPROVER_NO'
                }, {
                    header: ' NAME',
                    width: 200,
                    dataIndex: 'APPROVER_NAME'
                },{
                    header: 'STATUS',
                    width: 130,
                    dataIndex: 'APPROVAL_STATUS_NAME',
                    renderer: approvalStatusVal
                },{
                    header: 'DATE',
                    columns: [{
                        header: 'APPROVAL DATE',
                        width: 150,
                        align: 'center',
                        renderer: Ext.util.Format.dateRenderer('m/d/Y H:i:s A'),
                        dataIndex: 'APPROVAL_DATE'
                    },{
                        header: 'DATE OF BY PASS',
                        width: 150,
                        align: 'center',
                        dataIndex: 'DATE_OF_BYPASS'
                    }]
                },{
                    header: 'REMARK',
                    width: 280,
                    dataIndex: 'APPROVAL_REMARK'
                }]
            });
            /** 
             * =======================================
             * Define PR Approver Special Grid
             * =======================================
             **/
            var prApproverSpecial = Ext.create ('Ext.grid.Panel',{
                columnLines: true,
                frame: true,
                hidden: true,
                height: 110,
                store: dsPrApproverSpecial,
                region: 'center',
                title: 'IS Approval Information',
                columns: [{
                    header: 'NO',
                    width: 25,
                    align: 'center',
                    xtype: 'rownumberer',
                    sortable: true
                },{
                    header: ' NAME',
                    width: 200,
                    dataIndex: 'APPROVER_NAME'
                },{
                    header: 'STATUS',
                    width: 130,
                    dataIndex: 'APPROVAL_STATUS_NAME',
                    renderer: approvalStatusVal
                },{
                    header: 'DATE',
                    columns: [{
                        header: 'APPROVAL DATE',
                        width: 150,
                        align: 'center',
                        renderer: Ext.util.Format.dateRenderer('m/d/Y H:i:s A'),
                        dataIndex: 'APPROVAL_DATE'
                    },{
                        header: 'DATE OF BY PASS',
                        width: 150,
                        align: 'center',
                        dataIndex: 'DATE_OF_BYPASS'
                    }]
                },{
                    header: 'REMARK',
                    width: 280,
                    dataIndex: 'APPROVAL_REMARK'
                }]
            });
            if(Ext.String.trim(prApproverBu) != '3300' && Ext.String.trim(prApproverBu) != '3941' &&  prSpecialType == 'IT'){
                prApproverSpecial.hidden=false;
            }
            /** 
             * ========================================================================================================
             * **************************************** DEFINE FORM ***************************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define PR Header Form 
             * =======================================
             **/
            var prHeader = Ext.create('Ext.form.Panel',{
                frame: true,
                bodyPadding: '5',
                margin: '1',
                height: 220,
                fieldDefaults: {
                    msgTarget: 'side',
                    anchor: '100%',
                    labelWidth: 145
                },
                items: [{
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
                }]
            });
            /** 
             * ========================================================================================================
             * **************************************** DEFINE MISCELLANEOUS ******************************************
             * ========================================================================================================
             **/
            /** 
             * =======================================
             * Define Toolbar Button
             * =======================================
             **/
            var tb = [{
                xtype: 'toolbar',
                id: 'tb',
                ui: 'light',
                dock: 'top',
                items: [{
                    text: 'Back',
                    tooltip: 'Back',
                    id: 'back-btn',
                    iconCls: 'cancel_button',
                    scale: 'medium',
                    handler: function(me){
                        returnPr(me.getText());
                    }
                },{
                    text: 'Replicate PR',
                    tooltip: 'Replicate PR',
                    id: 'replicate-btn',
                    iconCls: 'transfer_button',
                    scale: 'medium',
                    disabled: true,
                    handler: function(me){
                        replicatePr(me.getText());
                    }
                }]
            }];
            /** 
             * =======================================
             * Define Content
             * =======================================
             **/
            var panelCenter = Ext.create('Ext.form.Panel', {
                border: false,
                frame: true,
                title: 'Detail Purchase Requisition',
                fieldDefaults: {
                    msgTarget: 'side',
                    labelAlign: 'right',
                    labelWidth: 145
                },
                dockedItems: tb,
                items: [prHeader,prApprover,prApproverSpecial,prDetailGrid]
            });
            /** 
             * =======================================
             * Define Layout
             * =======================================
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
                    items: [panelCenter]
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