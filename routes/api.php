<?php

use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================ SIGNUP API ================================
Route::post('/signup', [App\Http\Controllers\UserController::class, 'signup']);

// ============================ LOGIN API ================================
Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);

// ============================ TEST API ============================
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

// ============================ ABOUT API ================================
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/about/education', [App\Http\Controllers\AboutController::class, 'createEducation']);
    Route::post('/about/certification', [App\Http\Controllers\AboutController::class, 'createCertification']);
    Route::post('/about/info', [App\Http\Controllers\AboutController::class, 'createUserInfo']);
    Route::post('/about/overview', [App\Http\Controllers\AboutController::class, 'createUserOverview']);
    Route::post('/about/skills', [App\Http\Controllers\AboutController::class, 'createUserSkill']);

    // ============================ ABOUT GET API'S ================================
    Route::get('/about/overview/{id}', [App\Http\Controllers\AboutController::class, 'getUserOverview']);
    Route::get('/about/education/{id}', [App\Http\Controllers\AboutController::class, 'getUserEducation']);
    Route::get('/about/certification/{id}', [App\Http\Controllers\AboutController::class, 'getUserCertification']);
    Route::get('/about/skills/{id}', [App\Http\Controllers\AboutController::class, 'getUserSkill']);
    Route::get('/about/info/{id}', [App\Http\Controllers\AboutController::class, 'getUserInfo']);

    // ============================ ABOUT UPDATE API'S ================================
    Route::put('/about/overview/{id}', [App\Http\Controllers\AboutController::class, 'updateUserOverview']);
    Route::put('/about/education/{educationId}', [App\Http\Controllers\AboutController::class, 'updateUserEducation']);
    Route::put('/about/certification/{certificationId}', [App\Http\Controllers\AboutController::class, 'updateUserCertification']);
    Route::put('/about/skills/{skillId}', [App\Http\Controllers\AboutController::class, 'updateUserSkill']);
    Route::put('/about/info/{infoId}', [App\Http\Controllers\AboutController::class, 'updateUserInfo']);

    // ============================ ABOUT DELETE API'S ================================
    Route::delete('/about/overview/{id}', [App\Http\Controllers\AboutController::class, 'deleteUserOverview']);
    Route::delete('/about/education/{educationId}', [App\Http\Controllers\AboutController::class, 'deleteUserEducation']);
    Route::delete('/about/certification/{certificationId}', [App\Http\Controllers\AboutController::class, 'deleteUserCertification']);
    Route::delete('/about/skills/{skillId}', [App\Http\Controllers\AboutController::class, 'deleteUserSkill']);
    Route::delete('/about/info/{infoId}', [App\Http\Controllers\AboutController::class, 'deleteUserInfo']);

    // ======================= GET RANDOM USERS ============================================================
    Route::post('/users/getrandomusers', [App\Http\Controllers\UserController::class, 'getrandomusers']);
});

// ============================ CHECK AUTH THAT USER IS LOGGED IN API ================================
Route::middleware('auth:sanctum')->get('/check-auth', [App\Http\Controllers\UserController::class, 'checkAuth']);

// ============================ GET USER PROFILE DATA API ================================
Route::get('/user/profile/{user_id}', [App\Http\Controllers\UserProfileController::class, 'getProfile']);
Route::post('/user/profile/{user_id}', [App\Http\Controllers\UserProfileController::class, 'updateProfile']);
Route::delete('/user/profile/{user_id}', [App\Http\Controllers\UserProfileController::class, 'deleteProfileFields']);

// ============================ LOGOUT API ================================
Route::middleware('auth:sanctum')->post('/logout', [App\Http\Controllers\UserController::class, 'logout']);

// ============================ GROUPS API ================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/groups', [App\Http\Controllers\GroupController::class, 'store']);
    Route::post('/groups/{groupid}', [App\Http\Controllers\GroupController::class, 'update']);
    // =================================== GET ONE GROUP ===================================
    Route::get('/groups/{groupid}', [App\Http\Controllers\GroupController::class, 'show']);
    // =================================== GET ALL GROUP ===================================
    Route::get('/groups', [App\Http\Controllers\GroupController::class, 'showall']);
    Route::delete('/groups/{groupid}', [App\Http\Controllers\GroupController::class, 'deleteGroupFields']);
});

// ============================ PAGES API ================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pages', [App\Http\Controllers\PageController::class, 'store']);
});

