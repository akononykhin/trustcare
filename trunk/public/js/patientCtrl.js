var patientCtrl = {
	 idDlgAddPatient: 'dialog-add-patient'
	,idHidden: undefined
	,idText: undefined
	,newDlg: undefined
	
    ,init: function(idHidden, idText, newDlgTitle) {
    	this.idHidden = '#' + idHidden;
        this.idText = '#' + idText;
    	
        $(this.idText).autocomplete({
            source:     internalAddress.patientArray()
            ,delay:     300
            ,focus: function(event, ui ) {
                $(patientCtrl.idText).val(ui.item.label);
                return false;
            }
            ,select: function( event, ui ) {
                $(patientCtrl.idText).val(ui.item.label);
                $(patientCtrl.idHidden).val(ui.item.value);
                if(patientCtrl.checkOnChangePatient && $.isFunction(patientCtrl.checkOnChangePatient)) {
                    patientCtrl.checkOnChangePatient();
                }
                return false;
            }
        });
    	
    	
        this.newDlg = $("<div id='" + this.idDlgAddPatient + "'></div>").dialog({
            dialogClass: 'dialog-content'
           ,title: i18n.translate("Add Patient")
           ,autoOpen: false
           ,height: 500
           ,width: 500
           ,modal: true
        });
        
        return this.newDlg;
    }

    ,configureAddLink: function(idAddLink) {
        $("#" + idAddLink).click(function() {
            patientCtrl.newDlg.load(internalAddress.patientCreateDlg(), function(){
                pageAsDlg.linkFormToDlg("#" + patientCtrl.idDlgAddPatient);
            }).dialog('open');
            return false;
        });
    }
    
    ,checkOnChangePatient: undefined

}