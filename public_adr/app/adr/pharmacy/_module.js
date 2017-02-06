angular.module('trustrx.adr.pharmacy', [
    'ui.bootstrap'
]);



angular.module('trustrx.adr.pharmacy').run(["$templateCache", function($templateCache) {
    $templateCache.put("adr/pharmacy/info.tpl.html",
        "<div class=\"modal-content\">"+
            "    <div class=\"modal-header\">"+
            "        <button type=\"button\" class=\"close\" data-ng-click=\"cancel();\">&times;</button>"+
            "        <h4 class=\"modal-title\">{{getTitle()}}</h4>"+
            "    </div>"+
            "    <div class=\"modal-body\">"+
            "        <div class=\"alert alert-danger\" data-ng-show=\"formErrorMessage\">"+
            "            <span>{{formErrorMessage}}</span>"+
            "        </div>"+
            "        <form class=\"form-horizontal\" role=\"form\" name=\"infoForm\" novalidate>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.name.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"name\">{{translate('Title')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" name=\"name\" id=\"name\" data-ng-model=\"params.name\" required>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.name.$dirty && infoForm.name.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.name.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"is_active\">{{translate('Is Active')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"checkbox\" name=\"is_active\" id=\"is_active\" data-ng-model=\"params.is_active\">"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.id_country.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"id_country\">{{translate('Country')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <select name=\"id_country\" id=\"id_country\" class=\"form-control\" data-ng-model=\"params.id_country\" data-ng-options=\"o.id as o.name for o in countries | orderBy:'name'\" data-ng-change=\"loadStates()\">"+
            "                        <option value=\"\"></option>"+
            "                    </select>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.id_country.$dirty && infoForm.id_country.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.id_country.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.id_state.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"id_state\">{{translate('State')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <select name=\"id_state\" id=\"id_state\" class=\"form-control\" data-ng-model=\"params.id_state\" data-ng-options=\"o.id as o.name for o in states | orderBy:'name'\" data-ng-change=\"loadLga()\">"+
            "                        <option value=\"\"></option>"+
            "                    </select>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.id_state.$dirty && infoForm.id_state.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.id_state.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.address.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"address\">{{translate('Address')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" address=\"address\" id=\"address\" data-ng-model=\"params.address\">"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.address.$dirty && infoForm.address.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.address.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.id_lga.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"id_lga\">{{translate('LGA')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <select name=\"id_lga\" id=\"id_lga\" class=\"form-control\" data-ng-model=\"params.id_lga\" data-ng-options=\"o.id as o.name for o in lgas | orderBy:'name'\" data-ng-change=\"loadFacilities()\">"+
            "                        <option value=\"\"></option>"+
            "                    </select>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.id_lga.$dirty && infoForm.id_lga.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.id_lga.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.id_facility.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"id_facility\">{{translate('Facility')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <select name=\"id_facility\" id=\"id_facility\" class=\"form-control\" data-ng-model=\"params.id_facility\" data-ng-options=\"o.id as o.name for o in facilities | orderBy:'name'\" required>"+
            "                        <option value=\"\"></option>"+
            "                    </select>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.id_facility.$dirty && infoForm.id_facility.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.id_facility.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"row\">"+
            "                <div class=\"col-sm-offset-3 col-sm-8\">"+
            "                    <button type=\"button\" class=\"btn btn-primary\" data-ng-click=\"save();\" data-ng-disabled=\"infoForm.$invalid || isWaitAnswer()\">{{translate('Save')}}</button>"+
            "                </div>"+
            "            </div>"+
            "        </form>"+
            "    </div>"+
            "</div><!-- /.modal-content -->"
    );
}]);

