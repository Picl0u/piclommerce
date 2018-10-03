<?php
namespace Piclou\Piclommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use anlutro\LaravelSettings\Facade as Setting;
use Piclou\Piclommerce\Http\Entities\Content;
use Piclou\Piclommerce\Http\Entities\Countries;
use SEO;

class SettingController extends Controller
{
    /**
    * @var string
    */
    private $viewPath = 'piclommerce::admin.settings.';

    /**
     * @var string
     */
    private $route = 'admin.settings.';

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

    public function generals()
    {
        $data = [
            'websiteName' => Setting::get('generals.websiteName'),
            'logo' => Setting::get('generals.logo'),
            'firstname' => Setting::get('generals.firstname'),
            'lastname' => Setting::get('generals.lastname'),
            'company' => Setting::get('generals.company'),
            'siret' => Setting::get('generals.siret'),
            'email' => Setting::get('generals.email'),
            'orderEmail' => Setting::get('generals.orderEmail'),
            'phone' => Setting::get('generals.phone'),
            'address' => Setting::get('generals.address'),
            'zipCode' => Setting::get('generals.zipCode'),
            'city' => Setting::get('generals.city'),
            'invoiceLogo' => Setting::get('generals.LogoInvoice'),
            'invoiceCompany' => Setting::get('generals.invoiceCompany'),
            'invoiceSiret' => Setting::get('generals.invoiceSiret'),
            'invoicePhone' => Setting::get('generals.invoicePhone'),
            'invoiceAddress' => Setting::get('generals.invoiceAddress'),
            'invoiceZipCode' => Setting::get('generals.invoiceZipCode'),
            'invoiceCity' => Setting::get('generals.invoiceCity'),
            'invoiceCountry' => Setting::get('generals.invoiceCountry'),
            'invoiceTVA' => Setting::get('generals.invoiceTVA'),
            'invoiceRCS' => Setting::get('generals.invoiceRCS'),
            'invoiceFooter' => Setting::get('generals.invoiceFooter'),
            'invoiceNote' => Setting::get('generals.invoiceNote'),
            'facebook' => Setting::get('generals.facebook'),
            'twitter' => Setting::get('generals.twitter'),
            'pinterest' => Setting::get('generals.pinterest'),
            'googlePlus' => Setting::get('generals.googlePlus'),
            'instagram' => Setting::get('generals.instagram'),
            'youtube' => Setting::get('generals.youtube'),
            'seoRobot' => Setting::get('generals.seoRobot'),
            'analytics' => Setting::get('generals.analytics'),
            'seoTitle' => Setting::get('generals.seoTitle'),
            'seoDescription' => Setting::get('generals.seoDescription'),
        ];

        SEO::setTitle(__("piclommerce::admin.navigation_generals_settings"));
        return view($this->viewPath . 'generals', compact('data'));
    }

