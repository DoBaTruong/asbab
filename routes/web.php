<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::namespace('Guest')->group(function ()
{
    Route::get('/', 'HomeController@index')->name('asbab.home');
    Route::post('/', 'HomeController@login')->name('asbab.login');
    Route::get('logout', 'HomeController@logout')->name('asbab.logout');

    // Route about search function
    Route::match(['get', 'post'], 'search', 'SearchController@index')->name('asbab.search.index');

    // Route about chat real time client
    Route::get('chat', 'ChatController@index')->name('asbab.chat.index');
    Route::post('chat/store', 'ChatController@store')->name('asbab.chat.store');

    // Route about page login by socialite
    Route::get('redirect/{provider}', 'SocialteController@redirect')->name('asbab.login.redirect');
    Route::get('callback/{provider}', 'SocialteController@callback')->name('asbab.login.callback');

    // Route about page product
    Route::get('product/{slug}', 'ProductController@show')->name('asbab.product.show');
    Route::get('product/getSubComm/{id}', 'ProductController@getSubComm')->name('asbab.product.getSubComm');
    Route::get('product/getComment/{id}', 'ProductController@getComment')->name('asbab.product.getComment');
    Route::post('product/{id}', 'ProductController@stars')->name('asbab.product.stars');
    Route::post('product/add_comment/{id}', 'ProductController@add_comment')->name('asbab.product.add_comment');
    Route::get('product/remove_comment/{id}', 'ProductController@remove_comment')->name('asbab.product.remove_comment');
    Route::get('product/like_comment/{id}', 'ProductController@like_comment')->name('asbab.product.like_comment');

    // Route wishlist page
    Route::get('wishlist', function () {
        return view('asbab.wishlist');
    })->name('asbab.wishlist');
    Route::get('wishlist/data', 'ProductController@wishlist')->name('asbab.wishlist.data');
    Route::get('wishlist/add', 'ProductController@add_wishlist')->name('asbab.wishlist.add');
    Route::get('wishlist/remove/{id}', 'ProductController@remove_wishlist')->name('asbab.wishlist.remove');

    // Route compare page
    Route::get('compare', function () {
        return view('asbab.compare');
    })->name('asbab.compare');
    Route::get('compare/data', 'ProductController@compare')->name('asbab.compare.data');

    // Route about page cart
    Route::get('cart/addcart/{id}', 'CartController@addcart')->name('asbab.cart.addcart');
    Route::get('cart/removecart/{id}', 'CartController@removecart')->name('asbab.cart.removecart');
    Route::get('shop', 'ShopController@index')->name('asbab.shop.index');
    Route::get('shop/get_data', 'ShopController@get_data')->name('asbab.shop.data');
    Route::get('cart', 'CartController@index')->name('asbab.cart.index');
    Route::get('cart/update', 'CartController@update')->name('asbab.cart.update');

    // Route about page checkout
    Route::get('checkout/coupon', 'CheckoutController@coupon')->name('asbab.checkout.coupon');
    Route::get('checkout/calc_fee_ship', 'CheckoutController@calc_fee_ship')->name('asbab.checkout.calc_fee_ship');
    Route::get('checkout/payment', 'CheckoutController@payment')->name('asbab.checkout.payment');

    // Route about paypal method
    Route::get('checkout/paypal', 'CheckoutController@paypal')->name('asbab.checkout.paypal');
    Route::get('checkout/success', 'CheckoutController@success')->name('asbab.checkout.success');
    Route::get('checkout/cancel', 'CheckoutController@cancel')->name('asbab.checkout.cancel');

    // Route about vnpay method
    Route::get('checkout/vnpay', 'CheckoutController@vnpay')->name('asbab.checkout.vnpay');
    Route::get('checkout/vnpay/return', 'CheckoutController@vnpayReturn')->name('asbab.checkout.vnpay.return');

    // Route about page news
    Route::get('news', 'NewsController@index')->name('asbab.news.index');
    Route::get('news/data', 'NewsController@data')->name('asbab.news.data');
    Route::get('news/getComment/{id}', 'NewsController@getComment')->name('asbab.news.getComment');
    Route::get('news/getSubComm/{id}', 'NewsController@getSubComm')->name('asbab.news.getSubComm');
    Route::get('news/{slug}', 'NewsController@details')->name('asbab.news.details');
    Route::post('news/add_comment/{id}', 'NewsController@add_comment')->name('asbab.news.add_comment');
    Route::get('news/remove_comment/{id}', 'NewsController@remove_comment')->name('asbab.news.remove_comment');
    Route::get('news/like_comment/{id}', 'NewsController@like_comment')->name('asbab.news.like_comment');

    // Route about page account
    Route::get('account', 'AccountController@index')->name('asbab.account.index');
    Route::post('account/edit_profile', 'AccountController@edit_profile')->name('asbab.account.edit_profile');
    Route::post('account/reset_password', 'AccountController@reset_password')->name('asbab.account.reset_password');

    // Route about page account
    Route::get('order', function () {
        if (auth()->user()) {
            return view('asbab.order');
        } else {
            return redirect()->to('asbab#login_account');
        }
    })->name('asbab.order.index');
    Route::get('order/history', 'AccountController@history')->name('asbab.order.history');
    Route::get('order/confirm/{id}', 'AccountController@confirm')->name('asbab.order.confirm');
    Route::get('order/detail/{id}', 'AccountController@detail')->name('asbab.order.detail');
    Route::get('order/cancel/{id}', 'AccountController@cancel_order')->name('asbab.order.cancel');

    // Route about page contact
    Route::get('contact', 'ContactController@index')->name('asbab.contact.index');
    Route::post('contact/question', 'ContactController@question')->name('asbab.contact.question');
});

