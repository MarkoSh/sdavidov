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
    <div ng-if="dnd">
        <div class="form-group">
            <label translate="Preview"></label>
            <div class="share-preview-wrapper" ng-class="config.theme">
                <share ng-init="buildShareElement(true)"></share>
            </div>
        </div>
        <form-group label="Element">
            <div class="row">
                <div class="col-md-3">
                    <form-group label="Theme">
                        <select ng-model="config.theme"
                                ng-change="buildShareElement()"
                                ng-options="theme.name as theme.label for theme in themes"
                        ></select>
                    </form-group>
                </div>
                <div class="col-md-3">
                    <form-group label="Style">
                        <select ng-model="config.style"
                                ng-change="buildShareElement()"
                                ng-options="style.name as style.label for style in styles"
                        ></select>
                    </form-group>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <form-group label="Border">
                        <prestashop-switch ng-model="config.border" ng-change="buildShareElement()"></prestashop-switch>
                    </form-group>
                </div>
                <div class="col-md-3">
                    <form-group label="Link underline">
                        <prestashop-switch ng-model="config.linkUnderline" ng-change="buildShareElement()"></prestashop-switch>
                    </form-group>
                </div>
                <div class="col-md-3">
                    <form-group label="Link icon">
                        <prestashop-switch ng-model="config.linkIcon" ng-change="buildShareElement()"></prestashop-switch>
                    </form-group>
                </div>
            </div>

        </form-group>
        <form-group label="Popup">
            <div class="row">
                <div class="col-md-3">
                    <form-group label="Copy paste field">
                        <prestashop-switch ng-model="config.copyPasteField" ng-change="buildShareElement()"></prestashop-switch>
                    </form-group>
                </div>
                <div class="col-md-3">
                    <form-group label="Direction">
                        <select ng-model="config.vDirection"
                                ng-change="buildShareElement()"
                                ng-options="direction.value as direction.label for direction in directions"
                        ></select>
                    </form-group>
                </div>
            </div>
        </form-group>

        <div class="row">
            <div class="col-md-5">
                <label translate="Socials"></label>
                <ul class="socials-list list-inline">
                    <li ng-repeat="social in socials" sya-draggable="dnd.draggable" class="draggable-social">
                        <span class="icon icon-social" ng-class="'icon-social-'+social.name"></span>
                        <span class="text" ng-bind="social.label">Blogger</span>
                    </li>
                </ul>
            </div>
            <div class="col-md-7">
                <div class="form-group">
                    <label translate="Main block"></label>
                    <div class="droppable-social">
                        <ul sya-sortable="dnd.sortable"
                            ng-model="config.main_block"
                            sya-init="restoreMainBlock($event)"
                            ng-change="buildShareElement()" class="socials-list socials-list-small list-inline">
                            <li class="hint" translate="Drag & Drop icons for add a button"></li>
                        </ul>
                        <div sya-droppable="dnd.droppable" class="trash-droparea">
                            <span class="icon icon-trash"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label translate="Popup blocks"></label>
                    <div ng-repeat="popup_block in config.popup_blocks">
                        <div class="form-group">
                            <label translate="Block links"></label>
                            <div class="input-group">
                                <div class="droppable-social">
                                    <ul sya-sortable="dnd.sortable"
                                        sya-init="restorePopupBlock($index, $event)"
                                        ng-model="popup_block.socials" ng-change="buildShareElement()" class="socials-list socials-list-small list-inline">
                                        <li class="hint" translate="Drag & Drop icons for add a button"></li>
                                    </ul>
                                    <div sya-droppable="dnd.droppable" class="trash-droparea">
                                        <span class="icon icon-trash"></span>
                                    </div>
                                </div>
                                <span class="input-group-btn">
                                    <button class="btn btn-danger" ng-click="removePopupBlock($index)">x</button>
                                </span>
                            </div>
                            <div class="form-group">
                                <label translate="Block title"></label>
                                <input type="text" class="form-control" ng-model="popup_block.title" ng-change="buildShareElementDelayed()">
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" ng-click="addPopupBlock()" translate="Add popup block" class="btn btn-default"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/literal}
