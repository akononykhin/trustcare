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
        reportsList: function (offset, quantity) {
            return replacePatterns('reports', 'list') + "/offset/" + offset + "/quantity/" + quantity + "?_=" + Date.now().toString();
        }
    };
});

