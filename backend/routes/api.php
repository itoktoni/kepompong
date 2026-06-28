<?php

use App\Actions\PlanAction;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\AnakController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\ChallengeHistoryController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\CompletedSkillController;
use App\Http\Controllers\ContentApprovalController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PilarController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SkillActivityController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\WorksheetController;
use App\Models\Activity;
use App\Models\Plan;
use App\PeriodEnum;
use App\Services\LocalImageGeneratorService;
use App\Services\StoryGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/storage-image/{path}', function (string $path) {
    $fullPath = 'images/' . $path;
    $disk = \Illuminate\Support\Facades\Storage::disk('public');

    if (!$disk->exists($fullPath)) {
        abort(404);
    }

    $file = $disk->get($fullPath);
    $mime = $disk->mimeType($fullPath);

    return response($file, 200, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.image');

Route::get('/stories/preview', function (Request $request, LocalImageGeneratorService $images) {
    $pages = $request->query('pages', []);
    if (! is_array($pages)) {
        $pages = [$pages];
    }
    $generateImages = (bool) $request->query('generate_images', false);

    if ($generateImages) {
        foreach ($pages as &$page) {
            $prompt = is_array($page) ? trim($page['text'] ?? '') : trim((string) $page);
            if (is_array($page) && isset($page['text'])) {
                $page['image'] = $images->generate($prompt);
            }
        }
        unset($page);
    }

    return response()->json([
        'pages' => $pages,
    ]);
})->name('stories.preview');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/config', function () {
    return response()->json([
        'forgot_gateway' => config('langkahkecil.verification.forgot_gateway', 'whatsapp'),
        'verification_gateway' => config('langkahkecil.verification.gateway', 'whatsapp'),
        'app_name' => config('app.name', 'Jejak Tumbuh'),
    ]);
});

Route::get('/plans', function () {
    $plans = Plan::where('plan_status', 1)
        ->orderBy('plan_harga')
        ->get()
        ->map(function ($p) {
            $periodEnum = PeriodEnum::tryFrom($p->plan_periode);

            return [
                'id' => $p->plan_id,
                'name' => $p->plan_nama,
                'description' => $p->plan_keterangan,
                'value' => $p->plan_value,
                'price' => $p->plan_harga,
                'price_strikethrough' => $p->plan_coret,
                'fee' => $p->plan_fee,
                'color' => $p->plan_color,
                'recommended' => (bool) $p->plan_recomended,
                'period' => $p->plan_periode,
                'period_label' => $periodEnum?->description() ?? $p->plan_periode,
                'interval' => $p->plan_interval,
            ];
        });

    return response()->json(['plans' => $plans]);
})->name('plans.index');

Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/activities/types', [ActivityController::class, 'types'])->name('activities.types');
Route::get('/activities/type/{type}', [ActivityController::class, 'byType'])->name('activities.byType');
Route::get('/activities/sync/{type}', [ActivityController::class, 'syncByType'])->name('activities.syncByType');
Route::get('/activities/popular', [ActivityController::class, 'popular'])->name('activities.popular');
Route::get('/activities/{slug}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/activities/{id}/view', [ActivityController::class, 'trackView'])->name('activities.view');

Route::get('/pilars', [PilarController::class, 'index'])->name('pilars.index');

Route::get('/worksheet/file/{worksheetKey}', [WorksheetController::class, 'getDownloadFile'])->name('worksheet.download.file');
Route::get('/worksheet-types', [WorksheetController::class, 'getTypes'])->name('worksheet.types');

Route::middleware('')->group(function () {
    Route::get('/worksheet/{worksheetKey}/download-url', [WorksheetController::class, 'xgetDownloadUrl'])->name('worksheet.download.url');
});

Route::get('/payment-methods', [PaymentMethodController::class, 'xgetActive'])->name('payment-methods.active');
Route::get('/payment-methods/categories', [PaymentMethodController::class, 'xgetCategories'])->name('payment-methods.categories');
Route::get('/payment-methods/list', [PaymentMethodController::class, 'xgetList'])->name('payment-methods.list');

Route::post('/webhook/payment', [PaymentWebhookController::class, 'handle'])->name('webhook.payment');

