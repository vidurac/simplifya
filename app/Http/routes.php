<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//*******************************Api Routes*************************************//

    // API route prefix
    define('API_PREFIX', 'api/');

    Route::resource(API_PREFIX.'auth', 'Api\Auth\OauthController', array('only' => array('store','destroy')));

    Route::post(API_PREFIX.'forgotPassword','Web\FogotPasswordController@store');

    Route::group(['middleware' => ['oauth']], function(){ // Only authenticated users may enter...
        // Get appointment routes
        Route::resource(API_PREFIX.'appointment', 'Api\AppointmentController', array('only' => array('index')));
        Route::get(API_PREFIX.'appointment/list/{type}', 'Api\AppointmentController@appointmentList');
        Route::get(API_PREFIX.'comment/list/{aid}/{cid}', 'Api\AppointmentController@getActionComments');
        //store answer routes
        Route::resource(API_PREFIX.'answer', 'Api\AnswerController', array('only' => array('store')));
        // Appointment questions list API
        Route::get(API_PREFIX.'appointmentQuestionList/{id}', 'Api\QuestionController@show');
        //Appointment report list API
        Route::get(API_PREFIX.'appointmentReportList/{id}','Api\QuestionController@AppointmentQuestions');
        // Questions save api route
        Route::post(API_PREFIX.'question/save','Api\AnswerController@storeQuestions');
        // Save images api
        Route::post(API_PREFIX.'images/save','Api\AnswerController@storeImages');
        // Logout route
        Route::delete(API_PREFIX.'logout', 'Api\Auth\OauthController@destroy');
        // Get Users By Location
        Route::get(API_PREFIX.'getUsersByLocation/{appointment_id}/{action_item_id}', 'Api\ReportController@getUsersByLocation');
        // Save inspection details
        Route::post(API_PREFIX.'inspection/save','Api\AppointmentController@saveInspectionData');
        // Assign users 
        Route::post(API_PREFIX.'reports/assignUsers','Api\ReportController@assignUsers');
        // Add comment api
        Route::post(API_PREFIX.'reports/addComment', 'Api\ReportController@insertComment');
        // Get action items list
        Route::get(API_PREFIX.'reports/getActionItems/{id}', 'Api\ReportController@getActionItems');
        // Get appointment reports list
        Route::get(API_PREFIX.'reports/getAppointmentReportList/{appointment_id}', 'Api\ReportController@getAppointmentReportList');
        // Get appointment reports list with tree
        Route::get(API_PREFIX.'reports/getAppointmentReportList/{appointment_id}/{tree}', 'Api\ReportController@getAppointmentReportList');
    });


//*******************************Web Routes*************************************//

    Route::get('get/license', 'Web\LicenseController@getMjbLicenseByCompanyId');
    Route::post('search/license', 'Web\LicenseController@searchLicense');
    //................ Company Registration .......................
    Route::get('company/companyType/', 'Web\CompanyController@companyType');
    Route::get('company/mjb-register/{token?}', 'Web\CompanyController@mjbRegister');
    Route::get('company/registration', 'Web\CompanyController@index');
    Route::post('company/registration', 'Web\CompanyController@store');

    Route::get('get/subscription/fee', 'Web\CompanyController@getSubscriptionFee');

    //............... Authentication routes .......................
    Route::get('/', 'Web\Auth\AuthController@getLogin');
    Route::post('auth/login', 'Web\Auth\AuthController@postLogin');
    Route::get('auth/logout', 'Web\Auth\AuthController@getLogout');
    Route::get('register/{code}', 'Web\UserController@userRegister');
    Route::post('user/register', 'Web\UserController@setPassword');
    Route::get('not/allowed', 'Web\CommonController@userRestricted');
    Route::get('pending', 'Web\CommonController@cgePending');
    Route::get('error', 'Web\CompanyController@errorPageLoader');
    Route::get('suspend', 'Web\CommonController@suspendMsg');
    Route::get('thanks', 'Web\CompanyController@thankPageLoader');
    Route::get('resetPassword', 'Web\FogotPasswordController@index');
    Route::post('resetPassword/reset', 'Web\FogotPasswordController@store');
    Route::get('verify/password/{code}', 'Web\FogotPasswordController@confirm');
    Route::post('resetPassword/newpassword', 'Web\FogotPasswordController@createpassword');

    // for testing
    Route::get('mail/test', 'Web\DashboardController@sendMail');
    Route::get('android/push', 'Web\DashboardController@pushNotificationToAndroid');



/************************* Reports Manager **********************************/

