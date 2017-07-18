/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    SeoSA<885588@bk.ru>
 *  @copyright 2012-2017 SeoSA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

(function () {
    var app = angular.module('sya.core');

    app.directive('productFilter', ProductFilterDirective);

    ProductFilterDirective.$inject = ['Api', '$filter'];
    ProductFilterDirective.$id = 0;
    function ProductFilterDirective (Api, $filter) {
        var translate = $filter('translate');

        const Model = {mode: 'all', items: []};
        function isValidModel(value) {
            return value && value.mode && Array.isArray(value.items);
        }

        return {
            require: 'ngModel',
            restrict: 'EA',
            templateUrl: 'core/product-filter.tpl',
            scope: {},
            compile: function () {
                return function ($scope, $element, $attrs, ngModel) {
                    ProductFilterDirective.$id++;
                    $scope.directiveId = $attrs.id || 'product_filter_'+ProductFilterDirective.$id;
                    $scope.ngModel = ngModel;
                    $scope.settings = {};

                    ngModel.$render = function () {
                        if (!isValidModel(ngModel.$viewValue))
                            ngModel.$setViewValue(angular.copy(Model));

                        reset();
                    };

                    function reset() {
                        $scope.searchQuery = '';
                        $scope.items = [];
                        $scope.tmpNotSelected = [];
                        $scope.tmpSelected = [];
                        $scope.load();
                    }

                    $scope.setMode = function (mode, $event) {
                        if ($event)
                            $event.preventDefault();

                        ngModel.$setViewValue({
                            mode: mode,
                            items: [],
                        });
                        reset();
                    };

                    $scope.needSelector = function (mode) {
                        return ['all', 'active', 'not_active'].indexOf(mode) === -1;
                    };

                    $scope.isNotSelected = function (item) {
                        return ngModel.$viewValue.items.indexOf(item.id) == -1;
                    };

                    $scope.addItems = function () {
                        if (!$scope.tmpNotSelected) return;


                        for (var i = 0 ; i < $scope.tmpNotSelected.length; i++) {
                            var $index = $scope.tmpNotSelected[i];
                            var item = $scope.items[$index];
                            ngModel.$viewValue.items.push(item.id);
                        }

                        ngModel.$setViewValue({
                            mode: ngModel.$viewValue.mode,
                            items: ngModel.$viewValue.items
                        });

                        $scope.tmpNotSelected = null;
                        $scope.tmpSelected = null;


                    };

                    $scope.removeItems = function () {
                        if (!$scope.tmpSelected) return;

                        for (var i = 0 ; i < $scope.tmpSelected.length; i++) {
                            var item = $scope.tmpSelected[i];
                            var $index = item.id - i;
                            ngModel.$viewValue.items.splice($index, 1)
                        }

                        ngModel.$setViewValue({
                            mode: ngModel.$viewValue.mode,
                            items: ngModel.$viewValue.items
                        });

                        $scope.tmpNotSelected = null;
                        $scope.tmpSelected = null;
                    };


                    $scope.matchSearch = function (name, query) {
                        query = query || "";
                        query = query.trim();
                        return !query || name.toLowerCase().indexOf(query.toLowerCase()) > -1;
                    };

                    $scope.load = function () {
                        var rpc;
                        if (!$scope.needSelector(ngModel.$viewValue.mode))
                            return;

                        switch (ngModel.$viewValue.mode) {
                            case 'by_category':
                                rpc = 'categories_search';
                                break;
                            case 'by_manufacturer':
                                rpc = 'manufacturers_search';
                                break;
                            case 'by_supplier':
                                rpc = 'suppliers_search';
                                break;
                            case 'selected':
                            case 'not_selected':
                                rpc = 'products_search';
                                break;
                            default:
                                return;
                        }

                        if ($scope.load.promise)
                            Api.cancel($scope.load.promise);

                        $scope.load.promise = Api.post(rpc, {}).then(function (response) {
                            $scope.tmpNotSelected = null;
                            $scope.tmpSelected = null;
                            $scope.items = response;
                            $scope.load.promise = null;
                        });
                    };
                };
            }
        };
    }
})();