// ============================ POSTS API ================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/textposts', [App\Http\Controllers\PostController::class, 'storetext']);

    // ======================== POST BROWSING API THAT FETCH ONLY 3 POSTS PER REQUEST===================
    Route::post('/allposts', [App\Http\Controllers\PostController::class, 'getAllPosts']);
    // ========== POST BROWSING API (IN PROFILE SECTION) THAT FETCH ONLY 3 POSTS PER REQUEST============
    Route::post('/getauthenticatedposts', [App\Http\Controllers\PostController::class, 'getauthenticatedPosts']);
    Route::post('/imageposts', [App\Http\Controllers\PostController::class, 'storeimage']);
    Route::post('/videoposts', [App\Http\Controllers\PostController::class, 'storevideo']);
    // ========================== POST POLL API's ==============================================
    Route::post('/pollposts', [App\Http\Controllers\PollController::class, 'storepoll']);
    Route::post('/postsreactions', [App\Http\Controllers\PostController::class, 'storereaction']);
    Route::post('/getcommentsreplies', [App\Http\Controllers\PostController::class, 'getcomments']);
    Route::post('/storecommentsreplies', [App\Http\Controllers\PostController::class, 'storecomment']);
    Route::post('/storereply', [App\Http\Controllers\PostController::class, 'storereply']);
    Route::post('/likeacomment', [App\Http\Controllers\PostController::class, 'likeacomment']);
    Route::post('/savedapost', [App\Http\Controllers\PostController::class, 'savedapost']);
    Route::post('/getsavedposts', [App\Http\Controllers\PostController::class, 'getsavedposts']);
});

// ============================ REQUEST MEMBERSHIP API ================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/requestmembership', [App\Http\Controllers\UserMembershipController::class, 'requestMembership']);
    Route::post('/getcompanies', [App\Http\Controllers\UserMembershipController::class, 'getCompanies']);
    Route::post('/getUserMemberships', [App\Http\Controllers\UserMembershipController::class, 'getUserMemberships']);
    Route::post('/storecompaniesresponses', [App\Http\Controllers\UserMembershipController::class, 'storecompaniesresponses']);
});


// ============================ FRIEND REQUEST API ================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/friends/send', [App\Http\Controllers\FriendController::class, 'sendRequest']);
    Route::post('/friends/{id}/accept', [App\Http\Controllers\FriendController::class, 'acceptRequest']);
    Route::post('/friends/{id}/reject', [App\Http\Controllers\FriendController::class, 'rejectRequest']);
    Route::get('/friends/received', [App\Http\Controllers\FriendController::class, 'receivedRequests']);
    Route::get('/friends/sent', [App\Http\Controllers\FriendController::class, 'sentRequests']);
    Route::get('/friends', [App\Http\Controllers\FriendController::class, 'getFriends']);
    Route::get('/friends/search', [App\Http\Controllers\FriendController::class, 'search']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/messages/{friendId}', [MessageController::class, 'getMessages']);
    Route::post('/messages', [MessageController::class, 'sendMessage']);
    Route::post('/messages/image', [MessageController::class, 'sendImageMessage']);
    Route::post('/messages/file', [MessageController::class, 'sendFileMessage']);
    Route::post('/messages/post', [MessageController::class, 'sendPostMessage']);
    Route::post('/messages/voice', [MessageController::class, 'sendVoiceMessage']);
    Route::post('/messages/clear-chat', [MessageController::class, 'clearChat']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/group-chats', [GroupChatController::class, 'store']);
    Route::post('/group-chats/add-member', [GroupChatController::class, 'AddMembers']);
    Route::get('/group-chats/{groupChat}', [GroupChatController::class, 'show']);
    Route::get('/group-chats', [GroupChatController::class, 'index']);
    Route::post('/group-chats/{groupid}/update', [GroupChatController::class, 'updateDetails']);
    Route::post('/group-chats/remove-member', [GroupChatController::class, 'removeMember']);

    // Group Message Routes
    Route::post('/group-messages', [GroupMessageController::class, 'sendMessage']);
    Route::post('/group-messages/mark-read', [GroupMessageController::class, 'markAsRead']);
    Route::post('/group-messages/{group}/members', [App\Http\Controllers\GroupMessageController::class, 'getMembers']);
    Route::post('/group-messages/image', [GroupMessageController::class, 'sendImageMessage']);
    Route::post('/group-messages/file', [GroupMessageController::class, 'sendFileMessage']);
    Route::post('/group-messages/voice', [GroupMessageController::class, 'sendVoiceMessage']);
    Route::post('/group-messages/clear-chat', [GroupMessageController::class, 'clearChat']);
});


// ============================ STORIES API ================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/stories/text', [App\Http\Controllers\StoryController::class, 'createTextStory']);
    Route::post('/stories/image', [App\Http\Controllers\StoryController::class, 'createImageStory']);
    Route::post('/stories/postimage', [App\Http\Controllers\StoryController::class, 'createPostImageStory']);
    Route::post('/stories/posttext', [App\Http\Controllers\StoryController::class, 'createPostTextStory']);
    Route::post('/stories/postvideo', [App\Http\Controllers\StoryController::class, 'createPostVideoStory']);
    Route::get('/stories', [App\Http\Controllers\StoryController::class, 'getFriendsStories']);
    Route::get('/user-stories/{userId}', [App\Http\Controllers\StoryController::class, 'getUserStories']);
});