Route::group(['middleware' => array('auth', 'roles','reports')], function()
{
    Route::get('/reports', 'Web\ReportController@index');
    Route::get('/report/export/{id}/{pw}', 'Web\ReportController@export');
    Route::get('/report/all', 'Web\ReportController@searchReports');
    Route::post('/report/edit/questions', 'Web\ReportController@reportQuestions');
    Route::post('/report/edit/finalizeReport', 'Web\ReportController@finalizeReport');
    Route::get('/report/edit/questions/comment', 'Web\ReportController@viewReportQuestionComment');
    Route::get('/report/edit/questions/comment/store', 'Web\ReportController@editReportQuestionComment');
    Route::post('/report/edit/actionItems', 'Web\ReportController@reportActionItems');
    Route::post('/report/edit/actionItemsAssignee', 'Web\ReportController@reportActionItemsAssignee');
    Route::post('/report/edit/actionItems/update', 'Web\ReportController@updateReportActionItems');
    Route::get('/report/edit/actionItems/comment', 'Web\ReportController@getReportActionItemsComment');
    Route::get('/report/getUsersByLocation', 'Web\ReportController@getUsersByLocation');
    Route::post('/reports/assignUsers', 'Web\ReportController@assignUsers');
    Route::post('/reports/comment/insert', 'Web\ReportController@insertComment');
    Route::post('/report/unknownCompliance', 'Web\ReportController@getUnknownComplianceAnswers');
    Route::get('/report/category/questions', 'Web\ReportController@getCategoryBasedQuestions');
    Route::get('/report/edit/{code}', 'Web\ReportController@reportsEdit');
    Route::get('/report/getAppointmentReportData/{appointment_id}', 'Web\ReportController@loadReportListDataAjax');
    Route::get('/report/getAppointmentReportData/{appointment_id}/{tree}', 'Web\ReportController@loadReportListDataAjax');
    Route::post('/report/deactivate_action_item', 'Web\ReportController@deactivateActionItem');
    Route::post('/report/reopen_action_item', 'Web\ReportController@reopen_action_item');
    Route::get('/report/getAppointmentReportData/{appointment_id}', 'Web\ReportController@loadReportListData');
    Route::get('/report/getNavigationCategories', 'Web\ReportController@loadNavigationCategories');
});

