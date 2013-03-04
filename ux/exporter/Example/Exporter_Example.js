Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux.exporter','..'); //relative path to the Ext.ux.Exporter directory

Ext.application({
    name: 'trial',
    appFolder: 'app',
    requires: [
        'Ext.ux.exporter.Exporter',
    ],
    launch: function() {
        Ext.define('MyModel', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'title', type: 'string'},
                {name: 'firstName', type: 'string'},
                {name: 'lastName', type: 'string'}
            ]
        });
        var myData = [
            ['Mr','John','Doe'],
            ['Mrs','Jane','Doe']
        ];
        var myStore = Ext.create('Ext.data.ArrayStore',{
            model: 'MyModel',
            data: myData
        });
        var myGrid = Ext.create('Ext.grid.Panel', {
            title: 'My Example Grid',
            store: myStore,
            columns: [{
                text: 'Title',
                width: 50,
                dataIndex: 'title'
            },{
                text: 'First Name',
                flex: 1,
                dataIndex: 'firstName'
            },{
                text: 'Surname',
                flex: 1,
                dataIndex: 'lastName'
            }],
            height: 300,
            width: 500
        });
        var filename = 'exportedData';
        Ext.create('Ext.container.Viewport', {
            layout: 'fit',
            items: [{
                xtype: 'panel',
                title: 'Exporter Download Example',
                height: 400,
                width: 550,
                items: [myGrid],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                        text: 'Download Excel File',
                        handler: function() {
                            //extract data from grid as excel data
                            var data = Ext.ux.exporter.Exporter.exportAny(myGrid, 'excel', filename);
                            //save data on the server in a temp file
                            Ext.Ajax.request({
                                url: '../ServerSide/saveFile.jsp',
                                params: {
                                    filename: filename,
                                    data: data
                                },
                                success: function() {
                                    //prompt a download of the temp file that was saved
                                    var ifrm = document.getElementById('downloadFrame');
                                    ifrm.src = '../ServerSide/download.jsp?filename=' + filename + '&filetype=xls';
                                }
                            });
                        }
                    },{
                        text: 'Download CSV File',
                        handler: function() {
                            //extract data from grid as csv data
                            var data = Ext.ux.exporter.Exporter.exportAny(myGrid, 'csv', filename);
                            //save data on the server in a temp file
                            Ext.Ajax.request({
                                url: '../ServerSide/saveFile.jsp',
                                params: {
                                    filename: filename,
                                    data: data
                                },
                                success: function() {
                                    //prompt a download of the temp file that was saved
                                    var ifrm = document.getElementById('downloadFrame');
                                    ifrm.src = '../ServerSide/download.jsp?filename=' + filename + '&filetype=csv';
                                }
                            });
                        }
                    }]
                }]
            }]
        });
    }
});