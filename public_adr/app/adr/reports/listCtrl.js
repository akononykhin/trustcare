var adrReportsModule = angular.module('trustrx.adr.reports');

adrReportsModule.controller('AdrReportsListCtrl', ['$scope', '$timeout', '$window', '$http', 'AdrInternalAddressSvc', function($scope, $timeout, $window, $http, AdrInternalAddressSvc) {
    /* Data */
    $scope.list = [];
    $scope.listIsLoading = false;
    $scope.listIsLoaded = false;

    $scope.loadMoreReports = function () {
        var quantity = 10;
        $scope.loadReports($scope.list.length, quantity);
    };

    $scope.loadReports = function (offset, quantity) {
        if($scope.listIsLoaded) {
            return;
        }
        $scope.listIsLoading = true;

        $http.get(AdrInternalAddressSvc.reportsList(offset, quantity)).
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

    $scope.refreshList = function () {
        var offset = 0;
        var quantity = $scope.list.length;

        $scope.list = [];
        $scope.listIsLoaded = false;
        $scope.loadReports(offset, quantity);
    };

    $scope.showLoadingBtn = function () {
        return (!$scope.listIsLoading && !$scope.listIsLoaded) ? true : false;
    };

    $scope.delete = function (index) {
        if(index < 0 || index >= $scope.list.length) {
            return;
        }
        var report = $scope.list[index];
        var messageStr = i18n.translate("Are you sure you want to delete report __id__ generated __generation_date__?", {id: "<strong>" + report.id + "</strong>", generation_date: "<strong>" + report.gd + "</strong>"});

        bootbox.dialog({
            message: messageStr,
            className: 'bootbox-danger',
            buttons: {
                confirm: {
                    label: 'OK',
                    className: "btn-danger",
                    callback: function() {
                        $http({url: AdrInternalAddressSvc.reportsDelete(report.id), method: 'POST', data: {}})
                            .success(function(data, status, headers, config) {
                                if (!data.success) {
                                    var errorMsg = data.message ? data.message : i18n.translate("Internal Error");
                                    bootbox.dialog({
                                        message: errorMsg,
                                        className: 'bootbox-danger',
                                        buttons: {
                                            confirm: {
                                                label: 'OK',
                                                className: "btn-danger"
                                            }
                                        }
                                    });
                                    return;
                                }
                                $scope.refreshList();
                            })
                            .error(function(data, status, headers, config) {
                                bootbox.dialog({
                                    message: "Request Failed",
                                    className: 'bootbox-danger',
                                    buttons: {
                                        confirm: {
                                            label: 'OK',
                                            className: "btn-danger"
                                        }
                                    }
                                });
                            });

                    }
                },
                cancel: {
                    label: i18n.translate("Cancel"),
                    className: "btn-primary"
                }
            }
        });
    };

    $scope.download = function (index) {
        if(index < 0 || index >= $scope.list.length) {
            return;
        }
        var report = $scope.list[index];

        $window.open(AdrInternalAddressSvc.reportsDownload(report.id), '_download');
    };



    $scope.loadMoreReports();
}]);
