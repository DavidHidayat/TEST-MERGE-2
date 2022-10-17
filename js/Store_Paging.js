/** 
 * ===========================
 * Define PR Header for Paging
 * ===========================
 **/
Ext.define('PrHeaderPaging',{
    extend: 'Ext.data.Model',
    fields:[
        {name: 'PR_NO'},
        {name: 'BU_CD'},
        {name: 'ISSUED_DATE', type:'date', dateFormat:'Ymd'},
        {name: 'PR_STATUS_NAME'},
        {name: 'REQUESTER'},
        {name: 'REQUESTER_NAME'},
        {name: 'APPROVER'},
        {name: 'APPROVER_NAME'},
        {name: 'PROC_IN_CHARGE_NAME'},
        {name: 'PROC_ACCEPT_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'}
    ]
});
var dsPrHeader = new Ext.data.Store({
    model: 'PrHeaderPaging',
    pageSize: 15,
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_PR.php',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    },
    sorters: [{
        property: 'ISSUED_DATE',
        direction: 'DESC'
    },{
        property: 'PR_NO',
        direction: 'DESC'
    }]
 });
 /** 
 * ===========================
 * Define PR Waiting for Paging
 * ===========================
 **/
Ext.define('PrWaitingPaging',{
    extend: 'Ext.data.Model',
    fields:[
        {name: 'PR_NO'},
        {name: 'BU_CD'},
        {name: 'ISSUED_DATE', type:'date', dateFormat:'Ymd'},
        {name: 'PR_STATUS_NAME'},
        {name: 'REQUESTER'},
        {name: 'REQUESTER_NAME'},
        {name: 'APPROVER'},
        {name: 'APPROVER_NAME'},
        {name: 'PROC_IN_CHARGE_NAME'},
        {name: 'PROC_ACCEPT_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'}
    ]
});
var dsPrWaiting = new Ext.data.Store({
    model: 'PrWaitingPaging',
    pageSize: 15,
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_PR_WAITING.php',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    },
    sorters: [{
        property: 'ISSUED_DATE',
        direction: 'DESC'
    },{
        property: 'PR_NO',
        direction: 'DESC'
    }]
 });
 /** 
 * ===========================
 * Define Item Master for Paging
 * ===========================
 **/
