function searchPrItem(){
    Ext.QuickTips.init();
    
    /** 
     * ========================================================================================================
     * **************************************** DEFINE VARIABLE ***********************************************
     * ========================================================================================================
    **/
    var winPrSearch;
    var prDateKey,prNoKey,requesterKey,approverKey,prStatusKey,itemTypeKey,itemNameKey
        ,accountNoKey,rfiNoKey,unitCdKey,deliveryDateKey,supplierNameKey
        ,prIssuerKey,prChargedKey;
    
    /** 
     * ========================================================================================================
     * **************************************** DEFINE WINDOW *************************************************
     * ========================================================================================================
    **/
    /** 
     * =======================================
     * Define Window Pr Search
     * =======================================
    **/
    function showWinSearchPrSearch(){
        if(!winPrSearch){
            winPrSearch = Ext.widget('window',{
                title: 'Search',
                closeAction: 'hide',
                width: 650,
                height: 300,
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
     * ========================================================================================================
     * **************************************** DEFINE FUNCTION ***********************************************
     * ========================================================================================================
    **/
    /** 
     * =======================================
     * Define Reset 
     * =======================================
    **/
    function resetPrSearch(){
        prDateForm.reset();
        prNoForm.reset();   
        requesterForm.reset();
        prApproverForm.reset();  
        prStatusForm.reset();  
        itemTypeForm.reset();
        itemNameForm.reset();
        accountNoForm.reset();
        rfiNoForm.reset();
        unitCdForm.reset();   
        deliveryDateForm.reset();
        supplierNameForm.reset(); 
        prIssuerForm.reset();
        prChargedForm.reset(); 
    }
    /** 
     * =======================================
     * Define Cancel 
     * =======================================
    **/
    function cancelPrSearch(){
        resetPrSearch();
        winPrSearch.hide();    
    }
    /** 
     * =======================================
     * Define Search 
     * =======================================
    **/
    function searchPrSearch(){
        prDateKey       = prDateForm.getRawValue();
        prNoKey         = Ext.String.trim(prNoForm.getValue());
        requesterKey    = Ext.String.trim(requesterForm.getValue());
        approverKey     = Ext.String.trim(prApproverForm.getValue());
        prStatusKey     = prStatusForm.getValue();
        itemTypeKey     = itemTypeForm.getValue();
        itemNameKey     = Ext.String.trim(itemNameForm.getValue());
        accountNoKey    = accountNoForm.getValue();
        rfiNoKey        = Ext.String.trim(rfiNoForm.getValue());
        unitCdKey       = unitCdForm.getValue();
        deliveryDateKey = deliveryDateForm.getRawValue();
        supplierNameKey = Ext.String.trim(supplierNameForm.getValue());
        prIssuerKey     = prIssuerForm.getValue();
        prChargedKey    = prChargedForm.getValue();
        
        dsPrSearch.load({
            params: {
                prDateVal       : prDateKey,
                prNoVal         : prNoKey,
                requesterVal    : requesterKey,
                approverVal     : approverKey,
                prStatusVal     : prStatusKey,
                itemTypeVal     : itemTypeKey,
                itemNameVal     : itemNameKey,
                accountNoVal    : accountNoKey,
                rfiNoVal        : rfiNoKey,
                unitCdVal       : unitCdKey,
                deliveryDateVal : deliveryDateKey,
                supplierNameVal : supplierNameKey,
                prIssuerVal     : prIssuerKey,
                prChargedVal    : prChargedKey
            }
        });
        winPrSearch.hide(); 
    }
    /** 
     * ========================================================================================================
     * **************************************** DEFINE STORE **************************************************
     * ========================================================================================================
    **/
    Ext.define('PrSearch',{
        extend: 'Ext.data.Model',
        fields:[
            {name: 'PR_NO'},
            {name: 'ISSUED_DATE', type:'date', dateFormat:'Ymd'},
            {name: 'REQUESTER_NAME'},
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
            {name: 'ITEM_PRICE'},
            {name: 'AMOUNT'},
            {name: 'ITEM_STATUS'},
            {name: 'REASON_TO_REJECT_ITEM'},
            {name: 'REJECT_ITEM_NAME_BY'},
            {name: 'ATTACHMENT_ITEM_COUNT'},
            {name: 'PR_STATUS_NAME'},
            {name: 'APPROVER_NAME'},
            {name: 'ITEM_STATUS_NAME'},
            {name: 'REQ_BU_CD'},
            {name: 'CHARGED_BU_CD'}
        ]
    });
    var dsPrSearch = Ext.create('Ext.data.Store', {
        model: 'PrSearch',
        groupField: 'PR_NO',
        sorters: ['PR_NO','ITEM_NAME'],
        proxy:{
            type: 'ajax',
            url: '../db/Paging/PAGING_PR_ITEM.php',
            reader: {
                type: 'json',
                root: 'rows'
            }
        }//,
        //autoLoad: true
    }); 
    dsPrSearch.load({
        params: {
            prDateVal       : prDateKey,
            prNoVal         : prNoKey,
            requesterVal    : requesterKey,
            approverVal     : approverKey,
            prStatusVal     : prStatusKey,
            itemTypeVal     : itemTypeKey,
            itemNameVal     : itemNameKey,
            accountNoVal    : accountNoKey,
            rfiNoVal        : rfiNoKey,
            unitCdVal       : unitCdKey,
            deliveryDateVal : deliveryDateKey,
            supplierNameVal : supplierNameKey,
            prIssuerVal     : prIssuerKey,
            prChargedVal    : prChargedKey
        }
    });
    
    /** 
     * ========================================================================================================
     * **************************************** DEFINE FORM COMPONENT *****************************************
     * ========================================================================================================
    **/
    var prNoForm = new Ext.form.TextField({
        fieldStyle: {
            textTransform: "uppercase"
        },
        fieldLabel: 'PR NO.',
        name: 'prNoForm',
        flex: 2
    });
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
    var prApproverForm = new Ext.form.TextField({
        fieldStyle: {
            textTransform: "uppercase"
        },
        fieldLabel: 'APPROVER',
        name: 'prApproverForm',
        flex: 2
    });
    var prStatusForm = Ext.create('Ext.form.field.ComboBox', {
        fieldLabel: 'STATUS',
        name: 'prStatusForm',
        store: dsAppStatus,
        displayField: 'APP_STATUS_NAME',
        valueField: 'APP_STATUS_CD',
        queryMode: 'local',
        editable: true,
        flex: 3
    });
    
    var itemTypeForm = Ext.create('Ext.form.field.ComboBox', {
        fieldLabel: 'TYPE',
        name: 'itemTypeForm',
        store: dsItemType,
        displayField: 'ITEM_TYPE_NAME',
        valueField: 'ITEM_TYPE_CD',
        queryMode: 'local',
        editable: true,
        flex: 2
    });
    var accountNoForm = Ext.create('Ext.form.field.ComboBox', {
        fieldLabel: 'EXPENSE',
        name: 'accountNoForm',
        store: dsAccount,
        valueField: 'ACCOUNT_NO',
        displayField: 'ACCOUNT_CD_NAME',
        queryMode: 'local',
        editable: true,
        typeAhead: true,
        forceSelection: true,
        flex: 2
    });
    var rfiNoForm=new Ext.form.TextField({
        fieldLabel: 'RFI',
        name: 'rfiNo',
        maxLength: 6,
        enforceMaxLength: 6,
        flex: 2
    });
    var itemNameForm = new Ext.form.TextField({
        fieldStyle: {
            textTransform: "uppercase"
        },
        fieldLabel: 'ITEM NAME',
        name: 'itemNameForm',
        flex: 3
    });
    var unitCdForm = Ext.create('Ext.form.field.ComboBox', {
        fieldLabel: 'U M',
        name: 'unitCd',
        store: dsUnit,
        valueField: 'UNIT_CD',
        displayField: 'UNIT_NAME',
        queryMode: 'local',
        editable: false,
        typeAhead: true,
        forceSelection: true,
        allowBlank: false,
        flex: 2
    });
    var deliveryDateForm = new Ext.form.field.Date({
        fieldLabel: 'DUE DATE',
        name: 'prDateForm',
        format: 'd/m/Y',
        flex: 2
    });
    var supplierNameForm = new Ext.form.TextField({
        fieldStyle: {
            textTransform: "uppercase"
        },
        fieldLabel: 'SUPPLIER',
        name: 'supplierNameForm',
        flex: 3
    });
    var prIssuerForm = Ext.create('Ext.form.field.ComboBox', {
        fieldLabel: 'ISSUER BU',
        name: 'prIssuer',
        store: dsBuCode,
        displayField: 'BU_CD_NAME',
        valueField: 'BU_CD',
        queryMode: 'local',
        editable: false,
        allowBlank: false,
        flex: 2
    });
    var prChargedForm = Ext.create('Ext.form.field.ComboBox', {
        fieldLabel: 'CHARGED BU',
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
    * ========================================================================================================
    * **************************************** DEFINE GRID ***************************************************
    * ========================================================================================================
    **/
    var groupingFeature = Ext.create ('Ext.grid.feature.Grouping',{
        groupHeaderTpl: 'PR NO: {name} ({rows.length} Item{[values.rows.length > 1? "s" : ""]})'
    });
    var prDetailGrid = new Ext.grid.GridPanel({
        title: 'Search',
        autoScroll: true,
        border: false,
        columnLines: true,
        store: dsPrSearch,
        features: [groupingFeature],
        columns :[{
            header: 'NO',
            width: 30,
            align: 'right',
            xtype: 'rownumberer',
            sortable: true
        },{
            header: 'ITEM STATUS',
            dataIndex: 'ITEM_STATUS_NAME'
        },{
            header: 'HEADER',
            columns: [{
                header: 'PR NO',
                width: 90,
                dataIndex: 'PR_NO'
            },{
                header: 'ISSUED DATE',
                dataIndex: 'ISSUED_DATE',
                renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                width: 72
            },{
                header: 'REQUESTER',
                dataIndex: 'REQUESTER_NAME',
                width: 150
            },{
                header: 'PR STATUS',
                width: 210,
                dataIndex: 'PR_STATUS_NAME',
                renderer: prStatusVal
            },{
                header: 'APPROVER',
                width: 150,
                dataIndex: 'APPROVER_NAME'
            },{
                header: 'ISSUER BU',
                width: 70,
                dataIndex: 'REQ_BU_CD'
            },{
                header: 'CAHRGED BU',
                width: 70,
                dataIndex: 'CHARGED_BU_CD'
            }]
        },{
            header: 'DETAIL',
            columns: [{
                header: 'NAME (Maker, Part No, Spec, Size, Color etc)',
                width: 350,
                dataIndex: 'ITEM_NAME'
            },{
                header: 'DUE DATE',
                width: 72,
                dataIndex: 'DELIVERY_DATE',
                renderer: Ext.util.Format.dateRenderer('d/m/Y')
            },{
                header: 'TYPE',
                width: 40,
                dataIndex: 'ITEM_TYPE_CD',
                renderer:  function jnsinvest(val){
                    if(val == '1'){
                        return 'EXP';
                    }else if(val == '2'){
                        return 'RFI';
                    }else if(val == '3'){
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
                dataIndex: 'UNIT_CD'
            },{
                header: 'QTY',
                width: 50,
                align: 'right',
                dataIndex: 'QTY'
            },{
                header: 'UNIT PRICE',
                width: 100,
                align: 'right',
                dataIndex: 'ITEM_PRICE',
                xtype: 'numbercolumn',
                format: '0,000'
            },{
                header: 'SUPPLIER',
                width: 220,
                dataIndex: 'SUPPLIER_NAME',
                summaryType: 'count',
                summaryRenderer: function(value, summaryData, dataIndex) {
                    return '<b>Total Amount</b>'
                }
            }]
        }],
        tbar: [{
            text: 'Search',
            tooltip: 'Search',
            iconCls: 'search_button',
            scale: 'medium',
            handler: function(){
                showWinSearchPrSearch();
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
    * Define PR Detail Form 
    * =======================================
    **/
    var prSearchForm = Ext.widget('form',{
        border: false, 
        frame: true,
        bodyPadding: '2',
        height: 263,
        fieldDefaults: {
            anchor: '100%',
            labelWidth: 100,
            msgTarget: 'side',
            labelAlign: 'right'
        },
        items: [{
            xtype: 'container',
            layout: 'hbox',
            items: [prNoForm,prDateForm]
        },{
            xtype: 'container',
            layout: 'hbox',
            items: [requesterForm,prApproverForm]
        },{
            xtype: 'container',
            layout: 'hbox',
            items: [prStatusForm,itemTypeForm]
        },{
            xtype: 'container',
            layout: 'hbox',
            items: [itemNameForm,unitCdForm]
        },{
            xtype: 'container',
            layout: 'hbox',
            items: [supplierNameForm,deliveryDateForm]
        },{
            xtype: 'container',
            layout: 'hbox',
            items: [accountNoForm,rfiNoForm]
        },{
            xtype: 'container',
            layout: 'hbox',
            items: [prChargedForm]
        }],
        buttons: [{
            text: 'Save',
            name: 'save',
            handler: function(){
                searchPrSearch();
            }
        },{
            text: 'Reset',
            name: 'reset',
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
    return prDetailGrid;
}


