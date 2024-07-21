<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentsController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/register', function () {
    return view('User.register');
})->name('register');
Route::post('/create/user', [UserController::class, "store"])->name('create.user');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/login', function () {
    return view('User.login');
})->name('login');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');


Route::post('/logout', function () {
    Auth::logout();
    return redirect('/'); // Redirect to the home page or any page after logout
})->name('logout');



Route::get('/index/{id}', function () {
    return view('User.index');
})->name('index');
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-zA-Z]{2}'],
    'middleware' => 'setlocale',
  ], function() {
    Route::get('/', function(){
      return view('welcome');
    });
  });

Route::get('/', function () {
    return redirect(app()->getLocale());
  });

Route::middleware(['auth'])->group(function () {

    Route::get('/showposts', [PostController::class, 'show'])->name('posts.show');
    Route::get('posts/create/{id?}', [PostController::class, 'create'])->name('posts.create');

    Route::get('posts', [PostController::class, 'index'])->name('posts.post');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    
    Route::get('/edit_posts/{id}', [PostController::class, 'getPostId'])->name('posts.getPostId');
    Route::put('/edit_posts/{id}', [PostController::class, 'update']);
    Route::delete('/delete_posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/user', [PostController::class, 'getByUserId']);
    Route::get('/FilterTitel', [PostController::class, 'getPostsByTitle']);

    Route::post('/addCommets', [CommentsController::class, 'store']);
    Route::get('/GetComments/{id}', [CommentsController::class, 'getCommentsByPostId']);
});
Route::get('/check-database', function () {
    try {
        // Check if a PDO connection can be established
        if (DB::connection()->getPdo()) {
            return "Connected successfully to database: " . DB::connection()->getDatabaseName();
        } else {
            return "Could not connect to the database. Check your configuration.";
        }
    } catch (\Exception $e) {
        return "Exception caught: " . $e->getMessage();
    }
});



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
