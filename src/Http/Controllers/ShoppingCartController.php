<?php
namespace App\Http\Controllers\Piclommerce;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Piclou\Piclommerce\Helpers\Invoice;
use Piclou\Piclommerce\Http\Entities\Carriers;
use Piclou\Piclommerce\Http\Entities\CarriersPrices;
use Piclou\Piclommerce\Http\Entities\Content;
use Piclou\Piclommerce\Http\Entities\Countries;
use Piclou\Piclommerce\Http\Entities\Coupon;
use Piclou\Piclommerce\Http\Entities\CouponProduct;
use Piclou\Piclommerce\Http\Entities\CouponUser;
use Piclou\Piclommerce\Http\Entities\Newsletters;
use Piclou\Piclommerce\Http\Entities\Order;
use Piclou\Piclommerce\Http\Entities\OrdersProducts;
use Piclou\Piclommerce\Http\Entities\OrdersStatus;
use Piclou\Piclommerce\Http\Entities\Product;
use Piclou\Piclommerce\Http\Entities\ProductsAttribute;
use Piclou\Piclommerce\Http\Entities\Shoppingcart;
use Piclou\Piclommerce\Http\Entities\Status;
use Piclou\Piclommerce\Http\Entities\User;
use Piclou\Piclommerce\Http\Entities\UsersAdresses;
use Piclou\Piclommerce\Http\Mail\OrderCreated;
use Piclou\Piclommerce\Http\Mail\ProductQuantityAlert;
use Piclou\Piclommerce\Http\Payments\PaypalPayment;
use Piclou\Piclommerce\Http\Requests\ExpressUsers;
use Piclou\Piclommerce\Http\Requests\SelectAddresses;
use Piclou\Piclommerce\Http\Requests\SelectCarrier;
use Piclou\Piclommerce\Http\Requests\UsersAddresses as RequestAddresses;
use Ramsey\Uuid\Uuid;
use \Session;
use \Mail;
use Artesaos\SEOTools\Facades\SEOMeta;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;

class ShoppingCartController extends Controller
{
    protected $viewPath = "piclommerce::cart.";