Route::post('/centrifugo/token', function (Request $request) {
    if (!config('langkahkecil.notification_enable')) {
        return response()->json(['token' => 'disabled']);
    }

    $user = $request->user();
    if (!$user) {
        return response()->json(['token' => null, 'unauthenticated' => true], 401);
    }

    $centrifugo = app(\App\Services\CentrifugoService::class);

    if ($request->input('channel')) {
        return response()->json([
            'token' => $centrifugo->generateSubscriptionToken((string) $user->id, $request->input('channel')),
        ]);
    }

    return response()->json([
        'token' => $centrifugo->generateConnectionToken((string) $user->id),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/activities/{id}', [ActivityController::class, 'update'])->name('activities.update');
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/send-verification', [AuthController::class, 'sendVerification'])->name('verification.send');
    Route::post('/verify', [AuthController::class, 'verify'])->name('verification.verify');

    Route::middleware('verified')->group(function () {
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AuthController::class, 'changePassword'])->name('password.change');
        Route::put('/affiliate-code', [AuthController::class, 'updateAffiliateCode'])->name('affiliate.update');
        Route::post('/rekening', [AuthController::class, 'updateRekening'])->name('rekening.update');
        Route::post('/cashout', [AuthController::class, 'requestCashout'])->name('cashout.request');
        Route::get('/cashouts', [AuthController::class, 'cashoutList'])->name('cashout.list');
        Route::get('/referrals', [AuthController::class, 'referralList'])->name('referrals.list');
        Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
        Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
        Route::delete('/discounts/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
        Route::post('/purchase-plan', PlanAction::class . '@purchase')->name('purchase.plan');
        Route::get('/validate-plan', PlanAction::class . '@validatePlan')->name('validate.plan');

        Route::prefix('payments')->group(function () {
            Route::post('/', [PaymentController::class, 'create'])->name('payments.create');
            Route::get('/{id}', [PaymentController::class, 'status'])->name('payments.status');
            Route::post('/{id}/settle', [PaymentController::class, 'settle'])->name('payments.settle');
            Route::post('/{id}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
            Route::get('/', [PaymentController::class, 'history'])->name('payments.history');
            Route::post('/validate-discount', [PaymentController::class, 'validateDiscount'])->name('payments.validate-discount');
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
            Route::put('/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
            Route::put('/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
            Route::delete('/', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
        });

        Route::get('/anak', [AnakController::class, 'index'])->name('anak.index');
        Route::post('/anak', [AnakController::class, 'store'])->name('anak.store');
        Route::put('/anak/{anakId}', [AnakController::class, 'update'])->name('anak.update');
        Route::delete('/anak/{anakId}', [AnakController::class, 'destroy'])->name('anak.destroy');
        Route::post('/sync', [AnakController::class, 'sync'])->name('anak.sync');

        Route::post('/anak/{anakId}/skills', [SkillController::class, 'store'])->name('anak.skills.store');
        Route::put('/anak/{anakId}/skills/{skillId}', [SkillController::class, 'update'])->name('anak.skills.update');
        Route::delete('/anak/{anakId}/skills/{skillId}', [SkillController::class, 'destroy'])->name('anak.skills.destroy');

        Route::put('/activities/{id}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])->name('activities.destroy');
        Route::put('/activities/{id}/update', [ActivityController::class, 'xputUpdate'])->name('activities.xputUpdate');
        Route::post('/activities/{id}/update', [ActivityController::class, 'xputUpdate'])->name('activities.xputUpdate.post');
        Route::post('/activities/{id}/generate-image', [ActivityController::class, 'generateImage'])->name('activities.generate-image');
        Route::post('/activities/{id}/generate-prompt', [ActivityController::class, 'xpostGeneratePrompt'])->name('activities.generate-prompt');
        Route::post('/generate-idea', [ActivityController::class, 'generateIdea'])->name('activities.generate-idea');
        Route::post('/ideas/{id}/generate-activity', [ActivityController::class, 'ideaToActivity'])->name('activities.idea-to-activity');
        Route::get('/ideas', [ActivityController::class, 'ideasList'])->name('activities.ideas-list');
        Route::get('/ideas/users', [ActivityController::class, 'ideasUsers'])->name('activities.ideas-users');
        Route::put('/ideas/{id}', [ActivityController::class, 'ideaUpdate'])->name('activities.idea-update');
        Route::delete('/ideas/{id}', [ActivityController::class, 'ideaDelete'])->name('activities.idea-delete');
        Route::post('/ideas/batch-delete', [ActivityController::class, 'ideaBatchDelete'])->name('activities.idea-batch-delete');
        Route::get('/ai-providers', [ActivityController::class, 'aiProviders'])->name('activities.ai-providers');
        Route::get('/activity-types', [ActivityController::class, 'activityTypes'])->name('activities.activity-types');
        Route::get('/skills-list', [ActivityController::class, 'skillsList'])->name('activities.skills-list');
        Route::get('/activities-list', [ActivityController::class, 'activitiesList'])->name('activities.activities-list');

        Route::post('/anak/{anakId}/activities', [SkillActivityController::class, 'store'])->name('anak.activities.store');
        Route::delete('/anak/{anakId}/activities/{activityId}', [SkillActivityController::class, 'destroy'])->name('anak.activities.destroy');
        Route::put('/anak/{anakId}/activities/{activityId}/toggle', [SkillActivityController::class, 'toggle'])->name('anak.activities.toggle');

        Route::post('/anak/{anakId}/completed-skills', [CompletedSkillController::class, 'store'])->name('anak.completed-skills.store');
        Route::delete('/anak/{anakId}/completed-skills/{key}', [CompletedSkillController::class, 'destroy'])->name('anak.completed-skills.destroy');

        Route::get('/anak/{anakId}/challenges', [ChallengeController::class, 'index'])->name('anak.challenges.index');
        Route::post('/anak/{anakId}/challenges', [ChallengeController::class, 'store'])->name('anak.challenges.store');
        Route::put('/anak/{anakId}/challenges/{challengeId}', [ChallengeController::class, 'update'])->name('anak.challenges.update');
        Route::delete('/anak/{anakId}/challenges/{challengeId}', [ChallengeController::class, 'destroy'])->name('anak.challenges.destroy');

        Route::get('/anak/{anakId}/challenge-history', [ChallengeHistoryController::class, 'index'])->name('anak.challenge-history.index');
        Route::post('/anak/{anakId}/challenge-history', [ChallengeHistoryController::class, 'store'])->name('anak.challenge-history.store');

        Route::get('/anak/{anakId}/checklists', [ChecklistController::class, 'index'])->name('anak.checklists.index');
        Route::post('/anak/{anakId}/checklists', [ChecklistController::class, 'store'])->name('anak.checklists.store');
        Route::put('/anak/{anakId}/checklists/{checklistId}', [ChecklistController::class, 'update'])->name('anak.checklists.update');
        Route::delete('/anak/{anakId}/checklists/{checklistId}', [ChecklistController::class, 'destroy'])->name('anak.checklists.destroy');

        Route::get('/anak/{anakId}/schedules', [ScheduleController::class, 'index'])->name('anak.schedules.index');
        Route::post('/anak/{anakId}/schedules', [ScheduleController::class, 'store'])->name('anak.schedules.store');
        Route::put('/anak/{anakId}/schedules/{scheduleId}', [ScheduleController::class, 'update'])->name('anak.schedules.update');
        Route::delete('/anak/{anakId}/schedules/{scheduleId}', [ScheduleController::class, 'destroy'])->name('anak.schedules.destroy');
        Route::post('/anak/{anakId}/schedules/{scheduleId}/toggle', [ScheduleController::class, 'toggleDone'])->name('anak.schedules.toggle');
        Route::get('/anak/{anakId}/schedule-histories', [ScheduleController::class, 'xgetHistories'])->name('anak.schedules.histories');

        Route::post('/anak/{anakId}/worksheets', [WorksheetController::class, 'store'])->name('anak.worksheets.store');
        Route::delete('/anak/{anakId}/worksheets/{worksheetId}', [WorksheetController::class, 'destroy'])->name('anak.worksheets.destroy');

        Route::get('/anak/{anakId}/evaluations', [EvaluationController::class, 'index'])->name('anak.evaluations.index');
        Route::post('/anak/{anakId}/evaluations', [EvaluationController::class, 'store'])->name('anak.evaluations.store');
        Route::get('/evaluations/{evaluationId}', [EvaluationController::class, 'show'])->name('evaluations.show');
        Route::post('/evaluations/{evaluationId}/finalize', [EvaluationController::class, 'finalize'])->name('evaluations.finalize');
    });

    Route::get('/content/pending', [ContentApprovalController::class, 'xgetPending'])->name('content.pending');
    Route::put('/content/{type}/{id}/approve', [ContentApprovalController::class, 'xputApprove'])->name('content.approve');
    Route::put('/content/{type}/{id}/reject', [ContentApprovalController::class, 'xputReject'])->name('content.reject');
    Route::put('/content/{type}/{id}/review', [ContentApprovalController::class, 'xputReview'])->name('content.review');

    Route::get('/addons', [AddonController::class, 'xgetIndex'])->name('addons.index');
    Route::get('/addons/purchased', [AddonController::class, 'xgetPurchased'])->name('addons.purchased');
    Route::get('/addons/mine', [AddonController::class, 'xgetMyAddons'])->name('addons.mine');
    Route::post('/addons', [AddonController::class, 'xpostCreate'])->name('addons.create');
    Route::put('/addons/{addonId}', [AddonController::class, 'xputUpdate'])->name('addons.update');
    Route::delete('/addons/{addonId}', [AddonController::class, 'xdeleteDestroy'])->name('addons.destroy');
    Route::post('/addons/{addonId}/purchase', [AddonController::class, 'xpostPurchase'])->name('addons.purchase');
    Route::post('/addons/{addonId}/activities', [AddonController::class, 'xpostCreateActivity'])->name('addons.activities.create');
    Route::get('/addons/{addonId}/activities', [AddonController::class, 'xgetAddonActivities'])->name('addons.activities.index');
    Route::post('/addons/{addonId}/worksheets', [AddonController::class, 'xpostCreateWorksheet'])->name('addons.worksheets.create');
    Route::get('/addons/{addonId}/worksheets', [AddonController::class, 'xgetAddonWorksheets'])->name('addons.worksheets.index');
});
