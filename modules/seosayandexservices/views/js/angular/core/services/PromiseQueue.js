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

/**
 * Created by andrew on 11.08.15.
 */
(function () {
    angular.module('sya.core').constant('PromiseQueue', PromiseQueue);


    function PromiseQueue (service) {
        this.service = service;
        this.queue = {}
    }

    PromiseQueue.prototype.cancel = function (key) {
        if (this.queue[key])
        {
            this.service.cancel(this.queue[key]);
            this.remove(key)
        }
    };

    PromiseQueue.prototype.add = function (key, promise) {
        this.cancel(key);

        this.queue[key] = promise;
        var that = this;
        promise.then(function () {
            that.remove(key)
        });
    };

    PromiseQueue.prototype.remove = function (key) {
        this.queue[key] = undefined;
    };

})();