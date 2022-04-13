modxcompatibility.panel.Extras = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('modxcompatibility')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            title: _('modxcompatibility.extras')
            ,items: [{
                xtype: 'modxcompatibility-grid-extras'
                ,preventRender: true
                ,cls: 'main-wrapper'
                ,anchor: '100%'
            }]
        }]
    });
    modxcompatibility.panel.Extras.superclass.constructor.call(this,config);
};
Ext.extend(modxcompatibility.panel.Extras, MODx.Panel);
Ext.reg('modxcompatibility-panel-extras', modxcompatibility.panel.Extras);
