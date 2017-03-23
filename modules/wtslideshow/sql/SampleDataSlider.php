<?php
/**
* 2007-2014 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SampleDataSlider
{
	public function initData()
	{
		$return = true;
		$languages = Language::getLanguages(true);
		$id_shop = Configuration::get('PS_SHOP_DEFAULT');
		
		$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'wtslideshow` (`id_wtslideshow`, `position`, `slidedelay`, `transition2d`, `transition3d`, `timeshift`, `active`) VALUES 
		(1, 0, 4000, \'["20"]\', "false", 0, 1),
		(2, 0, 4000, \'["109"]\', "false", 0, 1),
		(3, 0, 4000, \'["23"]\', "false", 0, 1);
		');
		
		$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'wtslideshow_shop` (`id_wtslideshow`, `id_shop`, `position`, `slidedelay`, `transition2d`, `transition3d`, `timeshift`, `active`) VALUES 
		(1, "'.$id_shop.'", 0, 4000, \'["20"]\', "false", 0, 1),
		(2, "'.$id_shop.'", 0, 4000, \'["109"]\', "false", 0, 1),
		(3, "'.$id_shop.'", 0, 4000, \'["23"]\', "false", 0, 1);
		');
		
		$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'wtslideshow_options` (`id_wtslideshow_op`, `options`) VALUES 
		(1, \'{"fullwidth":"true","width":"1920","height":"843","responsive":"true","responsiveUnder":"1920","layersContainer":"1920","showmobile":"true","autoStart":"true","pauseOnHover":"true","firstSlide":"1","animateFirstSlide":"true","loops":"0","forceLoopNum":"true","towWaySlideshow":"false","randomSlideshow":"true","skin":"v5","skinsPath":"views\\/css\\/skins\\/","globalBGColor":"transparent","globalBGImage":"false","navPrevNext":"true","navStartStop":"false","navButtons":"true","hoverPrevNext":"true","hoverBottomNav":"true","keybNav":"true","touchNav":"true","showBarTimer":"false","showCircleTimer":"false","thumbnailNavigation":"hover","tnContainerWidth":"60%","tnWidth":"100","tnHeight":"60","tnActiveOpacity":"35","tnInactiveOpacity":"100","autoPlayVideos":"true","autoPauseSlideshow":"auto","youtubePreview":"maxresdefault.jpg","imgPreload":"true","lazyLoad":"true","yourLogo":"false","yourLogoStyle":"left: -10px; top: -10px;","yourLogoLink":"false","yourLogoTarget":"_blank"}\')
		');
		
		$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'wtslideshow_options_shop` (`id_wtslideshow_op`, `id_shop`, `options`) VALUES 
		(1, "'.$id_shop.'", \'{"fullwidth":"true","width":"1920","height":"843","responsive":"true","responsiveUnder":"1920","layersContainer":"1920","showmobile":"true","autoStart":"true","pauseOnHover":"true","firstSlide":"1","animateFirstSlide":"true","loops":"0","forceLoopNum":"true","towWaySlideshow":"false","randomSlideshow":"true","skin":"v5","skinsPath":"views\\/css\\/skins\\/","globalBGColor":"transparent","globalBGImage":"false","navPrevNext":"true","navStartStop":"false","navButtons":"true","hoverPrevNext":"true","hoverBottomNav":"true","keybNav":"true","touchNav":"true","showBarTimer":"false","showCircleTimer":"false","thumbnailNavigation":"hover","tnContainerWidth":"60%","tnWidth":"100","tnHeight":"60","tnActiveOpacity":"35","tnInactiveOpacity":"100","autoPlayVideos":"true","autoPauseSlideshow":"auto","youtubePreview":"maxresdefault.jpg","imgPreload":"true","lazyLoad":"true","yourLogo":"false","yourLogoStyle":"left: -10px; top: -10px;","yourLogoLink":"false","yourLogoTarget":"_blank"}\')
		');
		
		$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'wtslideshow_caption` (`id_caption`, `id_wtslideshow`, `type`, `order`, `params`) VALUES 
		(3, 1, 2, 300, \'{"style":"big_black","parallaxlevel":"2","class":"","datax":"435","datay":"223","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"300","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(4, 1, 2, 600, \'{"style":"big_black","parallaxlevel":"0","class":"","datax":"1307","datay":"302","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"600","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(5, 1, 1, 900, \'{"style":"very_big_white","parallaxlevel":"1","class":"","datax":"1132","datay":"406","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"900","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(6, 1, 1, 1200, \'{"style":"big_white","parallaxlevel":"0","class":"","datax":"1206","datay":"498","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1200","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(7, 1, 1, 1500, \'{"style":"medium_text","parallaxlevel":"0","class":"","datax":"1191","datay":"570","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1500","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(8, 1, 1, 1800, \'{"style":"small_text","parallaxlevel":"0","class":"","datax":"1421","datay":"652","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1800","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(9, 1, 1, 2100, \'{"style":"grassfloor","parallaxlevel":"0","class":"","datax":"1314","datay":"652","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"2100","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(11, 2, 2, 600, \'{"style":"big_black","parallaxlevel":"0","class":"","datax":"1305","datay":"280","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"600","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(12, 2, 1, 900, \'{"style":"very_big_white","parallaxlevel":"1","class":"","datax":"1169","datay":"369","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"900","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(13, 2, 1, 1200, \'{"style":"big_white","parallaxlevel":"0","class":"","datax":"1180","datay":"476","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1200","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(14, 2, 1, 1500, \'{"style":"medium_text","parallaxlevel":"0","class":"","datax":"1143","datay":"552","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1500","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(15, 2, 1, 1800, \'{"style":"small_text","parallaxlevel":"0","class":"bg-green","datax":"1400","datay":"624","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1800","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(16, 2, 1, 2100, \'{"style":"grassfloor","parallaxlevel":"0","class":"","datax":"1292","datay":"624","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"2100","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(18, 3, 2, 300, \'{"style":"big_black","parallaxlevel":"2","class":"","datax":"450","datay":"270","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"300","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(19, 3, 2, 600, \'{"style":"big_black","parallaxlevel":"0","class":"","datax":"1261","datay":"287","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"600","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(20, 3, 1, 900, \'{"style":"very_big_white","parallaxlevel":"1","class":"","datax":"1142","datay":"381","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"900","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(21, 3, 1, 1200, \'{"style":"big_white","parallaxlevel":"0","class":"","datax":"1222","datay":"476","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1200","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(22, 3, 1, 1500, \'{"style":"medium_text","parallaxlevel":"0","class":"orange","datax":"1088","datay":"538","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1500","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(23, 3, 1, 1800, \'{"style":"small_text","parallaxlevel":"0","class":"bg-orange","datax":"1357","datay":"605","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1800","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(24, 3, 1, 2100, \'{"style":"grassfloor","parallaxlevel":"0","class":"","datax":"1252","datay":"605","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"2100","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(25, 2, 2, 2100, \'{"style":"big_black","parallaxlevel":"2","class":"","datax":"427","datay":"285","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"2100","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\');');
		
		$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'wtslideshow_caption_shop` (`id_caption`, `id_wtslideshow`, `id_shop`, `type`, `order`, `params`) VALUES 
		(3, 1, "'.$id_shop.'", 2, 300, \'{"style":"big_black","parallaxlevel":"2","class":"","datax":"435","datay":"223","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"300","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(4, 1, "'.$id_shop.'", 2, 600, \'{"style":"big_black","parallaxlevel":"0","class":"","datax":"1307","datay":"302","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"600","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(5, 1, "'.$id_shop.'", 1, 900, \'{"style":"very_big_white","parallaxlevel":"1","class":"","datax":"1132","datay":"406","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"900","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(6, 1, "'.$id_shop.'", 1, 1200, \'{"style":"big_white","parallaxlevel":"0","class":"","datax":"1206","datay":"498","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1200","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(7, 1, "'.$id_shop.'", 1, 1500, \'{"style":"medium_text","parallaxlevel":"0","class":"","datax":"1191","datay":"570","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1500","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(8, 1, "'.$id_shop.'", 1, 1800, \'{"style":"small_text","parallaxlevel":"0","class":"","datax":"1421","datay":"652","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1800","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(9, 1, "'.$id_shop.'", 1, 2100, \'{"style":"grassfloor","parallaxlevel":"0","class":"","datax":"1314","datay":"652","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"2100","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(11, 2, "'.$id_shop.'", 2, 600, \'{"style":"big_black","parallaxlevel":"0","class":"","datax":"1305","datay":"280","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"600","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(12, 2, "'.$id_shop.'", 1, 900, \'{"style":"very_big_white","parallaxlevel":"1","class":"","datax":"1169","datay":"369","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"900","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(13, 2, "'.$id_shop.'", 1, 1200, \'{"style":"big_white","parallaxlevel":"0","class":"","datax":"1180","datay":"476","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1200","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(14, 2, "'.$id_shop.'", 1, 1500, \'{"style":"medium_text","parallaxlevel":"0","class":"","datax":"1143","datay":"552","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1500","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(15, 2, "'.$id_shop.'", 1, 1800, \'{"style":"small_text","parallaxlevel":"0","class":"bg-green","datax":"1400","datay":"624","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1800","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(16, 2, "'.$id_shop.'", 1, 2100, \'{"style":"grassfloor","parallaxlevel":"0","class":"","datax":"1292","datay":"624","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"2100","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(18, 3, "'.$id_shop.'", 2, 300, \'{"style":"big_black","parallaxlevel":"2","class":"","datax":"450","datay":"270","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"300","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(19, 3, "'.$id_shop.'", 2, 600, \'{"style":"big_black","parallaxlevel":"0","class":"","datax":"1261","datay":"287","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"600","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(20, 3, "'.$id_shop.'", 1, 900, \'{"style":"very_big_white","parallaxlevel":"1","class":"","datax":"1142","datay":"381","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"900","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(21, 3, "'.$id_shop.'", 1, 1200, \'{"style":"big_white","parallaxlevel":"0","class":"","datax":"1222","datay":"476","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1200","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(22, 3, "'.$id_shop.'", 1, 1500, \'{"style":"medium_text","parallaxlevel":"0","class":"orange","datax":"1088","datay":"538","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1500","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(23, 3, "'.$id_shop.'", 1, 1800, \'{"style":"small_text","parallaxlevel":"0","class":"bg-orange","datax":"1357","datay":"605","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"1800","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(24, 3, "'.$id_shop.'", 1, 2100, \'{"style":"grassfloor","parallaxlevel":"0","class":"","datax":"1252","datay":"605","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"2100","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\'),
		(25, 2, "'.$id_shop.'", 2, 2100, \'{"style":"big_black","parallaxlevel":"2","class":"","datax":"427","datay":"285","offsetxin":"80","offsetxout":"-180","offsetyin":"0","offsetyout":"0","delayin":"2100","showuntil":"0","durationin":"1000","durationout":"1000","easingin":"easeInOutQuint","easingout":"easeInOutQuint","fadein":"true","fadeout":"true","rotatein":"0","rotateout":"0","rotatexin":"0","rotatexout":"0","rotateyin":"0","rotateyout":"0","scalexin":"1","scalexout":"1","scaleyin":"1","scaleyout":"1","skewxin":"0","skewxout":"0","skewyin":"0","skewyout":"0","transformoriginin":"50% 50% 0","transformoriginout":"50% 50% 0","widthv":"","heightv":"","typev":""}\');');
		
		foreach ($languages as $language)
		{
			$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'wtslideshow_lang` (`id_wtslideshow`, `id_lang`, `id_shop`, `title`, `url`, `image`, `thumbnail`) VALUES 
			(1, "'.$language['id_lang'].'", "'.$id_shop.'", "Slider title", "#", "b43427829f86cbaa708cc27152ba73fbdcb4fa01_bg-slider1.jpg", ""),
			(2, "'.$language['id_lang'].'", "'.$id_shop.'", "Slider title", "#", "edd6baf9c646b08eb4c4cd35a38a281ba1ca77f7_bg-slider3.jpg", ""),
			(3, "'.$language['id_lang'].'", "'.$id_shop.'", "Slider title", "#", "f6e235f0ac5d4a28d746813254df9af4d78c02a7_bg-slider2.jpg", "");
			');
			
			$return &= Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'wtslideshow_caption_lang` (`id_caption`, `id_shop`, `id_lang`, `captext`, `capimage`, `capvideo`, `link`) VALUES 
			(3, "'.$id_shop.'", "'.$language['id_lang'].'", "Image 0", "18d42ea86650532146144ecfc606b872d89bfc3b_slider-1.png", "", "#"),
			(4, "'.$id_shop.'", "'.$language['id_lang'].'", "Image 1", "81f48491d305c7628a578162635fc8978c8f9574_caption-1.png", "", "#"),
			(5, "'.$id_shop.'", "'.$language['id_lang'].'", "Blackside", "Layer Image 2", "Layer Video 2", "#"),
			(6, "'.$id_shop.'", "'.$language['id_lang'].'", "The Zermatt Jacket", "Layer Image 3", "Layer Video 3", "#"),
			(7, "'.$id_shop.'", "'.$language['id_lang'].'", "Lorem ipsum dolor sit amet, consectetur adipisicing elite </br> tempor incididunt ut labore et dolore magna", "Layer Image 4", "Layer Video 4", "#"),
			(8, "'.$id_shop.'", "'.$language['id_lang'].'", "Shop Now", "Layer Image 5", "Layer Video 5", "#"),
			(9, "'.$id_shop.'", "'.$language['id_lang'].'", "£295.00", "Layer Image 6", "Layer Video 6", "#"),
			(11, "'.$id_shop.'", "'.$language['id_lang'].'", "Image 1", "05902054b40cc8de7153b6afb6f885f37f794356_caption-2.png", "", "#"),
			(12, "'.$id_shop.'", "'.$language['id_lang'].'", "a’Soccer", "Layer Image 2", "Layer Video 2", "#"),
			(13, "'.$id_shop.'", "'.$language['id_lang'].'", "The best design suit", "Layer Image 3", "Layer Video 3", "#"),
			(14, "'.$id_shop.'", "'.$language['id_lang'].'", "ellentesque finibus egestas aliquet. Quisque mollis urna turpis</br>Nam sollicitudin sapien luctus facilisis sollicitudin", "Layer Image 4", "Layer Video 4", "#"),
			(15, "'.$id_shop.'", "'.$language['id_lang'].'", "shop now", "Layer Image 5", "Layer Video 5", "#"),
			(16, "'.$id_shop.'", "'.$language['id_lang'].'", "£295.00", "Layer Image 6", "Layer Video 6", "#"),
			(18, "'.$id_shop.'", "'.$language['id_lang'].'", "Image 0", "e8788957af52100224edfd6367132eff784c2429_slider-2.png", "", "#"),
			(19, "'.$id_shop.'", "'.$language['id_lang'].'", "Image 1", "05902054b40cc8de7153b6afb6f885f37f794356_caption-2.png", "", "#"),
			(20, "'.$id_shop.'", "'.$language['id_lang'].'", "Helmets", "Layer Image 2", "Layer Video 2", "#"),
			(21, "'.$id_shop.'", "'.$language['id_lang'].'", "Best protection", "Layer Image 3", "Layer Video 3", "#"),
			(22, "'.$id_shop.'", "'.$language['id_lang'].'", "Aenean elementum ac eros sed sagittis. Nullam a posuere lorem</br>condimentum purus, id fermentum mi hendrerit", "Layer Image 4", "Layer Video 4", "#"),
			(23, "'.$id_shop.'", "'.$language['id_lang'].'", "shop now", "Layer Image 5", "Layer Video 5", "#"),
			(24, "'.$id_shop.'", "'.$language['id_lang'].'", "£295.00", "Layer Image 6", "Layer Video 6", "#"),
			(25, "'.$id_shop.'", "'.$language['id_lang'].'", "Image 6", "07844bd529fe8f9be9b292a173c6d4dbc11d2ba4_slider-3.png", "", "#");');
		}
		return $return;
	}
}
?>