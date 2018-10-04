<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\Countries;
use Piclou\Piclommerce\Http\Entities\Slider;
use Piclou\Piclommerce\Http\Entities\User;
use Piclou\Piclommerce\Http\Entities\UsersAdresses;
use Piclou\Piclommerce\Http\Requests\Admin\Sliders;
use Yajra\DataTables\DataTables;
use SEO;

class UserAddressController extends Controller
{
    protected $viewPath = 'piclommerce::admin.addresses.';
    protected $route = 'admin.addresses.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_addresses"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new UsersAdresses();
        $users = User::where('role','user')->get();
        $countries = Countries::where('activated', 1)->get();
        SEO::setTitle(__("piclommerce::admin.navigation_addresses") . " - " . __("piclommerce::admin.add"));
        return view($this->viewPath . 'create', compact('data', 'users', 'countries'));
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
        User::create([
            'user_id' => $request->user_id,
            'gender' => $request->gender,
            'billing' => ($request->billing)?1:0,
            'delivery' => 1,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'address' => $request->address,
            'additional_address' => $request->additional_address,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'phone' => $request->phone,
            'country_id' => $request->country_id
        ]);

        session()->flash('success', __("piclommerce::admin.addresses_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = UsersAdresses::where('uuid', $uuid)->firstOrFail();
        $users = User::where('role','user')->get();
        $countries = Countries::where('activated', 1)->get();

        SEO::setTitle(__("piclommerce::admin.navigation_addresses") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data', 'users', 'countries'));
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
        $address = UsersAdresses::where('uuid', $uuid)->firstOrFail();
        $update = [
            'user_id' => $request->user_id,
            'gender' => $request->gender,
            'billing' => ($request->billing)?1:0,
            'delivery' => 1,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'address' => $request->address,
            'additional_address' => $request->additional_address,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'phone' => $request->phone,
            'country_id' => $request->country_id
        ];
        UsersAdresses::where('id', $address->id)->update($update);

        session()->flash('success', __("piclommerce::admin.addresses_edit"));
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

        $address = UsersAdresses::where('uuid', $uuid)->firstOrFail();
        UsersAdresses::where('id', $address->id)->delete();

        session()->flash('success',__("piclommerce::admin.addresses_delete"));
        return redirect()->route($this->route . 'index');
    }


    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $addresses = UsersAdresses::select([
            'id',
            'uuid',
            'firstname',
            'lastname',
            'phone',
            'address',
            'additional_address',
            'zip_code',
            'city',
            'updated_at'
        ]);
        return DataTables::of($addresses)
            ->editColumn('address', function(UsersAdresses $address){
                return $address->address.' '.$address->additional_address.' - '.$address->zip_code.' '.$address->city;
            })
            ->addColumn('actions', function(UsersAdresses $address) {
                return $this->getTableButtons($address->uuid);
            })
            ->editColumn("updated_at",function(UsersAdresses $address) use ($datatable) {
                return $datatable->date($address->updated_at);
            })
            ->rawColumns(['actions','orders','email'])
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