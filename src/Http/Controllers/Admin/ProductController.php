<?php
namespace Piclou\Piclommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\Product;
use Piclou\Piclommerce\Http\Entities\ProductsAssociate;
use Piclou\Piclommerce\Http\Entities\ProductsAttribute;
use Piclou\Piclommerce\Http\Entities\ProductsHasCategory;
use Piclou\Piclommerce\Http\Entities\ShopCategory;
use Piclou\Piclommerce\Http\Entities\Vat;
use Piclou\Piclommerce\Http\Requests\Admin\ProductImports;
use Piclou\Piclommerce\Http\Requests\Admin\Products;
use Piclou\Piclommerce\Http\Requests\Admin\ProductsImports;
use SEO;
use Yajra\DataTables\DataTables;
use \Maatwebsite\Excel\Facades\Excel;
use \File;

class ProductController extends Controller
{
    /**
     * @var string
     */
    private $viewPath = 'piclommerce::admin.shop.products.';

    /**
     * @var string
     */
    private $route = 'admin.shop.products.';

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * @param string $viewPath
     */
    public function setViewPath(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute(string $route)
    {
        $this->route = $route;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTable();
        }
        SEO::setTitle(__("piclommerce::admin.navigation_products"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Product();
        $vats = Vat::select('id','name','percent')->get();

        $products = Product::select('id','name','reference')
            ->orderBy("name",'asc')
            ->get();
        $categories = ShopCategory::select('id','name','parent_id')->get();
        $categories_array = [];
        foreach ($categories as $category) {
            $categories_array[] = [
                'id' => $category->id,
                'name' => $category->translate('name'),
                'parent_id' => $category->parent_id
            ];
        }
        SEO::setTitle(__("piclommerce::admin.navigation_products") . " - " . __("piclommerce::admin.add"));
        return view($this->viewPath . 'create', compact(
        'data', 'categories', 'vats', 'categories_array', 'products'
        ));
    }

    /**
     * @param Products $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Products $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $product = Product::create([
            'published' => ($request->published)?1:0,
            'week_selection' => ($request->week_selection)?1:0,
            'name' => $request->name,
            'slug' => (empty($request->slug))?str_slug($request->name):str_slug($request->slug),
            'summary' => $request->summary,
            'description' => $request->description,
            'shop_category_id' => $request->shop_category_id,
            'reference' => $request->reference,
            'isbn_code' => $request->isbn_code,
            'ean_code' => $request->ean_code,
            'upc_code' => $request->upc_code,
            'vat_id' => $request->vat_id,
            'price_ht' => $request->price_ht,
            'price_ttc' => $request->price_ttc,
            'reduce_date_begin' => $request->reduce_date_begin,
            'reduce_date_end' => $request->reduce_date_end,
            'reduce_price' => $request->reduce_price,
            'reduce_percent' => $request->reduce_percent,
            'stock_brut' => $request->stock_brut,
            'stock_available' => $request->stock_brut,
            'weight' => $request->weight,
            'height' => $request->height,
            'length' => $request->length,
            'width' => $request->width,
            'seo_keywords' => $request->seo_keywords,
            'order' => (Product::count() + 1),
        ]);

        if($request->hasFile('image')){
            $product->uploadImage('image', 'shop/products', $request->image);
        }
        if($request->hasFile('imageList')){
            $product->uploadMultipleImages('imageList', 'shop/products', $request->imageList);
        }

        $product
            ->setTranslation('name', config('app.locale'), $request->name)
            ->setTranslation('slug', config('app.locale'), (empty($request->slug))?str_slug($request->name):str_slug($request->slug))
            ->setTranslation('summary', config('app.locale'), $request->summary)
            ->setTranslation('description', config('app.locale'), $request->description)
            ->setTranslation('seo_title', config('app.locale'), $request->seo_title)
            ->setTranslation('seo_description', config('app.locale'), $request->seo_description)
            ->update();

        foreach ($request->categories as $category) {
            if (!empty($category)) {
                ProductsHasCategory::create([
                    'shop_category_id' => intval($category),
                    'product_id' => $product->id,
                ]);
            }
        }
        if (!empty($request->associates)) {
            foreach ($request->associates as $associate) {
                if (!empty($associate)) {
                    ProductsAssociate::create([
                        'product_parent' => $product->id,
                        'product_id' => $associate
                    ]);
                }
            }
        }

        session()->flash('success', __("piclommerce::admin.shop_product_create"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Product::where('uuid',$uuid)->FirstOrFail();
        $products = Product::select('id','name','reference')
            ->where('id', '!=', $data->id)
            ->orderBy("name",'asc')
            ->get();
        $categories = ShopCategory::select('id','name','parent_id')->get();
        $categories_array = [];
        foreach ($categories as $category) {
            $categories_array[] = [
                'id' => $category->id,
                'name' => $category->translate('name'),
                'parent_id' => $category->parent_id
            ];
        }
        $vats = Vat::select('id','name','percent')->get();

        SEO::setTitle(__("piclommerce::admin.navigation_products") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact(
            'data', 'categories', 'vats', 'categories_array', 'products'
        ));

    }

    /**
     * @param Products $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Products $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $product = Product::where('uuid', $uuid)->FirstOrFail();
        Product::where("id", $product->id)->update([
            'published' => ($request->published)?1:0,
            'week_selection' => ($request->week_selection)?1:0,
            'name' => $request->name,
            'slug' => (empty($request->slug))?str_slug($request->name):str_slug($request->slug),
            'summary' => $request->summary,
            'description' => $request->description,
            'shop_category_id' => $request->shop_category_id,
            'reference' => $request->reference,
            'isbn_code' => $request->isbn_code,
            'ean_code' => $request->ean_code,
            'upc_code' => $request->upc_code,
            'vat_id' => $request->vat_id,
            'price_ht' => $request->price_ht,
            'price_ttc' => $request->price_ttc,
            'reduce_date_begin' => $request->reduce_date_begin,
            'reduce_date_end' => $request->reduce_date_end,
            'reduce_price' => $request->reduce_price,
            'reduce_percent' => $request->reduce_percent,
            'stock_brut' => $request->stock_brut,
            'stock_available' => $request->stock_brut,
            'weight' => $request->weight,
            'height' => $request->height,
            'length' => $request->length,
            'width' => $request->width,
            'seo_keywords' => $request->seo_keywords,
        ]);

        if($request->hasFile('image')){
            $product->uploadImage('image', 'shop/products', $request->image);
        }
        if($request->hasFile('imageList')){
            $product->uploadMultipleImages('imageList', 'shop/products', $request->imageList);
        }

        $product
            ->setTranslation('name', config('app.locale'), $request->name)
            ->setTranslation('slug', config('app.locale'), (empty($request->slug))?str_slug($request->name):str_slug($request->slug))
            ->setTranslation('summary', config('app.locale'), $request->summary)
            ->setTranslation('description', config('app.locale'), $request->description)
            ->setTranslation('seo_title', config('app.locale'), $request->seo_title)
            ->setTranslation('seo_description', config('app.locale'), $request->seo_description)
            ->update();

        ProductsHasCategory::where("product_id",$product->id)->delete();
        foreach($request->categories as $category) {
            if(!empty($category)) {
                ProductsHasCategory::create([
                    'shop_category_id' => intval($category),
                    'product_id' => $product->id,
                ]);
            }
        }

        ProductsAssociate::where('product_parent', $product->id)->delete();
        if (!empty($request->associates)) {
            foreach ($request->associates as $associate) {
                if (!empty($associate)) {
                    ProductsAssociate::create([
                        'product_parent' => $product->id,
                        'product_id' => $associate
                    ]);
                }
            }
        }

        session()->flash('success', __("piclommerce::admin.shop_product_edit"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $product = Product::where('uuid', $uuid)->FirstOrFail();
        $medias = $product->getMedias("image");
        /*if($medias)  {
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
        }*/

        Product::where('id', $product->id)->delete();

        session()->flash('success', __("piclommerce::admin.shop_product_delete"));
        return redirect()->route($this->route . 'index');
    }

    public function duplicate(string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $product = Product::where('uuid', $uuid)->FirstOrFail()->toArray();
        $id = $product['id'];
        unset($product['id']);
        unset($product['uuid']);
        unset($product['created_at']);
        unset($product['updated_at']);
        $product['reference'] = $product['reference'] . "-" . ($id+1);
        $newProduct = Product::create($product);

        $categories = ProductsHasCategory::where('product_id', $id)->get();
        foreach($categories as $category) {
            ProductsHasCategory::create([
                'shop_category_id' => $category->shop_category_id,
                'product_id' => $newProduct->id,
            ]);
        }
        $associates = ProductsAssociate::where('product_id', $id)->get();
        foreach($associates as $associate) {
            ProductsAssociate::create([
                'product_parent' => $newProduct->id,
                'product_id' => $associate->product_id
            ]);
        }
        $declinaisons = ProductsAttribute::where("product_id", $id)->get();
        foreach($declinaisons as $declinaison) {
            ProductsAttribute::create([
                'product_id' => $newProduct->id,
                'declinaisons' => $declinaison->declinaisons,
                'stock_brut' => $declinaison->stock_brut,
                'price_impact' => $declinaison->price_impact,
                'images' => $declinaison->images,
                'reference' => $declinaison->reference,
                'ean_code' => $declinaison->ean_code,
                'upc_code' => $declinaison->upc_code,
                'isbn_code' => $declinaison->isbn_code,
            ]);
        }

        session()->flash('success', __("piclommerce::admin.shop_product_duplicate_success"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateImage(Request $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $product = Product::where('uuid', $uuid)->FirstOrFail();
        $medias = $product->getMedias("image");
        $updateMedia = [
            'uuid' => $medias['uuid'],
            'target_path' => $medias['target_path'],
            'file_name' => $medias['file_name'],
            'file_type' => $medias['file_type'],
            'alt' => ($request->alt)?$request->alt:$medias['alt'],
            'description' => ($request->description)?$request->description:$medias['description'],
        ];
        Product::where('id', $product->id)->update([
            'image' => json_encode($updateMedia),
        ]);
        return response()->json(["message" => __("piclommerce::admin.medias_updated")]);
    }

    /**
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateImageList(Request $request, string $uuid, string $image)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $product = Product::where('uuid', $uuid)->FirstOrFail();
        $mediasList = $product->getMedias("imageList");
        $updateMedia = [];
        foreach($mediasList as $key => $medias) {
            if($medias['uuid'] == $image) {
                $updateMedia[$key] = [
                    'uuid' => $medias['uuid'],
                    'target_path' => $medias['target_path'],
                    'file_name' => $request->file_name,
                    'file_type' => $medias['file_type'],
                    'alt' => $request->alt,
                    'description' => $request->description,
                ];
            } else {
                $updateMedia[$key] = [
                    'uuid' => $medias['uuid'],
                    'target_path' => $medias['target_path'],
                    'file_name' => $medias['file_name'],
                    'file_type' => $medias['file_type'],
                    'alt' => $medias['alt'],
                    'description' => $medias['description'],
                ];
            }
        }
        Product::where('id', $product->id)->update([
            'imageList' => json_encode($updateMedia),
        ]);
        return response()->json(["message" => __("piclommerce::admin.medias_updated")]);
    }

    public function imageDelete(string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $category = ShopCategory::where('uuid', $uuid)->firstOrFail();
        $category->update([
            'image' => null
        ]);
        if(!empty($category->image) && file_exists($category->image)) {
            unlink($category->image);
        }
        session()->flash('success', __("piclommerce::admin.medias_delete"));
        return redirect()->route($this->route . 'edit',['uuid' => $category->uuid]);
    }

    public function imageListDelete(string $uuid, string $image)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $product = Product::where('uuid', $uuid)->FirstOrFail();
        $mediasList = $product->getMedias("imageList");
        $updateMedia = [];
        foreach($mediasList as $key => $medias) {
            if($medias['uuid'] == $image) {
                if(file_exists($medias['target_path'])) {
                    unlink($medias['target_path']);
                }
            } else {
                $updateMedia[$key] = [
                    'uuid' => $medias['uuid'],
                    'target_path' => $medias['target_path'],
                    'file_name' => $medias['file_name'],
                    'file_type' => $medias['file_type'],
                    'alt' => $medias['alt'],
                    'description' => $medias['description'],
                ];
            }
        }
        Product::where('id', $product->id)->update([
            'imageList' => json_encode($updateMedia),
        ]);
        session()->flash('success', __("piclommerce::admin.medias_delete"));
        return redirect()->route($this->route . 'edit',['uuid' => $product->uuid]);
    }

    /**
     * Traductions
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translate(Request $request)
    {
        return (new FormTranslate(Product::class))->formRequest($request);
    }

    public function positions(){

        $products = Product::OrderBy('order','asc')->get();
        $datas = [];
        foreach($products as $data){
            $datas[] = [
                'id' => $data->id,
                'name' => $data->translate('name', config('app.locale')),
                'order' => $data->order,
                'parent_id' => 0,
                'slug' => $data->translate('slug', config('app.locale')),
            ];
        }
        return view($this->viewPath . 'positions',compact('datas'));
    }

    public function positionsStore(Request $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $datas = Product::all();
        $dataArray = [];
        foreach ($datas as $data) {
            $dataArray[$data->id] = [
                'order' => $data->order
            ];
        }
        foreach ($request->orders as $key => $order) {
            if (!empty($order['id'])) {
                if ($dataArray[$order['id']]['order'] != $key) {
                    Product::where('id', $order['id'])->update([
                        'order' => $key,
                    ]);
                }
            }
        }
        return __("piclommerce::admin.position_success");
    }

    public function declinaison(int $id)
    {
        $product = Product::where('id', $id)->FirstOrFail();
        $title = __("piclommerce::admin.shop_product_declinaison_create");
        $route = Route('admin.shop.products.attribute.store',['id' => $product->id]);
        $data = new ProductsAttribute();
        $medias = $product->getMedias("imageList");
        return view(
            $this->viewPath. 'attributes.form',
            compact('data','title', 'route','medias')
        );
    }

    public function declinaisonStore(Request $request, int $id)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $product = Product::where('id', $id)->FirstOrFail();
        $product->update([
            "stock_brut" => $product->stock_brut + $request->stock_brut
        ]);
        $insert = [
            'product_id' => $product->id,
            'declinaisons' => '{}',
            'stock_brut' => $request->stock_brut,
            'price_impact' => $request->price_impact,
            'images' => json_encode($request->images),
            'reference' => $request->reference,
            'ean_code' => $request->ean_code,
            'upc_code' => $request->upc_code,
            'isbn_code' => $request->isbn_code,
        ];

        $attribute = ProductsAttribute::create($insert);
        $insertAttributes = [];
        $reference = $request->reference;
        if(empty($request->reference)) {
            $reference = $product->reference;
            foreach($request->attr as $key => $attr) {
                $insertAttributes[$attr] = $request->values[$key];
                $reference .= "-" . $request->values[$key];
            }
        } else {
            foreach($request->attr as $key => $attr) {
                $insertAttributes[$attr] = $request->values[$key];
            }
        }

        $attribute->update([
            'declinaisons' => $attribute->setAttr('declinaisons', $insertAttributes),
            'reference' => $reference
        ]);

        return view($this->viewPath . 'attributes.line',compact('attribute'));

    }

    public function declinaisonEdit(int $id, string $uuid)
    {
        $product = Product::where('id', $id)->FirstOrFail();
        $data = ProductsAttribute::where('product_id', $id)
            ->where('uuid', $uuid)
            ->firstOrFail();
        $title = __("piclommerce::admin.shop_product_declinaison_edit");
        $route = Route('admin.shop.products.attribute.update',['uuid' => $uuid]);
        $medias = $product->getMedias("imageList");
        $images = json_decode($data->images ?? '' ?: '{}', true) ?: [];
        return view(
            $this->viewPath. 'attributes.form',
            compact('data','title', 'route', 'medias','images')
        );
    }

    public function declinaisonUpdate(Request $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $attribute = ProductsAttribute::where('uuid', $uuid)->FirstOrFail();

        $product = Product::where('id',$attribute->product_id)->First();
        $product->update([
            "stock_brut" => $product->stock_brut + $request->stock_brut
        ]);

        $attribute->update([
            'declinaisons' => '{}',
            'stock_brut' => $request->stock_brut,
            'reference' => $request->reference,
            'ean_code' => $request->ean_code,
            'upc_code' => $request->upc_code,
            'isbn_code' => $request->isbn_code,
            'price_impact' => $request->price_impact,
            'images' => json_encode($request->images),
        ]);

        $insertAttributes = [];
        foreach($request->attr as $key => $attr) {
            $insertAttributes[$attr] = $request->values[$key];
        }
        $attribute->update([
            'declinaisons' =>  $attribute->setAttr('declinaisons', $insertAttributes)
        ]);

        return view($this->viewPath . 'attributes.cell',compact('attribute'));

    }

    public function declinaisonDelete(string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $attribute = ProductsAttribute::where('uuid', $uuid)->FirstOrFail();
        $attribute->delete();
        return '';
    }

    public function import()
    {
        $directory = config('piclommerce.fileUploadFolder') . '/' . config('piclommerce.directoryImport');
        if(!file_exists($directory)){
            if(!mkdir($directory,0770, true)){
                dd('Echec lors de la création du répertoire : '.$directory);
            }
        }
        $files = File::allFiles($directory);
        SEO::setTitle(__("piclommerce::admin.navigation_products") . " - " . __("piclommerce::admin.import"));

        return view($this->viewPath . 'import', compact('files'));
    }

    public function storeImport(ProductsImports $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        if($request->hasFile('file')){
            $file = $request->file;
            $directory = config('piclommerce.directoryImport');

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
            if($file->getClientOriginalExtension() == 'csv' || $file->getClientOriginalExtension() == 'xls') {

                Excel::load($targetPath)->chunk(5, function($results){
                    $results->each(function($row) {
                        if(
                            !empty($row->ref) &&
                            !empty($row->price_ttc) &&
                            !is_null($row->ref) &&
                            !is_null($row->price_ttc)
                        ) {
                            $this->insertRow($row);
                        }
                    });
                    session()->flash('success',__("piclommerce::admin.shop_product_import_success"));
                });
            } else {
                session()->flash('error',__("piclommerce::admin.shop_product_import_error") . $file->getClientOriginalExtension());
            }
        }
        return redirect()->route($this->route . 'imports');
    }

    public function import_attributes()
    {
        $directory = config('piclommerce.fileUploadFolder') . '/' .
            config('piclommerce.directoryImport') .
            '/attributes';

        if(!file_exists($directory)){
            if(!mkdir($directory,0770, true)){
                dd('Echec lors de la création du répertoire : '.$directory);
            }
        }
        $files = File::allFiles($directory);
        SEO::setTitle(__("piclommerce::admin.navigation_products") . " - " . __("piclommerce::admin.import"));

        return view($this->viewPath . 'importAttributes', compact('files'));
    }

    public function import_attributes_store(Request $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        if($request->hasFile('file')){
            $file = $request->file;
            $directory = config('piclommerce.directoryImport') . "/attributes";

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
            if($file->getClientOriginalExtension() == 'csv' || $file->getClientOriginalExtension() == 'xls') {

                Excel::load($targetPath)->chunk(100, function($results){
                    $results->each(function($row) {
                        if(
                            !empty($row->product_ref) &&
                            !is_null($row->product_ref)
                        ) {
                            $product = Product::where('reference', $row->product_ref)->First();
                            if (!is_null($product)) {
                                $insert = [
                                    'product_id' => $product->id,
                                    'stock_brut' => (isset($row->quantiti)) ? $row->quantiti : $row->quantite,
                                    'ean_code' => $row->ean,
                                    'upc_code' => $row->upc,
                                    'isbn_code' => $row->isbn,
                                    'price_impact' => $row->price_impact
                                ];

                                $reference = $row->ref;
                                if (empty($reference)) {
                                    $reference = $product->reference;

                                    $declinaisons = explode('/', $row->declinaisonvaleur_sipari_par_un);
                                    foreach ($declinaisons as $declinaison) {
                                        $attr = explode(":", $declinaison);
                                        $insertAttributes[$attr[0]] = $attr[1];
                                        $reference .= "-" . $attr[1];
                                    }

                                    $attribute = ProductsAttribute::where("reference", $row->ref)
                                        ->where('product_id', $product->id)
                                        ->first();

                                    if (!is_null($attribute)) {
                                        $attribute->update($insert);
                                    } else {
                                        $attribute = ProductsAttribute::insert($insert);
                                    }

                                    $attribute->update([
                                        'declinaisons' => $attribute->setAttr('declinaisons', $insertAttributes),
                                        'reference' => $reference
                                    ]);

                                }
                            }
                        }
                    });
                    session()->flash('success', __("piclommerce:admin.shop_product_import_success"));
                });
            } else {
                session()->flash('error', __("piclommerce:admin.shop_product_import_serror") . $file->getClientOriginalExtension());
            }
        }
        return redirect()->route($this->route . 'imports');
    }

    public function export_attributes()
    {
        return Excel::create('export-declinaisons-'.now(), function($excel) {
            $excel->setTitle('Export declinaisons-'.date('d/m/Y à H:i'));

            $excel->sheet('Sheetname', function($sheet) {

                $sheet->row(1, array(
                    'Product ref',
                    'Ref',
                    'Declinaison:Valeur (separe par un /)',
                    'Slug',
                    'Quantite',
                    'Impact prix',
                    'Ean',
                    'UPC',
                    'Isbn'
                ));
            });
        })->download('csv');
    }

    /**
     * Exporter produits
     * @return mixed
     */
    public function export_product()
    {
        return Excel::create('export-products-'.now(), function($excel){
            $excel->setTitle('Export products-'.date('d/m/Y à H:i'));

            $excel->sheet('Sheetname', function($sheet){

                $sheet->row(1, array(
                    'Ref',
                    'Published (1 ou 0)',
                    'Name',
                    'Slug',
                    'Summary',
                    'Description',
                    'isbn_code',
                    'upc_code',
                    'ean_code',
                    'main_category',
                    'categories',
                    'taxe (en %)',
                    'price_ht',
                    'price_ttc',
                    'week_selection (1 ou 0)',
                    'reduce_date_begin',
                    'reduce_date_end',
                    'reduce_price',
                    'reduce_percent',
                    'no_stock (1 ou 0)',
                    'stock_brut',
                    'stock_booked',
                    'stock_available',
                    'Weight (kg)',
                    'height (cm)',
                    'length (cm)',
                    'width (cm)',
                    'seo_title',
                    'seo_description',
                    'position'
                ));

            });
        })->download('csv');
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $products = Product::select(['id','uuid','published','name','reference','image','price_ht','stock_available','updated_at']);
        return DataTables::of($products)
            ->addColumn('actions', function(Product $product) {
                return $this->getTableButtons($product->uuid);
            })
            ->editColumn("published",function(Product $product) use ($datatable) {
                return $datatable->yesOrNot($product->published);
            })
            ->editColumn("updated_at",function(Product $product) use ($datatable) {
                return $datatable->date($product->updated_at);
            })
            ->editColumn("image",function(Product $product) use ($datatable) {
                $medias = $product->getMedias("image");
                if ($medias) {
                    return $datatable->image(
                        $product->resizeImage("image", 30, 30)['target_path'],
                        $medias['target_path'],
                        $medias['alt']
                    );
                } else {
                    return "";
                }
            })
            ->editColumn("name",function(Product $product){
                return $product->translate("name");
            })
            ->editColumn("stock_available",function(Product $product){
                if($product->stock_available < 1){
                    return '<label class="label error">' . $product->stock_available .'</label>';
                }else {
                    return '<label class="label success">' . $product->stock_available .'</label>';
                }
            })
            ->editColumn('price_ht',function(Product $product) {
                return priceFormat($product->price_ht);
            })

            ->rawColumns(['actions','published','image','price_ht','stock_available'])
            ->make(true);
    }

    /**
     * @return string
     */
    private function getTableButtons($uuid): string
    {
        $editRoute = route($this->getRoute() . "edit",['uuid' => $uuid]);
        $duplicateRoute = route($this->getRoute() . "duplicate",['uuid' => $uuid]);
        $deleteRoute = route($this->getRoute() . "delete",['uuid' => $uuid]);
        $html = '<a href="'.$editRoute.'" class="table-button edit-button"><i class="fa fa-pencil"></i></a>';
        $html .= '<a href="'.$duplicateRoute.'" class="table-button duplicate-button duplicate-alert"><i class="fa fa-clipboard"></i></a>';
        $html .= '<a href="'.$deleteRoute.'" class="table-button delete-button confirm-alert"><i class="fa fa-trash"></i></a>';
        return $html;
    }

    /**
     * @param $row
     */
    private function insertRow($row)
    {
        /* Cherche une catégorie si pas existante en créé une */
        $category = ShopCategory::where('name','like', '%'.$row->main_category.'%')->First();
        if (empty($category)) {
            $category = ShopCategory::create([
                'published' => 1,
                'name' => $row->main_category,
                'slug' => str_slug($row->main_category),

            ]);
            $category->setTranslation('name', config('app.locale'), $row->main_category)
                ->setTranslation('slug', config('app.locale'), str_slug($row->main_category))
                ->update();
        }
        $mainCategoryId = $category->id;

        /* Vérification de la taxe si pas existante création */
        $vat = Vat::where('percent', $row->taxe_en)->First();
        if (empty($vat)) {
            $vat = Vat::create([
                'name' => 'Taxe : ' . $row->taxe_en . '%',
                'percent' => $row->taxe_en
            ]);
        }
        // En ligne ?
        if ($row->published_1_ou_0 == 1.0) {
            $row->published_1_ou_0 = 1;
        } else {
            $row->published_1_ou_0 = 0;
        }
        // Selection de la semaine
        if ($row->week_selection_1_ou_0 == 1.0) {
            $row->week_selection_1_ou_0 = 1;
        } else {
            $row->week_selection_1_ou_0 = 0;
        }
        /* Formatage des dates */
        if (!empty($row->reduce_date_begin)) {
            $row->reduce_date_begin = Carbon::parse(str_replace('/', '-', $row->reduce_date_begin))
                ->format('Y-m-d H:i:s');
        }
        if (!empty($row->reduce_date_end)) {
            $row->reduce_date_end = Carbon::parse(str_replace('/', '-', $row->reduce_date_end))
                ->format('Y-m-d H:i:s');
        }
        /* Slug du produit */
        if (empty($row->slug)) {
            $slug = str_slug($row->name);
        } else {
            $slug = str_slug($row->slug);
        }
        /* Stock disponible */
        if (empty($row->stock_available)) {
            $row->stock_available = $row->stock_brut - $row->stock_booked;
        }

        $insert = [
            'reference' => $row->ref,
            'published' => $row->published_1_ou_0,
            'shop_category_id' => $mainCategoryId,
            'isbn_code' => $row->isbn_code,
            'upc_code' => $row->upc_code,
            'ean_code' => $row->ean_code,
            'vat_id' => $vat->id,
            'price_ht' => $row->price_ht,
            'price_ttc' => $row->price_ttc,
            'week_selection' => $row->week_selection_1_ou_0,
            'reduce_date_begin' => $row->reduce_date_begin,
            'reduce_date_end' => $row->reduce_date_end,
            'reduce_price' => $row->reduce_price,
            'reduce_percent' => $row->reduce_percent,
            'stock_brut' => intval($row->stock_brut),
            'stock_booked' => intval($row->stock_booked),
            'stock_available' => intval($row->stock_available),
            'weight' => $row->weight_kg,
            'height' => $row->height_cm,
            'length' => $row->length_cm,
            'width' => $row->width_cm,
        ];
        //dd($insert);
        /* Test si le produit existe */
        $product = Product::where('reference', $row->ref)->First();
        if (!empty($product)) {
            /* Update Produit */
            Product::where('id', $product->id)->update($insert);
            $product->setTranslation('name', config('app.locale'), $row->name)
                ->setTranslation('slug', config('app.locale'), $slug)
                ->setTranslation('summary', config('app.locale'), '<p>'.$row->summary.'</p>')
                ->setTranslation('description', config('app.locale'), '<p>'.$row->description.'</p>')
                ->setTranslation('seo_title', config('app.locale'), $row->seo_title)
                ->setTranslation('seo_description', config('app.locale'), $row->seo_description)
                ->update();
            $this->flush('product', $product->id);
        } else {
            /* Insert Produit */
            $product = Product::create($insert);
            $product->setTranslation('name', config('app.locale'), $row->name)
                ->setTranslation('slug', config('app.locale'), $slug)
                ->setTranslation('summary', config('app.locale'), '<p>'.$row->summary.'</p>')
                ->setTranslation('description', config('app.locale'), '<p>'.$row->description.'</p>')
                ->setTranslation('seo_title', config('app.locale'), $row->seo_title)
                ->setTranslation('seo_description', config('app.locale'), $row->seo_description)
                ->update();
        }
        /* Liste des catégories */
        $categories = explode("/", $row->categories);
        $categoriesID = [];
        foreach ($categories as $cat) {
            if (!empty($cat)) {
                $category = ShopCategory::where('name','like', '%'.$row->main_category.'%')->First();
                if (empty($category)) {
                    $category = ShopCategory::create([
                        'published' => 1,

                    ]);

                    $category->setTranslation('name', config('app.locale'), $cat)
                        ->setTranslation('slug', config('app.locale'), str_slug($cat))
                        ->update();
                }
                $categoriesID[] = $category->id;
            }
        }

        /* Insertion de toute les catégories */
        ProductsHasCategory::where("product_id", $product->id)->delete();
        foreach ($categoriesID as $category) {
            if (!empty($category)) {
                ProductsHasCategory::create([
                    'shop_category_id' => intval($category),
                    'product_id' => $product->id,
                ]);
            }
        }
    }


}