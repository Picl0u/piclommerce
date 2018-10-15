<?php
namespace App\Http\Controllers\Piclommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Artesaos\SEOTools\Facades\SEOMeta;
use \Artesaos\SEOTools\Facades\OpenGraph;
use Piclou\Piclommerce\Helpers\Share;
use Piclou\Piclommerce\Http\Entities\Comment;
use Piclou\Piclommerce\Http\Entities\Product;
use Piclou\Piclommerce\Http\Entities\ShopCategory;
use Piclou\Piclommerce\Http\Mail\SendCommentToAdmin;
use Piclou\Piclommerce\Http\Requests\CommentRequest;

class ProductController extends Controller
{
    protected $viewPath = 'piclommerce::products.';

    public function lists(string $slug, int $id)
    {
        $category = ShopCategory::select('id','slug','name','parent_id','imageList')
            ->where('published', 1)
            ->where('id', $id)
            ->FirstOrFail();

        if($category->slug != $slug) {
            return redirect(
                Route('product.list',[
                    'slug' => $category->slug,
                    'id' => $category->id
                ]),
                301
            );
        }
        // Parent Categories
        $parent = $category->parentCategory($category->parent_id);

        // Categories
        $associatedCategories = ShopCategory::where('published', 1)->orderBy('order','asc')->get();
        $categories = [];
        foreach($associatedCategories as $assoc) {
            $categories[] = [
                'id' => $assoc->id,
                'name' => $assoc->name,
                'slug' => $assoc->slug,
                'order' => $assoc->order,
                'parent_id' => $assoc->parent_id,
                'imageList' => $assoc->imageList
            ];
        }

        // Products Order
        $orderBy = setting('products.orderField');
        $orderDir = setting('products.orderDirection');
        $order="pertinence";
        if(Input::has('orderField')){
            $order = Input::get('orderField');
            if($order == 'name_asc') {
                $orderBy = 'name';
                $orderDir = 'ASC';
            }
            if($order == 'name_desc') {
                $orderBy = 'name';
                $orderDir = 'DESC';
            }
            if($order == 'price_asc') {
                $orderBy = 'price_ttc';
                $orderDir = 'ASC';
            }
            if($order == 'price_desc') {
                $orderBy = 'price_ttc';
                $orderDir = 'DESC';
            }
        }
        // Product list
        $products = Product::join('products_has_categories', 'products.id', '=', 'products_has_categories.product_id')
            ->select(
                'products.id',
                'products.uuid',
                'products.stock_available',
                'products.reduce_price',
                'products.reduce_percent',
                'products.reduce_date_begin',
                'products.reduce_date_end',
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
            ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
            ->where('products.published', 1)
            ->where('products_has_categories.shop_category_id',$category->id)
            ->orderBy('products.'.$orderBy, $orderDir)
            ->paginate(setting('products.paginate'))->appends('order',$order);

        // Arianne
        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
        ];
        if(!is_null($parent)){
            $arianne[$parent->name] = Route('product.list',[
                'slug' => $parent->slug,
                'id' => $parent->id
            ]);
            if($parent->id != $category->parent_id){
                $parentCat = ShopCategory::where('id', $category->parent_id)->First();
                if(!empty($parentCat))  {
                    $arianne[$parentCat->name] = Route(
                        'product.list',
                        [
                            'slug' => $parentCat->slug,
                            'id' => $parentCat->id
                        ]
                    );
                }
            }
        }
        $arianne[$category->name] = Route('product.list',[
            'slug' => $category->slug,
            'id' => $category->id
        ]);

        $url = Route('product.list',['slug' => $category->slug,'id' => $category->id ]);
        $title = ($category->seoTitle)?$category->seoTitle:$category->name . " - " . setting("generals.seoTitle");
        $description =($category->seoDescription)?$category->seoDescription:$category->name . " - " .  setting("generals.seoDescription");

        SEOMeta::setCanonical($url);
        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);
        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl($url);
        OpenGraph::setSiteName(setting("generals.websiteName"));
        if($category->imageList) {
            OpenGraph::addImage(asset($category->getMedias("imageList")['target_path']));
        }

