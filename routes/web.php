<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    SocialAuthController,
    Auth\VerifyEmailController,
    ProductController,
    CartController,
    CheckoutController,
    OrderController,
    AttributeController

};
use App\Http\Middleware\CheckRole;
use App\Livewire\Pages\Auth\{ForgotPassword, ResetPassword, VerifyEmail, ConfirmPassword};
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Mail;
use App\Livewire\Layout\NewsletterSubscription;



// ------------------------------------------------------
// Utility Routes (admin/employee only)
// ------------------------------------------------------
Route::prefix('utilities')->middleware(['auth', CheckRole::class . ':admin,employee'])->group(function () {
    Route::get('/generate-invoice/{orderId}', function ($orderId) {
        $order = App\Models\Order::find($orderId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        try {
            App\Jobs\GenerateInvoiceJob::dispatchSync($order);
            return response()->json(['message' => 'Invoice generation completed']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });


    Route::get('/send-email/{orderId}', function ($orderId) {
        $order = App\Models\Order::find($orderId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Calculate necessary values
        $subtotalExcludingVat = $order->products->sum(function ($product) {
            return $product->priceWithoutVat() * $product->pivot->quantity;
        });

        $totalVat = $order->products->sum(function ($product) {
            return $product->vatAmount() * $product->pivot->quantity;
        });

        $shipping = $order->shipping_cost ?? 0;

        // Dispatch the job with all required arguments
        App\Jobs\SendOrderConfirmationEmailJob::dispatchSync($order);

        return response()->json(['message' => 'Email dispatch job queued']);
    });


    Route::get('/send-test-email', function () {
        Mail::raw('This is a test email.', function ($message) {
            $message->to('recipient@example.com')->subject('Test Email');
        });
        return 'Test email sent!';
    });

    Route::get('/test-pdf', function () {
        $pdf = PDF::loadView('pdfs.test', ['data' => 'Test Data']);
        return $pdf->download('test.pdf');
    });
});




// ------------------------------------------------------
// Public Routes
// ------------------------------------------------------
Route::middleware(['web'])->group(function () {
    // Home and About
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/despre-noi', [\App\Http\Controllers\HomeController::class, 'about'])->name('about');
    Route::get('/politica-livrare', [\App\Http\Controllers\HomeController::class, 'politicaLivrare'])->name('politica-livrare');
    Route::get('/politica-retur', [\App\Http\Controllers\HomeController::class, 'politicaRetur'])->name('politica-retur');
    Route::get('/politica-confidentialitate', [\App\Http\Controllers\HomeController::class, 'politicaConfidentialitate'])->name('politica-confidentialitate');
    Route::get('/politica-gdpr', [\App\Http\Controllers\HomeController::class, 'politicaGdpr'])->name('politica-gdpr');
    Route::get('/termeni-conditii', [\App\Http\Controllers\HomeController::class, 'termeniConditii'])->name('termeni-conditii');

    Route::get('/sitemap.xml', [\App\Http\Controllers\HomeController::class, 'sitemap'])->name('sitemap');
    Route::post('/newsletter/subscribe', [NewsletterSubscription::class, 'subscribe'])->name('newsletter.subscribe');
//    Route::get('/api/attributes/{id}/values', [AttributeController::class, 'getValues']);
//    Route::post('/api/attributes', [AttributeController::class, 'store']);
//    Route::post('/api/attributes/{id}/values', [AttributeController::class, 'addValue']);

});

// ------------------------------------------------------
// Cart and Product Routes (Available to Everyone)
// ------------------------------------------------------
Route::middleware(['web'])->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add-standard', [CartController::class, 'addStandardProduct'])->name('cart.add.standard');
    Route::post('/cart/add-custom', [CartController::class, 'addCustomProduct'])->name('cart.add.custom');
    Route::patch('/cart/{id}', [CartController::class, 'updateCart'])->name('cart.update');
    Route::patch('/cart/custom/{id}', [CartController::class, 'updateCustom'])->name('cart.update.custom');
    Route::delete('/cart/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');


    Route::post('/set-cart-transfer', function () {
        session(['eligible_to_transfer_cart' => true]);
        return redirect()->route('login');
    })->name('set.cart.transfer');

    // Products
    Route::get('/produse/{slug}', [ProductController::class, 'showByCategory'])->name('products.category');
    Route::get('/produs/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/api/get-filters', [ProductController::class, 'getAvailableFilters'])->name('api.getFilters');

});

// ------------------------------------------------------
// Authentication Routes
// ------------------------------------------------------
Route::middleware('guest', 'web')->group(function () {
    // Login & Register
    Volt::route('login', 'pages.auth.login')->name('login');
    Volt::route('register', 'pages.auth.register')->name('register');

    // Password Reset
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

    // Social Authentication
    Route::get('/login/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('social.login');
    Route::get('/login/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
});

// ------------------------------------------------------
// Authenticated Routes
// ------------------------------------------------------
Route::middleware(['auth', 'verified', 'web'])->group(function () {
    // Email Verification
    Route::get('verify-email', VerifyEmail::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::get('confirm-password', ConfirmPassword::class)->name('password.confirm');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    Route::post('/address', [CheckoutController::class, 'storeAddress'])->name('address.store');
    Route::post('/invoice-address', [CheckoutController::class, 'storeInvoiceAddress'])->name('invoice_address.store');

    Route::patch('/address/{id}/default', [CheckoutController::class, 'setDefaultAddress'])->name('address.default');
    Route::patch('/invoice-address/{id}/default', [CheckoutController::class, 'setDefaultInvoiceAddress'])->name('invoice_address.default');

    Route::patch('/address/{id}/edit', [CheckoutController::class, 'updateAddress'])->name('address.update');
    Route::delete('/address/{id}', [CheckoutController::class, 'destroyAddress'])->name('address.destroy');

    Route::patch('/invoice-address/{id}/edit', [CheckoutController::class, 'updateInvoiceAddress'])->name('invoice_address.update');
    Route::delete('/invoice-address/{id}', [CheckoutController::class, 'destroyInvoiceAddress'])->name('invoice_address.destroy');

    Route::post('/checkout/apply-voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.apply-voucher');

    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    // Account Routes (Client-Specific)
    Route::prefix('account')->middleware(CheckRole::class . ':client')->group(function () {
        Route::get('/', \App\Livewire\Account\AccountController::class)->name('account.index');
        Route::get('/change-password', \App\Livewire\Account\ChangePassword::class)->name('account.change-password');
        Route::get('/my-orders', \App\Livewire\Account\MyOrders::class)->name('account.my-orders');
        Route::get('/favorites', \App\Livewire\Account\MyFavorites::class)->name('favorites.index');
    });

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});

// ------------------------------------------------------
// Admin and Role-Specific Routes
// ------------------------------------------------------

Route::prefix('admin')->middleware(['auth', CheckRole::class . ':admin,employee'])->group(function () {
    Route::get('/', \App\Livewire\AdminDashboardController::class)->name('admin.dashboard');

    // Categories
    Route::get('/categories', \App\Livewire\Categories\Crud::class)->name('admin.categories');

    // Product Management with Separate Components
    Route::get('/products', \App\Livewire\Products\ProductList::class)->name('admin.products');
    Route::get('/products-emga', \App\Livewire\Products\ProductListEmag::class)->name('admin.products.emag');
    Route::get('/products/create', \App\Livewire\Products\ProductCreate::class)->name('admin.products.create');
    Route::get('/products/{id}/edit', \App\Livewire\Products\ProductEdit::class)->name('admin.products.edit');

    // Filtering Page for Admin Products
    Route::get('/products/filter', \App\Livewire\Products\ProductFilter::class)->name('admin.products.filter');

    // Orders
    Route::get('/orders', \App\Livewire\Orders\Crud::class)->name('admin.orders');

    // Account Settings
    Route::get('/change-password', \App\Livewire\Account\ChangePassword::class)->name('admin.change-password');
});
