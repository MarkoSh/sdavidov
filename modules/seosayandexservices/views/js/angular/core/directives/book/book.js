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
        .directive('book', Book);

    function Book () {

        BookController.$inject = ['$window', '$attrs', 'Router'];

        function BookController($window, $attrs, Router)
        {
            this.tree = {};
            this.currentPage = decodeURIComponent(Router.getStateParam('book_page')) || $attrs.book || '';

            this.addPage = function (name) {
                var path = name.split('/');
                var tree = this.tree;

                for (var i =0; i < path.length; i++) {
                    var part = path[i];
                    if (i == (path.length - 1)) {
                        tree[part] = name;
                    } else {
                        if (!tree[part]) tree[part] = {};
                    }

                    tree = tree[part];
                }
            };

            this.setPage = function (name, $event) {
                if ($event) $event.preventDefault();
                this.currentPage = name;
                $($window).scrollTop(0);

                var params = Router.getStateParams();
                if (this.currentPage === $attrs.book && params['book_page'])
                    delete params['book_page'];
                else if (this.currentPage !== $attrs.book)
                    params['book_page'] = encodeURIComponent(this.currentPage);

                Router.setStateParams(params);
            }
        }

        return {
            restrict: 'A',
            controller: BookController,
            controllerAs: 'book'
        }
    }
})();