        return view($this->viewPath . 'list',
            compact('category','parent', 'arianne', 'products', 'categories', 'order')
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        if(!isset($request->keywords) && empty($request->keywords)) {
            return redirect('/');
        }
        $keywords = str_replace("+", " ",$request->keywords);
        $keywordsArray = explode(" ",$keywords);

        /* Order des produits */
        $orderBy = setting('products.orderField');
        $orderDir = setting('products.orderDirection');
        $order="pertinence";
        if(Input::has('orderField')){
            $order = Input::get('orderField');
            if($order == 'name_asc') {
                $orderBy = 'name';
                $orderDir = 'ASC';
            }
            if($order == 'name_desc') {
                $orderBy = 'name';
                $orderDir = 'DESC';
            }
            if($order == 'price_asc') {
                $orderBy = 'price_ttc';
                $orderDir = 'ASC';
            }
            if($order == 'price_desc') {
                $orderBy = 'price_ttc';
                $orderDir = 'DESC';
            }
        }

        $products = Product::where('products.published', 1)
        ->where(function($query) use ($keywordsArray) {
            foreach($keywordsArray as $keywords) {
                $query->orwhere('products.name', 'like', '%'.ucfirst($keywords).'%')
                    ->orWhere('products.name', 'like', '%'.$keywords.'%')
                    ->orWhere('products.reference', 'like', '%'.$keywords.'%');
            }
        })
        ->select(
            'products.id',
            'products.uuid',
            'products.stock_available',
            'products.reduce_date_begin',
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
        ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
        ->orderBy('products.'.$orderBy, $orderDir)
        ->paginate(setting('products.paginate'))->appends(['order' => $order, 'keywords' => $keywords]);
        if(count($products) < 1) {
            $category = ShopCategory::where('published', 1)
                ->where(function($query) use ($keywordsArray) {
                    foreach($keywordsArray as $keywords) {
                        $query->orwhere('name', 'like', '%'.ucfirst($keywords).'%')
                            ->orWhere('name', 'like', '%'.$keywords.'%');
                    }
                })->first();
            if(!empty($category)) {
                $products = Product::where('products.published', 1)
                    ->where('products.shop_category_id',$category->id)
                    ->select(
                        'products.id',
                        'products.stock_available',
                        'products.reduce_price',
                        'products.reduce_percent',
                        'products.reduce_date_begin',
                        'products.reduce_date_end',
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
                    ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
                    ->orderBy('products.'.$orderBy, $orderDir)
                    ->paginate(setting('products.paginate'))->appends(['order' => $order, 'keywords' => $keywords]);
            }
        }

        /* Fil d'arianne */
        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.shop_product_search') . " : " . $request->keywords => route('product.search').'?keywords=' . $request->keywords
        ];

        return view($this->viewPath . 'search',
            compact('arianne', 'products', 'keywords', 'order')
        );
    }

    /**
     * @param string $slug
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(string $slug, int $id)
    {
        $product = Product::where('published', 1)
            ->where('id', $id)
            ->FirstOrFail();

        if($product->slug != $slug) {
            return redirect(
                Route('product.show',[
                    'slug' => $product->slug,
                    'id' => $product->id
                ]),
                301
            );
        }
        $product->addView();

        /* Déclinaisons */
        $attributes = $product->ProductsAttributes;
        $declinaisons = [];
        foreach ($attributes as $attribute) {
            $decl = $attribute->getValues('declinaisons');
            foreach($decl as $key => $value) {
                if(array_key_exists($key, $declinaisons)){
                    if(!in_array($value,$declinaisons[$key])){
                        $declinaisons[$key][] = $value;
                    }
                } else {
                    $declinaisons[$key][] = $value;
                }
            }
        }

        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
        ];
        $category = $product->shopCategory;
        $arianne[$category->name] = Route('product.list',[
            'slug' => $category->slug,
            'id' => $category->id
        ]);
        $arianne[$product->name] = Route('product.show',[
            'slug' => $product->slug,
            'id' => $product->id
        ]);

