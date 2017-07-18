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
<div class="row">
    <div class="col-md-8 form-group">
        <yandex-map ></yandex-map>
    </div>
    <div class="col-md-4">
        <form-group label="Map style">
            <select class="form-control"
                    ng-model="config.type"
                    ng-options="style as label for (style, label) in mapStyles"
                    ng-change="mapStyleChange()">
            </select>
        </form-group>
        <form-group label="Type selector">
            <prestashop-switch ng-model="config.controls.type_selector"
                               ng-change="mapTypeSelectorChange()">
            </prestashop-switch>
        </form-group>
        <form-group  label="Zoom control">
            <prestashop-switch ng-model="config.controls.zoom_control" ng-change="mapControlChange('zoom_control')"></prestashop-switch>
        </form-group>
        <form-group label="Search control">
            <prestashop-switch ng-model="config.controls.search_control" ng-change="mapControlChange('search_control')"></prestashop-switch>
        </form-group>
        <form-group label="Ruler control">
            <prestashop-switch ng-model="config.controls.ruler_control" ng-change="mapControlChange('ruler_control')"></prestashop-switch>
        </form-group>
        <form-group label="Traffic control">
            <prestashop-switch ng-model="config.controls.traffic_control" ng-change="mapControlChange('traffic_control')"></prestashop-switch>
        </form-group>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-8">
        <form-group label="Map center">
            <div class="row">
                <div class="col-md-6">
                    <input class="form-control" type="number" ng-model="config.center.lat" ng-change="mapCenterChange()">
                </div>
                <div class="col-md-6">
                    <input class="form-control" type="number" ng-model="config.center.lng" ng-change="mapCenterChange()">
                </div>
            </div>
        </form-group>
    </div>
    <div class="col-md-4">
        <form-group label="Map zoom">
            <input class="form-control" type="number" ng-model="config.zoom" ng-change="mapZoomChange()">
        </form-group>
    </div>
</div>
<div class="row form-group" >
    <div class="col-md-8">
        <form-group label="Placemark position">
            <div class="row">
                <div class="col-md-6">
                    <input class="form-control" type="number" ng-model="config.placemark.position.lat" ng-change="placemarkPositionChange()">
                </div>
                <div class="col-md-6">
                    <input class="form-control" type="number" ng-model="config.placemark.position.lng" ng-change="placemarkPositionChange()">
                </div>
            </div>
        </form-group>
    </div>
    <div class="col-md-4">
        <form-group label="Placemark style">
            <select class="form-control"
                    ng-model="config.placemark.style"
                    ng-options="style as label for (style, label) in placeMarkStyles"
                    ng-change="placemarkStyleChange()">
            </select>
        </form-group>
    </div>
</div>
<div class="row ">
    <div class="col-md-8">
        <form-group label="Placemark content">
            <textarea class="form-control" type="text" ng-model="config.placemark.content" ng-change="placemarkContentChange()"></textarea>
        </form-group>
    </div>
</div>
{/literal}
