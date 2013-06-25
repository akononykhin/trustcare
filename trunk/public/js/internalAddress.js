var internalAddress = {
    setUrlBase: function(urlBase) {
    	internalAddress.urlBase = urlBase;
    }
    ,replacePatterns: function(controller, action) {
        var url = internalAddress.urlBase;
        url = url.replace(/__controller__/g, controller).replace(/__action__/g, action);
        return url;
    }
    ,loadI18nDict: function() {
        return internalAddress.replacePatterns('language', 'load-dict-for-js') + '/language/__lng__/namespace/__ns__';
    }
    ,pharmacyArray: function() {
        return internalAddress.replacePatterns('pharmacy', 'load-array-of-active');
    }
    ,pharmacyCreateDlg: function() {
        return internalAddress.replacePatterns('pharmacy', 'create') + '/for_dialog/1';
    }
    ,patientArray: function() {
        return internalAddress.replacePatterns('patient', 'load-array-of-active');
    }
    ,patientCheckIsMale: function() {
        return internalAddress.replacePatterns('patient', 'check-is-male');
    }
    ,patientCreateDlg: function() {
        return internalAddress.replacePatterns('patient', 'create') + '/for_dialog/1';
    }
    ,pharmDictArray: function() {
        return internalAddress.replacePatterns('pharm-dict', 'load-array-for-type');
    }
    ,pharmDictCreate: function() {
        return internalAddress.replacePatterns('pharm-dict', 'create-ajax');
    }
    ,pharmDictChange: function() {
        return internalAddress.replacePatterns('pharm-dict', 'change-ajax');
    }
    ,pharmDictRemove: function() {
        return internalAddress.replacePatterns('pharm-dict', 'remove-ajax');
    }
    ,formCareIsAlreadyFilled: function() {
        return internalAddress.replacePatterns('form_care', 'check-if-filled');
    }
    ,formCareEdit: function(id) {
        return internalAddress.replacePatterns('form_care', 'edit') + '/id/' + id;
    }
}