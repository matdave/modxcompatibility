modxcompatibility.grid.Extras = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        url: modxcompatibility.config.connectorUrl
        ,baseParams: {
            action: 'checkextras'
        }
        ,fields: ['signature','package_name', 'info', 'update']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,columns: [{
            header: _('modxcompatibility.extras.signature')
            ,dataIndex: 'signature'
            ,width: 70
            ,hidden: true
            ,sortable: true
        },{
            header: _('modxcompatibility.extras.package_name')
            ,dataIndex: 'package_name'
            ,width: 200
            ,hidden: false
            ,sortable: true
        },{
            header: _('modxcompatibility.extras.info')
            ,dataIndex: 'info'
            ,width: 150
            ,sortable: false
            ,hidden: false
        },{
            header: _('modxcompatibility.extras.update')
            ,dataIndex: 'update'
            ,width: 150
            ,sortable: true
            ,hidden: false
        }]
        ,tbar: [
            {
                xtype: 'textfield'
                ,emptyText: _('modxcompatibility.global.search') + '...'
                ,name: 'search'
                ,listeners: {
                    'change': {fn: this.filterGrid, scope: this}
                    , 'render': {
                        fn: function (cmp) {
                            new Ext.KeyMap(cmp.getEl(), {
                                key: Ext.EventObject.ENTER
                                , fn: function () {
                                    this.fireEvent('change', this);
                                    return true;
                                }
                                , scope: cmp
                            });
                        }, scope: this
                    }
                }
            }
        ]
    });
    modxcompatibility.grid.Extras.superclass.constructor.call(this,config);
};
Ext.extend(modxcompatibility.grid.Extras,MODx.grid.Grid,{
    filterGrid: function(filter) {
        var filterName = filter.name;
        var value = filter.getValue();
        var store = this.getStore();

        if (store.baseParams[filterName] === value) return;

        store.baseParams[filterName] = value;
        this.getBottomToolbar().changePage(1);
    }
});
Ext.reg('modxcompatibility-grid-extras',modxcompatibility.grid.Extras);
