<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// Import the Role Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Parent\ParentController;

// Temporary Production Setup Route
Route::get('/server-setup', function() {
    // 1. Clear Caches
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    
    // 2. Force Database Migration
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    
    return 'Caches cleared and Database Migrated successfully!';
});

// 1. Root Redirect to Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Add the setup routes INSIDE the main auth middleware group
Route::middleware(['auth'])->group(function () {
    Route::get('/setup-password', [\App\Http\Controllers\Auth\PasswordSetupController::class, 'create'])->name('password.setup');
    Route::post('/setup-password', [\App\Http\Controllers\Auth\PasswordSetupController::class, 'store'])->name('password.setup.store');
});

// 2. Master Redirector
Route::get('/dashboard', function () {
    $role = Auth::user()->role;
    return redirect()->route($role . '.dashboard');
})->middleware(['auth', 'verified', \App\Http\Middleware\ForcePasswordChange::class])->name('dashboard');

// 3. Admin Routes
Route::middleware(['auth', 'role:admin', \App\Http\Middleware\ForcePasswordChange::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // User Management (Update/Delete)
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Teachers
    Route::get('/teachers', [AdminController::class, 'teachers'])->name('teachers.index');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
    
    // Students
    Route::get('/students', [AdminController::class, 'students'])->name('students.index');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
    
    // Parents
    Route::get('/parents', [AdminController::class, 'parents'])->name('parents.index');
    Route::post('/parents', [AdminController::class, 'storeParent'])->name('parents.store');

    // Classes
    Route::get('/classes', [AdminController::class, 'classes'])->name('classes.index');
    Route::post('/classes', [AdminController::class, 'storeClass'])->name('classes.store');
    Route::put('/classes/{id}', [AdminController::class, 'updateClass'])->name('classes.update');
    Route::delete('/classes/{id}', [AdminController::class, 'destroyClass'])->name('classes.destroy');
    
    // Academic Setup
    Route::get('/terms', [AdminController::class, 'terms'])->name('terms.index');
    Route::post('/terms', [AdminController::class, 'storeSchoolYear'])->name('terms.store');
    Route::post('/terms/{id}/activate', [AdminController::class, 'activateSchoolYear'])->name('terms.activate');
    
    Route::get('/competencies', [AdminController::class, 'competencies'])->name('competencies.index');
    Route::post('/competencies', [AdminController::class, 'storeCompetency'])->name('competencies.store');
    
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements.index');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
});

// 4. Teacher Routes
Route::middleware(['auth', 'role:teacher', \App\Http\Middleware\ForcePasswordChange::class])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'index'])->name('dashboard');
    Route::get('/classes', [TeacherController::class, 'classes'])->name('classes.index');
    Route::post('/classes', [TeacherController::class, 'storeClass'])->name('class.store');
    Route::get('/classes/{id}', [TeacherController::class, 'showClass'])->name('classes.show');
    Route::put('/classes/{id}', [TeacherController::class, 'updateClass'])->name('class.update');
    Route::delete('/classes/{id}', [TeacherController::class, 'destroyClass'])->name('class.destroy');
    Route::get('/classes/{class_id}/student/{student_id}/analytics', [TeacherController::class, 'studentAnalytics'])->name('classes.student.analytics');
    Route::delete('/classes/{class_id}/student/{student_id}', [TeacherController::class, 'removeStudent'])->name('classes.student.remove');

    Route::get('/requests', [TeacherController::class, 'joinRequests'])->name('requests.index');
    Route::patch('/requests/{class_id}/{student_id}', [TeacherController::class, 'updateRequest'])->name('requests.update');

    Route::post('/student/{id}/intervention', [TeacherController::class, 'generateIntervention'])->name('generate.intervention');
    Route::get('/intervention/{id}/edit', [TeacherController::class, 'editIntervention'])->name('intervention.edit');
    Route::put('/intervention/{id}', [TeacherController::class, 'updateIntervention'])->name('intervention.update');
    Route::delete('/intervention/{id}', [TeacherController::class, 'destroyIntervention'])->name('intervention.destroy');
    
    Route::post('/assessments/deploy', [TeacherController::class, 'generateAIAssessment'])->name('assessment.deploy');
    Route::post('/assessments/store', [TeacherController::class, 'storeAIAssessment'])->name('assessment.store'); 
    
    Route::put('/assessments/{id}', [TeacherController::class, 'updateAssessment'])->name('assessment.update');
    Route::delete('/assessments/{id}', [TeacherController::class, 'destroyAssessment'])->name('assessment.destroy');

    Route::post('/student/{id}/message', [TeacherController::class, 'directMessageParent'])->name('message.parent');
});

// 5. Student Routes
Route::middleware(['auth', 'role:student', \App\Http\Middleware\ForcePasswordChange::class])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'index'])->name('dashboard');
    Route::get('/classes', [StudentController::class, 'availableClasses'])->name('classes.index');
    Route::post('/class/join', [StudentController::class, 'joinClass'])->name('class.join');
    Route::get('/class/{id}', [StudentController::class, 'showClass'])->name('class.show');
    Route::get('/grades', [StudentController::class, 'grades'])->name('grades.index');
    Route::get('/learning-path', [StudentController::class, 'learningPath'])->name('learning-path.index');
    Route::post('/learning-path/complete/{id}', [StudentController::class, 'completeTask'])->name('learning-path.complete');

    Route::post('/link-parent', [StudentController::class, 'linkParentLater'])->name('link.parent');

    Route::get('/quiz/{id}', [StudentController::class, 'takeQuiz'])->name('quiz.take');
    Route::post('/quiz/{id}', [StudentController::class, 'submitQuiz'])->name('quiz.submit');
    Route::post('/offline-sync/quiz', [StudentController::class, 'submitQuiz'])->name('quiz.sync'); 
    
    Route::get('/intervention/{id}', [StudentController::class, 'showIntervention'])->name('intervention.show');
    Route::post('/intervention/{id}', [StudentController::class, 'submitIntervention'])->name('intervention.submit');
});

// 6. Parent Routes
Route::middleware(['auth', 'role:parent', \App\Http\Middleware\ForcePasswordChange::class])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    Route::get('/child/{id}/analytics', [ParentController::class, 'childAnalytics'])->name('child.analytics');
    Route::post('/sms/send', [ParentController::class, 'sendSms'])->name('sms.send');
    Route::get('/remedial-guide/{student_id}', [ParentController::class, 'remedialView'])->name('remedial.view')->middleware('signed'); 
});

// 7. Default Breeze Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';