Ext.define('ItemPaging',{
    extend: 'Ext.data.Model',
    fields:[
        {type: 'string', name: 'ITEM_CD'},
        {type: 'string', name: 'ITEM_NAME'},
        {type: 'string', name: 'ITEM_GROUP_CD'},
        {name: 'CREATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'CREATE_BY'},
        {name: 'UPDATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'UPDATE_BY'}
    ]
});
var dsItemPaging = new Ext.data.Store({
    model: 'ItemPaging',
    pageSize: 15,
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_MASTER.php?criteria=Item',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    }//,
    //autoLoad: true
 });
 /** 
 * ===========================
 * Define Item Group Master for Paging
 * ===========================
 **/
Ext.define('ItemGroupPaging',{
    extend: 'Ext.data.Model',
    fields:[
        {type: 'string', name: 'ITEM_GROUP_CD'},
        {type: 'string', name: 'ITEM_GROUP_NAME'},
        {name: 'CREATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'CREATE_BY'},
        {name: 'UPDATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'UPDATE_BY'}
    ]
});
var dsItemGroupPaging = new Ext.data.Store({
    model: 'ItemGroupPaging',
    pageSize: 15,
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_MASTER.php?criteria=ItemGroup',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    }//,
    //autoLoad: true
 });
 /** 
 * ===========================
 * Define Item Price Master for Paging
 * ===========================
 **/
Ext.define('ItemPricePaging',{
    extend: 'Ext.data.Model',
    fields:[
        {type: 'string', name: 'ITEM_CD'},
        {type: 'string', name: 'ITEM_NAME'},
        {type: 'string', name: 'UNIT_CD'},
        {type: 'string', name: 'ITEM_PRICE'},
        {name: 'EFFECTIVE_DATE_FROM', type: 'date', dateFormat:'Ymd'},
        {type: 'string', name: 'SUPPLIER_CD'},
        {type: 'string', name: 'SUPPLIER_NAME'},
        {name: 'CREATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'CREATE_BY'},
        {name: 'UPDATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'UPDATE_BY'}
    ]
});
var dsItemPricePaging = new Ext.data.Store({
    model: 'ItemPricePaging',
    pageSize: 15,
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_MASTER.php?criteria=ItemPrice',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    }//,
    //autoLoad: true
 });
 /** 
 * ===========================
 * Define Pr Approver Master for Paging
 * ===========================
 **/
Ext.define('PrApproverMstPaging',{
    extend: 'Ext.data.Model',
    fields:[
        {name: 'BU_CD'},
        {name: 'BU_NAME'},
        {name: 'APPROVER_NO'},
        {name: 'NPK'},
        {name: 'APPROVER_NAME'},
        {name: 'APPROVER_LEVEL'},
        {name: 'LIMIT_AMOUNT'}
    ]
});
var dsPrApproverMstPaging = new Ext.data.Store({
    model: 'PrApproverMstPaging',
    groupField: 'BU_CD',
    sorters: ['BU_CD','APPROVER_NO','LIMIT_AMOUNT','NPK','APPROVER_NAME'],
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_MASTER.php?criteria=PrApproverMst',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    }//,
    //autoLoad: true
 });
 /** 
 * ===========================
 * Define Pr Procurement Approver Master for Paging
 * ===========================
 **/
Ext.define('PrProcApproverMstPaging',{
    extend: 'Ext.data.Model',
    fields:[
        {name: 'PLANT_CD'},
        {name: 'PLANT_NAME'},
        {name: 'BU_CD'},
        {name: 'BU_NAME'},
        {name: 'NPK'},
        {name: 'APPROVER_NAME'}
    ]
});
var dsPrProcApproverMstPaging = new Ext.data.Store({
    model: 'PrProcApproverMstPaging',
    groupField: 'PLANT_NAME',
    sorters: ['NPK','APPROVER_NAME','PLANT_CD','PLANT_NAME','BU_CD'],
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_MASTER.php?criteria=PrProcApproverMst',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    },
    autoLoad: true
 });
 /** 
 * ===========================
 * Define Supplier Master for Paging
 * ===========================
 **/
Ext.define('SupplierPaging',{
    extend: 'Ext.data.Model',
    fields:[
        {type: 'string', name: 'SUPPLIER_CD'},
        {type: 'string', name: 'SUPPLIER_NAME'},
        {type: 'string', name: 'CURRENCY_CD'},
        {type: 'string', name: 'VAT'},
        {type: 'string', name: 'CONTACT'},
        {type: 'string', name: 'EMAIL'},
        {type: 'string', name: 'EMAIL_CC'},
        {type: 'string', name: 'PHONE'},
        {type: 'string', name: 'FAX'},
        {type: 'string', name: 'ADDRESS'},
        {name: 'CREATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'CREATE_BY'},
        {name: 'UPDATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'UPDATE_BY'}
    ]
});
var dsSupplierPaging = new Ext.data.Store({
    model: 'SupplierPaging',
    pageSize: 15,
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_MASTER.php?criteria=Supplier',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    }//,
    //autoLoad: true
 });
 /** 
 * ===========================
 * Define Unit Measure Master for Paging
 * ===========================
 **/
Ext.define('UnitMeasurePaging',{
    extend: 'Ext.data.Model',
    fields:[
        {type: 'string', name: 'UNIT_CD'},
        {type: 'string', name: 'UNIT_NAME'},
        {name: 'CREATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'CREATE_BY'},
        {name: 'UPDATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'UPDATE_BY'}
    ]
});
var dsUnitMeasurePaging = new Ext.data.Store({
    model: 'UnitMeasurePaging',
    pageSize: 15,
    proxy: {
        type: 'ajax',
        url: '../db/Paging/PAGING_MASTER.php?criteria=UnitMeasure',
        reader: {
            type: 'json',
            root: 'rows', 
            totalProperty: 'total'
        }
    }//,
    //autoLoad: true
 });