<?php

namespace App\Http\Controllers\Piclommerce;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gloudemans\Shoppingcart\Facades\Cart;
use Piclou\Piclommerce\Http\Entities\Product;
use Illuminate\Support\Facades\Auth;
use Artesaos\SEOTools\Facades\SEOMeta;

class WhishlistController extends Controller
{
    protected $viewPath = 'piclommerce::whishlist.';

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.user_my_whishlist') => route('whishlist.index'),
        ];

        SEOMeta::setCanonical(route('whishlist.index'));
        SEOMeta::setTitle(__('piclommerce::web.user_my_whishlist') . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__('piclommerce::web.user_my_whishlist') . " - " . setting("generals.seoDescription"));
        return view($this->viewPath . 'index', compact('arianne'));
    }

    public function addProduct(Request $request)
    {
        if(auth()->check()) {
            if(auth()->user()->role == 'user') {

                $product = Product::where('uuid', $request->uuid)->first();
                if (empty($product)) {
                    return response(__("piclommerce::web.cart_product_not_found"), 404)
                        ->header('Content-Type', 'text/plain');
                }

                if ($product->stock_available < $request->quantity) {
                    return response(__("piclommerce::web.cart_product_no_stock"), 404)
                        ->header('Content-Type', 'text/plain');
                }

                $vat = $product->Vat;
                $percent = 1+($vat->percent/100);

                $price = $product->price_ttc;
                if(
                    (!empty($product->reduce_price) && !is_null($product->reduce_price)) ||
                    (!empty($product->reduce_percent) && !is_null($product->reduce_percent))
                ) {
                    if(!empty($product->reduce_price) && !is_null($product->reduce_price)){
                        $price = $product->price_ttc - $product->reduce_price;
                    }else{
                        $price = $product->price_ttc - ($product->price_ttc * (($product->reduce_percent/100)));
                    }
                }
                $addCart = [
                    'id' => $product->reference,
                    'name' => $product->name,
                    'price' => $price/$percent,
                    'qty' => 1,
                    'options' => [
                        'image' => ($product->getMedias('image'))?$product->getMedias('image')['target_path']:null,
                    ]
                ];

                Cart::instance('whishlist')->restore(Auth::user()->id);
                Cart::instance('whishlist')->add($addCart)->associate('Product');
                $cart = [
                    'count' => Cart::instance('whishlist')->count(),
                    'message' => __("piclommerce::web.shop_there_is") . Cart::instance('whishlist')->count() . " " . __("piclommerce::web.shop_whishlist_count"),
                    'product' => $addCart
                ];
                Cart::instance('whishlist')->store(Auth::user()->id);
                return response()->json($cart);
            }
        }

        return response(__("piclommerce::web."), 403)
            ->header('Content-Type', 'text/plain');
    }


    /**
     * @param string $rowId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCart(string $rowId)
    {
        $row = Cart::instance('whishlist')->get($rowId);

        Cart::instance('whishlist')->restore(Auth::user()->id);
        Cart::instance('shopping')->restore(Auth::user()->uuid);

        Cart::instance('whishlist')->remove($rowId);
        $addCart = [
            'id' => $row->id,
            'name' => $row->name,
            'price' => $row->price,
            'qty' => $row->qty,
            'options' => [
                'image' => $row->options->image
            ]
        ];
        Cart::instance('shopping')->add($addCart);

        Cart::instance('whishlist')->store(Auth::user()->id);
        Cart::instance('shopping')->store(Auth::user()->uuid);

        session()->flash('success',__("piclommerce::web.shop_whishlist_to_cart"));
        return redirect()->route('cart.show');
    }
}