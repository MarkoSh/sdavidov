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
    angular.module('sya.core').factory('Tools', ToolsFactory);

    ToolsFactory.$inject = ['$timeout', '$q'];

    function ToolsFactory($timeout, $q) {
        var Tools = {};

        Tools.convertMemory = function convertMemory(pBytes, pUnits)
        {
            pBytes = parseFloat(pBytes);
            // Handle some special cases
            if(pBytes == 0) return '0 Bytes';
            if(pBytes == 1) return '1 Byte';
            if(pBytes == -1) return '-1 Byte';

            var bytes = Math.abs(pBytes);
            var orderOfMagnitude, abbreviations;
            if(pUnits && pUnits.toLowerCase && pUnits.toLowerCase() == 'si') {
                // SI units use the Metric representation based on 10^3 as a order of magnitude
                orderOfMagnitude = Math.pow(10, 3);
                abbreviations = ['Bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            } else {
                // IEC units use 2^10 as an order of magnitude
                orderOfMagnitude = Math.pow(2, 10);
                abbreviations = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
            }
            var i = Math.floor(Math.log(bytes) / Math.log(orderOfMagnitude));
            var result = (bytes / Math.pow(orderOfMagnitude, i));

            // This will get the sign right
            if (pBytes < 0) {
                result *= -1;
            }

            // This bit here is purely for show. it drops the percision on numbers greater than 100 before the units.
            // it also always shows the full number of bytes if bytes is the unit.
            if(result >= 99.995 || i==0) {
                return result.toFixed(0) + ' ' + abbreviations[i];
            } else {
                return result.toFixed(2) + ' ' + abbreviations[i];
            }
        };

        Tools.asynchronize = function asynchronize(func) {
            return function () {
                var context = this;
                var args = arguments;
                $timeout(function () {
                    func.apply(context, args)
                }, 0)
            }
        };

        Tools.getProperty = function getProperty(object, path) {
            var parts = path.split('.');

            for (var i = 0; i< parts.length; i++) {
                if (object && object.hasOwnProperty(parts[i]))
                    object = object[parts[i]];
                else
                    return undefined;
            }

            return object;
        };

        Tools.setProperty = function setProperty(object, path, value) {
            var parts = path.split('.');

            for (var i = 0; i < parts.length; i++) {
                if (object && object.hasOwnProperty(parts[i]))
                {
                    if (i < parts.length - 1) {
                        object = object[parts[i]];
                    } else {
                        object[ parts[i]] = value;
                        return true;
                    }
                }
                else
                    return false;
            }
        };

        Tools.getTemplate = function (options) {
            return options.template ? $q.when(options.template) :
                $http.get(angular.isFunction(options.templateUrl) ? (options.templateUrl)() : options.templateUrl,
                    {cache: $templateCache}).then(function (result) {
                        return result.data;
                    });
        };

        /**
         * @param number
         * @returns {string}
         */
        Tools.decbin = function (number) {
            if (number < 0) {
                number = 0xFFFFFFFF + number + 1;
            }
            return parseInt(number, 10).toString(2);
        };

        /**
         * @param binary
         * @returns {Number|number}
         */
        Tools.bindec = function (binary) {
            return parseInt(binary+'', 2) || 0;
        };

        /**
         * @param {number} number
         * @returns {string}
         */
        Tools.dechex = function (number) {
            return number.toString(16);
        };

        /**
         * @param hex
         * @returns {number}
         */
        Tools.hexdec = function (hex) {
            return parseInt(hex, 16);
        };

        /**
         * @param {string} hex
         * @returns {number}
         */
        Tools.getBrightness = function (hex)
        {
            hex = hex.replace('#', '', hex);

            var r = Tools.hexdec(hex.substr(0, 2));
            var g = Tools.hexdec(hex.substr(2, 2));
            var b = Tools.hexdec(hex.substr(4, 2));

            return ((r * 299) + (g * 587) + (b * 114)) / 1000;
        };

        var keyCodes = {
            'backspace': 8,
            'tab': 9,
            'enter': 13,
            'shift': 16,
            'ctrl': 17,
            'alt': 18,
            'pause/break': 19,
            'caps lock': 20,
            'escape': 27,
            'space': 32,
            'page up': 33,
            'page down': 34,
            'end': 35,
            'home': 36,
            'left arrow': 37,
            'up arrow': 38,
            'right arrow': 39,
            'down arrow': 40,
            'insert': 45,
            'delete': 46,
            '0': 48,
            '1': 49,
            '2': 50,
            '3': 51,
            '4': 52,
            '5': 53,
            '6': 54,
            '7': 55,
            '8': 56,
            '9': 57,
            'a': 65,
            'b': 66,
            'c': 67,
            'd': 68,
            'e': 69,
            'f': 70,
            'g': 71,
            'h': 72,
            'i': 73,
            'j': 74,
            'k': 75,
            'l': 76,
            'm': 77,
            'n': 78,
            'o': 79,
            'p': 80,
            'q': 81,
            'r': 82,
            's': 83,
            't': 84,
            'u': 85,
            'v': 86,
            'w': 87,
            'x': 88,
            'y': 89,
            'z': 90,
            'left window key': 91,
            'right window key': 92,
            'select key': 93,
            'numpad 0': 96,
            'numpad 1': 97,
            'numpad 2': 98,
            'numpad 3': 99,
            'numpad 4': 100,
            'numpad 5': 101,
            'numpad 6': 102,
            'numpad 7': 103,
            'numpad 8': 104,
            'numpad 9': 105,
            'multiply': 106,
            'add': 107,
            'subtract': 109,
            'decimal point': 110,
            'divide': 111,
            'f1': 112,
            'f2': 113,
            'f3': 114,
            'f4': 115,
            'f5': 116,
            'f6': 117,
            'f7': 118,
            'f8': 119,
            'f9': 120,
            'f10': 121,
            'f11': 122,
            'f12': 123,
            'num lock': 144,
            'scroll lock': 145,
            'semi-colon': 186,
            'equal sign': 187,
            'comma': 188,
            'dash': 189,
            'period': 190,
            'forward slash': 191,
            'grave accent': 192,
            'open bracket': 219,
            'back slash': 220,
            'close braket': 221,
            'single quote': 222
        };

        Tools.bindKeydownEvent = function () {
            var $element, $target, $command, $listener;
            if (arguments.length == 3) {
                $element = arguments[0];
                $command = arguments[1];
                $listener = arguments[2];
            } else if (arguments.length == 4) {
                $element = arguments[0];
                $target = arguments[1];
                $command = arguments[2];
                $listener = arguments[3];
            }

            var args = ['keydown'];
            if ($target)
                args.push($target);

            $command = $command.toLowerCase().split('+');
            var ctrl = $command.indexOf('ctrl') > -1;
            var shift = $command.indexOf('shift') > -1;
            var alt = $command.indexOf('alt') > -1;
            var keyCode = keyCodes[$command[$command.length-1]];

            args.push(function (e) {
                if (e.keyCode !== keyCode) return;
                if (alt !== e.altKey) return;
                if (ctrl !== e.ctrlKey) return;
                if (shift !== e.shiftKey) return;

                var args = arguments;
                var context = this;
                $timeout(function () {
                    $listener.apply(context, args);
                })
            });

            $element.on.apply($element, args)
        };

        Tools.snakeCaseToCamelCase = function (string){
            return string.replace(/(\-\w)/g, function(match){return match[1].toUpperCase();});
        };

        Tools.underscoreToCamelCase = function (string){
            return string.replace(/(\_\w)/g, function(match){return match[1].toUpperCase();});
        };

        Tools.lcFirstFilter = function (string) {
            var f = string.charAt(0).toLowerCase();
            return f + string.substr(1, string.length-1);
        };

        Tools.rejectRequest = function (worker, loading, response) {
            if (response && response.log)
                worker.console.printLog(response.log);

            if (loading)
                loading.cancel();

            return $q.reject();
        };

        /**
         * @param worker
         * @param loading
         * @returns {Function}
         */
        Tools.rejectRequestCallback = function (worker, loading)
        {
            return function (response) {
                return Tools.rejectRequest(worker, loading, response)
            }
        };

        Tools.returnReject = function (data) {
            return $q.reject(data);
        };

        Tools.downloadData = function (name, data, type) {
            var a = document.createElement("a");
            a.style.display = "none";
            document.body.appendChild(a);

            var blob = new Blob([data], {type: type || 'text/plain'});

            var url = window.URL.createObjectURL(blob);

            a.href = url;
            a.download = name;
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        };

        return Tools;
    }
})();
