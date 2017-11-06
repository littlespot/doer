<?php

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
Route::get('contracts', function()
{
    return View::make('condition');
});
Route::get('inaccessible', function()
{
    return View::make('inaccessible');
});

Route::get('terms', function()
{
    return View::make('terms');
});

Route::resource('users', 'UserController',
    array('only' => array('index','show')));

Route::resource('occupations', 'OccupationController',
    array('only' => array('index','show')));

Route::resource('invitation', 'PotentialController');

Route::get('active', 'Auth\PasswordController@initiation');
Route::get('relogin', 'Auth\LoginController@relogin');
Route::get('activation/{key}/{uid}', 'Auth\RegisterController@activation');
Route::get('password/reset', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@firstReset');
Route::post('forget', 'Auth\AuthController@forget');

Route::get('guest/{id}', 'GuestController@show');

Route::resource('languages', 'LanguageController');

Route::get('invite', function() {
    return View::make('auth.invitation');
});

Route::group(['middleware' =>  ['active']], function () {

    Route::resource('profile', 'ProfileController');
    Route::resource('account', 'AccountController',
        array('only' => array('index','show')));

    Route::resource('questions', 'QuestionController',
        array('only' => array('index','show')));


    Route::get('ask/{id}', 'ProjectController@ask');

    Route::get('notifications', 'ProfileController@notifications');
    Route::get('messages', 'ProfileController@messages');

    Route::get('project/{id}', 'ProjectController@detail');

    Route::get('preparations', 'PreparationController@create');

    Route::get('report/{id}','ReportController@create');
    Route::get('reports/{id}', 'ReportController@show');

    Route::get('discover', 'ProjectController@index');

    Route::get('contact', function (){
        return view('contact');
    });

    Route::get('join', function (){
        return view('join');
    });

    Route::get('privacy', function (){
        return view('privacy');
    });

    Route::get('/', 'HomeController@display')->name('home');
    Route::get('home', 'HomeController@display')->name('home');
});

Auth::routes();

Route::get('personal', 'AccountController@detail')->middleware('auth');

Route::group(['middleware' =>  ['auth']], function (){
    Route::post('crop', 'PictureController@crop');
    Route::post('upload', 'PictureController@upload');

    Route::resource('account', 'AccountController',
        array('only' => array('update', 'store')));

    Route::resource('sns', 'SnsController');

    Route::resource('locations', 'LocationController',
        array('only' => array('index','show')));
    Route::get('department/{id}', 'LocationController@department');
    Route::get('cities/{id}', 'LocationController@city');

});

// API ROUTES ==================================
Route::group(['prefix' => 'api', 'middleware' =>  ['active']], function() {
    Route::get('home/projects',  'HomeController@index');

    Route::get('filter', 'ProjectController@refresh');

    Route::get('questions/{id}', 'QuestionController@display');
    Route::get('questionRelated/{id}', 'QuestionController@relates');
    Route::get('questionTags', 'QuestionController@tags');

    Route::post('questionFollow', 'QuestionController@follow');

    Route::resource('answers', 'QuestionAnswerController',
        array('only' => array('index','show')));

    Route::resource('recruitments', 'RecruitmentController',
        array('only' => array('index','show')));

    Route::get('profile/creator/{id}', 'ProfileController@plans');
    Route::get('profile/participator/{id}', 'ProfileController@members');
    Route::get('profile/follower/{id}', 'ProfileController@follows');
    Route::get('profile/lover/{id}', 'ProfileController@follows');

    Route::get('common/projects/{id}', 'ProjectController@commonFollowers');
    Route::get('common/friends/{id}', 'RelationController@commonFriends');

    Route::resource('users', 'UserController',
        array('only' => array('index','show')));

    Route::get('my', function (){
        return Auth::user();
    });

    Route::get('mine/projects', 'UserController@projects');

    Route::get('reports', 'ReportController@index');

    Route::resource('reports/love', 'ReportLoverController',
        array('only' => array('index','show')));
    Route::resource('reports/comment', 'ReportCommentController',
        array('only' => array('index','show')));

    Route::resource('projects', 'ProjectController',
        array('only' => array('index','show')));

    Route::get('searchItems', 'ProjectController@search');
    Route::get('search', 'ProjectController@search');

    Route::get('project/reports/{id}', 'ProjectController@reports');
    Route::put('project/followers/{id}', 'ProjectController@followers');
    Route::put('project/lovers/{id}', 'ProjectController@lovers');

    Route::get('location/projects', 'LocationController@projects');

    Route::resource('occupations', 'OccupationController',
        array('only' => array('index','show')));

    Route::resource('genres', 'GenreController',
        array('only' => array('index','show')));

    Route::resource('applications', 'ApplicationController',
        array('only' => array('index','show')));

    Route::resource('messages', 'MessageController',
        array('only' => array('index','show')));

    Route::resource('invitations', 'InvitationController',
        array('only' => array('index','show')));

    Route::resource('reminders', 'ReminderController',
        array('only' => array('index','show')));

    Route::resource('comments', 'CommentController',
        array('only' => array('index','show')));

    Route::resource('events', 'EventController',
        array('only' => array('show', 'destroy')));

    Route::resource('teams', 'TeamController',
        array('only' => array('index', 'show')));

    Route::resource('budgets', 'BudgetController',
        array('only' => array('index','show')));

    Route::resource('relations', 'RelationController',
        array('only' => array('index','show')));

    Route::get('myrelation', 'RelationController@mine');
    Route::get('friends/{id}', 'RelationController@friends');
    Route::get('fans/{id}', 'RelationController@fans');
    Route::get('idols/{id}', 'RelationController@idols');

});

Route::group(['middleware' =>  ['professional']], function() {
    Route::get('api/videos', 'VideoController@refresh');

    Route::resource('videos', 'VideoController');
});

Route::group(['prefix' => 'person', 'middleware' =>  ['active']], function() {
    Route::get('questions/{id}',  'QuestionController@personal');

    Route::get('asks/{id}', 'QuestionController@asks');
    Route::get('answers/{id}', 'QuestionController@answers');
    Route::get('follows/{id}', 'QuestionController@follows');
    Route::get('supports/{id}', 'QuestionController@supports');

    Route::get('reports/{id}', 'ReportController@personal');
    Route::get('writes/{id}', 'ReportController@writes');
    Route::get('loves/{id}', 'ReportController@loves');
    Route::get('comments/{id}', 'ReportController@comments');
});

Route::group(['prefix' => 'admin', 'middleware' =>  ['active']], function() {
    Route::resource('users', 'UserController',
        array('only' => array('update','store')));

    Route::resource('projects', 'ProjectController',
        array('only' => array('update', 'store', 'destroy')));

    Route::get('projects/{id}', 'ProjectController@edit');
    Route::post('projects/description', 'ProjectController@description');
    Route::post('finish', 'ProjectController@finish');

    Route::resource('teams', 'TeamController',
        array('only' => array('update', 'store', 'destroy')));

    Route::resource('answers', 'QuestionAnswerController',
        array('only' => array('update','store', 'destroy')));

    Route::resource('preparations', 'PreparationController');
    Route::post('send', 'PreparationController@send');
    Route::post('preparation', 'PreparationController@description');
    Route::get('preparation/{id}', 'PreparationController@preview');

    Route::resource('comment/projects', 'CommentController',
        array('only' => array('update','store', 'destroy')));

    Route::get('reports/{id}', 'ReportController@edit');
    Route::post('reports/{id}', 'ReportController@update');
    Route::resource('reports', 'ReportController',
        array('only' => array('store', 'destroy')));
    Route::resource('reports/love', 'ReportLoverController',
        array('only' => array('update', 'store', 'destroy')));
    Route::resource('comment/reports', 'ReportCommentController',
        array('only' => array('update', 'store', 'destroy')));

    Route::resource('scripts', 'ScriptController',
        array('only' => array('update','store', 'destroy')));
    Route::resource('budgets', 'BudgetController',
        array('only' => array('update','store', 'destroy')));
    Route::resource('sponsors', 'SponsorController',
        array('only' => array('update','store', 'destroy')));
    Route::resource('recruitment', 'RecruitmentController',
        array('only' => array('update','store', 'destroy')));

    Route::resource('invitations', 'InvitationController',
        array('only' => array('update','store', 'destroy')));
    Route::resource('applications', 'ApplicationController',
        array('only' => array('update','store', 'destroy')));

    Route::resource('reminders', 'ReminderController',
        array('only' => array('update','store', 'destroy')));

    Route::put('check/applications/{id}', 'ApplicationController@check');

    Route::resource('messages', 'MessageController',
        array('only' => array('update','store', 'destroy')));

    Route::resource('relations', 'RelationController',
        array('only' => array('update')));

    Route::get('preparationsCount', 'AccountController@preparations');
    Route::get('messagesCount', 'AccountController@messages');

    Route::get('occupations', 'OccupationController@admin');
    Route::get('question/{id}', 'QuestionController@edit');
    Route::post('question', 'QuestionController@change');
    Route::resource('questions', 'QuestionController',
        array('only' => array('update','store', 'destroy')));
});