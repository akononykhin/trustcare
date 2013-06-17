var pharmacyCtrl = {
	 idDlgAddPharmacy: 'dialog-add-pharmacy'
	,idList: undefined
	,newDlg: undefined
	
    ,init: function(idList, newDlgTitle) {
    	this.idList = '#' + idList;
    	
        this.newDlg = $("<div id='" + this.idDlgAddPharmacy + "'></div>").dialog({
            dialogClass: 'dialog-content'
           ,title: i18n.translate("Add Pharmacy")
           ,autoOpen: false
           ,height: 500
           ,width: 700
           ,modal: true
           ,close: function() {
               pharmacyCtrl.pharmacyReload();
           }
        });
        
        return this.newDlg;
    }

    ,configureAddLink: function(idAddLink) {
        $("#" + idAddLink).click(function() {
            pharmacyCtrl.newDlg.load(internalAddress.pharmacyCreateDlg(), function(){
                pageAsDlg.linkFormToDlg("#" + pharmacyCtrl.idDlgAddPharmacy);
            }).dialog('open');
            return false;
        });
    }

    ,pharmacyReload: function(){
        if($(pharmacyCtrl.idList + " option").length) {
        	g_selectedPharmacy = $(pharmacyCtrl.idList + " option:selected").val();
        }
        $.ajax({
             url: internalAddress.pharmacyArray()
        	,type:'POST'
        	,data:''
        	,success: function(data){
        	    if(!data || !data.success) {
        	        return;
        	    }

        	    var select = $(pharmacyCtrl.idList);
        	    if(select.prop) {
        	        var options = select.prop('options');
        	    }
        	    else {
        	        var options = select.attr('options');
        	    }
        	    $('option', select).remove();
        	    options[options.length] = new Option('', '');
        	    for (var key in data.rows){
        	        var selectedFlag = false;
        	        if(key == g_selectedPharmacy) {
        	            selectedFlag = true;
        	        } 
        	        options[options.length] = new Option(data.rows[key], key, selectedFlag);
        	    }
        	}
            ,complete: function(jqXHR, textStatus) {
            }
        });
    }
}