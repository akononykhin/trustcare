var dictEntitiesCtrl = {
     idDlgAdd: 'dlg-add-dict-entity'
    ,idCtrlNameInDlgAdd: 'add-dict-entity-name'
    ,idDlgEdit: 'dlg-edit-dict-entity'
    ,idCtrlNameInDlgEdit: 'edit-dict-entity-name'
    ,idCtrlIdInDlgEdit: 'edit-dict-entity-id'
    ,idDlgRemove: 'dlg-remove-dict-entity'
    ,idDivNameInDlgRemove: 'remove-dict-entity-name'
        
    ,selectedList: Array()
    ,ctrlsList: {}
    
    ,init: function() {
        dictEntitiesCtrl.initDlgAdd();
        dictEntitiesCtrl.initDlgEdit();
        dictEntitiesCtrl.initDlgRemove();
    }


    ,addCtrl: function(ctrlId, typeId)
    {
        dictEntitiesCtrl.ctrlsList[ctrlId] = typeId;
        
        dictEntitiesCtrl.reload(ctrlId);
        dictEntitiesCtrl.linkDynamicActionsToPharmDictCtrl(ctrlId);

    }

    ,initDlgAdd: function() {
        var dlgHtml = "<div id='" + dictEntitiesCtrl.idDlgAdd + "' title='" + i18n.translate("Add") + "' list_element_id=''>" +
                      " <p class='errorInfo'></p>" +
                      " <form>" +
                      "  <label for='name'>" + i18n.translate("Name") + "</label>" +
                      "  <input type='text' name='name' id='" + dictEntitiesCtrl.idCtrlNameInDlgAdd + "' class='text ui-widget-content ui-corner-all' size='56' />" +
                      " </form>" +
                      "</div>";
        
        $(dlgHtml).dialog({
            dialogClass: 'dialog-content'
            ,autoOpen: false
            ,height: 200
            ,width: 530
            ,modal: true
            ,buttons: [{
                text: i18n.translate("Add")
                ,click: function() {
                    var name = $('#' + dictEntitiesCtrl.idCtrlNameInDlgAdd);
                    if(!name.val()) {
                        name.addClass("ui-state-error");
                        dictEntitiesCtrl.updateErrorInfo('#'+dictEntitiesCtrl.idDlgAdd, i18n.translate("Necessary to enter value"));
                        return false;
                    }
                    var list_element_id = $('#'+dictEntitiesCtrl.idDlgAdd).attr('list_element_id');
                    var type_id = dictEntitiesCtrl.ctrlsList[list_element_id];

                    $('#'+dictEntitiesCtrl.idDlgAdd).showLoading();
                    $.ajax({
                        url: internalAddress.pharmDictCreate()
                        ,type:'POST'
                        ,data: {
                            name : name.val()
                            ,type_id: type_id
                        }
                        ,success: function(data) {
                            if (data && data.success){
                                $('#'+dictEntitiesCtrl.idDlgAdd).dialog("close");
                                dictEntitiesCtrl.reload(list_element_id);
                            }
                            else{
                                var errorMsg = i18n.translate("Internal Error");
                                if(data) {
                                    errorMsg = data.error;
                                }
                                dictEntitiesCtrl.updateErrorInfo('#'+dictEntitiesCtrl.idDlgAdd, errorMsg);
                            }
                        }
                        ,complete: function(jqXHR, textStatus) {
                            $('#'+dictEntitiesCtrl.idDlgAdd).hideLoading();
                        }
                    });
                } 
            }
            ,{
                 text: i18n.translate("Cancel")
                ,click: function() {
                    $('#'+dictEntitiesCtrl.idDlgAdd).dialog("close");
                } 
            }]
            ,close: function() {
            }
            ,open: function() {
                dictEntitiesCtrl.updateErrorInfo('#'+dictEntitiesCtrl.idDlgAdd, '');
                $('#'+dictEntitiesCtrl.idCtrlNameInDlgAdd).val("").removeClass("ui-state-error");
            }
        });
    }
    
    ,initDlgEdit: function() {
        var dlgHtml = "<div id='" + dictEntitiesCtrl.idDlgEdit + "' title='" + i18n.translate("Edit") + "' list_element_id=''>" +
                      " <p class='errorInfo'></p>" +
                      " <form>" +
                      "  <label for='name'>" + i18n.translate("Name") + "</label>" +
                      "  <input type='text' name='name' id='" + dictEntitiesCtrl.idCtrlNameInDlgEdit + "' class='text ui-widget-content ui-corner-all' size='56' />" +
                      "  <input type='hidden' name='id' id='" + dictEntitiesCtrl.idCtrlIdInDlgEdit + "' />" +
                      " </form>" +
                      "</div>";
        
        $(dlgHtml).dialog({
            dialogClass: 'dialog-content'
            ,autoOpen: false
            ,height: 200
            ,width: 530
            ,modal: true
            ,buttons: [{
                text: i18n.translate("Save")
                ,click: function() {
                    var name = $('#' + dictEntitiesCtrl.idCtrlNameInDlgEdit);
                    if(!name.val()) {
                        name.addClass("ui-state-error");
                        dictEntitiesCtrl.updateErrorInfo('#'+dictEntitiesCtrl.idDlgEdit, i18n.translate("Necessary to enter value"));
                        return false;
                    }
                    var id = $('#' + dictEntitiesCtrl.idCtrlIdInDlgEdit);
                    var list_element_id = $('#'+dictEntitiesCtrl.idDlgEdit).attr('list_element_id');

                    $('#'+dictEntitiesCtrl.idDlgEdit).showLoading();
                    $.ajax({
                        url: internalAddress.pharmDictChange()
                        ,type:'POST'
                        ,data: {
                             name : name.val()
                            ,id: id.val()
                        }
                        ,success: function(data) {
                            if (data && data.success){
                                $('#'+dictEntitiesCtrl.idDlgEdit).dialog("close");
                                dictEntitiesCtrl.reload(list_element_id);
                            }
                            else{
                                var errorMsg = i18n.translate("Internal Error");
                                if(data) {
                                    errorMsg = data.error;
                                }
                                dictEntitiesCtrl.updateErrorInfo('#'+dictEntitiesCtrl.idDlgEdit, errorMsg);
                            }
                        }
                        ,complete: function(jqXHR, textStatus) {
                            $('#'+dictEntitiesCtrl.idDlgEdit).hideLoading();
                        }
                    });
                } 
            }
            ,{
                 text: i18n.translate("Cancel")
                ,click: function() {
                    $('#'+dictEntitiesCtrl.idDlgEdit).dialog("close");
                } 
            }]
            ,close: function() {
            }
            ,open: function() {
                dictEntitiesCtrl.updateErrorInfo('#'+dictEntitiesCtrl.idDlgEdit, '');
                $('#'+dictEntitiesCtrl.idCtrlNameInDlgEdit).removeClass("ui-state-error");
            }
        });
    }
 
    ,initDlgRemove: function() {
        var dlgHtml = "<div id='" + dictEntitiesCtrl.idDlgRemove + "' title='" + i18n.translate("Warning") + "' list_element_id='' dict_id=''>" +
        " <p class='errorInfo'></p>" +
        " <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>" +
        i18n.translate("__NameDiv__ will be removed. Are You sure?", {NameDiv: "<div id='" + dictEntitiesCtrl.idDivNameInDlgRemove + "' style='font-weight: bold;'></div>"}) +
        "</div>";
        
        
        $(dlgHtml).dialog({
             modal: true
            ,dialogClass: 'alert'
            ,autoOpen: false
            ,width: 450
            ,buttons: [{
                text: 'OK'
                ,click: function() {
                    var list_element_id = $('#'+dictEntitiesCtrl.idDlgRemove).attr('list_element_id');
                    var dict_id = $('#'+dictEntitiesCtrl.idDlgRemove).attr('dict_id');
                    
                    $('#'+dictEntitiesCtrl.idDlgRemove).showLoading();
                    $.ajax({
                        url: internalAddress.pharmDictRemove()
                        ,type:'POST'
                        ,data: {
                            id : dict_id
                        }
                        ,success: function(data) {
                            if (data && data.success){
                                $('#'+dictEntitiesCtrl.idDlgRemove).dialog("close");
                                dictEntitiesCtrl.reload(list_element_id);
                            }
                            else{
                                var errorMsg = i18n.translate("Internal Error");
                                if(data) {
                                    errorMsg = data.error;
                                }
                                dictEntitiesCtrl.updateErrorInfo('#'+dictEntitiesCtrl.idDlgRemove, errorMsg);
                            }
                        }
                        ,complete: function(jqXHR, textStatus) {
                            $('#'+dictEntitiesCtrl.idDlgRemove).hideLoading();
                        }
                    });
                } 
            }
            ,{
                 text: i18n.translate("Cancel")
                ,click: function() {
                    $('#'+dictEntitiesCtrl.idDlgRemove).dialog("close");
                } 
            }]
            ,close: function() {
            }
            ,open: function() {
                dictEntitiesCtrl.updateErrorInfo('#'+dictEntitiesCtrl.idDlgEdit, '');
            }
        });
    }
    
    ,addSelected: function(type, values) {
        dictEntitiesCtrl.selectedList[type] = [values];
    }
    
    ,reload: function(ctrlId){
        dictTypeId = dictEntitiesCtrl.ctrlsList[ctrlId];
        
        if($("#"+ctrlId + " option").length) {
            dictEntitiesCtrl.selectedList[dictTypeId] = new Array();
            $("#"+ctrlId + " option:selected").each( function() {
                dictEntitiesCtrl.selectedList[dictTypeId][dictEntitiesCtrl.selectedList[dictTypeId].length] = parseInt($(this).val(), 10);
            });
        }
        $.ajax({
             url: internalAddress.pharmDictArray()
            ,type:'POST'
            ,data: {
                type_id: dictTypeId
            }
            ,success: function(data){
                if(!data || !data.success) {
                    return;
                }

                var select = $("#"+ctrlId);
                if(select.prop) {
                    var options = select.prop('options');
                }
                else {
                    var options = select.attr('options');
                }
                $('option', select).remove();
                for (var key in data.rows){
                    var selectedFlag = false;
                    if(-1 != jQuery.inArray(parseInt(key, 10), dictEntitiesCtrl.selectedList[dictTypeId])) { /* to check int with int */
                        selectedFlag = true;
                    }
                    options[options.length] = new Option(data.rows[key], key, selectedFlag);
                }
            }
            ,complete: function(jqXHR, textStatus) {
            }
        });
    }
    
    ,linkDynamicActionsToPharmDictCtrl: function(list_element_id)
    {
        $("#link-add-"+list_element_id).click(function() {
            $("#" + dictEntitiesCtrl.idDlgAdd).attr('list_element_id', list_element_id);
            $("#" + dictEntitiesCtrl.idDlgAdd).dialog('open');
            return false;
        });
        $('#'+list_element_id).contextMenu({
            selector: 'option', 
            callback: function(key, options) {
                dictEntitiesCtrl.processPharmDictContextMenuPressed(key, options, list_element_id);
            },
            items: {
                "edit": {name: "Edit"},
                "delete": {name: "Delete"},
            }
        });
    }

    ,processPharmDictContextMenuPressed: function(key, options, list_element_id)
    {
        var label = options.$trigger[0].label;
        var value = options.$trigger[0].value;

        if('edit' == key) {
            $("#" + dictEntitiesCtrl.idDlgEdit).attr('list_element_id', list_element_id);
            $("#" + dictEntitiesCtrl.idCtrlNameInDlgEdit).val(label)
            $("#" + dictEntitiesCtrl.idCtrlIdInDlgEdit).val(value)
            $("#" + dictEntitiesCtrl.idDlgEdit).dialog('open');
        }
        else if('delete' == key) {
            $("#" + dictEntitiesCtrl.idDlgRemove).attr('dict_id', value);
            $("#" + dictEntitiesCtrl.idDlgRemove).attr('list_element_id', list_element_id);
            $("#" + dictEntitiesCtrl.idDivNameInDlgRemove).html(label);
            $("#" + dictEntitiesCtrl.idDlgRemove).dialog('open');
       }
    }

    ,updateErrorInfo: function(dlgId, message) {
        var infoEl = $(dlgId + " .errorInfo");
        if('' != message) {
            infoEl.text(message).addClass("ui-state-highlight");
        }
        else {
            infoEl.text('').removeClass("ui-state-highlight");
        }
          
        setTimeout(function() {
            infoEl.removeClass("ui-state-highlight", 1500);
        }, 500 );
    }

    
}