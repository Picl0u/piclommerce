<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Http\Entities\Countries;
use Yajra\DataTables\DataTables;
use SEO;

class CountriesController extends Controller
{
    protected $viewPath = 'piclommerce::admin.order.countries.';
    protected $route = 'admin.order.countries.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_countries"));
        return view($this->viewPath . 'index');
    }

    public function activate(int $id)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $country = Countries::where('id', $id)->firstOrFail();
        Countries::where('id', $country->id)->update([
            "activated" => 1
        ]);
        session()->flash('success',__("piclommerce::admin.order_countries_activate_success"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function desactivate(int $id)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }

        $country = Countries::where('id', $id)->firstOrFail();
        Countries::where('id', $country->id)->update([
            "activated" => 0
        ]);
        session()->flash('success',__("piclommerce::admin.order_countries_desactivate_success"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $countries = Countries::select(['id','activated','name','iso_3166_2','currency_symbol']);
        return DataTables::of($countries)
            ->addColumn('actions', function(Countries $country) {
                return $this->getTableButtons($country);
            })
            ->editColumn("activated",function(Countries $country) use ($datatable) {
                return $datatable->yesOrNot($country->activated);
            })
            ->rawColumns(['actions', 'activated'])
            ->make(true);
    }

    /**
     * @return string
     */
    private function getTableButtons($country): string
    {
        if(empty($country->activated)) {
            $html = '<a href="'.route($this->route . 'activate', ['id' => $country->id]) .'"  
                        class="table-button edit-button"
                     >
                        <i class="fa fa-check"></i> '.__("piclommerce::admin.order_countries_activate").'
                    </a>';
        }else {
            $html = '<a href="'.route($this->route . 'desactivate', ['id' => $country->id]) .'" 
                        class="table-button delete-button"
                    >
                        <i class="fa fa-times"></i> '.__("piclommerce::admin.order_countries_desactivate").'
                    </a>';
        }
        return $html;
    }
}