/**
 * Created by JetBrains PhpStorm.
 * User: stepchik
 * Date: 10.02.11
 * Time: 16:37
 * To change this template use File | Settings | File Templates.
 */
     jQuery.fn.Scrollable = function(tableHeight, tableWidth) {
	 this.each(function(){
			var table = new ScrollableTable(this, tableHeight, tableWidth);
	 });

     };

     $(document).ready(function() {
          //alert(jQuery.browser);
          $('table.dnis').Scrollable(200, 200);
          $('table.dnis').columnFilters();
     });