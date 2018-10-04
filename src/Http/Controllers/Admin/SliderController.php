<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\Slider;
use Piclou\Piclommerce\Http\Requests\Admin\Sliders;
use Yajra\DataTables\DataTables;
use SEO;

class SliderController extends Controller
{
    protected $viewPath = 'piclommerce::admin.slider.';
    protected $route = 'admin.sliders.';

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTable();
        }
        SEO::setTitle(__("piclommerce::admin.navigation_slider"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Slider();
        SEO::setTitle(__("piclommerce::admin.navigation_slider") . " - " . __("piclommerce::admin.add")) ;
        return view($this->viewPath . 'create', compact('data'));
    }

    /**
     * @param Sliders $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Sliders $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $create = [
            'published' => ($request->published)?1:0,
            'link' => $request->link,
            'name' => $request->name,
            'description' => $request->description,
            'position' => $request->position,
            'order' => (Slider::count()+1)
        ];
        $slider = Slider::create($create);
        if($request->hasFile('image')){
            $slider->uploadImage('image', 'sliders', $request->image);
        }
        $slider->setTranslation('name', config('app.locale'), $request->name)
               ->setTranslation('description', config('app.locale'), $request->description)
               ->update();

        session()->flash('success', __("piclommerce::admin.slider_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Slider::where('uuid', $uuid)->FirstOrFail();

        SEO::setTitle(__("piclommerce::admin.navigation_slider") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data'));
    }

    /**
     * @param Sliders $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Sliders $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $slider = Slider::where('uuid', $uuid)->FirstOrFail();

        Slider::where('id', $slider->id)->update([
            'published' => ($request->published)?1:0,
            'link' => $request->link,
            'position' => $request->position,
        ]);

        if($request->hasFile('image')){
            $medias = $slider->getMedias("image");
            $slider->uploadImage('image', 'sliders', $request->image);
            if(file_exists($medias['target_path'])) {
                unlink($medias['target_path']);
            }
        }
        $slider
            ->setTranslation('name', config('app.locale'), $request->name)
            ->setTranslation('description', config('app.locale'), $request->description)
            ->update();

        session()->flash('success', __("piclommerce::admin.slider_edit"));
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
        $slider = Slider::where('uuid', $uuid)->FirstOrFail();
        $slider->deleteFile('image');
        Slider::where('id', $slider->id)->delete();

        session()->flash('success',__("piclommerce::admin.slider_delete"));
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
        $slider = Slider::where('uuid', $uuid)->FirstOrFail();
        $medias = $slider->getMedias("image");
        $updateMedia = [
            'target_path' => $medias['target_path'],
            'file_name' => $medias['file_name'],
            'file_type' => $medias['file_type'],
            'alt' => ($request->alt)?$request->alt:$medias['alt'],
            'description' => ($request->description)?$request->description:$medias['description'],
        ];
        Slider::where('id', $slider->id)->update([
            'image' => json_encode($updateMedia),
        ]);

        return response()->json(["message" => __("piclommerce::admin.medias_updated")]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function positions(){
        $sliders = Slider::OrderBy('order','asc')->get();
        $datas = [];
        foreach($sliders as $slider){
            $datas[] = [
                'id' => $slider->id,
                'name' => $slider->name,
                'order' => $slider->order,
                'parent_id' => 0,
                'slug' => '',
            ];
        }
        SEO::setTitle(__("piclommerce::admin.navigation_slider") . " - " . __("piclommerce::admin.position"));
        return view($this->viewPath . 'positions', compact('datas'));
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\RedirectResponse|null|string
     */
    public function positionsStore(Request $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $datas = Slider::all();
        $dataArray = [];
        foreach ($datas as $data) {
            $dataArray[$data->id] = [
                'order' => $data->order
            ];
        }
        foreach ($request->orders as $key => $order) {
            if (!empty($order['id'])) {
                if ($dataArray[$order['id']]['order'] != $key) {
                    Slider::where('id', $order['id'])->update([
                        'order' => $key,
                    ]);
                }
            }
        }
        return __("piclommerce::admin.position_success");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translate(Request $request)
    {
        return (new FormTranslate(Slider::class))->formRequest($request);
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $sliders = Slider::select('id','published','uuid','image','name','updated_at');
        return DataTables::of($sliders)
            ->addColumn('actions', function(Slider $slider) {
                return $this->getTableButtons($slider->uuid);
            })
            ->editColumn("published",function(Slider $slider) use ($datatable) {
                return $datatable->yesOrNot($slider->published);
            })
            ->editColumn("updated_at",function(Slider $slider) use ($datatable) {
                return $datatable->date($slider->updated_at);
            })
            ->editColumn("image",function(Slider $slider) use ($datatable) {
                $medias = $slider->getMedias("image");
                if ($medias) {
                    return $datatable->image(
                        $slider->resizeImage("image", 30, 30)['target_path'],
                        $medias['target_path'],
                        $medias['alt']
                    );
                } else {
                    return "";
                }
            })
            ->editColumn("name",function(Slider $slider){
                return $slider->translate("name");
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