var dictEntitiesCtrl = {
    selectedList: Array()
    
    ,addSelected: function(type, values) {
        dictEntitiesCtrl.selectedList[type] = [values];
    }
    
    ,reload: function(ctrlId, dictTypeId){
        if($(ctrlId + " option").length) {
            dictEntitiesCtrl.selectedList[dictTypeId] = new Array();
            $(ctrlId + " option:selected").each( function() {
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

                var select = $(ctrlId);
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
}