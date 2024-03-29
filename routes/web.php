<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\login\LoginController;
use App\Http\Controllers\wilayah\KotaController;
use App\Http\Controllers\kios\KiosShopController;
use App\Http\Controllers\kios\KiosDashboardProduk;
use App\Http\Controllers\kios\KiosKasirController;
use App\Http\Controllers\kios\KiosPaymentController;
use App\Http\Controllers\kios\KiosProductController;
use App\Http\Controllers\employee\EmployeeController;
use App\Http\Controllers\kios\KiosKomplainController;
use App\Http\Controllers\kios\KiosSupplierController;
use App\Http\Controllers\wilayah\KecamatanController;
use App\Http\Controllers\wilayah\KelurahanController;
use App\Http\Controllers\kios\DashboardKiosController;
use App\Http\Controllers\kios\KiosDailyRecapController;
use App\Http\Controllers\kios\KiosPengirimanController;
use App\Http\Controllers\kios\KiosShopSecondController;
use App\Http\Controllers\customer\AddCustomerController;
use App\Http\Controllers\customer\DataCustomerController;
use App\Http\Controllers\kios\KiosProductSecondController;
use App\Http\Controllers\kios\AddKelengkapanKiosController;
use App\Http\Controllers\kios\KiosBuatPaketSecondController;
use App\Http\Controllers\kios\KiosPenerimaanProdukController;
use App\Http\Controllers\customer\DashboardCustomerController;
use App\Http\Controllers\kios\KiosFilterProdukSecondController;
use App\Http\Controllers\kios\KiosPengecekkanProdukBaruController;
use App\Http\Controllers\kios\KiosPengecekkanSecondController;
use App\Http\Controllers\logistik\LogistikDashboardController;
use App\Http\Controllers\logistik\LogistikPenerimaanController;
use App\Http\Controllers\logistik\LogistikValidasiProdukController;

