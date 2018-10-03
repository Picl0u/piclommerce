<?php
namespace Piclou\Piclommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Piclou\Piclommerce\Http\Entities\Order;
use Piclou\Piclommerce\Http\Entities\OrdersProducts;
use Piclou\Piclommerce\Http\Entities\Product;
use ConsoleTVs\Charts\Facades\Charts;
use Piclou\Piclommerce\Http\Entities\User;
use SEOMeta;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $viewPath = 'piclommerce::admin.';

    /**com
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

    public function login()
    {
        if(Auth::user()){
            if(Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }
        SEOMeta::setTitle(__("piclommerce::admin.login_seo_title"));
        return view($this->viewPath . "login");
    }

    public function dashboard()
    {
        // Products Views
        $productViews = Product::orderByViewsCount()->get(10);
        $totalProductViews = 0;
        foreach($productViews as $product) {
            if($product->numOfViews > 0) {
                $totalProductViews += $product->numOfViews;
            }
        }

        // Best Sale
        $bestSale = OrdersProducts::selectRaw('*, sum(product_id) as sum')
            ->groupBy('product_id')
            ->orderByRaw('SUM(product_id) DESC')
            ->limit(10)
            ->get();

        // Last orders
        $lastOrders = Order::select([
            'id',
            'uuid',
            'reference',
            'total_quantity',
            'price_ttc',
            'user_id',
            'user_firstname',
            'user_lastname',
            'delivery_country_name',
            'status_id',
            'created_at'
        ])
        ->orderBy("id","desc")
        ->get(10);

        $orders = Order::select('price_ttc','created_at')
            ->where('created_at','like', date('Y') . '%')
            ->get();
        $count = [];
        $total = [];
        for ($i = 1; $i<=12; $i++){
            $total[$i] = 0;
            $count[$i] = 0;
            foreach ($orders as $order) {
                if($order->created_at->format('m') == $i){
                    $total[$i] += $order->price_ttc;
                    $count[$i] += 1;
                }
            }
        }
        $chart = Charts::multi('line', 'highcharts')
            ->title('Commandes ' .date('Y'))
            ->colors(['#2ab27b', '#3097D1'])
            ->labels([
                'Janvier',
                'Février',
                'Mars',
                'Avril',
                'Mai',
                'Juin',
                'Juillet',
                'Août',
                'Septembre',
                'Octobre',
                'Novembre',
                'Décembre',
            ])
            ->dataset('Nombre de commande', $count)
            ->dataset('Prix total', $total);
        $totalOrders = Order::where('created_at','like', date('Y-')."%")->count();
        $pricesOrders = Order::where('created_at','like', date('Y-')."%")->sum('price_ttc');


        $custommers = User::where('created_at','like', date('Y-')."%")->where("role","user")->count();

        SEOMeta::setTitle(__("piclommerce::admin.navigation_dashboard"));
        return view($this->viewPath . "dashboard", compact(
            "productViews",
            "totalProductViews",
            "bestSale",
            "lastOrders",
            "totalOrders",
            "pricesOrders",
            "chart",
            "custommers"
        ));
    }

    /**
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard();
    }
}