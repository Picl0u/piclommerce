<?php

namespace Piclou\Piclommerce\Http\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \Piclou\Piclommerce\Http\Entities\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReturnOrder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Order
     */
    private $order;
    /**
     * @var string
     */
    private $message;

    /**
     * ReturnOrder constructor.
     * @param Order $order
     * @param string $message
     */
    public function __construct(Order $order, string $message )
    {
        $this->order = $order;
        $this->message = $message;
    }


    /**
     * @return $this
     */
    public function build()
    {
        $order = $this->order;
        $products = $order->OrdersProducts;
        $messageReturn =  $this->message;
        return $this->from(setting('generals.email'), setting('generals.websiteName'))
            ->subject('Retour de produit')
            ->view(
                'piclommerce::mail.orderReturn',
                compact('order','products', 'messageReturn')
            );
    }
}
