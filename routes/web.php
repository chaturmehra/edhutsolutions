<?php

use App\Http\Controllers\Account\DashboardController as AccountDashboardController;
use App\Http\Controllers\Account\LearningController;
use App\Http\Controllers\Account\OrderHistoryController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\QuizHistoryController;
use App\Http\Controllers\Account\ServiceLearningController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CourseDetailController;
use App\Http\Controllers\Admin\CourseMaterialController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\QuizResultController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\SeoDashboardController;
use App\Http\Controllers\Admin\ServiceMaterialController;
use App\Http\Controllers\Admin\ServicePlanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CorporateEnquiryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnquiryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Learn\MediaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizTakeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/courses/{course:slug}', [CartController::class, 'store'])->name('cart.store');
Route::post('/cart/services/{plan}', [CartController::class, 'storePlan'])->whereNumber('plan')->name('cart.plans.store');
Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{blog:slug}', [BlogController::class, 'show'])->name('blogs.show');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/courses/{course:slug}/resources/{resource}', [CourseController::class, 'resource'])->whereNumber('resource')->name('courses.resource');
Route::post('/courses/{course:slug}/enrollment-request', [CourseEnquiryController::class, 'store'])->middleware('throttle:8,1')->name('courses.enquiry');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::permanentRedirect('/gallery', '/services/corporate-training');
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/{quiz:slug}', [QuizTakeController::class, 'show'])->name('quizzes.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:8,1')->name('contact.store');
Route::post('/corporate-enquiry', [CorporateEnquiryController::class, 'store'])->middleware('throttle:8,1')->name('corporate.enquiry');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])->middleware('throttle:login')->name('admin.login.store');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:8,1');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/email/verify', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/quizzes/{quiz:slug}/start', [QuizTakeController::class, 'start'])->name('quizzes.start');
    Route::get('/quizzes/{quiz:slug}/attempt', [QuizTakeController::class, 'attempt'])->name('quizzes.attempt');
    Route::post('/quizzes/{quiz:slug}/heartbeat', [QuizTakeController::class, 'heartbeat'])->middleware('throttle:30,1')->name('quizzes.heartbeat');
    Route::post('/quizzes/{quiz:slug}/submit', [QuizTakeController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quizzes/{quiz:slug}/results/{attempt}', [QuizTakeController::class, 'results'])->name('quizzes.results');
    Route::get('/learn/pdfs/{pdf}', [MediaController::class, 'pdf'])->name('learn.pdf');
    Route::get('/learn/pdfs/{pdf}/file', [MediaController::class, 'pdfFile'])->name('learn.pdf.file');
    Route::get('/learn/videos/{video}', [MediaController::class, 'video'])->name('learn.video');
    Route::get('/learn/services/{service:slug}', [ServiceLearningController::class, 'show'])->name('learn.services.show');
    Route::get('/learn/{course:slug}', [LearningController::class, 'show'])->name('learn.show');
});

