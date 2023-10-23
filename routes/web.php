<?php

use App\Http\Controllers\installPackageController;
use App\Http\Controllers\Ordering\CreditCardGatewayController;
use Fosetico\LaravelPageBuilder\LaravelPageBuilder as LaravelPageBuilderLaravelPageBuilder;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return redirect('/page/home');
})->name('page');

Route::any( '/page/{any}', function() {

    $builder = new LaravelPageBuilderLaravelPageBuilder(config('pagebuilder'));
    $hasPageReturned = $builder->handlePublicRequest();

    if (request()->path() === '/' && ! $hasPageReturned) {
        $builder->getWebsiteManager()->renderWelcomePage();
    }

})->where('any', '.*');

Route::any( '/category-multikart/{any}', function() {

    $builder = new LaravelPageBuilderLaravelPageBuilder(config('pagebuilder'));
    $hasPageReturned = $builder->handlePublicRequest();

    if (request()->path() === '/' && ! $hasPageReturned) {
        $builder->getWebsiteManager()->renderWelcomePage();
    }

})->where('any', '.*');

Route::any( '/category/{any}', function() {

    $builder = new LaravelPageBuilderLaravelPageBuilder(config('pagebuilder'));
    $hasPageReturned = $builder->handlePublicRequest();

    if (request()->path() === '/' && ! $hasPageReturned) {
        $builder->getWebsiteManager()->renderWelcomePage();
    }

})->where('any', '.*');

Route::any( '/product/{any}', function() {

    $builder = new LaravelPageBuilderLaravelPageBuilder(config('pagebuilder'));
    $hasPageReturned = $builder->handlePublicRequest();

    if (request()->path() === '/' && ! $hasPageReturned) {
        $builder->getWebsiteManager()->renderWelcomePage();
    }

})->where('any', '.*');

Route::any( '/voucher/{any}', function() {

    $builder = new LaravelPageBuilderLaravelPageBuilder(config('pagebuilder'));
    $hasPageReturned = $builder->handlePublicRequest();

    if (request()->path() === '/' && ! $hasPageReturned) {
        $builder->getWebsiteManager()->renderWelcomePage();
    }

})->where('any', '.*');


Route::get('/nova/installPackage', [installPackageController::class, 'index'])->name('installPackage');

Route::get('/orderpaymentgateway', [CreditCardGatewayController::class, 'create_credit_card_payment_order']);
Route::get('/voucherpaymentgateway', [CreditCardGatewayController::class, 'create_credit_card_payment_voucher']);

Route::match(['get', 'post'], '/finalizepaymentgateway', [CreditCardGatewayController::class, 'finalize_payment_gateway']);


