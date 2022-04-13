modxcompatibility.grid.Extras = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        url: modxcompatibility.config.connectorUrl
        ,baseParams: {
            action: 'mgr/checkextras'
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
            ,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                if(!value.version || value.breaks_at){
                    return _('modxcompatibility.extras.version');
                }
                return _('modxcompatibility.extras.version') + ': '+ value.version + ' ' + _('modxcompatibility.extras.supported') + ': '+ value.breaks_at;
            }
        },{
            header: _('modxcompatibility.extras.update')
            ,dataIndex: 'update'
            ,width: 150
            ,sortable: true
            ,hidden: false
            ,renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                if (!value.length){
                    return '';
                } else {
                    var update = '';
                    value.forEach(function(item, index){
                        update += _('modxcompatibility.extras.version') + ': '+ item.version + ' ' + _('modxcompatibility.extras.supported') + ': '+ item.breaks_at + '<br />';
                    });
                    return update;
                }
            }
        }]
        ,tbar: [
            {
                xtype: 'textfield'
                ,emptyText: _('modxcompatibility.extras.search') + '...'
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
