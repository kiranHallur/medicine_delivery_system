<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("test-form", function(){
  return view('test.form');
});

Route::post("test-form/save", "Test@testSave");

$prepend = "Frontend\Home@";
Route::get('', $prepend.'index')->name('frontend.home'); 

$prepend = "Register\Register@";
Route::get('register', $prepend.'index')->name('register');
Route::post('register/save', $prepend.'store')->name('register.store');

$prepend = "User\Login@";
Route::get('login', $prepend.'index')->name('login');
Route::post('login/verify', $prepend.'verify')->name('login.verify');
Route::get('logout', $prepend.'logout')->name('logout');
Route::post('backend/login/verify', $prepend.'verify')->name('backend.login.verify');


Route::get('forgot-password/{user_id?}', $prepend . 'forgotPassword')->name('forgot-password');
Route::post('email-reset-password-link', $prepend . 'mailResetPasswordLink')->name('email-reset-password-link');
Route::get('reset-password/{user_id}', $prepend . 'resetPasswordForm')->name('reset-password');
Route::post('reset-password/save', $prepend . 'saveResetPasswordForm')->name('reset-password-save');


Route::group(['middleware' => ['session.auth']], function () {
    
  $prepend = "Product\Product@";
  Route::get('products', $prepend.'index')->name('products.list');
  Route::get('product/create', $prepend.'create')->name('product.create'); 
  Route::post('product/store', $prepend.'store')->name('product.store');
  Route::get('product/edit/{pk}', $prepend.'edit')->name('product.edit');
  Route::post('product/update', $prepend.'update')->name('product.update');
  Route::post('product/remove/{pk}', $prepend.'destroy')->name('product.destroy');

  $prepend = "Stock\Stock@";
  Route::get('stocks', $prepend.'index')->name('stocks');
  Route::get('stock/create', $prepend.'create')->name('stock.create');
  Route::post('stock/save', $prepend.'store')->name('stock.store');
  Route::get('stock/edit/{pk}', $prepend.'edit')->name('stock.edit');
  Route::post('stock/update', $prepend.'update')->name('stock.update');
  Route::post('stock/remove', $prepend.'destroyStock')->name('stock.destroy'); 

  Route::post('stock/item/update', $prepend.'updateItem')->name('stock.item.update');
  Route::post('stock/item/remove', $prepend.'removeItem')->name('stock.item.destroy');
   
  $prepend = "Order\Order@";
  Route::get('orders/list', $prepend.'index')->name('orders'); 
  Route::get('order/create', $prepend.'create')->name('order.create');
  Route::get('order/load-user-products/{user_id}', $prepend.'loadUserProducts')->name('order.load_user_products');
  Route::post('order/save', $prepend.'store')->name('order.store');
  Route::get('order/edit/{pk}', $prepend.'edit')->name('order.edit');
  Route::post('order/update', $prepend.'update')->name('order.update');
  Route::post('order/remove', $prepend.'destroy')->name('order.destroy');
  Route::post('order/item/remove', $prepend.'destroyItem')->name('order.item.destroy'); 

  
  Route::get('order/item-returns', $prepend.'itemReturns')->name('order.item.return.list'); 
  Route::get('order/item-return/{order_item_id}', $prepend.'itemReturnForm')->name('order.item.return.form'); 
  Route::post('order/item-return/save', $prepend.'itemReturnStore')->name('order.item.return.store'); 


  Route::get('order/item-cancel/{order_item_id}', $prepend.'itemCancel')->name('order.item.cancel'); 
  
  Route::get('order/cancel/{order_id}', $prepend.'cancelOrder')->name('order.cancel.form');  
  Route::post('order/cancel/save', $prepend.'cancelOrderStore')->name('order.cancel.store'); 
  Route::get('order/cancel-or-return/list', $prepend.'orderCancelOrReturn')->name('order.cancel_or_return.list'); 
  Route::post('order/cancel-or-return/store', $prepend.'orderCancelOrReturnStore')->name('order.cancel_or_return.store'); 

  $prepend = "Supply\Supply@";
  Route::get('supply', $prepend.'index')->name('supply');
  Route::get('supply/edit/{pk}', $prepend.'edit')->name('supply.edit');
  Route::post('supply/update', $prepend.'update')->name('supply.update');
  Route::post('supply/item/deduct', $prepend.'itemDeduct')->name('supply.item.deduct');
  Route::post('supply/item/rollback', $prepend.'orderItemRollBack')->name('supply.item.rollback');

  //user profile
  $prepend = "User\Profile@";
  Route::get('profile/edit', $prepend.'edit')->name('user.profile.edit');
  Route::post('profile/update', $prepend.'update')->name('user.profile.update');
  Route::post('user/change-password/update', $prepend.'changePassword')->name('user.change-password.update');
  Route::post('user/shop/update', $prepend.'shopUpdate')->name('user.shop.update');

  $prepend = "User\User@";
  Route::get('users', $prepend.'index')->name('users');
  Route::get('user/show-info/{pk}', $prepend.'show')->name('user.show-info');
  Route::post('user/status/save', $prepend.'statusStore')->name('user.status.store');
});

