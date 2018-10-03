<?php

namespace Piclou\Piclommerce\Http\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductQuantityAlert extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * @var array
     */
    private $products;

    /**
     * ProductQuantityAlert constructor.
     * @param array $products
     */
    public function __construct(array $products)
    {
        //
        $this->products = $products;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $products = $this->products;
        return $this->subject('Alerte quantité produit')
            ->markdown('piclommerce::mail.alertProductHtml',compact('products'));
    }
}