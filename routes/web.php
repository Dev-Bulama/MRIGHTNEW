<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Shop\PayoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AdminController;
use App\Services\NigeriaData;


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

// Landing Page
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Route::get('/', function () {
    // If user is authenticated, redirect to dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    
    return view('welcome');
})->name('home');

// Public verification route (no auth required)
Route::get('/verify/{receiptNumber}', [ReceiptController::class, 'publicVerify'])->name('public.verify');
Route::get('/public/verify/{receiptNumber}', [ReceiptController::class, 'publicVerify'])->name('public.receipt.verify');
// Public search routes (no auth required)  
Route::prefix('public')->name('public.')->group(function () {
    Route::post('/search', [SearchController::class, 'publicSearch'])->name('search');
    Route::get('/receipt/{receiptNumber}', [ReceiptController::class, 'publicView'])->name('receipt');
});
// Authentication Routes
Route::middleware('guest')->group(function () {
    // Registration Routes
    Route::get('/register', [RegisteredUserController::class, 'create'])
                ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // Login Routes
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');
  // Admin Pre-Approval Management
Route::prefix('admin/pre-approvals')->name('admin.pre-approvals.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\PreApprovalController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\PreApprovalController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\PreApprovalController::class, 'store'])->name('store');
    Route::post('/import', [App\Http\Controllers\Admin\PreApprovalController::class, 'import'])->name('import');
    Route::get('/export', [App\Http\Controllers\Admin\PreApprovalController::class, 'export'])->name('export');
    Route::get('/template', [App\Http\Controllers\Admin\PreApprovalController::class, 'template'])->name('template');
    Route::delete('/{preApproval}', [App\Http\Controllers\Admin\PreApprovalController::class, 'destroy'])->name('destroy');
});
//Route::post('/receipt/search-resale', [ReceiptController::class, 'searchForResale'])->name('receipt.search-resale');
    
// Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])
                ->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
                ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
                ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
                ->name('profile.destroy');

    Route::get('/logout', function () {
    return redirect()->route('login')->with('info', 'Please use the logout button to sign out.');
})->name('logout.get');
    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
Route::post('/webhook/paystack', [PaymentController::class, 'webhook'])->name('webhook.paystack');
// Phase 3: Shop Management & Receipt Generation Routes
Route::middleware('auth')->group(function () {
    // Shop Management Routes - Updated for Phase 3
    // Shop Management Routes (for shop owners)
// ================================
// SHOP ROUTES - PROPERLY ORDERED
// ================================

Route::middleware(['auth'])->prefix('shop')->name('shop.')->group(function () {
    
    // Static routes FIRST (before dynamic routes)
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/create', [ShopController::class, 'create'])->name('create');
    Route::post('/', [ShopController::class, 'store'])->name('store');
    Route::get('/payments', [ShopController::class, 'payments'])->name('payments.index');
    
    // PAYOUT ROUTES - Specific routes before dynamic ones
    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [App\Http\Controllers\Shop\PayoutController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Shop\PayoutController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Shop\PayoutController::class, 'store'])->name('store');
        Route::get('/export', [App\Http\Controllers\Shop\PayoutController::class, 'export'])->name('export');
        Route::get('/{payoutRequest}', [App\Http\Controllers\Shop\PayoutController::class, 'show'])->name('show');
    });
    
    // Dynamic routes LAST (these will catch anything not matched above)
    Route::get('/{shop}/edit', [ShopController::class, 'edit'])->name('edit');
    Route::put('/{shop}', [ShopController::class, 'update'])->name('update');
    Route::patch('/{shop}', [ShopController::class, 'update'])->name('update.patch');
    Route::get('/{shop}', [ShopController::class, 'show'])->name('show');
});
 
