## PAYOP API WRAPPER

### Include base class
<pre><code>
require_once 'Payop.php';
const PAYOK_PUBLIC = 'public_key';
const PAYOK_SECRET = 'secret_key';
</code></pre>
### Init payment
<pre><code>
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
</code></pre>
### Handle response
<pre><code>
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
</code></pre>