    public function storeGenerals(Request $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'generals');
        }
        $insertLogo = Setting::get('generals.logo');
        if($request->hasFile('logo')){
            $insertLogo = uploadImage('settings/website', $request->logo);
            if(!empty(Setting::get('generals.logo'))) {
                if(file_exists(Setting::get('generals.logo'))) {
                    unlink(Setting::get('generals.logo'));
                }
            }
        }

        $insertLogoInvoice = Setting::get('generals.LogoInvoice');
        if($request->hasFile('invoiceLogo')){
            $insertLogoInvoice = uploadImage('settings/invoices', $request->invoiceLogo);
            if(!empty(Setting::get('generals.LogoInvoice'))) {
                if(file_exists(Setting::get('generals.LogoInvoice'))) {
                    unlink(Setting::get('generals.LogoInvoice'));
                }
            }
        }

        Setting::set('generals.websiteName', $request->websiteName);
        Setting::set('generals.logo', $insertLogo);
        Setting::set('generals.firstname', $request->firstname);
        Setting::set('generals.lastname', $request->lastname);
        Setting::set('generals.company', $request->company);
        Setting::set('generals.siret', $request->siret);
        Setting::set('generals.email', $request->email);
        Setting::set('generals.orderEmail', $request->orderEmail);
        Setting::set('generals.phone', $request->phone);
        Setting::set('generals.address', $request->address);
        Setting::set('generals.zipCode', $request->zipCode);
        Setting::set('generals.city', $request->city);
        Setting::set('generals.LogoInvoice', $insertLogoInvoice);
        Setting::set('generals.invoiceCompany', $request->invoiceCompany);
        Setting::set('generals.invoiceSiret', $request->invoiceSiret);
        Setting::set('generals.invoiceAddress', $request->invoiceAddress);
        Setting::set('generals.invoiceZipCode', $request->invoiceZipCode);
        Setting::set('generals.invoiceCity', $request->invoiceCity);
        Setting::set('generals.invoiceCountry', $request->invoiceCountry);
        Setting::set('generals.invoicePhone', $request->invoicePhone);
        Setting::set('generals.invoiceTVA', $request->invoiceTVA);
        Setting::set('generals.invoiceRCS', $request->invoiceRCS);
        Setting::set('generals.invoiceFooter', $request->invoiceFooter);
        Setting::set('generals.invoiceNote', $request->invoiceNote);
        Setting::set('generals.facebook', $request->facebook);
        Setting::set('generals.twitter', $request->twitter);
        Setting::set('generals.pinterest', $request->pinterest);
        Setting::set('generals.instagram', $request->instagram);
        Setting::set('generals.youtube', $request->youtube);
        Setting::set('generals.seoRobot', ($request->seoRobot)?1:0);
        Setting::set('generals.analytics', $request->analytics);
        Setting::set('generals.seoTitle', $request->seoTitle);
        Setting::set('generals.seoDescription', $request->seoDescription);

        Setting::save();

        session()->flash('success', __("piclommerce::admin.setting_success"));
        return redirect()->route($this->route . "generals");
    }

    public function slider()
    {
        $data = [
            'arrows' => Setting::get('slider.arrows'),
            'dots' => Setting::get('slider.dots'),
            'type' => Setting::get('slider.type'),
            'transition' => Setting::get('slider.transition'),
            'slideDuration' => Setting::get('slider.slideDuration'),
            'transitionDuration' => Setting::get('slider.transitionDuration'),
        ];

        return view($this->viewPath . 'slider', compact('data'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSlider(Request $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'generals');
        }

        Setting::set('slider.arrows', ($request->arrows)?1:0);
        Setting::set('slider.dots', ($request->dots)?1:0);
        Setting::set('slider.type', $request->type);
        Setting::set('slider.transition', $request->transition);
        Setting::set('slider.slideDuration', $request->slideDuration);
        Setting::set('slider.transitionDuration', $request->transitionDuration);

        Setting::save();

        session()->flash('success', __("piclommerce::admin.setting_success"));
        return redirect()->route($this->route . "slider");

    }

    public function products()
    {
        $data = [
            'paginate' => Setting::get('products.paginate'),
            'orderField' => Setting::get('products.orderField'),
            'orderDirection' => Setting::get('products.orderDirection'),
            'socialEnable' => Setting::get('products.socialEnable'),
            'commentEnable' => Setting::get('products.commentEnable'),
            'zoomEnable' => Setting::get('products.zoomEnable'),
            'modalEnable' => Setting::get('products.modalEnable'),
            'new' => Setting::get('products.new'),
        ];

        return view($this->viewPath . 'products', compact('data'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeProducts(Request $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'generals');
        }
        Setting::set('products.paginate', $request->paginate);
        Setting::set('products.orderField', $request->orderField);
        Setting::set('products.orderDirection', $request->orderDirection);
        Setting::set('products.socialEnable', ($request->socialEnable)?1:0);
        Setting::set('products.commentEnable', ($request->commentEnable)?1:0);
        Setting::set('products.zoomEnable', ($request->zoomEnable)?1:0);
        Setting::set('products.modalEnable', ($request->modalEnable)?1:0);
        Setting::set('products.new', $request->new);

        Setting::save();

        session()->flash('success', __("piclommerce::admin.setting_success"));
        return redirect()->route($this->route . "products");
    }

    public function orders()
    {
        $data = [
            'noAccount' => Setting::get('orders.noAccount'),
            'orderAgain' => Setting::get('orders.orderAgain'),
            'minAmmout' => Setting::get('orders.minAmmout'),
            'stockBooked' => Setting::get('orders.stockBooked'),
            'countryId' => Setting::get('orders.countryId'),
            'freeShippingPrice' => Setting::get('orders.freeShippingPrice'),
            'productQuantityAlert' => Setting::get('orders.productQuantityAlert'),
            'cgv' => Setting::get('orders.cgv'),
            'cgvId' => Setting::get('orders.cgvId'),
            'acceptId' => Setting::get('orders.acceptId'),
            'refuseId' => Setting::get('orders.refuseId'),
        ];

        $countries = Countries::select('id','name')->where('activated',1)->get();
        $contents = Content::select('id','name')->where('published',1)->get();

        return view($this->viewPath . 'orders',compact('data','countries', 'contents'));
    }

    public function storeOrders(Request $request)
    {
        if(config('piclommerce.demo')) {
            session()->flash('error',__("piclommerce::admin.demo_error"));
            return redirect()->route($this->route . 'generals');
        }

        Setting::set('orders.noAccount', ($request->noAccount)?1:0);
        Setting::set('orders.orderAgain', ($request->orderAgain)?1:0);
        Setting::set('orders.cgv ', 1);
        Setting::set('orders.minAmmout', $request->minAmmout);
        Setting::set('orders.stockBooked', $request->stockBooked);
        Setting::set('orders.countryId', $request->countryId);
        Setting::set('orders.freeShippingPrice', $request->freeShippingPrice);
        Setting::set('orders.productQuantityAlert', $request->productQuantityAlert);
        Setting::set('orders.cgvId', $request->cgvId);
        Setting::set('orders.acceptId', $request->acceptId);
        Setting::set('orders.refuseId', $request->refuseId);

        Setting::save();

        session()->flash('success', __("piclommerce::admin.setting_success"));
        return redirect()->route($this->route. 'orders');
    }

}