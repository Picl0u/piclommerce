<?php
namespace Piclou\Piclommerce\Helpers;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Piclou\Piclommerce\Http\Entities\Order;
use Piclou\Piclommerce\Http\Entities\OrdersProducts;

class Invoice{
    /**
     * @var Order
     */
    private $order;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $layout;
    /**
     * @var string
     */
    private $notes;
    /**
     * @var string
     */
    private $footer;


    /**
     * Invoice constructor.
     * @param Order $order
     * @param string|null $name
     */
    public function __construct(Order $order, string $name = null)
    {
        $this->order = $order;
        $this->name = $name;
        $this->layout = 'piclommerce::orders.invoice';
    }

    /**
     * Permet de changer le layout définis par défaut
     * @param string $layout
     * @return Invoice
     */
    public function layout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Permet d'ajouter une note à la facture
     * @param string $notes
     * @return Invoice
     */
    public function notes(string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Permet d'ajouter des informations dans le footer de la facture
     * @param string $footer
     * @return Invoice
     */
    public function footer(string $footer): self
    {
        $this->footer = $footer;
        return $this;
    }


    public function generate(string $path = null)
    {
        $order = $this->order;
        $products = OrdersProducts::where('order_id',$order->id)->get();

        // Information Commerçant
        $merchant = $this->merchantInfos();
        // Information facturation client
        $custommer = $this->customerInfos();
        // Notes
        $notes = $this->notes;
        // Footer
        $footer = $this->footer;

        // Date de la facturation
        Carbon::setLocale(config('app.locale'));
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)
            ->format('d/m/Y ');

        // Enregistrer sur le serveur + Retourne le lien
        if(is_null($path)){
            $path = config('piclommerce.invoicePath');
        }
        if(is_null($this->name)){
            $this->name = config('piclommerce.invoiceName')."-".$order->reference;
        }

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
                'allow_self_signed'=> true,
            ],
        ]);

        $pdf->setHttpContext($context);
        $pdf->loadHtml(view(
                $this->layout,
                compact('merchant', 'custommer', 'order', 'products', 'notes', 'footer', 'date')
            )
        );
        $pdf->render();
        $fileName = config('piclommerce.invoicePath') . "/" . $this->name . ".pdf";
        Storage::put($fileName, $pdf->output(), 'public');
        return $fileName;
    }


    /**
     * Retourne les informations du marchant (infos saisis dans le back office)
     * @return array
     */
    private function merchantInfos()
    {
        return [
            'logo'        => setting("generals.invoiceLogo"),
            'name'        => setting("generals.invoiceCompany"),
            'siret'       => setting("generals.invoiceSiret"),
            'phone'       => setting("generals.invoicePhone"),
            'address'     => setting("generals.invoiceAddress"),
            'zip'         => setting("generals.invoiceZipCode"),
            'city'        => setting("generals.invoiceCity"),
            'country'     => setting("generals.invoiceCountry")
        ];
    }

    /**
     * Retourne les informations du client
     * @return array
     */
    private function customerInfos()
    {
        return [
            'name'    => $this->order->billing_firstname." ".$this->order->billing_lastname,
            'phone'   => $this->order->billing_phone,
            'address' => $this->order->billing_address . " " .$this->order->billing_additional_address,
            'zip'     => $this->order->billing_zip_code,
            'city'    => $this->order->billing_city,
            'country' => $this->order->billing_country_name
        ];
    }

}