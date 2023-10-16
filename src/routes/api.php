<?php
use App\Http\Controllers\installPackageController;
use App\Http\Controllers\Accounts\AddRemoveCartController;
use App\Http\Controllers\Accounts\AddRemoveWishlistController;
use App\Http\Controllers\Accounts\ChangePasswordController;
use App\Http\Controllers\Accounts\CreateCustomerController;
use App\Http\Controllers\Accounts\DeleteCartController;
use App\Http\Controllers\Accounts\GetCartController;
use App\Http\Controllers\Accounts\GetCustomerController;
use App\Http\Controllers\Accounts\GetWishListController;
use App\Http\Controllers\Accounts\LoginCustomerController;
use App\Http\Controllers\Accounts\LogoutUserController;
use App\Http\Controllers\Accounts\RemindPasswordController;
use App\Http\Controllers\Accounts\UpdateCartController;
use App\Http\Controllers\Accounts\UpdateCustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\GetPaginationController;
use App\Http\Controllers\Ordering\AddPaymentController;
use App\Http\Controllers\Ordering\CreateOrderController;
use App\Http\Controllers\Ordering\CreatePurchaseVoucherOrderController;
use App\Http\Controllers\Ordering\FinalizeOrderController;
use App\Http\Controllers\Ordering\GetActiveCountriesController;
use App\Http\Controllers\Ordering\GetActivePaymentMethodsController;
use App\Http\Controllers\Ordering\GetDeliveryGroupsController;
use App\Http\Controllers\Ordering\GetGiftAmountController;
use App\Http\Controllers\Ordering\GetOrdersController;
use App\Http\Controllers\Ordering\GetPickupGroupsController;
use App\Http\Controllers\Ordering\GetPickupsController;
use App\Http\Controllers\Ordering\GetShippingController;
use App\Http\Controllers\Ordering\ValidateVoucherController;
use App\Http\Controllers\ProductCatalogue\GetActiveBrandsController;
use App\Http\Controllers\ProductCatalogue\GetActiveCategoriesController;
use App\Http\Controllers\ProductCatalogue\GetActiveColorsController;
use App\Http\Controllers\ProductCatalogue\GetActiveOptionsController;
use App\Http\Controllers\ProductCatalogue\GetActiveProductsController;
use App\Http\Controllers\ProductCatalogue\GetActiveSizesController;
use App\Http\Controllers\ProductCatalogue\GetActiveVouchersController;
use App\Http\Controllers\ProductCatalogue\GetRelatedProductsController;
use App\Http\Controllers\ProductCatalogue\GetSizeChartController;
use App\Http\Controllers\ProductCatalogue\GetVariantsController;
use App\Http\Controllers\Website\GetBannerController;
use App\Http\Controllers\Website\GetBlogPostsController;
use App\Http\Controllers\Website\GetBlogsController;
use App\Http\Controllers\Website\GetLanguagesController;
use App\Http\Controllers\Website\GetMenuController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('localhost')->group(function () {
    Route::get('login', [LoginController::class, 'login']);  
	// Route::get('authorize-login', [webProductCatalogueController::class, 'authorize_login']); 

	Route::post('/internal-api/data', [ApiController::class, 'processData']);

	// webProductCatalogueController
	Route::post('getmenu', [GetMenuController::class, 'get_menu']);
	Route::post('getbanner', [GetBannerController::class, 'get_banner']);
	Route::post('getlanguages', [GetLanguagesController::class, 'get_languages']);
	Route::post('getblogs', [GetBlogsController::class, 'get_blogs']);
	Route::post('getblogposts', [GetBlogPostsController::class, 'get_blog_posts']);


	Route::post('getcategories', [GetActiveCategoriesController::class, 'get_categories']);
	Route::post('getpagination', [GetPaginationController::class, 'get_pagination']);
	Route::post('getvariants', [GetVariantsController::class, 'get_variant']); 	
	Route::post('getactiveoptions', [GetActiveOptionsController::class, 'get_active_options']);
	Route::post('getactiveproducts', [GetActiveProductsController::class, 'get_active_products']);
	Route::post('getactivebrands', [GetActiveBrandsController::class, 'get_active_brands']);
	Route::post('getactivecolours', [GetActiveColorsController::class, 'get_active_colours']);
	Route::post('getactivesizes', [GetActiveSizesController::class, 'get_active_sizes']);
	Route::post('getactivevouchers', [GetActiveVouchersController::class, 'get_active_vouchers']);

	// webCheckoutController
	Route::post('getactivecountries', [GetActiveCountriesController::class, 'get_active_countries']);
	Route::post('getpickups', [GetPickupsController::class, 'get_pickups']);
	Route::post('getactivepaymentmethods', [GetActivePaymentMethodsController::class, 'get_active_payment_methods']);
	Route::post('getrelatedproducts', [GetRelatedProductsController::class, 'get_related_products']);
	Route::post('getsizecharts', [GetSizeChartController::class, 'get_size_charts']);
	Route::post('getshipping', [GetShippingController::class, 'get_active_shipping_methods']);
	Route::post('getpickupgroups', [GetPickupGroupsController::class, 'get_pickup_groups']);
	Route::post('getdeliverygroups', [GetDeliveryGroupsController::class, 'get_delivery_groups']);
	Route::post('validate-voucher', [ValidateVoucherController::class, 'validate_voucher']);    
	Route::post('create-order', [CreateOrderController::class, 'create_order']);    
	Route::post('purchase-voucher', [CreatePurchaseVoucherOrderController::class, 'create_purchase_voucher_order']);    
	Route::post('finalize-order', [FinalizeOrderController::class, 'finalize_order']);    
	Route::post('getorders', [GetOrdersController::class, 'get_orders']);    
	Route::post('getgiftamount', [GetGiftAmountController::class, 'get_gift_amount']);    
	Route::post('addpayment', [AddPaymentController::class, 'add_payment']);    
	

	// webUsersController
	Route::post('remind-password', [RemindPasswordController::class, 'remind_password']);
	Route::post('create-webuser', [CreateCustomerController::class, 'create_user']);
	Route::post('get-webuser', [GetCustomerController::class, 'get_users']);
	Route::post('update-webuser', [UpdateCustomerController::class, 'update_user']);
	Route::post('webuserschangepassword', [ChangePasswordController::class, 'changepassword_user']);
	Route::post('login-webuser', [LoginCustomerController::class, 'login_user']);
	Route::post('logout-webuser', [LogoutUserController::class, 'logout_user']);
	Route::post('getwishlist', [GetWishListController::class, 'get_wish_list']);
	Route::post('getcart', [GetCartController::class, 'get_cart']);
	Route::post('update-cart', [UpdateCartController::class, 'update_cart']);
	Route::post('delete-cart', [DeleteCartController::class, 'delete_cart']);
	Route::post('add-remove-wishlist', [AddRemoveWishlistController::class, 'add_remove_to_wish_list']);
	Route::post('add-remove-cart', [AddRemoveCartController::class, 'add_remove_to_cart']);
	Route::post('add-remove-carts', [AddRemoveCartController::class, 'add_remove_to_carts']);
// });

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    })
    ->name('api.user');

Route::name('api.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('users', UserController::class);
    });

Route::post('installPackage', [installPackageController::class, 'install']);