var adrPharmacyModule = angular.module('trustrx.adr.pharmacy');

adrPharmacyModule.controller('AdrPharmacyInfoCtrl', ['$scope', '$filter', '$uibModalInstance', '$http', 'AdrInternalAddressSvc', 'params', function($scope, $filter, $uibModalInstance, $http, AdrInternalAddressSvc, params) {
    /* Data */
    $scope.itemId = null;
    $scope.params = {
        name: null,
        is_active: true,
        id_country: null,
        id_state: null,
        address: null,
        id_lga: null,
        id_facility: null
    };
    $scope.countries = [];
    $scope.states = [];
    $scope.lgas = [];
    $scope.facilities = [];

    $scope.is_wait_answer = false;
    $scope.formErrorMessage = '';

    /* Methods */
    $scope.isWaitAnswer = function () {
        return $scope.is_wait_answer;
    };
    $scope.isEditMode = function () {
        return  $scope.itemId ? true : false;
    };
    $scope.getTitle = function () {
        return  $scope.isEditMode() ? i18n.translate("Edit Pharmacy") : i18n.translate("Create Pharmacy");
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
    $scope.loadLga = function () {
        $scope.lgas = [];
        if($scope.params.id_state) {
            $http.get(AdrInternalAddressSvc.lgaList($scope.params.id_state)).
                success(function(data) {
                    $scope.lgas = data;
                }
            );
        }
    };
    $scope.loadFacilities = function () {
        $scope.facilities = [];
        if($scope.params.id_lga) {
            $http.get(AdrInternalAddressSvc.facilityList($scope.params.id_lga)).
                success(function(data) {
                    $scope.facilities = data;
                }
            );
        }
    };

    $scope.save = function () {
        var params = angular.copy($scope.params);
        var url = $scope.isEditMode() ? AdrInternalAddressSvc.pharmacySave($scope.itemId) : AdrInternalAddressSvc.pharmacyCreate();
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
    $scope.loadLga();
    $scope.loadFacilities();
}]);
