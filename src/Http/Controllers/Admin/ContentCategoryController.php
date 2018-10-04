<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\ContentCategory;
use Piclou\Piclommerce\Http\Requests\Admin\ContentCategories;
use Yajra\DataTables\DataTables;
use SEO;

class ContentCategoryController extends Controller
{

    /**
     * @var string
     */
    protected $viewPath = 'piclommerce::admin.contentCategories.';

    /**
     * @var string
     */
    protected $route = 'admin.pages.categories.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_sections"));
        return view($this->viewPath . 'index');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new ContentCategory();
        SEO::setTitle(__("piclommerce::admin.navigation_sections") . " - " . __("piclommerce::admin.add"));
        return view($this->viewPath . 'create', compact('data'));
    }


    /**
     * @param ContentCategories $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ContentCategories $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $category = ContentCategory::create([
            'on_footer' => ($request->on_footer)?1:0,
            'name' => $request->name
        ]);
        $category
            ->setTranslation('name', config('app.locale'), $request->name)
            ->update();

        session()->flash('success', __("piclommerce::admin.content_categories_create"));
        return redirect()->route($this->route . 'index');
    }
    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = ContentCategory::where('uuid', $uuid)->FirstOrFail();
        SEO::setTitle(__("piclommerce::admin.navigation_sections") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data'));
    }
    /**
     * @param ContentCategories $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ContentCategories $request, string $uuid)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'index');
        }
        $category = ContentCategory::where('uuid', $uuid)->FirstOrFail();
        ContentCategory::where('id', $category->id)->update([
            'on_footer' => ($request->on_footer)?1:0
        ]);
        $category
            ->setTranslation('name', config('app.locale'), $request->name)
            ->update();

        session()->flash('success', __("piclommerce::admin.content_categories_edit"));
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
        $category = ContentCategory::where('uuid', $uuid)->FirstOrFail();
        ContentCategory::where('id', $category->id)->delete();
        session()->flash('success', __("piclommerce::admin.content_categories_delete"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * Traductions
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translate(Request $request)
    {
        return (new FormTranslate(ContentCategory::class))->formRequest($request);
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $categories = ContentCategory::select('id','on_footer','uuid','name','updated_at');
        return DataTables::of($categories)
            ->addColumn('actions', function(ContentCategory $category) {
                return $this->getTableButtons($category->uuid);
            })
            ->editColumn("on_footer",function(ContentCategory $category) use ($datatable) {
                return $datatable->yesOrNot($category->on_footer);
            })
            ->editColumn("updated_at",function(ContentCategory $category) use ($datatable) {
                return $datatable->date($category->updated_at);
            })
            ->editColumn("name",function(ContentCategory $category){
                return $category->translate("name");
            })
            ->rawColumns(['actions','on_footer'])
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
