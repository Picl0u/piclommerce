<?php
namespace Piclou\Piclommerce\Http\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Piclou\Piclommerce\Http\Entities\Carriers;
use Piclou\Piclommerce\Http\Entities\Content;
use Piclou\Piclommerce\Http\Entities\Status as orderStatus;
use Piclou\Piclommerce\Http\Entities\User;
use Piclou\Piclommerce\Http\Entities\Vat;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Models\Role;
use anlutro\LaravelSettings\Facade as Setting;
use DB;
use Webpatser\Countries\Countries;

class InstallPiclommerceCommand extends Command{


    protected $user;
    protected $userSecond;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'piclommerce:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Piclommerce - Laravel Ecommerce solution';


    /**
     * InstallPiclommerceCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->disclaimer();
        $this->requirements();
        $this->preflight();
        $this->printTitle();
        $this->stepOne();
        $this->publish();

        $this->line('--------------------------------------------------------');
        $this->info('All done! Here is some useful info to get started');
        $headers = ['', ''];
        $this->table($headers, [
            ['Username / Email', $this->user->email],
            ['Password', '[hidden]'],
            //['Back Office Login', Router::url('admin.login')],
            //['Front office URL', route("")],
            ['Documentation URL', "http://www.ikon-k.fr"],
            ['Creator', 'Piclou'],
        ]);
        exit;
    }

    protected function disclaimer()
    {
        $this->warn('*** BEFORE YOU CONTINUE ***');
        $this->line(' ');
        $this->warn('Thank you for downloading this solution.It gives me a lot of pleasure.');
        $this->line(' ');
        $this->warn('Please note, this software is very much considered in an Alpha Release state, if you are installing this on an existing project, please reconsider.');
        $this->line(' ');
        $this->warn('We do not want to cause any damage to your data and at this stage we cannot guarantee this wont\'t happen.');
        $this->warn('The API needs certain data to "work" at this point so this installer will add data for things such as Taxes, Currencies, Attributes etc');
        if ($this->confirm('Are you happy to continue?')) {
            $this->info('Nice :)');
        } else {
            exit;
        }
    }

    protected function requirements()
    {
        $this->info('Checking requirements');
    }

    protected function preflight()
    {
        $this->info('Initialising... (This can take a long time) : lot of tables...');
        //$this->call('vendor:publish --provider="Artesaos\SEOTools\Providers\SEOToolsServiceProvider"');
        //$this->call('vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"');
        $this->call('migrate');
    }

    protected function printTitle()
    {
        $this->line('= Welcome to ====================================');
        $this->line("  _____ _      _                                             
 |  __ (_)    | |                                            
 | |__) |  ___| | ___  _ __ ___  _ __ ___   ___ _ __ ___ ___ 
 |  ___/ |/ __| |/ _ \| '_ ` _ \| '_ ` _ \ / _ \ '__/ __/ _ \
 | |   | | (__| | (_) | | | | | | | | | | |  __/ | | (_|  __/
 |_|   |_|\___|_|\___/|_| |_| |_|_| |_| |_|\___|_|  \___\___|
                                                             
                                                             ");
        $this->line('==================================== version  : '.config("piclommerce.apiVersion"));
    }

    /**
     * Step one
     *
     * @return void
     */
    protected function stepOne()
    {
        $this->info('Lets start with the basics...');

        $firstname = $this->ask('What\'s your firstname?');
        $lastname = $this->ask('What\'s your lastname?');

        $email = $this->ask("Nice to meet you {$firstname} {$lastname}, what's your email?");
        $tries = 0;
        while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($tries < 3) {
                $message = 'Oops! That email looks invalid, can we try again?';
            } elseif ($tries >= 3 && $tries <= 6) {
                $message = 'No really, give me a proper email...';
            } elseif ($tries >= 6 && $tries <= 9) {
                $message = 'Seriously now, lets not do this all day... what is it?';
            } elseif ($tries >= 10) {
                $this->error('I give up');
                exit();
            }
            $email = $this->ask($message);
            $tries++;
        }
        $password = $this->secret('Choose a password (hidden)');
        $passwordConfirm = $this->secret('Confirm it (hidden)');
        while ($password != $passwordConfirm) {
            $password = $this->secret('Oop! Passwords didn\'t match try again');
            $passwordConfirm = $this->secret('Aaaand confirm it');
        }
        $this->info('Just creating your admin account now');

        $role = Role::create([
            'uuid' => Uuid::uuid4()->toString(),
            'name' => config('piclommerce.superAdminRole'),
            'guard_name' =>config('piclommerce.superAdminRole'),
        ]);
        Role::create([
            'uuid' => Uuid::uuid4()->toString(),
            'name' => 'Admin',
            'guard_name' => 'Admin',
        ]);

