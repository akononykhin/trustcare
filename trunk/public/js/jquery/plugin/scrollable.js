
function ScrollableTable (tableEl, tableHeight, tableWidth) {

    //alert("Scrolltable work2");

    this.initIEengine = function () {

        //alert("IE");
        this.containerEl.style.overflowY = 'auto';
        if (this.tableEl.parentElement.clientHeight - this.tableEl.offsetHeight < 0) {
            this.tableEl.style.width = this.newWidth - this.scrollWidth +'px';
        } else {
            this.containerEl.style.overflowY = 'hidden';
            this.tableEl.style.width = this.newWidth +'px';
        }

        if (this.thead) {
            var trs = this.thead.getElementsByTagName('tr');
            for (x=0; x<trs.length; x++) {
                trs[x].style.position ='relative';
                trs[x].style.top.setExpression = "this.offsetParent.scrollTop";
                //trs[x].style.top=this.parentElement.parentElement.parentElement.scrollTop;
            }
        }
        /*
        if (this.tfoot) {
            var trs = this.tfoot.getElementsByTagName('tr');
            for (x=0; x<trs.length; x++) {
                trs[x].style.position ='relative';
                //trs[x].style.top.setExpression = "this.offsetParent.scrollTop";
                //trs[x].style.setExpression("bottom",  "(this.parentElement.parentElement.offsetHeight - this.parentElement.parentElement.parentElement.clientHeight - this.parentElement.parentElement.parentElement.scrollTop) + 'px'");
            }
        }
*/
        if (!this.tableEl.id) {
            this.tableEl.id = 'id' + Math.floor(Math.random()*1000);
        }
        eval("window.attachEvent('onresize', function () { document.getElementById('" + this.tableEl.id + "').style.visibility = 'hidden'; document.getElementById('" + this.tableEl.id + "').style.visibility = 'visible'; } )");
    };


    this.initFFengine = function () {
       // alert("FF2");
        this.containerEl.style.overflow = 'hidden';
        this.tableEl.style.width = this.newWidth + 'px';

        var headHeight = (this.thead) ? this.thead.clientHeight : 0;
        var footHeight = (this.tfoot) ? this.tfoot.clientHeight : 0;
        var bodyHeight = this.tbody.clientHeight;
        var trs = this.tbody.getElementsByTagName('tr');
        if (bodyHeight >= (this.newHeight - (headHeight + footHeight))) {
            
            this.tbody.style.overflow = '-moz-scrollbars-vertical';

            for (x=0; x<trs.length; x++) {
                var tds = trs[x].getElementsByTagName('td');
                tds[tds.length-1].style.paddingRight += this.scrollWidth + 'px';
            }

        } else {
            this.tbody.style.overflow = '-moz-scrollbars-none';
        }

        var cellSpacing = (this.tableEl.offsetHeight - (this.tbody.clientHeight + headHeight + footHeight)) / 4;
        //this.tbody.style.height = this.newHeight-50+'px';
        //50 px is for filtering is it wrong
         this.tbody.style.height = (this.newHeight -50- (headHeight + cellSpacing * 2) - (footHeight + cellSpacing * 2)) + 'px';
    };

    this.initFF4engine = function () {
        this.containerEl.style.overflow = 'auto';
    };


    this.initOPengine = function () {
        //alert("OPera");
        this.containerEl.style.overflow = 'auto';
    };

    this.initChromeengine = function () {
        //alert("Chrome");
        this.containerEl.style.overflow = 'auto';
    };

    //alert("Browser"+$.browser.msie);

    this.tableEl = tableEl;
    this.scrollWidth = 17;

    this.originalHeight = this.tableEl.clientHeight;
    this.originalWidth = this.tableEl.clientWidth;

    this.newHeight = parseInt(tableHeight);
    this.newWidth = tableWidth ? parseInt(tableWidth) : this.originalWidth;

    this.tableEl.style.height = 'auto';
    this.tableEl.removeAttribute('height');

    this.containerEl = this.tableEl.parentNode.insertBefore(document.createElement('div'), this.tableEl);
    this.containerEl.appendChild(this.tableEl);
    //alert(this.newHeight);
    this.containerEl.style.height = this.newHeight + 'px';
    this.containerEl.style.width = this.newWidth + 'px';

    var thead = this.tableEl.getElementsByTagName('thead');
    this.thead = (thead[0]) ? thead[0] : null;

    var tfoot = this.tableEl.getElementsByTagName('tfoot');
    this.tfoot = (tfoot[0]) ? tfoot[0] : null;

    var tbody = this.tableEl.getElementsByTagName('tbody');
    this.tbody = (tbody[0]) ? tbody[0] : null;

    if (!this.tbody) return;

    if (!document.all && document.getElementById && !window.opera && !$.browser.mozilla) {this.initChromeengine();}
    if (document.all && document.getElementById && !window.opera) {this.initIEengine();}
    if (!document.all && document.getElementById && !window.opera && $.browser.mozilla) {
      if(parseInt($.browser.version, 10) < 2) {
        this.initFFengine();
      }
      else {
        this.initFF4engine();
      }
    }
    if (!document.all && document.getElementById && window.opera) {this.initOPengine();}

}

/**
 * Created by IntelliJ IDEA.
 * User: stepchik
 * Date: 03.02.2011
 * Time: 12:23:10
 * To change this template use File | Settings | File Templates.
 */
