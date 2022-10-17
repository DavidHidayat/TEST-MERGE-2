/**
 * The main application viewport, which displays the whole application
 * @extends Ext.Viewport
 */
Ext.define('ExtMVC.view.Viewport', {
    extend: 'Ext.Viewport',    
    layout: 'anchor',
    
    requires: [
        'ExtMVC.view.StateCityCombo'
    ],
    
    initComponent: function() {
        var me = this;
        
        Ext.apply(me, {
            items: [
                {
                    xtype: 'statecityform'
                }
            ]
        });
                
        me.callParent(arguments);
    }
});