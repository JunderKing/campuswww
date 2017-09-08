<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

//Route::middleware('auth:api')->get('/user', function (Request $request) {
    //return $request->user();
//});
Route::group(['prefix' => 'campus', 'namespace' => 'Campus'], function(){
    //UserController
    Route::any('login', 'UserController@login');
    Route::any('getUserInfo', 'UserController@getUserInfo');
    Route::any('chgCurProject', 'UserController@chgCurProject');
    Route::any('chgCurActivity', 'UserController@chgCurActivity');
    Route::any('getWxcode', 'UserController@getWxcode');
    Route::any('getQrcode', 'UserController@getQrcode');
    Route::any('addAdmin', 'UserController@addAdmin');
    Route::any('delAdmin', 'UserController@delAdmin');
    //SchoolController
    Route::any('addSchool', 'SchoolController@addSchool');
    Route::any('delSchool', 'SchoolController@delSchool');
    Route::any('updSchlInfo', 'SchoolController@updSchlInfo');
    Route::any('getSchlInfo', 'SchoolController@getSchlInfo');
    Route::any('getSchlList', 'SchoolController@getSchlList');
    Route::any('addSchlAdmin', 'SchoolController@addSchlAdmin');
    Route::any('delSchlAdmin', 'SchoolController@delSchlAdmin');
    Route::any('addOrger', 'SchoolController@addOrger');
    Route::any('delOrger', 'SchoolController@delOrger');
    Route::any('getOrgerInfo', 'SchoolController@getOrgerInfo');
    //ProjectController
    Route::any('addProject', 'ProjectController@addProject');
    Route::any('delProject', 'ProjectController@delProject');
    Route::any('updProjInfo', 'ProjectController@updProjInfo');
    Route::any('getProjInfo', 'ProjectController@getProjInfo');
    Route::any('getUserProjInfo', 'ProjectController@getUserProjInfo');
    Route::any('getUserProjList', 'ProjectController@getUserProjList');
    Route::any('getAppProjList', 'ProjectController@getAppProjList');
    Route::any('getActProjList', 'ProjectController@getActProjList');
    Route::any('getAvlProjList', 'ProjectController@getAvlProjList');
    Route::any('addProjMember', 'ProjectController@addProjMember');
    Route::any('delProjMember', 'ProjectController@delProjMember');
    //PostController
    Route::any('addPost', 'PostController@addPost');
    Route::any('delPost', 'PostController@delPost');
    Route::any('getPostInfo', 'PostController@getPostInfo');
    Route::any('getPostList', 'PostController@getPostList');
    //ComntController
    Route::any('addComnt', 'ComntController@addComnt');
    Route::any('delComnt', 'ComntController@delComnt');
    Route::any('getComnt', 'ComntController@getComnt');
    Route::any('upsComntScore', 'ComntController@upsComntScore');
    Route::any('getComntScore', 'ComntController@getComntScore');
    Route::any('addReply', 'ComntController@addReply');
});

Route::group(['prefix' => 'spark', 'namespace' => 'Spark'], function(){
    //FestivalController
    Route::any('addFestival', 'FestivalController@addFestival');
    Route::any('delFestival', 'FestivalController@delFestival');
    Route::any('updFestInfo', 'FestivalController@updFestInfo');
    Route::any('getFestInfo', 'FestivalController@getFestInfo');
    Route::any('getUserFestInfo', 'FestivalController@getUserFestInfo');
    Route::any('getAllFestList', 'FestivalController@getAllFestList');
    Route::any('addFestProject', 'FestivalController@addFestProject');
    Route::any('delFestProject', 'FestivalController@delFestProject');
    Route::any('addFestMentor', 'FestivalController@addFestMentor');
    Route::any('delFestMentor', 'FestivalController@delFestMentor');
    //ProgressController
    Route::any('getProgInfo', 'ProgressController@getProgInfo');
    Route::any('updProgImage', 'ProgressController@updProgImage');
    Route::any('updProgContent', 'ProgressController@updProgContent');
});

Route::group(['prefix' => 'speedup', 'namespace' => 'Speedup'], function(){
    //CampController
    Route::any('addCamp', 'CampController@addCamp');
    Route::any('delCamp', 'CampController@delCamp');
    Route::any('updCampInfo', 'CampController@updCampInfo');
    Route::any('getCampInfo', 'CampController@getCampInfo');
    Route::any('getUserCampInfo', 'CampController@getUserCampInfo');
    Route::any('addCampProject', 'CampController@addCampProject');
    Route::any('delCampProject', 'CampController@delCampProject');
    Route::any('addCampMentor', 'CampController@addCampMentor');
    Route::any('delCampMentor', 'CampController@delCampMentor');
    //RecordController
    Route::any('addRecord', 'RecordController@addRecord');
    Route::any('delRecord', 'RecordController@delRecord');
    Route::any('updRecInfo', 'RecordController@updRecInfo');
    Route::any('getRecInfo', 'RecordController@getRecInfo');
    Route::any('getRecList', 'RecordController@getRecList');
    //GridController
    Route::any('updGridInfo', 'GridController@updGridInfo');
    Route::any('getGridInfo', 'GridController@getGridInfo');
    Route::any('getGridLog', 'GridController@getGridLog');
    Route::any('getCanvasInfo', 'GridController@getCanvasInfo');
    //CardController
    Route::any('addCard', 'CardController@addCard');
    Route::any('delCard', 'CardController@delCard');
    Route::any('updCardInfo', 'CardController@updCardInfo');
    Route::any('getCardInfo', 'CardController@getCardInfo');
    Route::any('getGridCardList', 'CardController@getGridCardList');
    Route::any('getProjCardList', 'CardController@getProjCardList');
});

Route::group(['prefix' => 'venture', 'namespace' => 'Venture'], function(){
    //MeetingController
    Route::any('addMeeting', 'MeetingController@addMeeting');
    Route::any('delMeeting', 'MeetingController@delMeeting');
    Route::any('updMeetInfo', 'MeetingController@updMeetInfo');
    Route::any('getMeetInfo', 'MeetingController@getMeetInfo');
    Route::any('getUserMeetInfo', 'MeetingController@getUserMeetInfo');
    Route::any('addMeetProject', 'MeetingController@addMeetProject');
    Route::any('delMeetProject', 'MeetingController@delMeetProject');
    Route::any('addMeetInvor', 'MeetingController@addMeetInvor');
    Route::any('delMeetInvor', 'MeetingController@delMeetInvor');
    //InvorController
    Route::any('addInvor', 'InvorController@addInvor');
    Route::any('delInvor', 'InvorController@delInor');
    Route::any('updInvorInfo', 'InvorController@updInvorInfo');
    Route::any('getInvorInfo', 'InvorController@getInvorInfo');
    Route::any('getMeetInvorList', 'InvorController@getMeetInvorList');
});
