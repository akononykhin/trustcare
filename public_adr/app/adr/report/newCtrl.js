var adrReportsModule = angular.module('trustrx.adr.report');

adrReportsModule.controller('AdrReportsNewCtrl', ['$scope', '$filter', '$uibModalInstance', '$http', 'AdrInternalAddressSvc', 'params', function($scope, $filter, $uibModalInstance, $http, AdrInternalAddressSvc, params) {
    /* Data */
    $scope.realmId = null;
    $scope.params = {
        patient_id: null,
        id_pharmacy: null,
        secret: '',
        uam_type: '',
        router_model: ''
    };
    $scope.pharmacies = [];
    $scope.is_wait_answer = false;
    $scope.formErrorMessage = '';

    /* Methods */
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
}]);
