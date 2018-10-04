<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Http\Entities\Product;
use Piclou\Piclommerce\Http\Entities\Vat;
use Piclou\Piclommerce\Http\Requests\Admin\Vats;
use SEO;
use Yajra\DataTables\DataTables;

class VatController extends Controller
{
    /**
     * @var string
     */
    private $viewPath = 'piclommerce::admin.shop.vats.';

    /**
     * @var string
     */
    private $route = 'admin.shop.vats.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_vat"));
        return view($this->viewPath . 'index');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Vat();
        SEO::setTitle(__("piclommerce::admin.navigation_vat") . " - " . __("piclommerce::admin.add"));
        return view($this->viewPath . 'create', compact('data'));
    }


    /**
     * @param Vats $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Vats $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        Vat::create([
            'name' => $request->name,
            'percent' => $request->percent,
        ]);

        session()->flash('success', __("piclommerce::admin.shop_vat_create"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Vat::where('uuid', $uuid)->FirstOrFail();
        SEO::setTitle(__("piclommerce::admin.navigation_categories") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data'));

    }

    /**
     * @param Vats $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Vats $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $vat = Vat::where('uuid', $uuid)->FirstOrFail();
        Vat::where('id', $vat->id)->update([
            'name' => $request->name,
            'percent' => $request->percent,
        ]);

        if($vat->percent != $request->percent) {
            $products = Product::where("vat_id", $vat->id)->get();
            foreach($products as $product){
                $price_ht = $product->price_ht;
                $price_ttc = $price_ht*(1+($request->percent/100));
                Product::where("id", $product->id)->update([
                    'price_ttc' => $price_ttc
                ]);
            }
        }

        session()->flash('success', __("piclommerce::admin.shop_vat_edit"));
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
        $vat = Vat::where('uuid', $uuid)->FirstOrFail();

        Vat::where('id', $vat->id)->delete();

        session()->flash('success', __("piclommerce::admin.shop_categories_delete"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $vats = Vat::select(['id','uuid','name','percent','updated_at']);
        return DataTables::of($vats)
            ->addColumn('actions', function(Vat $vat) {
                return $this->getTableButtons($vat->uuid);
            })
            ->editColumn("updated_at",function(Vat $vat) use ($datatable) {
                return $datatable->date($vat->updated_at);
            })
            ->editColumn("percent",function(Vat $vat){
                return $vat->percent . "%";
            })
            ->rawColumns(['actions','percent'])
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