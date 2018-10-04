Piclommerce
===============

### Laravel 5.5 – PHP &gt;= 7.0 – MySQL 5.6
### Version ALPHA 0.1  
### Cette version est une version alpha, des bugs sont sans doutes encore présents.

La documentation n'est pas encore rédigée, je m'en occupe très prochainement !

Installation
-----------

1.  Télécharger laravel 5.5 

2.  Lancer la commande :  `composer require piclou/piclommerce @dev`
puis `composer require netshell/paypal dev-master`

3. Rendez vous dans le fichier config "app" dans `config/app.php` pour ajouter les providers
 `'Webpatser\Countries\CountriesServiceProvider',
  'Netshell\Paypal\PaypalServiceProvider',`
  Puis les alias
`'Countries' => 'Webpatser\Countries\CountriesFacade',
 'Paypal' => 'Netshell\Paypal\Facades\Paypal'`  
 
 4. Lancez les commandes suivantes
 `php artisan vendor:publish --provider="Artesaos\SEOTools\Providers\SEOToolsServiceProvider`
 `php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider`
 `php artisan vendor:publish --provider="CyrildeWit\EloquentViewable\EloquentViewableServiceProvider" --tag="migrations"` 
 
 5. Modifiez le fichier de configuration "database" pour rendre mysql non strict `'strict' => false,`
 
 6. Lancez l'instalation à l'aide de la commande : `php artisan piclommerce:install` et suivez les étapes demandées.
 **Attention certaines étapes prennent du temps** 
 
 7. Ajoutez les dépendances suivantes dans le fichier "package.json"
 `
 "dependencies": {
         "bootstrap": "^4.1.3",
         "ckeditor": "^4.10.1",
         "datatables.net": "^1.10.16",
         "datatables.net-bs": "^1.10.16",
         "datatables.net-buttons-bs": "^1.4.2",
         "datatables.net-buttons-dt": "^1.5.3",
         "datatables.net-colreorder-bs": "^1.4.1",
         "datatables.net-dt": "^1.10.19",
         "datatables.net-responsive": "^2.2.0",
         "datatables.net-responsive-dt": "^2.2.3",
         "datatables.net-rowreorder-bs": "^1.2.3",
         "datatables.net-rowreorder-dt": "^1.2.5",
         "datatables.net-scroller-bs": "^1.4.3",
         "datatables.net-scroller-dt": "^1.5.1",
         "datetimepicker": "^0.1.38",
         "flatpickr": "^4.3.2",
         "font-awesome": "^4.7.0",
         "gmaps": "^0.4.24",
         "jquery": "^3.2.1",
         "jstree": "^3.3.4",
         "jszip": "^3.1.5",
         "pdfmake": "^0.1.38",
         "remodal": "^1.1.1",
         "select2": "^4.0.4",
         "sortablejs": "~1.4.2",
         "sweetalert": "^2.1.0",
         "toastr": "^2.1.4",
         "webpack-jquery-ui": "^1.0.0"
     }
 `
 
 8. Changez le fichier webpack.mix par celui ci : [webpack.mix](https://github.com/Picl0u/piclommerce/blob/master/webpack.mix.js)
 
 9. ENJOY !