        $insert = [
            'online' => 1,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => str_slug($firstname.$lastname),
            'email' => $email,
            'password' => bcrypt($password),
            'role' => 'admin',
            'gender' => "M",
            'newsletter' => 0,
            'role_id' => $role->id,
            'guard_name' => $role->name
        ];
        $user = User::create($insert);
        $user->assignRole($role->name);
        $this->user = $user;

        if ($this->confirm('Do you want a second administrator?')) {
            $firstnameSecond = $this->ask('What\'s his/her firstname?');
            $lastnameSecond = $this->ask('What\'s his/her lastname?');
            $emailSecond = $this->ask("Welcome {$firstnameSecond} {$lastnameSecond}, what's his/her email?");
            $tries = 0;
            while (!filter_var($emailSecond, FILTER_VALIDATE_EMAIL)) {
                if ($tries < 3) {
                    $message = 'Oops! That email looks invalid, can we try again?';
                } elseif ($tries >= 3 && $tries <= 6) {
                    $message = 'No really, give me a proper email...';
                } elseif ($tries >= 6 && $tries <= 9) {
                    $message = 'Seriously now, lets not do this all day... what is it?';
                } elseif ($tries >= 10) {
                    $this->error('No way...');
                }
                $emailSecond = $this->ask($message);
                $tries++;
            }
            $password = $this->secret('Choose a password (hidden)');
            $passwordConfirm = $this->secret('Confirm it (hidden)');
            while ($password != $passwordConfirm) {
                $password = $this->secret('Oop! Passwords didn\'t match try again');
                $passwordConfirm = $this->secret('Aaaand confirm it');
            }
            $this->info('Just creating the new admin account now');
            $insert = [
                'uuid' => Uuid::uuid4()->toString(),
                'online' => 1,
                'firstname' => $firstnameSecond,
                'lastname' => $lastnameSecond,
                'username' => str_slug($firstnameSecond.$lastnameSecond),
                'email' => $emailSecond,
                'password' => bcrypt($password),
                'role' => 'admin',
                'gender' => "M",
                'newsletter' => 0,
                'role_id' => $role->id,
                'guard_name' => $role->name
            ];
            $userSecond = User::create($insert);
            $userSecond->assignRole($role->name);
        }

        Carriers::create([
            'free' => 0,
            'price' => 1,
            'weight' => 0,
            'name' => 'Transporteur',
            'delay' => '5 jours',
            'url' => 'http://www.suiviscolis.fr?id=',
            'published' => 1,
            'default' => 1,
            'default_price' => 0
        ]);

        $this->info('Seeded the default carrier !');

        /* Paiement accepté */
        $accept = Content::create([
            'published' => 1,
            'name' => json_encode([config('app.locale') => "Paiement accepté"]),
            "slug" => json_encode([config('app.locale') => "paiement-accepte"]),
            'description' => json_encode([config('app.locale') => $this->lorem()]),
            'content_category_id' => null
        ]);
        //Setting::set('orders.acceptId', $accept->id);
        $this->info('Seeded the content of payment accept !');

        /* Paiement refusé */
        $refuse = Content::create([
            'published' => 1,
            'name' => json_encode([config('app.locale') => "Paiement refusé"]),
            "slug" => json_encode([config('app.locale') => "paiement-refuse"]),
            'description' => json_encode([config('app.locale') => $this->lorem()]),
            'content_category_id' => null
        ]);
        //Setting::set('orders.refuseId', $refuse->id);
        $this->info('Seeded the content of payment refuse !');

        /* CGV */
        $cgv = Content::create([
            'published' => 1,
            'on_footer' => 1,
            'name' => json_encode([config('app.locale') => "Conditions Générales de vente"]),
            'slug' => json_encode([config('app.locale') => "conditions-generals-de-vente"]),
            'description' => json_encode([config('app.locale') => $this->lorem()]),
            'content_category_id' => null
        ]);

        //Setting::set('orders.cgvId', $cgv->id);
        $this->info('Seeded the content of CGV !');

        //Empty the countries table
        DB::table(\Config::get('countries.table_name'))->delete();

        //Get all of the countries
        $c = new Countries();
        $countries = $c->getList();
        foreach ($countries as $countryId => $country){
            DB::table(\Config::get('countries.table_name'))->insert(array(
                'id' => $countryId,
                'capital' => ((isset($country['capital'])) ? $country['capital'] : null),
                'citizenship' => ((isset($country['citizenship'])) ? $country['citizenship'] : null),
                'country_code' => $country['country-code'],
                'currency' => ((isset($country['currency'])) ? $country['currency'] : null),
                'currency_code' => ((isset($country['currency_code'])) ? $country['currency_code'] : null),
                'currency_sub_unit' => ((isset($country['currency_sub_unit'])) ? $country['currency_sub_unit'] : null),
                'currency_decimals' => ((isset($country['currency_decimals'])) ? $country['currency_decimals'] : null),
                'full_name' => ((isset($country['full_name'])) ? $country['full_name'] : null),
                'iso_3166_2' => $country['iso_3166_2'],
                'iso_3166_3' => $country['iso_3166_3'],
                'name' => $country['name'],
                'region_code' => $country['region-code'],
                'sub_region_code' => $country['sub-region-code'],
                'eea' => (bool)$country['eea'],
                'calling_code' => $country['calling_code'],
                'currency_symbol' => ((isset($country['currency_symbol'])) ? $country['currency_symbol'] : null),
                'flag' =>((isset($country['flag'])) ? $country['flag'] : null),
                'activated' => ($countryId == 250)?1:0,
            ));
        }

