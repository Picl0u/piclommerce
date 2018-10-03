<?php
namespace Piclou\Piclommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Http\Entities\Carriers;
use Piclou\Piclommerce\Http\Entities\CarriersPrices;
use Piclou\Piclommerce\Http\Entities\Countries;
use Yajra\DataTables\DataTables;
use SEO;

class CarrierController extends Controller
{
    protected $viewPath = 'piclommerce::admin.order.carriers.';
    protected $route = 'admin.order.carriers.';

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
        $countDefault = Carriers::where('default',1)->count();
        SEO::setTitle(__("piclommerce::admin.navigation_carriers"));
        return view($this->viewPath . 'index', compact('countDefault'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Carriers();
        $countries = Countries::where('activated', 1)->orderBy('name','ASC')->get();
        SEO::setTitle(__("piclommerce::admin.navigation_carriers") . " - " . __("piclommerce::admin.add")) ;
        return view($this->viewPath . 'create', compact('data', 'countries'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $weight = 0;
        $price = 0;
        if($request->type_shipping == 'price'){
            $price = 1;
        }else{
            $weight = 1;
        }
        $create = [
            'published' => ($request->published)?1:0,
            'default' => ($request->default)?1:0,
            'free' => ($request->free)?1:0,
            'price' => $price,
            'weight' => $weight,
            'name' => $request->name,
            'delay' => $request->delay,
            'url' => $request->url,
            'max_weight' => $request->max_weight,
            'max_width' => $request->max_width,
            'max_height' => $request->max_height,
            'max_length' => $request->max_length,
            'default_price' => $request->default_price,
        ];
        $carrier = Carriers::create($create);
        if($request->hasFile('image')){
            $carrier->uploadImage('image', 'carriers', $request->image)->update();
        }

        if(isset($request->availableCountry) && !empty($request->availableCountry)) {
            foreach ($request->availableCountry as $key => $available) {
                if (is_null($request->priceMax[$key])) {
                    $priceMax = 0;
                } else {
                    $priceMax = $request->priceMax[$key];
                }
                if (is_null($request->priceMin[$key])) {
                    $priceMin = 0;
                } else {
                    $priceMin = $request->priceMin[$key];
                }
                foreach ($available as $country => $value) {
                    $insert = [
                        'carriers_id' => $carrier->id,
                        'price' => (is_null($request->countries[$key][$country])) ? 0 : $request->countries[$key][$country],
                        'country_id' => $country,
                        'price_min' => $priceMin,
                        'price_max' => $priceMax,
                        'key' => $key
                    ];

                    CarriersPrices::create($insert);
                }

            }
        }

        session()->flash('success', __("piclommerce::admin.order_carriers_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Carriers::where('uuid', $uuid)->FirstOrFail();
        $countries = Countries::where('activated', 1)->orderBy('name','ASC')->get();

        SEO::setTitle(__("piclommerce::admin.navigation_carriers") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data','countries'));
    }

    /**
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $carrier = Carriers::where('uuid', $uuid)->firstOrFail();
        $weight = 0;
        $price = 0;
        if($request->type_shipping == 'price'){
            $price = 1;
        }else{
            $weight = 1;
        }
        Carriers::where('id', $carrier->id)->update([
            'published' => ($request->published)?1:0,
            'default' => ($request->default)?1:0,
            'free' => ($request->free)?1:0,
            'price' => $price,
            'weight' => $weight,
            'name' => $request->name,
            'delay' => $request->delay,
            'url' => $request->url,
            'max_weight' => $request->max_weight,
            'max_width' => $request->max_width,
            'max_height' => $request->max_height,
            'max_length' => $request->max_length,
            'default_price' => $request->default_price,
        ]);
        if($request->hasFile('image')){
            $carrier->uploadImage('image', 'carriers', $request->image)->update();
        }

        CarriersPrices::where('carriers_id', $carrier->id)->delete();

        if(isset($request->availableCountry) && !empty($request->availableCountry)) {
            foreach ($request->availableCountry as $key => $available) {
                if (is_null($request->priceMax[$key])) {
                    $priceMax = 0;
                } else {
                    $priceMax = $request->priceMax[$key];
                    if (!is_numeric($priceMax)) {
                        $priceMax = 0;
                    }
                }
                if (is_null($request->priceMin[$key])) {
                    $priceMin = 0;
                } else {
                    $priceMin = $request->priceMin[$key];
                }
                foreach ($available as $country => $value) {
                    $insert = [
                        'carriers_id' => $carrier->id,
                        'price' => (is_null($request->countries[$key][$country])) ? 0 : $request->countries[$key][$country],
                        'country_id' => $country,
                        'price_min' => $priceMin,
                        'price_max' => $priceMax,
                        'key' => $key
                    ];

                    CarriersPrices::create($insert);
                }
            }
        }

        session()->flash('success', __("piclommerce::admin.order_carriers_edit"));
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
        $carriers = Carriers::where('uuid', $uuid)->FirstOrFail();

        Carriers::where('id', $carriers->id)->delete();
        if(!empty($carriers->image)) {
            unlink($carriers->image);
        }

        session()->flash('success',__("piclommerce::admin.order_carriers_delete"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $carriers = Carriers::select(['id','uuid','default','published','name','image','delay','updated_at']);
        return DataTables::of($carriers)
            ->addColumn('actions', function(Carriers $carrier) {
                return $this->getTableButtons($carrier->uuid);
            })
            ->editColumn("default",function(Carriers $carrier) use ($datatable) {
                return $datatable->yesOrNot($carrier->default);
            })
            ->editColumn("published",function(Carriers $carrier) use ($datatable) {
                return $datatable->yesOrNot($carrier->published);
            })
            ->editColumn("updated_at",function(Carriers $carrier) use ($datatable) {
                return $datatable->date($carrier->updated_at);
            })
            ->editColumn("image",function(Carriers $carrier) use ($datatable) {
                $medias = $carrier->getMedias("image");
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
            ->rawColumns(['actions','published','default','image'])
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