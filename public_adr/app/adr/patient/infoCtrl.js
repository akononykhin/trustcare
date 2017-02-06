var adrPatientModule = angular.module('trustrx.adr.patient');

adrPatientModule.controller('AdrPatientInfoCtrl', ['$scope', '$filter', '$uibModalInstance', '$http', 'AdrInternalAddressSvc', 'params', function($scope, $filter, $uibModalInstance, $http, AdrInternalAddressSvc, params) {
    /* Data */
    $scope.itemId = null;
    $scope.params = {
        identifier: null,
        is_active: true,
        first_name: null,
        last_name: null,
        id_country: null,
        id_state: null,
        city: null,
        address: null,
        zip: null,
        phone: null,
        birthdate: null,
        is_male: 1,
        id_physician: null
    };
    $scope.countries = [];
    $scope.states = [];
    $scope.physicians = [];
    $scope.genders = [
        {id: 1, name: i18n.translate("Male")},
        {id: 0, name: i18n.translate("Female")}
    ];

    $scope.is_wait_answer = false;
    $scope.formErrorMessage = '';

    $scope.dateOptions = {
        formatYear: 'yyyy',
        startingDay: 1,
        showWeeks: false
    };
    $scope.calendar = {
        birthdate_opened: false
    };

    /* Methods */
    $scope.calendar_open = function(type) {
        if('birthdate' == type) {
            $scope.calendar.birthdate_opened = true;
        }
    };
    $scope.isWaitAnswer = function () {
        return $scope.is_wait_answer;
    };
    $scope.isEditMode = function () {
        return  $scope.itemId ? true : false;
    };
    $scope.getTitle = function () {
        return  $scope.isEditMode() ? i18n.translate("Edit Patient") : i18n.translate("Create Patient");
    };
    $scope.translate = function (key, options) {
        return i18n.translate(key, options);
    };
    $scope.loadCountries = function () {
        $scope.countries = [];
        $http.get(AdrInternalAddressSvc.countryList()).
            success(function(data) {
                $scope.countries = data;
            }
        );
    };
    $scope.loadStates = function () {
        $scope.states = [];
        if($scope.params.id_country) {
            $http.get(AdrInternalAddressSvc.stateList($scope.params.id_country)).
                success(function(data) {
                    $scope.states = data;
                }
            );
        }
    };
    $scope.loadPhysicians = function () {
        $scope.physicians = [];
        $http.get(AdrInternalAddressSvc.physicianList()).
            success(function(data) {
                $scope.physicians = data;
            }
        );
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
        var params = angular.copy($scope.params);
        if(params.birthdate) {
            params.birthdate = $scope.formatDate(params.birthdate);
        }
        var url = $scope.isEditMode() ? AdrInternalAddressSvc.patientSave($scope.itemId) : AdrInternalAddressSvc.patientCreate();
        $scope.is_wait_answer = true;
        $scope.formErrorMessage = '';
        $http({url: url, method: 'POST', data: params, cache: false, timeout: 120000}).
            success(function (data) {
                $scope.is_wait_answer = false;
                if (!data.success) {
                    var messageStr = data.message ? data.message : i18n.translate("Internal Error");
                    $scope.formErrorMessage = messageStr;
                    return;
                }
                $uibModalInstance.close(data.id);
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
    $scope.itemId = params.id;
    if($scope.itemId) {
    }
    $scope.loadCountries();
    $scope.loadStates();
    $scope.loadPhysicians();
}]);