Route::prefix('receipt')->name('receipt.')->group(function () {
    Route::get('/', [ReceiptController::class, 'index'])->name('index');
    Route::get('/create', [ReceiptController::class, 'create'])->name('create');
    Route::post('/create', [ReceiptController::class, 'store'])->name('store');
    Route::get('/resale', [ReceiptController::class, 'resaleForm'])->name('resale');
        Route::post('/resale/process', [ReceiptController::class, 'processResaleWithPayment'])->name('resale.process-payment');
    Route::post('/resale', [ReceiptController::class, 'processResale'])->name('resale.process');
    Route::post('/search-resale', [ReceiptController::class, 'searchResale'])->name('search-resale');
    Route::get('/{receipt}', [ReceiptController::class, 'show'])->name('show');
    Route::get('/{receipt}/view', [ReceiptController::class, 'view'])->name('view');
    Route::get('/{receipt}/download', [ReceiptController::class, 'download'])->name('download');
    Route::get('/{receipt}/print', [ReceiptController::class, 'print'])->name('print');
    Route::patch('/{receipt}/update-status', [ReceiptController::class, 'updateStatus'])->name('update-status');
    // Route::post('/forgot-resale-code', [ReceiptController::class, 'forgotResaleCode'])->name('forgot-resale-code');
// Add this exact line

Route::post('/receipts/{receipt}/update-payment-status', [ReceiptController::class, 'updatePaymentStatus'])
    ->name('receipt.update-payment-status')
    ->middleware(['auth', 'verified']);
    });
    // Search Routes - Updated for Phase 3
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::post('/query', [SearchController::class, 'search'])->name('query');
        Route::get('/verify', [SearchController::class, 'verifyForm'])->name('verify');
        Route::post('/verify', [SearchController::class, 'verify'])->name('verify.process');
        Route::get('/quick', [SearchController::class, 'quickSearch'])->name('quick');
        Route::post('/quick', [SearchController::class, 'processQuickSearch'])->name('quick.process');
        Route::get('/advanced', [SearchController::class, 'advancedSearch'])->name('advanced');
        Route::post('/advanced', [SearchController::class, 'processAdvancedSearch'])->name('advanced.process');
    });

    // Performance Routes - Updated for Phase 3
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [DashboardController::class, 'performance'])->name('index');
        Route::get('/receipts', [DashboardController::class, 'receiptAnalytics'])->name('receipts');
        Route::get('/revenue', [DashboardController::class, 'revenueAnalytics'])->name('revenue');
        Route::get('/export', [DashboardController::class, 'exportData'])->name('export');
    });

    // Customer Routes - Updated for Phase 3 
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/receipts', [ReceiptController::class, 'customerReceipts'])->name('receipts');
        Route::get('/report-issue', [ReceiptController::class, 'reportIssueForm'])->name('report-issue');
        Route::post('/report-issue', [ReceiptController::class, 'reportIssue'])->name('report-issue.process');
        Route::get('/track/{receiptNumber}', [ReceiptController::class, 'trackReceipt'])->name('track');
    });

    // Help Routes - Enhanced
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', function () {
            return view('help.index');
        })->name('index');
        
        Route::get('/setup', function () {
            return view('help.setup');
        })->name('setup');
        
        Route::get('/customer', function () {
            return view('help.customer');
        })->name('customer');
        
        Route::get('/guides', function () {
            return view('help.guides');
        })->name('guides');
        
        Route::get('/contact', function () {
            return view('help.contact');
        })->name('contact');
        
        Route::get('/faq', function () {
            return view('help.faq');
        })->name('faq');
    });

    // Documentation Routes
    Route::prefix('mright')->name('mright.')->group(function () {
        Route::get('/', function () {
            return redirect()->away('https://www.mright.com.ng');
        })->name('index');
    });

    // Notifications Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('dashboard')->with('info', 'Notifications coming soon');
        })->name('index');
        
        Route::post('/mark-read', function () {
            return response()->json(['success' => true]);
        })->name('mark-read');
    });

    // Activity Routes
    Route::prefix('activity')->name('activity.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('dashboard')->with('info', 'Activity log coming soon');
        })->name('index');
    });

    // Payment Routes
    // Payment Management Routes
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::post('/update-status/{receipt}', [PaymentController::class, 'updateStatus'])->name('update-status');
        Route::post('/bulk-update-status', [PaymentController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
    });

    // Dashboard AJAX Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/refresh', function () {
            return response()->json([
                'stats' => [
                    'monthly_receipts' => rand(10, 50),
                    'yearly_receipts' => rand(100, 500),
                    'pending_payments' => rand(0, 10),
                ],
                'activities' => '<div class="text-center py-3"><small class="text-muted">Updated ' . now()->format('g:i A') . '</small></div>'
            ]);
        })->name('refresh');
        
        Route::get('/shop-stats', function () {
            return response()->json([
                'monthly_receipts' => rand(10, 50),
                'yearly_receipts' => rand(100, 500),
                'total_earnings' => rand(5000, 25000),
                'pending_payments' => rand(0, 10),
            ]);
        })->name('shop-stats');
        
        Route::get('/export', function () {
            return redirect()->route('dashboard')->with('info', 'Export functionality coming soon');
        })->name('export');
    });

    

