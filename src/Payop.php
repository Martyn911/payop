<?php
namespace martyn911\payop;
/**
 * Payop API Wrapper
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category        Payop
 * @package         martyn911/payop
 * @version         1.0
 * @author          martyn911
 * @license         MIT
 *
 */
class Payop
{
    const STATUS_WAIT = 'wait';

    const STATUS_SUCCESS = 'success';

    const STATUS_ERROR = 'error';

    const LANG_EN = 'en';

    const LANG_RU = 'ru';

    private $publicKey;

    private $secretKey;

    private $apiUrl = 'https://payop.com/api/v1.1/payments/payment';

    private $signatureRequiredParams = ['id', 'amount', 'currency'];

    private $paymentRequiredParams = ['publicKey', 'signature', 'order', 'order.id', 'order.amount', 'order.currency', 'customer', 'customer.email'];

    public function __construct($publicKey, $secretKey)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
    }

    public function createPayment($params = [])
    {
        $params['secretKey'] = $this->secretKey;
        $params['publicKey'] = $this->publicKey;
        $params['signature'] = $this->generateSignature(static::getValue($params, 'order'));
        foreach (array_values($this->paymentRequiredParams) as $key){
            if (!static::getValue($params, $key)) {
                throw new \InvalidArgumentException($key . ' is null');
            };
        }

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($params) );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        $response = json_decode(curl_exec($ch));

        if(!empty($response->errors)){
            throw new \Exception(json_encode($response->errors));
        }

        if(empty($response->data->redirectUrl)){
            throw new \Exception('redirectUrl is null');
        }

        return $response->data->redirectUrl;
    }

    public function generateSignature($order = array())
    {
        if(!empty($order['orderId'])) {
            $order['id'] = $order['orderId'];
        }
        foreach (array_values($this->signatureRequiredParams) as $key){
            if (!static::getValue($order, $key)) {
                throw new \InvalidArgumentException($key . ' is null');
            };
        }
        foreach ($order as $k => $v){
            if($k !== 'status' && !in_array($k, $this->signatureRequiredParams)) unset($order[$k]);
        }
        ksort($order, SORT_STRING);
        $dataSet = array_values($order);
        array_push($dataSet, $this->secretKey);

        return hash('sha256', \implode(':', $dataSet));
    }

    public function paymentHandler()
    {
        $response = json_decode(file_get_contents('php://input'));
        if(empty($response) || $response->signature !== $this->generateSignature((array) $response)){
            throw new \Exception('Response empty or wrong signature');
        }
        
        return $response;
    }

    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }
}