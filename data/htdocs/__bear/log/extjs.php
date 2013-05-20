/* * BEAR Log * */ Ext.onReady(function(){ var bearLogTabs = new
Ext.TabPanel({ renderTo: 'ajaxvar', activeTab: 0, width:1000,
height:"auto", plain:true, defaults:{autoScroll: true, autoHeight:
true}, items:[{ title: 'Page', autoLoad:'tab.php?var=app' },{ title:
'Smarty', autoLoad:'tab.php?var=smarty' },{ title: 'Vars',
autoLoad:'tab.php?var=var' },{ title: 'Previous',
autoLoad:'tab.php?var=prev' },{ title: 'Ajax', disabled:true } ] }); });
