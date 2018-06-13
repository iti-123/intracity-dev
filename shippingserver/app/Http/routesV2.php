<?php


// Version 1 of our API
$api->version('v1', function ($api) {
    $api->get("getuser",function() {
        dd(Tymon\JWTAuth\Facades\JWTAuth::parseToken()->getPayload());
    });
    $api->group(['prefix'=>'l'],function($api){        
        $api->group(['namespace' => 'ApiV2\Controllers', 'middleware' => '\Barryvdh\Cors\HandleCors::class'], function ($api) {
            $api->get('test','BuyerController@test');
        });
        //for paymengetway response
        $api->any('pgresponce',[
                    'uses'=>'ApiV2\Controllers\PaymentResponseController@GetwayResponse'
        ]);
        
       //bluecollar routes
        $api->group(['namespace' => 'ApiV2\Controllers\BlueCollar', 'middleware' => '\Barryvdh\Cors\HandleCors::class'], function ($api) {
          $api->group(['middleware' => 'jwt.auth'], function ($api) {
            $api->group(['prefix' => 'bluecollar'], function ($api) {
    
              $api->post('buyer-location-search', 'BuyerSearch@locationSearch');
              $api->post('buyer-search', 'BuyerSearch@search');
              $api->post('buyer-post/{status}', 'BuyerController@post');
              $api->post('buyer-post-list', 'BuyerController@postList');
              $api->get('buyer-bound-count', 'BuyerController@boundCount');
              $api->post('buyer-inbound', 'BuyerController@inboundList');
              $api->post('buyer-outbound', 'BuyerController@outboundList');
              $api->post('buyer-quote-action', 'BuyerController@quoteAction');
              $api->post('buyer-seller-search', 'BuyerController@sellerSearch');
              $api->post('city-suggestion', 'SellerRegistration@citySuggestions');
    
              $api->get('seller-post-details/{id}', 'SellerPostController@postDetails');
              $api->post('seller-registration', 'SellerRegistration@register');
              $api->get('seller-all-unverified', 'SellerVerification@getAllUnverified');
              $api->get('seller-verification-data', 'SellerVerification@getSellerData');
              $api->get('seller-verify', 'SellerVerification@sellerVerify');
              $api->post('seller-details', 'BuyerSearch@sellerDetails');
              $api->post('seller-search', 'SellerSearch@search');
              $api->post('seller-post-list', 'SellerController@postList');
              $api->post('seller-post/{status}', 'SellerRateController@post');
              $api->post('seller-search-details', 'SellerSearch@sellerSearchDetails');
             // $api->post('seller-quote-action', 'SellerController@sellerQuoteAction');
              $api->get('seller-bound-count', 'SellerController@boundCount');
              $api->post('seller-inbound', 'SellerController@inboundList');
              $api->post('seller-outbound', 'SellerController@outboundList');
              $api->post('seller-quote-action', 'SellerController@quoteAction');
              $api->get('vehicle-types', 'SellerRegistration@vehicleTypes');
              $api->get('machine-types', 'SellerRegistration@machineTypes');
              $api->post('email-check-seller', 'SellerController@emailCheck');
              /*sz*/
              $api->get('seller-post-detail-page/{id}','SellerPostController@postDetailPage');
            });
          });
          $api->get('bluecollar/docs/{file}', 'DocumentController@getDoc');
        });
    
    
    
    
    
    
        // Set our namespace for the underlying routes
        $api->group(['namespace' => 'ApiV2\Controllers', 'middleware' => '\Barryvdh\Cors\HandleCors::class'], function ($api) {
    
            $api->post('login', 'AuthController@authenticate');
            $api->get('getSignOut','AuthController@logoutUser');
            $api->post('testint','ShpInsuranceController@test');
            $api->post('getInsurance', 'ShpInsuranceController@getInsurance');
            $api->post('getServiceTaxCharges', 'ShpInsuranceController@getServiceTaxCharges');
            $api->get('gethdfcservice', 'ShpInsuranceController@hdfcpayment');
            $api->post('hdfcconfirm', 'ShpInsuranceController@response');
            $api->get('fcl/orders/payment_success/{paymentType}', 'FCLOrderController@paymentSuccess');
            

            $api->post('sendsms', 'CommonController\\MessageController@sendSms');
            $api->get('sendsms', 'CommonController\\MessageController@sendSms');
            
            $api->post('track', 'CommonController\\MessageController@userView');

            $api->group(['middleware' => 'jwt.auth', 'except' => ['index', 'show']], function ($api) {
    
                /************************************************
                 * Document Upload Controllers
                 ************************************************/
    
                $api->post('uploadfiles', 'DocumentUploadController@uploadFiles');
                $api->get('getfiledata/{postid}', 'DocumentUploadController@getFile');
    
    
                /************************************************
                 * Cart Management Controllers
                 ************************************************/
    
                $api->get('fcl/carts', 'FCLCartItemController@getCartItems');
                $api->post('fcl/carts', 'FCLCartItemController@addInitialCartDetails');
                $api->put('fcl/carts/{cartId}', 'FCLCartItemController@updateCartDetails');
                $api->get('fcl/carts/{cartId}', 'FCLCartItemController@getCartDetailsById');
                $api->delete('fcl/carts/{cartId}', 'FCLCartItemController@deleteCartById');
                $api->delete('fcl/carts', 'FCLCartItemController@emptyCart');
                $api->get('fcl/cart/checkout/{buyerid}/{serviceid}', 'FCLCartItemController@checkOut');
    
    
                /************************************************
                 * Order Management Controllers
                 ************************************************/
                $api->post('fcl/order', 'FCLOrderController@add');
                $api->get('fcl/order/{orderId}', 'FCLOrderController@get');
                $api->get('fcl/order/stats/{orderIds}', 'FCLOrderController@getStats');
                $api->post('fcl/orders/{userType}', 'FCLOrderController@getOrders');
                $api->put('fcl/order/{orderId}', 'FCLOrderController@updateStatus');
                $api->get('fcl/order/posts/{postId}', 'FCLOrderController@getOrdersPosts');
                $api->get('fcl/order/generate_invoice/{orderId}', 'FCLOrderController@generateInvoice');
                $api->get('fcl/orders/batch/{orderBatchId}', 'FCLOrderController@getOrdersByOrderBatchId');
    
    
                /************************************************
                 * Message Management Controllers
                 * TODO Soumya please check and take action
                 ************************************************/
    
                $api->post('messages','MessageController@CreateMessage');
                $api->post('messages/{messageId}/replies','MessageController@replies');
    
    
                $api->get('messages/{messageId}', 'MessageController@getMessage');
                $api->get('threads/{messageId}','MessageController@getThread');
    
                $api->post('messages/filter','MessageController@filter');
                $api->get('messages/list/show', 'MessageController@getNotificationMessages');
    
                $api->post('/messages/{messageId}?read=true','MessageController@markAsRead');
                $api->get('/mymessages','MessageController@getNotificationMessages');
    
                $api->post('/messages/{messageId}?notify=true','MessageController@markAsRead');


    
                /************************************************
                 * Token Management Controllers
                 ************************************************/
    
                $api->get('users', 'AuthController@index');
                $api->get('validate_token', 'AuthController@validateToken');
                $api->get('switchRole/{roleId}', 'AuthController@switchRole');
                $api->get('refreshToken', 'AuthController@refreshToken');
    
                /************************************************
                 * User Settings Controller
                 ************************************************/
    
                $api->get('usersettings/{serviceId}/{context}', 'UserSettingsController@getUserSettings');
                $api->post('usersettings/{serviceId}/{context}', 'UserSettingsController@storeUserSettings');

                $api->resource('usersettingadd', 'UserSettingsController@userSettingAdd');
                $api->post('settings/update', 'UserSettingsController@settingsUpdate');
                $api->get('settings/update', 'UserSettingsController@settingsUpdate');
                //$api->post('sellerSettings/update', 'UserSettingsController@sellerSettingsUpdate');
                  
    
                /************************************************
                 * TODO Sai please check and take action
                 * Remove all of these routes and the corresponding controllers
                 ************************************************/
                $api->get('sellerpost', 'SellerPostController@index');
                $api->get('sellerpost/{id}', 'SellerPostController@show');
                $api->post('sellerpost', 'SellerPostController@store');
                $api->put('sellerpost/{id}', 'SellerPostController@update');
    
    
                $api->get('buyerpost', 'BuyerPostController@index');
                $api->get('buyerpost/{id}', 'BuyerPostController@show');
                $api->post('buyerpost', 'BuyerPostController@store');
                $api->put('buyerpost/{id}', 'BuyerPostController@update');
    
                /************************************************
                 * Code List Management Controllers
                 ************************************************/
    
                $api->get('codelist', 'CodeListController@index');
                $api->get('codelist/{id}', 'CodeListController@show');
                $api->get('codelist/many/{ids}', 'CodeListController@showMany');
                $api->post('codelist/post', 'CodeListController@store');
    
    
                /************************************************
                 * Menu Management Controllers
                 ************************************************/
                $api->get('menu/{userId}', 'MenuController@getUserMenu');
    
    
                /************************************************
                 * FCL Seller Post Management Controllers
                 ************************************************/
                $api->get('fcl/sellerposts', 'FCLSellerPostController@getAllPosts');
                $api->get('fcl/sellerposts/{postId}', 'FCLSellerPostController@getPostById');
                $api->post('fcl/sellerposts/filters', 'FCLSellerPostController@filter');
                $api->post('fcl/sellerpost', 'FCLSellerPostController@saveOrUpdate');
                $api->post('fcl/bulksellerpost', 'FCLSellerPostController@bulkSaveOrUpdate');
                $api->post('fcl/sellerposts/postmasterfilters', 'FCLSellerPostController@postMasterFilter');
                $api->post('fcl/sellerposts/postmasterinboundfilters', 'FCLSellerPostController@postMasterInbound');
    
                /************************************************
                 * FCL Buyer Post Management Controllers
                 ************************************************/
    
                $api->get('fcl/buyerposts', 'FCLBuyerPostController@getAllPosts');
                $api->get('fcl/buyerposts/{postId}', 'FCLBuyerPostController@getPostById');
                $api->get('fcl/buyerspotposts/', 'FCLBuyerPostController@getAllSpotPosts');
                $api->get('fcl/buyertermposts/', 'FCLBuyerPostController@getAllTermPosts');
                $api->post('fcl/buyerposts/filters', 'FCLBuyerPostController@filter');
                $api->post('fcl/buyertermcontract', 'FCLBuyerPostController@saveGenerateContract');
                $api->get('fcl/buyertermcontract/{postId}', 'FCLBuyerPostController@getGeneratedContractsByPostId');
                $api->post('fcl/buyerposts/postmasterfilters', 'FCLBuyerPostController@postMasterFilter');
                $api->get('fcl/buyertermcontract/term/{contractId}', 'FCLBuyerPostController@getGeneratedContractsByTermContractId');
    
                //To retrieve Buyer Inbounds
                $api->post('fcl/buyerposts/postMasterInboundFilter', 'FCLBuyerPostController@postMasterInboundFilter');
    
                $api->get('fcl/buyerprivacyposts/{ispublic}', 'FCLBuyerPostController@getAllPostsByPostPrivacy');
                $api->post('fcl/buyerpostspot', 'FCLBuyerPostController@saveOrUpdateSpot');
                $api->post('fcl/buyerpostterm', 'FCLBuyerPostController@saveOrUpdateTerm');
                //Excel Uploads
                $api->post('fcl/bulkbuyerpostspot', 'FCLBuyerPostController@uploadSpotExcel');
                $api->post('fcl/bulkbuyerpostterm', 'FCLBuyerPostController@uploadTermExcel');
    
                //BuyerPost Seller Offer Usecase
                $api->post('fcl/sellerquote', 'FCLSellerQuoteControllers@saveOrUpdateQuote');
                $api->post('fcl/sellerquoteterm', 'FCLSellerQuoteControllers@saveOrUpdateTermQuote');
                $api->get('fcl/sellerquote/{postId}', 'FCLSellerQuoteControllers@getSellerOffersByBuyerPostId');
                $api->get('fcl/sellerquote/term/{postId}', 'FCLSellerQuoteControllers@getTermSellerOffersByBuyerPostId');
                $api->get('fcl/sellerinbound', 'FCLSellerQuoteControllers@getSellerInboundBuyerPosts');
                $api->get('fcl/sellerquote/quote/{quoteId}', 'FCLSellerQuoteControllers@getQuoteDetailsByQuoteId');
                $api->get('fcl/sellerprivacyposts/{ispublic}', 'FCLSellerPostController@getAllPostsByPostPrivacy');
    
                //Lookup locations/ports/zipcodes
                $api->get('locations/filters/{term}', 'LocationController@filterLocations');
                $api->get('locations/ports/{term}', 'LocationController@filterPorts');
                $api->get('locations/zipcodes/{term}', 'LocationController@filterZipcodes');
    
                /*
                |--------------------------------------------------------------------------
                | (Start) Codded By MindzTechnology
                |--------------------------------------------------------------------------
                */
    
                //***************quotation start**********************//
                
                $api->post('seller-quote-submission','IntracityNegotiation@sellerQuoteSubmit');
                $api->post('hp-seller-quote-submission','IntracityNegotiation@sellerhpQuoteSubmit');

                $api->post('negotation/buyerQuoteAction','IntracityNegotiation@buyerQuoteAction');
                $api->post('buyer-post-lead-list', 'BuyerController@postLeadsList');
                $api->post('get-intra-slab', 'BuyerController@getIntraSlab');

                $api->get('buyer-in-out-bound-count','IntracityNegotiation@buyerInOutBoundCount');
                $api->get('buyer-inbound-list','IntracityNegotiation@buyerInboundList');
                $api->get('buyer-outbound-list','IntracityNegotiation@buyerOutboundList');
                $api->post('accept-quote','IntracityNegotiation@buyerFirmQuoteAction');
                $api->post('buyer-competitive-quote-action','IntracityNegotiation@buyerCompetitiveQuoteAction');
                $api->post('buyer-final-quote-action','IntracityNegotiation@buyerFinalQuoteAction');
    
                $api->get('seller-in-out-bound-count','IntracityNegotiation@sellerInOutBoundCount');
                $api->post('seller-inbound-list','IntracityNegotiation@sellerInboundList');
                $api->get('seller-outbound-list','IntracityNegotiation@sellerOutboundList');
                $api->post('sellerQuoteAction','IntracityNegotiation@sellerQuoteAction');
                $api->post('seller-quote-counter-action','IntracityNegotiation@sellerQuoteCounterAction');
    
                $api->post('quote-decline-action','IntracityNegotiation@declineQuoteAction');

                //****************qutation end*************************//
    
                $api->get('locations/getCity','LocationController@getCity');
                $api->get('locations/getVehiletype','LocationController@getVehiletype');
    
                $api->get('locations/getlocality/{id}', 'LocationController@getLocality');
                $api->post('intracity-buyer-quotes', 'LocationController@intracityBuyerQuotesPost');
    
                /* For Buyer Details */
                $api->get('getbuyerdetails', 'LocationController@getbuyerdetails');
    
                /* For Hour Distance Slabs */
                $api->get('get-hour-distance-labs', 'IntracityBuyerPostController@hourDistanceLabs');
    
                /* For Location Type */
                $api->get('get-location-type', 'LocationController@locationType');
    
                /* For Packaging Types */
                $api->get('get-packaging-types', 'LocationController@packagingType');
    
                /* For Seller Details */
                $api->get('get-seller-details', 'LocationController@sellerDetails');
    
                /* For Seller Details */
                $api->get('getLoadType', 'LocationController@getLoadType');
    
                /* For Buyer Post Spot */
                $api->post('buyer-post-spots', 'BuyerController@buyerSpotsPost');
    
                /* For  Buyer Post Term*/
                $api->post('buyer-post-terms', 'BuyerController@buyerTermPost');
                /* For Seller Rate Cart */
                $api->post('seller-rate-cart', 'SellerController@sellerRateCartPost');
                $api->post('seller-drafts-rate-cart', 'SellerController@sellerDraftRateCartPost');
    
                /* For Buyer Post Count */
                $api->get('count-buyerpost-spots', 'BuyerController@countBuyerPostSpots');

               /**For Post and Order Counts */
                $api->get('total-count', 'CommonController\MessageController@totalCounts');

                /* For Total Post Count For Buyer */
                $api->get('total-post-count', 'BuyerController@totalPostCount');

                /* For Total Order Count For Buyer */
                $api->get('total-order-count', 'BuyerController@totalOrderCount');

                $api->get('left-nav-data/{id}/{roleId}', 'BuyerController@leftNavData');

                $api->get('popup-menu-data/{id}/{roleId}/{menutype}', 'BuyerController@popupMenuData');

                $api->post('/order-messages','CommonController\MessageController@getOrderMessages');
                /* For Buyer List */
                $api->get('buyer-post-list', 'BuyerController@buyerlist');
                $api->get('buyer-listing-according-filters/{id}', 'BuyerController@accorindfilter');
                $api->get('get-buyer-routes-details/{id}', 'BuyerController@getBuyerRouteDetail');
    
                /* Buyer Search seller to submit our quote */
                $api->post('buyer-search', 'BuyerController@buyerSearch');
    
    
                /* For All Records */
                $api->post('get-all-records', 'BuyerController@records');
                $api->post('get-post-details', 'BuyerController@getRecordsDetails');
                $api->post('intra-private-post', 'BuyerController@intraPrivateBuyerPost');

                /** For Notification Settings Data */
                $api->get('settings-data', 'BuyerController@settingsData');
    
                $api->get('count-inbound','BuyerController@countInboundRecords');
                // $api->get('get-all-records/{type}', 'BuyerController@records');
                $api->get('getPostDataById/{id}', 'BuyerController@getPostDataById');

                /**Header Notification section for post master */
                $api->get('post-master/{userid}/{roleId}', 'CommonController\MessageController@postMasterCounts');

                /**Header Notification section for order master */
                $api->get('order-master/{userid}/{roleId}', 'CommonController\MessageController@orderMasterCounts');

    
                /* For search Seller Rate Cart */
                $api->post('buyer-post-search', 'SellerController@buyerPostSearchIntra');

                /* For Seller Leads**/
                $api->get('seller-leads-details', 'SellerController@sellerLeadsDetails');

    
                /* For seller Post Count */
                $api->get('count-seller-post', 'SellerController@countSellerPostSpots');
                /* For get seller post details */
                $api->get('seller-post-details/{id}', 'SellerController@sellerPostDetails');
                $api->post('seller-post-draft-details', 'SellerController@sellerPostDraftDetails');

                $api->get('seller-post-discout/{id}', 'SellerController@sellerPostDiscount');
                $api->post('seller-post-delete', 'SellerController@sellerPostDelete');
    
    
                $api->get('seller-pre-discout/{id}', 'SellerController@sellerPreDiscount');
                $api->get('seller-final-discout/{id}', 'SellerController@sellerFinalDiscount');
    
                $api->any('seller-post-list', 'SellerController@sellerPostLists');
                /* For seller post message by Id */
                $api->get('seller-post-message/{id}/{id2}', 'SellerController@messageById');
                $api->get('buyer-post-details/{id}', 'BuyerController@buyerPostDetails');
                $api->post('buyer-post-lead-details', 'BuyerController@buyerPostLeadDetails');
                 $api->post('buyer-post-delete', 'BuyerController@deleteBuyerPost');
                 

                 
                /* For buyer message by Id */
                $api->get('buyer-post-message/{id}/{id2}', 'BuyerController@messageById');
    
                $api->post('intracity/updateCartItem',[
                    'uses'=>'IntracityCartItemsController@updateCartDetails'
                ]);

                $api->post('intracity/acceptPlaceTruckGSA',[
                    'uses'=>'OrderController@acceptPlaceTruckGSA'
                ]);

                $api->post('intracity/confirmPlaceTruck',[
                    'uses'=>'OrderController@confirmPlaceTruck'
                ]);

                $api->post('intracity/confirmConsignmentPickup',[
                    'uses'=>'OrderController@confirmConsignmentPickup'
                ]);

                $api->post('intracity/confirmTransitDetail',[
                    'uses'=>'OrderController@confirmTransitDetail'
                ]);

                $api->post('intracity/consignmentDeliveryDetails',[
                    'uses'=>'OrderController@consignmentDeliveryDetails'
                ]);
                // Confirm delivery by buyer
                $api->post('intracity/confirmDelivery',[
                    'uses'=>'OrderController@confirmDelivery'
                ]);
            

                
                
                //Get Cart Count
                $api->get('get-cart-count', 'IntracityCartItemsController@cartCount');

                //For preventing cart from same values
                $api->get('check-cart-value', 'IntracityCartItemsController@checkCartValue');
    
                //Get Seller Details By ID
                $api->get('get-seller-details-by-id/{sellerid}', 'SellerController@getSellerdetailsById');
    
                // Cart Item
    
                $api->post('intracity/carts',[
                    'uses'=>'IntracityCartItemsController@addInitialCartDetails'
                ]);

                $api->post('intracity/leadsCarts',[
                    'uses'=>'IntracityCartItemsController@addInitialLeadsCartDetail'
                ]);
    
                $api->get('intracity/carts',[
                    'uses'=>'IntracityCartItemsController@getCartItems'
                ]);

                $api->Post('intracity/dataPrepaid',[
                    'uses'=>'IntracityCartItemsController@dataPrepaid'
                ]);
            // Import Excel 
                $api->Post('intracity/uploadBulkData',[
                    'uses'=>'CommonController\\DocumentImportController@uploadBulkData'
                ]);
                
                $api->get('intracity/carts/{id}',[
                    'uses'=>'IntracityCartItemsController@getDetailsByCartId'
                ]);
    
                $api->post('intracity/deleteCarts',[
                    'uses'=>'IntracityCartItemsController@deleteCartItem'
                ]);
                $api->post('intracity/clearCarts',[
                    'uses'=>'IntracityCartItemsController@emptyCartItem'
                ]);
    
    
                $api->post('intracity/updateCartStatus',[
                    'uses'=>'IntracityCartItemsController@updateCartStatus'
                ]);
    
                $api->post('orderplace',[
                    'uses'=>'OrderController@orderPlace'
                ]);
                $api->post('orderconform',[
                    'uses'=>'OrderController@orderConform'
                ]);
                $api->get('orderinfo',[
                    'uses'=>'OrderController@orderInfo'
                ]);
                $api->get('orderMaster/{serviceId}',[
                    'uses'=>'OrderController@orderMaster'
                ]);
    
                $api->post('orderDetails',[
                    'uses'=>'OrderController@orderDetails'
                ]);
                
                $api->get('getOrderNumber/{serviceId}',[
                    'uses'=>'OrderController@getOrderNumber'
                ]);
    
                

                $api->post('orderMaster/filter',[
                    'uses'=>'OrderController@orderMasterFilter'
                ]);
    
                //Get Buyer Discount By ID
                $api->get('get-buyer-discount/{id}', 'BuyerController@getDiscount');
    
    
                //Get Buyer Discount By ID
                $api->get('messageType', 'MessageController@getMessageTypes');
    
                $api->get('txn', 'BuyerController@generateTxn');
                //Uploads file
    
                $api->post('uploadfiles/file', 'BuyerController@uploadFiles');

                $api->post('hpUploadfiles/file', 'BuyerController@hpUploadFiles');
                
                $api->post('fetch/order-document', 'OrderController@fetchOrderDocument');
                
                $api->post('download/order-document', 'OrderController@downloadOrderDocument');
                
                $api->post('email/order-document', 'OrderController@emailOrderDocument');
                
                

                
                // Message Routes
                $api->post('messages/send', 'BuyerController@sendMessage');
                $api->post('messages/list/sender', 'CommonController\\MessageController@getMessage');
                $api->post('messages/communityMessage', 'CommonController\\MessageController@communityMessage');
                $api->post('messages/{id}/reply', 'CommonController\\MessageController@reply');
                $api->get('getMessageById/{senderId}', 'CommonController\\MessageController@getMessageBySenderId');
                $api->get('broadcastMessage/{channel}/{event}', 'CommonController\\MessageController@broadcastMessage');
                $api->get('hyperlocal/getMsgNotification', 'CommonController\\MessageController@getNotification');
                $api->post('hyperlocal/updateMessage/{id}', 'CommonController\\MessageController@updateMessage');
                $api->get('hyperlocal/getMessageCount', 'CommonController\\MessageController@messageCount');
                $api->post('hyperlocal/seller-setting-save', 'Intracity\\SellerSettingPost@saveSellerPostMasterSetting'); 
                $api->post('hyperlocal/buyer-setting-save', 'Intracity\\SellerSettingPost@saveBuyerPostMasterSetting');                     
                
                // sellerExcelUpload
                $api->post('hyperlocal/sellerExcelUpload','CommonController\\DocumentImportController@uploadBulkDataSellerRatecard');
                
                $api->group(['namespace' => 'HyperLocal', 'middleware' => '\Barryvdh\Cors\HandleCors::class'], function ($api) {
                    $api->group(['middleware' => 'jwt.auth'], function ($api) {
                        $api->group(['prefix' => 'hyperlocal'], function ($api) {
                            $api->get('product-category', 'SellerController@productCategory');
                            $api->post('seller-rate-card-post', 'SellerController@sellerRateCardPost');
                            $api->post('seller-rate-card-drafts-post', 'SellerController@sellerRateCardDraftsPost');
                            $api->post('seller-list', 'SellerController@sellerPostList');
                            $api->get('seller-list-counts', 'SellerController@sellerListCounts');
                            $api->get('seller-search-filters', 'SellerController@searchAccdngFilters');
                            $api->post('search-result-seller', 'SellerController@sellerSearchResult');
                            $api->get('seller-post-details/{id}', 'SellerController@getPostDetails');
                            $api->post('hp-buyer-post-search', 'SellerController@hpBuyerPostSearch');
                            $api->post('hp-seller-post-lead-list', 'SellerController@postSellerLeadsList');
    
                            $api->any('hp-buyer-search', 'SellerController@sellerSearchList');
                            $api->post('hp-seller-buyer-search', 'BuyerPost@sellerSearch');
                            $api->post('hp-buyer-post-lead-list', 'BuyerPost@postLeadsList');
    
                            $api->post('buyer-post', 'BuyerPost@datapost');
                            $api->post('buyer-post-drafts', 'BuyerPost@datapostDrafts');
    
                            $api->post('buyer-post-draft-detail', 'BuyerPost@getHpPostDetails');
                            $api->post('seller-post-draft-detail', 'SellerController@getHpSellerPostDetails');
                            $api->post('drafts-location', 'BuyerPost@getDraftsLocation');
                            $api->get('hp-get-all-records-inbound/{type}', 'BuyerPost@getRecordsInbound');
                            $api->post('hp-get-all-records-inbound/{type}', 'BuyerPost@getRecordsInbound');
                             $api->post('hp-get-all-records-inbound', 'BuyerPost@getRecordsInbound');
    
                            $api->get('hp-get-all-records', 'BuyerPost@getRecords');
                            $api->post('hp-get-all-records', 'BuyerPost@getRecords');
                            $api->get('get-post-count','BuyerPost@getPostCount');
                            $api->post('hp-get-buyer-inbound','BuyerPost@getInboundPost');
                            $api->post('hp-get-buyer-inbound-details','BuyerPost@inboundPostDetail');
                            $api->post('hp-get-seller-inbound-details','BuyerPost@sellerInbondDetails');
                            
                            $api->post('seller-search-result', 'SellerSearch@searchresult');
                            $api->get('buyer-post-details/{id}', 'BuyerPost@getPostDetail');
                            $api->get('buyer-postlead-details/{id}', 'BuyerPost@getPostLeadDetail');
                            $api->get('seller-postlead-details/{id}', 'SellerController@getSellerPostLeadDetail');
                            $api->any('buyer-post-route-quote/{id}','BuyerPost@quoteRoute');
                            $api->post('buyer-post-delete', 'BuyerPost@buyerPostDelete');
                            $api->post('buyer-post-term-contract', 'BuyerPost@termContract');
                            /// negotiation update
                            $api->post('update-quote-status','BuyerPost@updateQuoteStatus');
                            $api->post('cancel-quote-status','BuyerPost@cancelContract');
                            $api->post('seller-update-quote-final','BuyerPost@sellerFinalQuote');
                            $api->post('seller-quote-contract-accept','BuyerPost@sellerContractAccept');
                             

                        });
                    });
                });
                ///community route start here

                 $api->group(['namespace' => 'Community', 'middleware' => '\Barryvdh\Cors\HandleCors::class'], function ($api) {
                    $api->group(['middleware' => 'jwt.auth'], function ($api) {
                        $api->group(['prefix' => 'community'], function ($api) {

                            $api->post('communityProfile', 'CommunityProfileController@communityProfile');
                            $api->post('getFollowers', 'FollowController@getFollowers');
                            $api->post('follow', 'FollowController@follow');
                            $api->post('getMyFollowers', 'FollowController@getMyFollowers');
                            $api->post('getMyFollowing', 'FollowController@getMyFollowing');
                            
                            $api->post('unFollow', 'FollowController@unFollow');

                            $api->post('getAllBusiness', 'MemberController@getAllBusiness');
                             
                            $api->post('sendInvitation', 'MemberController@sendInvitation');
                            $api->post('sendBulkInvitation', 'MemberController@sendBulkInvitation');
                            
                            $api->post('createNewGroup', 'MemberController@createNewGroup');
                            
                            $api->get('getInvitation', 'MemberController@getInvitation');
                            
                            $api->post('invitation/action', 'MemberController@actionOnInvitation');

                            $api->get('getGroup', 'MemberController@getGroupAction');                            
                            
                            
                            $api->get('getProfileDetail', 'CommunityProfileController@getProfileDetail');
                            
                            $api->post('add-article', 'ArticlePostController@addArticle');
                            $api->post('edit-articles', 'ArticlePostController@editArticles');
                            $api->post('edit-article/{id?}', 'ArticlePostController@editArticle');
                            $api->get('article-list/{id?}', 'ArticlePostController@Articlelist');
                            $api->post('post-comment', 'ArticlePostController@Comment');
                            $api->post('post-comment-reply', 'ArticlePostController@CommentReply');
                            $api->post('article-likes', 'ArticlePostController@Likes');
                            $api->post('comment-delete', 'ArticlePostController@commentDelete');
                            $api->post('comment-update', 'ArticlePostController@commentUpdate');
                            $api->post('reply-delete', 'ArticlePostController@replyDelete');
                            $api->post('reply-update', 'ArticlePostController@replyUpdate');

                            $api->post('post-delete', 'ArticlePostController@postDelete');
                            $api->get('get-all-users', 'CommunityController@getAllUsers');
                            $api->post('get-all-seller-community', 'CommunityController@getSellerDetail');

                            /** For member who register in Events */
                            $api->post('events-register', 'EventsRegisterController@register');
                            $api->get('get-event-details/{title}', 'EventsRegisterController@eventDetails');

                            $api->post('share', 'ShareController@shared');
                        });

                    });
                });

               // communit route end here

    
                /*
                |--------------------------------------------------------------------------
                | (End) Codded By MindzTechnology
                |--------------------------------------------------------------------------
                */
    
                /******************************** START LCL ****************************************************/
    
                //SellerPost  Usecase
                $api->get('lcl/sellerposts', 'LCLSellerPostController@getAllPosts');
                $api->get('lcl/sellerposts/{postId}', 'LCLSellerPostController@getPostById');
                $api->post('lcl/sellerposts/filters', 'LCLSellerPostController@filter');
                $api->post('lcl/sellerpost', 'LCLSellerPostController@saveOrUpdate');
    
                //BuyerPost  Usecase
                $api->get('lcl/buyerposts', 'LCLBuyerPostController@getAllPosts');
                $api->get('lcl/buyerposts/{postId}', 'LCLBuyerPostController@getPostById');
                $api->post('lcl/buyerposts/filters', 'LCLBuyerPostController@filter');
                $api->post('lcl/buyerpostspot', 'LCLBuyerPostController@saveOrUpdateSpot');
                $api->post('lcl/buyerpostterm', 'LCLBuyerPostController@saveOrUpdateTerm');
    
                //BuyerPost Seller Offer Usecase
                $api->post('lcl/sellerquote', 'LCLSellerQuoteControllers@saveOrUpdateQuote');
                $api->post('lcl/sellerquoteterm', 'LCLSellerQuoteControllers@saveOrUpdateTermQuote');
                $api->get('lcl/sellerquote/{postId}', 'LCLSellerQuoteControllers@getSellerOffersByBuyerPostId');
                $api->get('lcl/sellerquote/term/{postId}', 'LCLSellerQuoteControllers@getTermSellerOffersByBuyerPostId');
                $api->get('lcl/sellerinbound', 'LCLSellerQuoteControllers@getSellerInboundBuyerPosts');
                $api->get('lcl/sellerquote/quote/{quoteId}', 'LCLSellerQuoteControllers@getQuoteDetailsByQuoteId');
                $api->get('lcl/sellerprivacyposts/{ispublic}', 'LCLSellerPostController@getAllPostsByPostPrivacy');
    
                //LCL Excel uploads
    
                $api->post('lcl/bulkbuyerpostspot', 'LCLBuyerPostController@uploadSpotExcel');
                //$api->post('lcl/bulkbuyerpostterm', 'LCLBuyerPostController@uploadTermExcel');
    
    
                /******************************** END LCL ****************************************************/
    
                /******************************** START AirFreight ****************************************************/
    
                //SellerPost  Usecase
                $api->get('airfreight/sellerposts', 'AirFreightSellerPostController@getAllPosts');
                $api->get('airfreight/sellerposts/{postId}', 'AirFreightSellerPostController@getPostById');
                $api->post('airfreight/sellerposts/filters', 'AirFreightSellerPostController@filter');
                $api->post('airfreight/sellerpost', 'AirFreightSellerPostController@saveOrUpdate');
    
                //BuyerPost  Usecase
                $api->get('airfreight/buyerposts', 'AirFreightBuyerPostController@getAllPosts');
                $api->get('airfreight/buyerposts/{postId}', 'AirFreightBuyerPostController@getPostById');
                $api->post('airfreight/buyerposts/filters', 'AirFreightBuyerPostController@filter');
                $api->post('airfreight/buyerpostspot', 'AirFreightBuyerPostController@saveOrUpdateSpot');
                $api->post('airfreight/buyerpostterm', 'AirFreightBuyerPostController@saveOrUpdateTerm');
    
    
    
                //Excel Uploads
                $api->post('airfreight/bulkspotupload', 'AirFreightBuyerPostController@uploadSpotExcel');
                $api->post('airfreight/bulktermupload', 'AirFreightBuyerPostController@uploadTermExcel');
    
                /******************************** END AirFreight ****************************************************/
    
                /******************************** START RoRo ****************************************************/
    
                //SellerPost  Usecase
                $api->get('roro/sellerposts', 'RoRoSellerPostController@getAllPosts');
                $api->get('roro/sellerposts/{postId}', 'RoRoSellerPostController@getPostById');
                $api->post('roro/sellerposts/filters', 'RoRoSellerPostController@filter');
                $api->post('roro/sellerpost', 'RoRoSellerPostController@saveOrUpdate');
    
                //BuyerPost  Usecase
                $api->get('roro/buyerposts', 'RoRoBuyerPostController@getAllPosts');
                $api->get('roro/buyerposts/{postId}', 'RoRoBuyerPostController@getPostById');
                $api->post('roro/buyerposts/filters', 'RoRoBuyerPostController@filter');
                $api->post('roro/buyerpost', 'RoRoBuyerPostController@saveOrUpdate');
    
                /******************************** END RoRo ****************************************************/
    
                /******************************** START User Services ****************************************************/
    
                //UserServices  Usecase
                $api->get('getallseller', 'UserServices@getAllSeller');
                $api->get('getallbuyer', 'UserServices@getAllBuyer');
                $api->get('getallusers', 'UserServices@getAllBuyer');
                $api->get('getuserbyid/{userID}', 'UserServices@getUserById');
                $api->get('getcurrentuser', 'UserServices@getCurrentUserDetails');
                $api->get('getnamelist/{term}', 'UserServices@getNameList');
                $api->get('getuseremail/{username}', 'UserServices@getUserEmail');
                $api->get('getbuyerpostmastercounts', 'UserServices@getBuyerPostMasterCounts');
                $api->get('getsellerpostmastercounts', 'UserServices@getSellerPostMasterCounts');
    
                /******************************** END User Services  ****************************************************/
    
    
                $api->get('howto/exceptions/good', 'HowtoExceptionsController@good');
                $api->get('howto/exceptions/bad', 'HowtoExceptionsController@bad');
                $api->get('howto/exceptions/ugly', 'HowtoExceptionsController@ugly');
                $api->get('howto/exceptions/unauthorized', 'HowtoExceptionsController@unauthorized');
                $api->get('howto/exceptions/failure', 'HowtoExceptionsController@failure');
    
    
    
            });
    
    
            /*-------------------------------
    
            Temporary test Controllers. Will be removed post release
    
            ---------------------------------*/
    
            $api->post('fcl/cart/upload', 'FCLCartItemController@uploadCartExcel');
    
            $api->get('/syncbuyerposts', function () {
                dispatch(new App\Jobs\SyncBuyerPosts2SearchStore());
            });
    
            $api->get('/phpinfo', function () {
                phpinfo();
            });
    
            $api->get('testSMTP', function()
            {
                Mail::raw('Hola ! Testing SMTP Mail.', function ($message) {
                    $message->from('sreedutt.santhinilayam@techwave.net', 'Sreedutt');
                    $message->to('ttudeers@gmail.com');
                });
                return "Success";
            });
        });
    });      
   
    
    });