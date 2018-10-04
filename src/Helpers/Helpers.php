<?php
/**
 * @param $img
 * @param bool $width
 * @param bool $height
 * @param string $direction
 * @return string
 */
function resizeImage($img, $width = false, $height = false, string $direction = 'center'): string
{
    if(is_null($img) || empty($img)){
        return '/'. config('piclommerce.imageNotFound');
    }
    $dir = config('piclommerce.imageCacheFolder');
    $infos = pathinfo($img);
    $fileName = $infos['filename'];
    $extension = $infos['extension'];
    $dir .= '/' . $infos['dirname'];

    if (!file_exists($infos['dirname']. '/' . $fileName . "." .  $infos['extension'])) {
        return '/'. config('piclommerce.imageNotFound');
    }
    if(!file_exists($dir)){
        if(!mkdir($dir,0770, true)){
            dd('Echec lors de la création du répertoire : '.$dir);
        }
    }

    if ($width && $height) {
        $cacheResize = "_".$width."_".$height;
    } elseif ($width && !$height) {
        $cacheResize = "_".$width;
    } else {
        $cacheResize = "_".$height;
    }

    if (file_exists(public_path() . "/" . $dir . "/" . $fileName.$cacheResize.".".$extension)) {
        return asset($dir. "/" . $fileName.$cacheResize.".".$extension);
    } else {
        $manager = new \Intervention\Image\ImageManager(['drive' => 'gd']);
        $image = $manager->make($img);
        if ($width && $height) {
            $image->fit($width, $height, function () {
            }, $direction);
        } elseif ($width && !$height) {
            $image->fit($width, null, function () {
            }, $direction);
        } else {
            $image->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        $image->save(
            $dir . "/" . $fileName.$cacheResize.".".$extension,
            config('piclommerce.imageQuality')
        );
        return "/".$dir . "/" . $fileName.$cacheResize.".".$extension;
    }
}

/**
 * Upload des images
 * @param string $directory
 * @param $file
 * @return string
 */
function uploadImage(string $directory, $file): string
{
    $directory = str_replace('\\', '/',$directory);

    $dir = config('piclommerce.fileUploadFolder') . DIRECTORY_SEPARATOR .$directory;
    if(!file_exists($dir)){
        if(!mkdir($dir,0770, true)){
            dd('Echec lors de la création du répertoire : '.$dir);
        }
    }
    $fileName = $file->getClientOriginalName();
    $extension = getExtension($fileName);

    $fileNewName = time().str_slug(str_replace(".".$extension,"",$fileName)).".".strtolower($extension);
    $file->move($dir,$fileNewName);
    $targetPath = $dir. "/" . $fileNewName;

    $imageManager =  new \Intervention\Image\ImageManager();
    $img = $imageManager->make($targetPath);
    $width = $img->width();
    if ($width > config('piclommerce.imageMaxWidth')) {
        $img->resize( config('piclommerce.imageMaxWidth'), null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($targetPath, config('piclommerce.imageQuality'));
    }
    return $targetPath;
}

/**
 * Upload des fichiers
 * @param string $directory
 * @param $file
 * @return string
 */
function uploadFile(string $directory, $file): string
{
    $directory = str_replace("\\","/",$directory);
    $file = str_replace("\\","/",$file);
    $dir = config('ikCommerce.fileUploadFolder') . "/" .$directory;
    if(!file_exists($dir)){
        if(!mkdir($dir,0770, true)){
            dd('Echec lors de la création du répertoire : '.$dir);
        }
    }
    $fileName = $file->getClientOriginalName();
    $extension = getExtension($fileName);

    $fileNewName = time().str_slug(str_replace(".".$extension,"",$fileName)).".".strtolower($extension);
    $file->move($dir,$fileNewName);
    $targetPath = $dir. DIRECTORY_SEPARATOR . $fileNewName;;

    return $targetPath;

}

/**
 * @param string $str
 * @return string
 */
function getExtension(string $str): string
{
    $i = strrpos($str, ".");
    if(!$i) {
        return "";
    }
    $l = strlen($str) - $i;
    return substr($str, $i+1, $l);
}

/**
 * @param array|null $data
 * @return object|\Piclou\Piclommerce\Helpers\NestableExtends
 */
function nestableExtends(array $data = null)
{
    $nestable = new \Piclou\Piclommerce\Helpers\NestableExtends();
    if (is_array($data)) {
        $nestable = $nestable->make($data);
    }
    return $nestable;
}

/**
 * @param $modelName
 * @param $data
 * @return \Piclou\Piclommerce\Helpers\Translatable\FormTranslate
 */
function formTranslate($modelName, $data)
{
    return(new \Piclou\Piclommerce\Helpers\Translatable\FormTranslate(
        $modelName,
        $data
    ));
}

/**
 * @param float $price
 * @param string $currency
 * @return string
 */
function priceFormat(float $price, $currency = false): string
{
    if(!$currency) {
        $currency = config("piclommerce.currency");
    }
    return number_format($price, 2, ",", " ").$currency;
}

/**
 * @param string $key
 * @return string
 */
function setting(string $key):string
{
    $setting = anlutro\LaravelSettings\Facade::get($key);
    if(is_null($setting)) {
        return '';
    }
    return $setting;
}

/**
 * @return string
 */
function navigationShopCategories(): string
{
    $categories = \Piclou\Piclommerce\Http\Entities\ShopCategory::select('id', 'slug', 'name', 'order', 'parent_id')
        ->where('published', 1)
        ->orderBy('order', 'asc')
        ->get();
    $html = '';
    $last_parent = 0;
    if (count($categories) > 0){
        foreach ($categories as $key => $category) {
            $link = route('product.list', ['slug' => $category->slug, "id" => $category->id]);
            if (!$category->parent_id && empty($category->parent_id)) {
                if($category->parent_id != $last_parent){
                    $html .= '</li>';
                }
                $last_parent = $category->id;
                $html .= '<li class="is-col is-33">';
                    $html .= '<a href="' . $link . '" class="sub-title">';
                        $html .= $category->name;
                    $html .= '</a>';
            } else {
                if ($category->parent_id && $category->parent_id == $last_parent) {
                    $html .= '<a href="' . $link . '" class="sub-child">';
                    $html .= $category->name;
                    $html .= '</a>';
                }
                if($category->parent_id != $last_parent){
                    $html .= '</li>';
                }
            }

        }
        $html .= '</li>';
    }
    return $html;
}

/**
 * @return mixed
 */
function navigationNewProduct()
{
    $orderBy = setting('products.orderField');
    $orderDir = setting('products.orderDirection');
    $html = "";
    $products = \Piclou\Piclommerce\Http\Entities\Product::select(
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
    ->join('shop_categories', 'shop_categories.id', '=', 'products.shop_category_id')
    ->where('products.published', 1)
    ->whereDate('products.created_at', '<=', date('Y-m-d H:i:s'))
    ->whereDate('products.created_at', '>=', date('Y-m-d H:i:s',strtotime('-'.setting('products.new').' days', strtotime(date("Y-m-d")))))
    ->orderBy('products.'.$orderBy, $orderDir)
    ->get(5);

    return $products;
}

function navigationContents(): string
{
    $contents = \Piclou\Piclommerce\Http\Entities\Content::select('id','name','slug')
        ->where('on_menu', 1)
        ->orderBy('order', 'asc')
        ->get();
    $html = '';
    if(count($contents) > 0) {
        $active = '';
        if(Route::current()->getName() == 'content.index'){ $active = 'is-active'; }
        $html .=' <li class="is-menu-parent '.$active.'">';
        $html .='<a href="#">';
        $html .= __("piclommerce::web.informations");
        $html .=' <i class="fa fa-caret-down" aria-hidden="true"></i>';
        $html .='</a>';
        $html .='<ul class="submenu">';
        foreach ($contents as $content) {
            $html .='<li><a href="'. route('content.index',[ 'slug' => $content->slug, 'id' => $content->id ]) .'">';
            $html .= $content->name;
            $html .='</a></li>';
        }
        $html .='</ul>';
        $html .='</li>';
    }
    return $html;
}

/**
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate(string $date, string $format = 'd/m/Y'): string
{
    return \Carbon\Carbon::parse($date)->format($format);
}

/**
 * @param float $price
 * @param $reducPrice
 * @param $reducPercent
 * @return string
 */
function percentReduc(float $price, $reducPrice, $reducPercent)
{
    if(!is_null($reducPrice) && !empty($reducPrice)) {
        $calcul = (($price - $reducPrice)-$price)/$price*100;
    } else{
        $calcul = "-".$reducPercent;
    }

    return number_format(round ($calcul),0, ',', ' ') . "%";
}

/**
 * @return string
 */
function footerNavigation(): string
{
    $contents = \Piclou\Piclommerce\Http\Entities\Content::select('id','name','slug')
        ->where('on_footer', 1)
        ->orderBy('order', 'asc')
        ->get();
    $html = '';
    foreach ($contents as $content) {
        $html .='<a href="'. route('content.index',[ 'slug' => $content->slug, 'id' => $content->id ]) .'">';
            $html .= '<i class="fa fa-angle-double-right" aria-hidden="true"></i> ';
            $html .= $content->name;
        $html .='</a>';
    }
    return $html;
}
/**
 * @return array
 */
function priceCarrier(): array
{
    $total = Cart::instance('shopping')->total(2, ".", "");
    $carrier = \Piclou\Piclommerce\Http\Entities\CarriersPrices::where("price_min", "<", $total)->where(function ($query) use ($total) {
        $query->where('price_max', '>', $total);
    })->where('country_id', setting('orders.countryId'))
        ->orderBy('price', "ASC")
        ->first();

    if (empty($carrier)) {
        $carrier = \Piclou\Piclommerce\Http\Entities\CarriersPrices::where("price_min", "<", $total)
            ->where('price_max', 0)
            ->where('country_id', setting('orders.countryId'))
            ->orderBy('price', "ASC")
            ->first();
    }else{
        $price = $carrier->price;
    }
    if (empty($carrier)) {
        $carrier = \Piclou\Piclommerce\Http\Entities\Carriers::where('default', 1)->first();
        $price = $carrier->default_price;
    } else{
        $price = $carrier->price;
    }
    if (!empty(setting('orders.freeShippingPrice'))) {
        if ($total >= setting('orders.freeShippingPrice')) {
            $price = 0;
        }
    }
    return [
        'priceCarrier' => $price,
        'total' => $price + $total
    ];
}

/**
 * @return array
 */
function checkCoupon(): array
{
    $coupon = [];
    $total = Cart::instance('shopping')->total(2,".","");
    if (session()->get('coupons') ){
        $coupon = (new \App\Http\Controllers\Piclommerce\ShoppingCartController())
            ->checkCoupon(session()->get('coupons')['coupon_id'],$total);
    }
    return $coupon;
}
