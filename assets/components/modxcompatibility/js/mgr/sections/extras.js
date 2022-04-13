modxcompatibility.page.Extras = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'modxcompatibility-panel-extras',
            renderTo: 'modxcompatibility-panel-extras-div'
        }]
    });
    modxcompatibility.page.Extras.superclass.constructor.call(this,config);
};
Ext.extend(modxcompatibility.page.Extras,MODx.Component);
Ext.reg('modxcompatibility-page-extras',modxcompatibility.page.Extras);
