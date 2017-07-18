{*
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
*}

{literal}
    <div ng-controller="YMarketConfigureLeftFormController as market">
        <panel heading="Actions">
            <form-group label="GZip Compression">
                <prestashop-switch ng-model="market.config.gzip"
                                   ng-change="market.saveGZipConfigurationValue()"></prestashop-switch>
            </form-group>
            <form-group label="Public feed">
                <prestashop-switch ng-model="market.config.public_feed"
                                   ng-change="market.savePublicFeedConfigurationValue()"></prestashop-switch>
            </form-group>
            <div ng-if="market.config.public_feed">
                <div class="form-group">
                    <label translate="Dynamic feed"></label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button copy-button="market.getDynamicURL()" translate-title="Copy" class="btn btn-default">
                                <span class="icon icon-copy"></span>
                            </button>
                        </span>
                        <input ng-value="market.getDynamicURL()" readonly="readonly" class="form-control"/>
                        <span class="input-group-btn">
                            <a target="_blank" dyn-href="market.getDynamicURL()" translate-title="Open" class="btn btn-default">
                                <span class="icon icon-link"></span>
                            </a>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label translate="Static feed"></label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button copy-button="market.getStaticURL()" translate-title="Copy" class="btn btn-default">
                                <span class="icon icon-copy"></span>
                            </button>
                        </span>
                        <input ng-value="market.getStaticURL()" readonly="readonly" class="form-control"/>
                        <span class="input-group-btn">
                            <a target="_blank" dyn-href="market.getStaticURL()" translate-title="Open" class="btn btn-default">
                                <span class="icon icon-link"></span>
                            </a>
                        </span>
                    </div>
                    <p class="help-block" translate="May be not exists"></p>
                </div>
                <div class="form-group">
                    <label translate="CRON task"></label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button copy-button="market.getCRONCommand()" translate-title="Copy" class="btn btn-default">
                                <span class="icon icon-copy"></span>
                            </button>
                        </span>
                        <input ng-value="market.getCRONCommand()" readonly="readonly" class="form-control"/>
                        <span class="input-group-btn">
                            <button ng-disabled="market.generating" ng-click="market.execCRONCommand()" class="btn btn-default" translate-title="Generate now">
                                <span class="icon icon-cogs"></span>
                            </button>
                        </span>
                    </div>
                    <p class="help-block" translate="Cron command for generating static file"></p>
                </div>
            </div>
            <hr>
            <div class="text center">
                <button class="btn btn-success btn-download full-width"  ng-click="market.download()">
                    <span class="process-icon-download"></span>
                    <span translate="Download file"></span>
                </button>
            </div>
        </panel>
    </div>
{/literal}