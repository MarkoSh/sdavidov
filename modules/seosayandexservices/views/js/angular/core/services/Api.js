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

'use strict';
(function () {
    angular.module('sya.core')
        .factory('Api', ApiFactory);

    ApiFactory.$inject = ['Tools', '$q', '$log', 'api_url'];

    function ApiFactory(Tools, $q, $log, api_url) {
        var Api = {};

        var deferreds = {};
        var requestid = 0;
        function request (method, rpc, formData, dataType, ajaxOpts, disableJsonRequest)
        {
            dataType = dataType || 'json';
            formData = formData || {};
            var deferred = $q.defer();

            var request_data;
            if (formData instanceof FormData) {
                request_data = formData;
            }
            else {
                request_data = {};
                if (dataType === 'json' && !disableJsonRequest)
                    request_data['json_request'] = JSON.stringify(formData);
                else
                    request_data = formData;

            }

            var append_to_request_data = function (key, value) {
                if (request_data instanceof FormData)
                    request_data.append(key, value);
                 else
                    request_data[key] = value;
            };

            append_to_request_data('ajax', true);
            if (angular.isString(rpc)) {
                append_to_request_data('action',rpc);
            } else if (angular.isArray(rpc)) {
                append_to_request_data('component', rpc[0]);
                append_to_request_data('action', rpc[1]);
            } else {
                append_to_request_data('component', rpc.component);
                append_to_request_data('action', rpc.action);
            }


            var params = {
                type: method,
                cache: false,
                url:  api_url,
                dataType: dataType,
                success: Tools.asynchronize(function (response) {
                    if (deferred.canceled) return;
                    if (dataType.toLocaleLowerCase() === 'json' )
                    {
                        if (response && response.status !== 200) {
                            if (response.data && response.data.errors)
                                response.data.errors.map(function (err) { $log.error(err)} );
                            return deferred.reject(response.data);
                        }

                        return deferred.resolve(response.data);
                    }

                    return deferred.resolve(response);
                }),
                error: Tools.asynchronize(function (xhr) {
                    if (deferred.canceled) return;

                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response && response.data && !deferred.canceled)
                        {
                            deferred.reject(response.data);
                            return;
                        }
                    } catch (e) {
                        $log.error(xhr.responseText || 'Unknown');
                    }

                    deferred.reject({log: {level: 'critical', message: xhr.status+': '+ xhr.statusText}});
                }),
                data: request_data
            };

            if (formData instanceof FormData) {
                params.processData = false;
                params.contentType = false;
            }

            params = angular.extend(params, ajaxOpts || {});

            deferred.promise.$$ajaxId = ++requestid;
            deferreds[deferred.promise.$$ajaxId] = deferred;
            deferreds[deferred.promise.$$ajaxId].$$ajax = $.ajax(params);

            return deferred.promise;
        }

        function cancel (promise) {
            if (promise && promise.$$ajaxId in deferreds) {
                deferreds[promise.$$ajaxId].canceled = true;
                deferreds[promise.$$ajaxId].$$ajax.abort();
                delete deferreds[promise.$$ajaxId];
                return true;
            }
            return false;
        }

        Api.request = request;
        Api.cancel  = cancel;

        Api.get = function (rpc, query, dataType, ajaxOpts) {
            return request('get', rpc, query, dataType, ajaxOpts);
        };

        Api.post = function (rpc, query, dataType, ajaxOpts) {
            return request('post', rpc, query, dataType, ajaxOpts);
        };

        Api['delete'] = function (rpc, query, dataType, ajaxOpts) {
            return request('delete', rpc, query, dataType, ajaxOpts);
        };

        return Api;
    }
})();