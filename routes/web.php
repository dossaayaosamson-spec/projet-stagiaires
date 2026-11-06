<?php

// ─── Imports ──────────────────────────────────────────────────────────────────

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CandidatureEclaAdminController;
use App\Http\Controllers\Admin\CommentModerationController;
use App\Http\Controllers\Admin\ContractAdminController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ManuscriptAdminController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PayoutAdminController;
use App\Http\Controllers\Admin\ShortStoryController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserSuspensionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorAIController;
use App\Http\Controllers\AuthorApplicationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookRatingController;
use App\Http\Controllers\CandidatureEclaController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriController;
use App\Http\Controllers\FedaPayWebhookController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ManualPaymentController;
use App\Http\Controllers\ManuscriptController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentDocumentController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PublicCatalogueAssistantController;
use App\Http\Controllers\ReadController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShortStoryPaymentController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// ─── Utilitaire (temporaire) ──────────────────────────────────────────────────

Route::get('/clear-cache', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');

    return 'Caches cleared successfully!';
});

// ─── Routes Publiques ─────────────────────────────────────────────────────────

Route::get('/', [PageController::class, 'welcome'])->name('home');
Route::get('/catalogue', [FrontController::class, 'catalogue'])->name('front.catalogue');
Route::get('/livres/{id}/{slug?}', [FrontController::class, 'showBook'])->name('front.livres.show');
Route::post('/assistant/catalogue', PublicCatalogueAssistantController::class)
    ->middleware('throttle:10,1')
    ->name('public-assistant.ask');
Route::middleware(['auth', 'not-suspended'])->prefix('/livres/{book}/notes')->name('front.ratings.')->group(function () {
    Route::post('/', [BookRatingController::class, 'store'])->name('store');
    Route::put('/', [BookRatingController::class, 'update'])->name('update');
    Route::delete('/', [BookRatingController::class, 'destroy'])->name('destroy');
});
Route::get('/auteurs', [FrontController::class, 'authorsIndex'])->name('front.auteurs.index');
Route::get('/auteurs/{id}/{slug?}', [FrontController::class, 'showAuthor'])->name('front.auteurs.show');
Route::get('/short-stories', [FrontController::class, 'storiesIndex'])->name('front.stories.index');
Route::get('/short-stories/{id}/{slug?}', [FrontController::class, 'showStory'])->name('front.stories.show');
Route::get('/evenements/{id}', [FrontController::class, 'showEvent'])->name('front.events.show');
Route::get('/nos-offres', [FrontController::class, 'plans'])->name('front.plans');
Route::get('/contact', [FrontController::class, 'contact'])->name('front.contact');
Route::post('/contact', [FrontController::class, 'sendContact'])->name('front.contact.send');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/processus-editions', [FrontController::class, 'contratEditions'])->name('front.contrat.editions');

