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
Route::get('terms', function()
{
    return View::make('terms');
});
Route::get('inaccessible', function()
{
    return View::make('inaccessible');
});

Route::get('/festivals/{id}', 'FestivalController@show');

Route::resource('occupations', 'OccupationController',
    array('only' => array('index','show')));
Route::get('/', 'HomeController@show');
Route::get('/index', 'HomeController@welcome');
Route::get('/discover', 'ProjectController@discover');
Route::get('/festival', 'FestivalController@index');
Route::get('api/filter', 'ProjectController@refresh');
Route::get('api/festivals', 'FestivalController@display');
Route::resource('users', 'UserController',
    array('only' => array('index','show')));
Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/activation/{key}/{uid}', 'Auth\RegisterController@activation');

Route::resource('languages', 'LanguageController');

Route::group(['middleware' =>  ['active']], function () {

/*
    Route::get('/alipay/pay','AlipayController@pay');

    Route::post('/alipay/paid','AlipayController@result');
*/
    Route::get('api/rules', 'FestivalController@getRelatedRules');

    Route::get('myprojects', 'projectController@favorites');

    Route::get('festivals', 'FestivalController@index');
    Route::post('festivals', 'FestivalController@store');
    Route::put('festivals/{id}', 'FestivalController@update');

    Route::get('myfestivals', 'FestivalController@favorites');
    Route::get('my/festivals', 'FestivalController@mine');
    Route::get('/units/{id}', 'FestivalController@unit');
    Route::post('/units/{id}', 'FestivalController@inscription');
    Route::post('/rules/{id}', 'FestivalController@validFilm');


    Route::resource('projects', 'ProjectController',
        array('only' => array('index','show')));
    Route::get('/contacts', 'FilmController@getContacts');

    Route::resource('archives', 'ArchiveController');
    Route::resource('plays', 'PlayController');
    Route::resource('movies', 'MovieController');
    Route::resource('entries', 'EntryController');
    Route::resource('filmaker', 'FilmakerController');
    Route::put('filmaker/contact/{id}/{contact_id}', 'FilmakerController@changeContact');
    Route::post('filmaker/contact/{id}', 'FilmakerController@contact');
    Route::delete('filmaker/contact/{id}', 'FilmakerController@deleteContact');

    Route::resource('movies', 'MovieController');
    Route::resource('profile', 'ProfileController');
    Route::post('contact', 'AccountController@contact');
    Route::resource('account', 'AccountController',
        array('only' => array('index','show')));
/*
    Route::resource('questions', 'QuestionController',
        array('only' => array('index','show')));
*/

    Route::get('ask/{id}', 'ProjectController@ask');

    Route::get('notifications', 'ProfileController@notifications');
    Route::get('messages', 'ProfileController@messages');

    Route::get('project/{id}', 'ProjectController@detail');

    Route::get('report/{id}','ReportController@create');
    Route::get('reports/{id}', 'ReportController@show');

    Route::get('contact', function (){
        return view('contact');
    });

    Route::get('join', function (){
        return view('join');
    });

    Route::get('privacy', function (){
        return view('privacy');
    });

    Route::get('/home', 'HomeController@display')->name('home');

    Route::resource('entry', 'EntryController');
});

Route::get('guest/{id}', 'GuestController@show');
Route::get('personal', 'AccountController@detail')->middleware('auth');

Route::group(['middleware' =>  ['auth']], function (){
    Route::post('crop', 'PictureController@crop');
    Route::post('uploads', 'PictureController@upload');

    Route::resource('account', 'AccountController',
        array('only' => array('update', 'store')));
    Route::post('accountInfo', 'AccountController@info');
    Route::post('accountOccupation', 'AccountController@saveOccupation');
    Route::delete('accountOccupation/{id}', 'AccountController@removeOccupation');
    Route::post('accountPresentation', 'AccountController@presentation');
    Route::resource('sns', 'SnsController');

    Route::resource('locations', 'LocationController',
        array('only' => array('index','show', 'update')));
    Route::get('country/{id}', 'LocationController@citiesByCountry');
    Route::get('departments/{id}', 'LocationController@departments');
    Route::get('departCities/{id}', 'LocationController@departCities');
    Route::get('cities/{id}', 'LocationController@cities');
});

