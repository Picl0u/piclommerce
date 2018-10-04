<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Http\Entities\Newsletters;
use Yajra\DataTables\DataTables;
use SEO;

class NewsletterController extends Controller
{
    protected $viewPath = 'piclommerce::admin.newsletters.';
    protected $route = 'admin.newsletter.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_newsletter"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Newsletters();

        SEO::setTitle(__("piclommerce::admin.navigation_newsletter") . " - " . __("piclommerce::admin.add")) ;

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

        Newsletters::create([
            'active' => ($request->active)?1:0,
            'email' => $request->email,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
        ]);

        session()->flash('success', __("piclommerce::admin.newsletter_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Newsletters::where('uuid', $uuid)->FirstOrFail();

        SEO::setTitle(__("piclommerce::admin.navigation_newsletter") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
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
        $newsletter = Newsletters::where('uuid', $uuid)->firstOrFail();

        Newsletters::where('id', $newsletter->id)->update([
            'active' => ($request->active)?1:0,
            'email' => $request->email,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
        ]);

        session()->flash('success', __("piclommerce::admin.newsletter_edit"));
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
        $newsletter = Newsletters::where('uuid', $uuid)->firstOrFail();
        Newsletters::where("id", $newsletter->id)->delete();

        session()->flash('success',__("piclommerce::admin.newsletter_delete"));
        return redirect()->route($this->route . 'index');
    }

    public function export()
    {
        $newsletters = Newsletters::where('active', 1)
            ->select('firstname','lastname','email')
            ->orderBy('id','desc')
            ->get()
            ->toArray();

        return Excel::create('export-newsletter'.now(), function($excel) use ($newsletters) {
            $excel->setTitle('Export Newsletter'.date('d/m/Y à H:i'));

            $excel->sheet('Sheetname', function($sheet) use ($newsletters) {
                $sheet->fromArray($newsletters);
            });
        })->download('csv');
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $newsletters = Newsletters::select(['id','uuid','active','firstname','lastname','email','updated_at']);
        return DataTables::of($newsletters)
            ->addColumn('actions', function(Newsletters $newsletter) {
                return $this->getTableButtons($newsletter->uuid);
            })
            ->editColumn("updated_at",function(Newsletters $newsletter) use ($datatable) {
                return $datatable->date($newsletter->updated_at);
            })
            ->editColumn("active",function(Newsletters $newsletter) use ($datatable) {
                return $datatable->yesOrNot($newsletter->active);
            })
            ->rawColumns(['actions','active'])
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