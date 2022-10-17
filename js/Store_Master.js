/** 
 * ===========================
 * Define BU Plant
 * ===========================
 **/
Ext.define('Plant', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'PLANT_CD'},
        {type: 'string', name: 'PLANT_NAME'}
    ]
});
var dsPlant = 
    Ext.create('Ext.data.Store', {
        model: 'Plant',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_PLANT.php',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
/** 
 * ===========================
 * Define BU Code
 * ===========================
 **/
Ext.define('BuCode', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'BU_CD'},
        {type: 'string', name: 'BU_CD_NAME'}
    ]
});
var dsBuCode = 
    Ext.create('Ext.data.Store', {
        model: 'BuCode',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_TBUNIT.php',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
/** 
 * ===========================
 * Define Unit Measure
 * ===========================
 **/
Ext.define('Unit', {
extend: 'Ext.data.Model',
    fields: [
        {name: 'UNIT_CD'},
        {type: 'string', name: 'UNIT_NAME'}
    ]
});
var dsUnit = 
    Ext.create('Ext.data.Store', {
        model: 'Unit',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_UNIT.php?action=view',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
    
    /** 
 * ===========================
 * Define ITEM CODE
 * ===========================
 **/
Ext.define('ItemCode', {
extend: 'Ext.data.Model',
    fields: [
        {name: 'ACCOUNT_NO'},
        {type: 'string', name: 'ACCOUNT_NAME'}
    ]
});
var dsItemCode = 
    Ext.create('Ext.data.Store', {
        model: 'ItemCode',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_ITEM_CODE.php?action=view',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
    
/** 
 * ===========================
 * Define BUDGET CODE
 * ===========================
 **/
//Ext.define('State', {
//extend: 'Ext.data.Model',
//    fields: [
//        {name: 'state_id'},
//        {name: 'state_name'}
//    ],
//    idProperty : 'state_id'
//});
//var dsState = 
//    Ext.create('Ext.data.Store', {
//        model: 'State',
//        autoLoad : true,
//        storeId : 'statesStore',
//        
//        proxy:{
//            type: 'ajax',
//            url: '../db/Master_Data/EPS_M_STATES.php?action=view',
//            reader: {
//                type: 'json',
//                root: 'rows'
//            }
//        }
//        });  
       
    
//    /** 
// * ===========================
// * Define BUDGET ITEM CODE
// * ===========================
// **/
//Ext.define('City', {
//extend: 'Ext.data.Model',
//    fields: [
//        {name: 'city_id'},
//        {name: 'state_id'},
//        {name: 'city_name'}
//    ],
//    idProperty : 'city_id'
//});
//var dsCity = 
//    Ext.create('Ext.data.Store', {
//        model: 'City',
//        autoLoad : true,
//        
//        proxy:{
//            type: 'ajax',
//            url: '../db/Master_Data/EPS_M_CITIES.php?action=view',
//            reader: {
//                type: 'json',
//                root: 'rows'
//            }
//        }
//        });  

/** 
 * ===========================
 * Define Account
 * ===========================
 **/
Ext.define('Account', {
extend: 'Ext.data.Model',
    fields: [
        {name: 'ACCOUNT_NO'},
        {type: 'string', name: 'ACCOUNT_CD_NAME'},
        //{type: 'string', name: 'ACCOUNT_CD' }
    ]
});
var dsAccount = 
    Ext.create('Ext.data.Store', {
        model: 'Account',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_ACCOUNT.php?action=view',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
    
Ext.define('Account2', {
extend: 'Ext.data.Model',
    fields: [
        {name: 'ACCOUNT_NO'},
        {type: 'string', name: 'ACCOUNT_CD_NAME'}
    ]
});
var dsAccount2 = 
    Ext.create('Ext.data.Store', {
        model: 'Account2',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_ACCOUNT.php?action=viewInv',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
Ext.define('Account3', {
extend: 'Ext.data.Model',
    fields: [
        {name: 'ACCOUNT_NO'},
        {type: 'string', name: 'ACCOUNT_CD_NAME'}
    ]
});
var dsAccount3 = 
    Ext.create('Ext.data.Store', {
        model: 'Account3',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_ACCOUNT.php?action=viewInvS',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
/** 
 * ===========================
 * Define Item
 * ===========================
 **/
Ext.define('Item', {
    extend: 'Ext.data.Model',
    fields: [
        {type: 'string', name: 'ITEM_CD'},
        {type: 'string', name: 'ITEM_NAME'},
        {type: 'string', name: 'CREATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'CREATE_BY'},
        {type: 'string', name: 'UPDATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'UPDATE_BY'},
        {type: 'string', name: 'ITEM_NAME_VALUES'}
    ]
});
var dsItem = 
    Ext.create('Ext.data.Store', {
        model: 'Item',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_ITEM.php?action=searchItem',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
/** 
 * ===========================
 * Define Item Group
 * ===========================
 **/
Ext.define('ItemGroup', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'ITEM_GROUP_CD'},
        {name: 'ITEM_GROUP_NAME'},
        {name: 'CREATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'CREATE_BY'},
        {name: 'UPDATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'UPDATE_BY'}
    ]
});
var dsItemGroup = 
    Ext.create('Ext.data.Store', {
        model: 'ItemGroup',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_ITEM_GROUP.php?action=search',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    });
/** 
 * ===========================
 * Define Item Group
 * ===========================
 **/
Ext.define('ItemPrice', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'ITEM_GROUP_CD'},
        {name: 'ITEM_GROUP_NAME'},
        {name: 'CREATE_DATE'},
        {name: 'CREATE_BY'},
        {name: 'UPDATE_DATE'},
        {name: 'UPDATE_BY'}
    ]
});
var dsItemPrice = 
    Ext.create('Ext.data.Store', {
        model: 'ItemPrice',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_ITEM.php?action=searchItemPrice',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    });  
/** 
 * ===========================
 * Define Item tYPE
 * ===========================
 **/
Ext.define('ItemtType', {
    extend: 'Ext.data.Model',
    fields: [
        {type: 'string', name: 'ITEM_TYPE_CD'},
        {type: 'string', name: 'ITEM_TYPE_NAME'}
    ]
});
var dsItemType = 
    Ext.create('Ext.data.Store', {
        model: 'ItemtType',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_ITEM_TYPE.php',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 
/** 
 * ===========================
 * Define Supplier
 * ===========================
 **/
Ext.define('Supplier', {
    extend: 'Ext.data.Model',
    fields: [
        {type: 'string', name: 'SUPPLIER_CD'},
        {type: 'string', name: 'SUPPLIER_NAME'},
        {type: 'string', name: 'CONTACT'},
        {type: 'string', name: 'PHONE'},
        {type: 'string', name: 'FAX'},
        {type: 'string', name: 'ADDRESS'},
        {name: 'CREATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'CREATE_BY'},
        {name: 'UPDATE_DATE', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {type: 'string', name: 'UPDATE_BY'}
    ]
});
var dsSupplier = 
    Ext.create('Ext.data.Store', {
        model: 'Supplier',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_SUPPLIER.php?action=search',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    });  
/** 
 * ===========================
 * Define App Status
 * ===========================
 **/
Ext.define('AppStatus', {
    extend: 'Ext.data.Model',
    fields: [
        {type: 'string', name: 'APP_STATUS_CD'},
        {type: 'string', name: 'APP_STATUS_NAME'}
    ]
});
var dsAppStatus = 
    Ext.create('Ext.data.Store', {
        model: 'AppStatus',
        proxy:{
            type: 'ajax',
            url: '../db/Master_Data/EPS_M_APP_STATUS.php?action=view',
            reader: {
                type: 'json',
                root: 'rows'
            }
        },
        autoLoad: true
    }); 