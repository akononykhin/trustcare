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
        reportGet: function(id) {
            return replacePatterns('report', 'get') + '/id/' + id;
        },
        reportDownload: function(id) {
            return replacePatterns('report', 'download') + '/id/' + id;
        },
        reportRegenerate: function(id) {
            return replacePatterns('report', 'regenerate') + '/id/' + id;
        },
        reportAttrLists: function() {
            return replacePatterns('report', 'attr-lists');
        },

        patientCreate: function () {
            return replacePatterns('patient', 'create');
        },
        patientSave: function (id) {
            return replacePatterns('patiant', 'save') + '/id/' + id;
        },
        patientGet: function(id) {
            return replacePatterns('patient', 'get') + '/id/' + id;
        },
        patientFilterAccessable: function() {
            return replacePatterns('patient', 'filter-accessable');
        },

        pharmacyListActive: function () {
            return replacePatterns('pharmacy', 'list-active');
        },
        pharmacyCreate: function () {
            return replacePatterns('pharmacy', 'create');
        },
        pharmacySave: function (id) {
            return replacePatterns('pharmacy', 'save') + '/id/' + id;
        },

        countryList: function () {
            return replacePatterns('country', 'list');
        },
        stateList: function (country_id) {
            return replacePatterns('state', 'list') + '/country_id/' + country_id;
        },
        lgaList: function (state_id) {
            return replacePatterns('lga', 'list') + '/state_id/' + state_id;
        },
        facilityList: function (lga_id) {
            return replacePatterns('facility', 'list') + '/lga_id/' + lga_id;
        },
        physicianList: function() {
            return replacePatterns('physician', 'list');
        }
    };
});

