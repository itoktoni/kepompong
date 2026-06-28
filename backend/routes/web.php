<?php

use App\Http\Controllers\DashboardController;
use App\Models\Notification;
use Buki\AutoRoute\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::view('/', 'welcome')->name('home');

Route::post('/centrifugo/token', function (Request $request) {
    if (!config('langkahkecil.notification_enable')) {
        return response()->json(['token' => null, 'disabled' => true]);
    }

    if (!Auth::check()) {
        return response()->json(['token' => null, 'unauthenticated' => true], 401);
    }

    $centrifugo = app(\App\Services\CentrifugoService::class);
    $user = Auth::user();

    if ($request->input('channel')) {
        return response()->json([
            'token' => $centrifugo->generateSubscriptionToken((string) $user->id, $request->input('channel')),
        ]);
    }

    return response()->json([
        'token' => $centrifugo->generateConnectionToken((string) $user->id),
    ]);
});
Route::middleware(['auth', 'verified', 'access'])->group(function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::auto('/user', 'UsersController', ['name' => 'user']);
    Route::auto('/subscribe', 'SubscribeController', ['name' => 'subscribe']);
    Route::auto('/plan', 'PlanController', ['name' => 'plan']);
    Route::auto('/cashout', 'CashoutController', ['name' => 'cashout']);
    Route::auto('/payment', 'PaymentController', ['name' => 'payment']);
    Route::auto('/affiliate', 'AffiliateController', ['name' => 'affiliate']);
    Route::auto('/activity', 'ActivityController', ['name' => 'activity']);
    Route::auto('/addon', 'AddonWebController', ['name' => 'addon']);
    Route::auto('/discount', 'DiscountController', ['name' => 'discount']);

    Route::prefix('notifications-web')->group(function () {
        Route::get('/', function (Request $request) {
            $notifications = Notification::where('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->limit($request->input('limit', 50))
                ->get();

            $unreadCount = Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->count();

            return response()->json([
                'notifications' => $notifications->map(fn ($n) => [
                    'id' => $n->id,
                    'icon' => $n->icon,
                    'iconColor' => $n->icon_color,
                    'title' => $n->title,
                    'body' => $n->body,
                    'url' => $n->url,
                    'type' => $n->type,
                    'read' => $n->read,
                    'time' => $n->created_at?->diffForHumans() ?? '',
                    'created_at' => $n->created_at->toIso8601String(),
                ]),
                'unread_count' => $unreadCount,
            ]);
        });

        Route::put('/{id}/read', function (int $id) {
            $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
            $notification->update(['read' => true]);

            return response()->json(['message' => 'Marked as read']);
        });

        Route::put('/read-all', function () {
            Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->update(['read' => true]);

            return response()->json(['message' => 'All marked as read']);
        });
    });
});

require __DIR__.'/settings.php';
