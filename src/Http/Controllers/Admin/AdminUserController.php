<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Http\Entities\User;
use Piclou\Piclommerce\Http\Requests\Admin\UserRequest;
use Piclou\Piclommerce\Http\Requests\Admin\UserUpdateRequest;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use SEO;

class AdminUserController extends Controller
{
    protected $viewPath = 'piclommerce::admin.admin.';
    protected $route = 'admin.admin.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_administrators"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new User();
        $roles = Role::where('guard_name', '!=', config("piclommerce.superAdminRole"))->get();
        SEO::setTitle(__("piclommerce::admin.navigation_administrators") . " - " . __("piclommerce::admin.add")) ;
        return view($this->viewPath . 'create', compact('data','roles'));
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $role = Role::where('id',$request->role_id)->first();
        $create = [
            'online' => ($request->online)?1:0,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => str_slug($request->firstname."-".$request->lastname),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
            'gender' => $request->gender,
            'newsletter' => 0,
            'role_id' => $request->role_id,
            'guard_name' => $role->name
        ];
        $user = User::create($create);
        $user->assignRole($role->name);

        session()->flash('success', __("piclommerce::admin.admin_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = User::where('uuid', $uuid)->firstOrFail();
        $roles = Role::where('guard_name', '!=', 'super_admin')->get();

        SEO::setTitle(__("piclommerce::admin.navigation_administrators") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data', 'roles'));
    }

    /**
     * @param UserUpdateRequest $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserUpdateRequest $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $user = User::where('uuid', $uuid)->firstOrFail();
        $role = Role::where('id',$request->role_id)->first();

        $update = [
            'online' => ($request->online)?1:0,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => str_slug($request->firstname."-".$request->lastname),
            'email' => $request->email,
            'role' => 'admin',
            'gender' => $request->gender,
            'role_id' => $request->role_id,
            'guard_name' => $role->name
        ];

        if(!empty($request->password)) {
            $update['password'] = bcrypt($request->password);
        }
        User::where('id', $user->id)->update($update);
        $user->removeRole($role->guard_name);
        $user->assignRole($role->name);

        session()->flash('success', __("piclommerce::admin.admin_edit"));
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

        session()->flash('success',__("piclommerce::admin.admin_delete"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $users = User::select(['id','uuid','firstname','lastname','email','updated_at'])
            ->where('role','admin')
            ->where('guard_name', '!=', config("piclommerce.superAdminRole"));
        return DataTables::of($users)
            ->addColumn('actions', function(User $admin) {
                return $this->getTableButtons($admin->uuid);
            })
            ->editColumn("updated_at",function(User $admin) use ($datatable) {
                return $datatable->date($admin->updated_at);
            })
            ->rawColumns(['actions'])
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