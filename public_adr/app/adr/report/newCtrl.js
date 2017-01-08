var adrReportsModule = angular.module('trustrx.adr.report');

adrReportsModule.controller('AdrReportsNewCtrl', ['$scope', '$filter', '$uibModalInstance', '$http', 'AdrInternalAddressSvc', 'params', function($scope, $filter, $uibModalInstance, $http, AdrInternalAddressSvc, params) {
    /* Data */
    $scope.realmId = null;
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
        duration_of_admission: null
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
    }

    $scope.create = function () {
        var params = angular.copy($scope.params);
        var url = ClientInternalAddressSvc.nasCreate($scope.realmId);
        $scope.is_wait_answer = true;
        $scope.formErrorMessage = '';
        $http({url: url, method: 'POST', data: params, cache: false}).
            success(function (data) {
                $scope.is_wait_answer = false;
                if (!data.success) {
                    var messageStr = data.message ? data.message : i18n.translate("Internal Error");
                    $scope.formErrorMessage = messageStr;
                    return;
                }
                $uibModalInstance.close();
            }).
            error(function () {
                $scope.is_wait_answer = false;
                $scope.formErrorMessage = i18n.translate("Request Failed");
            }
        );
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };


    /* initialization */
    $scope.loadPharmacies();
    $scope.loadLists();
}]);