Route::group(['middleware' => array('auth', 'roles')], function()
{
    Route::get('company/info', 'Web\CompanyController@companyInfo');
    Route::post('add/company/location', 'Web\CompanyController@addCompanyLocation');
    Route::get('company/locations/{code}', 'Web\CompanyController@getCompanyLocation');
    Route::get('get/company/locations/{code}', 'Web\CompanyController@companyLocationById');
    Route::get('get/company/permission/{code}', 'Web\CompanyController@companyPermissionLevelById');
    Route::get('get/company/details/{code}', 'Web\CompanyController@getCompanyDetails');
    Route::post('change/company/status', 'Web\CompanyController@changeCompanyStatus');
    Route::post('change/company/foc', 'Web\CompanyController@changeCompanyFoc');

    Route::get('get/business/location/{code}', 'Web\CompanyController@getBusinessLocation');
    Route::post('edit/company/location', 'Web\CompanyController@updateBusinessLocation');
    Route::get('change/business/location/status', 'Web\CompanyController@changeBusinessLocationStatus');
    Route::get('checkUsersInLocations/{code}', 'Web\CompanyController@checkUsersInLocation');
    Route::post('get/company/subscription', 'Web\CompanyController@calculateSubscriptionFee');
    Route::get('get/card/detail/{code}', 'Web\CompanyController@getCardDetails');
    Route::post('update/card/detail', 'Web\CompanyController@updateCardDetails');
    Route::get('active/company', 'Web\CompanyController@activeCcGeCompany');

    Route::post('company/commonCompanyPayment', 'Web\CompanyController@commonCompanyPayment');
    Route::post('company/addCompanyPaymentCard', 'Web\CompanyController@addCompanyPaymentCard');
    Route::post('add/default/card/details', 'Web\CompanyController@addDefaultCard');

    Route::get('get/card/all/{code}', 'Web\CompanyController@getAddedCardDetails');
    Route::post('active/company/card', 'Web\CompanyController@selectDefaultCard');
    Route::post('/company/subscription/plan', 'Web\CompanyController@companySubscriptionPlan');

    //get subscription packages
    Route::get('company/subscriptionPlans', 'Web\CompanyController@subscriptionPlans');
    Route::post('get/subscription/package/', 'Web\CompanyController@getSubscriptionFeeForPlan');

    //................ Edit Company Details ....................................
    Route::get('change/company/info', 'Web\CompanyController@getCompanyInfo');
    Route::post('change/company/info', 'Web\CompanyController@changeBusinessInfo');
    Route::get('company/manager', 'Web\CompanyController@companyMangeViewer');
    Route::get('get/all/company', 'Web\CompanyController@getAllCompany');
    Route::post('company/filtering', 'Web\CompanyController@companySearchByTypeAndName');
    Route::get('company/export', 'Web\CompanyController@exportCompanySearchResults');

    //.................locations.........................................
    Route::get('location/info', 'Web\CompanyController@getLocationInfo');

    //.................coupons...........................................
    Route::post('company/getDiscount', 'Web\CompanyController@getDiscount');
    Route::post('company/validateCoupon', 'Web\CompanyController@validateCoupon');

    //................ Licenses  ....................................
    Route::post('add/license/type', 'Web\LicenseController@store');
    Route::post('change/license/type', 'Web\LicenseController@update');
    Route::get('get/all/license', 'Web\LicenseController@getLicenseTypes');
    Route::get('get/license/{code}', 'Web\LicenseController@getLicenseById');
    Route::post('add/licenses','Web\CompanyController@companyLicenseLocation');
    Route::get('company/license/{code}', 'Web\LicenseController@getCompanyLicense');
    Route::get('get/licensetypes', 'Web\LicenseController@getLicenseTypeByStateId');
    Route::get('get/license/details/{code}','Web\LicenseController@getLicenseDetailsById');
    Route::post('change/licenses', 'Web\LicenseController@changeLocationLicense');
    Route::get('change/licenses/location/status', 'Web\LicenseController@changeLocationLicenseStatus');
    Route::post('get/licenses/amount', 'Web\LicenseController@getLicenseAmount');
    Route::post('get/company/licenses', 'Web\LicenseController@getAllLicenses');
    Route::get('change/license/status', 'Web\LicenseController@changeLicenseStatus');
    Route::get('license/locations', 'Web\LicenseController@getLicenseTypeLicenseLocation');
    Route::get('license/locations/{code}', 'Web\LicenseController@getLocationsByLicenseId');
    Route::get('license/getApplicabilityByStateAndCountries/{type_id}/{country_id}', 'Web\LicenseController@getApplicabilityByStateAndCountries');

    Route::get('licenses', 'Web\LicenseController@mjbLicense');
    Route::get('get/mjb/licenses/{code}', 'Web\LicenseController@getMjbLicenseByCompanyId');
    Route::post('license/perches', 'Web\LicenseController@perchesLicense');
    Route::get('license/fee/{code}', 'Web\LicenseController@calculateLicenseFee');
    Route::post('update/license', 'Web\LicenseController@updateLicense');
    Route::post('activate/license', 'Web\LicenseController@activateLicense');
    Route::get('activate/license/count', 'Web\LicenseController@activateLicenseCount');
    Route::get('/remove/license/check', 'Web\LicenseController@checkLicenseExists');

    //................Users ............................................................
    Route::post('invite/to/employees', 'Web\UserController@inviteToEmployees');
    Route::post('edit/invite/employees', 'Web\UserController@changeInviteEmployees');
    Route::get('get/invite/user/details/{code}', 'Web\UserController@getInviteEmployees');
    Route::get('get/all/user/details/{code}', 'Web\UserController@getAllEmployees');
    Route::get('get/employees/{code}', 'Web\UserController@getEmployeesByCompanyId');
    Route::get('users','Web\UserController@getUsersByCompanyId');
    Route::get('get/all/users', 'Web\UserController@allUsersByCompanyId');
    Route::get('change/users/status', 'Web\UserController@changeUserStatus');
    Route::get('user/permission/levels', 'Web\UserController@getUserPermissionsByCompany');
    Route::post('users/filtering','Web\UserController@userSearchByLevelsAndStatus');
    Route::get('users/profile','Web\UserController@userProfile');
    Route::post('users/updateProfile','Web\UserController@updateProfile');
    Route::get('restore/users','Web\UserController@reStoreUser');

    //..................Payment Routes ...........................................
    Route::get('company/subscription', 'Web\PaymentController@paymentHandler');
    Route::get('/payment', 'Web\PaymentController@index');
    Route::get('/payment/all', 'Web\PaymentController@getAllPayments');
    Route::get('/request/index', 'Web\RequestController@index');

    //..................dashboard routes ...........................................
    Route::get('/dashboard', 'Web\DashboardController@index');
    Route::get('get/all/pending', 'Web\DashboardController@getAllPendingCompany');
    Route::get('get/company/summary', 'Web\DashboardController@getRegisterCompnaySummary');
    Route::get('get/notifications', 'Web\DashboardController@getUserNotifications');
    Route::get('get/rosters', 'Web\DashboardController@getUserRosters');
    Route::get('get/rosters/job/task', 'Web\DashboardController@getAllJobTasks');
    Route::post('rosters/job/task/save', 'Web\DashboardController@saveAllJobTasks');
    Route::get('get/report/notification', 'Web\DashboardController@getReportNotifications');
    Route::get('get/notifications/count', 'Web\DashboardController@getNewNotification');
    Route::get('notifications/{id}/update', 'Web\DashboardController@updateUserNotifications');
    Route::get('appointment/list', 'Web\DashboardController@showUpcomingAppointments');
    Route::get('requests/list', 'Web\DashboardController@showRequestToCCAdmin');
    Route::get('licenses/list', 'Web\DashboardController@showLicensesList');
    Route::get('report/{id}/success', 'Web\DashboardController@updateWhenReportStatusChanged');
    Route::post('dashboard/moduleSetup', 'Web\DashboardController@moduleSetup');
    Route::get('get/commissions', 'Web\DashboardController@getCommissions');


    //..................Question routes ...........................................
    Route::get('/question', 'Web\QuestionController@index');
    Route::get('/question/create', 'Web\QuestionController@create');
    Route::get('/question/export', 'Web\QuestionController@export');
    Route::get('/question/export_csv', 'Web\QuestionController@export_csv');
    Route::get('/question/create/child/{parent_id}/{answer_id}', 'Web\QuestionController@createChild');
    Route::get('/question/edit/child/{question_id}/{visibility}', 'Web\QuestionController@editAndViewChild');

    Route::get('/question/getStates', 'Web\QuestionController@getStatus');
    Route::get('/question/getCities', 'Web\QuestionController@getCities');
    Route::get('/question/getLicences', 'Web\QuestionController@getLicences');
    Route::get('/question/getLicenseFromCountry', 'Web\QuestionController@getLicenseFromCountry');
    //Route::get('/question/getChildQuestions', 'Web\QuestionController@getChildQuestions');
    Route::get('/question/getAnswerQuestion', 'Web\QuestionController@getAnswerQuestion');
    Route::post('/question/createQuestion', 'Web\QuestionController@store');
    Route::post('/question/createChildQuestion', 'Web\QuestionController@saveChildQuestion');
    Route::post('/question/updateChildQuestion', 'Web\QuestionController@updateChildQuestion');
    Route::post('/question/updateParentQuestion', 'Web\QuestionController@updateParentQuestion');
    Route::post('/question/checkParentQuestion', 'Web\QuestionController@checkParentQuestion');
    Route::get('/question/deleteQuestionAnswer', 'Web\QuestionController@deleteQuestionAnswer');
    Route::get('/question/editQuestion/{id}', 'Web\QuestionController@edit');
    Route::get('/question/viewQuestion/{id}', 'Web\QuestionController@view');
    Route::get('/question/getUserHistory', 'Web\QuestionController@getUserHistory');
    Route::get('/question/getChildQuestion', 'Web\QuestionController@getChildQuestion');
    Route::get('/question/getAllChildQuestions', 'Web\QuestionController@getAllChildQuestionAjax');
    Route::get('/question/getChildQuestionForView', 'Web\QuestionController@getChildQuestionForView');
    Route::get('/questions/all', 'Web\QuestionController@allParentQuestions');
    Route::get('/questions/findQuestionVersions', 'Web\QuestionController@findQuestionVersions');
    Route::post('/questions/updateQuestionStatus', 'Web\QuestionController@updateQuestionStatus');
    Route::post('/questions/updateSubQuestionStatus', 'Web\QuestionController@updateSubQuestionStatus');
    Route::post('/questions/saveUserQuestionSession', 'Web\QuestionController@saveUserQuestionSession');
    Route::post('/questions/deleteQuestion', 'Web\QuestionController@deleteQuestion');
    Route::post('/questions/deleteSubQuestion', 'Web\QuestionController@deleteSubQuestion');
    Route::post('/questions/createNewVersion', 'Web\QuestionController@createNewVersion');
    Route::get('/questions/getAnswersCount/{appointment_id}', 'Web\QuestionController@getAnswersCount');
    Route::get('/questions/getAnswersCount/{appointment_id}/{category_id}', 'Web\QuestionController@getAnswersCount');
    Route::get('/questions/getQuestionCountToShow', 'Web\QuestionController@getQuestionCountToShow');
    Route::post('/questions/updateUserPagination', 'Web\QuestionController@updateUserPagination');

    //..................Angular routes ...........................................
    Route::get('/question/parent', 'Web\QuestionController@getParentDetails');
    Route::get('/question/child', 'Web\QuestionController@getchildDetailsForEditView');


    //..................Request routes ...........................................
    Route::get('/request/create', 'Web\RequestController@create');
    Route::post('/request/store', 'Web\RequestController@store');
    Route::get('/request/manage', 'Web\RequestController@manage');
    Route::get('/request/edit/{code}', 'Web\RequestController@edit');
    Route::get('/request/process/', 'Web\RequestController@process');
    Route::post('/company/location/{code}', 'Web\CompanyController@getCompanyLocationById');

    //..................appointment routes ...........................................
    Route::get('/appointment', 'Web\AppointmentController@index');
    Route::get('/appointment/create/', 'Web\AppointmentController@createAppointment');
    Route::get('/appointment/all/', 'Web\AppointmentController@getAllAppointments');
    Route::post('/appointment/store', 'Web\AppointmentController@store');
    Route::post('/appointment/update', 'Web\AppointmentController@update');
    Route::post('/appointment/cancel', 'Web\AppointmentController@cancelAppointment');
    Route::get('get/company/assignTo', 'Web\AppointmentController@getAssignTo');
    Route::get('/appointment/create/nonmjb', 'Web\AppointmentController@createAppointmentForNonMjb');
    Route::get('/appointment/edit/nonmjb/{company_id}', 'Web\AppointmentController@createAppointmentForNonMjb');
    Route::get('/appointment/nonmjb/get/{company_id}', 'Web\AppointmentController@getNonMjbDetails');


    /*----------------------- Configuration ---------------------------*/
    Route::get('/configuration', 'Web\ConfigurationController@index');
    Route::get('/configuration/masterdata', 'Web\ConfigurationController@masterData');
    Route::post('/configuration/masterdata/store', 'Web\ConfigurationController@storeMasterData');
    Route::post('/configuration/masterdata/update', 'Web\ConfigurationController@updateMasterData');
    Route::post('/configuration/getCities', 'Web\ConfigurationController@getCities');
    Route::get('/configuration/subscription/{code}', 'Web\ConfigurationController@subscription');
    Route::get('/configuration/subscription/{code}/new', 'Web\ConfigurationController@addNewSubscription');
    Route::post('/configuration/subscription/{code}/store', 'Web\ConfigurationController@newSubscriptionStore');
    Route::post('/configuration/subscription/edit/item', 'Web\ConfigurationController@subscriptionEdit');
    Route::post('/configuration/subscription/edit/store/item', 'Web\ConfigurationController@subscriptionEditStore');
    Route::get('/configuration/subscription/remove/{code}', 'Web\ConfigurationController@remove');
    Route::get('/subscription/filter/{code}', 'Web\ConfigurationController@filter');

    Route::get('/qcategories/filter/{is_only_main}', 'Web\ConfigurationController@filterQuestionCategories');
   // Route::get('/mqcategories/filter', 'Web\ConfigurationController@filterMainQuestionCategories');


    Route::get('/configuration/qcategories/', 'Web\ConfigurationController@QuestionCategories');
    Route::get('/configuration/mqcategories/', 'Web\ConfigurationController@MainQuestionCategories');
    Route::get('/configuration/qcategories/new', 'Web\ConfigurationController@NewQuestionCategories');
    Route::post('/configuration/qcategories/store', 'Web\ConfigurationController@StoreQuestionCategories');
    Route::get('/configuration/qcategory/edit', 'Web\ConfigurationController@editQuestionCategory');
    Route::post('/configuration/qcategory/insert', 'Web\ConfigurationController@insertQuestionCategory');
    Route::get('/configuration/qcategory/remove', 'Web\ConfigurationController@removeQuestionCategory');
    Route::get('/configuration/getSubQuestionLevel', 'Web\ConfigurationController@getSubQuestionLevel');
    Route::get('/configuration/qcategory/main/check', 'Web\ConfigurationController@checkMainQuestionCategory');
    Route::get('/configuration/qcategory/main/delete', 'Web\ConfigurationController@removeMainQuestionCategory');
    Route::get('/configuration/qcategory/options/{code}', 'Web\ConfigurationController@QuestionCategoriesOptions');

    /************************* Country Manager **********************************/
    Route::get('/configuration/country', 'Web\ConfigurationController@countryManager');
    Route::get('/country/filter/', 'Web\ConfigurationController@countryFilter');
    Route::post('/country/new/store', 'Web\ConfigurationController@insertCountry');
    Route::get('/configuration/country/new', 'Web\ConfigurationController@addNewCountry');
    Route::get('/configuration/country/manage', 'Web\ConfigurationController@ManageAddedCountry');
    Route::get('/get/countryList', 'Web\ConfigurationController@getAllCountryList');

    /************************* State Manager **********************************/
    Route::get('/configuration/state', 'Web\ConfigurationController@stateManager');
    Route::get('/state/filter/', 'Web\ConfigurationController@stateFilter');
    Route::post('/state/new/store', 'Web\ConfigurationController@insertState');
    Route::post('/state/edit/store', 'Web\ConfigurationController@storeEditState');
    Route::get('/configuration/state/new', 'Web\ConfigurationController@addNewState');
    Route::post('/configuration/state/edit', 'Web\ConfigurationController@editState');

    /************************* City Manager **********************************/
    Route::get('/configuration/city', 'Web\ConfigurationController@cityManager');
    Route::get('/city/filter/', 'Web\ConfigurationController@cityFilter');
    Route::post('/city/new/store', 'Web\ConfigurationController@insertCity');
    Route::post('/city/edit/store', 'Web\ConfigurationController@storeEditCity');
    Route::get('/configuration/city/new', 'Web\ConfigurationController@addNewCity');
    Route::post('/configuration/city/edit', 'Web\ConfigurationController@editCity');
    Route::post('/configuration/city/update', 'Web\ConfigurationController@updateCity');
    Route::get('/configuration/checkCitiesOccupied', 'Web\ConfigurationController@checkCitiesOccupied');

    /************************* User Group Manager **********************************/
    Route::get('/configuration/userGroup', 'Web\ConfigurationController@userGroupManager');
    Route::get('/userGroup/filter/', 'Web\ConfigurationController@userGroupFilter');
    Route::post('/userGroup/new/store', 'Web\ConfigurationController@insertUserGroup');
    Route::post('/UserGroup/edit/store', 'Web\ConfigurationController@storeEditUserGroup');
    Route::get('/configuration/UserGroup/new', 'Web\ConfigurationController@addNewUserGroup');
    Route::post('/configuration/UserGroup/edit', 'Web\ConfigurationController@editUserGroup');
    Route::get('/configuration/UserGroup/remove', 'Web\ConfigurationController@changeStatusUserGroup');

    Route::get('/configuration/licenses', 'Web\LicenseController@index');
    Route::get('/configuration/licenses/new', 'Web\LicenseController@createLicenseView');
    Route::get('/configuration/licenses/edit/{license_id}', 'Web\LicenseController@createLicenseView');
    Route::get('/get/ccge/subscribtionfee', 'Web\SubscribtionController@getGeAndCcSubs');
    Route::get('/request/filter', 'Web\RequestController@searchRequests');

    Route::get('/reports/list', 'Web\ReportController@getAllReport');
    Route::get('/reports/type/list', 'Web\ReportController@getReportTypes');
    Route::get('/company/list', 'Web\ReportController@getCompanyList');
    Route::get('/inspection/list', 'Web\ReportController@getInspectionReport');
    Route::get('/company/users/list', 'Web\ReportController@getUserList');
    Route::get('/company/location/list', 'Web\ReportController@getCompanyLocations');
    Route::get('/company/locations', 'Web\ReportController@CompanyLocations');
    Route::get('/company/location/export', 'Web\ReportController@exportCompanyLocations');
    Route::get('/inspection/report/export', 'Web\ReportController@exportInspectionReport');
    Route::get('/inspection/report', 'Web\ReportController@inspectionReport');

    /************************* Coupons Manager **********************************/
    Route::get('/configuration/coupons', 'Web\ConfigurationController@couponManager');
    Route::get('/configuration/coupons/new', 'Web\ConfigurationController@addNewCoupon');
    Route::get('/configuration/coupons/edit/{coupon_id}', 'Web\ConfigurationController@addNewCoupon');


    Route::get('/configuration/referrals/codes', 'Web\ConfigurationController@referralCodeManager');
    Route::get('/configuration/referrals/code/new', 'Web\ConfigurationController@addNewReferralCode');
    Route::get('/configuration/referrals/code/edit/{referral_id}', 'Web\ConfigurationController@addNewReferralCode');
    Route::get('/configuration/referrals/code/view/{referral_id}', 'Web\ConfigurationController@viewNewReferralCode');

    /************************* Applicability Manager **********************************/
    Route::get('/configuration/applicability', 'Web\ConfigurationController@applicabilityManager');
    Route::get('/configuration/applicability/new', 'Web\ConfigurationController@addNewApplicability');
    Route::get('/configuration/applicability/edit/{applicability_id}', 'Web\ConfigurationController@addNewApplicability');
    Route::post('/configuration/applicability/delete/{applicability_id}', 'Web\ConfigurationController@deleteApplicability');
    Route::post('/configuration/applicability/status/change', 'Web\ConfigurationController@changeApplicabilityStatus');
    Route::get('/configuration/applicabilities', 'Web\ConfigurationController@applicabilityList');
    Route::post('/configuration/applicability/create', 'Web\ConfigurationController@createApplicability');
    Route::get('/get/applicability/types', 'Web\ConfigurationController@getApplicabilityTypesAndGroups');
    Route::get('/configuration/applicabilities/applicability-id/{id}', 'Web\ConfigurationController@getApplicabilityById');

    /********************* Keyword Manager *****************************************/
    Route::get('/configuration/keywords', 'Web\ConfigurationController@keywordManager');
    Route::get('/configuration/get/keywords', 'Web\ConfigurationController@getKeywords');
    Route::get('/configuration/delete/keywords/{keyword_id}', 'Web\ConfigurationController@deleteKeywordsById');
    Route::post('/configuration/edit/keywords', 'Web\ConfigurationController@editKeywordName');
    Route::get('/configuration/get/question/keyword/{keyword_id}', 'Web\ConfigurationController@getQuestionKeywordById');
    Route::get('/get/applicability', 'Web\ConfigurationController@getApplicabilityTypes');

    // angular service
    Route::post('/configuration/coupons/create', 'Web\ConfigurationController@createCoupon');
    Route::get('/configuration/coupons/all', 'Web\ConfigurationController@allCoupons');
    Route::get('/configuration/referralCodes', 'Web\ConfigurationController@referralCodes');
    Route::get('/configuration/coupons/coupon/{id}', 'Web\ConfigurationController@getCouponDetails');
    Route::get('/configuration/referrals/all', 'Web\ConfigurationController@getAllReferrals');

    /************************* Referral Manager **********************************/
    Route::get('/configuration/referrals', 'Web\ConfigurationController@referralManager');
    Route::get('/configuration/referrals/new', 'Web\ConfigurationController@addNewReferrer');
    Route::get('/configuration/referrals/code', 'Web\ConfigurationController@addReferralCode');
    Route::get('/configuration/referrals/edit/{referral_id}', 'Web\ConfigurationController@addNewReferrer');
    Route::get('/configuration/referrals/code/{id}', 'Web\ConfigurationController@getCouponReferralDetails');
    Route::get('/configuration/referrals/view/{referral_id}', 'Web\ConfigurationController@viewNewReferrer');
    // angular service
    Route::post('/configuration/referrals/create', 'Web\ConfigurationController@createReferrer');
    Route::post('/configuration/referrals/delete', 'Web\ConfigurationController@deleteReferrer');
    Route::get('/configuration/referrals/referrer/{id}', 'Web\ConfigurationController@getReferrerDetails');
    Route::get('/configuration/referrals/referrer-code/{id}', 'Web\ConfigurationController@getCouponReferralDetails');
    Route::get('/configuration/referrals/commissions/{id}', 'Web\ConfigurationController@getReferrerCommissionDetails');
    Route::get('/configuration/referrals/commission/payments/{id}', 'Web\ConfigurationController@getReferrerCommissionPayments');
    Route::post('/configuration/referrals/commissions/save', 'Web\ConfigurationController@saveReferrerCommissions');
//    Route::get('/configuration/referrals/referrer/{id}', 'Web\ConfigurationController@getCouponReferralDetails');
//    Route::get('/configuration/coupons/all', 'Web\ConfigurationController@allCoupons');
//    Route::get('/configuration/coupons/coupon/{id}', 'Web\ConfigurationController@getCouponDetails');


    /************************* Non MJB  Registrations **********************************/
    Route::get('/appointment/nonmjb/countries', 'Web\AppointmentController@getCountriesForNonMjb');
    Route::get('/appointment/nonmjb/states', 'Web\AppointmentController@getStatesForNonMjb');
    Route::get('/appointment/nonmjb/cities', 'Web\AppointmentController@getCitiesForNonMjb');
    Route::get('/appointment/nonmjb/licenses', 'Web\AppointmentController@getLicensesForNonMjb');
    Route::post('/appointment/nonmjb/create', 'Web\CompanyController@saveNonMjb');

    //.................Checklist routes ...........................................
    Route::get('/checklist', 'Web\ChecklistsController@index');
    Route::get('/checklist/getStates', 'Web\ChecklistsController@getStates');
    Route::get('/checklist/getCities', 'Web\ChecklistsController@getCities');
    Route::get('/checklist/getLicences', 'Web\ChecklistsController@getLicences');
    Route::get('/checklist/searchQuestions', 'Web\ChecklistsController@searchQuestions');

    //.............MailChimp routs .........................................
    Route::get('mailchimp/', 'Web\MailChimpController@index');
    Route::get('mailchimp/list', 'Web\MailChimpController@getMailList');
    Route::get('mailchimp/all', 'Web\MailChimpController@getAllUsers');
    Route::get('company/users', 'Web\MailChimpController@getAllCompanyUsers');
    Route::POST('mailchimp/sync', 'Web\MailChimpController@syncUsers');
    Route::get('mailchimp/companyList', 'Web\MailChimpController@companyList');
    Route::get('company/users/export', 'Web\MailChimpController@exportCompanyUserSearchResults');

    //Report Summary Routs
    Route::get('get/state/city/{country_id}', 'Web\ReportController@getStateCityByCountry');
    Route::get('get/country/city/{state_id}', 'Web\ReportController@getCountryCityByState');
    Route::get('get/country/state/{city_id}', 'Web\ReportController@getCountryStateByCity');

    Route::get('reports/readAllActionItemComments', 'Web\ReportController@readAllActionItemComments');

    //upload images
    Route::post('upload_file','Web\UploadController@store');

    //.............Tasks routs .........................................
    Route::get('/roster/list', 'Web\RosterController@index');
    Route::get('/roster/list/all', 'Web\RosterController@getAllRosters');
    Route::get('/roster/list/new', 'Web\RosterController@NewRoster');
    Route::get('/roster/users', 'Web\RosterController@getUsers');
    Route::get('/roster/jobs', 'Web\RosterController@getJobs');
    Route::get('/roster/job/all', 'Web\RosterController@getAllJobs');
    Route::get('/roster/job', 'Web\RosterController@getAllRosterJobs');
    Route::get('/roster/assignees', 'Web\RosterController@getAssignees');
    Route::get('/roster/assignee', 'Web\RosterController@getRosterAssignee');
    Route::get('/roster/assignees/all', 'Web\RosterController@getAllAssignees');
    Route::get('get/roster/assignee', 'Web\RosterController@getAssignee');
    Route::post('/roster/assignee/update', 'Web\RosterController@updateAssignee');
    Route::post('/roster/create', 'Web\RosterController@saveRoster');

    Route::get('/roster/list/task/', 'Web\RosterController@getAllTasks');
    Route::post('/roster/list/task/add', 'Web\RosterController@saveTasks');
    Route::post('/roster/list/task/delete', 'Web\RosterController@deleteTasks');
    Route::post('/roster/list/task/assign', 'Web\RosterController@assignTasks');
    Route::post('/roster/delete', 'Web\RosterController@deleteRoster');
    Route::get('get/roster/count', 'Web\RosterController@getRosterCount');


    //.............Subscription .........................................
    Route::get('/subscription/plan', 'Web\CompanyController@currentPlan');
    Route::get('/subscription/available-plans', 'Web\CompanyController@getSubscriptionPlansByCurrentEntity');
    Route::post('/subscription/update', 'Web\CompanyController@updateSubscriptionPlan');
    Route::post('/subscription/cancel', 'Web\CompanyController@cancelSubscriptionPlan');


});
Route::group(['middleware' => array('auth', 'roles','rosters')], function()
{
    Route::get('/roster/list/task/{roster_id}', 'Web\RosterController@getTasks');
});

