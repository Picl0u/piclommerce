<?php

namespace Piclou\Piclommerce\Http\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Piclou\Piclommerce\Http\Entities\Product;

class SendCommentToAdmin extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * @var Product
     */
    private $product;
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $comment;

    public function __construct(Product $product, User $user, string $comment)
    {
        $this->product = $product;
        $this->user = $user;
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__("piclommerce::web.shop_comment_object") . ' '.$this->product->name)
            ->view('piclommerce::mail.comment',[
                'product' => $this->product,
                'user' => $this->user,
                'comment' => $this->comment
            ]);
    }
}