// API Routes for AJAX calls - Phase 3
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/search/suggestions', [SearchController::class, 'searchSuggestions'])->name('search.suggestions');
    Route::get('/search/dashboard', [SearchController::class, 'dashboardSearch'])->name('search.dashboard');  // ADD THIS LINE
    Route::get('/receipt/{receipt}/status', [ReceiptController::class, 'getStatus'])->name('receipt.status');
    Route::post('/receipt/{receipt}/resend-email', [ReceiptController::class, 'resendEmail'])->name('receipt.resend-email');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
});



});
Route::prefix('admin')->name('admin.')->group(function () {
    // Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    // This is CORRECT
Route::get('/users/{user}', [AdminController::class, 'userDetails']);

});
// Admin Routes (Protected)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
   
    
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/export', [AdminController::class, 'export'])->name('dashboard.export');

    // Shop Management - Use the proper controller methods
    Route::prefix('shops')->name('shops.')->group(function () {
        Route::get('/', [AdminController::class, 'shopIndex'])->name('index');
        Route::get('/{shop}', [AdminController::class, 'shopShow'])->name('show');
        Route::get('/{shop}/details', [AdminController::class, 'shopDetails'])->name('details');
        Route::post('/{shop}/approve', [AdminController::class, 'approveShop'])->name('approve');
        Route::post('/{shop}/reject', [AdminController::class, 'rejectShop'])->name('reject');
        Route::post('/{shop}/suspend', [AdminController::class, 'suspendShop'])->name('suspend');
        Route::post('/{shop}/reinstate', [AdminController::class, 'reinstateShop'])->name('reinstate');
        Route::delete('/{shop}', [AdminController::class, 'deleteShop'])->name('delete');
        Route::post('/bulk-delete', [AdminController::class, 'bulkDeleteShops'])->name('bulk-delete');
        Route::post('/approve-all', [AdminController::class, 'bulkApproveShops'])->name('approve-all');
        Route::post('/bulk-approve', [AdminController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [AdminController::class, 'bulkReject'])->name('bulk-reject');
    });
 // System Settings Routes - ADD THESE
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminController::class, 'settings'])->name('index');
        Route::post('/update-payment', [AdminController::class, 'updatePaymentSettings'])->name('update-payment');
        Route::post('/test-paystack', [AdminController::class, 'testPaystackConnection'])->name('test-paystack');
    });
    // User Management
// User Management Routes
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [AdminController::class, 'users'])->name('index');
    Route::post('/import', [AdminController::class, 'importUsers'])->name('import');
    
    // Individual user actions
    Route::get('/{user}/details', [AdminController::class, 'userDetails'])->name('details');
    Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
    Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
    Route::get('/{user}/contact', [AdminController::class, 'contactUser'])->name('contact');
    Route::post('/{user}/send-message', [AdminController::class, 'sendUserMessage'])->name('send-message');
    
    // User status actions
    Route::post('/{user}/activate', [AdminController::class, 'activateUser'])->name('activate');
    Route::post('/{user}/suspend', [AdminController::class, 'suspendUser'])->name('suspend');
    Route::delete('/{user}', [AdminController::class, 'deleteUser'])->name('delete');
    
    // Bulk actions
    Route::post('/bulk-activate', [AdminController::class, 'bulkActivateUsers'])->name('bulk-activate');
    Route::post('/bulk-suspend', [AdminController::class, 'bulkSuspendUsers'])->name('bulk-suspend');
    Route::post('/bulk-send_welcome', [AdminController::class, 'bulkSendWelcomeUsers'])->name('bulk-send_welcome');
    Route::post('/bulk-delete', [AdminController::class, 'bulkDeleteUsers'])->name('bulk-delete');
});
// Union Management Routes
    Route::prefix('unions')->name('unions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UnionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\UnionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\UnionController::class, 'store'])->name('store');
        Route::get('/{union}', [App\Http\Controllers\Admin\UnionController::class, 'show'])->name('show');
        Route::get('/{union}/edit', [App\Http\Controllers\Admin\UnionController::class, 'edit'])->name('edit');
        Route::put('/{union}', [App\Http\Controllers\Admin\UnionController::class, 'update'])->name('update');
        Route::delete('/{union}', [App\Http\Controllers\Admin\UnionController::class, 'destroy'])->name('destroy');
        
        // AJAX Routes
        Route::post('/get-lgas', [App\Http\Controllers\Admin\UnionController::class, 'getLgas'])->name('get-lgas');
        Route::post('/{union}/assign-shop-owners', [App\Http\Controllers\Admin\UnionController::class, 'assignToShopOwners'])->name('assign-shop-owners');
    });
     // Role Management Routes  
 // Role Management Routes  
