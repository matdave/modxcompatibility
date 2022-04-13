var modxcompatibility = function(config) {
    config = config || {};
    config.record = {};
    modxcompatibility.superclass.constructor.call(this,config);
};
Ext.extend(modxcompatibility,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('modxcompatibility',modxcompatibility);
modxcompatibility = new modxcompatibility();
