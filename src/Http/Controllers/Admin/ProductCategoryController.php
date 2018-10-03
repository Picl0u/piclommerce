<?php
namespace Piclou\Piclommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\ShopCategory;
use Piclou\Piclommerce\Http\Requests\Admin\ShopCategories;
use SEO;
use Yajra\DataTables\DataTables;

class ProductCategoryController extends Controller
{
    /**
     * @var string
     */
    private $viewPath = 'piclommerce::admin.shop.categories.';

    /**
     * @var string
     */
    private $route = 'admin.shop.categories.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_categories"));
        return view($this->viewPath . 'index');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new ShopCategory();
        SEO::setTitle(__("piclommerce::admin.navigation_categories") . " - " . __("piclommerce::admin.add"));
        return view($this->viewPath . 'create', compact('data'));
    }


    /**
     * @param ShopCategories $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ShopCategories $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $category = shopCategory::create([
            'published' => ($request->published)?1:0,
            'on_homepage' => ($request->on_homepage)?1:0,
            'name' => $request->name,
            'slug' => (empty($request->slug))?str_slug($request->name):str_slug($request->slug),
            'description' => $request->description,
            'seo_keywords' => $request->seo_keywords,
            'order' => (shopCategory::count()+1)
        ]);

        if($request->hasFile('image')){
            $category->uploadImage('image', 'shop/categories', $request->image);
        }
        if($request->hasFile('imageList')) {
            $category->uploadImage('imageList', 'shop/categories', $request->imageList);
        }

        $category
            ->setTranslation('name', config('app.locale'), $request->name)
            ->setTranslation('slug', config('app.locale'), (empty($request->slug))?str_slug($request->name):str_slug($request->slug))
            ->setTranslation('description', config('app.locale'), $request->description)
            ->setTranslation('seo_title', config('app.locale'), $request->seo_title)
            ->setTranslation('seo_description', config('app.locale'), $request->seo_description)
            ->update();

        session()->flash('success', __("piclommerce::admin.shop_categories_create"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = ShopCategory::where('uuid', $uuid)->FirstOrFail();
        SEO::setTitle(__("piclommerce::admin.navigation_categories") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data'));

    }

    /**
     * @param ShopCategories $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ShopCategories $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $category = ShopCategory::where('uuid', $uuid)->FirstOrFail();
        ShopCategory::where('id', $category->id)->update([
            'published' => ($request->published)?1:0,
            'on_homepage' => ($request->on_homepage)?1:0,
            'name' => $request->name,
            'slug' => (empty($request->slug))?str_slug($request->name):str_slug($request->slug),
            'description' => $request->description,
            'seo_keywords' => $request->seo_keywords,
        ]);

        if($request->hasFile('image')){
            $medias = $category->getMedias("image");
            $category->uploadImage('image', 'shop/categories', $request->image);
            if($medias) {
                if (file_exists($medias['target_path'])) {
                    unlink($medias['target_path']);
                }
            }
        }
        if($request->hasFile('imageList')){
            $medias = $category->getMedias("imageList");
            $category->uploadImage('imageList', 'shop/categories', $request->imageList);
            if($medias) {
                if (file_exists($medias['target_path'])) {
                    unlink($medias['target_path']);
                }
            }
        }
        $category
            ->setTranslation('name', config('app.locale'), $request->name)
            ->setTranslation('slug', config('app.locale'), (empty($request->slug))?str_slug($request->name):str_slug($request->slug))
            ->setTranslation('description', config('app.locale'), $request->description)
            ->setTranslation('seo_title', config('app.locale'), $request->seo_title)
            ->setTranslation('seo_description', config('app.locale'), $request->seo_description)
            ->update();

        session()->flash('success', __("piclommerce::admin.shop_categories_edit"));
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
        $category = ShopCategory::where('uuid', $uuid)->FirstOrFail();
        $medias = $category->getMedias("image");
        if($medias)  {
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
        }
        $medias = $category->getMedias("image");
        if($medias)  {
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
        }
        $medias = $category->getMedias("imageList");
        if($medias)  {
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
        }
        ShopCategory::where('id', $category->id)->delete();

        session()->flash('success', __("piclommerce::admin.shop_categories_delete"));
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
        $category = ShopCategory::where('uuid', $uuid)->FirstOrFail();
        $medias = $category->getMedias("image");
        $updateMedia = [
            'target_path' => $medias['target_path'],
            'file_name' => $medias['file_name'],
            'file_type' => $medias['file_type'],
            'alt' => ($request->alt)?$request->alt:$medias['alt'],
            'description' => ($request->description)?$request->description:$medias['description'],
        ];
        ShopCategory::where('id', $category->id)->update([
            'image' => json_encode($updateMedia),
        ]);
        return response()->json(["message" => __("piclommerce::admin.medias_updated")]);
    }


    /**
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateImageList(Request $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $category = ShopCategory::where('uuid', $uuid)->FirstOrFail();
        $medias = $category->getMedias("imageList");
        $updateMedia = [
            'target_path' => $medias['target_path'],
            'file_name' => $medias['file_name'],
            'file_type' => $medias['file_type'],
            'alt' => ($request->alt)?$request->alt:$medias['alt'],
            'description' => ($request->description)?$request->description:$medias['description'],
        ];
        ShopCategory::where('id', $category->id)->update([
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

    public function imageListDelete(string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $category = ShopCategory::where('uuid', $uuid)->firstOrFail();
        $category->update([
            'imageList' => null
        ]);
        if(!empty($category->imageList) && file_exists($category->imageList)) {
            unlink($category->imageList);
        }

        session()->flash('success', __("piclommerce::admin.medias_delete"));
        return redirect()->route($this->route . 'edit',['uuid' => $category->uuid]);
    }

    /**
     * Traductions
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translate(Request $request)
    {
        return (new FormTranslate(ShopCategory::class))->formRequest($request);
    }


    public function positions(){

        $products = ShopCategory::OrderBy('order','asc')->get();
        $datas = [];
        foreach($products as $data){
            $datas[] = [
                'id' => $data->id,
                'name' => $data->translate('name', config('app.locale')),
                'order' => $data->order,
                'parent_id' => $data->parent_id,
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
        $datas = ShopCategory::all();
        $dataArray = [];
        foreach ($datas as $data) {
            $dataArray[$data->id] = [
                'parent_id' => $data->parent_id,
                'order' => $data->order
            ];
        }
        foreach ($request->orders as $key => $order) {
            if (!empty($order['id'])) {
                if ($dataArray[$order['id']]['order'] != $key) {
                    // Si la position est différente
                    ShopCategory::where('id', $order['id'])->update([
                        'order' => $key,
                        'parent_id' => $order['parent_id']
                    ]);
                } elseif ($dataArray[$order['id']]['parent_id'] != $order['parent_id']) {
                    // Si le parent est différent
                    ShopCategory::where('id', $order['id'])->update([
                        'order' => $key,
                        'parent_id' => $order['parent_id']
                    ]);
                }
            }
        }
        return __("piclommerce::admin.position_success");
    }


    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $categories = ShopCategory::select(['id','uuid','published','name','image','updated_at']);
        return DataTables::of($categories)
            ->addColumn('actions', function(ShopCategory $category) {
                return $this->getTableButtons($category->uuid);
            })
            ->editColumn("published",function(ShopCategory $category) use ($datatable) {
                return $datatable->yesOrNot($category->published);
            })
            ->editColumn("updated_at",function(ShopCategory $category) use ($datatable) {
                return $datatable->date($category->updated_at);
            })
            ->editColumn("image",function(ShopCategory $category) use ($datatable) {
                $medias = $category->getMedias("image");
                if ($medias) {
                    return $datatable->image(
                        $category->resizeImage("image", 30, 30)['target_path'],
                        $medias['target_path'],
                        $medias['alt']
                    );
                } else {
                    return "";
                }
            })
            ->editColumn("name",function(ShopCategory $category){
                return $category->translate("name");
            })

            ->rawColumns(['actions','published','image'])
            ->make(true);
    }

    /**
     * @return string
     */
    private function getTableButtons($uuid): string
    {
        $editRoute = route($this->getRoute() . "edit",['uuid' => $uuid]);
        $deleteRoute = route($this->getRoute() . "delete",['uuid' => $uuid]);
        $html = '<a href="'.$editRoute.'" class="table-button edit-button"><i class="fa fa-pencil"></i> '.__("piclommerce::admin.edit").'</a>';
        $html .= '<a href="'.$deleteRoute.'" class="table-button delete-button confirm-alert"><i class="fa fa-trash"></i> '.__("piclommerce::admin.delete").'</a>';
        return $html;
    }
}