Route::prefix('roles')->name('roles.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
    Route::get('/export', [App\Http\Controllers\Admin\RoleController::class, 'export'])->name('export');
    Route::get('/{role}', [App\Http\Controllers\Admin\RoleController::class, 'show'])->name('show');
    Route::get('/{role}/edit', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
    Route::put('/{role}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
    Route::delete('/{role}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
});
     // Payout Management Routes
    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('index');
        Route::get('/{payoutRequest}', [App\Http\Controllers\Admin\PayoutController::class, 'show'])->name('show');
        Route::post('/{payoutRequest}/approve', [App\Http\Controllers\Admin\PayoutController::class, 'approve'])->name('approve');
        Route::post('/{payoutRequest}/reject', [App\Http\Controllers\Admin\PayoutController::class, 'reject'])->name('reject');
        Route::post('/{payoutRequest}/mark-paid', [App\Http\Controllers\Admin\PayoutController::class, 'markPaid'])->name('mark-paid');
        Route::get('/export/{status?}', [App\Http\Controllers\Admin\PayoutController::class, 'export'])->name('export');
    });
   


    // Receipt Management
Route::prefix('receipts')->name('receipts.')->group(function () {
    Route::get('/', [AdminController::class, 'receipts'])->name('index');
    Route::get('/{receipt}/details', [AdminController::class, 'receiptDetails'])->name('details');
    Route::get('/{receipt}/download', [AdminController::class, 'downloadReceipt'])->name('download');
    Route::post('/{receipt}/check-status', [AdminController::class, 'checkPaymentStatus'])->name('check-status');
    Route::post('/{receipt}/resend', [AdminController::class, 'resendReceipt'])->name('resend');
});

  

    // Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [AdminController::class, 'reports'])->name('index');
    Route::get('/generate/{reportType}', [AdminController::class, 'generateReportForm'])->name('generate');
    Route::post('/process', [AdminController::class, 'processReport'])->name('process');
    Route::get('/view/{reportId}', [AdminController::class, 'viewReport'])->name('view');
    Route::get('/download/{reportId}', [AdminController::class, 'downloadReport'])->name('download');
    Route::delete('/delete/{reportId}', [AdminController::class, 'deleteReport'])->name('delete');
    Route::get('/locations', function () {
        return view('admin.reports.locations');
    })->name('locations');
});
    // Payment Management
  // Payment Management
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [AdminController::class, 'payments'])->name('index');
    Route::get('/{transaction}/details', [AdminController::class, 'transactionDetails'])->name('details');
    Route::post('/{transaction}/refund', [AdminController::class, 'initiateRefund'])->name('refund');
    Route::post('/{transaction}/check-status', [AdminController::class, 'checkTransactionStatus'])->name('check-status');
    Route::post('/reconcile', [AdminController::class, 'reconcilePayments'])->name('reconcile');
});

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', function () {
            return view('admin.settings.index');
        })->name('index');
    });

    // System Health
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/health', function () {
            return view('admin.system.health');
        })->name('health');
    });
});

// Registration validation routes
Route::post('/auth/check-pre-approval', [App\Http\Controllers\Auth\RegistrationValidationController::class, 'checkPreApproval']);
// Shop Payment Overview
Route::prefix('shop/payments')->name('shop.payments.')->middleware('auth')->group(function () {
    Route::get('/', function() {
        return view('shop.payments.index');
    })->name('index');
});

