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
 *  @author    SeoSA <885588@bk.ru>
 *  @copyright 2012-2017 SeoSA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

(function () {
    angular.module('sya.core')
        .directive('bookImage', BookImage);

    BookImage.$inject = ['Api', '$q', '$timeout', 'moduleBookImgUrl'];
    function BookImage (Api, $q, $timeout, moduleBookImgUrl) {

        function downloadImagesList() {
            var deferred = $q.defer();
            if (downloadImagesList.list) {
                $timeout(function () {
                    deferred.resolve(downloadImagesList.list)
                }, 0);
            }

            if (downloadImagesList.promise) {
                return downloadImagesList.promise;
            }

            Api.get('get_book_images', {}).then(function (response) {
                downloadImagesList.list = response.result;
                deferred.resolve(downloadImagesList.list);
            });

            downloadImagesList.promise = deferred.promise;

            return deferred.promise;
        }

        function link ($scope, $element, $attrs) {
            $scope.currentLocale = 'en';
            $scope.moduleBookImgUrl = moduleBookImgUrl;
            $scope.locales = [];

            $scope.setLocale = function (locale, $event) {
                if ($event) $event.preventDefault();
                $scope.currentLocale = locale;
            };

            downloadImagesList().then(function(list) {
                $scope.locales = list[$scope.path] || [];
                $scope.currentLocale = $scope.locales[0];
            });
        }

        return {
            restrict: 'E',
            link: link,
            scope: {
                path: '@'
            },
            templateUrl: 'core/book/image.tpl'
        }
    }
})();