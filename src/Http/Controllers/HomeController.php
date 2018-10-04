<?php
namespace App\Http\Controllers\Piclommerce;

use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEOMeta;
use Piclou\Piclommerce\Http\Entities\Banner;
use Piclou\Piclommerce\Http\Entities\Content;
use Piclou\Piclommerce\Http\Entities\OrdersProducts;
use Piclou\Piclommerce\Http\Entities\Product;
use Piclou\Piclommerce\Http\Entities\ShopCategory;
use Piclou\Piclommerce\Http\Entities\Slider;

class HomeController extends Controller
{
    /**
     * Homepage
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Slider
        $sliders = Slider::where("published",1)->orderBy('order','ASC')->get();
        // Banner
        $banners = Banner::where("published",1)->orderBy('order', 'ASC')->get();
        // Catégories
        $categories = ShopCategory::where('published',1)
            ->where('on_homepage',1)
            ->orderBy('order','ASC')
            ->get();
        // Best Sale
        $bestSale = OrdersProducts::selectRaw('*, sum(product_id) as sum')
            ->groupBy('product_id')
            ->join('products', 'products.id', '=', 'orders_products.product_id')
            ->orderByRaw('SUM(product_id) DESC')
            ->limit(9)
            ->get();
        // Flash Sale
        $flashSales = Product::select(
            'products.id',
            'products.uuid',
            'products.stock_available',
            'products.reduce_date_end',
            'products.reduce_price',
            'products.reduce_percent',
            'products.price_ttc',
            'products.image',
            'products.name',
            'products.slug',
            'products.summary',
            'products.updated_at',
            'shop_categories.name as category_name',
            'shop_categories.slug as category_slug',
            'shop_categories.id as category_id'
        )
        ->where('products.published',1)
        ->where('products.reduce_date_begin', '<=', date('Y-m-d H:i:s'))
        ->where('products.reduce_date_end', '>', date('Y-m-d H:i:s'))
        ->orderBy('products.reduce_date_end','ASC')
        ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
        ->limit(5)
        ->get();

        // Week Selection
        $weekSelections = Product::select(
            'products.id',
            'products.uuid',
            'products.stock_available',
            'products.reduce_price',
            'products.reduce_percent',
            'products.price_ttc',
            'products.image',
            'products.name',
            'products.slug',
            'products.summary',
            'products.updated_at',
            'shop_categories.name as category_name',
            'shop_categories.slug as category_slug',
            'shop_categories.id as category_id'
        )
        ->where('products.published',1)
        ->where('products.week_selection',1)
        ->orderBy('products.'.setting('products.orderField'),setting('products.orderDirection'))
        ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
        ->get();

        // Contents
        $contents = Content::select('id','image','name','slug','summary','description','content_category_id')
            ->where('published', 1)
            ->where('on_homepage', 1)
            ->orderBy('order','ASC')
            ->get();

        SEOMeta::setTitle(setting("generals.seoTitle"));
        SEOMeta::setDescription(setting("generals.seoDescription"));

        return view("piclommerce::homepage.index",compact(
            "sliders",
            "banners",
            "categories",
            "bestSale",
            "flashSales",
            "weekSelections",
            "contents"
        ));
    }

    /**
     * Multilangue
     * @param $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocale($locale)
    {
        if(in_array($locale, config('piclommerce.languages'))){
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }
}