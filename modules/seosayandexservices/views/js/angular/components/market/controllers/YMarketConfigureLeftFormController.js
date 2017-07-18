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
    angular.module('sya.market')
        .controller('YMarketConfigureLeftFormController', YMarketConfigureLeftFormController);

    YMarketConfigureLeftFormController.$inject = ['market_config', 'base_url', 'market_urls', 'Notifier', 'Api', 'Tools'];
    function YMarketConfigureLeftFormController(market_config, base_url, market_urls, Notifier, Api, Tools) {
        this.config = market_config;
        this.getDynamicURL = function () {
            return market_urls.dynamic;
        };

        this.getCRONCommand = function () {
            return 'wget '+market_urls.cron;
        };

        this.generating = false;
        this.execCRONCommand = function () {
            var that = this;
            that.generating = true;
            Api.post(['market', 'generate_static_feed']).then(function () {
                that.generating = false;
                Notifier.notify('Feed has been generated');
            }, function () {
                that.generating = false;
            })
        };

        this.download = function () {
            var gzip = this.config.gzip;
            Notifier.notify('Downloading');

            Api.post(['market', 'generate_feed'], {}, 'binary').then(function (response) {
                Tools.downloadData(
                    'yml.xml'+(gzip ? '.gz' :''),
                    response,
                    gzip ? 'application/xml-dtd' : 'application/gzip'
                )
            });
        };

        this.getStaticURL = function () {
            return base_url+'yml.xml'+(this.config.gzip ? '.gz' :'');
        };

        this.saveGZipConfigurationValue = function () {
            var value = this.config.gzip;
            Api.post(['market', 'set_gzip'], {value: value}).then(function () {
                if (value)
                    Notifier.notify('GZip enabled');
                else
                    Notifier.notify('GZip disabled');
            });
        };

        this.savePublicFeedConfigurationValue = function () {
            var value = this.config.public_feed;
            Api.post(['market', 'set_public_feed'], {value: value}).then(function () {
                if (value)
                    Notifier.notify('Now feed is public');
                else
                    Notifier.notify('Now feed is private');
            });
        };
    }
})();