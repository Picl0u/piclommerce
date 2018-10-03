<?php

namespace Piclou\Piclommerce\Http\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Piclou\Piclommerce\Http\Entities\Order;

class OrderCarrier extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * @var Order
     */
    private $order;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $id;

    /**
     * OrderCarrier constructor.
     * @param Order $order
     * @param string $url
     * @param string $id
     */
    public function __construct(Order $order, string $url, string $id)
    {
        //
        $this->order = $order;
        $this->url = $url;
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $order = $this->order;
        $url = $this->url;
        $id = $this->id;
        $products = $order->OrdersProducts;

        return $this->view('piclommerce::mail.admin.orderCarrier',compact('order', 'url', 'id', 'products'));
    }
}