Route::middleware('guest')->group(function (){
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('form-login');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dependent Dropdown 
Route::get('/getKota/{provinsiId}', [KotaController::class, 'getKota']);
Route::get('/getKecamatan/{kotaId}', [KecamatanController::class, 'getKecamatan']);
Route::get('/getKelurahan/{kecamatanId}', [KelurahanController::class, 'getKelurahan']);

Route::middleware('superadmin')->group(function () {
    Route::get('/', function() {
        return view('admin.index');
    });
});

Route::middleware('kios')->group(function () {
    Route::prefix('/kios')->group(function () {
        
        Route::prefix('/analisa')->group(function () {
            Route::get('/dashboard', [DashboardKiosController::class, 'index']);
        });

        Route::prefix('/customer')->group(function () {
            Route::resource('/daily-recap', KiosDailyRecapController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::post('/daily-recap', [KiosDailyRecapController::class, 'store'])->name('form-daily-recap');
        });

        Route::prefix('/product')->group(function () {
            Route::get('/dashboard-produk', [KiosDashboardProduk::class, 'index']);

            Route::group(['controller' => KiosProductController::class], function () {
                Route::resource('/list-product', KiosProductController::class)->only(['index', 'update', 'destroy']);
                Route::post('/update-srp-baru', 'updateSrpBaru');
                Route::get('/get-paket-penjualan/{paketPenjualanId}', 'getPaketPenjualan');
            });

            Route::group(['controller' => KiosProductSecondController::class], function () {
                Route::resource('/list-product-second', KiosProductSecondController::class);
                Route::post('/update-srp-second', 'updateSRPSecond');
            });
            
            Route::resource('/shop', KiosShopController::class);
            Route::post('/shop', [KiosShopController::class, 'store'])->name('form-belanja');
            
            Route::group(['controller' => KiosShopSecondController::class], function () {
                Route::resource('/shop-second', KiosShopSecondController::class);
                Route::post('/validasiSecond/{id}', 'validasisecond')->name('validasi-payment-second');
                Route::get('/get-kelengkapan-second/{jenisId}', 'getKelengkapanSecond');
                Route::get('/getCustomerbyNomor/{nomor}', 'getCustomerbyNomor');
                Route::get('/getAdditionalKelengkapan/{id}', 'getAdditionalKelengkapan');
            });
            
            Route::group(['controller' => KiosPaymentController::class], function () {
                Route::resource('/pembayaran', KiosPaymentController::class)->only(['index', 'update']);
                Route::post('/pembayaran/{id}', 'validasi')->name('form-validasi-payment');
                Route::put('/api/updatePayment/{id}', 'updatePayment');
            });
            
            Route::resource('/pengiriman', KiosPengirimanController::class)->only(['index', 'update']);
            Route::get('/getLayanan/{ekspedisiId}', [KiosPengirimanController::class, 'getLayanan']);
            
            Route::resource('/supplier', KiosSupplierController::class);
            
            Route::group(['controller' => AddKelengkapanKiosController::class], function () {
                Route::get('/add-product', 'index');
                Route::post('/add-product', 'store')->name('form-kelengkapan');
                Route::get('/getKelengkapan/{jenisId}', 'getKelengkapan');
            });
            
            Route::group(['controller' => KiosBuatPaketSecondController::class], function () {
                Route::resource('add-paket-penjualan-second', KiosBuatPaketSecondController::class)->only(['index', 'store']);
                Route::get('/getKelengkapanSecond', 'getKelengkapanSecond');
                Route::get('/getSNSecond/{id}', 'getSNSecond');
                Route::get('/getPriceSecond/{id}', 'getPriceSecond');
            });
            
            Route::resource('/penerimaan-produk', KiosPenerimaanProdukController::class)->only(['index', 'update']);
            
            Route::resource('/pengecekkan-produk-second', KiosPengecekkanSecondController::class)->names([
                'edit' => 'pengecekkan-produk-second.quality-control',
            ]);

            Route::resource('/filter-product-second', KiosFilterProdukSecondController::class);
            
            Route::resource('/qc-produk-baru', KiosPengecekkanProdukBaruController::class)->only(['index', 'store']);
            Route::get('/getOrderList/{orderId}', [KiosPengecekkanProdukBaruController::class, 'getOrderList']);
            Route::get('/getQtyOrderList/{orderListId}', [KiosPengecekkanProdukBaruController::class, 'getQtyOrderList']);
            
            Route::resource('/komplain', KiosKomplainController::class)->only(['index', 'update']);
        });

        Route::prefix('/kasir')->group(function () {
            Route::group(['controller' => KiosKasirController::class], function () {
                Route::resource('/kasir', KiosKasirController::class)->only(['index', 'store']);
                Route::get('/autocomplete/{jenisTransaksi}', 'autocomplete');
                Route::get('/getSerialNumber/{jenisTransaksi}/{id}', 'getSerialNumber');
                Route::get('/getCustomer/{customerId}', 'getCustomer');
            });
        });

    });
});

Route::middleware('logistik')->group(function () {
    Route::prefix('/logistik')->group(function () {
        Route::get('/', [LogistikDashboardController::class, 'index']);
        Route::resource('/penerimaan', LogistikPenerimaanController::class)->only(['index', 'update']);
        Route::resource('/validasi', LogistikValidasiProdukController::class)->only(['index', 'store']);
        Route::get('/getOrderList/{orderId}', [LogistikValidasiProdukController::class, 'getOrderList']);
        Route::get('/getQtyOrderList/{orderListId}', [LogistikValidasiProdukController::class, 'getQtyOrderList']);
    });
});

Route::middleware('access')->group(function () {
    Route::prefix('/customer')->group(function () {
        Route::get('/', [DashboardCustomerController::class, 'index']);
        
        Route::resource('/data-customer', DataCustomerController::class)->only(['index', 'update', 'destroy']);
        Route::get('/data-customer/search', [DataCustomerController::class, 'search']);
        
        Route::get('/add-customer', [AddCustomerController::class, 'index']);
        Route::post('/add-customer', [AddCustomerController::class, 'store'])->name('form-customer');
        
        Route::middleware('admin.superAdmin')->group(function () {
            Route::get('/add-user', [EmployeeController::class, 'index']);
            Route::post('/add-user', [EmployeeController::class, 'store'])->name('form-user');
        });
    });
});



// Route::get('/gudang', function () {
//     return view('gudang.main.index');
// });
// Route::get('gudang/sender', function () {
//     return view('gudang.sender.main');
// });
// Route::get('gudang/belanja', function () {
//     return view('gudang.shop.belanja.belanja');
// });
// Route::get('gudang/req-payment', function () {
//     return view('gudang.shop.payment');
// });
// Route::get('gudang/input-resi', function () {
//     return view('gudang.shop.resi');
// });
// Route::get('gudang/unboxing', function () {
//     return view('gudang.checkpart.unboxing');
// });
// Route::get('gudang/quality-control', function () {
//     return view('gudang.checkpart.qc.quality_control');
// });
// Route::get('gudang/quality-control/detail', function () {
//     return view('gudang.checkpart.detail');
// });
// Route::get('gudang/validasi', function () {
//     return view('gudang.checkpart.validasi');
// });
// Route::get('gudang/stock-opname', function () {
//     return view('gudang.stockopname.stock_opname');
// });
// Route::get('gudang/inventory', function () {
//     return view('gudang.main.inventory');
// });
// Route::get('gudang/inventory/tambah-sku', function () {
//     return view('gudang.main.add_sku');
// });


// Route::get('/battery', function () {
//     return view('battery.main.index');
// });

// Route::get('/content', function () {
//     return view('content.main.index');
// });


// Route::get('/repair', function () {
//     return view('repair.main.index');
// });


// Route::get('/logistik', function () {
//     return view('logistik.main.index');
// });
