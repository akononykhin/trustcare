var adrReportsModule = angular.module('trustrx.adr.report');

adrReportsModule.controller('AdrReportsNewCtrl', ['$scope', '$filter', '$uibModalInstance', '$uibModal', '$http', '$templateCache', 'AdrInternalAddressSvc', 'params', function($scope, $filter, $uibModalInstance, $uibModal, $http, $templateCache, AdrInternalAddressSvc, params) {
    /* Data */
    $scope.params = {
        patient_id: null,
        id_pharmacy: null,
        date_of_visit: null,
        adr_description: '',
        onset_time: null,
        onset_type: null,
        adr_start_date: null,
        adr_stop_date: null,
        outcome_of_reaction_type: null,
        outcome_of_reaction_desc: '',
        subsided: null,
        reappeared: null,
        extent: null,
        seriousness: null,
        relationship: null,
        treatment_of_reaction: '',
        was_admitted: false,
        was_hospitalization_prolonged: false,
        duration_of_admission: null,
        suspected_drugs: [],
        concomitant_drugs: [],
        relevant_data: null,
        relevant_history: null,
        reporter_name: null,
        reporter_address: null,
        reporter_profession: null,
        reporter_contact: null,
        reporter_email: null
    };
    $scope.onsetTypes = [
        {value: 'mins', name: i18n.translate("mins")},
        {value: 'hours', name: i18n.translate("hours")},
        {value: 'days', name: i18n.translate("days")},
        {value: 'months', name: i18n.translate("months")},
        {value: 'years', name: i18n.translate("years")}
    ];
    $scope.pharmacies = [];
    $scope.outcomeList = [];
    $scope.subsidedList = [];
    $scope.reappearedList = [];
    $scope.extentList = [];
    $scope.seriousnessList = [];
    $scope.relationshipList = [];

    $scope.is_wait_answer = false;
    $scope.formErrorMessage = '';

    $scope.dateOptions = {
        formatYear: 'yyyy',
        startingDay: 1,
        showWeeks: false
    };
    $scope.calendar = {
        dov_opened: false,
        adr_start_date_opened: false,
        adr_stop_date_opened: false
    };

    /* Methods */
    $scope.calendar_open = function(type) {
        if('dov' == type) {
            $scope.calendar.dov_opened = true;
        }
        else if('adr_start_date' == type) {
            $scope.calendar.adr_start_date_opened = true;
        }
        else if('adr_stop_date' == type) {
            $scope.calendar.adr_stop_date_opened = true;
        }
    };
    $scope.concomitant_calendar_open = function(type, index) {
        if(index >= $scope.params.concomitant_drugs.length) {
            return;
        }
        if('date_started' == type) {
            $scope.params.concomitant_drugs[index].date_started_opened = true;
        }
        else if('date_stopped' == type) {
            $scope.params.concomitant_drugs[index].date_stopped_opened = true;
        }
    };
    $scope.suspected_calendar_open = function(type, index) {
        if(index >= $scope.params.suspected_drugs.length) {
            return;
        }
        if('date_started' == type) {
            $scope.params.suspected_drugs[index].date_started_opened = true;
        }
        else if('date_stopped' == type) {
            $scope.params.suspected_drugs[index].date_stopped_opened = true;
        }
        else if('expiry_date' == type) {
            $scope.params.suspected_drugs[index].expiry_date_opened = true;
        }
    };

    $scope.isWaitAnswer = function () {
        return $scope.is_wait_answer;
    };
    $scope.translate = function (key, options) {
        return i18n.translate(key, options);
    };
    $scope.loadPharmacies = function () {
        $scope.pharmacies = [];
        $http.get(AdrInternalAddressSvc.pharmacyListActive()).
            success(function(data) {
                $scope.pharmacies = data;
            }
        );
    };
    $scope.loadLists = function () {
        $scope.outcomeList = [];
        $scope.subsidedList = [];
        $scope.reappearedList = [];
        $scope.extentList = [];
        $scope.seriousnessList = [];
        $scope.relationshipList = [];
        $http.get(AdrInternalAddressSvc.reportAttrLists()).
            success(function(data) {
                $scope.outcomeList = data.outcome;
                $scope.subsidedList = data.subsided;
                $scope.reappearedList = data.reappeared;
                $scope.extentList = data.extent;
                $scope.seriousnessList = data.seriousness;
                $scope.relationshipList = data.relationship;
            }
        );
    };

    $scope.isOnsetTimeRequired = function () {
        return $scope.params.onset_type ? true : false;
    };
    $scope.isOnsetTypeRequired = function () {
        return $scope.params.onset_time ? true : false;
    };
    $scope.showDeathComment = function () {
        return $scope.params.outcome_of_reaction_type == 3 ? true : false; /* 3 == death */
    };

    $scope.addSuspectedDrug = function () {
        $scope.params.suspected_drugs.push({brand_name: '', generic_name: '', dosage: '', batch_number: '', date_started: '', date_stopped: '', indication_for_use: '', nafdac_number: '', expiry_date: '', manufactor: '', route_of_administration: ''});
    };
    $scope.removeSuspectedDrug = function (index) {
        if (0 == index) {
            return;
        }
        $scope.params.suspected_drugs.splice(index, 1);
    };
    $scope.addConcomitantDrug = function () {
        $scope.params.concomitant_drugs.push({generic_name: '', dosage: '', batch_number: '', date_started: '', date_stopped: '', indication_for_use: ''});
    };
    $scope.removeConcomitantDrug = function (index) {
        if (0 == index) {
            return;
        }
        $scope.params.concomitant_drugs.splice(index, 1);
    };

    $scope.formatDate = function (date) {
        if(!date) {
            return null;
        }
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    };

    $scope.save = function () {
        var url = AdrInternalAddressSvc.reportCreate();
        var params = angular.copy($scope.params);
        if($scope.params.date_of_visit) {
            params.date_of_visit = $scope.formatDate($scope.params.date_of_visit);
        }
        if($scope.params.adr_start_date) {
            params.adr_start_date = $scope.formatDate($scope.params.adr_start_date);
        }
        if($scope.params.adr_stop_date) {
            params.adr_stop_date = $scope.formatDate($scope.params.adr_stop_date);
        }
        $.each(params.suspected_drugs, function(index, drug) {
            if(drug.date_started) {
                drug.date_started = $scope.formatDate(drug.date_started);
            }
            if(drug.date_stopped) {
                drug.date_stopped = $scope.formatDate(drug.date_stopped);
            }
            if(drug.expiry_date) {
                drug.expiry_date = $scope.formatDate(drug.expiry_date);
            }
        });
        $.each(params.concomitant_drugs, function(index, drug) {
            if(drug.date_started) {
                drug.date_started = $scope.formatDate(drug.date_started);
            }
            if(drug.date_stopped) {
                drug.date_stopped = $scope.formatDate(drug.date_stopped);
            }
        });


        var warnDialog = bootbox.dialog({
            message: '<p class="text-center">'+i18n.translate("Please wait. Report is generating ...")+'</p>',
            className: 'bootbox-warning',
            closeButton: false
        });
        $scope.is_wait_answer = true;
        $scope.formErrorMessage = '';
        $http({url: url, method: 'POST', data: params, cache: false, timeout: 120000}).
            success(function (data) {
                warnDialog.modal('hide');
                $scope.is_wait_answer = false;
                if (!data.success) {
                    var messageStr = data.message ? data.message : i18n.translate("Internal Error");
                    $scope.formErrorMessage = messageStr;
                    return;
                }
                $uibModalInstance.close();
            }).
            error(function () {
                warnDialog.modal('hide');
                $scope.is_wait_answer = false;
                $scope.formErrorMessage = i18n.translate("Request Failed");
            }
        );
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

    $scope.pharmacyAdd = function () {
        var modalInstance = $uibModal.open({
            template : $templateCache.get('adr/pharmacy/info.tpl.html'),
            controller  : 'AdrPharmacyInfoCtrl',
            resolve     : {
                params: function () {
                    return {
                        id: null
                    };
                }
            },
            backdrop    : 'static'
        });

        modalInstance.result.then(function (id) {
            $scope.params.id_pharmacy = ""+id;
            $scope.loadPharmacies();
        }, function () {
        });
    }


    /* initialization */
    $scope.loadPharmacies();
    $scope.loadLists();
    $scope.addSuspectedDrug();
    $scope.addConcomitantDrug();
}]);
