var adrReportsModule = angular.module('trustrx.adr.report');

adrReportsModule.controller('AdrReportsViewCtrl', ['$scope', '$filter', '$uibModalInstance', '$http', '$window', 'AdrInternalAddressSvc', 'params', function($scope, $filter, $uibModalInstance, $http, $window, AdrInternalAddressSvc, params) {
    /* Data */
    $scope.itemId = null;
    $scope.params = {
        id: null,
        generation_date: null,
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

    $scope.is_wait_answer = false;
    $scope.formErrorMessage = '';

    /* Methods */
    $scope.getOnsetType = function (type) {
        return $scope.onsetTypes.hasOwnProperty(type) ? $scope.onsetTypes.type : type;
    };
    $scope.isWaitAnswer = function () {
        return $scope.is_wait_answer;
    };
    $scope.translate = function (key, options) {
        return i18n.translate(key, options);
    };
    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

    $scope.download = function () {
        $window.open(AdrInternalAddressSvc.reportDownload($scope.itemId), '_download');
    };

    $scope.regenerate = function () {
        var messageStr = i18n.translate("Regenerate report __id__ generated __generation_date__?", {id: "<strong>" + $scope.params.id + "</strong>", generation_date: "<strong>" + $scope.params.generation_date + "</strong>"});

        bootbox.dialog({
            message: messageStr,
            className: 'bootbox-info',
            buttons: {
                confirm: {
                    label: 'OK',
                    className: "btn-primary",
                    callback: function() {
                        $http({url: AdrInternalAddressSvc.reportRegenerate($scope.params.id), method: 'POST', data: {}})
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
                                $uibModalInstance.close();
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
                    className: "btn-default"
                }
            }
        });
    };

    /* initialization */
    $scope.itemId = params.id;
    if($scope.itemId) {
        $http.get(AdrInternalAddressSvc.reportGet($scope.itemId)).
            success(function(data) {
                if (!data.success) {
                    var messageStr = data.message ? data.message : i18n.translate("Internal Error");
                    bootbox.dialog({
                        title: i18n.translate("Error"),
                        message: messageStr,
                        className: 'bootbox-danger',
                        buttons: {
                            confirm: {
                                label: 'OK',
                                className: "btn-danger"
                            }
                        }
                    });
                    $uibModalInstance.dismiss('cancel');
                    return;
                }
                $scope.params = data.info;
            }
        );
    }
}]);
