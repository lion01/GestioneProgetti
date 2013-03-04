/**
 * @class Ext.ux.grid.filter.StringFilter
 * @extends Ext.ux.grid.filter.Filter
 * Filter by a configurable Ext.form.field.Text
 * <p><b><u>Example Usage:</u></b></p>
 * <pre><code>
var filters = Ext.create('Ext.ux.grid.GridFilters', {
    ...
    filters: [{
        // required configs
        type: 'string',
        dataIndex: 'name',

        // optional configs
        value: 'foo',
        active: true, // default is false
        iconCls: 'ux-gridfilter-text-icon' // default
        // any Ext.form.field.Text configs accepted
    }]
});
 * </code></pre>
 */
var querystring;
 
Ext.define('Ext.ux.grid.filter.StringFilter', {
    extend: 'Ext.ux.grid.filter.Filter',
    alias: 'gridfilter.string',

    /**
     * @cfg {String} iconCls
     * The iconCls to be applied to the menu item.
     * Defaults to <tt>'ux-gridfilter-text-icon'</tt>.
     */
    iconCls : 'ux-gridfilter-text-icon',

    emptyText: 'Inserire testo filtro...',
    selectOnFocus: true,
    width: 180,

    /**
     * @private
     * Template method that is to initialize the filter and install required menu items.
     */
    init : function (config) {
        Ext.applyIf(config, {
            enableKeyEvents: true,
            iconCls: this.iconCls,
			labelCls: 'ux-rangemenu-icon '+this.iconCls,
			hideEmptyLabel: false,
			labelSeparator: '',
			labelWidth: 29,
            hideLabel: true,
            listeners: {
                scope: this,
                keyup: this.onInputKeyUp,
                el: {
                    click: function(e) {
                        e.stopPropagation();
                    }
                }
            }
        });	

        this.inputItem = Ext.create('Ext.form.field.Text', config);
		
        this.menu.add(this.inputItem);
		
		var me = this, 
		listeners = {
                checkchange: me.checkChange,
                scope: me
        };
				
        this.checkboxItem = Ext.create('Ext.menu.CheckItem', {
				text: 'Includi Record Nulli',
                hideOnClick: false,
                value: '',
                listeners: listeners,
				scope:this
        })
		
		this.menu.add(this.checkboxItem);
		
		this.checkboxItem.addEvents('checkchange');

		this.checkboxItem.on('checkchange', this.onCheckChange, this);		
				
		this.updateTask = Ext.create('Ext.util.DelayedTask', this.fireUpdate, this);
		
		
		
    },
	checkChange:function (item, checked){
	
		if (item.checked) {
					
			this.fireEvent('checkchange', item, checked);			
		
		}
	
	},
	
    /**
     * @private
     * Template method that is to get and return the value of the filter.
     * @return {String} The value of this filter
     */
    getValue : function () {
	
		querystring = this.inputItem.getValue();
		console.log(querystring);
		return this.inputItem.getValue();
    },

    /**
     * @private
     * Template method that is to set the value of the filter.
     * @param {Object} value The value to set the filter
     */
    setValue : function (value) {
		
		this.inputItem.setValue(value);
        this.fireEvent('update', this);
    },

    /**
     * @private
     * Template method that is to return <tt>true</tt> if the filter
     * has enough configuration information to be activated.
     * @return {Boolean}
     */
    isActivatable : function () {
	
		if(this.checkboxItem.checked) {
		
			return true;
			
		}
		
        else return this.inputItem.getValue().length > 0;
    },

    /**
     * @private
     * Template method that is to get and return serialized filter data for
     * transmission to the server.
     * @return {Object/Array} An object or collection of objects containing
     * key value pairs representing the current configuration of the filter.
     */
    getSerialArgs : function () {

		return {type: 'string', value: this.getValue()};
    },
	
	
	onCheckChange:function (){
	
		this.updateTask.delay(this.updateBuffer);
			
	},

    /**
     * Template method that is to validate the provided Ext.data.Record
     * against the filters configuration.
     * @param {Ext.data.Record} record The record to validate
     * @return {Boolean} true if the record is valid within the bounds
     * of the filter, false otherwise.
     */
    validateRecord : function (record) {
        var val = record.get(this.dataIndex);
		
        if(typeof val != 'string') {
            return (this.getValue().length === 0);
        }
		
		if(this.checkboxItem.checked) {
		
			if(val == "") return true;
			
		}
		
		else {
		
			return val.toLowerCase().indexOf(this.getValue().toLowerCase()) > -1;
		
		}
    },

    /**
     * @private
     * Handler method called when there is a keyup event on this.inputItem
     */
    onInputKeyUp : function (field, e) {
	
		var k = e.getKey();
		
        if (k == e.RETURN && field.isValid()) {
            e.stopEvent();
            this.menu.hide();
            return;
        }
        		
        this.updateTask.delay(this.updateBuffer);
		
		
    }
});