angular.module('trustrx.common.filters')
.filter('nl2br', function() {
        return function(input) {
            var is_xhtml = true;
            var breakTag = (is_xhtml) ? '<br />' : '<br>';
            var text = (input + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
            return text;
        };
});