//Webhook routes (no auth required) - Phase 4 Enhanced
Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::post('/paystack', [WebhookController::class, 'paystack'])->name('paystack');
    Route::post('/antitheft', [WebhookController::class, 'antitheft'])->name('antitheft');
    Route::post('/sms-delivery', [WebhookController::class, 'smsDelivery'])->name('sms-delivery');
    
    // Legacy support (keep the old PaymentController webhook for backward compatibility)
    Route::post('/payment/paystack', [PaymentController::class, 'webhook'])->name('payment.paystack');
    
    // Future webhook endpoints
    Route::post('/flutterwave', function () {
        // Flutterwave webhook handler - Future implementation
        return response()->json(['status' => 'success']);
    })->name('flutterwave');
    
    Route::post('/stripe', function () {
        // Stripe webhook handler - Future implementation
        return response()->json(['status' => 'success']);
    })->name('stripe');
});
// Static Pages
Route::get('/terms', function () {
    return view('legal.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('privacy');

// Password Reset Routes - COMPLETE IMPLEMENTATION
Route::middleware('guest')->prefix('password')->name('password.')->group(function () {
    Route::get('/request', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'create'])
                ->name('request');
    Route::post('/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'store'])
                ->name('email');
    Route::get('/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'create'])
                ->name('reset');
    Route::post('/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'store'])
                ->name('store');
});

// Email Verification Routes (Future Implementation)
Route::prefix('verification')->name('verification.')->group(function () {
    Route::post('/send', function () {
        return redirect()->back()->with('success', 'Verification email sent (demo)');
    })->name('send');
});
// Payment Routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/{receipt}', [PaymentController::class, 'show'])->name('show');
    Route::post('/{receipt}/initialize', [PaymentController::class, 'initialize'])->name('initialize');
    Route::get('/verify/{reference}', [PaymentController::class, 'verify'])->name('verify');
    Route::get('/success/{reference}', [PaymentController::class, 'success'])->name('success');
    Route::get('/failed/{reference}', [PaymentController::class, 'failed'])->name('failed');
    Route::post('/{payment}/retry', [PaymentController::class, 'retry'])->name('retry');
    Route::get('/history', [PaymentController::class, 'history'])->name('history');
});
Route::post('/receipt/forgot-resale-code', [ReceiptController::class, 'forgotResaleCode'])
    ->name('receipt.forgot-resale-code')
    ->middleware(['auth', 'verified']);
Route::get('/lgas/{state}', function($state) {
    return response()->json(NigeriaData::lgas($state));
});

// Logo Management Routes (Admin only)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // System Logo Management
    Route::get('/logos', [App\Http\Controllers\Admin\LogoController::class, 'index'])->name('admin.logos.index');
    Route::post('/logos/upload-system', [App\Http\Controllers\Admin\LogoController::class, 'uploadSystemLogo'])->name('admin.logos.upload-system');
    Route::delete('/logos/delete-system', [App\Http\Controllers\Admin\LogoController::class, 'deleteSystemLogo'])->name('admin.logos.delete-system');
});

// User Logo Management Routes (All authenticated users)
// User Logo Management Routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::post('/user/logo/upload', [App\Http\Controllers\Admin\LogoController::class, 'uploadUserLogo'])->name('user.logo.upload');
    Route::delete('/user/logo/delete', [App\Http\Controllers\Admin\LogoController::class, 'deleteUserLogo'])->name('user.logo.delete');
});

// Shop Owner Payout Routes - Clean Implementation

// Shop Owner Payout Routes - Fixed Version
// Shop Owner Payout Routes - COMPLETE FIXED VERSION
// Route::middleware(['auth'])->prefix('shop/payouts')->name('shop.payouts.')->group(function () {
//     Route::get('/', [App\Http\Controllers\Shop\PayoutController::class, 'index'])->name('index');
//     Route::get('/create', [App\Http\Controllers\Shop\PayoutController::class, 'create'])->name('create');
//     Route::post('/', [App\Http\Controllers\Shop\PayoutController::class, 'store'])->name('store');
//     Route::get('/export', [App\Http\Controllers\Shop\PayoutController::class, 'export'])->name('export');
//     Route::get('/{payoutRequest}', [App\Http\Controllers\Shop\PayoutController::class, 'show'])->name('show');
// });


