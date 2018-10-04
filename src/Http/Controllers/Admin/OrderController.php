<?php
namespace App\Http\Controllers\Piclommerce\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Piclou\Piclommerce\Helpers\DataTable;
use Piclou\Piclommerce\Http\Entities\Order;
use Piclou\Piclommerce\Http\Entities\OrdersExports;
use Piclou\Piclommerce\Http\Entities\OrdersProducts;
use Piclou\Piclommerce\Http\Entities\OrdersStatus;
use Piclou\Piclommerce\Http\Entities\Status;
use Piclou\Piclommerce\Http\Entities\User;
use Piclou\Piclommerce\Http\Mail\Admin\OrderCarrier;
use Piclou\Piclommerce\Http\Mail\Admin\OrderStatus;
use Piclou\Piclommerce\Http\Requests\Admin\InvoiceExport;
use Piclou\Piclommerce\Http\Requests\Admin\OrderCarrierRequest;
use Piclou\Piclommerce\Http\Requests\Admin\OrderStatusRequest;
use Ramsey\Uuid\Uuid;
use SEO;
use Yajra\DataTables\DataTables;
use \Mail;
use \ZipArchive;

class OrderController extends Controller
{
    /**
     * @var string
     */
    private $viewPath = 'piclommerce::admin.order.orders.';

    /**
     * @var string
     */
    private $route = 'admin.orders.orders.';

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

    public function index(Request $request)
    {
        if($request->ajax()){
            return $this->dataTable();
        }
        return view($this->viewPath . "index");
    }

    public function getInvoice(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstorFail();
        $name = config('piclommerce.invoicePath') . "/" .
            config('piclommerce.invoiceName') . "-" .
            $order->reference . '.pdf';
        if(Storage::exists($name)) {
            return Storage::download($name);
        }
        session()->flash('error', __('piclommerce::web.order_invoice_not_found'));
        return redirect()->back();
    }

    public function edit(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstorFail();
        $products = OrdersProducts::where('order_id', $order->id)->get();
        if(!empty($order->user_id)){
            $user = User::where('id', $order->user_id)->first();
            $nbOrder = Order::where('user_id', $order->user_id)->count();
            $totalOrder = Order::select('price_ttc')->where('user_id', $order->user_id)->sum('price_ttc');
        } else{
            $user = new User();
            $nbOrder = Order::where('user_email', $order->user_email)->count();
            $totalOrder = Order::select('price_ttc')->where('user_email', $order->user_email)->sum('price_ttc');
        }
        $status = Status::all();

        return view(
            $this->viewPath . "edit",
            compact('order','products', 'user', 'status', 'nbOrder', 'totalOrder')
        );
    }

    public function statusUpdate(OrderStatusRequest $request, string $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        $status = Status::where('id', $request->status_id)->firstOrFail();

        if($order->status_id != $status->id) {

            Order::where('id', $order->id)->update([
                'status_id' => $status->id
            ]);

            OrdersStatus::where('order_id', $order->id)->create([
                'status_id' => $status->id,
                'order_id'  => $order->id
            ]);

            /* Envoie email au client */
            Mail::to($order->user_email)
                ->send(new OrderStatus($order, $status));

            session()->flash('success',__("piclommerce::admin.orders_status_create"));
        } else {
            session()->flash('error',__("piclommerce::admin.orders_status_same"));
        }

        return redirect()->route($this->route. 'edit',['uuid' => $order->uuid]);

    }

    public function carrierUpdate(OrderCarrierRequest $request, string $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();

        Order::where('id', $order->id)->update([
            'shipping_url' => $request->shipping_url,
            'shipping_order_id' => $request->shipping_order_id,
            'shipping_delay' => $request->shipping_delay
        ]);

        /* Envoie email au client */
        Mail::to($order->user_email)
            ->send(new OrderCarrier($order, $request->shipping_url, $request->shipping_order_id));

        session()->flash('success',__("piclommerce::admin.orders_carrier_update"));
        return redirect()->route($this->route . 'edit',['uuid' => $order->uuid]);

    }

    public function invoices()
    {
        $exports = OrdersExports::orderBy('id','desc')->get();
        return view($this->viewPath.'invoices', compact('exports'));
    }

