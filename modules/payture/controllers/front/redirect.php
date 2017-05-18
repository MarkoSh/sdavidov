<?php

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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
class PaytureRedirectModuleFrontController extends ModuleFrontController
{
    /**
     * Do whatever you have to before redirecting the customer on the website of your payment processor.
     */
    public function postProcess()
    {
        /**
         * Oops, an error occured.
         */
        if (Tools::getValue('action') == 'error') {
            return $this->displayError('An error occurred while trying to redirect the customer');
        } else {
            $cart_id = Context::getContext()->cart->id;
            $secure_key = Context::getContext()->customer->secure_key;
            $query = http_build_query(array(
                'Key' => 'Merchant',
                'Data' => implode(';', array(
                    'SessionType=Block',
                    'Product=',
                    'Total=' . Context::getContext()->cart->getOrderTotal(),
                    'OrderId=' . $cart_id,
                    'Amount=' . Context::getContext()->cart->getOrderTotal(),
                    'IP=' . $this->getRealIp(),
                    'Url=' . Context::getContext()->link->getModuleLink(
                        'payture',
                        'confirmation',
                        ['cart_id' => $cart_id, 'secure_key' => $secure_key],
                        true
                    )
                ))
            ));

            $responseXML = Tools::simplexml_load_file('https://sandbox.payture.com/apim/Init?' . $query);
            $responseAttributes = $responseXML->attributes();

            if (end($responseAttributes->{"Success"}) == 'True') {
                $session_id = $responseAttributes->{"SessionId"};
                Tools::redirect('https://sandbox.payture.com/apim/Pay?SessionId=' . $session_id);
                return true;
            } else {
                return $this->displayError(end($responseAttributes->{"ErrCode"}));
            }
        }
    }

