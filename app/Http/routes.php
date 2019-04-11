<?php
if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    // Ignores notices and reports all other kinds... and warnings
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    // error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['web']], function() {
    Route::get('/login', 'LoginController@index');
    Route::get('/logout', 'LoginController@logout');
    Route::post('/login', 'LoginController@login');
    Route::get('/moxiemanager', 'LoginController@moxiemanager');

    Route::group(['middleware' => 'auth.admin'], function() {

        Route::group(['prefix' => 'amazon', 'namespace' => 'Amazon'], function () {
            Route::group(['prefix' => 'instance'], function () {
                Route::get('start/{id}', 'InstanceController@start');
                Route::get('stop/{id}', 'InstanceController@stop');
            });
        });

        Route::group(['prefix' => 'members'], function()
        {
            $sections = [
                'members' => 'Member'
            ];

            foreach ($sections as $k => $v) {
                Route::get('/' . $k, $v . 'Controller@showIndex');
                Route::get('/' . $k . '/list', ['as' => $k . '.dt.list', 'uses' => $v . 'Controller@dtlist']);
                Route::get('/' . $k . '/create', $v . 'Controller@showCreate');
                Route::get('/' . $k . '/edit/{id}', $v . 'Controller@showEdit');
                Route::post('/' . $k . '/create', $v . 'Controller@create');
                Route::post('/' . $k . '/edit/{id}', $v . 'Controller@edit');
                Route::post('/' . $k . '/delete/{id}', $v . 'Controller@delete');
            }

            Route::get('/members/export', 'MemberController@showExport');
            Route::post('/members/export', 'MemberController@export');

            Route::get('/members/import', 'MemberController@showImport');
            Route::post('/members/import', 'MemberController@import');
            Route::get('/members/import-result/{id}', ['as' => 'members.import.result', 'uses' => 'MemberController@showImportResult']);
            Route::post('/members/import-finalise', 'MemberController@importFinalise');

            Route::get('/members/login/{id}', 'MemberController@loginAs');
        });

        Route::group(['prefix' => 'instructors'], function()
        {
            $sections = [
                'instructors' => 'Instructor',
                'bulk-packages' => 'BulkPackage',
                'lists' => 'InstructorList',
                'photos' => 'InstructorPhoto',
                'videos' => 'InstructorVideo'
            ];

            foreach ($sections as $k => $v) {
                Route::get('/' . $k, $v . 'Controller@showIndex');
                Route::get('/' . $k . '/list', ['as' => $k . '.dt.list', 'uses' => $v . 'Controller@dtlist']);
                Route::get('/' . $k . '/create', $v . 'Controller@showCreate');
                Route::get('/' . $k . '/edit/{id}', $v . 'Controller@showEdit');
                Route::post('/' . $k . '/create', $v . 'Controller@create');
                Route::post('/' . $k . '/edit/{id}', $v . 'Controller@edit');
                Route::post('/' . $k . '/delete/{id}', $v . 'Controller@delete');
            }

            Route::get('/instructors/export', 'InstructorController@showExport');
            Route::post('/instructors/export', 'InstructorController@export');

            Route::get('/instructors/earnings', 'InstructorController@showEarnings');
            Route::post('/instructors/earnings', 'InstructorController@earnings');

            Route::post('/lists/action/{id}', 'InstructorListController@action');
            Route::post('/lists/import/{id}', 'InstructorListController@import');

            Route::get('/instructors/login/{id}', 'InstructorController@loginAs');
        });

        Route::group(['prefix' => 'classes'], function() {
            $sections = [
                'classes' => 'ClassEvent',
                'classes-types' => 'ClassType',
                'bookings' => 'Booking'
            ];

            foreach ($sections as $k => $v) {
                Route::get('/' . $k, $v . 'Controller@showIndex');
                Route::get('/' . $k . '/list', ['as' => $k . '.dt.list', 'uses' => $v . 'Controller@dtlist']);
                Route::get('/' . $k . '/create', $v . 'Controller@showCreate');
                Route::get('/' . $k . '/edit/{id}', $v . 'Controller@showEdit');
                Route::post('/' . $k . '/create', $v . 'Controller@create');
                Route::post('/' . $k . '/edit/{id}', $v . 'Controller@edit');
                Route::post('/' . $k . '/delete/{id}', $v . 'Controller@delete');
            }

            Route::get('/classes/export', 'ClassEventController@showExport');
            Route::post('/classes/export', 'ClassEventController@export');

            Route::get('/bookings/export', 'BookingController@showExport');
            Route::post('/bookings/export', 'BookingController@export');
        });

        Route::group(['prefix' => 'orders'], function() {
            $sections = [
                'orders' => 'Order',
                'statuses' => 'OrderStatus',
                'discount-codes' => 'DiscountCode',
                'discount-code-redemptions' => 'DiscountCodeRedemption'
            ];

            foreach ($sections as $k => $v) {
                Route::get('/' . $k, $v . 'Controller@showIndex');
                Route::get('/' . $k . '/list', ['as' => $k . '.dt.list', 'uses' => $v . 'Controller@dtlist']);
                Route::get('/' . $k . '/create', $v . 'Controller@showCreate');
                Route::get('/' . $k . '/edit/{id}', $v . 'Controller@showEdit');
                Route::post('/' . $k . '/create', $v . 'Controller@create');
                Route::post('/' . $k . '/edit/{id}', $v . 'Controller@edit');
                Route::post('/' . $k . '/delete/{id}', $v . 'Controller@delete');
            }

            Route::get('/discount-codes/export', 'DiscountCodeController@showExport');
            Route::post('/discount-codes/export', 'DiscountCodeController@export');
        });

        Route::group(['prefix' => 'content'], function() {
            $sections = [
                'pages' => 'Page',
                'sliders' => 'Slider',
                'grid' => 'Grid',
                'hiw' => 'HIW',
                'newsfeed' => 'Newsfeed'
            ];

            foreach ($sections as $k => $v) {
                Route::get('/' . $k, $v . 'Controller@showIndex');
                Route::get('/' . $k . '/list', ['as' => $k . '.dt.list', 'uses' => $v . 'Controller@dtlist']);
                Route::get('/' . $k . '/create', $v . 'Controller@showCreate');
                Route::get('/' . $k . '/edit/{id}', $v . 'Controller@showEdit');
                Route::post('/' . $k . '/create', $v . 'Controller@create');
                Route::post('/' . $k . '/edit/{id}', $v . 'Controller@edit');
                Route::post('/' . $k . '/delete/{id}', $v . 'Controller@delete');
            }

            Route::get('/blocks', 'BlockController@showIndex');
            Route::get('/blocks/list', ['as' => 'blocks.dt.list', 'uses' => 'BlockController@dtlist']);
            Route::get('/blocks/edit/{id}', 'BlockController@showEdit');
            Route::post('/blocks/edit/{id}', 'BlockController@edit');
        });

        Route::group(['prefix' => 'faqs'], function() {
            $sections = [
                'faqs' => 'FAQ', 
                'faqs-categories' => 'FAQCategory'
            ];

            foreach ($sections as $k => $v) {
                Route::get('/' . $k, $v . 'Controller@showIndex');
                Route::get('/' . $k . '/list', ['as' => $k . '.dt.list', 'uses' => $v . 'Controller@dtlist']);
                Route::get('/' . $k . '/create', $v . 'Controller@showCreate');
                Route::get('/' . $k . '/edit/{id}', $v . 'Controller@showEdit');
                Route::post('/' . $k . '/create', $v . 'Controller@create');
                Route::post('/' . $k . '/edit/{id}', $v . 'Controller@edit');
                Route::post('/' . $k . '/delete/{id}', $v . 'Controller@delete');
            }
        });

        Route::group(['prefix' => 'moderation'], function() {
            $sections = [
                'bans' => 'Ban',
                'reported-users' => 'ReportedUser'
            ];

            foreach ($sections as $k => $v) {
                Route::get('/' . $k, $v . 'Controller@showIndex');
                Route::get('/' . $k . '/list', ['as' => $k . '.dt.list', 'uses' => $v . 'Controller@dtlist']);
                Route::get('/' . $k . '/create', $v . 'Controller@showCreate');
                Route::get('/' . $k . '/edit/{id}', $v . 'Controller@showEdit');
                Route::post('/' . $k . '/create', $v . 'Controller@create');
                Route::post('/' . $k . '/edit/{id}', $v . 'Controller@edit');
                Route::post('/' . $k . '/delete/{id}', $v . 'Controller@delete');
            }
        });

        Route::group(['prefix' => 'settings'], function() {
            Route::get('/general', 'SettingController@showEdit');
            Route::post('/general/edit/{id}', 'SettingController@edit');
            Route::get('/features', 'FeatureSettingController@showEdit');
            Route::post('/features/edit/{id}', 'FeatureSettingController@edit');
            Route::get('/liveswitch', 'LiveswitchSettingController@showEdit');
            Route::post('/liveswitch/edit/{id}', 'LiveswitchSettingController@edit');
            Route::get('/liveswitch/aws-instance/stop', 'LiveswitchSettingController@stopAwsInstance');
            Route::get('/liveswitch/aws-instance/start', 'LiveswitchSettingController@startAwsInstance');

            $sections = [
                'users' => 'User',
                'countries' => 'Country',
                'currencies' => 'Currency'
            ];

            foreach ($sections as $k => $v) {
                Route::get('/' . $k, $v . 'Controller@showIndex');
                Route::get('/' . $k . '/list', ['as' => $k . '.dt.list', 'uses' => $v . 'Controller@dtlist']);
                Route::get('/' . $k . '/create', $v . 'Controller@showCreate');
                Route::get('/' . $k . '/edit/{id}', $v . 'Controller@showEdit');
                Route::post('/' . $k . '/create', $v . 'Controller@create');
                Route::post('/' . $k . '/edit/{id}', $v . 'Controller@edit');
                Route::post('/' . $k . '/delete/{id}', $v . 'Controller@delete');
            }

            Route::get('/emails', 'EmailTemplateController@showIndex');
            Route::get('/emails/list', ['as' => 'emails.dt.list', 'uses' => 'EmailTemplateController@dtlist']);
            Route::get('/emails/edit/{id}', 'EmailTemplateController@showEdit');
            Route::post('/emails/edit/{id}', 'EmailTemplateController@edit');
        });
        
        // **********************************************************************************************************
        // Dashboard
        // **********************************************************************************************************

        Route::post('/charts', 'DashboardController@charts');
        Route::get('/', 'DashboardController@index');
    });
});

