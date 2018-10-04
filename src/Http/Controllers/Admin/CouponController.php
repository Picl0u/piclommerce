<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Helpers\Translatable\FormTranslate;
use Piclou\Piclommerce\Http\Entities\Coupon;
use Piclou\Piclommerce\Http\Entities\CouponProduct;
use Piclou\Piclommerce\Http\Entities\CouponUser;
use Piclou\Piclommerce\Http\Entities\Product;
use Piclou\Piclommerce\Http\Entities\Slider;
use Piclou\Piclommerce\Http\Entities\User;
use Piclou\Piclommerce\Http\Requests\Admin\Sliders;
use Yajra\DataTables\DataTables;
use SEO;

class CouponController extends Controller
{
    protected $viewPath = 'piclommerce::admin.coupon.';
    protected $route = 'admin.coupon.';

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
        SEO::setTitle(__("piclommerce::admin.navigation_promotional"));
        return view($this->viewPath . 'index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = new Coupon();
        $users = User::where('role','user')->orderBy('email', 'ASC')->get();
        $products = Product::orderBy('name','ASC')->get();
        SEO::setTitle(__("piclommerce::admin.navigation_promotional") . " - " . __("piclommerce::admin.add"));
        return view($this->viewPath . 'create', compact('data', 'users', 'products'));
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
        $coupon = Coupon::create([
            'name' =>  $request->name,
            'coupon' => $request->coupon,
            'percent' => $request->percent,
            'price' => $request->price,
            'use_max' => $request->use_max,
            'amount_min' => $request->amount_min,
            'begin' => $request->begin,
            'end' => $request->end
        ]);

        if (!empty($request->users)) {
            $insertUser = [];
            foreach ($request->users as $user) {
                $insertUser[] = [
                    'coupon_id' => $coupon->id,
                    'user_id' => $user,
                ];
            }
            $coupon->couponUsers()->createMany($insertUser);
        }
        if (!empty($request->products)) {
            $insertProduct = [];
            foreach ($request->products as $product) {
                $insertProduct[] = [
                    'coupon_id' => $coupon->id,
                    'product_id' => $product,
                ];
            }
            $coupon->couponProducts()->createMany($insertProduct);
        }

        session()->flash('success', __("piclommerce::admin.coupon_create"));
        return redirect()->route($this->route . 'index');

    }

    /**
     * @param string $uuid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $uuid)
    {
        $data = Coupon::where('uuid', $uuid)->FirstOrFail();
        $users = User::where('role','user')->orderBy('email', 'ASC')->get();
        $products = Product::orderBy('name','ASC')->get();

        SEO::setTitle(__("piclommerce::admin.navigation_promotional") . " - " . __("piclommerce::admin.edit") . " : " . $data->name);

        return view($this->viewPath . 'edit', compact('data','users', 'products'));
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
        $coupon = Coupon::where('uuid', $uuid)->FirstOrFail();

        Coupon::where('id', $coupon->id)->update([
            'name' =>  $request->name,
            'coupon' => $request->coupon,
            'percent' => $request->percent,
            'price' => $request->price,
            'use_max' => $request->use_max,
            'amount_min' => $request->amount_min,
            'begin' => $request->begin,
            'end' => $request->end
        ]);

        $couponsUser = $coupon->CouponUsers;
        CouponUser::where('coupon_id', $coupon->id)->delete();
        if (!empty($request->users)) {
            $usersInsert = [];
            foreach ($request->users as $key => $user) {
                $usersInsert[$key] = [
                    'coupon_id' => $coupon->id,
                    'user_id' => $user
                ];
                foreach ($couponsUser as $cu) {
                    if ($cu->coupon_id == $coupon->id && $cu->user_id == $user) {
                        $usersInsert[$key]['use'] =  $cu->use;
                    }
                }
            }
            $coupon->couponUsers()->createMany($usersInsert);
        }

        CouponProduct::where('coupon_id', $coupon->id)->delete();
        if (!empty($request->products)) {
            $productsInsert = [];
            foreach ($request->products as $key => $product) {
                $productsInsert[$key] = [
                    'coupon_id' => $coupon->id,
                    'product_id' => $product
                ];
            }
            $coupon->couponProducts()->createMany($productsInsert);
        }

        session()->flash('success', __("piclommerce::admin.coupon_edit"));
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
        $coupon = Coupon::where('uuid', $uuid)->FirstOrFail();

        Coupon::where('id', $coupon->id)->delete();
        CouponProduct::where('coupon_id', $coupon->id)->delete();
        CouponUser::where('coupon_id', $coupon->id)->delete();

        session()->flash('success',__("piclommerce::admin.coupon_delete"));
        return redirect()->route($this->route . 'index');
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $coupons = Coupon::select(['id','uuid','coupon','name','percent','price','updated_at']);
        return DataTables::of($coupons)
            ->addColumn('actions', function(Coupon $coupon) {
                return $this->getTableButtons($coupon->uuid);
            })
            ->editColumn("updated_at",function(Coupon $coupon) use ($datatable) {
                return $datatable->date($coupon->updated_at);
            })
            ->addColumn('reduce', function(Coupon $coupon){
                if(!empty($coupon->percent)){
                    return '-' . $coupon->percent . '%';
                } else{
                    return '-' . $coupon->price . '€';
                }
            })
            ->rawColumns(['actions','reduce'])
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