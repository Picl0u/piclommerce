<?php
namespace Piclou\Piclommerce\Http\Payments;
use Illuminate\Http\Request;
use Netshell\Paypal\Facades\Paypal;

class PaypalPayment implements PaymentInterface
{
    private $apiContext;

    public function process(float $totalOrder)
    {
        $this->apiContext();

        $payer = Paypal::Payer();
        $payer->setPaymentMethod('paypal');

        $amount = PayPal:: Amount();
        $amount->setCurrency('EUR');
        $amount->setTotal($totalOrder); // This is the simple way,

        $transaction = PayPal::Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription(setting('generals.websiteName'));

        $redirectUrls = PayPal:: RedirectUrls();
        $redirectUrls->setReturnUrl(
            action('\App\Http\Controllers\Piclommerce\ShoppingCartController@orderReturn')
        );
        $redirectUrls->setCancelUrl(
            action('\App\Http\Controllers\Piclommerce\ShoppingCartController@orderCancel')
        );

        $payment = PayPal::Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));
        $response = $payment->create($this->apiContext);
        $redirectUrl = $response->links[1]->href;
        $token = $response->id;

        return [
            'redirect' => $redirectUrl,
            'token' => $token
        ];
    }

    public function auto(Request $request)
    {
        $this->apiContext();

        $id = $request->get('paymentId');
        $token = $request->get('token');
        $payer_id = $request->get('PayerID');
        $payment = PayPal::getById($id, $this->apiContext);

        $paymentExecution = PayPal::PaymentExecution();

        $paymentExecution->setPayerId($payer_id);
        $executePayment = $payment->execute($paymentExecution, $this->apiContext);

        return[
            'id' => $id,
            'token' => $token,
            'payment' => $executePayment
        ];
    }

    public function accept()
    {

    }

    public function refuse()
    {

    }

    private function apiContext()
    {
        $this->apiContext = Paypal::ApiContext(
            config('piclommerce.paypal.client_id'),
            config('piclommerce.paypal.secret')
        );
        if(config('piclommerce.paypal.sandbox')) {

            $this->apiContext->setConfig(array(
                'mode' => 'sandbox',
                'service.EndPoint' => 'https://api.sandbox.paypal.com',
                'http.ConnectionTimeOut' => 30,
                'log.LogEnabled' => true,
                'log.FileName' => storage_path('logs/paypal.log'),
                'log.LogLevel' => 'FINE'
            ));
        } else {
            $this->apiContext->setConfig(array(
                'mode' => 'live',
                'service.EndPoint' => 'https://api.paypal.com',
                'http.ConnectionTimeOut' => 30,
                'log.LogEnabled' => true,
                'log.FileName' => storage_path('logs/paypal.log'),
                'log.LogLevel' => 'FINE'
            ));
        }

        return $this;
    }

}
