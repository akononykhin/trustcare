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
            "                    <div class=\"col-sm-6\">"+
            "                        <my-select-patient with-add=\"false\" name=\"patient_id\" data-ng-model=\"params.patient_id\" required></my-select-patient>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.id_pharmacy.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"id_pharmacy\">{{translate('Pharmacy')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <select name=\"id_pharmacy\" id=\"id_pharmacy\" class=\"form-control\" data-ng-model=\"params.id_pharmacy\" data-ng-options=\"o.id as o.name for o in pharmacies | orderBy:'name'\" required>"+
            "                        </select>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.id_pharmacy.$dirty && infoForm.id_pharmacy.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.id_pharmacy.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.date_of_visit.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"date_of_visit\">{{translate('Date of Visit')}}</label>"+
            "                    <div class=\"col-sm-3\">"+
            "                        <p class=\"input-group\">"+
            "                            <input type=\"text\" name=\"date_of_visit\" class=\"form-control\" readonly=\"readonly\" uib-datepicker-popup=\"yyyy-MM-dd\" ng-model=\"params.date_of_visit\" is-open=\"calendar.dov_opened\" datepicker-options=\"dateOptions\" required />"+
            "                            <span class=\"input-group-btn\">"+
            "                               <button type=\"button\" class=\"btn btn-default\" ng-click=\"calendar_open('dov')\"><i class=\"glyphicon glyphicon-calendar\"></i></button>"+
            "                            </span>"+
            "                        </p>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.date_of_visit.$dirty && infoForm.date_of_visit.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.date_of_visit.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Adverse Drug Reaction')}}</legend>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.adr_description.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"adr_description\">{{translate('Description')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <textarea autocomplete=\"off\" class=\"form-control\" name=\"adr_description\" id=\"adr_description\" data-ng-model=\"params.adr_description\" ng-trim=\"true\" required/>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.adr_description.$dirty && infoForm.adr_description.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.adr_description.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"row form-group\"  data-ng-class=\"{'has-error': infoForm.onset_time.$invalid || infoForm.onset_type.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\">{{translate('Time to onset of reaction')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <div class=\"row\">"+
            "                            <div class=\"col-sm-2\">"+
            "                                <input type=\"text\" class=\"form-control\" name=\"onset_time\" id=\"onset_time\" data-ng-model=\"params.onset_time\" data-ng-required=\"isOnsetTimeRequired()\" data-ng-pattern=\"/^[1-9]\\d*$/\"/>"+
            "                            </div>"+
            "                            <div class=\"col-sm-4\">"+
            "                                <select name=\"onset_type\" id=\"onset_type\" class=\"form-control\" data-ng-model=\"params.onset_type\" data-ng-options=\"o.value as o.name for o in onsetTypes\" data-ng-required=\"isOnsetTypeRequired()\">"+
            "                                    <option value=\"\"></option>"+
            "                                </select>"+
            "                            </div>"+
            "                        </div>"+
            "                        <div class=\"row\" data-ng-show=\"infoForm.onset_time.$invalid || infoForm.onset_type.$invalid\">"+
            "                            <div class=\"col-sm-12\">"+
            "                                <div class=\"help-block\" data-ng-show=\"infoForm.onset_time.$invalid\" data-ng-messages=\"infoForm.onset_time.$error\">"+
            "                                    <span data-ng-message=\"required\">{{translate('Necessary to enter time.')}}</span>"+
            "                                    <span data-ng-message=\"pattern\">{{translate('Necessary to enter a number.')}}</span>"+
            "                                </div>"+
            "                                <div class=\"help-block\" data-ng-show=\"infoForm.onset_type.$invalid\" data-ng-messages=\"infoForm.onset_type.$error\">"+
            "                                    <span data-ng-message=\"required\">{{translate('Necessary to choose type of the time.')}}</span>"+
            "                                </div>"+
            "                            </div>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.adr_start_date.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"adr_start_date\">{{translate('Date start of reaction')}}</label>"+
            "                    <div class=\"col-sm-3\">"+
            "                        <p class=\"input-group\">"+
            "                            <input type=\"text\" name=\"adr_start_date\" class=\"form-control\" readonly=\"readonly\" uib-datepicker-popup=\"yyyy-MM-dd\" ng-model=\"params.adr_start_date\" is-open=\"calendar.adr_start_date_opened\" datepicker-options=\"dateOptions\" />"+
            "                            <span class=\"input-group-btn\">"+
            "                               <button type=\"button\" class=\"btn btn-default\" ng-click=\"calendar_open('adr_start_date')\"><i class=\"glyphicon glyphicon-calendar\"></i></button>"+
            "                            </span>"+
            "                        </p>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.adr_start_date.$dirty && infoForm.adr_start_date.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.adr_start_date.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.adr_stop_date.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"adr_stop_date\">{{translate('Date end of reaction')}}</label>"+
            "                    <div class=\"col-sm-3\">"+
            "                        <p class=\"input-group\">"+
            "                            <input type=\"text\" name=\"adr_stop_date\" class=\"form-control\" readonly=\"readonly\" uib-datepicker-popup=\"yyyy-MM-dd\" ng-model=\"params.adr_stop_date\" is-open=\"calendar.adr_stop_date_opened\" datepicker-options=\"dateOptions\" />"+
            "                            <span class=\"input-group-btn\">"+
            "                               <button type=\"button\" class=\"btn btn-default\" ng-click=\"calendar_open('adr_stop_date')\"><i class=\"glyphicon glyphicon-calendar\"></i></button>"+
            "                            </span>"+
            "                        </p>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.adr_stop_date.$dirty && infoForm.adr_stop_date.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.adr_stop_date.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.subsided.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"subsided\">{{translate('Reaction subsided after stopping drug / reducing dose')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <select name=\"subsided\" id=\"subsided\" class=\"form-control\" data-ng-model=\"params.subsided\" data-ng-options=\"key as value for (key , value) in subsidedList\">"+
            "                        </select>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.subsided.$dirty && infoForm.subsided.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.subsided.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reappeared.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"reappeared\">{{translate('Reaction reappeared after reintroducing drug')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <select name=\"reappeared\" id=\"reappeared\" class=\"form-control\" data-ng-model=\"params.reappeared\" data-ng-options=\"key as value for (key , value) in reappearedList\">"+
            "                        </select>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.reappeared.$dirty && infoForm.reappeared.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.reappeared.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.extent.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"extent\">{{translate('Extent of reaction')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <select name=\"extent\" id=\"extent\" class=\"form-control\" data-ng-model=\"params.extent\" data-ng-options=\"key as value for (key , value) in extentList\">"+
            "                        </select>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.extent.$dirty && infoForm.extent.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.extent.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.seriousness.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"seriousness\">{{translate('Seriousness of reaction')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <select name=\"seriousness\" id=\"seriousness\" class=\"form-control\" data-ng-model=\"params.seriousness\" data-ng-options=\"key as value for (key , value) in seriousnessList\">"+
            "                        </select>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.seriousness.$dirty && infoForm.seriousness.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.seriousness.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.treatment_of_reaction.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"treatment_of_reaction\">{{translate('Treatment of adverse reaction & action taken')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <input type=\"text\" class=\"form-control\" name=\"treatment_of_reaction\" id=\"treatment_of_reaction\" data-ng-model=\"params.treatment_of_reaction\"/>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.outcome_of_reaction_type.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"outcome_of_reaction_type\">{{translate('Outcome')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <div class=\"row\">"+
            "                            <div class=\"col-sm-4\">"+
            "                                <select name=\"outcome_of_reaction_type\" id=\"outcome_of_reaction_type\" class=\"form-control\" data-ng-model=\"params.outcome_of_reaction_type\" data-ng-options=\"key as value for (key , value) in outcomeList\">"+
            "                                </select>"+
            "                            </div>"+
            "                        </div>"+
            "                        <div class=\"row\" data-ng-show=\"showDeathComment()\">"+
            "                            <div class=\"col-sm-12\">"+
            "                                <input type=\"text\" class=\"form-control\" name=\"outcome_of_reaction_desc\" id=\"outcome_of_reaction_desc\" data-ng-model=\"params.outcome_of_reaction_desc\" placeholder=\"{{translate('Date & Cause of death')}}\"/>"+
            "                            </div>"+
            "                        </div>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.outcome.$dirty && infoForm.outcome.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.outcome.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.relationship.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"relationship\">{{translate('Drug-Reaction Relationship')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <select name=\"relationship\" id=\"relationship\" class=\"form-control\" data-ng-model=\"params.relationship\" data-ng-options=\"key as value for (key , value) in relationshipList\">"+
            "                        </select>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.relationship.$dirty && infoForm.relationship.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.relationship.$error.required\">{{translate('Necessary to choose value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.was_admitted.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"was_admitted\">{{translate('Was Patient Admitted Due to ADR?')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        {{translate('Yes')}}<input type=\"radio\" data-ng-model=\"params.was_admitted\" data-ng-value=\"true\">"+
            "                        {{translate('No')}}<input type=\"radio\" data-ng-model=\"params.was_admitted\" data-ng-value=\"false\">"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.was_hospitalization_prolonged.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"was_hospitalization_prolonged\">{{translate('If Already Hospitalized, Was it Prolonged Due to ADR?')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        {{translate('Yes')}}<input type=\"radio\" data-ng-model=\"params.was_hospitalization_prolonged\" data-ng-value=\"true\">"+
            "                        {{translate('No')}}<input type=\"radio\" data-ng-model=\"params.was_hospitalization_prolonged\" data-ng-value=\"false\">"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.duration_of_admission.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"duration_of_admission\">{{translate('Duration of Admission (days)')}}</label>"+
            "                    <div class=\"col-sm-9\">"+
            "                        <div class=\"row\">"+
            "                            <div class=\"col-sm-2\">"+
            "                                <input type=\"text\" class=\"form-control\" name=\"duration_of_admission\" id=\"duration_of_admission\" data-ng-model=\"params.duration_of_admission\" data-ng-pattern=\"/^[1-9]\\d*$/\"/>"+
            "                            </div>"+
            "                        </div>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.duration_of_admission.$dirty && infoForm.duration_of_admission.$invalid\" data-ng-messages=\"infoForm.duration_of_admission.$error\">"+
            "                            <span data-ng-message=\"pattern\">{{translate('Necessary to enter a number.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Suspected Drug')}}</legend>"+
            "                <div  data-ng-repeat=\"drug in params.suspected_drugs\">"+
            "                    <div class=\"col-sm-2\">"+
            "                        <button type=\"button\" class=\"btn btn-default\" data-ng-click=\"addSuspectedDrug();\"><i class=\"glyphicon glyphicon-plus\"></i></button>"+
            "                        <button type=\"button\" class=\"btn btn-default\" data-ng-click=\"removeSuspectedDrug($index);\" data-ng-show=\"0 != $index\"><i class=\"glyphicon glyphicon-trash\"></i></button>"+
            "                    </div>"+
            "                    <div class=\"col-sm-10\">"+
            "                        <data-ng-form name=\"subForm\">"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.brand_name.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"brand_name\">{{translate('Brand Name')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"brand_name\" data-ng-model=\"drug.brand_name\" required>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.brand_name.$dirty && subForm.brand_name.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.brand_name.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.generic_name.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"generic_name\">{{translate('Generic Name')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"generic_name\" data-ng-model=\"drug.generic_name\" required>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.generic_name.$dirty && subForm.generic_name.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.generic_name.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.batch_number.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"batch_number\">{{translate('Batch No.')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"batch_number\" data-ng-model=\"drug.batch_number\" required>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.batch_number.$dirty && subForm.batch_number.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.batch_number.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.nafdac_number.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"nafdac_number\">{{translate('NAFDAC No.')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"nafdac_number\" data-ng-model=\"drug.nafdac_number\">"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.nafdac_number.$dirty && subForm.nafdac_number.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.nafdac_number.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.expiry_date.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"expiry_date\">{{translate('Expiry Date')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"expiry_date\" data-ng-model=\"drug.expiry_date\">"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.expiry_date.$dirty && subForm.expiry_date.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.expiry_date.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.manufactor.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"manufactor\">{{translate('Name & Address of Manufacturer')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"manufactor\" data-ng-model=\"drug.manufactor\">"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.manufactor.$dirty && subForm.manufactor.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.manufactor.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.indication_for_use.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"indication_for_use\">{{translate('Indication for Use')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"indication_for_use\" data-ng-model=\"drug.indication_for_use\" required>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.indication_for_use.$dirty && subForm.indication_for_use.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.indication_for_use.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.dosage.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"dosage\">{{translate('Dosage')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"dosage\" data-ng-model=\"drug.dosage\" required>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.dosage.$dirty && subForm.dosage.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.dosage.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.route_of_administration.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"route_of_administration\">{{translate('Route of Administration')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"route_of_administration\" data-ng-model=\"drug.route_of_administration\">"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.route_of_administration.$dirty && subForm.route_of_administration.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.route_of_administration.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.date_started.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"date_started\">{{translate('Date started')}}</label>"+
            "                                <div class=\"col-sm-3\">"+
            "                                    <p class=\"input-group\">"+
            "                                        <input type=\"text\" name=\"date_started\" class=\"form-control\" readonly=\"readonly\" uib-datepicker-popup=\"yyyy-MM-dd\" data-ng-model=\"drug.date_started\" is-open=\"drug.date_started_opened\" datepicker-options=\"dateOptions\" />"+
            "                                        <span class=\"input-group-btn\">"+
            "                                            <button type=\"button\" class=\"btn btn-default\" ng-click=\"suspected_calendar_open('date_started', $index)\"><i class=\"glyphicon glyphicon-calendar\"></i></button>"+
            "                                        </span>"+
            "                                    </p>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.date_started.$dirty && subForm.date_started.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.date_started.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.date_stopped.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"date_stopped\">{{translate('Date stopped')}}</label>"+
            "                                <div class=\"col-sm-3\">"+
            "                                    <p class=\"input-group\">"+
            "                                        <input type=\"text\" name=\"date_stopped\" class=\"form-control\" readonly=\"readonly\" uib-datepicker-popup=\"yyyy-MM-dd\" data-ng-model=\"drug.date_stopped\" is-open=\"drug.date_stopped_opened\" datepicker-options=\"dateOptions\" />"+
            "                                        <span class=\"input-group-btn\">"+
            "                                            <button type=\"button\" class=\"btn btn-default\" ng-click=\"suspected_calendar_open('date_stopped', $index)\"><i class=\"glyphicon glyphicon-calendar\"></i></button>"+
            "                                        </span>"+
            "                                    </p>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.date_stopped.$dirty && subForm.date_stopped.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.date_stopped.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                        </data-ng-form>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Concomitant Drug')}}</legend>"+
            "                <div  data-ng-repeat=\"drug in params.concomitant_drugs\">"+
            "                    <div class=\"col-sm-2\">"+
            "                        <button type=\"button\" class=\"btn btn-default\" data-ng-click=\"addConcomitantDrug();\"><i class=\"glyphicon glyphicon-plus\"></i></button>"+
            "                        <button type=\"button\" class=\"btn btn-default\" data-ng-click=\"removeConcomitantDrug($index);\" data-ng-show=\"0 != $index\"><i class=\"glyphicon glyphicon-trash\"></i></button>"+
            "                    </div>"+
            "                    <div class=\"col-sm-10\">"+
            "                        <data-ng-form name=\"subForm\">"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.generic_name.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"generic_name\">{{translate('Brand or Generic Name')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"generic_name\" data-ng-model=\"drug.generic_name\">"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.generic_name.$dirty && subForm.generic_name.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.generic_name.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.dosage.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"dosage\">{{translate('Dosage')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"dosage\" data-ng-model=\"drug.dosage\">"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.dosage.$dirty && subForm.dosage.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.dosage.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.batch_number.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"batch_number\">{{translate('Route')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"batch_number\" data-ng-model=\"drug.batch_number\">"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.batch_number.$dirty && subForm.batch_number.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.batch_number.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.date_started.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"date_started\">{{translate('Date started')}}</label>"+
            "                                <div class=\"col-sm-3\">"+
            "                                    <p class=\"input-group\">"+
            "                                        <input type=\"text\" name=\"date_started\" class=\"form-control\" readonly=\"readonly\" uib-datepicker-popup=\"yyyy-MM-dd\" data-ng-model=\"drug.date_started\" is-open=\"drug.date_started_opened\" datepicker-options=\"dateOptions\" />"+
            "                                        <span class=\"input-group-btn\">"+
            "                                            <button type=\"button\" class=\"btn btn-default\" ng-click=\"concomitant_calendar_open('date_started', $index)\"><i class=\"glyphicon glyphicon-calendar\"></i></button>"+
            "                                        </span>"+
            "                                    </p>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.date_started.$dirty && subForm.date_started.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.date_started.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.date_stopped.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"date_stopped\">{{translate('Date stopped')}}</label>"+
            "                                <div class=\"col-sm-3\">"+
            "                                    <p class=\"input-group\">"+
            "                                        <input type=\"text\" name=\"date_stopped\" class=\"form-control\" readonly=\"readonly\" uib-datepicker-popup=\"yyyy-MM-dd\" data-ng-model=\"drug.date_stopped\" is-open=\"drug.date_stopped_opened\" datepicker-options=\"dateOptions\" />"+
            "                                        <span class=\"input-group-btn\">"+
            "                                            <button type=\"button\" class=\"btn btn-default\" ng-click=\"concomitant_calendar_open('date_stopped', $index)\"><i class=\"glyphicon glyphicon-calendar\"></i></button>"+
            "                                        </span>"+
            "                                    </p>"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.date_stopped.$dirty && subForm.date_stopped.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.date_stopped.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.indication_for_use.$invalid}\">"+
            "                                <label class=\"col-sm-3 control-label\" for=\"indication_for_use\">{{translate('Reason for Use')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <input type=\"text\" class=\"form-control\" name=\"indication_for_use\" data-ng-model=\"drug.indication_for_use\">"+
            "                                    <div class=\"help-block\" data-ng-show=\"subForm.indication_for_use.$dirty && subForm.indication_for_use.$invalid\">"+
            "                                        <span data-ng-show=\"subForm.indication_for_use.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                                    </div>"+
            "                                </div>"+
            "                            </div>"+
            "                        </data-ng-form>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Relevant Information')}}</legend>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.relevant_data.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"relevant_data\">{{translate('Relevant Investigations / Laboratory Data')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <textarea autocomplete=\"off\" class=\"form-control\" name=\"relevant_data\" id=\"relevant_data\" data-ng-model=\"params.relevant_data\" ng-trim=\"true\"/>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.relevant_data.$dirty && infoForm.relevant_data.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.relevant_data.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.relevant_history.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"relevant_history\">{{translate('Relevant Medical History')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <textarea autocomplete=\"off\" class=\"form-control\" name=\"relevant_history\" id=\"relevant_history\" data-ng-model=\"params.relevant_history\" ng-trim=\"true\"/>"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.relevant_history.$dirty && infoForm.relevant_history.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.relevant_history.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Reporter Details')}}</legend>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_name.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"reporter_name\">{{translate('Name')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <input type=\"text\" name=\"reporter_name\" class=\"form-control\" ng-model=\"params.reporter_name\" required />"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.reporter_name.$dirty && infoForm.reporter_name.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.reporter_name.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_address.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"reporter_address\">{{translate('Address')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <input type=\"text\" name=\"reporter_address\" class=\"form-control\" ng-model=\"params.reporter_address\" required />"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.reporter_address.$dirty && infoForm.reporter_address.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.reporter_address.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_profession.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"reporter_profession\">{{translate('Profession')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <input type=\"text\" name=\"reporter_profession\" class=\"form-control\" ng-model=\"params.reporter_profession\" />"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.reporter_profession.$dirty && infoForm.reporter_profession.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.reporter_profession.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_contact.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"reporter_contact\">{{translate('Tel No')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <input type=\"text\" name=\"reporter_contact\" class=\"form-control\" ng-model=\"params.reporter_contact\" required />"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.reporter_contact.$dirty && infoForm.reporter_contact.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.reporter_contact.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_email.$invalid}\">"+
            "                    <label class=\"col-sm-3 control-label\" for=\"reporter_email\">Email</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <input type=\"text\" name=\"reporter_email\" class=\"form-control\" ng-model=\"params.reporter_email\" required />"+
            "                        <div class=\"help-block\" data-ng-show=\"infoForm.reporter_email.$dirty && infoForm.reporter_email.$invalid\">"+
            "                            <span data-ng-show=\"infoForm.reporter_email.$error.required\">{{translate('Necessary to enter value.')}}</span>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <div class=\"row\">"+
            "                <div class=\"col-sm-offset-3 col-sm-9\">"+
            "                    <button type=\"button\" class=\"btn btn-primary\" data-ng-click=\"save();\" data-ng-disabled=\"infoForm.$invalid || isWaitAnswer()\">{{translate('Save')}}</button>"+
            "                </div>"+
            "            </div>"+
            "        </form>"+
            "    </div>"+
            "</div><!-- /.modal-content -->"
    );

    $templateCache.put("adr/report/view.tpl.html",
        "<div class=\"modal-content\">"+
            "    <div class=\"modal-header\">"+
            "        <button type=\"button\" class=\"close\" data-ng-click=\"cancel();\">&times;</button>"+
            "        <h4 class=\"modal-title\">{{translate('View Report')}}</h4>"+
            "    </div>"+
            "    <div class=\"modal-body\">"+
            "        <div class=\"alert alert-danger\" data-ng-show=\"formErrorMessage\">"+
            "            <span>{{formErrorMessage}}</span>"+
            "        </div>"+
            "        <form class=\"form-horizontal\" role=\"form\" name=\"infoForm\" novalidate>"+
            "            <fieldset>"+
            "                <legend>{{translate('Patient Information')}}</legend>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.patient_id.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"patient_id\">{{translate('Patient')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.patient}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.id_pharmacy.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"id_pharmacy\">{{translate('Pharmacy')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.pharmacy}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.date_of_visit.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"date_of_visit\">{{translate('Date of Visit')}}</label>"+
            "                    <div class=\"col-sm-3\">"+
            "                        <p class=\"form-control-static\">{{params.date_of_visit}}</p>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Adverse Drug Reaction')}}</legend>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.adr_description.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"adr_description\">{{translate('Description')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.adr_description}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"row form-group\"  data-ng-class=\"{'has-error': infoForm.onset_time.$invalid || infoForm.onset_type.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\">{{translate('Time to onset of reaction')}}</label>"+
            "                    <div class=\"col-sm-1\">"+
            "                        <p class=\"form-control-static\">{{params.onset_time}}</p>"+
            "                    </div>"+
            "                    <div class=\"col-sm-2\">"+
            "                        <p class=\"form-control-static\">{{getOnsetType(params.onset_type)}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.adr_start_date.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"adr_start_date\">{{translate('Date start of reaction')}}</label>"+
            "                    <div class=\"col-sm-3\">"+
            "                        <p class=\"form-control-static\">{{params.adr_start_date}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.adr_stop_date.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"adr_stop_date\">{{translate('Date end of reaction')}}</label>"+
            "                    <div class=\"col-sm-3\">"+
            "                        <p class=\"form-control-static\">{{params.adr_stop_date}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.subsided.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"subsided\">{{translate('Reaction subsided after stopping drug / reducing dose')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.subsided}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reappeared.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"reappeared\">{{translate('Reaction reappeared after reintroducing drug')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.reappeared}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.extent.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"extent\">{{translate('Extent of reaction')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.extent}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.seriousness.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"seriousness\">{{translate('Seriousness of reaction')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.seriousness}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.treatment_of_reaction.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"treatment_of_reaction\">{{translate('Treatment of adverse reaction & action taken')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.treatment_of_reaction}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.outcome_of_reaction_type.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"outcome_of_reaction_type\">{{translate('Outcome')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <div class=\"row\">"+
            "                            <div class=\"col-sm-4\">"+
            "                                <p class=\"form-control-static\">{{params.outcome_of_reaction_type}}</p>"+
            "                            </div>"+
            "                        </div>"+
            "                        <div class=\"row\" data-ng-show=\"params.outcome_of_reaction_desc\">"+
            "                            <p class=\"form-control-static\">{{params.outcome_of_reaction_desc}}</p>"+
            "                        </div>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.relationship.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"relationship\">{{translate('Drug-Reaction Relationship')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.relationship}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.was_admitted.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"was_admitted\">{{translate('Was Patient Admitted Due to ADR?')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\" data-ng-show=\"params.was_admitted\">{{translate('Yes')}}</p>"+
            "                        <p class=\"form-control-static\" data-ng-show=\"!params.was_admitted\">{{translate('No')}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.was_hospitalization_prolonged.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"was_hospitalization_prolonged\">{{translate('If Already Hospitalized, Was it Prolonged Due to ADR?')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\" data-ng-show=\"params.was_hospitalization_prolonged\">{{translate('Yes')}}</p>"+
            "                        <p class=\"form-control-static\" data-ng-show=\"!params.was_hospitalization_prolonged\">{{translate('No')}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.duration_of_admission.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"duration_of_admission\">{{translate('Duration of Admission (days)')}}</label>"+
            "                    <div class=\"col-sm-8\">"+
            "                        <p class=\"form-control-static\">{{params.duration_of_admission}}</p>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Suspected Drug')}}</legend>"+
            "                <div  data-ng-repeat=\"drug in params.suspected_drugs\">"+
            "                    <div class=\"col-sm-2\">"+
            "                        #{{$index}}"+
            "                    </div>"+
            "                    <div class=\"col-sm-10\">"+
            "                        <data-ng-form name=\"subForm\">"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.brand_name.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"brand_name\">{{translate('Brand Name')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.brand_name}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.generic_name.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"generic_name\">{{translate('Generic Name')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.generic_name}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.batch_number.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"batch_number\">{{translate('Batch No.')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.batch_number}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.nafdac_number.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"nafdac_number\">{{translate('NAFDAC No.')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.nafdac_number}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.expiry_date.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"expiry_date\">{{translate('Expiry Date')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.expiry_date}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.manufactor.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"manufactor\">{{translate('Name & Address of Manufacturer')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.manufactor}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.indication_for_use.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"indication_for_use\">{{translate('Indication for Use')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.indication_for_use}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.dosage.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"dosage\">{{translate('Dosage')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.dosage}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.route_of_administration.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"route_of_administration\">{{translate('Route of Administration')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.route_of_administration}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.date_started.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"date_started\">{{translate('Date started')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.date_started}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.date_stopped.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"date_stopped\">{{translate('Date stopped')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.date_stopped}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                        </data-ng-form>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Concomitant Drug')}}</legend>"+
            "                <div  data-ng-repeat=\"drug in params.concomitant_drugs\">"+
            "                    <div class=\"col-sm-2\">"+
            "                        #{{$index}}"+
            "                    </div>"+
            "                    <div class=\"col-sm-10\">"+
            "                        <data-ng-form name=\"subForm\">"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.generic_name.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"generic_name\">{{translate('Brand or Generic Name')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.generic_name}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.dosage.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"dosage\">{{translate('Dosage')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.dosage}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.batch_number.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"batch_number\">{{translate('Route')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.batch_number}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.date_started.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"date_started\">{{translate('Date started')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.date_started}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.date_stopped.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"date_stopped\">{{translate('Date stopped')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.date_stopped}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                            <div class=\"form-group\"  data-ng-class=\"{'has-error': subForm.indication_for_use.$invalid}\">"+
            "                                <label class=\"col-sm-4 control-label\" for=\"indication_for_use\">{{translate('Reason for Use')}}</label>"+
            "                                <div class=\"col-sm-6\">"+
            "                                    <p class=\"form-control-static\">{{drug.indication_for_use}}</p>"+
            "                                </div>"+
            "                            </div>"+
            "                        </data-ng-form>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Relevant Information')}}</legend>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.relevant_data.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"relevant_data\">{{translate('Relevant Investigations / Laboratory Data')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.relevant_data}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.relevant_history.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"relevant_history\">{{translate('Relevant Medical History')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.relevant_history}}</p>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <fieldset>"+
            "                <legend>{{translate('Reporter Details')}}</legend>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_name.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"reporter_name\">{{translate('Name')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.reporter_name}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_address.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"reporter_address\">{{translate('Address')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.reporter_address}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_profession.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"reporter_profession\">{{translate('Profession')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.reporter_profession}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_contact.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"reporter_contact\">{{translate('Tel No')}}</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.reporter_contact}}</p>"+
            "                    </div>"+
            "                </div>"+
            "                <div class=\"form-group\"  data-ng-class=\"{'has-error': infoForm.reporter_email.$invalid}\">"+
            "                    <label class=\"col-sm-4 control-label\" for=\"reporter_email\">Email</label>"+
            "                    <div class=\"col-sm-6\">"+
            "                        <p class=\"form-control-static\">{{params.reporter_email}}</p>"+
            "                    </div>"+
            "                </div>"+
            "            </fieldset>"+
            "            <div class=\"row\">"+
            "                <div class=\"col-sm-offset-4 col-sm-9\">"+
            "                    <button type=\"button\" class=\"btn btn-primary\" data-ng-click=\"download();\" data-ng-disabled=\"infoForm.$invalid || isWaitAnswer()\">{{translate('Download')}}</button>"+
            "                    <button type=\"button\" class=\"btn btn-default\" data-ng-click=\"regenerate();\" data-ng-disabled=\"infoForm.$invalid || isWaitAnswer()\">{{translate('Regenerate')}}</button>"+
            "                </div>"+
            "            </div>"+
            "        </form>"+
            "    </div>"+
            "</div><!-- /.modal-content -->"
    );
}]);

