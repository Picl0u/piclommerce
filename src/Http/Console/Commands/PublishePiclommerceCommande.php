<?php
namespace Piclou\Piclommerce\Http\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishePiclommerceCommande extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'piclommerce:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Piclommerce - Laravel Ecommerce solution';

    /**
     * Create a new command instance.
     *
     * @return void
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
            File::copyDirectory($views_dir, base_path('resources/piclommerce/views'));
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
}