var pageAsDlg = {
    linkFormToDlg: function(dialogId) {
    	var form = $(dialogId + '>form');
    	if(form) {
    		form.submit(function(event) {
    			event.preventDefault();
    			$(dialogId).showLoading();
    			$.ajax( {
    			     type: "POST"
    				,url: $(this).attr('action')
    				,data: $(this).serialize()
	                ,success: function(response) {
	        	        $(dialogId).html(response);
	        	        pageAsDlg.linkFormToDlg(dialogId);
	                }
    	            ,complete: function(jqXHR, textStatus) {
    	                $(dialogId).hideLoading();
    	            }
	            });			    	      
    		});
    	} 			    	  
    }

}