// Union Routes (for union users)
Route::middleware(['auth', 'user_type:union'])->prefix('union')->name('union.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Union\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [App\Http\Controllers\Union\DashboardController::class, 'exportReport'])->name('dashboard.export');
    Route::get('/dashboard/statistics', [App\Http\Controllers\Union\DashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::post('/dashboard/get-lgas', [App\Http\Controllers\Union\DashboardController::class, 'getLgas'])->name('dashboard.get-lgas');
    Route::get('/commission-history', [App\Http\Controllers\Union\DashboardController::class, 'getCommissionHistory'])->name('commission-history');
    // Shop Owner Management
    Route::prefix('shop-owners')->name('shop-owners.')->group(function () {
        Route::get('/', [App\Http\Controllers\Union\ShopOwnerController::class, 'index'])->name('index');
        Route::get('/export', [App\Http\Controllers\Union\ShopOwnerController::class, 'export'])->name('export');
        Route::get('/widget-data', [App\Http\Controllers\Union\ShopOwnerController::class, 'getWidgetData'])->name('widget-data');
        Route::post('/get-lgas', [App\Http\Controllers\Union\ShopOwnerController::class, 'getLgas'])->name('get-lgas');
        Route::get('/{shopOwner}', [App\Http\Controllers\Union\ShopOwnerController::class, 'show'])->name('show');
        Route::get('/{shopOwner}/statistics', [App\Http\Controllers\Union\ShopOwnerController::class, 'statistics'])->name('statistics');
    });
    
    // Pre-approval Management (for unions)
      // FIXED Pre-approval Management Routes
    Route::prefix('pre-approvals')->name('pre-approvals.')->group(function () {
        Route::get('/', [App\Http\Controllers\Union\PreApprovalController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Union\PreApprovalController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Union\PreApprovalController::class, 'store'])->name('store');
        Route::post('/import', [App\Http\Controllers\Union\PreApprovalController::class, 'import'])->name('import');
        Route::get('/export', [App\Http\Controllers\Union\PreApprovalController::class, 'export'])->name('export');
        Route::get('/template', [App\Http\Controllers\Union\PreApprovalController::class, 'template'])->name('template');
        Route::post('/get-lgas', [App\Http\Controllers\Union\PreApprovalController::class, 'getLgas'])->name('get-lgas');
    });
    // Route::prefix('pre-approvals')->name('pre-approvals.')->group(function () {
    //     Route::get('/', [App\Http\Controllers\Union\PreApprovalController::class, 'index'])->name('index');
    //     Route::get('/create', [App\Http\Controllers\Union\PreApprovalController::class, 'create'])->name('create');
    //     Route::post('/', [App\Http\Controllers\Union\PreApprovalController::class, 'store'])->name('store');
    //     Route::post('/import', [App\Http\Controllers\Union\PreApprovalController::class, 'import'])->name('import');
    //     Route::get('/export', [App\Http\Controllers\Union\PreApprovalController::class, 'export'])->name('export');
    //     Route::get('/template', [App\Http\Controllers\Union\PreApprovalController::class, 'template'])->name('template');
    // });
});

// Public receipt verification route
Route::get('/verify/{receiptNumber}', [ReceiptController::class, 'publicVerify'])->name('public.receipt.verify');
// Test Credentials Route (Local Environment Only)
Route::get('/test-email', function() {
    try {
        Mail::raw('Test email from M-right system', function($message) {
            $message->to('citipolytechnicofficial@gmail.com')
                   ->from(config('mail.from.address'), config('mail.from.name'))
                   ->subject('Test Email - M-right System');
        });
        
        return 'Email sent successfully!';
    } catch (Exception $e) {
        return 'Email failed: ' . $e->getMessage();
    }
});
// Union Management Routes
Route::middleware(['auth', 'verified'])->prefix('union')->name('union.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Union\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/shop-owners', [App\Http\Controllers\Union\ShopOwnerController::class, 'index'])->name('shop-owners.index');
    Route::get('/pre-approvals', [App\Http\Controllers\Union\PreApprovalController::class, 'index'])->name('pre-approvals.index');
    Route::get('/receipts', [App\Http\Controllers\Union\ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/payouts', [App\Http\Controllers\Union\PayoutController::class, 'index'])->name('payouts.index');
    Route::get('/reports', [App\Http\Controllers\Union\ReportController::class, 'index'])->name('reports.index');
});
// Union Executive Routes
Route::middleware(['auth', 'verified'])->prefix('union')->name('union.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Union\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [App\Http\Controllers\Union\DashboardController::class, 'exportReport'])->name('dashboard.export');
    
    // Union-specific management routes
    Route::get('/shop-owners', [App\Http\Controllers\Union\ShopOwnerController::class, 'index'])->name('shop-owners.index');
    Route::get('/shop-owners/{shop}', [App\Http\Controllers\Union\ShopOwnerController::class, 'show'])->name('shop-owners.show');
    Route::get('/shop-owners/{user}/statistics', [App\Http\Controllers\Union\ShopOwnerController::class, 'statistics'])->name('shop-owners.statistics');
    
    Route::get('/pre-approvals', [App\Http\Controllers\Union\PreApprovalController::class, 'index'])->name('pre-approvals.index');
    Route::post('/pre-approvals', [App\Http\Controllers\Union\PreApprovalController::class, 'store'])->name('pre-approvals.store');
    
    Route::get('/receipts', [App\Http\Controllers\Union\ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/payouts', [App\Http\Controllers\Union\PayoutController::class, 'index'])->name('payouts.index');
    Route::get('/reports', [App\Http\Controllers\Union\ReportController::class, 'index'])->name('reports.index');
});
// Help/support routes (add these if missing)
Route::prefix('help')->name('help.')->group(function () {
    Route::get('/contact', function() {
        return view('help.contact');
    })->name('contact');
});
// TEMPORARY: Test LGA route directly
Route::get('/test-union-lgas', function() {
    $user = auth()->user();
    if (!$user || !$user->isUnion()) {
        return response()->json(['error' => 'Must be logged in as union user']);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_type' => $user->user_type,
        'assigned_states' => $user->assigned_states,
        'assigned_lgas' => $user->assigned_lgas,
        'route_url' => route('union.pre-approvals.get-lgas'),
        'test_lga_call' => 'Ready to test'
    ]);
})->name('test.union.lgas');
// TEMPORARY: Test direct LGA call
Route::get('/test-direct-lgas', function() {
    $user = auth()->user();
    
    if (!$user || !$user->isUnion()) {
        return response()->json(['error' => 'Must be logged in as union user']);
    }
    
    // Simulate the POST request
    $request = new \Illuminate\Http\Request(['state' => 'Kaduna']);
    
    $controller = new \App\Http\Controllers\Union\PreApprovalController();
    
    try {
        $response = $controller->getLgas($request);
        return $response;
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});
// TEMPORARY ROUTE - Remove after fixing
// Route::get('/clear-cache-temp', function () {
//     Artisan::call('config:clear');
//     Artisan::call('cache:clear');
//     Artisan::call('route:clear');
    
//     return response()->json([
//         'message' => 'Cache cleared successfully',
//         'config_keys' => config('services.antitheft.api_keys'),
//         'env_check' => [
//             'MRIGHT_API_KEY_1' => env('MRIGHT_API_KEY_1'),
//             'MRIGHT_API_KEY_2' => env('MRIGHT_API_KEY_2'),
//             'MRIGHT_API_KEY_3' => env('MRIGHT_API_KEY_3'),
//         ]
//     ]);
// });
// // TEMPORARY DEBUG ROUTE
// Route::get('/debug-config-temp', function () {
//     return response()->json([
//         'env_variables' => [
//             'MRIGHT_API_KEY_1' => env('MRIGHT_API_KEY_1'),
//             'MRIGHT_API_KEY_2' => env('MRIGHT_API_KEY_2'),
//             'MRIGHT_API_KEY_3' => env('MRIGHT_API_KEY_3'),
//         ],
//         'config_antitheft' => config('services.antitheft'),
//         'config_api_keys' => config('services.antitheft.api_keys'),
//         'all_env_vars' => $_ENV,
//         'app_env' => app()->environment(),
//     ]);
// });
// Route::get('/test-credentials', function () {
//     if (app()->environment('local')) {
//         return view('test-credentials');
//     }
//     abort(404);
// })->name('test-credentials');