Route::middleware(['auth', 'verified', 'role:student'])->prefix('account')->name('account.')->group(function () {
    Route::get('/', AccountDashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/courses', [LearningController::class, 'index'])->name('courses.index');
    Route::get('/services', [ServiceLearningController::class, 'index'])->name('services.index');
    Route::get('/quizzes', [QuizHistoryController::class, 'index'])->name('quizzes.index');
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderHistoryController::class, 'show'])->name('orders.show');
});

Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/import/template', [AdminUserController::class, 'template'])->name('users.import.template');
    Route::post('users/import', [AdminUserController::class, 'import'])->name('users.import');
    Route::post('users/{user}/status', [AdminUserController::class, 'status'])->name('users.status');
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    Route::get('quiz-results', [QuizResultController::class, 'index'])->name('quiz-results.index');
    Route::get('quiz-results/{attempt}', [QuizResultController::class, 'show'])->whereNumber('attempt')->name('quiz-results.show');
    Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::patch('enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('enquiries.update');
    Route::patch('enquiries/{enquiry}/read', [EnquiryController::class, 'markRead'])->name('enquiries.read');
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::prefix('courses/{course}')->whereNumber('course')->name('courses.')->group(function () {
        Route::get('materials', [CourseMaterialController::class, 'show'])->name('materials');
        Route::get('packages/create', [CourseMaterialController::class, 'createPackage'])->name('packages.create');
        Route::post('packages', [CourseMaterialController::class, 'storePackage'])->name('packages.store');
        Route::get('packages/{package}/edit', [CourseMaterialController::class, 'editPackage'])->whereNumber('package')->name('packages.edit');
        Route::put('packages/{package}', [CourseMaterialController::class, 'updatePackage'])->whereNumber('package')->name('packages.update');
        Route::delete('packages/{package}', [CourseMaterialController::class, 'destroyPackage'])->whereNumber('package')->name('packages.destroy');
        Route::get('cohorts/create', [CourseMaterialController::class, 'createCohort'])->name('cohorts.create');
        Route::post('cohorts', [CourseMaterialController::class, 'storeCohort'])->name('cohorts.store');
        Route::get('cohorts/{cohort}/edit', [CourseMaterialController::class, 'editCohort'])->whereNumber('cohort')->name('cohorts.edit');
        Route::put('cohorts/{cohort}', [CourseMaterialController::class, 'updateCohort'])->whereNumber('cohort')->name('cohorts.update');
        Route::delete('cohorts/{cohort}', [CourseMaterialController::class, 'destroyCohort'])->whereNumber('cohort')->name('cohorts.destroy');
        Route::get('modules/create', [CourseDetailController::class, 'createModule'])->name('modules.create');
        Route::post('modules', [CourseDetailController::class, 'storeModule'])->name('modules.store');
        Route::get('modules/{module}/edit', [CourseDetailController::class, 'editModule'])->whereNumber('module')->name('modules.edit');
        Route::put('modules/{module}', [CourseDetailController::class, 'updateModule'])->whereNumber('module')->name('modules.update');
        Route::delete('modules/{module}', [CourseDetailController::class, 'destroyModule'])->whereNumber('module')->name('modules.destroy');
        Route::get('resources/create', [CourseDetailController::class, 'createResource'])->name('resources.create');
        Route::post('resources', [CourseDetailController::class, 'storeResource'])->name('resources.store');
        Route::get('resources/{resource}/edit', [CourseDetailController::class, 'editResource'])->whereNumber('resource')->name('resources.edit');
        Route::put('resources/{resource}', [CourseDetailController::class, 'updateResource'])->whereNumber('resource')->name('resources.update');
        Route::delete('resources/{resource}', [CourseDetailController::class, 'destroyResource'])->whereNumber('resource')->name('resources.destroy');
        Route::get('pdfs/create', [CourseMaterialController::class, 'createPdf'])->name('pdfs.create');
        Route::post('pdfs', [CourseMaterialController::class, 'storePdf'])->name('pdfs.store');
        Route::get('pdfs/{pdf}/edit', [CourseMaterialController::class, 'editPdf'])->whereNumber('pdf')->name('pdfs.edit');
        Route::put('pdfs/{pdf}', [CourseMaterialController::class, 'updatePdf'])->whereNumber('pdf')->name('pdfs.update');
        Route::delete('pdfs/{pdf}', [CourseMaterialController::class, 'destroyPdf'])->whereNumber('pdf')->name('pdfs.destroy');
        Route::get('videos/create', [CourseMaterialController::class, 'createVideo'])->name('videos.create');
        Route::post('videos', [CourseMaterialController::class, 'storeVideo'])->name('videos.store');
        Route::get('videos/{video}/edit', [CourseMaterialController::class, 'editVideo'])->whereNumber('video')->name('videos.edit');
        Route::put('videos/{video}', [CourseMaterialController::class, 'updateVideo'])->whereNumber('video')->name('videos.update');
        Route::delete('videos/{video}', [CourseMaterialController::class, 'destroyVideo'])->whereNumber('video')->name('videos.destroy');
        Route::get('quizzes/create', [CourseMaterialController::class, 'createQuiz'])->name('quizzes.create');
        Route::post('quizzes', [CourseMaterialController::class, 'storeQuiz'])->name('quizzes.store');
        Route::get('quizzes/{quiz}/edit', [CourseMaterialController::class, 'editQuiz'])->whereNumber('quiz')->name('quizzes.edit');
        Route::put('quizzes/{quiz}', [CourseMaterialController::class, 'updateQuiz'])->whereNumber('quiz')->name('quizzes.update');
        Route::delete('quizzes/{quiz}', [CourseMaterialController::class, 'destroyQuiz'])->whereNumber('quiz')->name('quizzes.destroy');
        Route::get('quizzes/{quiz}/questions', [CourseMaterialController::class, 'questions'])->whereNumber('quiz')->name('questions');
        Route::get('quizzes/{quiz}/questions/create', [CourseMaterialController::class, 'createQuestion'])->whereNumber('quiz')->name('questions.create');
        Route::post('quizzes/{quiz}/questions', [CourseMaterialController::class, 'storeQuestion'])->whereNumber('quiz')->name('questions.store');
        Route::get('quizzes/{quiz}/questions/import', [CourseMaterialController::class, 'importQuestionsForm'])->whereNumber('quiz')->name('questions.import.form');
        Route::post('quizzes/{quiz}/questions/import', [CourseMaterialController::class, 'importQuestions'])->whereNumber('quiz')->name('questions.import');
        Route::get('quizzes/{quiz}/questions/template', [CourseMaterialController::class, 'questionTemplate'])->whereNumber('quiz')->name('questions.template');
        Route::get('quizzes/{quiz}/questions/{question}/edit', [CourseMaterialController::class, 'editQuestion'])->whereNumber(['quiz', 'question'])->name('questions.edit');
        Route::put('quizzes/{quiz}/questions/{question}', [CourseMaterialController::class, 'updateQuestion'])->whereNumber(['quiz', 'question'])->name('questions.update');
        Route::delete('quizzes/{quiz}/questions/{question}', [CourseMaterialController::class, 'destroyQuestion'])->whereNumber(['quiz', 'question'])->name('questions.destroy');
    });

    Route::prefix('services/{service}/plans')->whereNumber('service')->name('services.plans.')->group(function () {
        Route::get('/', [ServicePlanController::class, 'index'])->name('index');
        Route::get('create', [ServicePlanController::class, 'create'])->name('create');
        Route::post('/', [ServicePlanController::class, 'store'])->name('store');
        Route::get('{plan}/edit', [ServicePlanController::class, 'edit'])->whereNumber('plan')->name('edit');
        Route::put('{plan}', [ServicePlanController::class, 'update'])->whereNumber('plan')->name('update');
        Route::delete('{plan}', [ServicePlanController::class, 'destroy'])->whereNumber('plan')->name('destroy');
    });

    Route::prefix('services/{service}')->whereNumber('service')->name('services.')->group(function () {
        Route::get('materials', [ServiceMaterialController::class, 'show'])->name('materials');
        Route::get('pdfs/create', [ServiceMaterialController::class, 'createPdf'])->name('pdfs.create');
        Route::post('pdfs', [ServiceMaterialController::class, 'storePdf'])->name('pdfs.store');
        Route::get('pdfs/{pdf}/edit', [ServiceMaterialController::class, 'editPdf'])->whereNumber('pdf')->name('pdfs.edit');
        Route::put('pdfs/{pdf}', [ServiceMaterialController::class, 'updatePdf'])->whereNumber('pdf')->name('pdfs.update');
        Route::delete('pdfs/{pdf}', [ServiceMaterialController::class, 'destroyPdf'])->whereNumber('pdf')->name('pdfs.destroy');
        Route::get('videos/create', [ServiceMaterialController::class, 'createVideo'])->name('videos.create');
        Route::post('videos', [ServiceMaterialController::class, 'storeVideo'])->name('videos.store');
        Route::get('videos/{video}/edit', [ServiceMaterialController::class, 'editVideo'])->whereNumber('video')->name('videos.edit');
        Route::put('videos/{video}', [ServiceMaterialController::class, 'updateVideo'])->whereNumber('video')->name('videos.update');
        Route::delete('videos/{video}', [ServiceMaterialController::class, 'destroyVideo'])->whereNumber('video')->name('videos.destroy');
    });

    $modules = 'categories|courses|quizzes|services|testimonials|trainers|hall-of-fame|video-stories|pages|blogs|clients|corporate-images';
    Route::get('{module}/create', [ContentController::class, 'create'])->name('modules.create')->where('module', $modules);
    Route::post('{module}', [ContentController::class, 'store'])->name('modules.store')->where('module', $modules);
    Route::get('{module}/{id}/edit', [ContentController::class, 'edit'])->name('modules.edit')->where('module', $modules)->whereNumber('id');
    Route::put('{module}/{id}', [ContentController::class, 'update'])->name('modules.update')->where('module', $modules)->whereNumber('id');
    Route::delete('{module}/{id}', [ContentController::class, 'destroy'])->name('modules.destroy')->where('module', $modules)->whereNumber('id');
    Route::post('{module}/{id}/status', [ContentController::class, 'status'])->name('modules.status')->where('module', $modules)->whereNumber('id');
    Route::get('{module}', [ContentController::class, 'index'])->name('modules.index')->where('module', $modules);
});

Route::middleware(['auth', 'verified', 'role:super_admin,seo_manager'])->prefix('admin/seo')->name('admin.seo.')->group(function () {
    Route::get('/', SeoDashboardController::class)->name('dashboard');
    Route::post('/redirects', [RedirectController::class, 'store'])->name('redirects.store');
    Route::delete('/redirects/{redirect}', [RedirectController::class, 'destroy'])->name('redirects.destroy');
});

Route::get('/pages/{page:slug}', fn (Page $page) => redirect('/'.$page->slug, 301));
Route::get('/about-us', function () {
    $page = Page::query()->where('slug', 'about')->firstOrFail();

    return app(PageController::class)->show($page);
});
Route::get('/{page:slug}', [PageController::class, 'show'])->name('pages.show');