// ─── Authentification ─────────────────────────────────────────────────────────

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login/otp', [AuthController::class, 'showOtp'])->name('login.otp');
Route::post('/login/otp', [AuthController::class, 'verifyOtp']);
Route::post('/login/otp/resend', [AuthController::class, 'resendOtp'])->name('login.otp.resend');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Mot de passe oublié
Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'showForm'])->name('password.request');
Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reinitialiser-mot-de-passe/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reinitialiser-mot-de-passe', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Notifications (marquer comme lue)
Route::middleware('auth')->group(function () {
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Vérification d'email
Route::get('/email/verify', [VerifyEmailController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::post('/email/verify', [VerifyEmailController::class, 'verify'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ─── Dashboard ────────────────────────────────────────────────────────────────

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/client/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('client.dashboard');

// ─── Espace Client ────────────────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->prefix('client')->name('client.')->group(function () {
    Route::get('/bibliotheque', [ClientController::class, 'library'])->name('library');
    Route::get('/en-cours', [ClientController::class, 'readingInProgress'])->name('reading');
    Route::get('/favoris', [ClientController::class, 'favorites'])->name('favorites');
    Route::get('/commandes', [ClientController::class, 'orders'])->name('orders');
    Route::get('/abonnement', [ClientController::class, 'subscription'])->name('subscription');
    Route::get('/candidatures', [ClientController::class, 'candidatures'])->name('candidatures');
    Route::get('/devenir-auteur', [AuthorApplicationController::class, 'create'])->name('become-author');
    Route::post('/devenir-auteur', [AuthorApplicationController::class, 'store'])->name('become-author.store');
    Route::get('/devenir-auteur/statut', [AuthorApplicationController::class, 'status'])->name('become-author.status');
    Route::get('/profil', [ClientController::class, 'profile'])->name('profile');
    Route::put('/profil', [ClientController::class, 'updateProfile'])->name('profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/abonnement/souscrire', [ClientController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/abonnement/confirmer', [ClientController::class, 'confirmSubscription'])->name('subscription.confirm');
});

// ─── Espace Auteur ────────────────────────────────────────────────────────────

Route::middleware(['auth', 'verified', 'author'])->prefix('auteur')->name('author.')->group(function () {
    Route::get('/dashboard', [AuthorController::class, 'dashboard'])->name('dashboard');
    Route::post('/payout-request', [AuthorController::class, 'storePayoutRequest'])->name('payout.request');
    Route::get('/profil', [AuthorController::class, 'profile'])->name('profile');
    Route::put('/profil', [AuthorController::class, 'updateProfile'])->name('profile.update');
});

// ─── Lecteur PDF & Favoris ────────────────────────────────────────────────────

Route::get('/lire/{bookId}', [ReadController::class, 'read'])->name('read.book');
Route::get('/lire/{bookId}/pdf', [ReadController::class, 'servePdf'])->name('read.pdf');

Route::middleware(['auth'])->group(function () {
    Route::get('/mes-favoris', [FavoriController::class, 'index'])->name('favoris.index');
    Route::post('/favoris/toggle', [FavoriController::class, 'toggle'])->name('favoris.toggle');
    Route::delete('/favoris/{livreId}', [FavoriController::class, 'destroy'])->name('favoris.destroy');
    Route::post('/progression/update', [ProgressController::class, 'update'])->name('progress.update');
    Route::get('/progression/{bookId}', [ProgressController::class, 'get'])->name('progress.get');
});

// ─── Candidature ECLA ─────────────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->prefix('candidature-ecla')->name('candidature.')->group(function () {
    Route::get('/etape-1', [CandidatureEclaController::class, 'step1'])->name('step1');
    Route::post('/etape-1', [CandidatureEclaController::class, 'step1Post']);
    Route::get('/etape-2', [CandidatureEclaController::class, 'step2'])->name('step2');
    Route::post('/etape-2', [CandidatureEclaController::class, 'step2Post']);
    Route::get('/etape-3', [CandidatureEclaController::class, 'step3'])->name('step3');
    Route::post('/etape-3', [CandidatureEclaController::class, 'step3Post']);
    Route::get('/etape-4', [CandidatureEclaController::class, 'step4'])->name('step4');
    Route::post('/etape-4', [CandidatureEclaController::class, 'step4Post']);
    Route::get('/confirmation', [CandidatureEclaController::class, 'confirmation'])->name('confirmation');
});

// ─── Panier ───────────────────────────────────────────────────────────────────

Route::middleware('auth')->prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/ajouter/{id}', [CartController::class, 'add'])->name('add');
    Route::any('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/supprimer', [CartController::class, 'remove'])->name('remove');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::get('/livraison', [CartController::class, 'deliveryForm'])->name('delivery');
    Route::post('/livraison', [CartController::class, 'storeDeliveryInfo'])->name('delivery.store');
});

// Confirmation de commande (nom conservé pour compatibilité avec les vues)
Route::post('/panier/confirmer/{order}', [CartController::class, 'confirmCheckout'])
    ->middleware('auth')
    ->name('payment.confirm');

// ─── Paiement ─────────────────────────────────────────────────────────────────

Route::middleware('auth')->prefix('pay')->name('payment.')->group(function () {
    // Routes spécifiques en premier (avant le joker /{order})
    Route::get('/retour/{order}', [PaymentController::class, 'retour'])->name('return');
    Route::get('/success/{order}', [PaymentController::class, 'success'])->name('success');
    Route::get('/manual/instructions/{order}', [ManualPaymentController::class, 'showInstructions'])->name('manual.instructions');
    Route::post('/manual/proof/{order}', [ManualPaymentController::class, 'submitProof'])->name('manual.proof');
    // Joker en dernier pour ne pas intercepter les routes ci-dessus
    Route::get('/{order}', [PaymentController::class, 'pay'])->name('pay');
});

Route::middleware('auth')->group(function () {
    Route::get('/abonnement/retour/{plan}', [PaymentController::class, 'subscriptionReturn'])->name('subscription.return');
    Route::post('/payment/initiate', [PaymentDocumentController::class, 'initiate'])->name('payment.initiate');
    Route::get('/payment/status/{transactionId}', [PaymentDocumentController::class, 'status'])->name('payment.status');
    Route::get('/payment/callback', [PaymentDocumentController::class, 'callback'])->name('payment.callback');
    Route::get('/payment/success', [PaymentDocumentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/download/{manuscriptId}', [PaymentDocumentController::class, 'download'])->name('payment.download');

    // Short Story Payments
    Route::post('/short-stories/{story}/pay', [ShortStoryPaymentController::class, 'pay'])
        ->middleware('throttle:5,1')
        ->name('front.stories.pay');
    Route::get('/short-stories/payment/callback', [ShortStoryPaymentController::class, 'callback'])
        ->name('front.stories.payment.callback');
});

Route::match(['get', 'post'], '/fedapay/webhook', [FedaPayWebhookController::class, 'handle'])->name('payment.webhook');

// ─── Lecthor (IA Manuscrits) ──────────────────────────────────────────────────

Route::prefix('manuscripts')->name('manuscripts.')->group(function () {
    Route::get('/create', [ManuscriptController::class, 'create'])->name('create');
    Route::post('/', [ManuscriptController::class, 'submit'])->name('store');
    Route::get('/{id}/results', [ManuscriptController::class, 'results'])->name('results');
});

Route::prefix('authors')->name('manuscripts.')->group(function () {
    Route::get('/', [AuthorAIController::class, 'createauteur'])->name('auteur');
    Route::get('/{id}', [AuthorAIController::class, 'show'])->name('infoauteur');
    Route::delete('/{id}', [AuthorAIController::class, 'destroy'])->name('deleteauteur');
});

Route::post('/api/analyze', [ManuscriptController::class, 'apiAnalyze'])->name('api.analyze');

// ─── Blog & Commentaires ──────────────────────────────────────────────────────

Route::get('/blog', [CommentController::class, 'index'])->name('front.blog.index');

Route::middleware(['auth', 'not-suspended'])->group(function () {
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// ─── Administration ───────────────────────────────────────────────────────────

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Ressources
    Route::resource('books', BookController::class);
    Route::resource('authors', App\Http\Controllers\Admin\AuthorController::class);
    Route::resource('stories', ShortStoryController::class);
    Route::resource('slides', SlideController::class);
    Route::resource('events', EventController::class);
    Route::resource('users', UserController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);

    // Candidatures ECLA
    Route::resource('candidatures', CandidatureEclaAdminController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::get('candidatures/{candidature}/download', [CandidatureEclaAdminController::class, 'download'])->name('candidatures.download');

    // Manuscrits reçus
    Route::resource('manuscripts', ManuscriptAdminController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::get('manuscripts/{manuscript}/download', [ManuscriptAdminController::class, 'download'])->name('manuscripts.download');

    // Contrats d'édition
    Route::resource('contracts', ContractAdminController::class);
    Route::get('contracts/{contract}/download', [ContractAdminController::class, 'downloadPdf'])->name('contracts.download');

    // Versements Auteurs (Payouts)
    Route::get('payouts', [PayoutAdminController::class, 'index'])->name('payouts.index');
    Route::post('payouts/{payout}/approve', [PayoutAdminController::class, 'approve'])->name('payouts.approve');
    Route::post('payouts/{payout}/reject', [PayoutAdminController::class, 'reject'])->name('payouts.reject');

    // Validation et gestion des paiements
    Route::post('orders/{order}/confirm', [OrderController::class, 'approveProof'])->name('orders.approve');
    Route::post('orders/{order}/reject', [OrderController::class, 'rejectProof'])->name('orders.reject');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
    Route::get('orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');

    // Modération des commentaires
    Route::prefix('moderation')->name('moderation.')->group(function () {
        Route::get('/', [CommentModerationController::class, 'index'])->name('index');
        Route::get('/signalements', [CommentModerationController::class, 'flagged'])->name('flagged');
        Route::get('/rapport', [CommentModerationController::class, 'rapport'])->name('rapport');
        Route::post('/{comment}/archiver', [CommentModerationController::class, 'archive'])->name('archive');
        Route::post('/resolve-alias', [CommentModerationController::class, 'resolveAlias'])->name('resolve-alias');
    });

    // Suspension des utilisateurs
    Route::post('/users/{user}/suspend', [UserSuspensionController::class, 'store'])->name('users.suspend');
    Route::delete('/suspensions/{suspension}', [UserSuspensionController::class, 'destroy'])->name('suspensions.destroy');
});
