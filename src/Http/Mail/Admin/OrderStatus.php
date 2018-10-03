<?php

namespace Piclou\Piclommerce\Http\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Piclou\Piclommerce\Http\Entities\Order;
use Piclou\Piclommerce\Http\Entities\Status;

class OrderStatus extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * @var Order
     */
    private $order;
    /**
     * @var Status
     */
    private $status;

    /**
     * OrderStatus constructor.
     * @param Order $order
     * @param Status $status
     */
    public function __construct(Order $order, Status $status)
    {
        //
        $this->order = $order;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $order = $this->order;
        $status = $this->status;
        $products = $order->OrdersProducts;

        return $this->view('piclommerce::mail.admin.orderStatus',compact('order', 'status', 'products'));
    }
}