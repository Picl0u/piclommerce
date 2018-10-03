<?php
return [

    // Infos
    "apiVersion" => "alpha0.0.1",
    "author" => "Picl0u",
    "authorUrl" => "http://www.google.fr",
    "demo" => false,

    "currency" => '&euro;',

    // Languages
    "defaultLanguage" => "fr",
    "languages" => [
        "fr",
        "en"
    ],
    "translateKey" => "translate",

    // Administration
    'adminUrl' => 'admin',
    'adminRole' => 'admin',

    // Administrators
    'superAdminRole' => 'SuperAdmin',

    // Medias
    'fileUploadFolder' => 'uploads',
    'imageQuality' => 100,
    'imageCacheFolder' => 'caches',
    'imageMaxWidth' => 1400,
    'imageNotFound' => 'images/no-found.jpg',
    'directoryImport' =>  'shop/imports',

    // Orders
    'orderRef' => date('Ym'),
    'refCount' => 4, // Number zero for the order reference
    'invoicePath' => 'invoices',
    'invoiceExportPath' => 'exports',
    'invoiceName' => 'Facture',
    'invoiceLogoHeight' => 60,

    // Share socials medias
    'shareWebsites' => ['facebook', 'twitter', 'pinterest', 'gplus'],

    // Paypal
    'paypal' => [
        'client_id' => '',
        'secret' => ''
    ],

];
