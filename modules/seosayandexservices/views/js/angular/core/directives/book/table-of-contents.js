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
        .directive('tableOfContents',TableOfContents);

    TableOfContents.$inject = ['$compile', '$filter'];

    function TableOfContents ($compile, $filter) {

        function postLink($scope, $element, $attr, book) {
            $element.addClass('nav nav-pills nav-stacked');

            $scope.toggleNested = function ($event) {
                $event.preventDefault();
                var $target = angular.element($event.target);
                $target.siblings('ul').slideToggle();
            };

            $scope.displayPath = function (str) {
                var matches = str.match(/^(\d+)\._(.*)$/);
                return matches[1]+'. ' + $filter('translate')($filter('toEnglish')(matches[2]))
            };

            $scope.isPage = function (path) {
                return typeof $scope.tree[path] === 'string';
            };

            $scope.isCurrentCategory = function (path) {
                path = ($scope.parent ?  $scope.parent+ '/' : '') + path;

                return book.currentPage.substr(0,  path.length) === path;
            };

            $scope.isCurrentPage = function (path) {
                path = ($scope.parent ?  $scope.parent+ '/' : '') + path;

                return book.currentPage === path;
            };
        }

        return {
            restrict: 'A',
            require: '^book',
            scope: {
                tree: '=tableOfContents',
                parent: '=?'
            },
            templateUrl: 'core/book/table-of-contents.tpl',
            compile: function(element){
                // Normalize the link parameter
                var link = { post: postLink };

                // Break the recursion loop by removing the contents
                var contents = element.contents().remove();
                var compiledContents;
                return {
                    pre: (link && link.pre) ? link.pre : null,
                    /**
                     * Compiles and re-adds the contents
                     */
                    post: function(scope, element){
                        // Compile the contents
                        if(!compiledContents){
                            compiledContents = $compile(contents);
                        }
                        // Re-add the compiled contents to the element
                        compiledContents(scope, function(clone){
                            element.append(clone);
                        });

                        // Call the post-linking function, if any
                        if(link && link.post){
                            link.post.apply(null, arguments);
                        }
                    }
                };
            }
        }
    }
})();