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
    angular.module('sya.configure').provider('Router', RouterProvider);

    function params_to_string(params)
    {
        params = params || {};
        var paramsQuery = '';
        for (var key in params) {
            if (!params.hasOwnProperty(key)) continue;
            paramsQuery += '&'+key+'='+params[key];
        }

        return paramsQuery;
    }


    RouterProvider.$inject = [];
    function RouterProvider()
    {
        var routes = {};
        var excluded_params = ['mode', 'controller', 'token', 'configure'];
        var base_url = null;
        var default_route = null;
        var current_route = null;


        function get_state_url(state) {
            if (state === default_route)
                return base_url;

            return base_url+'&mode='+state;
        }

        RouterFactory.$inject = ['$filter', '$window', '$timeout'];
        function RouterFactory($filter, $window, $timeout) {

            var Router = {};

            Router.setState = function (state, params, $event) {
                if ($event) $event.preventDefault();

                var url;

                if (routes.hasOwnProperty(state))
                    url = get_state_url(state);

                if (!url)
                    return Router.setState(default_route, params);

                current_route = state;
                var title = routes[state];
                var location  = url+params_to_string(params);

                $('title').html(title);
                $window.history.pushState({pageTitle: title}, "", location);
            };

            Router.getStateParam = function (name) {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");

                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                    results = regex.exec(location.search);

                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
            };

            Router.getStateParams = function () {
                var match,
                    stateParams = {},
                    pl     = /\+/g,  // Regex for replacing addition symbol with a space
                    search = /([^&=]+)=?([^&]*)/g,
                    decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
                    query  = window.location.search.substring(1);

                while (match = search.exec(query))
                {
                    var name = decode(match[1]);
                    if (excluded_params.indexOf(name) === -1)
                        stateParams[name] = decode(match[2]);
                }

                return stateParams;
            };

            Router.setStateParams = function (params)
            {
                var title = routes[current_route];
                var url = get_state_url(current_route);
                var location  = url+params_to_string(params);
                $('title').html(title);
                $window.history.pushState({pageTitle: title}, "", location);
            };

            Router.initialize = function () {

                $(window).on('popstate', function() {
                    $timeout(function () {
                        var mode = Router.getStateParam('mode') || default_route;
                        Router.setState(mode);
                    }, 0);
                });

                Router.setState(Router.getStateParam('mode') || default_route, Router.getStateParams());

                return Router;
            };

            Router.isCurrentState = function (state) {
                return current_route === state
            };

            return Router.initialize();
        }

        return {
            setBaseUrl: function (url) {
                base_url = url
            },
            setDefaultRoute: function (route) {
                default_route = route
            },
            addRoute: function (name, title) {
                routes[name] = title;
            },
            $get: RouterFactory
        }
    }
})();