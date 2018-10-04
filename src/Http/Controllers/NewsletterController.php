<?php
namespace App\Http\Controllers\Piclommerce;

use Piclou\Piclommerce\Http\Entities\Newsletters;
use Illuminate\Routing\Controller;
use Piclou\Piclommerce\Http\Requests\Newsletter;
class NewsletterController extends Controller
{
    public function register(Newsletter $request)
    {
        $newsletter = Newsletters::where('email', $request->email)->first();

        if(!empty($newsletter)){
            if(empty($newsletter->active)) {
                Newsletters::where('id', $newsletter->id)->update([
                    'active' => 1
                ]);
            }else{
                return response(__("piclommerce::web.newsletter_already"), 403)
                    ->header('Content-Type', 'text/plain');
            }
        }else{
            Newsletters::create([
                'active' => 1,
                'email' => $request->email
            ]);
        }
        return response(__("piclommerce::web.newsletter_thanks"), 200)
            ->header('Content-Type', 'text/plain');

    }
}
