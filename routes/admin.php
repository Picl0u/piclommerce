<?php
use \Piclou\Piclommerce\Helpers\CustomRoute;

Route::group([
    'middleware' => ['web','piclommerce.admin'],
    'prefix' => config("piclommerce.adminUrl"),
    'namespace' => 'Piclommerce\Admin'
], function() {

    // Dashboard
    Route::get('', 'AdminController@dashboard')->name('admin.dashboard');

    // Slider
    CustomRoute::crud('sliders','SliderController', 'admin.sliders', 'slider');
    Route::get("/sliders/positions",'SliderController@positions')->name("admin.sliders.positions");
    Route::post("/sliders/positionsStore", 'SliderController@positionsStore')
        ->name("admin.sliders.positions.store");
    Route::post("/sliders/updateImage/{uuid}","SliderController@updateImage")
        ->name("admin.sliders.image.update")
        ->where(['uuid' => '[a-z-0-9\-]+']);
    Route::any("/sliders/translate",'SliderController@translate')
        ->name("admin.sliders.translate");

    // Banner
    CustomRoute::crud('banner','BannerController', 'admin.banner', 'content');
    Route::get("/banner/positions",'BannerController@positions')->name("admin.banner.positions");
    Route::post("/banner/positionsStore", 'BannerController@positionsStore')
        ->name("admin.banner.positions.store");
    Route::post("/banner/updateImage/{uuid}","BannerController@updateImage")
        ->name("admin.banner.image.update")
        ->where(['uuid' => '[a-z-0-9\-]+']);

    // Pages - Categories
    CustomRoute::crud('pages/categories','ContentCategoryController', 'admin.pages.categories', 'categories');
    Route::any("/pages/categories/translate",'ContentCategoryController@translate')
        ->name("admin.pages.categories.translate");

    // Pages - Contents
    CustomRoute::crud('pages/contents','ContentController', 'admin.pages.contents', 'content');
    Route::any("/pages/contents/translate",'ContentController@translate')
        ->name("admin.pages.contents.translate");
    Route::post("/pages/contents/updateImage/{uuid}","ContentController@updateImage")
        ->name("admin.pages.content.image.update")
        ->where(['uuid' => '[a-z-0-9\-]+']);
    Route::get("/pages/contents/positions",'ContentController@positions')->name("admin.pages.contents.positions");
    Route::post("/pages/contents/positionsStore", 'ContentController@positionsStore')
        ->name("admin.pages.contents.positions.store");

    // Shop - Categories
    CustomRoute::crud('shop/categories','ProductCategoryController', 'admin.shop.categories', 'products');
    Route::any("shop/categories/translate",'ProductCategoryController@translate')
        ->name("admin.shop.categories.translate");
    Route::get("shop/categories/positions",'ProductCategoryController@positions')->name("admin.shop.categories.positions");
    Route::post("shop/categories/positionsStore", 'ProductCategoryController@positionsStore')
        ->name("admin.shop.categories.positions.store");
    Route::post("shop/categories/updateImage/{uuid}","ProductCategoryController@updateImage")
        ->name("admin.shop.categories.image.update")
        ->where(['uuid' => '[a-z-0-9\-]+']);
    Route::post("shop/categories/updateImageList/{uuid}","ProductCategoryController@updateImageList")
        ->name("admin.shop.categories.imageList.update")
        ->where(['uuid' => '[a-z-0-9\-]+']);
    Route::get("shop/categories/image/delete/{uuid}",'ProductCategoryController@imageDelete')
        ->name("admin.shop.categories.image.delete")
        ->where(['uuid' => '[a-z-0-9\-]+']);
    Route::get("shop/categories/imagelist/delete/{uuid}",'ProductCategoryController@imageListDelete')
        ->name("admin.shop.categories.imagelist.delete")
        ->where(['uuid' => '[a-z-0-9\-]+']);

    // Shop - Vats
    CustomRoute::crud('shop/vats','VatController', 'admin.shop.vats', 'products');

    // Shop - Products
    CustomRoute::crud('products','ProductController', 'admin.shop.products', 'product');
    Route::post("shop/products/updateImage/{uuid}","ProductController@updateImage")
        ->name("admin.shop.products.image.update")
        ->where(['uuid' => '[a-z-0-9\-]+']);
    Route::post("shop/products/updateImageList/{uuid}/{image}","ProductController@updateImageList")
        ->name("admin.shop.products.imagelist.update")
        ->where(['image' => '[a-z-0-9\-]+', 'uuid' => '[a-z-0-9\-]+']);
    Route::get("shop/products/image/delete/{uuid}",'ProductController@imageDelete')
        ->name("admin.shop.products.image.delete")
        ->where(['uuid' => '[a-z-0-9\-]+']);
    Route::get("shop/products/image/duplicate/{uuid}",'ProductController@duplicate')
        ->name("admin.shop.products.duplicate")
        ->where(['uuid' => '[a-z-0-9\-]+']);
    Route::get("shop/products/imagelist/delete/{uuid}/{image}",'ProductController@imageListDelete')
        ->name("admin.shop.products.imagelist.delete")
        ->where(['image' => '[a-z-0-9\-]+', 'uuid' => '[a-z-0-9\-]+']);
    // Shop - Products - Positions
    Route::get("/products/positions", 'ProductController@positions')->name("admin.shop.products.positions");
    Route::post("/products/positionsStore", 'ProductController@positionsStore')
        ->name("admin.shop.products.positions.store");
    // Déclinaisons - Affichage du formulaire
    Route::get('products/attributes/add/{id}', 'ProductController@declinaison')
        ->name('admin.shop.products.attribute.add')
        ->where(['id' => '[0-9]+']);
    // Déclinaisons - Ajout d'une déclinaisons
    Route::post('products/attributes/store/{id}', 'ProductController@declinaisonStore')
        ->name('admin.shop.products.attribute.store')
        ->where(['id' => '[0-9]+']);
    // Déclinaisons - Modifier une déclinaison
    Route::get('products/attributes/edit/{id}/{uuid}', 'ProductController@declinaisonEdit')
        ->name('admin.shop.products.attribute.edit')
        ->where([
            'id' => '[0-9]+',
            'uuid' => '[a-z-0-9\-]+'
        ]);
    // Déclinaisons - Modifier une déclinaison
    Route::post('products/attributes/update/{uuid}', 'ProductController@declinaisonUpdate')
        ->name('admin.shop.products.attribute.update')
        ->where([
            'uuid' => '[a-z-0-9\-]+'
        ]);
    // Déclinaisons - Supprimer une déclinaison
    Route::get('products/attributes/delete/{uuid}', 'ProductController@declinaisonDelete')
        ->name('admin.shop.products.attribute.delete')
        ->where([
            'uuid' => '[a-z-0-9\-]+'
        ]);
    Route::get("/products/imports", 'ProductController@import')->name("admin.shop.products.imports");
    Route::post("/products/imports/store", 'ProductController@storeImport')
        ->name("admin.shop.products.imports.store");
    Route::get("/products/export", 'ProductController@export_product')
        ->name("admin.shop.products.export");
    Route::get("/products/export/attributes", 'ProductController@export_attributes')
        ->name("admin.shop.products.export.attributes");
    Route::get("/products/imports/attributes", 'ProductController@import_attributes')
        ->name("admin.shop.products.attributes.imports");
    Route::post("/products/imports/attributes/store", 'ProductController@import_attributes_store')
        ->name("admin.shop.products.imports.attributes.store");

    // Shop - Comments
    CustomRoute::crud('comments', 'CommentController', 'admin.shop.comments', 'product' );

    // Orders
    CustomRoute::crud('orders','OrderController', 'admin.orders.orders', 'admin.order');
    Route::get("/orders/orders/invoice/{uuid}", 'OrderController@getInvoice')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name("admin.orders.orders.invoice");
    // Orders - Invoices
    Route::get("/orders/orders/invoices", 'OrderController@invoices')->name("admin.orders.orders.invoices");
    Route::post("/orders/orders/invoices/export", 'OrderController@invoicesExport')
        ->name("admin.orders.orders.invoices.export");
    Route::get("/orders/orders/invoices/download/{uuid}", 'OrderController@invoicesDownload')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name("admin.orders.orders.invoices.download");
    /* Mettre à jours le statut de la commande */
    Route::post('orders/status/{uuid}', 'OrderController@statusUpdate')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name('admin.orders.orders.status');
    /* Mettre à jours les infos du transporteur */
    Route::post('orders/carrier/{uuid}', 'OrderController@carrierUpdate')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name('admin.orders.orders.carrier');

    // Order - Status
    CustomRoute::crud('status','StatusController', 'admin.order.status','order');
    Route::any("status/translate",'StatusController@translate')
        ->name("admin.order.status.translate");
    // Order - Countries
    CustomRoute::crud('countries','CountriesController', 'admin.order.countries','order');
    Route::get("/countries/activate/{id}", 'CountriesController@activate')
        ->name("admin.order.countries.activate")
        ->where(['id' => '[0-9]+']);
    Route::get("/countries/desactivate/{id}", 'CountriesController@desactivate')
        ->name("admin.order.countries.desactivate")
        ->where(['id' => '[0-9]+']);
    // Order - Carrier
    CustomRoute::crud('carriers','CarrierController', 'admin.order.carriers','order');
    // Order - Coupons
    CustomRoute::crud('coupons','CouponController', 'admin.coupon', 'coupon');

    //Custommers
    CustomRoute::crud('users','UserController', 'admin.users','user');
    //Custommers - Addresses
    CustomRoute::crud('addresses','UserAddressController', 'admin.addresses', 'user');

    // Newsletter
    CustomRoute::crud('newsletters', 'NewsletterController', 'admin.newsletter', 'newsletter');
    Route::get('newsletters/export', 'NewsletterController@export')->name('admin.newsletter.export');

    // Administrators
    CustomRoute::crud('admins','AdminUserController','admin.admin', 'admin');

    // Settings - General
    Route::get('/settings/generals', 'SettingController@generals')->name("admin.settings.generals");
    Route::post('/settings/generals/store', 'SettingController@storeGenerals')->name("admin.settings.generals.store");
    // Settings - Slider
    Route::get('/settings/slider', 'SettingController@slider')->name("admin.settings.slider");
    Route::post('/settings/slider/store', 'SettingController@storeSlider')->name("admin.settings.slider.store");
    // Settings - Products
    Route::get('/settings/products', 'SettingController@products')->name("admin.settings.products");
    Route::post('/settings/products/store', 'SettingController@storeProducts')->name("admin.settings.products.store");
    // Settings - Orders
    Route::get('/settings/orders', 'SettingController@orders')->name("admin.settings.orders");
    Route::post('/settings/orders/store', 'SettingController@storeOrders')->name("admin.settings.orders.store");

});

Route::group([
    'middleware' => 'web',
    'prefix' => config("piclommerce.adminUrl"),
    'namespace' => 'Piclommerce\Admin'
], function() {

    // Login
    Route::get('login', 'AdminController@login')->name('admin.login');
});