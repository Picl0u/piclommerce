<?php
use \Piclou\Piclommerce\Helpers\CustomRoute;
Route::group([
    'middleware' => ['web','piclommerce.lang'],
    'prefix' => '',
    'namespace' => 'Piclou\Piclommerce\Http\Controllers'
], function() {
    // Auth
    CustomRoute::auth();
    Route::get('home', 'Auth\LoginController@logout')->name('logout');

    // Homepage
    Route::get('', 'HomeController@index')->name('homepage');
    // Choose Language
    Route::get('locale/{locale}',  'HomeController@setLocale')
        ->where(['locale' => '[a-z]+'])
        ->name('change.language');
    // Pages - content
    Route::get('page/{slug}-{id}', 'ContentController@index')
        ->where(['slug' => '[a-z-0-9\-]+', 'id' => '[0-9]+'])
        ->name('content.index');
    // Newsletter
    Route::post('newsletter/register', 'NewsletterController@register')->name('newsletter.register');
    // Newsletter
    Route::post('panier/ajouter', 'NewsletterController@register')->name('newsletter.register');
    // Whistlist - Add product
    Route::post('liste-de-souhait/ajouter-produit', 'WhishlistController@addProduct')->name('whishlist.product.add');
    // Contact
    Route::get('contact', 'ContactController@index')->name('contact.index');
    Route::post('contact/send', 'ContactController@send')->name('contact.send');
});
// Shop
Route::group([
    'middleware' => ['web','piclommerce.lang'],
    'namespace' => 'Piclou\Piclommerce\Http\Controllers',
    'prefix' => "boutique"
], function() {
    // Products - List
    Route::get('{slug}-{id}','ProductController@lists')
        ->name('product.list')
        ->where(['slug' => '[a-z-0-9\-]+', 'id' => '[0-9]+']);
    // Products - Detail
    Route::get('produit/{slug}-{id}','ProductController@detail')
        ->name('product.show')
        ->where(['slug' => '[a-z-0-9\-]+', 'id' => '[0-9]+']);
    // Add comment
    Route::post('produit/comment/{uuid}','ProductController@addComment')
        ->name('product.comment')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->middleware('user');
    // Search
    Route::get('recherche','ProductController@search')->name('product.search');
    // Flash sale
    Route::get('ventes-flash','ProductController@flashSales')->name('product.flash');
});

// Shopping Cart
Route::group([
    'middleware' => ['web','piclommerce.lang'],
    'namespace' => 'Piclou\Piclommerce\Http\Controllers',
    'prefix' => 'panier',
], function() {
    /* Ajouter un produit */
    Route::post('ajouter-produit', 'ShoppingCartController@addProduct')->name('cart.product.add');
    /* Ajouter un produit */
    Route::post('check-attriutes', 'ShoppingCartController@productAttributes')->name('cart.product.attributes');

    /* Modifier un produit */
    Route::post('edit', 'ShoppingCartController@editProduct')->name('cart.product.edit');

    /* Afficher le panier */
    Route::get('/', 'ShoppingCartController@show')->name('cart.show');

    /* Code promo */
    Route::post('coupon', 'ShoppingCartController@coupon')->name('cart.coupon');
    /* Code promo - Annuler */
    Route::get('coupon/cancel', 'ShoppingCartController@couponCancel')->name('cart.coupon.cancel');
    /* Code promo - Check */
    Route::get('coupon/check', 'ShoppingCartController@checkCoupon')->name('cart.coupon.check');
});

// Orders
Route::group([
    'middleware' => ['web','piclommerce.lang'],
    'namespace' => 'Piclou\Piclommerce\Http\Controllers',
    'prefix' => 'commande',
], function() {

    /* Connexion / Inscription */
    Route::get('connexion-inscription', 'ShoppingCartController@orderUser')->name('cart.user.connect');
    /* Utilisateur - Commande express */
    Route::post('user-express', 'ShoppingCartController@orderUserExpress')->name('cart.user.express');

    /* Retour */
    Route::any('return', 'ShoppingCartController@orderReturn')
        ->name('cart.return');
    Route::any('return-test', 'ShoppingCartController@orderTest')
        ->name('cart.return.test');
    /* Annulé/Refusé */
    Route::any('cancel', 'ShoppingCartController@orderCancel')
        ->name('cart.cancel');
    /* Accepté */
    Route::any('accept', 'ShoppingCartController@orderAccept')
        ->name('cart.accept');

    Route::group([
        'middleware' => 'piclommerce.cart',
    ], function() {
        /* Adresse */
        Route::get('/adresses', 'ShoppingCartController@orderAddresses')->name('cart.user.address');
        Route::post('/address-add', 'ShoppingCartController@orderAddressStore')->name('cart.user.address.store');
        Route::post('address-select', 'ShoppingCartController@orderAddressSelect')->name('cart.user.address.select');

        /* Transpoteurs */
        Route::get('transpoteurs', 'ShoppingCartController@orderShipping')->name('cart.user.shipping');
        Route::post('transpoteurs/store', 'ShoppingCartController@orderShippingStore')->name('cart.user.shipping.store');

        /* Récapitulatif */
        Route::get('recapitulatif', 'ShoppingCartController@orderRecap')->name('cart.recap');

        /* Process */
        Route::get('process', 'ShoppingCartController@process')->name('cart.process');
    });
});

// Users
Route::group([
    'middleware' => ['web','piclommerce.user','piclommerce.lang'],
    'namespace' => 'Piclou\Piclommerce\Http\Controllers',
    'prefix' => 'compte',
], function() {

    // Mon compte
    Route::get('/', 'UserController@index')->name('user.account');
    // Mes informations
    Route::get('mes-informations', 'UserController@informations')->name('user.infos');
    Route::post('mes-informations/update', 'UserController@informationsUpdate')->name('user.infos.update');
    // Mes adresses
    Route::get('mes-adresses', 'UserController@addresses')->name('user.addresses');
    Route::get('mes-adresses/creation', 'UserController@addressesCreate')->name('user.addresses.create');
    Route::post('mes-adresses/store', 'UserController@addressesStore')->name('user.addresses.store');
    Route::get('mes-adresses/edition/{uuid}', 'UserController@addressesEdit')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name('user.addresses.edit');
    Route::post('mes-adresses/update/{uuid}', 'UserController@addressesUpdate')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name('user.addresses.update');
    Route::get('mes-adresses/delete/{uuid}', 'UserController@addressesDelete')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name('user.addresses.delete');
    /* Mon compte */
    Route::get('commandes', 'OrderController@index')->name('order.index');

    /* Facture */
    Route::get("commandes/facture/{uuid}",'OrderController@invoice')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name("order.invoice");

    /* Commande en détail */
    Route::get("commandes/detail/{uuid}",'OrderController@show')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name("order.show");

    /* Retour produit */
    Route::post('commandes/return/{uuid}','OrderController@returnProducts')
        ->where(['uuid' => '[a-z-0-9\-]+'])
        ->name("order.return");

    Route::get('liste-de-souhait/', 'WhishlistController@index')->name('whishlist.index');

    Route::get('liste-de-souhait/ajouter-panier/{rowId}', 'WhishlistController@addCart')
        ->where(['rowId' => '[a-z-0-9\-]+'])
        ->name('whishlist.addCart');

});