    protected function getRealIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    protected function displayError($message, $description = false)
    {
        $errcodes = array(
            'NONE' => $this->module->l('Операция выполнена без ошибок'),
            'ACCESS_DENIED' => $this->module->l('Запрещены операции с данным набором параметров для терминала'),
            'AMOUNT_ERROR' => $this->module->l('Ошибка суммы операции. Превышена сумма либо сумма операции не проверена в биллинге'),
            'AMOUNT_EXCEED' => $this->module->l('Сумма транзакции превышает доступный остаток средств на выбранном счете'),
            'API_NOT_ALLOWED' => $this->module->l('Запрет использования API с данного IP'),
            'CARD_EXPIRED' => $this->module->l('Истек срок действия карты'),
            'CARD_NOT_FOUND' => $this->module->l('Не найдена карта по данному идентификатору'),
            'COMMUNICATE_ERROR' => $this->module->l('Ошибка связи в физических каналах'),
            'CURRENCY_NOT_ALLOWED' => $this->module->l('Валюта не разрешена для предприятия'),
            'CUSTOMER_NOT_FOUND' => $this->module->l('Пользователь не найден'),
            'DUPLICATE_ORDER_ID' => $this->module->l('Номер заказа уже использовался ранее'),
            'DUPLICATE_PROCESSING_ORDER_ID' => $this->module->l('Заказ существует в процессинге с данным идентификатором'),
            'DUPLICATE_CARD' => $this->module->l('Карта уже существует'),
            'DUPLICATE_USER' => $this->module->l('Пользователь уже зарегистрирован'),
            'EMPTY_RESPONSE' => $this->module->l('Пустой ответ процессинга'),
            'EMAIL_ERROR' => $this->module->l('Ошибка при обработке сообщения электронной почты ошибка отправки сообщения'),
            'FRAUD_ERROR' => $this->module->l('Недопустимая транзакция согласно настройкам антифродового фильтра'),
            'FRAUD_ERROR_BIN_LIMIT' => $this->module->l('Превышен лимит по карте(BINу, маске) согласно настройкам антифродового фильтра'),
            'FRAUD_ERROR_BLACKLIST_BANKCOUNTRY' => $this->module->l('Страна данного BINа находится в черном списке или не находится в списке допустимых стран'),
            'FRAUD_ERROR_BLACKLIST_AEROPORT' => $this->module->l('Аэропорт находится в черном списке'),
            'FRAUD_ERROR_BLACKLIST_USERCOUNTRY' => $this->module->l('Страна данного IP находится в черном списке или не находится в списке допустимых стран'),
            'FRAUD_ERROR_CRITICAL_CARD' => $this->module->l('Номер карты(BIN, маска) внесен в черный список антифродового фильтра'),
            'FRAUD_ERROR_CRITICAL_CUSTOMER' => $this->module->l('IP-адрес внесен в черный список антифродового фильтра'),
            'FRAUD_ERROR_IP' => $this->module->l('IP помечен как мошеннический'),
            'FRAUD_INTERNAL_ERROR' => $this->module->l('Ошибка антифрод фильтра при обработке транзакции'),
            'ILLEGAL_ORDER_STATE' => $this->module->l('Попытка выполнения недопустимой операции для текущего состояния платежа'),
            'INTERNAL_ERROR' => $this->module->l('Внутренняя ошибка шлюза'),
            'INVALID_PAYTUREID' => $this->module->l('Неверный fingerprint устройства'),
            'INVALID_SIGNATURE' => $this->module->l('Неверная подпись запроса'),
            'ISSUER_BLOCKED_CARD' => $this->module->l('Владелец карты пытается выполнить транзакцию, которая для него не разрешена банком-эмитентом, либо произошла внутренняя ошибка эмитента'),
            'ISSUER_CARD_FAIL' => $this->module->l('Банк-эмитент запретил интернет транзакции по карте'),
            'ISSUER_FAIL' => $this->module->l('Владелец карты пытается выполнить транзакцию, которая для него не разрешена банком-эмитентом, либо внутренняя ошибка эмитента'),
            'ISSUER_LIMIT_FAIL' => $this->module->l('Предпринята попытка, превышающая ограничения банка-эмитента на сумму или количество операций в определенный промежуток времени'),
            'ISSUER_LIMIT_AMOUNT_FAIL' => $this->module->l('Предпринята попытка выполнить транзакцию на сумму, превышающую (дневной) лимит, заданный банком-эмитентом'),
            'ISSUER_LIMIT_COUNT_FAIL' => $this->module->l('Превышен лимит на число транзакций: клиент выполнил максимально разрешенное число транзакций в течение лимитного цикла и пытается провести еще одну'),
            'ISSUER_TIMEOUT' => $this->module->l('Эмитент не ответил в установленное время'),
            'MERCHANT_FORWARD' => $this->module->l('Перенаправление на другой терминал'),
            'MERCHANT_RESTRICTION' => $this->module->l('Запрет МПС или экваера на проведение операции мерчанту'),
            'MPI_CERTIFICATE_ERROR' => $this->module->l('Ошибка сервиса MPI(шлюз)'),
            'MPI_RESPONSE_ERROR' => $this->module->l('Ошибка сервиса MPI(МПС)'),
            'ORDER_NOT_FOUND' => $this->module->l('Не найдена транзакция'),
            'ORDER_TIME_OUT' => $this->module->l('Время платежа (сессии) истекло'),
            'PAYMENT_ENGINE_ERROR' => $this->module->l('Ошибка взаимодействия в ядре процессинга'),
            'PROCESSING_ACCESS_DENIED' => $this->module->l('Доступ к процессингу запрещен'),
            'PROCESSING_ERROR' => $this->module->l('Ошибка функционирования системы, имеющая общий характер. Фиксируется платежной сетью или банком-эмитентом'),
            'PROCESSING_FRAUD_ERROR' => $this->module->l('Процессинг отклонил мошенническую транзакцию'),
            'PROCESSING_TIME_OUT' => $this->module->l('Не получен ответ от процессинга в установленное время'),
            'REFUSAL_BY_GATE' => $this->module->l('Отказ шлюза в выполнении операции'),
            'THREE_DS_ATTEMPTS_FAIL' => $this->module->l('Попытка 3DS авторизации неудачна'),
            'THREE_DS_AUTH_ERROR' => $this->module->l('Ошибка авторизации 3DS'),
            'THREE_DS_ERROR' => $this->module->l('Ошибка оплаты 3DS'),
            'THREE_DS_FAIL' => $this->module->l('Ошибка сервиса 3DS'),
            'THREE_DS_NOT_ATTEMPTED' => $this->module->l('3DS не вводился'),
            'THREE_DS_NOTENROLLED' => $this->module->l('Карта не вовлечена в систему 3DS'),
            'THREE_DS_TIME_OUT' => $this->module->l('Превышено время ожидания 3DS'),
            'THREE_DS_USER_AUTH_FAIL' => $this->module->l('Пользователь ввел неверный код 3DS'),
            'UNKNOWN_STATE' => $this->module->l('Неизвестный статус транзакции'),
            'USER_NOT_FOUND' => $this->module->l('Пользователь не найден'),
            'WRONG_AUTHORIZATION_CODE' => $this->module->l('Неверный код авторизации'),
            'WRONG_CARD_INFO' => $this->module->l('Введены неверные параметры карты'),
            'WRONG_CONFIRM_CODE' => $this->module->l('Недопустимый код подтверждения'),
            'WRONG_CVV' => $this->module->l('Недопустимый CVV'),
            'WRONG_EXPIRE_DATE' => $this->module->l('Неправильная дата окончания срока действия'),
            'WRONG_PAN' => $this->module->l('Неверный номер карты'),
            'WRONG_CARDHOLDER' => $this->module->l('Недопустимое имя держателя карты'),
            'WRONG_PARAMS' => $this->module->l('Неверный набор или формат параметров'),
            'WRONG_PAY_INFO' => $this->module->l('Некорректный параметр PayInfo (неправильно сформирован или нарушена криптограмма)'),
            'WRONG_PHONE' => $this->module->l('Неверный телефон'),
            'WRONG_USER_PARAMS' => $this->module->l('Пользователь с такими параметрами не найден'),
            'OTHER_ERROR' => $this->module->l('Ошибка которая произошла при невозможном стечении обстоятельств')
        );
        /**
         * Create the breadcrumb for your ModuleFrontController.
         */
        $this->context->smarty->assign('path',
            '
			<a href="' . $this->context->link->getPageLink('order', null, null, 'step=3') . '">' . $this->module->l('Payment') . '</a>
			<span class="navigation-pipe">&gt;</span>' . $this->module->l('Error'));

        /**
         * Set error message and description for the template.
         */

        if (array_key_exists($message, $errcodes))
            $this->errors[] = $errcodes[$message];
        else
            $this->errors[] = $message;
        if ($description)
            $this->errors[] = $description;

        return $this->setTemplate('error.tpl');
    }
}
