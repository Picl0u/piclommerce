<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\Status;
use Piclou\Piclommerce\Http\Requests\Admin\Status as StatusRequest;
use Yajra\DataTables\DataTables;
use SEO;

class StatusController extends Controller
{
    protected $viewPath = 'piclommerce::admin.order.status.';
    protected $route = 'admin.order.status.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_status"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Status();
        SEO::setTitle(__("piclommerce::admin.navigation_status") . " - " . __("piclommerce::admin.add")) ;
        return view($this->viewPath . 'create', compact('data'));
    }

    /**
     * @param StatusRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StatusRequest $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $create = [
            'color' => $request->color,
            'name' => $request->name,
            'order_accept' => ($request->order_accept)?1:0,
            'order_refuse' => ($request->order_refuse)?1:0,
        ];
        $status = Status::create($create);

        $status->setTranslation('name', config('app.locale'), $request->name)->update();

        session()->flash('success', __("piclommerce::admin.order_status_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Status::where('uuid', $uuid)->FirstOrFail();

        SEO::setTitle(__("piclommerce::admin.navigation_status") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data'));
    }

    /**
     * @param StatusRequest $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StatusRequest $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $status = Status::where('uuid', $uuid)->FirstOrFail();

        Status::where('id', $status->id)->update([
            'color' => $request->color,
            'name' => $request->name,
            'order_accept' => ($request->order_accept)?1:0,
            'order_refuse' => ($request->order_refuse)?1:0,
        ]);

        $status->setTranslation('name', config('app.locale'), $request->name)->update();

        session()->flash('success', __("piclommerce::admin.order_status_edit"));
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
        $status = Status::where('uuid', $uuid)->FirstOrFail();

        Status::where('id', $status->id)->delete();

        session()->flash('success',__("piclommerce::admin.order_status_delete"));
        return redirect()->route($this->route . 'index');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translate(Request $request)
    {
        return (new FormTranslate(Status::class))->formRequest($request);
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $status = Status::select('id','uuid','name','updated_at');
        return DataTables::of($status)
            ->addColumn('actions', function(Status $statut) {
                return $this->getTableButtons($statut->uuid);
            })
            ->editColumn("updated_at",function(Status $statut) use ($datatable) {
                return $datatable->date($statut->updated_at);
            })
            ->editColumn("name",function(Status $statut){
                return $statut->translate("name");
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