Route::prefix('admin')->namespace('Admin')->group(function ()
{
    Route::get('/', 'AuthController@index')->name('admin');
    Route::get('logout', 'AuthController@logout')->name('admin.logout');
    Route::post('/', 'AuthController@login')->name('admin.login');
    Route::get('notification/message', 'AuthController@notification')->name('admin.notification.message');

    // Route about chat real time client
    Route::get('chat', 'ChatController@index')->name('admin.chat.index')->middleware('permission:list chat');
    Route::post('chat/store', 'ChatController@store')->name('admin.chat.store')->middleware('permission:add chat');;
    Route::get('chat/data/{id}', 'ChatController@data')->name('admin.chat.data')->middleware('permission:list chat');;
    Route::get('chat/search', 'ChatController@search')->name('admin.chat.search')->middleware('permission:list chat');;

    Route::prefix('dashboard')->group(function () {
        Route::get('line_chart', 'AuthController@line_chart')->name('admin.dashboard.line_chart');
        Route::get('donuts_chart', 'AuthController@donuts_chart')->name('admin.dashboard.donuts_chart');
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', function () {
            return view('admin.user.employee');
        })->name('admin.employee.index')->middleware('permission:xem nhân viên');
        Route::get('data/{permission}', 'UserController@employees')->name('admin.employee.data');
        Route::get('create', 'UserController@create')->name('admin.employee.create')->middleware('permission:thêm nhân viên');
        Route::post('store', 'UserController@store')->name('admin.employee.store')->middleware('permission:thêm nhân viên');
        Route::get('edit/{id}', 'UserController@edit')->name('admin.employee.edit')->middleware('permission:sửa nhân viên');
        Route::post('update/{id}', 'UserController@update')->name('admin.employee.update')->middleware('permission:sửa nhân viên');
        Route::get('delete/{id}', 'UserController@destroy')->name('admin.employee.delete')->middleware('permission:xóa nhân viên');
    });

    Route::prefix('customer')->group(function () {
        Route::get('/', function () {
            return view('admin.user.customer');
        })->name('admin.customer.index');
        Route::get('data/{permission}', 'UserController@customers')->name('admin.customer.data');
        Route::post('update/{type}', 'UserController@cus_update')->name('admin.customer.update')->middleware('permission:sửa khách hàng');
    });

    Route::prefix('category')->group(function () {
        Route::get('index', function () {
            return view('admin.category.index');
        })->name('admin.category.index')->middleware('permission:xem danh mục');
        Route::get('data/{permission}', 'CategoryController@index')->name('admin.category.data');
        Route::get('create', 'CategoryController@create')->name('admin.category.create')->middleware('permission:thêm danh mục');
        Route::post('store', 'CategoryController@store')->name('admin.category.store')->middleware('permission:thêm danh mục');
        Route::get('edit/{id}', 'CategoryController@edit')->name('admin.category.edit')->middleware('permission:sửa danh mục');
        Route::post('update/{id}', 'CategoryController@update')->name('admin.category.update')->middleware('permission:sửa danh mục');
        Route::get('delete/{id}', 'CategoryController@destroy')->name('admin.category.delete')->middleware('permission:xóa danh mục');
    });

    Route::prefix('brand')->group(function () {
        Route::get('/', function () {
            return view('admin.brand.index');
        })->name('admin.brand.index')->middleware('permission:list brand');
        Route::get('data/{permission}', 'BrandController@index')->name('admin.brand.data');
        Route::post('store', 'BrandController@store')->name('admin.brand.store')->middleware('permission:add brand');
        Route::get('edit/{id}', 'BrandController@edit')->name('admin.brand.edit')->middleware('permission:edit brand');
        Route::post('update/{id}', 'BrandController@update')->name('admin.brand.update')->middleware('permission:edit brand');
        Route::get('delete/{id}', 'BrandController@destroy')->name('admin.brand.delete')->middleware('permission:delete brand');
    });

    Route::prefix('product')->group(function () {
        Route::get('/', function () {
            return view('admin.product.index');
        })->name('admin.product.index')->middleware('permission:xem sản phẩm');
        Route::get('data/{permission}', 'ProductController@index')->name('admin.product.data');
        Route::get('create', 'ProductController@create')->name('admin.product.create')->middleware('permission:thêm sản phẩm');
        Route::post('store', 'ProductController@store')->name('admin.product.store')->middleware('permission:thêm sản phẩm');
        Route::get('edit/{id}', 'ProductController@edit')->name('admin.product.edit')->middleware('permission:sửa sản phẩm');
        Route::post('update/{id}', 'ProductController@update')->name('admin.product.update')->middleware('permission:sửa sản phẩm');
        Route::get('delete/{id}', 'ProductController@destroy')->name('admin.product.delete')->middleware('permission:xóa sản phẩm');
    });

    Route::prefix('news')->group(function () {
        Route::get('/', function () {
            return view('admin.news.index');
        })->name('admin.news.index')->middleware('permission:list news');
        Route::get('data/{permission}', 'NewsController@index')->name('admin.news.data');
        Route::get('create', 'NewsController@create')->name('admin.news.create')->middleware('permission:add news');
        Route::post('store', 'NewsController@store')->name('admin.news.store')->middleware('permission:add news');
        Route::get('edit/{id}', 'NewsController@edit')->name('admin.news.edit')->middleware('permission:edit news');
        Route::post('update/{id}', 'NewsController@update')->name('admin.news.update')->middleware('permission:edit news');
        Route::get('delete/{id}', 'NewsController@destroy')->name('admin.news.delete')->middleware('permission:delete news');
    });

    Route::prefix('comment')->group(function () {
        Route::get('product', function () {
            return view('admin.comment.product');
        })->name('admin.comment.product')->middleware('permission:list comment');
        Route::get('product/data', 'CommentController@PRDcomments')->name('admin.comment.product.data');
        Route::get('product/{slug}', 'CommentController@PRDcommDT')->name('admin.comment.product.details');
        Route::get('product/{slug}/reply', 'CommentController@PRDreply')->name('admin.comment.product.reply')->middleware('permission:reply comment');
        Route::get('product/{slug}/delete', 'CommentController@PRDdelete')->name('admin.comment.product.delete')->middleware('permission:delete comment');

        Route::get('news', function () {
            return view('admin.comment.news');
        })->name('admin.comment.news')->middleware('permission:list comment');
        Route::get('news/data', 'CommentController@NEWScomments')->name('admin.comment.news.data');
        Route::get('news//{slug}', 'CommentController@NEWScommDT')->name('admin.comment.news.details');
        Route::get('news/{slug}/reply', 'CommentController@NEWSreply')->name('admin.comment.news.reply')->middleware('permission:reply comment');
        Route::get('news/{slug}/delete', 'CommentController@NEWSdelete')->name('admin.comment.news.delete')->middleware('permission:delete comment');
    });

    Route::prefix('slider')->group(function () {
        Route::get('index', function () {
            return view('admin.slider.index');
        })->name('admin.slider.index')->middleware('permission:list slider');
        Route::get('data/{permission}', 'SliderController@index')->name('admin.slider.data');
        Route::post('store', 'SliderController@store')->name('admin.slider.store')->middleware('permission:add slider');
        Route::get('edit/{id}', 'SliderController@edit')->name('admin.slider.edit')->middleware('permission:edit slider');
        Route::post('update/{id}', 'SliderController@update')->name('admin.slider.update')->middleware('permission:edit slider');
        Route::get('delete/{id}', 'SliderController@destroy')->name('admin.slider.delete')->middleware('permission:delete slider');
    });

    Route::prefix('order')->group(function () {
        Route::get('index', 'OrderController@index')->name('admin.order.index')->middleware('permission:list order');
        Route::get('data/{show}/{update}', 'OrderController@data')->name('admin.order.data');
        Route::get('show/{id}', 'OrderController@show')->name('admin.order.show')->middleware('permission:show order');;
        Route::get('print/{id}', 'OrderController@print')->name('admin.order.print')->middleware('permission:print order');;
        Route::post('update/{status}', 'OrderController@update')->name('admin.order.update')->middleware('permission:update order');;
    });

    Route::prefix('coupon')->group(function () {
        Route::get('index', function () {
            return view('admin.coupon.index');
        })->name('admin.coupon.index')->middleware('permission:list coupon');;
        Route::get('data/{permission}', 'CouponController@index')->name('admin.coupon.data');
        Route::get('create', 'CouponController@create')->name('admin.coupon.create')->middleware('permission:add coupon');
        Route::post('store', 'CouponController@store')->name('admin.coupon.store')->middleware('permission:add coupon');
        Route::get('edit/{id}', 'CouponController@edit')->name('admin.coupon.edit')->middleware('permission:edit coupon');
        Route::get('send/{id}/{type}', 'CouponController@send')->name('admin.coupon.send')->middleware('permission:send coupon');
        Route::post('update/{id}', 'CouponController@update')->name('admin.coupon.update')->middleware('permission:edit coupon');
        Route::get('delete/{id}', 'CouponController@destroy')->name('admin.coupon.delete')->middleware('permission:delete coupon');
    });

    Route::prefix('role')->group(function () {
        Route::get('index', function () {
            return view('admin.role.index');
        })->name('admin.role.index')->middleware('permission:xem vai trò');
        Route::get('data/{permission}', 'RoleController@index')->name('admin.role.data');
        Route::get('create', 'RoleController@create')->name('admin.role.create');
        Route::post('store', 'RoleController@store')->name('admin.role.store');
        Route::get('edit/{id}', 'RoleController@edit')->name('admin.role.edit')->middleware('permission:sửa vai trò');
        Route::post('update/{id}', 'RoleController@update')->name('admin.role.update')->middleware('permission:sửa vai trò');
        Route::get('delete/{id}', 'RoleController@destroy')->name('admin.role.delete')->middleware('permission:xóa vai trò');
    });

    Route::prefix('delivery')->group(function () {
        Route::get('index', function () {
            return view('admin.delivery.index');
        })->name('admin.delivery.index')->middleware('permission:list delivery');
        Route::get('data', 'DeliveryController@index')->name('admin.delivery.data');
        Route::get('provinces', 'DeliveryController@provinces')->name('admin.delivery.provinces');
        Route::get('districts/{id}', 'DeliveryController@wards')->name('admin.delivery.wards');
        Route::get('wards/{id}', 'DeliveryController@districts')->name('admin.delivery.districts');
        Route::post('store', 'DeliveryController@store')->name('admin.delivery.store')->middleware('permission:add delivery');
        Route::post('update/{id}', 'DeliveryController@update')->name('admin.delivery.update')->middleware('permission:edit delivery');
    });

    Route::prefix('support')->group(function () {
        Route::get('index', function () {
            return view('admin.support.index');
        })->name('admin.support.index')->middleware('permission:list support');
        Route::get('data/{permission}', 'SupportController@index')->name('admin.support.data');
        Route::get('reply/{id}', 'SupportController@store')->name('admin.support.reply')->middleware('permission:reply support');
        Route::get('delete/{id}', 'SupportController@destroy')->name('admin.support.delete')->middleware('permission:delete support');
    });

    Route::prefix('permission')->group(function () {
        Route::get('create', 'PermissionController@create')->name('admin.permission.create')->middleware('permission:thêm phân quyền');
        Route::get('get_actions', 'PermissionController@get_actions')->name('admin.permission.get_actions')->middleware('permission:thêm phân quyền');
        Route::post('store', 'PermissionController@store')->name('admin.permission.store')->middleware('permission:thêm phân quyền');
    });

    Route::prefix('setting')->group(function () {
        Route::get('index', function () {
            return view('admin.setting.index');
        })->name('admin.setting.index')->middleware('permission:list setting');
        Route::get('data', 'SettingController@index')->name('admin.setting.data');
        Route::post('store', 'SettingController@store')->name('admin.setting.store')->middleware('permission:add setting');
        Route::post('update/{id}', 'SettingController@update')->name('admin.setting.update')->middleware('permission:edit setting');
    });
});
