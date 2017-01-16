var adrReportsModule = angular.module('trustrx.adr.report');

adrReportsModule.controller('AdrReportsListCtrl', ['$scope', '$templateCache', '$window', '$http', '$uibModal', 'AdrInternalAddressSvc', function($scope, $templateCache, $window, $http, $uibModal, AdrInternalAddressSvc) {
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

        $http.get(AdrInternalAddressSvc.reportList(offset, quantity)).
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

    $scope.create = function () {
        var modalInstance = $uibModal.open({
            template : $templateCache.get('adr/report/new.tpl.html'),
            controller  : 'AdrReportsNewCtrl',
            resolve     : {
                params: function () {
                    return {
                        id: null
                    };
                }
            },
            windowClass : 'newReportsDlg',
            backdrop    : 'static'
        });

        modalInstance.result.then(function () {
            $scope.refreshList();
        }, function () {
        });
    };


    $scope.edit = function (index) {
        if(index < 0 || index >= $scope.list.length) {
            return;
        }
        var report = $scope.list[index];

        var modalInstance = $uibModal.open({
            template : $templateCache.get('adr/report/view.tpl.html'),
            controller  : 'AdrReportsViewCtrl',
            resolve     : {
                params: function () {
                    return {
                        id: report.id
                    };
                }
            },
            windowClass : 'viewReportsDlg',
            backdrop    : 'static'
        });

        modalInstance.result.then(function () {
            $scope.refreshList();
        }, function () {
        });
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
                        $http({url: AdrInternalAddressSvc.reportDelete(report.id), method: 'POST', data: {}})
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

        $window.open(AdrInternalAddressSvc.reportDownload(report.id), '_download');
    };



    $scope.loadMoreReports();
}]);
