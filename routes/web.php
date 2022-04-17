<?php

use Illuminate\Support\Facades\Route;

// custom
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DojoController;
use App\Http\Controllers\GradingPolicyController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::prefix('admin')->middleware('isAdmin', 'auth')->group(function(){
    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');
    Route::get('profile',    'AdminController@profile')->name('admin.profile');
    Route::get('settings',   'AdminController@settings')->name('admin.settings');
});

Route::prefix('attendance')->name('attendance.')->middleware('isAdminOrDojo', 'auth')->group(function(){
    Route::get('/upload', [App\Http\Controllers\AttendanceController::class, 'index'])->name('upload');
    Route::post('/save', [App\Http\Controllers\AttendanceController::class, 'store'])->name('save');
    Route::get('/daily', [App\Http\Controllers\AttendanceController::class, 'daily'])->name('daily');
    Route::post('/daily-report', [App\Http\Controllers\AttendanceController::class, 'daily_report'])->name('daily-report');
    Route::get('/summary', [App\Http\Controllers\AttendanceController::class, 'attendance_summary'])->name('attendance-summary');
    Route::post('/summary', [App\Http\Controllers\AttendanceController::class, 'attendance_summary_report'])->name('attendance-summary-report');
});


Route::prefix('grading-policy')->name('grading-policy.')->middleware('isAdminOrDojo', 'auth')->group(function () {
    Route::get('/show', [App\Http\Controllers\GradingPolicyController::class, 'index'])->name('show');
    Route::get('/edit/{id?}', [App\Http\Controllers\GradingPolicyController::class, 'edit'])->name('edit');
    Route::get('/delete/{id?}', [App\Http\Controllers\GradingPolicyController::class, 'destroy'])->name('delete');
    Route::post('/save', [App\Http\Controllers\GradingPolicyController::class, 'store'])->name('save');
});


Route::prefix('dojo')->middleware('isDojo', 'auth')->group(function(){
    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dojo.dashboard');
    Route::get('profile',   'DojoController@profile')->name('dojo.profile');
    Route::get('settings',  'DojoController@settings')->name('dojo.settings');
});

Route::prefix('isStudent', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->group(function(){
    Route::get('dashboard', 'StudentController@index')->name('student.dashboard');
    Route::get('profile',   'StudentController@profile')->name('student.profile');
    Route::get('settings',  'StudentController@settings')->name('student.settings');
});



















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


// Route::get('/', function () {
//     return view('auth.login');
// });
//
//
//
//
// Auth::routes();
// Route::middleware('auth')->group(function () {
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/logout', function(){
  Session::flush();
       Auth::logout();
       return redirect('login');
})->name('auth.logout');

Route::prefix('dojos')->name('dojos.')->middleware('isAdmin', 'auth')->group(function () {
    Route::get('/show', [App\Http\Controllers\DojoController::class, 'index'])->name('show');
    Route::get('/edit/{id?}', [App\Http\Controllers\DojoController::class, 'edit'])->name('edit');
    Route::get('/delete/{id?}', [App\Http\Controllers\DojoController::class, 'destroy'])->name('delete');
    Route::post('/save', [App\Http\Controllers\DojoController::class, 'store'])->name('save');
});

Route::prefix('events')->name('events.')->middleware('isAdminOrDojo', 'auth')->group(function () {
    Route::post('/add', [App\Http\Controllers\EventController::class, 'add'])->name('add');
    Route::get('/show', [App\Http\Controllers\EventController::class, 'index'])->name('show');
    Route::post('/create', [App\Http\Controllers\EventController::class, 'create'])->name('create');
    Route::post('/update', [App\Http\Controllers\EventController::class, 'update']);
    Route::post('/delete', [App\Http\Controllers\EventController::class, 'destroy']);
});

Route::prefix('students')->name('students.')->middleware('isAdminOrDojo', 'auth')->group(function () {
    Route::get('/show', [App\Http\Controllers\StudentController::class, 'index'])->name('show');
    Route::get('/edit/{id?}', [App\Http\Controllers\StudentController::class, 'edit'])->name('edit');
    Route::get('/copy/{id?}', [App\Http\Controllers\StudentController::class, 'copy'])->name('copy');
    Route::get('/delete/{id?}', [App\Http\Controllers\StudentController::class, 'destroy'])->name('delete');
    Route::post('/save', [App\Http\Controllers\StudentController::class, 'store'])->name('save');
});




// });
//
//
// Route::get('login', function () {
//     return view('auth.login');
// })->name('login');