    public function invoicesExport(InvoiceExport $request)
    {
        $date_begin = $request->date_begin;
        $date_end = $request->date_end;

        $orders = Order::select('reference')
            ->where('created_at', '>=', $date_begin. ' 00:00:00')
            ->where('created_at','<=', $date_end.' 23:59:59')
            ->get();
        if(count($orders) == 0) {
            session()->flash('errors', __("piclommerce::admin.orders_no_invoice"));
            return redirect()->route($this->route . 'invoices');
        }
        $uuid = Uuid::uuid4()->toString();
        $zipName = $this->fileNameExists("invoices-".$date_begin."-".$date_end).".zip";

        if(!file_exists( storage_path("app/" . config('piclommerce.invoiceExportPath')))){
            if(!mkdir(storage_path("app/" . config('piclommerce.invoiceExportPath')),0770, true)){
                dd('Echec lors de la création du répertoire : '.$dir);
            }
        }
        $zip = new ZipArchive;
        if ($zip->open(
                storage_path("app/" . config('piclommerce.invoiceExportPath') . "/" . $zipName),
                ZipArchive::CREATE) === TRUE
        ) {
            foreach ($orders as $order) {
                $invoice = storage_path('app/'.
                    config('piclommerce.invoicePath') . "/" .
                    config('piclommerce.invoiceName') . "-" .
                    $order->reference . '.pdf');
                if(file_exists($invoice)) {
                    $zip->addFile($invoice, config('piclommerce.invoiceName') . "-" . $order->reference . '.pdf');
                }

            }
            $zip->close();
        } else {
            session()->flash('errors', __("piclommerce::admin.orders_no_zip"));
            return redirect()->route($this->route . 'invoices');
        }

        OrdersExports::create([
            'uuid' => $uuid,
            'fileName' => $zipName,
            'begin' => $date_begin,
            'end' => $date_end
        ]);
        session()->flash('success',__("piclommerce::admin.orders_export_create"));
        return redirect()->route($this->route . 'invoices');
    }

    public function invoicesDownload(string $uuid)
    {
        $zip = OrdersExports::select("fileName")->where('uuid', $uuid)->firstOrFail();
        $name = config('piclommerce.invoiceExportPath') . "/" . $zip->fileName;
        if(Storage::exists($name)) {
            return Storage::download($name);
        }
        session()->flash('error', __('piclommerce::web.order_invoice_not_found'));
        return redirect()->back();

    }

    private function fileNameExists(string $fileName): string
    {
        if(file_exists(storage_path(
            "app/". config('piclommerce.invoiceExportPath') . "/" . $fileName . ".zip"
        ))) {
            return $this->fileNameExists($fileName ."_copy");
        }
        return $fileName;
    }

    /**
     * @return mixed
     */
    private function dataTable()
    {
        $datatable = new DataTable();
        $orders = Order::select([
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
            'updated_at'
        ]);
        return DataTables::of($orders)
            ->addColumn('actions', function(Order $order) {
                return $this->getTableButtons($order->uuid);
            })
            ->editColumn("updated_at",function(Order $order) use ($datatable) {
                return $datatable->date($order->updated_at);
            })
            ->editColumn("user_id",function(Order $order) use ($datatable) {
                return  $datatable->yesOrNot($order->user_id);;
            })
            ->editColumn('price_ttc', function(Order $order){
                return priceFormat($order->price_ttc)." ({$order->total_quantity})";
            })
            ->editColumn('user_firstname', function(Order $order){
                return $order->user_firstname." ".$order->user_lastname;
            })
            ->editColumn('status_id',function(Order $order){
                if(!empty($order->status_id))
                {
                    $html = '<div class="label"';
                    if(!empty($order->Status->color)) {
                        $html .=' style="background-color:'.$order->Status->color.';"';
                    }
                    $html .= '>';
                    $html .= $order->Status->name;
                    $html .= '</label>';
                    return $html;
                }
                return "";
            })
            ->rawColumns(['actions','price_ttc','user_firstname','user_id','status_id'])
            ->make(true);
    }

    /**
     * @return string
     */
    private function getTableButtons($uuid): string
    {
        $editRoute = route($this->getRoute() . "edit",['uuid' => $uuid]);
        $deleteRoute = route($this->getRoute() . "delete",['uuid' => $uuid]);
        $invoiceRoute = route($this->getRoute() . "invoice" ,['uuid' => $uuid]);

        $html = '<a href="'.$invoiceRoute.'" class="table-button"><i class="fa fa-file-pdf-o"></i> '.__("piclommerce::admin.orders_invoice").'</a>';
        $html .= '<a href="'.$editRoute.'" class="table-button edit-button"><i class="fa fa-pencil"></i> '.__("piclommerce::admin.edit").'</a>';
        //$html .= '<a href="'.$deleteRoute.'" class="table-button delete-button confirm-alert"><i class="fa fa-trash"></i> '.__("piclommerce::admin.delete").'</a>';
        return $html;
    }
}