        $this->info('Seeded the countries!');
        $this->info('Default country : France !');

        /* Paiement accepté */
        orderStatus::create([
            'color' => '#2ab27b',
            'order_accept' => 1,
            'order_refuse' => 0,
            'name' => json_encode([config('app.locale') => "Paiement accepté"]),
        ]);

        /* Paiement refusé */
        orderStatus::create([
            'color' => '#bf5329',
            'order_accept' => 0,
            'order_refuse' => 1,
            'name' => json_encode([config('app.locale') => "Paiement refusé"]),
        ]);

        /* Commande en préparation */
        orderStatus::create([
            'color' => null,
            'order_accept' => 0,
            'order_refuse' => 0,
            'name' => json_encode([config('app.locale') => "Commande en préparation"]),
        ]);

        /* Commande en attente */
        orderStatus::create([
            'color' => null,
            'order_accept' => 0,
            'order_refuse' => 0,
            'name' => json_encode([config('app.locale') => "Commande en attente"]),
        ]);
        $this->info('Seeded the order\'s status !');

        Vat::create([
            'name' => 'TVA FR 20%',
            'percent' => 20
        ]);
        $this->info('Seeded the default VAT !');

        /*$setting_file = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/settings/settings.json';
        if(file_exists($setting_file)){
            Storage::copy($setting_file, 'settings.json');
            $this->info('settings.json copied in storage path !');
        } else {
            $this->info('settings.json not copied in storage path... Sorry');
        }*/

    }

    protected function publish()
    {
        $this->line("This action involves copying routes, controllers, views and more...");
        $this->line("This action will take a lot of time...");
        $this->line("processing...");

        if(file_exists(base_path('routes')."/web.php")){
            $web_route = __DIR__ . '../../../../../routes/web.php';
            if(file_exists($web_route)){
                File::copy($web_route, base_path('routes')."/web.php");
            }
        }

        $assets_dir = __DIR__ . '../../../../../resources/assets';
        if(file_exists($assets_dir)) {
            File::copyDirectory($assets_dir, base_path('resources/assets/piclommerce'));
            $this->line("Assets folder copied ");
        }

        $lang_dir = __DIR__ . '../../../../../resources/lang';
        if(file_exists($lang_dir)) {
            File::copyDirectory($lang_dir, base_path('resources/piclommerce/lang'));
            $this->line("Langs folder copied ");
        }

        $views_dir = __DIR__ . '../../../../../resources/views';
        if(file_exists($views_dir)) {
            File::copyDirectory($lang_dir, base_path('resources/piclommerce/views'));
            $this->line("Views folder copied ");
        }

        $config_file = __DIR__ . '../../../../../config/piclommerce.php';
        if(file_exists($config_file)) {
            File::copy($config_file, config_path("piclommerce.php"));
            $this->line("Config file copied ");
        }

        $settings_file = __DIR__ . '../../../../../settings/settings.json';
        if(file_exists($settings_file)) {
            File::copy($settings_file,storage_path("settings.json"));
            $this->line("Settings file copied ");
        }

        $public_dir = __DIR__ . '../../../../../public';
        if(file_exists($public_dir)) {
            File::copyDirectory($public_dir, (file_exists("public"))?base_path("public"):base_path("web"));
            $this->line("Public folder copied ");
        }

        $route_dir = __DIR__ . '../../../../../routes';
        if(file_exists($route_dir)) {
            File::copyDirectory($route_dir, base_path('routes'));
            $this->line("Routes copied ");
        }

        $controller_dir = __DIR__ . '../../../Controllers';
        if(file_exists($controller_dir)) {
            File::copyDirectory($controller_dir, base_path('app/Http/Controllers/Piclommerce'));
            $this->line("Controllers copied ");
        }
        $this->line("All done !");
    }

    protected function lorem()
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras sed ullamcorper purus. Integer in porta 
        leo, nec feugiat neque. Sed congue tristique justo, a dignissim eros facilisis nec. Ut et interdum sapien. 
        Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed aliquam quam 
         fringilla egestas. Fusce accumsan nisl urna, eu mattis turpis pellentesque a. Fusce vel tortor tortor. Aenean 
         eu finibus lectus. Mauris commodo, justo et eleifend porttitor, mauris erat tincidunt purus, vel luctus lectus 
         justo eget urna. Integer vitae velit at ipsum convallis volutpat ut eu nulla.';
    }
}
