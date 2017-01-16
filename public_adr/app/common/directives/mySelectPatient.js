angular.module('trustrx.common.directives')
.directive('mySelectPatient', ['$templateCache', '$uibModal', '$http', 'AdrInternalAddressSvc', function ($templateCache, $uibModal, $http, AdrInternalAddressSvc) {
    var controller = ['$scope', function ($scope) {
        $scope.modelOptions = {
            debounce: {
                default: 500,
                blur: 250
            },
            getterSetter: true
        };
        $scope.params = {
            user: null
        };

        $scope.translate = function (key, options) {
            return i18n.translate(key, options);
        };

        $scope.getFormControlProperty = function (propertyName) {
            var ctrlName = $scope.controlName ? $scope.form[$scope.controlName] : undefined;
            return ctrlName ? ctrlName[propertyName] : undefined;
        };

        $scope.getPatientsList = function (filterValue) {
            return $http({url: AdrInternalAddressSvc.patientFilterAccessable(), method: 'POST', data: {filter: filterValue}, cache: false}).then(function(result){
                return result.data;
            });

        };

        $scope.getPatientInfo = function (id) {
            if(!id) {
                $scope.params.user = null;
                return;
            }
            $http.get(AdrInternalAddressSvc.patientGet(id)).
                success(function(data) {
                    if (!data.success) {
                        $scope.params.user = null;
                    }
                    else {
                        $scope.params.user = data.info;
                    }
                }
            );
        };

        $scope.allowCreate = function () {
            return false/*$scope.withAdd()*/;
        };

        $scope.createUser = function () {
            /*
            var modalInstance = $uibModal.open({
                template : $templateCache.get('client/users/info.tpl.html'),
                controller  : 'ClientUsersInfoCtrl',
                resolve     : {
                    params: function () {
                        return {
                            id: null,
                            realm_id: $scope.realmId
                        };
                    }
                },
                backdrop    : 'static'
            });

            modalInstance.result.then(function (id) {
                $scope.getPatientInfo(id);
            }, function () {
            });
             */
        };

    }];
    return {
        template: ""+
            "<div data-ng-class=\"{'input-group': allowCreate()}\">"+
            "    <input type=\"text\""+
            "        autocomplete=\"off\""+
            "        class=\"form-control\""+
            "        data-ng-model=\"params.user\""+
            "        uib-typeahead=\"user as user.full_name for user in getPatientsList($viewValue)\""+
            "        typeahead-editable=\"false\""+
            "        typeahead-min-length=\"2\""+
            "        typeahead-wait-ms=\"500\""+
            "        typeahead-loading=\"loading\""+
            "        placeholder=\"" + i18n.translate("Enter patient name or identifier") + "\""+
            "        data-ng-required=\"required\">"+
            "    <div data-ng-show='allowCreate()' class='input-group-btn'>"+
            "        <button type=\"button\" class=\"btn btn-default\" data-ng-attr-title=\"{{translate('Create User')}}\" data-ng-click=\"createUser();\"><span class=\"glyphicon glyphicon-plus\"></span></button>"+
            "    </div>"+
            "</div>"+
            "<div class=\"help-block\" data-ng-show=\"getFormControlProperty('$dirty') && getFormControlProperty('$invalid')\" data-ng-messages=\"getFormControlProperty('$error')\">"+
            "    <span data-ng-message=\"required\">{{translate('Necessary to choose user.')}}</span>"+
            "</div>"+
            "<img data-ng-show=\"loading\" class=\"addon\" src=\""+AdrInternalAddressSvc.loadFile("img/loading.gif")+"\">",
        restrict: 'EA',
        scope: {
            withAdd: '&',    /* @ means no binding, just evaluation as string. = means two-way binding as variable, & - one-way binding */
            realmId: '@'
        },
        controller: controller,
        require: ['^form', 'ngModel'],

        link: function(scope, element, attrs, ctrls) {
            scope.form = ctrls[0];
            scope.ngModel = ctrls[1];

            scope.ngModel.$render = function() {
                /* Because at the moment of link call $modelValue is not filled yet (NaN) and is filled by default value later.
                 *  It catches the changing of the password 'outside' directive, not inside.
                 */
                scope.getPatientInfo(scope.ngModel.$modelValue);
            };

            /* getting data-ng-required attribute from instance of used directive */
            attrs.$observe('required', function(value) {
                scope.required = value;
            });
            scope.controlName = attrs.name;

            scope.$watch('params.user', function(newValue, oldValue) {
                if(newValue !== oldValue) {
                    scope.ngModel.$setViewValue(newValue ? newValue.id : null);
                }
            });
        }

    };
}]);
