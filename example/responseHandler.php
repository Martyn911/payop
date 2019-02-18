<?php
/**
 * Created by PhpStorm.
 * Date: 2/18/19
 * Time: 5:53 PM
 */

require_once 'Payop.php';

const PAYOK_PUBLIC = 'public_key';
const PAYOK_SECRET = 'secret_key';

$payop = new Payop(PAYOK_PUBLIC, PAYOK_SECRET);

$response = $payop->paymentHandler();

if($response->status == Payop::STATUS_WAIT){
    //wait
}

if($response->status == Payop::STATUS_SUCCESS){
    //success
}

if($response->status == Payop::STATUS_ERROR){
    //error
}

print_r($response);