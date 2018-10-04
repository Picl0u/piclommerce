<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\Content;
use Piclou\Piclommerce\Http\Entities\ContentCategory;
use Piclou\Piclommerce\Http\Requests\Admin\Contents;
use SEO;
use Yajra\DataTables\DataTables;

class ContentController extends Controller
{
    /**
     * @var string
     */
    private $viewPath = 'piclommerce::admin.contents.';

    /**
     * @var string
     */
    private $route = 'admin.pages.contents.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_contents"));
        return view($this->viewPath . 'index');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Content();
        $categories = ContentCategory::select('id','name')->get();
        SEO::setTitle(__("piclommerce::admin.navigation_contents") . " - " . __("piclommerce::admin.add"));
        return view($this->viewPath . 'create', compact('data', 'categories'));
    }


    /**
     * @param Contents $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Contents $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $content = Content::create([
            'published' => ($request->published)?1:0,
            'on_homepage' => ($request->on_homepage)?1:0,
            'on_menu' => ($request->on_menu)?1:0,
            'on_footer' => ($request->on_footer)?1:0,
            'content_category_id' => $request->content_category_id,
            'name' => $request->name,
            'slug' => (empty($request->slug))?str_slug($request->name):str_slug($request->slug),
            'summary' => $request->summary,
            'description' => $request->description,
            'seo_keywords' => $request->seo_keywords,
            'order' => (Content::count()+1)
        ]);

        if($request->hasFile('image')){
            $content->uploadImage('image', 'pages', $request->image);
        }

        $content
            ->setTranslation('name', config('app.locale'), $request->name)
            ->setTranslation('slug', config('app.locale'), (empty($request->slug))?str_slug($request->name):str_slug($request->slug))
            ->setTranslation('summary', config('app.locale'), $request->summary)
            ->setTranslation('description', config('app.locale'), $request->description)
            ->setTranslation('seo_title', config('app.locale'), $request->seo_title)
            ->setTranslation('seo_description', config('app.locale'), $request->seo_description)
            ->update();

        session()->flash('success', __("piclommerce::admin.content_pages_create"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Content::where('uuid', $uuid)->FirstOrFail();
        $categories = ContentCategory::select('id','name')->get();
        SEO::setTitle(__("piclommerce::admin.navigation_contents") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data','categories'));

    }

    /**
     * @param Contents $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Contents $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $content = Content::where('uuid', $uuid)->FirstOrFail();
        Content::where('id', $content->id)->update([
            'published' => ($request->published)?1:0,
            'on_homepage' => ($request->on_homepage)?1:0,
            'on_menu' => ($request->on_menu)?1:0,
            'on_footer' => ($request->on_footer)?1:0,
            'content_category_id' => $request->content_category_id,
            'name' => $request->name,
            'slug' => (empty($request->slug))?str_slug($request->name):str_slug($request->slug),
            'summary' => $request->summary,
            'description' => $request->description,
            'seo_keywords' => $request->seo_keywords,
        ]);

        if($request->hasFile('image')){
            $medias = $content->getMedias("image");
            $content->uploadImage('image', 'pages', $request->image);
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
        }
        $content
            ->setTranslation('name', config('app.locale'), $request->name)
            ->setTranslation('slug', config('app.locale'), (empty($request->slug))?str_slug($request->name):str_slug($request->slug))
            ->setTranslation('summary', config('app.locale'), $request->summary)
            ->setTranslation('description', config('app.locale'), $request->description)
            ->setTranslation('seo_title', config('app.locale'), $request->seo_title)
            ->setTranslation('seo_description', config('app.locale'), $request->seo_description)
            ->update();

        session()->flash('success', __("piclommerce::admin.content_pages_edit"));
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
        $content = Content::where('uuid', $uuid)->FirstOrFail();
        $medias = $content->getMedias("image");
        $updateMedia = [
            'target_path' => $medias['target_path'],
            'file_name' => $medias['file_name'],
            'file_type' => $medias['file_type'],
            'alt' => ($request->alt)?$request->alt:$medias['alt'],
            'description' => ($request->description)?$request->description:$medias['description'],
        ];
        Content::where('id', $content->id)->update([
            'image' => json_encode($updateMedia),
        ]);

        return response()->json(["message" => __("piclommerce::admin.medias_updated")]);
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
        $content = Content::where('uuid', $uuid)->FirstOrFail();
        $medias = $content->getMedias("image");
        if($medias)  {
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
        }
        Content::where('id', $content->id)->delete();

        session()->flash('success', __("piclommerce::admin.content_pages_delete"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * Traductions
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translate(Request $request)
    {
        return (new FormTranslate(Content::class))->formRequest($request);
    }


    public function positions(){

        $products = Content::OrderBy('order','asc')->get();
        $datas = [];
        foreach($products as $data){
            $datas[] = [
                'id' => $data->id,
                'name' => $data->translate('name', config('app.locale')),
                'order' => $data->order,
                'parent_id' => 0,
                'slug' => '',
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
        $datas = Content::all();
        $dataArray = [];
        foreach ($datas as $data) {
            $dataArray[$data->id] = [
                'order' => $data->order
            ];
        }
        foreach ($request->orders as $key => $order) {
            if (!empty($order['id'])) {
                if ($dataArray[$order['id']]['order'] != $key) {
                    Content::where('id', $order['id'])->update([
                        'order' => $key,
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
        $contents = Content::select(['id','uuid','published','name','image','content_category_id','updated_at']);
        return DataTables::of($contents)
        ->addColumn('actions', function(Content $content) {
            return $this->getTableButtons($content->uuid);
        })
        ->editColumn("published",function(Content $content) use ($datatable) {
            return $datatable->yesOrNot($content->published);
        })
        ->editColumn("updated_at",function(Content $content) use ($datatable) {
            return $datatable->date($content->updated_at);
        })
        ->editColumn("image",function(Content $content) use ($datatable) {
            $medias = $content->getMedias("image");
            if ($medias) {
                return $datatable->image(
                    $content->resizeImage("image", 30, 30)['target_path'],
                    $medias['target_path'],
                    $medias['alt']
                );
            } else {
                return "";
            }
        })
        ->editColumn("name",function(Content $content){
            return $content->translate("name");
        })
        ->editColumn('content_category_id',function(Content $content){
            if (!empty($content->content_category_id)) {
                if ($content->ContentCategory) {
                    return $content->ContentCategory->translate('name');
                } else {
                    return __("piclommerce::admin.nothing");
                }
            }
            return __("piclommerce::admin.nothing");
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