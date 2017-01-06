angular.module('trustrx.adr.report', [
    'ui.bootstrap'
]);


angular.module('trustrx.adr.report').run(["$templateCache", function($templateCache) {
    $templateCache.put("adr/report/new.tpl.html",
        "<div class=\"modal-content\">"+
            "    <div class=\"modal-header\">"+
            "        <button type=\"button\" class=\"close\" data-ng-click=\"cancel();\">&times;</button>"+
            "        <h4 class=\"modal-title\">{{translate('New Report')}}</h4>"+
            "    </div>"+
            "    <div class=\"modal-body\">"+
            "        <div class=\"alert alert-danger\" data-ng-show=\"formErrorMessage\">"+
            "            <span>{{formErrorMessage}}</span>"+
            "        </div>"+
            "        <form class=\"form-horizontal\" role=\"form\" name=\"infoForm\" novalidate>"+
            "            <fieldset>"+
            "                <legend>{{translate('Patient Information')}}</legend>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.patient_id.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"patient_id\">{{translate('Patient')}}</label>"+
            "                    <div class=\"col-sm-9\">"+
            "                        <my-select-patient with-add=\"false\" name=\"patient_id\" data-ng-model=\"params.patient_id\" required></my-select-patient>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.id_pharmacy.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"id_pharmacy\">{{translate('Pharmacy')}}</label>"+
            "                    <div class=\"col-sm-9\">"+
            "                        <select name=\"id_pharmacy\" id=\"id_pharmacy\" class=\"form-control\" data-ng-model=\"params.id_pharmacy\" data-ng-options=\"o.id as o.name for o in pharmacies | orderBy:'name'\" required>"+
            "                        </select>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.id_pharmacy.$dirty && infoForm.id_pharmacy.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.id_pharmacy.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+

            "            <div class=\"row\">"+
            "                <label class=\"col-sm-3 control-label\">{{translate('Access to Location')}}</label>"+
            "                <div class=\"col-sm-9\">"+
            "                    <div class=\"row\">"+
            "                        <div class=\"col-sm-5\"><strong>{{translate('Manager')}}</strong></div>"+
            "                        <div class=\"col-sm-5\"><strong>{{translate('Role')}}</strong></div>"+
            "                        <div class=\"col-sm-2\"></div>"+
            "                    </div>"+
            "                    <div class=\"row\" data-ng-repeat=\"manager in params.managers\">"+
            "                        <data-ng-form name=\"subForm\">"+
            "                            <div class=\"col-sm-5\" data-ng-class=\"{'has-error': subForm.user_id.$invalid}\">"+
            "                                <my-select-user with-add=\"true\" name=\"user_id\" data-ng-model=\"manager.user_id\" required></my-select-user>"+
            "                            </div>"+
            "                            <div class=\"col-sm-5\" data-ng-class=\"{'has-error': subForm.role_id.$invalid}\">"+
            "                                <select name=\"role_id\" class=\"form-control\" data-ng-model=\"manager.role_id\" data-ng-options=\"o.id as o.name for o in roles | orderBy:'name'\" required>"+
            "                                </select>"+
            "                                <div class=\"help-block\" data-ng-show=\"subForm.role_id.$dirty && subForm.role_id.$invalid\">"+
            "                                    <span data-ng-show=\"subForm.role_id.$error.required\">{{translate('Necessary to choose value')}}</span>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"col-sm-2\">"+
            "                                <button type=\"button\" class=\"btn btn-xs\" data-ng-attr-title=\"{{translate('Add')}}\" data-ng-click=\"addManager();\"><span class=\"glyphicon glyphicon-plus\"></span></button>"+
            "                                <button data-ng-show='$index > 0' =\"button\" class=\"btn btn-xs\" data-ng-attr-title=\"{{translate('Delete')}}\" data-ng-click=\"deleteManager($index);\"><span class=\"glyphicon glyphicon-minus\"></span></button>"+
            "                            </div>"+
            "                        </data-ng-form>"+
            "                    </div>"+
            "                    <div class=\"row\">"+
            "                        <div class=\"col-sm-12\"></div>"+
            "                    </div>"+
            "                </div>"+
            "            </div>"+
            "            <div class=\"row\">"+
            "                <div class=\"col-sm-offset-3 col-sm-9\">"+
            "                    <button type=\"button\" class=\"btn btn-primary\" data-ng-click=\"save();\" data-ng-disabled=\"infoForm.$invalid || isWaitAnswer()\">{{translate('Save')}}</button>"+
            "                </div>"+
            "            </div>"+
            "        </form>"+
            "    </div>"+
            "</div><!-- /.modal-content -->"
    );
}]);

