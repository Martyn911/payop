<?php
/**
 * Created by PhpStorm.
 * Date: 2/18/19
 * Time: 5:50 PM
 */

require_once 'Payop.php';

const PAYOK_PUBLIC = 'public_key';
const PAYOK_SECRET = 'secret_key';

// Order params
$params['order']['id'] = 1;
$params['order']['amount'] = '2.0000';
$params['order']['currency'] = 'UAH';
$params['order']['description'] = 'test payment';
$params['customer']['email'] = 'test@mail.ua';
$params['resultUrl'] = 'https://site.ua/success.html';
$params['failUrl'] = 'https://site.ua/error.html';

$payop = new Payop(PAYOK_PUBLIC, PAYOK_SECRET);

header("Location: " . $payop->createPayment($params));