Route::get('/test11/{type_id}/{country_id}', 'Web\LicenseController@getApplicabilityByStateAndCountries');
Route::get('7uLN426KP9-subscription-charge/{date?}', function ($date=null) {
    $currentDate = $date;

    if (!isset($date)) {
        $currentDate = date('Y-m-d');
    }

    try {
        echo '<br>init subscription cron for ' . $currentDate . '...';
        \Artisan::call('subscription:renew', ['customDate'=> $currentDate]);
        echo '<br>done...';
        return '';
    } catch (Exception $e) {
        return $e->getMessage();
    }

});

Route::get('7uLN426KP9-roster-check/{date?}', function ($date=null) {
    $currentDate = $date;

    if (!isset($date)) {
        $currentDate = date('Y-m-d');
    }

    try {
        echo '<br>init Roster cron for ' . $currentDate . '...';
        \Artisan::call('roster:job', ['customDate'=> $currentDate]);
        echo '<br>done...';
        return '';
    } catch (Exception $e) {
        return $e->getMessage();
    }

});

Route::get('8uLK76TL5-remove-tempmjb/', function () {

    try {
        echo '<br>init cron for remove temporary MJBs';
        \Artisan::call('tempMjb:remove');
        echo '<br>done...';
        return '';
    } catch (Exception $e) {
        return $e->getMessage();
    }

});


Route::get('8uLK76TL5-question_publisher/{date?}', function ($date=null) {
    $currentDate = $date;

    if (!isset($date)) {
        $currentDate = date('Y-m-d');
    }
    try {
        echo '<br>init cron for question publisher';
        \Artisan::call('question_publisher', ['customDate'=> $currentDate]);
        echo '<br>done...';
        return '';
    } catch (Exception $e) {
        return $e->getMessage();
    }

});

Route::get('8uLK76TL5-mjb-email/', function () {

    try {
        echo '<br>init cron for remove temporary MJBs';
        \Artisan::call('notify_no_audit_after_payment');
        echo '<br>done...';
        return '';
    } catch (Exception $e) {
        return $e->getMessage();
    }
});


Route::get('8uLK76TL5-audit-reminder/', function () {

    try {
        echo '<br>init cron for remove temporary MJBs';
        \Artisan::call('audit');
        echo '<br>done...';
        return '';
    } catch (Exception $e) {
        return $e->getMessage();
    }

});

if (App::environment('local')) {
    \DB::listen(function ($sql, $bindings, $time) {
        \Log::debug($sql);
        \Log::debug($bindings);
        \Log::debug($time);
    });
}