    /**
     * Add product to cart
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProduct(Request $request)
    {

        $product = Product::where('uuid', $request->product_id)->first();
        $ref = $product->reference;
        if (empty($product)) {
            return response(__("piclommerce::web.cart_product_not_found"), 404)
                ->header('Content-Type', 'text/plain');
        }

        $check_attribute = false;
        $attribute_exist = null;
        if(isset($request->declinaisons) && !empty($request->declinaisons)) {
            $declinaisons = $request->declinaisons;
            $attributes = ProductsAttribute::where('product_id', $product->id)->get()->toArray();
            $attributes_decode = [];
            foreach($attributes as $key => $attr) {
                $attributes_decode[$key] = json_decode($attr['declinaisons'] ?? '' ?: '{}', true) ?: [];
            }
            $number_field = count($declinaisons);
            foreach($attributes_decode as $k => $v) {
                $result = array_intersect($declinaisons, $v);
                if(!empty($result) && count($result) == $number_field) {
                    $attribute_exist = $k;
                    $check_attribute = true;
                    break;
                }
            }

            if(!($check_attribute)) {
                return response(__("piclommerce::web.cart_product_not_found"), 404)
                    ->header('Content-Type', 'text/plain');
            }
            if(!empty($attributes[$attribute_exist]['reference']) && !is_null($attributes[$attribute_exist]['reference'])) {
                $product->reference = $attributes[$attribute_exist]['reference'];
            }
            $product->stock_available = $attributes[$attribute_exist]['stock_brut'];

        }
        if ($product->stock_available < $request->quantity) {
            return response(__("piclommerce::web.cart_product_no_stock"), 404)
                ->header('Content-Type', 'text/plain');
        }

        $vat = $product->Vat;
        $percent = 1+($vat->percent/100);

        $price = $product->price_ttc;
        if(is_null($product->reduce_date_begin) || $product->reduce_date_begin == '0000-00-00 00:00:00') {

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
        } else {
            if($product->reduce_date_begin <= date('Y-m-d H:i:s') && $product->reduce_date_end > date('Y-m-d H:i:s')) {
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
            }
        }
        $addCart = [
            'id' => $product->reference,
            'name' => $product->name,
            'qty' => $request->quantity,
            'price' => $price/$percent,
            'options' => [
                'image' => ($product->getMedias('image'))?$product->getMedias('image')['target_path']:null,
                'id' => $product->id,
                'price' => $price/$percent,
                'declinaison' => '',
                'price_impact' => null,
                'ref' => $ref,
            ]
        ];

        if($check_attribute) {
            $attribute = ProductsAttribute::where('id', $attributes[$attribute_exist]['id'])->first();
            $declinaisons = $attribute->getValues('declinaisons');
            $option = '';
            foreach($declinaisons as $key => $value) {
                $option .= $key . ' : '.$value . ' - ';
            }
            $option = substr($option,0 ,-3);
            $addCart['options']['declinaison'] = $option;
            if($attribute->price_impact) {
                $addCart['price'] =  $addCart['price'] + $attribute->price_impact;
                $addCart['options']['price'] = $addCart['options']['price'] + $attribute->price_impact;
                $addCart['options']['price_impact'] = $attribute->price_impact;
            }
        }

        if(auth()->check()) {
            if(auth()->user()->role == 'user') {
                Cart::instance('shopping')->restore(Auth::user()->uuid);
            }
        }

        $cartitem = Cart::instance('shopping')->add($addCart);
        $cartitem->associate('Piclou\Piclommerce\Http\Entities\Product');

        // Update Stock
        if(!empty(setting('orders.stockBooked'))) {
            Product::where('id', $product->id)->update([
                'stock_booked' => $product->stock_booked + $request->quantity,
                'stock_available' => $product->stock_brut - ($product->stock_booked + $request->quantity)
            ]);
        }

        $total = Cart::instance('shopping')->total(2,".","");
        $shippingPrice = $this->selectCarrier($total);
        $total = $shippingPrice + $total;

        $addCart['price'] = priceFormat($price);
        $cart = [
            'count' => Cart::instance('shopping')->count(),
            'countCart' => "Il y'a " . Cart::instance('shopping')->count() . " articles dans votre panier",
            'total' => "<strong>".__("piclommerce::web.cart_total")." : </strong>" . priceFormat($total),
            'shipping' => "<strong>".__("piclommerce::web.cart_transport_price")." : </strong>" . priceFormat($shippingPrice),
            'vat' => "<strong>".__("piclommerce::web.cart_vat")." : </strong>" . Cart::instance('shopping')->tax() . " &euro;",
            'subtotal' => "<strong>".__("piclommerce::web.cart_transport_sub_total")." :</strong>" . Cart::instance('shopping')->subtotal() . "&euro",
            'product' => $addCart
        ];

        if(auth()->check()) {
            if(auth()->user()->role == 'user') {
                Cart::instance('shopping')->store(Auth::user()->uuid);
            }
        }

        return response()->json($cart);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function productAttributes(Request $request){
        $product = Product::where('uuid', $request->product_id)->first();

        if (empty($product)) {
            return response(__("piclommerce::web.cart_product_not_found"), 404)
                ->header('Content-Type', 'text/plain');
        }
        if(isset($request->declinaisons) && !empty($request->declinaisons)) {
            $declinaisons = $request->declinaisons;
            $attributes = ProductsAttribute::where('product_id', $product->id)->get()->toArray();
            $attributes_decode = [];
            foreach($attributes as $key => $attr) {
                $attributes_decode[$key] = json_decode($attr['declinaisons'] ?? '' ?: '{}', true) ?: [];
            }
            $check_attribute = false;
            $attribute_exist = null;
            $number_field = count($declinaisons);
            foreach($attributes_decode as $k => $v) {
                $result = array_intersect($declinaisons, $v);
                if(!empty($result) && count($result) == $number_field) {
                    $attribute_exist = $k;
                    $check_attribute = true;
                    break;
                }
            }
            if($check_attribute) {
                if($attributes[$attribute_exist]['price_impact'] && !empty($attributes[$attribute_exist]['price_impact'])) {
                    $product->price_ttc += $attributes[$attribute_exist]['price_impact'];
                }
                $prices = "";
                if(!empty($product->reduce_price) || !empty($product->reduce_percent)) {
                    if(is_null($product->reduce_date_begin) || $product->reduce_date_begin == '0000-00-00 00:00:00') {
                        $prices .= '<span>'.priceFormat($product->price_ttc).'</span>';
                        if(!empty($product->reduce_price)) {
                            $prices .= priceFormat($product->price_ttc - $product->reduce_price);
                        }
                        if(!empty($product->reduce_percent)) {
                            $prices .= priceFormat(
                                $product->price_ttc -
                                ($product->price_ttc * (($product->reduce_percent / 100)))
                            );
                        }
                    } elseif($product->reduce_date_begin <= date('Y-m-d H:i:s') && $product->reduce_date_end > date('Y-m-d H:i:s')) {
                        $prices .= '<span>'.priceFormat($product->price_ttc).'</span>';
                        if(!empty($product->reduce_price)){
                            $prices .= priceFormat($product->price_ttc - $product->reduce_price);
                        }
                        if(!empty($product->reduce_percent)) {
                            $prices .=priceFormat(
                                $product->price_ttc -
                                ($product->price_ttc * (($product->reduce_percent/100)))
                            );
                        }
                    } else {
                        $prices .= priceFormat($product->price_ttc);
                    }
                } else {
                    $prices .= priceFormat($product->price_ttc);
                }
                $attributes[$attribute_exist]['prices'] = $prices;

                return response()->json($attributes[$attribute_exist]);
            }
        }

        return null;

    }

    /**
     * Show cart
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        //Cart::instance('shopping')->destroy();
        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.order_cart') => Route('cart.show')
        ];
        $coupon = [];
        $total = Cart::instance('shopping')->total(2,".","");
        if (session()->get('coupons') ){
            $coupon = $this->checkCoupon(session()->get('coupons')['coupon_id'], $total);
            if(!empty($coupon)){
                $total = $coupon['reduce'];
            }
        }

        SEOMeta::setTitle(__("piclommerce::web.order_cart") . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__("piclommerce::web.order_cart") . " - " . setting("generals.seoDescription"));

        $shippingPrice = $this->selectCarrier($total);
        $total = $shippingPrice + $total;
        return view($this->viewPath.'show', compact('arianne','shippingPrice','total','coupon'));
    }

    /**
     * Edit product
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProduct(Request $request)
    {
        $lineCart = Cart::instance('shopping')->get($request->product_id);
        if(!empty($lineCart)) {
            $product = Product::where('reference', $lineCart->id)->First();
            if(empty($product)) {
                $declinaison = ProductsAttribute::where('reference', $lineCart->id)->First();
                $product = $declinaison->Product;
                if(empty($declinaison)) {
                    return response(__("piclommerce::web.cart_product_not_found"), 404)
                        ->header('Content-Type', 'text/plain');
                }

                $product->stock_available = $declinaison->stock_brut;
            }

            if($product->stock_available >= $request->quantity ) {
                $lastQuantity = $lineCart->qty;

                if(auth()->check()) {
                    if(auth()->user()->role == 'user') {
                        Cart::instance('shopping')->restore(Auth::user()->uuid);
                    }
                }

                Cart::instance('shopping')->update($request->product_id, $request->quantity);

                if(auth()->check()) {
                    if(auth()->user()->role == 'user') {
                        Cart::instance('shopping')->store(Auth::user()->uuid);
                    }
                }

                if(!empty(setting('orders.stockBooked'))) {

                    $updatedQuantity = $request->quantity - $lastQuantity;

                    Product::where('id', $product->id)->update([
                        'stock_booked' => $product->stock_booked + $updatedQuantity,
                        'stock_available' => $product->stock_brut - ($product->stock_booked + $updatedQuantity)
                    ]);
                }
            } else {
                return response(__("piclommerce::web.cart_product_no_stock"), 404)
                    ->header('Content-Type', 'text/plain');
            }
        } else {
            return response(__("piclommerce::web.cart_product_not_found"), 404)
                ->header('Content-Type', 'text/plain');
        }

        $total = Cart::instance('shopping')->total(2,".","");
        $shippingPrice = $this->selectCarrier($total);
        $coupon = [];
        if (session()->get('coupons') ){
            $coupon = $this->checkCoupon(session()->get('coupons')['coupon_id'], $total);
            if(!empty($coupon)){
                $total = $coupon['reduce'];
            }
        }
        $total = $shippingPrice + $total;
        return view("piclommerce::components.cart", compact('total','shippingPrice','coupon'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function coupon(Request $request)
    {
        $code = $request->coupon;

        if(auth()->check()) {
            if(auth()->user()->role == 'user') {
                Cart::instance('shopping')->restore(Auth::user()->uuid);
            }
        }
        $total = Cart::instance('shopping')->total(2,".","");
        $coupon = Coupon::where('coupon',$code)
            ->where(function($query) {
                $query->where('begin', null)->orWhere('begin', '<', now());
            })
            ->where(function($query){
                $query->where('end', null)->orWhere('end', '>=', now());
            })->where(function($query) use ($total){
                $query->where('amount_min', null)->orWhere('amount_min', 0)->orWhere('amount_min', '<', $total);
            })->first();

        $messages = [
            "success" => __('piclommerce::web.cart_coupon_success'),
            "noCoupon" => __('piclommerce::web.cart_coupon_noCoupon'),
            "noAccess" => __('piclommerce::web.cart_coupon_noAccess'),
            "noConnect" => __('piclommerce::web.cart_coupon_noConnect'),
            "noProduct" => __('piclommerce::web.cart_coupon_noProduct'),
        ];
        $redirect = redirect()->route('cart.show');

        if(!empty($coupon)){

            if(!empty($coupon->percent)) {
                $totalReduc =  $total - ($total * (($coupon->percent/100)));
            } else {
                $totalReduc = $total - $coupon->price;
            }

            /* Utilisateur */
            $couponUser = CouponUser::where('coupon_id', $coupon->id)->first();
            if(!empty($couponUser)){
                if(Auth::check()) {
                    $checkUser = CouponUser::where('coupon_id', $coupon->id)
                        ->where('user_id', Auth::user()->id)
                        ->first();
                    if(!empty($checkUser)) {

                    } else {
                        session()->flash('error',$messages['noAccess']);
                        return $redirect;
                    }
                }else{
                    session()->flash('error',$messages['noConnect']);
                    return $redirect;
                }
            }
            /* Produits */
            $couponProduct = CouponProduct::where('coupon_id', $coupon->id)->first();
            if(!empty($couponProduct)) {
                $cart = Cart::instance('shopping')->content();
                $checkProduct = CouponProduct::where('coupon_id', $coupon->id)
                    ->where(function($query) use($cart) {
                        foreach($cart as $row) {
                            $query->orWhere('product_id', $row->options->id);
                        }
                    })->get();

                if(!empty(count($checkProduct))) {
                    foreach($checkProduct as $product){
                        foreach ($cart as $row) {
                            if($row->options->id == $product->product_id){
                                if(!empty($coupon->percent)) {
                                    $reduceProduct =  $row->options->price - ($row->options->price * (($coupon->percent/100)));
                                } else {
                                    $reduceProduct = $row->options->price - $coupon->price;
                                }
                                Cart::instance('shopping')->update($row->rowId, ['price-reduce' => $reduceProduct]);

                                $totalReduc = $total - (($row->options->price - $reduceProduct)*$row->qty);
                                $total = $totalReduc;
                            }
                        }
                    }
                }else{
                    session()->flash('error',$messages['noProduct']);
                    return $redirect;
                }
            }

        }else{
            session()->flash('error',$messages['noCoupon']);
            return $redirect;
        }
        session([
            'coupons' => [
                'coupon_id'  => $coupon->id,
                'reduce'     => $totalReduc,
                'totalCart'  => Cart::instance('shopping')->total(2,".",""),
                'reduceDiff' => Cart::instance('shopping')->total(2,".","") - $totalReduc
            ]
        ]);
        if(auth()->check()) {
            if(auth()->user()->role == 'user') {
                Cart::instance('shopping')->store(Auth::user()->uuid);
                Shoppingcart::where('identifier',auth()->user()->uuid)->update([
                    'coupon_id' => $coupon->id
                ]);
            }
        }
        session()->flash('success', $messages['success']);
        return $redirect;
    }

    /** Delete the coupon in session
     * @return \Illuminate\Http\RedirectResponse
     */
    public function couponCancel()
    {
        session()->forget('coupons');
        return redirect()->route('cart.show');
    }

    /**
     * Affiche la connexion / inscription de l'utilisateur
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function orderUser()
    {
        if(auth()->user()){
            if(auth()->user()->role == 'user') {
                session(['custommers' => auth()->user()]);
                return redirect()->route('cart.user.address');
            }
        }
        if(session()->get('custommers')) {
            return redirect()->route('cart.user.address');
        }

        SEOMeta::setTitle(__("piclommerce::web.user_personal_informations") . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__("piclommerce::web.user_personal_informations") . " - " . setting("generals.seoDescription"));

        return view($this->viewPath . 'user');
    }

    /**
     * Create account or not
     * @param ExpressUsers $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function orderUserExpress(ExpressUsers $request)
    {

        $newsletter = 0;
        if ($request->express_newsletter == "on") {
            $newsletter = 1;
        }
        if(!empty($request->express_password) && !is_null($request->express_password)){
            $user = User::create([
                'gender' => $request->express_gender,
                'firstname' => $request->express_firstname,
                'lastname' => $request->express_lastname,
                'username' => str_slug($request->express_firstname . "-" . $request->express_lastname),
                'email' => $request->express_email,
                'password' => bcrypt($request->express_password),
                'newsletter' => $newsletter
            ]);
            Auth::login($user);
        } else {
            $user = new User();
            $user->gender = $request->express_gender;
            $user->firstname = $request->express_firstname;
            $user->lastname = $request->express_lastname;
            $user->username = str_slug( $request->express_firstname . "-" . $request->express_lastname);
            $user->email = $request->express_email;
            $user->newsletter = $newsletter;
            $user->role = 'guest';
        }

        /* Inscription newsletter */
        if(!empty($newsletter)){
            $testNewsletter = Newsletters::where('email', $request->express_email)->first();
            if(empty($testNewsletter)) {
                Newsletters::create([
                    'active' => 1,
                    'email' => $request->express_email,
                    'firstname' => $request->express_firstname,
                    'lastname' => $request->express_lastname,
                ]);
            }
        };
        session(['custommers' => $user]);

        return redirect()->route('cart.user.address');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orderAddresses()
    {
        $user = session()->get('custommers');
        //session()->forget('custommersAddresses');
        $addressList = $this->adressesUser($user);
        $countries = Countries::select('id','name')
            ->where('activated', 1)
            ->orderBy('name','asc')
            ->get();

        SEOMeta::setTitle(__("piclommerce::web.cart_address") . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__("piclommerce::web.cart_address") . " - " . setting("generals.seoDescription"));

        return view($this->viewPath . "addresses", compact('addressList','user', 'countries'));
    }

    public function orderAddressStore(RequestAddresses $request)
    {
        $user = session()->get('custommers');
        $billing = 0;
        if ($request->billing == "on") {
            $billing = 1;
        }

        $insertAddress = [
            'uuid' => Uuid::uuid4()->toString(),
            'delivery' => 1,
            'billing' => $billing,
            'gender' => $request->gender,
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'address' => $request->address,
            'additional_address' => $request->additional_address,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'phone' => $request->phone
        ];

        if (!empty($user->id) && !is_null($user->id)) {
            $insertAddress['user_id'] = $user->id;
            $address = UsersAdresses::create($insertAddress);
            $insertAddress['id'] = $address->id;
        }
        $custommerAddresses = [];
        $addressList = session()->get('custommersAddresses');
        if (!empty($addressList)) {
            foreach ($addressList as $address) {
                $custommerAddresses[] = $address;
            }
        }
        $custommerAddresses[] = $insertAddress;
        session(['custommersAddresses' => $custommerAddresses]);

        return redirect()->route('cart.user.address');

    }

    /**
     * @param SelectAddresses $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function orderAddressSelect(SelectAddresses $request)
    {
        $user = session()->get('custommers');

        $addressList = $this->adressesUser($user);
        $deliveryAddress = null;
        $billingAddress = null;
        foreach($addressList as $address) {
            if($address['uuid'] == $request->delivery_address) {
                $deliveryAddress = $address;
            }
            if($address['uuid'] == $request->billing_address) {
                $billingAddress = $address;
            }
        }
        session(['cartAddresses' => [
            'delivery' => $deliveryAddress,
            'billing'  => $billingAddress
        ]]);

        return redirect()->route('cart.user.shipping');
    }

    /**
     * Affiche la liste des transporteurs en fonction du pays de livraison
     * Si aucun transporteur, affiche le transporteur par défaut
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function orderShipping()
    {
        $address = session()->get('cartAddresses');
        if(empty($address) || empty($address['delivery']) || empty($address['billing'])) {
            session()->flash('error', __("piclommerce:web.cart_address_select"));
            return redirect()->route('cart.user.address');
        }

        $total = Cart::instance('shopping')->total(2,".","");

        $carriers = CarriersPrices::where("price_min" , "<", $total)
            ->where(function($query) use ($total) {
                $query->where('price_max', '>', $total)->orwhere('price_max', 0);
            })
            ->where('country_id', $address['delivery']['country_id'])
            ->orderBy('price',"ASC")
            ->get();

        if(empty($carriers)) {
            $carriers = CarriersPrices::where("price_min" , "<", $total)
                ->where('price_max',0)
                ->where('country_id', $address['delivery']['country_id'])
                ->orderBy('price',"ASC")
                ->get();
        }
        if(empty($carriers)) {
            $carrier = Carriers::where('default',1)->first();
        }

        return view(
            $this->viewPath . "shipping",
            compact('total','address', 'carriers', 'carrier')
        );
    }

    /**
     * Enregistre le transporteur dans la session
     * @param SelectCarrier $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function orderShippingStore(SelectCarrier $request)
    {

        $address = session()->get('cartAddresses');
        if(empty($address) || empty($address['delivery']) || empty($address['billing'])) {
            session()->flash('error', __("piclommerce:web.cart_address_select"));
            return redirect()->route('cart.user.address');
        }

        $carrier = Carriers::where('id',$request->carrier_id)->first();
        if(is_null($carrier)) {
            session()->flash('error', __("piclommerce:web.cart_carrier_select"));
            return redirect()->route('cart.user.shipping');
        }
        $total = Cart::instance('shopping')->total(2,".","");
        $carrierPrice = null;
        foreach ($carrier->CarriersPrices as $price) {
            if(
                $total > $price->price_min &&
                ($total <= $price->price_max || empty($price->price_max))
                && $address['delivery']['country_id'] == $price['country_id']
            ){
                $carrierPrice = $price->price;
                break;
            }
        }
        if(is_null($carrierPrice)){
            if(!empty($carrier->default)){
                $carrierPrice = $carrier->default_price;
            }
        }

        if(is_null($carrierPrice)){
            session()->flash('error',__("piclommerce:web.cart_carrier_select_error"));
            return redirect()->route('cart.user.shipping');
        }

        if(!empty(setting('orders.freeShippingPrice'))) {
            if($total >= setting('orders.freeShippingPrice')) {
                $carrierPrice = 0;
            }
        }

        session(['cartShipping' => [
            'id' => $carrier->id,
            'price' => $carrierPrice
        ]]);

        return redirect()->route('cart.recap');

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function orderRecap()
    {
        $address = session()->get('cartAddresses');
        if(empty($address) || empty($address['delivery']) || empty($address['billing'])) {
            session()->flash('error',__("piclommerce:web.cart_address_select"));
            return redirect()->route('cart.user.address');
        }
        $shipping = session()->get('cartShipping');
        if(empty($shipping) || empty($shipping['id'])) {
            session()->flash('error',__("piclommerce:web.cart_varrier_select"));
            return redirect()->route('cart.user.shipping');
        }
        Cart::instance('shopping')->restore(Auth::user()->uuid);
        $coupon = [];
        $total = Cart::instance('shopping')->total(2,".","");
        if (session()->get('coupons') ){
            $coupon = $this->checkCoupon(session()->get('coupons')['coupon_id'], $total);
            if(!empty($coupon)){
                $total = $coupon['reduce'];
            }
        }
        $cgv = "";
        if(!empty(setting('orders.cgvId'))){
            $cgv = Content::where('id', setting('orders.cgvId'))->first();
        }

        $country = Countries::select('name')->where('id',$address['delivery']['country_id'])->first();
        $address['delivery']['country_name'] = $country->name;
        $country = Countries::select('name')->where('id',$address['billing']['country_id'])->first();
        $address['billing']['country_name'] = $country->name;

        session(['cartAddresses' => $address]);

        $user = session('custommers');
        $address = session()->get('cartAddresses');
        $shipping = session()->get('cartShipping');

        $coupon = [];
        $total = Cart::instance('shopping')->total(2,".","");
        if (session()->get('coupons') ){
            $coupon = $this->checkCoupon(session()->get('coupons')['coupon_id'], $total);
            $total = $coupon['reduce'];
        }
        $transport = Carriers::select('name','delay','url')->where('id', $shipping['id'])->first();
        $totalOrder = $total + $shipping['price'];
        Cart::instance('shopping')->store($user->uuid);
        $storeCart = [
            'shipping_name' => $transport->name,
            'shipping_delay' => $transport->delay,
            'shipping_url' => $transport->url,
            'shipping_price' => $shipping['price'],

            'user_id' => $user->id,
            'user_firstname' => $user['firstname'],
            'user_lastname' => $user['lastname'],
            'user_email' => $user['email'],

            'delivery_gender' => $address['delivery']['gender'],
            'delivery_firstname' => $address['delivery']['firstname'],
            'delivery_lastname' => $address['delivery']['lastname'],
            'delivery_address' => $address['delivery']['address'],
            'delivery_additional_address' => $address['delivery']['additional_address'],
            'delivery_zip_code' => $address['delivery']['zip_code'],
            'delivery_city' => $address['delivery']['city'],
            'delivery_country_id' => $address['delivery']['country_id'],
            'delivery_country_name' => $address['delivery']['country_name'],
            'delivery_phone' => $address['delivery']['phone'],

            'billing_gender' => $address['billing']['gender'],
            'billing_firstname' => $address['billing']['firstname'],
            'billing_lastname' => $address['billing']['lastname'],
            'billing_address' => $address['billing']['address'],
            'billing_additional_address' => $address['billing']['additional_address'],
            'billing_zip_code' => $address['billing']['zip_code'],
            'billing_city' => $address['billing']['city'],
            'billing_country_id' => $address['billing']['country_id'],
            'billing_country_name' => $address['billing']['country_name'],
            'billing_phone' => $address['billing']['phone'],

            'updated_at' => now(),
            'created_at' => now(),
        ];
        if(!empty($coupon)) {
            $storeCart['coupon_id'] = session()->get('coupons')['coupon_id'];
        }
        Shoppingcart::where('identifier',$user->uuid)->update($storeCart);
        SEOMeta::setTitle(__("piclommerce::web.cart_recap") . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__("piclommerce::web.cart_recap") . " - " . setting("generals.seoDescription"));

        return view(
            $this->viewPath . 'recap',
            compact('address','shipping', 'total', 'coupon', 'cgv')
        );
    }

    public function process()
    {
        // Création du panier en base de données
        $user = session('custommers');
        $address = session()->get('cartAddresses');
        $shipping = session()->get('cartShipping');

        /* Utilisateur */
        $uuid = $user['uuid'];
        if(!isset($user['id'])) {
            $user_id = 0;
        } else {
            $user_id = $user['id'];
        }
        if(auth()->check()) {
            Cart::instance('shopping')->restore(Auth::user()->uuid);
        }
        /* Transporteur */
        $transport = Carriers::select('name','delay','url')->where('id', $shipping['id'])->first();

        $token = Uuid::uuid4()->toString();

        $coupon = [];
        $total = Cart::instance('shopping')->total(2,".","");
        if (session()->get('coupons') ){
            $coupon = $this->checkCoupon(session()->get('coupons')['coupon_id'], $total);
            $total = $coupon['reduce'];
        }
        $totalOrder = $total + $shipping['price'];

        $payment = (new PaypalPayment())->process($totalOrder);
        Cart::instance('shopping')->store($uuid);
        $storeCart = [
            'token' => $payment['token'],
            'shipping_name' => $transport->name,
            'shipping_delay' => $transport->delay,
            'shipping_url' => $transport->url,
            'shipping_price' => $shipping['price'],

            'user_id' => $user_id,
            'user_firstname' => $user['firstname'],
            'user_lastname' => $user['lastname'],
            'user_email' => $user['email'],

            'delivery_gender' => $address['delivery']['gender'],
            'delivery_firstname' => $address['delivery']['firstname'],
            'delivery_lastname' => $address['delivery']['lastname'],
            'delivery_address' => $address['delivery']['address'],
            'delivery_additional_address' => $address['delivery']['additional_address'],
            'delivery_zip_code' => $address['delivery']['zip_code'],
            'delivery_city' => $address['delivery']['city'],
            'delivery_country_id' => $address['delivery']['country_id'],
            'delivery_country_name' => $address['delivery']['country_name'],
            'delivery_phone' => $address['delivery']['phone'],

            'billing_gender' => $address['billing']['gender'],
            'billing_firstname' => $address['billing']['firstname'],
            'billing_lastname' => $address['billing']['lastname'],
            'billing_address' => $address['billing']['address'],
            'billing_additional_address' => $address['billing']['additional_address'],
            'billing_zip_code' => $address['billing']['zip_code'],
            'billing_city' => $address['billing']['city'],
            'billing_country_id' => $address['billing']['country_id'],
            'billing_country_name' => $address['billing']['country_name'],
            'billing_phone' => $address['billing']['phone'],

            'updated_at' => now(),
            'created_at' => now(),
        ];
        if(!empty($coupon)) {
            $storeCart['coupon_id'] = session()->get('coupons')['coupon_id'];
        }
        Shoppingcart::where('identifier',$uuid)->update($storeCart);
        return redirect( $payment['redirect'] );
    }

    public function stripe(Request $request)
    {
        Stripe::setApiKey(setting("stripe.secret"));
        $token = $request->stripeToken;
        $customer = Customer::create(array(
            'email' => $request->stripeEmail,
            'source' => $request->stripeToken
        ));

        $cart = Shoppingcart::where('identifier', Auth::user()->uuid)->first();
        Cart::instance('shopping')->restore(Auth::user()->uuid);
        $nbOrder = (Order::where('created_at', 'like', date('Y-m').'%')->count() + 1);
        $refOrder = config('piclommerce.orderRef') .
            str_pad($nbOrder, config('piclommerce.refCount'), 0, STR_PAD_LEFT);

        $statut = Status::where('order_accept',1)->first();
        $coupon = [];
        $total = Cart::instance('shopping')->total(2,".","");
        if($cart->coupon_id) {
            $coupon = $this->checkCoupon($cart->coupon_id, $total);
            $total = $coupon['reduce'];
        }
        $charge = Charge::create(array(
            'customer' => $customer->id,
            'amount' => ($total + $cart->shipping_price)*100,
            'currency' => 'eur'
        ));

        $insertOrder = [
            'token' => $token,
            'reference' => $refOrder,
            'status_id' => $statut->id,

            'price_ht' => Cart::instance('shopping')->subtotal(2,".",""),
            'vat_price' => Cart::instance('shopping')->tax(2,".",""),
            'vat_percent' => config('cart.tax'),
            'total_quantity' => Cart::instance('shopping')->count(),
            'price_ttc' => $total + $cart->shipping_price,

            'shipping_name' => $cart->shipping_name,
            'shipping_delay' => $cart->shipping_delay,
            'shipping_url' => $cart->shipping_url,
            'shipping_price' => $cart->shipping_price,

            'user_id' => $cart->user_id,
            'user_firstname' => $cart->user_firstname,
            'user_lastname' => $cart->user_lastname,
            'user_email' => $cart->user_email,

            'delivery_gender' => $cart->delivery_gender,
            'delivery_firstname' => $cart->delivery_firstname,
            'delivery_lastname' => $cart->delivery_lastname,
            'delivery_address' => $cart->delivery_address,
            'delivery_additional_address' => $cart->delivery_additional_address,
            'delivery_zip_code' => $cart->delivery_zip_code,
            'delivery_city' => $cart->delivery_city,
            'delivery_country_id' => $cart->delivery_country_id,
            'delivery_country_name' => $cart->delivery_country_name,
            'delivery_phone' => $cart->delivery_phone,

            'billing_gender' => $cart->billing_gender,
            'billing_firstname' => $cart->billing_firstname,
            'billing_lastname' => $cart->billing_lastname,
            'billing_address' => $cart->billing_address,
            'billing_additional_address' => $cart->billing_additional_address,
            'billing_zip_code' => $cart->billing_zip_code,
            'billing_city' => $cart->billing_city,
            'billing_country_id' => $cart->billing_country_id,
            'billing_country_name' => $cart->billing_country_name,
            'billing_phone' => $cart->billing_phone,
        ];
        if(!empty($coupon)) {
            $insertOrder['coupon_id'] = $cart->coupon_id;
            $insertOrder['coupon_price'] = $coupon['reduceDiff'];
            $insertOrder['coupon_name'] = $coupon['name'];
        }
        $order = Order::create($insertOrder);
        OrdersStatus::create([
            'status_id' => $statut->id,
            'order_id' => $order->id
        ]);

        $productsOrder = [];
        $productsUpdate = [];
        $alertProduct = [];
        foreach(Cart::instance('shopping')->content() as $key => $row){
            $product = Product::where('id', $row->options->id)->first();
            $productsOrder[$key] = [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'ref' => $row->id ." / ".$row->options->ref,
                'name' => $product->name,
                'image' => $product->image,
                'quantity' => $row->qty,
                'price_ht' => $row->price,
                'price_ttc' => $row->total
            ];

            // Alerte si stock à zéro ?
            $calculStock = ($product->stock_brut - $row->qty);

            $productsUpdate[$key] = [
                'id' => $product->id,
                'stock_brut' => ($product->stock_brut - $row->qty ),
                'stock_available' => ($product->stock_brut - $row->qty ) - $product->stock_booked
            ];
            if(!empty(setting('orders.stockBooked'))){
                $productsUpdate[$key] = [
                    'stock_booked' => ($product->stock_booked - $row->qty )
                ];
            }
            Product::where('id',$product->id)->update($productsUpdate[$key]);

            if($calculStock <= setting('orders.productQuantityAlert')){
                $alertProduct[$key] = [
                    'id' => $product->id,
                    'ref' => $row->id,
                    'name' => $product->name,
                    'quantity' => ($product->stock_brut - $row->qty ) - $product->stock_booked
                ];
            }

        }
        OrdersProducts::insert($productsOrder);

        /* Génération du PDF */
        $invoice = new Invoice($order);
        $invoiceLink = $invoice->generate();

        Mail::to($order->user_email)
            ->send(new OrderCreated($order, $productsOrder, $invoiceLink));

        Mail::to(setting('generals.orderEmail'))
            ->send(new OrderCreated($order, $productsOrder, $invoiceLink));

        if(!empty($alertProduct)) {
            Mail::to(setting('generals.orderEmail'))
                ->send(new ProductQuantityAlert($alertProduct));
        }

        /* Destruction du panier */
        Cart::instance('shopping')->destroy();

        /* Page paiement accepté */
        $content = Content::select('id','slug')->where("id", setting('orders.acceptId'))->first();

        return redirect()->route('content.index',[
            'slug' => $content->slug,
            'id' => $content->id
        ]);
    }

    public function orderReturn(Request $request)
    {
        $payment = (new PaypalPayment())->auto($request);
        $id = $payment['id'];
        $token = $payment['token'];

        $cart = Shoppingcart::where('token', $id)->first();
        Cart::instance('shopping')->restore($cart->identifier);

        $nbOrder = (Order::where('created_at', 'like', date('Y-m').'%')->count() + 1);
        $refOrder = config('piclommerce.orderRef') .
            str_pad($nbOrder, config('piclommerce.refCount'), 0, STR_PAD_LEFT);

        $statut = Status::where('order_accept',1)->first();

        $coupon = [];
        $total = Cart::instance('shopping')->total(2,".","");
        if(!is_null($cart->coupon_id)) {
            $coupon = $this->checkCoupon($cart->coupon_id, $total);
            $total = $coupon['reduce'];
        }

        $insertOrder = [
            'token' => $token,
            'reference' => $refOrder,
            'status_id' => $statut->id,

            'price_ht' => Cart::instance('shopping')->subtotal(2,".",""),
            'vat_price' => Cart::instance('shopping')->tax(2,".",""),
            'vat_percent' => config('cart.tax'),
            'total_quantity' => Cart::instance('shopping')->count(),
            'price_ttc' => $total + $cart->shipping_price,

            'shipping_name' => $cart->shipping_name,
            'shipping_delay' => $cart->shipping_delay,
            'shipping_url' => $cart->shipping_url,
            'shipping_price' => $cart->shipping_price,

            'user_id' => $cart->user_id,
            'user_firstname' => $cart->user_firstname,
            'user_lastname' => $cart->user_lastname,
            'user_email' => $cart->user_email,

            'delivery_gender' => $cart->delivery_gender,
            'delivery_firstname' => $cart->delivery_firstname,
            'delivery_lastname' => $cart->delivery_lastname,
            'delivery_address' => $cart->delivery_address,
            'delivery_additional_address' => $cart->delivery_additional_address,
            'delivery_zip_code' => $cart->delivery_zip_code,
            'delivery_city' => $cart->delivery_city,
            'delivery_country_id' => $cart->delivery_country_id,
            'delivery_country_name' => $cart->delivery_country_name,
            'delivery_phone' => $cart->delivery_phone,

            'billing_gender' => $cart->billing_gender,
            'billing_firstname' => $cart->billing_firstname,
            'billing_lastname' => $cart->billing_lastname,
            'billing_address' => $cart->billing_address,
            'billing_additional_address' => $cart->billing_additional_address,
            'billing_zip_code' => $cart->billing_zip_code,
            'billing_city' => $cart->billing_city,
            'billing_country_id' => $cart->billing_country_id,
            'billing_country_name' => $cart->billing_country_name,
            'billing_phone' => $cart->billing_phone,
        ];
        if(!empty($coupon)) {
            $insertOrder['coupon_id'] = $cart->coupon_id;
            $insertOrder['coupon_price'] = $coupon['reduceDiff'];
            $insertOrder['coupon_name'] = $coupon['name'];
        }
        $order = Order::create($insertOrder);
        OrdersStatus::create([
            'status_id' => $statut->id,
            'order_id' => $order->id
        ]);

        $productsOrder = [];
        $productsUpdate = [];
        $alertProduct = [];
        foreach(Cart::instance('shopping')->content() as $key => $row){
            $product = Product::where('id', $row->options->id)->first();
            $productsOrder[$key] = [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'ref' => $row->id,
                'name' => $product->name,
                'image' => $product->image,
                'quantity' => $row->qty,
                'price_ht' => $row->price,
                'price_ttc' => $row->total
            ];

            // Alerte si stock à zéro ?
            $calculStock = ($product->stock_brut - $row->qty);

            $productsUpdate[$key] = [
                'id' => $product->id,
                'stock_brut' => ($product->stock_brut - $row->qty ),
                'stock_available' => ($product->stock_brut - $row->qty ) - $product->stock_booked
            ];
            if(!empty(setting('orders.stockBooked'))){
                $productsUpdate[$key] = [
                    'stock_booked' => ($product->stock_booked - $row->qty )
                ];
            }
            Product::where('id',$product->id)->update($productsUpdate[$key]);

            if($calculStock <= setting('orders.productQuantityAlert')){
                $alertProduct[$key] = [
                    'id' => $product->id,
                    'ref' => $row->id,
                    'name' => $product->name,
                    'quantity' => ($product->stock_brut - $row->qty ) - $product->stock_booked
                ];
            }

        }
        OrdersProducts::insert($productsOrder);

        /* Génération du PDF */
        $invoice = new Invoice($order);
        $invoiceLink = $invoice->generate();

        Mail::to($order->user_email)
            ->send(new OrderCreated($order, $productsOrder, $invoiceLink));

        Mail::to(setting('generals.orderEmail'))
            ->send(new OrderCreated($order, $productsOrder, $invoiceLink));

        if(!empty($alertProduct)) {
        Mail::to(setting('generals.orderEmail'))
            ->send(new ProductQuantityAlert($alertProduct));
        }

        /* Destruction du panier */
        Cart::instance('shopping')->destroy();

        /* Page paiement accepté */
        $content = Content::select('id','slug')->where("id", setting('orders.acceptId'))->first();

        return redirect()->route('content.index',[
            'slug' => $content->slug,
            'id' => $content->id
        ]);
    }

    public function orderCancel()
    {
        $content = Content::select('id','slug')->where("id", setting('orders.refuseId'))->first();
        return redirect()->route('content.index',[
            'slug' => $content->slug,
            'id' => $content->id
        ]);
    }

    public function orderAccept()
    {
        Cart::instance('shopping')->destroy();
        $content = Content::select('id','slug')->where("id", setting('orders.acceptId'))->first();

        return redirect()->route('content.index',[
            'slug' => $content->slug,
            'id' => $content->id
        ]);
    }

    /**
     * Check coupon
     * @param int $couponId
     * @param float $total
     * @return array
     */
    public function checkCoupon(int $couponId, float $total) : array
    {
        $couponArray = [];
        $coupon = Coupon::where('id',$couponId)
            ->where(function($query) {
                $query->where('begin', null)->orWhere('begin', '<', now());
            })
            ->where(function($query){
                $query->where('end', null)->orWhere('end', '>=', now());
            })->where(function($query) use ($total){
                $query->where('amount_min', null)->orWhere('amount_min', 0)->orWhere('amount_min', '<', $total);
            })->first();

        $messages = [
            "success" => __('piclommerce::web.cart_coupon_success'),
            "noCoupon" => __('piclommerce::web.cart_coupon_noCoupon'),
            "noAccess" => __('piclommerce::web.cart_coupon_noAccess'),
            "noConnect" => __('piclommerce::web.cart_coupon_noConnect'),
            "noProduct" => __('piclommerce::web.cart_coupon_noProduct'),
        ];

        if(!empty($coupon)) {

            if(!empty($coupon->percent)) {
                $totalReduc =  $total - ($total * (($coupon->percent/100)));
            } else {
                $totalReduc = $total - $coupon->price;
            }

            /* Utilisateur */
            $couponUser = CouponUser::where('coupon_id', $coupon->id)->first();
            if(!empty($couponUser)){
                if(Auth::check()) {
                    $checkUser = CouponUser::where('coupon_id', $coupon->id)
                        ->where('user_id', Auth::user()->id)
                        ->first();
                    if(!empty($checkUser)) {

                    } else {
                        session()->flash('error',$messages['noAccess']);
                        return false;
                    }
                }else{
                    session()->flash('error',$messages['noConnect']);
                    return false;
                }
            }
            /* Produits */
            $couponProduct = CouponProduct::where('coupon_id', $coupon->id)->first();
            if(!empty($couponProduct)) {
                $cart = Cart::instance('shopping')->content();
                $checkProduct = CouponProduct::where('coupon_id', $coupon->id)
                    ->where(function($query) use($cart) {
                        foreach($cart as $row) {
                            $query->orWhere('product_id', $row->options->id);
                        }
                    })->get();

                if(!empty(count($checkProduct))) {
                    foreach($checkProduct as $product){
                        foreach ($cart as $row) {
                            if($row->options->id == $product->product_id){
                                if(!empty($coupon->percent)) {
                                    $reduceProduct =  $row->options->price - ($row->options->price * (($coupon->percent/100)));
                                } else {
                                    $reduceProduct = $row->options->price - $coupon->price;
                                }
                                Cart::instance('shopping')->update($row->rowId, ['price-reduce' => $reduceProduct]);

                                $totalReduc = $total - (($row->options->price - $reduceProduct)*$row->qty);
                                $total = $totalReduc;
                            }
                        }
                    }
                }else{
                    session()->flash('error',$messages['noProduct']);
                    return false;
                }
            }
            $couponArray =  [
                'name' => $coupon->coupon,
                'reduce' => $totalReduc,
                'reduceDiff' => Cart::instance('shopping')->total(2,".","") - $totalReduc
            ];
        }else{
            if(auth()->check()) {
                if(auth()->user()->role == 'user') {
                    Cart::instance('shopping')->restore(Auth::user()->uuid);
                    Cart::instance('shopping')->store(Auth::user()->uuid);
                    Shoppingcart::where('identifier',auth()->user()->uuid)->update([
                        'coupon_id' => null
                    ]);
                }
            }
        }

        return $couponArray;

    }
    /**
     * Get Default price to carriers
     * @param float $total
     * @param null $country
     * @return float
     */
    private function selectCarrier(float $total, $country = null): float
    {

        if(!empty(setting('orders.freeShippingPrice'))) {
            if($total >= setting('orders.freeShippingPrice')) {
                return 0;
            }
        }
        $carrier = CarriersPrices::where("price_min" , "<", $total)->where(function($query) use ($total) {
            $query->where('price_max', '>', $total);
        })->where('country_id', setting('orders.countryId'))
            ->orderBy('price',"ASC")
            ->first();

        if(empty($carrier)) {
            $carrier = CarriersPrices::where("price_min" , "<", $total)
                ->where('price_max',0)
                ->where('country_id', setting('orders.countryId'))
                ->orderBy('price',"ASC")
                ->first();
        }else{
            return $carrier->price;
        }

        if(empty($carrier)) {
            $carrier = Carriers::where('default',1)->first();
            return $carrier->default_price;
        } else{
            return $carrier->price;
        }
    }

    /**
    * @param $user
    * @return array|mixed
    */
    private function adressesUser($user)
    {
        $addressList = [];
        if(!is_null($user->id) && !empty($user->id)){
            $addressList = UsersAdresses::where("user_id", $user->id)->get()->toArray();
        } else {
            $addressList = session()->get('custommersAddresses');
        }
        if (empty($addressList)) {
            $addressList = session()->get('custommersAddresses');
        }
        return $addressList;
    }
}