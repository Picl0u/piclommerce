<?php

namespace Piclou\Piclommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Support\Facades\Mail;
use Piclou\Piclommerce\Http\Entities\Content;
use Piclou\Piclommerce\Http\Mail\SendContactEmail;
use Piclou\Piclommerce\Http\Requests\SendEmailRequest;

class ContactController extends Controller
{
    protected $viewPath = 'piclommerce::contact.';

    public function index()
    {
        $url = Route('contact.index');
        $title =  __("piclommerce::web.contact_seo_title") . " - " . setting("generals.seoTitle");
        $description = __("piclommerce::web.contact_seo_title") . " - " .  setting("generals.seoDescription");

        // Arianne
        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __("piclommerce::web.contact_title") => $url,
        ];
        SEOMeta::setCanonical($url);
        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);

        $contents = Content::select('id','image','name','slug','summary','content_category_id','updated_at')
            ->where('published', 1)
            ->where('on_homepage', 1)
            ->orderBy('order','ASC')
            ->get();

        return view($this->viewPath . 'index', compact('arianne', 'contents'));
    }

    public function send(SendEmailRequest $request)
    {
        $contact = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'message' => $request->message,
        ];
        Mail::to(setting('generals.email'))->send(new SendContactEmail($contact));

        session()->flash('success',__("piclommerce::web.contact_success"));
        return redirect()->route('contact.index');

    }

}