        // Produits associés
        $productAssociates = $product->ProductsAssociates;
        $relatedProducts = [];
        if(count($productAssociates) < 1) {
            $relatedProducts = Product::select(
                'products.id',
                'products.stock_available',
                'products.reduce_price',
                'products.reduce_percent',
                'products.reduce_date_begin',
                'products.reduce_date_end',
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
                ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
                ->where('products.published', 1)
                ->where('products.id','!=',$product->id)
                ->where('products.shop_category_id', $product->shop_category_id)
                ->inRandomOrder()
                ->get(6);
        }

        $comments = '';
        if(!empty(setting('products.commentEnable'))) {
            $comments = $product->Comments;
        }

        $url = Route('product.show',['slug' => $product->slug,'id' => $product->id ]);
        $title = ($product->seoTitle)?$product->seoTitle:$product->name . " - " . setting("generals.seoTitle");
        $description =($product->seoDescription)?$product->seoDescription:$product->name . " - " .  setting("generals.seoDescription");

        SEOMeta::setCanonical($url);
        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);
        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl($url);
        OpenGraph::setSiteName(setting("generals.websiteName"));
        if($product->image) {
            OpenGraph::addImage(asset($product->getMedias("image")['target_path']));
        }

        $share = [];
        if (!empty(setting('products.socialEnable'))) {
            $share = (new Share($url,$title,asset((!empty($product->getMedias("image")))?$product->getMedias("image")['target_path']:null)))->render();
        }

        return view(
            $this->viewPath . "detail",
            compact(
                'product',
                'arianne',
                'images',
                'contents',
                'share',
                'relatedProducts',
                'comments',
                'productAssociates',
                'declinaisons'
            )
        );
    }

    /**
     * @param CommentRequest $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addComment(CommentRequest $request, string $uuid)
    {
        $product = Product::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $insert = [
            'published' => 1,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'comment' => $request->comment
        ];
        Comment::create($insert);

        Mail::to(setting('generals.email'))
            ->send(new SendCommentToAdmin($product, $user, $request->comment));

        session()->flash('success', __("piclommerce::web.shop_comment_send"));
        return redirect()->route('product.show',['slug' => $product->slug, 'id' => $product->id]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function flashSales()
    {
        /* Order des produits */
        $orderBy = 'reduce_date_end';
        $orderDir = 'asc';
        $order="pertinence";

        if(Input::has('order')){
            $order = Input::get('order');
            if($order == 'name_asc') {
                $orderBy = 'name';
                $orderDir = 'ASC';
            }
            if($order == 'name_desc') {
                $orderBy = 'name';
                $orderDir = 'DESC';
            }
            if($order == 'price_asc') {
                $orderBy = 'price_ttc';
                $orderDir = 'ASC';
            }
            if($order == 'price_desc') {
                $orderBy = 'price_ttc';
                $orderDir = 'DESC';
            }
        }

        $products = Product::select(
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
        ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
        ->orderBy('products.'.$orderBy, $orderDir)
        ->paginate(setting('products.paginate'))->appends('order',$order);

        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.navigation_flash') => route('product.flash')
        ];

        $url = Route('product.flash');
        $title = __('piclommerce::web.navigation_flash') . " - " . setting("generals.seoTitle");
        $description = __('piclommerce::web.navigation_flash') . " - " .  setting("generals.seoDescription");

        SEOMeta::setCanonical($url);
        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);
        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl($url);
        OpenGraph::setSiteName(setting("generals.websiteName"));

        return view(
            $this->viewPath . 'flash',
            compact('products', 'arianne', 'order')
        );
    }
}