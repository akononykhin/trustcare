var adrReportsModule = angular.module('trustrx.adr.reports');

adrReportsModule.controller('AdrReportsListCtrl', ['$scope', '$timeout', '$http', 'AdrInternalAddressSvc', function($scope, $timeout, $http, AdrInternalAddressSvc) {
    /* Data */
    $scope.list = [];
    $scope.listIsLoading = false;
    $scope.listIsLoaded = false;

    $scope.loadMoreReports = function () {
        if($scope.listIsLoaded) {
            return;
        }
        $scope.listIsLoading = true;

        var quantity = 50;
        $http.get(AdrInternalAddressSvc.reportsList($scope.list.length, quantity)).
            success(function(response) {
                $scope.list = $scope.list.concat(response.list);
                $scope.listIsLoading = false;
                $scope.listIsLoaded = (quantity > response.list.length) ? true : false;
            }).
            error(function () {
                $scope.listIsLoading = false;
            }
        );
    };

    $scope.showLoadingBtn = function () {
        return (!$scope.listIsLoading && !$scope.listIsLoaded) ? true : false;
    };

    $scope.loadMoreReports();
}]);
