<?php

namespace Piclou\Piclommerce\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Support\Facades\Auth;
use Piclou\Piclommerce\Http\Entities\Countries;
use Piclou\Piclommerce\Http\Entities\User;
use Piclou\Piclommerce\Http\Entities\UsersAdresses;
use Piclou\Piclommerce\Http\Requests\AddressRequest;
use Piclou\Piclommerce\Http\Requests\UpdateAccountRequest;
use Ramsey\Uuid\Uuid;


class UserController extends Controller
{
    protected $viewPath = 'piclommerce::users.';

    public function index()
    {
        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.user_my_account') => route('user.account'),
        ];

        SEOMeta::setCanonical(route('user.account'));
        SEOMeta::setTitle(__('piclommerce::web.user_my_account') . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__('piclommerce::web.user_my_account') . " - " . setting("generals.seoDescription"));

        return view($this->viewPath . "account",compact('arianne'));
    }


    public function informations()
    {
        $user = Auth::user();
        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.user_my_account') => route('user.account'),
            __('piclommerce::web.user_my_informations') => route('user.infos'),
        ];

        SEOMeta::setCanonical(route('user.infos'));
        SEOMeta::setTitle(__('piclommerce::web.user_my_informations') . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__('piclommerce::web.user_my_informations') . " - " . setting("generals.seoDescription"));
        return view($this->viewPath . "infos", compact('user', 'arianne'));
    }

    public function informationsUpdate(UpdateAccountRequest $request)
    {
        $user = Auth::user();

        $newsletter = 0;
        if($request->newsletter == 'on') {
            $newsletter = 1;
        }
        $update = [
            'gender' => $request->gender,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'newsletter' => $newsletter
        ];

        if (!empty($request->password)) {
            $update['password'] = bcrypt($request->password);
        }

        User::where('id', $user->id)->update($update);

        session()->flash('success', __('piclommerce::web.user_infos_update_success'));
        return redirect()->route('user.infos');
    }

    public function addresses()
    {
        $addresses = UsersAdresses::where('user_id', Auth::user()->id)->orderBy('id','DESC')->get();

        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.user_my_account') => route('user.account'),
            __('piclommerce::web.user_my_addresses') => route('user.addresses'),
        ];
        SEOMeta::setCanonical(route('user.infos'));
        SEOMeta::setTitle(__('piclommerce::web.user_my_addresses') . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__('piclommerce::web.user_my_addresses') . " - " . setting("generals.seoDescription"));

        return view($this->viewPath . "addresses", compact('addresses', 'arianne'));
    }

    public function addressesCreate()
    {
        $countries = Countries::where('activated', 1)->orderBy('name','asc')->get();

        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.user_my_account') => route('user.account'),
            __('piclommerce::web.user_my_addresses') => route('user.addresses'),
            __('piclommerce::web.user_add_new_address') => route('user.addresses.create'),
        ];
        SEOMeta::setCanonical(route('user.infos'));
        SEOMeta::setTitle(__('piclommerce::web.user_add_new_address') . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__('piclommerce::web.user_add_new_address') . " - " . setting("generals.seoDescription"));

        $data = new UsersAdresses();
        $data->gender = Auth::user()->gender;
        $data->firstname = Auth::user()->firstname;
        $data->lastname = Auth::user()->lastname;

        return view(
            $this->viewPath . "address.create",
            compact('countries', 'data', 'arianne')
        );

    }

    public function addressesStore(AddressRequest $request)
    {
        $insert = [
            'uuid' => Uuid::uuid4()->toString(),
            'user_id' => Auth::user()->id,
            'gender' => $request->gender,
            'delivery' => 1,
            'billing' => ($request->billing)?1:0,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'address' => $request->address,
            'additional_address' => $request->additional_address,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'phone' => $request->phone,
            'country_id' => $request->country_id
        ];
        UsersAdresses::create($insert);

        session()->flash('success', __('piclommerce::web.user_address_created'));
        return redirect()->route('user.addresses');
    }

    public function addressesEdit(string $uuid)
    {
        $data = UsersAdresses::where('uuid', $uuid)
            ->where('user_id', Auth::user()->id)
            ->FirstOrFail();

        $countries = Countries::where('activated', 1)->orderBy('name','asc')->get();

        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            __('piclommerce::web.user_my_account') => route('user.account'),
            __('piclommerce::web.user_my_addresses') => route('user.addresses'),
            __('piclommerce::web.user_edit_address') => route('user.addresses.edit',["uuid" => $data->uuid]),
        ];
        SEOMeta::setCanonical(route('user.infos'));
        SEOMeta::setTitle(__('piclommerce::web.user_edit_address') . " - " . setting("generals.seoTitle"));
        SEOMeta::setDescription(__('piclommerce::web.user_edit_address') . " - " . setting("generals.seoDescription"));

        return view(
            $this->viewPath . "address.edit",
            compact('countries', 'data', 'arianne')
        );
    }

    public function addressesUpdate(AddressRequest $request, string $uuid)
    {
        $address = UsersAdresses::where('uuid', $uuid)
            ->where('user_id', Auth::user()->id)
            ->FirstOrFail();

        UsersAdresses::where('id', $address->id)->update([
            'gender' => $request->gender,
            'billing' => ($request->billing)?1:0,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'address' => $request->address,
            'additional_address' => $request->additional_address,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'phone' => $request->phone,
            'country_id' => $request->country_id
        ]);

        session()->flash('success', __('piclommerce::web.user_address_updated'));
        return redirect()->route('user.addresses');

    }

    public function addressesDelete(string $uuid)
    {
        $address = UsersAdresses::where('uuid', $uuid)
            ->where('user_id', Auth::user()->id)
            ->FirstOrFail();

        UsersAdresses::where('id', $address->id)->delete();

        session()->flash('success', __('piclommerce::web.user_address_deleted'));
        return redirect()->route('user.addresses');

    }
}