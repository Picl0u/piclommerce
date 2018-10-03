<?php

namespace Piclou\Piclommerce\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Piclou\Piclommerce\Http\Entities\Order;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Piclou\Piclommerce\Http\Entities\OrderReturn;
use Piclou\Piclommerce\Http\Mail\ReturnOrder;
use Ramsey\Uuid\Uuid;

class OrderController extends Controller
{
    protected $viewPath = 'piclommerce::orders.';

    public function index()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('id','DESC')->get();

        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.user_my_account') => route('user.account'),
            __('piclommerce::web.user_my_orders') => route('user.addresses'),
        ];
        SEOMeta::setCanonical(route('user.infos'));
        SEOMeta::setTitle(__('piclommerce::web.user_my_orders') . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__('piclommerce::web.user_my_orders') . " - " . setting("generals.seoDescription"));

        return view($this->viewPath . 'index', compact('orders', 'arianne'));

    }

    public function invoice(string $uuid)
    {
        $order = Order::select('reference')->where('uuid', $uuid)
            ->where('user_id', Auth::user()->id)
            ->firstorFail();

        $name = config('piclommerce.invoicePath') . "/" .
            config('piclommerce.invoiceName') . "-" .
            $order->reference . '.pdf';
        if(Storage::exists($name)) {
            return Storage::download($name);
        }

        session()->flash('error', __('piclommerce::web.order_invoice_not_found'));
        return redirect()->back();
    }

    public function show(string $uuid)
    {
        $order = Order::where('uuid', $uuid)
            ->where('user_id', Auth::user()->id)
            ->firstorFail();

        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.user_my_account') => route('user.account'),
            __('piclommerce::web.user_my_orders') => route('user.addresses'),
            __('piclommerce::web.order')." : ".$order->reference => route('order.show',['uuid' => $order->uuid]),
        ];

        SEOMeta::setCanonical(route('user.infos'));
        SEOMeta::setTitle(__('piclommerce::web.order')." : ".$order->reference . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__('piclommerce::web.order')." : ".$order->reference . " - " . setting("generals.seoDescription"));

        return view($this->viewPath . "show", compact('order','arianne'));
    }

    public function returnProducts(OrderReturnRequest $request ,string $uuid)
    {
        $order = Order::where('uuid', $uuid)
            ->where('user_id', Auth::user()->id)
            ->firstorFail();

        $insertReturn = [
            'uuid' => Uuid::uuid4()->toString(),
            'order_id' => $order->id,
            'user_id' => Auth::user()->id,
            'message' => $request->message
        ];

        Mail::to($order->user_email)
            ->send(new ReturnOrder($order, $request->message));

        Mail::to(setting('generals.orderEmail'))
            ->send(new ReturnOrder($order, $request->message));

        if(isset($request->product) && !empty($request->product)) {
            foreach($request->product as $product) {
                $insertReturn['orders_product_id'] = $product;
                OrderReturn::create($insertReturn);
            }
        }else{
            OrderReturn::create($insertReturn);
        }

        session()->flash('success','Merci, votre retour a bien été envoyé.');
        return redirect()->route('order.show',['uuid' => $order->uuid]);

    }
}