// API ROUTES ==================================
Route::group(['prefix' => 'api', 'middleware' =>  ['active']], function() {
    Route::get('search/{key}', 'HomeController@search');

    Route::get('home/projects',  'HomeController@index');

    Route::resource('recruitments', 'RecruitmentController',
        array('only' => array('index','show')));

    Route::get('profile/creator/{id}', 'ProfileController@plans');
    Route::get('profile/participator/{id}', 'ProfileController@members');
    Route::get('profile/follower/{id}', 'ProfileController@follows');
    Route::get('profile/lover/{id}', 'ProfileController@loves');

    Route::get('common/projects/{id}', 'ProjectController@commonFollowers');
    Route::get('common/friends/{id}', 'RelationController@commonFriends');

    Route::resource('sponsors', 'SponsorController',
        array('only' => array('update','store', 'destroy')));

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

    Route::get('project/reports/{id}', 'ProjectController@reports');
    Route::put('project/followers/{id}', 'ProjectController@followers');
    Route::put('project/lovers/{id}', 'ProjectController@lovers');

    Route::get('location/projects', 'LocationController@projects');

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

Route::group(['prefix'=>'archive', 'middleware'=>['active']], function (){
    Route::get('/creation', 'ArchiveController@creation');
    Route::get('/contacts', 'ArchiveController@getContacts');
    Route::get('/makers', 'ArchiveController@getMakers');

    Route::post('/{id}/title', 'ArchiveController@saveTitle');
    Route::delete('/{id}/title/{lang_id}', 'ArchiveController@removeTitle');

    Route::post('/{id}/synopsis', 'ArchiveController@saveSynopsis');
    Route::delete('/{id}/synopsis/{lang_id}', 'ArchiveController@removeSynopsis');

    Route::post('/{id}/productions',  'ArchiveController@saveProductions');
    Route::delete('/{id}/productions/{country_id}', 'ArchiveController@removeProduction');

    Route::post('/{id}/languages',  'ArchiveController@saveLanguages');
    Route::delete('/{id}/languages/{language_id}', 'ArchiveController@removeLanguage');

    Route::post('/{id}/upload/{format}', 'ArchiveController@saveFile');
    Route::post('/{id}/remove/{format}', 'ArchiveController@removeFile');

    Route::put('/{id}/country', 'ArchiveController@updateCountry');
    Route::put('/{id}/conlange', 'ArchiveController@updateConlange');

    Route::post('maker/{format}', 'ArchiveController@saveMaker');
    Route::delete('/{id}/maker', 'ArchiveController@deleteMakers');
    Route::get('/{id}/{position}', 'ArchiveController@getPosition');
});

Route::group(['prefix' => 'play', 'middleware' =>  ['active']], function() {
    Route::get('/{id}/upload',  'PlayController@uploadForm');
    Route::put('/{id}/credit', 'PlayController@saveCredits');
    Route::delete('/{id}/credit', 'PlayController@deleteCredits');
    Route::post('/{id}/complete', 'PlayController@complete');
});

Route::group(['prefix' => 'movie', 'middleware' =>  ['active']], function() {

    Route::post('/{id}/festival', 'ArchiveController@saveFestival');
    Route::delete('/{id}/festival/{festival_id}', 'ArchiveController@removeFestival');

    Route::post('/{id}/diffusion', 'MovieController@saveDiffusion');
    Route::delete('/{id}/diffusion/{diffusion_id}', 'MovieController@removeDiffusion');

    Route::post('/{id}/theater', 'ArchiveController@saveTheater');
    Route::delete('/{id}/theater/{theater_id}', 'ArchiveController@removeTheater');

    Route::post('/{id}/shootings',  'MovieController@saveShootings');
    Route::delete('/{id}/shootings/{country_id}', 'MovieController@removeShooting');


    Route::post('{id}/screen/{format}', 'MovieController@saveScreen');
    Route::delete('{id}/screen/{format}', 'MovieController@removeScreen');

    Route::put('/credit', 'MovieController@createCredits');
    Route::post('/{id}/credit', 'MovieController@saveCredits');
    Route::delete('/{id}/credit', 'MovieController@deleteCredits');

    Route::post('/{id}/preview', 'MovieController@savePreview');
    Route::post('/{id}/complete', 'MovieController@complete');
});

Route::group(['prefix' => 'person', 'middleware' =>  ['active']], function() {
    /*Route::get('questions/{id}',  'QuestionController@personal');

    Route::get('asks/{id}', 'QuestionController@asks');
    Route::get('answers/{id}', 'QuestionController@answers');
    Route::get('follows/{id}', 'QuestionController@follows');
    Route::get('supports/{id}', 'QuestionController@supports');*/

    Route::get('reports/{id}', 'ReportController@personal');
    Route::get('writes/{id}', 'ReportController@writes');
    Route::get('loves/{id}', 'ReportController@loves');
    Route::get('comments/{id}', 'ReportController@comments');
});

Route::group(['prefix' => 'admin', 'middleware' =>  ['active']], function() {
    Route::put('users', 'UserController@update');
    Route::post('users', 'UserController@store');

    Route::resource('projects', 'ProjectController',
        array('only' => array('update', 'store', 'destroy')));

    Route::get('projects/{id}', 'PreparationController@show');
    Route::post('projects/description', 'ProjectController@description');
    Route::post('finish', 'ProjectController@finish');

    Route::resource('teams', 'TeamController',
        array('only' => array('update', 'store', 'destroy')));

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

    Route::delete('notifications/{id}', 'ProfileController@removeNotification');
});