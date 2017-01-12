angular.module('trustrx.adr.services')

.factory('AdrInternalAddressSvc', function() {
    var webRoot = '',
        urlWithPatterns = '';

    var replacePatterns = function(controller, action) {
        var url = urlWithPatterns;
        url = url.replace(/__controller__/g, controller).replace(/__action__/g, action);
        return url;
    };

    return {
        setUrlBase: function (newUrlBase, newUrlWithPatters) {
            webRoot = newUrlBase.replace(/^\/index.php\//, "/");
            urlWithPatterns = newUrlWithPatters;
        },
        loadI18nDict: function() {
            return replacePatterns('language', 'load-dict-for-js') + '/language/__lng__/namespace/__ns__';
        },
        loadAppFile: function (file) {
            return webRoot + 'app/' + file;
        },
        loadFile: function (file) {
            return webRoot + file;
        },
        reportList: function (offset, quantity) {
            return replacePatterns('report', 'list') + "/offset/" + offset + "/quantity/" + quantity + "?_=" + Date.now().toString();
        },
        reportCreate: function() {
            return replacePatterns('report', 'create');
        },
        reportDelete: function(id) {
            return replacePatterns('report', 'delete') + '/id/' + id;
        },
        reportDownload: function(id) {
            return replacePatterns('report', 'download') + '/id/' + id;
        },
        reportAttrLists: function() {
            return replacePatterns('report', 'attr-lists');
        },

        patientGet: function(id) {
            return replacePatterns('patient', 'get') + '/id/' + id;
        },
        patientFilterAccessable: function() {
            return replacePatterns('patient', 'filter-accessable');
        },

        pharmacyListActive: function () {
            return replacePatterns('pharmacy', 'list-active');
        }
    };
});

