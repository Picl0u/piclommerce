<?php
namespace Piclou\Piclommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\Slider;
use Piclou\Piclommerce\Http\Entities\User;
use Piclou\Piclommerce\Http\Requests\Admin\Sliders;
use Yajra\DataTables\DataTables;
use SEO;

class UserController extends Controller
{
    protected $viewPath = 'piclommerce::admin.users.';
    protected $route = 'admin.users.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_customers"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new User();
        SEO::setTitle(__("piclommerce::admin.navigation_customers") . " - " . __("piclommerce::admin.add")) ;
        return view($this->viewPath . 'create', compact('data'));
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
            'online' => ($request->online)?1:0,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => str_slug($request->firstname."-".$request->lastname),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user',
            'gender' => $request->gender,
            'newsletter' => ($request->newsletter)?1:0,
        ]);

        session()->flash('success', __("piclommerce::admin.user_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = User::where('uuid', $uuid)->firstOrFail();

        SEO::setTitle(__("piclommerce::admin.navigation_customers") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data'));
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
        $user = User::where('uuid', $uuid)->firstOrFail();
        $update = [
            'online' => ($request->online)?1:0,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => str_slug($request->firstname."-".$request->lastname),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user',
            'gender' => $request->gender,
            'newsletter' => ($request->newsletter)?1:0,
        ];
        if(!empty($request->password)) {
            $update['password'] = bcrypt($this->password);
        }
        User::where('id', $user->id)->update($update);

        session()->flash('success', __("piclommerce::admin.user_edit"));
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
        $user = User::where('uuid', $uuid)->firstOrFail();
        User::where('id', $user->id)->delete();

        session()->flash('success',__("piclommerce::admin.user_delete"));
        return redirect()->route($this->route . 'index');
    }


    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $users = User::select(['id','uuid','firstname','lastname','email','updated_at'])->where('role','user');
        return DataTables::of($users)
            ->addColumn('orders',function(User $user){
                return '<span class="label success">' . count($user->Orders) . '</span>';
            })
            ->addColumn('actions', function(User $user) {
                return $this->getTableButtons($user->uuid);
            })
            ->editColumn("email",function(User $user) use ($datatable) {
                return $datatable->email($user->email);
            })
            ->editColumn("updated_at",function(User $user) use ($datatable) {
                return $datatable->date($user->updated_at);
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