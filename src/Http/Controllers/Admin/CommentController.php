<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Http\Entities\Comment;
use Piclou\Piclommerce\Http\Entities\Product;
use Piclou\Piclommerce\Http\Entities\User;
use Yajra\DataTables\DataTables;
use SEO;

class CommentController extends Controller
{
    protected $viewPath = 'piclommerce::admin.shop.comments.';
    protected $route = 'admin.shop.comments.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_comments"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Comment();
        $users = User::select('id','firstname','lastname','email')->where('role', 'user')->orderBy('email','asc')->get();
        $products = Product::select('id','reference','name')->orderBy('name','asc')->get();

        SEO::setTitle(__("piclommerce::admin.navigation_comments") . " - " . __("piclommerce::admin.add")) ;

        return view($this->viewPath . 'create', compact('data','users','products'));
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
        $user = User::where('id', $request->user_id)->first();
        $product = Product::where('id', $request->product_id)->first();

        Comment::create([
            'published' => ($request->published)?1:0,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'comment' => $request->comment
        ]);

        session()->flash('success', __("piclommerce::admin.shop_comment_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Comment::where('uuid', $uuid)->firstOrFail();
        $users = User::select('id','firstname','lastname','email')->where('role', 'user')->orderBy('email','asc')->get();
        $products = Product::select('id','reference','name')->orderBy('name','asc')->get();

        SEO::setTitle(__("piclommerce::admin.navigation_comments") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);
        return view($this->viewPath . 'edit', compact('data','users','products'));
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
        $comment = Comment::where('uuid', $uuid)->firstOrFail();

        $user = User::where('id', $request->user_id)->first();
        $product = Product::where('id', $request->product_id)->first();

        Comment::where('id', $comment->id)->update([
            'published' => ($request->published)?1:0,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'comment' => $request->comment
        ]);

        session()->flash('success', __("piclommerce::admin.shop_comment_edit"));
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
        $comment = Comment::where('uuid', $uuid)->firstOrFail();
        Comment::where("id", $comment->id)->delete();

        session()->flash('success',__("piclommerce::admin.shop_comment_delete"));
        return redirect()->route($this->route . 'index');
    }



    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $comments = Comment::select(['id','published','uuid','product_id','firstname','lastname','updated_at']);
        return DataTables::of($comments)
            ->addColumn('actions', function(Comment $comment) {
                return $this->getTableButtons($comment->uuid);
            })
            ->editColumn("updated_at",function(Comment $comment) use ($datatable) {
                return $datatable->date($comment->updated_at);
            })
            ->editColumn("published",function(Comment $comment) use ($datatable) {
                return $datatable->yesOrNot($comment->published);
            })
            ->addColumn('fullname',function(Comment $comment){
                return $comment->firstname . ' ' . $comment->lastname;
            })
            ->editColumn('product_id',function(Comment $comment){
                $productLink = route('product.show',[
                    'slug' => $comment->Product->slug,
                    'id' => $comment->Product->id
                ]);

                return '<a href="' . $productLink . '" target="_blank">'.
                    $comment->Product->name . ' - ' . $comment->Product->reference.
                    '</a>';
            })
            ->rawColumns(['actions','published','product_id'])
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