Route::group(['namespace' => 'Web', 'middleware' => ['web']], function() {
    Route::group(['middleware' => 'auth.member'], function()
    {
        Route::get('/member/classes', ['as' => 'web.member.classes', 'uses' => 'MemberClassController@index']);

        Route::get('/member/calendar', ['as' => 'web.member.calendar', 'uses' => 'MemberCalendarController@index']);

        Route::get('/member/instructors', ['as' => 'web.member.instructors', 'uses' => 'MemberInstructorController@index']);

        Route::get('/member/profile', ['as' => 'web.member.profile', 'uses' => 'MemberProfileController@index']);
        Route::post('/member/profile', ['as' => 'web.member.profile.editProfile', 'uses' => 'MemberProfileController@editProfile', 'middleware' => 'bancheck']);
        Route::post('/member/password', ['as' => 'web.member.profile.editPassword', 'uses' => 'MemberProfileController@editPassword']);
        Route::post('/member/photo', ['as' => 'web.member.profile.editPhoto', 'uses' => 'MemberProfileController@editPhoto']);
        Route::post('/member/interests', ['as' => 'web.member.profile.editInterests', 'uses' => 'MemberProfileController@editInterests']);
        Route::post('/member/mobile', ['as' => 'web.member.profile.editMobile', 'uses' => 'MemberProfileController@editMobile']);

        Route::get('/member/communications', ['as' => 'web.member.communications', 'uses' => 'MemberCommunicationController@index']);
        Route::post('/member/communications', ['as' => 'web.member.communications.editSettings', 'uses' => 'MemberCommunicationController@editSettings']);

        Route::get('/member/notifications', ['as' => 'web.member.notifications', 'uses' => 'MemberNotificationController@index']);

        Route::get('/member/bulk', ['as' => 'web.member.bulk', 'uses' => 'MemberBulkController@index']);

        Route::get('/member/credits', ['as' => 'web.member.credits', 'uses' => 'MemberCreditController@index']);

        Route::get('/member/newsfeed', ['as' => 'web.member.newsfeed', 'uses' => 'MemberNewsfeedController@index']);

        Route::get('/member/switch', ['as' => 'web.member.switch', 'uses' => 'MemberSwitchController@switch']);

        Route::get('/book/{id}', ['as' => 'web.book', 'uses' => 'MemberBookingController@index']);
        Route::post('/book', ['as' => 'web.book.action', 'uses' => 'MemberBookingController@book']);
        Route::get('/booking-confirmation/{id}', ['as' => 'web.book.confirmation', 'uses' => 'MemberBookingController@confirmation']);
    });

    Route::group(['middleware' => 'auth.instructor'], function()
    {
        Route::get('/instructor/dashboard', ['as' => 'web.instructor.dashboard', 'uses' => 'InstructorDashboardController@index']);

        Route::get('/instructor/stats', ['as' => 'web.instructor.stats', 'uses' => 'InstructorStatController@index']);

        Route::get('/instructor/notifications', ['as' => 'web.instructor.notifications', 'uses' => 'InstructorNotificationController@index']);

        Route::get('/instructor/classes', ['as' => 'web.instructor.classes', 'uses' => 'InstructorClassController@index']);
        Route::post('/instructor/ajax/classes/cancel', ['as' => 'web.instructor.ajax.classes.cancel', 'uses' => 'InstructorClassController@cancelClass']);

        Route::get('/instructor/create', ['as' => 'web.instructor.create', 'uses' => 'InstructorClassController@create']);
        Route::post('/instructor/create', ['as' => 'web.instructor.create.addEditClass', 'uses' => 'InstructorClassController@addEditClass']);
        Route::post('/instructor/upload-class-photo', ['as' => 'web.instructor.create.uploadClassPhoto', 'uses' => 'InstructorClassController@uploadClassPhoto']);
        Route::get('/instructor/publish/{id}', ['as' => 'web.instructor.publish', 'uses' => 'InstructorClassController@publishClass']);

        Route::get('/instructor/invite/{id}', ['as' => 'web.instructor.invite', 'uses' => 'InstructorInviteController@index']);
        Route::get('/instructor/invite-responses/{id}', ['as' => 'web.instructor.inviteResponses', 'uses' => 'InstructorInviteController@inviteResponses']);
        Route::post('/instructor/invite', ['as' => 'web.instructor.invite.send', 'uses' => 'InstructorInviteController@sendInvites']);

        Route::get('/instructor/bookings/{id}', ['as' => 'web.instructor.bookings', 'uses' => 'InstructorBookingController@index']);

        Route::get('/instructor/newsfeed', ['as' => 'web.instructor.newsfeed', 'uses' => 'InstructorNewsfeedController@index']);

        Route::get('/instructor/profile', ['as' => 'web.instructor.profile', 'uses' => 'InstructorProfileController@index']);
        Route::post('/instructor/all', ['as' => 'web.instructor.profile.editAll', 'uses' => 'InstructorProfileController@editAll', 'middleware' => 'bancheck']);
        Route::post('/instructor/photo', ['as' => 'web.instructor.profile.editPhoto', 'uses' => 'InstructorProfileController@editPhoto']);
        Route::post('/instructor/photos', ['as' => 'web.instructor.profile.editPhotos', 'uses' => 'InstructorProfileController@editPhotos']);
        Route::post('/instructor/signature', ['as' => 'web.instructor.profile.editSignature', 'uses' => 'InstructorProfileController@editSignature']);

        Route::get('/instructor/calendar', ['as' => 'web.instructor.calendar', 'uses' => 'InstructorCalendarController@index']);

        Route::get('/instructor/communications', ['as' => 'web.instructor.communications', 'uses' => 'InstructorCommunicationController@index']);
        Route::post('/instructor/communications', ['as' => 'web.instructor.communications.editSettings', 'uses' => 'InstructorCommunicationController@editSettings']);

        Route::get('/instructor/followers', ['as' => 'web.instructor.followers', 'uses' => 'InstructorFollowerController@index']);

        Route::get('/instructor/lists', ['as' => 'web.instructor.lists', 'uses' => 'InstructorListController@index']);
        Route::post('/instructor/lists', ['as' => 'web.instructor.lists.addList', 'uses' => 'InstructorListController@addList']);
        Route::get('/instructor/list-details/{id}', ['as' => 'web.instructor.listDetails', 'uses' => 'InstructorListController@listDetails']);
        Route::post('/instructor/list-user-add', ['as' => 'web.instructor.listDetails.addUser', 'uses' => 'InstructorListController@addUser']);
        Route::post('/instructor/list-user-upload', ['as' => 'web.instructor.listDetails.uploadUsers', 'uses' => 'InstructorListController@uploadUsers']);
        Route::post('/instructor/ajax/lists/delete', ['as' => 'web.instructor.ajax.lists.delete', 'uses' => 'InstructorListController@deleteList']);
        Route::post('/instructor/ajax/list-users/move', ['as' => 'web.instructor.ajax.listUsers.move', 'uses' => 'InstructorListController@moveUsers']);
        Route::post('/instructor/ajax/list-users/copy', ['as' => 'web.instructor.ajax.listUsers.copy', 'uses' => 'InstructorListController@copyUsers']);
        Route::post('/instructor/ajax/list-users/delete', ['as' => 'web.instructor.ajax.listUsers.delete', 'uses' => 'InstructorListController@deleteUsers']);

        Route::get('/instructor/bulk', ['as' => 'web.instructor.bulk', 'uses' => 'InstructorBulkController@index']);
        Route::post('/instructor/bulk', ['as' => 'web.instructor.bulk.editPackages', 'uses' => 'InstructorBulkController@editPackages']);

        Route::get('/instructor/help', ['as' => 'web.instructor.help', 'uses' => 'InstructorHelpController@index']);

        Route::get('/instructor/switch', ['as' => 'web.instructor.switch', 'uses' => 'InstructorSwitchController@switch']);
        Route::get('/instructor/switch-config', ['as' => 'web.instructor.switch.config', 'uses' => 'InstructorSwitchController@index']);
        Route::get('/instructor/switch-unlink', ['as' => 'web.instructor.switch.unlink', 'uses' => 'InstructorSwitchController@unlink']);
        Route::post('/instructor/switch-auth-and-link', ['as' => 'web.instructor.switch.authAndLink', 'uses' => 'InstructorSwitchController@authAndLink']);
        Route::post('/instructor/switch-create-and-link', ['as' => 'web.instructor.switch.createAndLink', 'uses' => 'InstructorSwitchController@createAndLink']);
    });

    Route::get('/coming-soon', ['as' => 'web.holding', 'uses' => 'HoldingController@index']);
    Route::post('/coming-soon', ['as' => 'web.holding.send', 'uses' => 'HoldingController@send']);
    Route::post('/contact-us', ['as' => 'web.contact.send', 'uses' => 'ContactController@send']);

    Route::post('/login',  ['as' => 'web.login.action', 'uses' => 'LoginController@login']);
    Route::get('/logout',  ['as' => 'web.logout', 'uses' => 'LoginController@logout']);

    Route::post('/forgot-password', ['as' => 'web.forgot.remind', 'uses' => 'ForgotPasswordController@remind']);
    Route::get('/forgot-password/reset/{token}', ['as' => 'web.forgot', 'uses' => 'ForgotPasswordController@index']);
    Route::post('/forgot-password/reset', ['as' => 'web.forgot.reset', 'uses' => 'ForgotPasswordController@reset']);

    Route::get('/join', ['as' => 'web.join', 'uses' => 'JoinController@index']);
    Route::post('/join', ['as' => 'web.join.action', 'uses' => 'JoinController@join', 'middleware' => 'bancheck']);
    Route::get('/activate/{token}', ['as' => 'web.join.activate', 'uses' => 'JoinController@activate']);
    Route::get('/join/thanks', ['as' => 'web.join.thanks', 'uses' => 'JoinController@thanks']);
    Route::get('/resend-activation', ['as' => 'web.join.activate.resend', 'uses' => 'JoinController@resendActivation']);

    Route::get('/how-it-works', ['as' => 'web.hiw', 'uses' => 'HIWController@index']);
    Route::get('/discover', ['as' => 'web.discover', 'uses' => 'DiscoverController@index']);

    Route::get('/instructor-details/{id}/{name}', ['as' => 'web.instructor.profile-details', 'uses' => 'ProfileController@index']);

    Route::get('/class-details/{id}/{title}', ['as' => 'web.class.details', 'uses' => 'ClassController@index']);

    Route::get('/invite/{guid}', ['as' => 'web.invite.fromInstructor', 'uses' => 'InviteController@inviteFromInstructor']);

    Route::post('/ajax/class-types-listbox', ['as' => 'web.ajax.class-types-listbox', 'uses' => 'ClassTypeController@typesListbox']);
    Route::post('/ajax/follow-unfollow', ['as' => 'web.ajax.follow-unfollow', 'uses' => 'ProfileController@followUnfollow']);
    Route::post('/ajax/invite-friends', ['as' => 'web.ajax.invite-friends', 'uses' => 'InviteController@inviteFriends']);
    Route::post('/ajax/send-mobile-verification', ['as' => 'web.ajax.send-mobile-verification', 'uses' => 'CommonUserController@sendMobileVerification']);
    Route::post('/ajax/report-instructor', ['as' => 'web.ajax.report-instructor', 'uses' => 'ProfileController@reportInstructor']);
    Route::post('/ajax/report-member', ['as' => 'web.ajax.report-member', 'uses' => 'ProfileController@reportMember']);
    Route::post('/ajax/newsfeed/delete', ['as' => 'web.ajax.newsfeed.delete', 'uses' => 'CommonUserController@deleteNewsfeed']);
    Route::post('/ajax/notifications/delete', ['as' => 'web.ajax.notifications.delete', 'uses' => 'CommonUserController@deleteNotification']);
    Route::post('/ajax/private-comment', ['as' => 'web.ajax.private-comment', 'uses' => 'ProfileController@privateComment']);
    Route::post('/ajax/rate-class', ['as' => 'web.ajax.rate-class', 'uses' => 'LiveController@rateClass']);
    Route::post('/ajax/live/check-member', ['as' => 'web.ajax.live.check-member', 'uses' => 'LiveController@checkMember']);
    Route::post('/ajax/live/check-instructor', ['as' => 'web.ajax.live.check-instructor', 'uses' => 'LiveController@checkInstructor']);
    Route::post('/ajax/live/instructor-ready', ['as' => 'web.ajax.live.instructor-ready', 'uses' => 'LiveController@instructorReady']);
    Route::post('/ajax/live/member-check-for-end', ['as' => 'web.ajax.member-check-for-end', 'uses' => 'LiveController@memberCheckForEnd']);
    Route::post('/ajax/live/instructor-ends-class', ['as' => 'web.ajax.live.instructor-ends-class', 'uses' => 'LiveController@instructorEndsClass']);
    Route::post('/ajax/live/instructor-force-end', ['as' => 'web.ajax.live.instructor-force-end', 'uses' => 'LiveController@instructorForceEnd']);
    Route::post('/ajax/live/instructor-start-broadcast', ['as' => 'web.ajax.live.instructor-start-broadcast', 'uses' => 'LiveController@instructorStartBroadcast']);
    Route::post('/ajax/live/instructor-countries', ['as' => 'web.ajax.live.instructor-countries', 'uses' => 'LiveController@instructorCountries']);
    Route::get('/ajax/live/member-check-broadcast-url', ['as' => 'web.ajax.live.member-check-broadcast-url', 'uses' => 'LiveController@getBroadcastUrl']);
    Route::post('/ajax/system-checks/init', ['as' => 'web.ajax.system-checks.init', 'uses' => 'HIWController@getInit']);
    Route::post('/ajax/system-checks/success', ['as' => 'web.ajax.system-checks.success', 'uses' => 'HIWController@testsPassed']);

    Route::get('/live/{id}', ['as' => 'web.live', 'uses' => 'LiveController@index']);
    Route::get('/live/{id}/rate', ['as' => 'web.live.rate', 'uses' => 'LiveController@rate']);
    Route::post('/live/start', ['as' => 'web.live.start', 'uses' => 'LiveController@start']);
    Route::post('/live/camselect', ['as' => 'web.live.camselect', 'uses' => 'LiveController@camSelect']);

    // Temp
    //Route::get('/live-dev/{id}', ['as' => 'web.livedev', 'uses' => 'LiveController@Xindex']);
    //Route::post('/live-dev/start', ['as' => 'web.livedev.start', 'uses' => 'LiveController@Xstart']);
    // Remove after!
    Route::post('/instructor/rest/create-class', ['as' => 'web.instructor.rest.createClass', 'uses' => 'InstructorClassController@restCreateClass']);
    // Temp

    Route::get('/calendar/{token}.ics', ['as' => 'web.ics', 'uses' => 'ICSController@index']);

    Route::get('/', ['as' => 'web.home', 'uses' => 'HomeController@index']);
    Route::get('/{url}', ['as' => 'web.page', 'uses' => 'PageController@index'])->where('url', '(.*)');
});

Route::group(['prefix' => 'api/v1', 'namespace' => 'API', 'middleware' => ['api', 'api.allowed']], function() {
    Route::post('/landing/instructor-join', 'InstructorController@join');
    Route::post('/landing/member-join-book', 'MemberController@joinBook');
    Route::post('/ls/token/register', 'LiveswitchController@generateClientRequestToken');
    Route::post('/ls/token/join', 'LiveswitchController@generateClientJoinToken');
});

Route::group(['prefix' => 'api/v1/ls', 'namespace' => 'API'], function() {
    Route::group(['middleware' => ['api', 'api.ls', 'api.ls.allowed']], function() {
        Route::post('/tokens/register', 'LiveswitchController@generateClientRegisterToken');
        Route::post('/tokens/join', 'LiveswitchController@generateClientJoinToken');
    });
    Route::group(['middleware' => ['api', 'api.ls']], function() {
        Route::post('/test/tokens/register', 'TestLiveswitchController@generateClientRegisterToken');
        Route::post('/test/tokens/join', 'TestLiveswitchController@generateClientJoinToken');
    });
});
