<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Http\Entities\Banner;
use Piclou\Piclommerce\Http\Requests\Admin\Banners;
use SEO;
use Yajra\DataTables\DataTables;

class BannerController extends Controller
{
    /**
     * @var string
     */
    private $viewPath = 'piclommerce::admin.banner.';

    /**
     * @var string
     */
    private $route = 'admin.banner.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_banner"));
        return view($this->viewPath . 'index');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Banner();
        SEO::setTitle(__("piclommerce::admin.navigation_banner") . " - " . __("piclommerce::admin.add"));
        return view($this->viewPath . 'create', compact('data'));
    }


    /**
     * @param Banners $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Banners $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $banner = Banner::create([
            'published' => ($request->published)?1:0,
            'name' => $request->name,
            'link' => $request->link,
            'order' => (Banner::count()+1)
        ]);
        if($request->hasFile('image')){
            $banner->uploadImage('image', 'banners', $request->image);
            $banner->update();
        }

        session()->flash('success', __("piclommerce::admin.banner_create"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Banner::where('uuid', $uuid)->FirstOrFail();
        SEO::setTitle(__("piclommerce::admin.navigation_banner") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data'));

    }

    /**
     * @param Banners $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Banners $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $banner = Banner::where('uuid', $uuid)->FirstOrFail();
        Banner::where('id', $banner->id)->update([
            'published' => ($request->published)?1:0,
            'name' => $request->name,
            'link' => $request->link,
        ]);

        if($request->hasFile('image')){
            $medias = $banner->getMedias("image");
            $banner->uploadImage('image', 'banners', $request->image);
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
            $banner->update();
        }

        session()->flash('success', __("piclommerce::admin.banner_edit"));
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
        $banner = Banner::where('uuid', $uuid)->FirstOrFail();
        $medias = $banner->getMedias("image");
        $updateMedia = [
            'target_path' => $medias['target_path'],
            'file_name' => $medias['file_name'],
            'file_type' => $medias['file_type'],
            'alt' => ($request->alt)?$request->alt:$medias['alt'],
            'description' => ($request->description)?$request->description:$medias['description'],
        ];
        Banner::where('id', $banner->id)->update([
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
        $banner = Banner::where('uuid', $uuid)->FirstOrFail();
        $medias = $banner->getMedias("image");
        if($medias)  {
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
        }
        Banner::where('id', $banner->id)->delete();

        session()->flash('success', __("piclommerce::admin.banner_delete"));
        return redirect()->route($this->route . 'index');
    }

    public function positions(){

        $banners = Banner::OrderBy('order','asc')->get();
        $datas = [];
        foreach($banners as $data){
            $datas[] = [
                'id' => $data->id,
                'name' => $data->name,
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
        $datas = Banner::all();
        $dataArray = [];
        foreach ($datas as $data) {
            $dataArray[$data->id] = [
                'order' => $data->order
            ];
        }
        foreach ($request->orders as $key => $order) {
            if (!empty($order['id'])) {
                if ($dataArray[$order['id']]['order'] != $key) {
                    Banner::where('id', $order['id'])->update([
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
        $banners = Banner::select(['id','uuid','published','name','image','updated_at']);
        return DataTables::of($banners)
        ->addColumn('actions', function(Banner $banner) {
            return $this->getTableButtons($banner->uuid);
        })
        ->editColumn("published",function(Banner $banner) use ($datatable) {
            return $datatable->yesOrNot($banner->published);
        })
        ->editColumn("updated_at",function(Banner $banner) use ($datatable) {
            return $datatable->date($banner->updated_at);
        })
        ->editColumn("image",function(Banner $banner) use ($datatable) {
            $medias = $banner->getMedias("image");
            if ($medias) {
                return $datatable->image(
                    resizeImage($medias['target_path'], 30, 30),
                    $medias['target_path'],
                    $medias['alt']
                );
            } else {
                return "";
            }
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