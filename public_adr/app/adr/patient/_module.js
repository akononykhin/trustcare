angular.module('trustrx.adr.patient', [
    'ui.bootstrap'
]);



angular.module('trustrx.adr.patient').run(["$templateCache", function($templateCache) {
    $templateCache.put("adr/patient/info.tpl.html",
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
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.identifier.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"identifier\">{{translate('Client ID')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" name=\"identifier\" id=\"identifier\" data-ng-model=\"params.identifier\" required>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.identifier.$dirty && infoForm.identifier.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.identifier.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"is_active\">{{translate('Is Active')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"checkbox\" name=\"is_active\" id=\"is_active\" data-ng-model=\"params.is_active\">"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.is_male.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"is_male\">{{translate('Gender')}}</label>"+
            "                <div class=\"col-sm-4\">"+
            "                    <select name=\"is_male\" id=\"is_male\" class=\"form-control\" data-ng-model=\"params.is_male\" data-ng-options=\"o.id as o.name for o in genders\">"+
            "                    </select>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.first_name.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"first_name\">{{translate('First Name')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" name=\"first_name\" id=\"first_name\" data-ng-model=\"params.first_name\" required>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.first_name.$dirty && infoForm.first_name.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.first_name.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.last_name.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"last_name\">{{translate('Last Name')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" name=\"last_name\" id=\"last_name\" data-ng-model=\"params.last_name\" required>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.last_name.$dirty && infoForm.last_name.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.last_name.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.id_physician.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"id_physician\">{{translate('Physician')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <select name=\"id_physician\" id=\"id_physician\" class=\"form-control\" data-ng-model=\"params.id_physician\" data-ng-options=\"o.id as o.name for o in physicians | orderBy:'name'\">"+
            "                        <option value=\"\"></option>"+
            "                    </select>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.id_physician.$dirty && infoForm.id_physician.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.id_physician.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.birthdate.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"birthdate\">{{translate('Date of Birth')}}</label>"+
            "                <div class=\"col-sm-4\">"+
            "                    <p class=\"input-group\">"+
            "                        <input type=\"text\" name=\"birthdate\" class=\"form-control\" readonly=\"readonly\" uib-datepicker-popup=\"yyyy-MM-dd\" ng-model=\"params.birthdate\" is-open=\"calendar.birthdate_opened\" datepicker-options=\"dateOptions\" required />"+
            "                        <span class=\"input-group-btn\">"+
            "                           <button type=\"button\" class=\"btn btn-default\" ng-click=\"calendar_open('birthdate')\"><i class=\"glyphicon glyphicon-calendar\"></i></button>"+
            "                        </span>"+
            "                    </p>"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.birthdate.$dirty && infoForm.birthdate.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.birthdate.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
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
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.city.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"city\">{{translate('City')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" name=\"city\" id=\"city\" data-ng-model=\"params.city\">"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.city.$dirty && infoForm.city.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.city.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.phone.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"address\">{{translate('Address')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" name=\"address\" id=\"address\" data-ng-model=\"params.address\">"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.address.$dirty && infoForm.address.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.address.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.zip.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"zip\">{{translate('Zip')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" name=\"zip\" id=\"zip\" data-ng-model=\"params.zip\">"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.zip.$dirty && infoForm.zip.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.zip.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.phone.$invalid}\">"+
            "                <label class=\"col-sm-3 control-label\" for=\"phone\">{{translate('Phone')}}</label>"+
            "                <div class=\"col-sm-8\">"+
            "                    <input type=\"text\" class=\"form-control\" name=\"phone\" id=\"phone\" data-ng-model=\"params.phone\">"+
            "                    <div class=\"help-block\" data-ng-show=\"infoForm.phone.$dirty && infoForm.phone.$invalid\">"+
            "                        <span data-ng-show=\"infoForm.phone.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
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

