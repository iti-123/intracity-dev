app.config(function ($stateProvider, $urlRouterProvider) {

    var Auth = new AuthenticationContext();

    $urlRouterProvider.otherwise('/home');
    //State provider

    $stateProvider.state('/seller', {
        url: "/seller-rate-card",
        templateUrl: "templates/seller/seller-rate-card.htm",
        controller: "SellerCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                      'js/data.js', 
                    ]
                });
            }
        }
    })
        .state('home', {
            url: "/home",
            templateUrl: "templates/home/home.htm",
            controller: "lgAppCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'templates/home/HomeCtrl.js',
                            'templates/home/homeServices.js'
                        ]
                    });
                }
            }
        })
        .state('services', {
            url: "/services",
            templateUrl: "templates/home/home.htm",
            controller: "lgAppCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'templates/home/HomeCtrl.js',
                            'templates/home/homeServices.js'
                        ]
                    });
                }
            }
        })
        .state('products', {
            url: "/products",
            templateUrl: "templates/home/home.htm",
            controller: "lgAppCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'templates/home/HomeCtrl.js',
                            'templates/home/homeServices.js'
                        ]
                    });
                }
            }
        })
        .state('buyer', {
            url: "/buyer-post",
            templateUrl: "templates/buyer/buyer-post.htm",
            controller: "BuyerCtrl",
        })
        .state('login', {
            url: "/login",
            templateUrl: "login.html"
        })
        .state('post-buyer-as-term', {
            url: "/post-buyer-as-term",
            templateUrl: "templates/buyer/post-buyer-term.htm",
            controller: "BuyerCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/buyer/BuyerCtrl.js',
                            'js/services/validate.js',
                            'modules/map/GoogleMapService.js',
                            'js/directive/calenderdirective.js',
                            'js/data.js'

                        ]
                    });
                }
            }
        })
        .state('post-buyer-as-spot', {
            url: "/post-buyer-as-spot",
            templateUrl: "templates/buyer/post-buyer-spot.htm",
            controller: "BuyerCtrl",
            resolve: {
                "check": function ($location) { //function to be resolved, accessFac and $location Injected

                    // if (Auth.getUserActiveRole().toLowerCase() != 'seller') { //check if the user has permission -- This happens before the page loads

                    // }
                },
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/buyer/BuyerCtrl.js'
                        ]
                    });
                }
            }
        })
        .state('post-buyer-as-spot-edit', {
            url: "/post-buyer-as-spot-edit/:id",
            templateUrl: "templates/buyer/post-buyer-spot.htm",
            controller: "BuyerCtrl",
            resolve: {
                "check": function ($location) { //function to be resolved, accessFac and $location Injected

                    // if (Auth.getUserActiveRole().toLowerCase() != 'seller') { //check if the user has permission -- This happens before the page loads

                    // }
                },
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/buyer/BuyerCtrl.js'
                        ]
                    });
                }
            }
        })
        
        .state('/sellerDrafts', {
            url: "/seller-rate-draft-card/:id",
            templateUrl: "templates/seller/seller-rate-draft-card.htm",
            controller: "SellerDraftCardCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                           'js/controller/seller/SellerDraftCardCtrl.js',
                          'js/data.js', 
                        ]
                    });
                }
            }
        })

        .state('/buyerHpDrafts', {
            url: "/hp-buyer-post-draft/:id",
            templateUrl: "modules/hyperlocal/buyer/templates/buyer-post-draft.htm",
            controller: "BuyerDraftCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                           'modules/hyperlocal/buyer/controllers/BuyerDraftCtrl.js',
                          'js/data.js', 
                        ]
                    });
                }
            }
        })

        .state('seller-search', {
            url: "/seller-search",
            templateUrl: "templates/seller/seller-search.htm",
            controller: "SellerSearchCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/seller/SellerSearchCtrl.js',
                            'js/data.js',
                        ]
                    });
                }
            }
        })
        .state('seller-search-result', {
            url: "/seller-search-result",
            templateUrl: "templates/seller/seller-search-result.htm",
            controller: "SellerSearchResultCtrl",
            //params:{'searchdata': null},
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/seller/SellerSearchResultCtrl.js',
                            'js/data.js',
                            'modules/messages/services/messageServices.js',
                            'modules/bluecollar/directives/bluecollar-directives.js'
                        ]
                    });
                }
            }
        })
        .state('buyer-search', {
            url: "/buyer-search",
            templateUrl: "templates/buyer/buyer-search.htm",
            controller: "BuyerSearchCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/buyer/BuyerSearchCtrl.js',
                            'js/services/validate.js',
                            'js/data.js'
                        ]
                    });
                }
            }
        })

        .state('buyer-list', {
            url: "/buyerlist",
            templateUrl: "templates/buyer/buyer-list.htm",
            controller: "BuyerListCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/buyer/BuyerListCtrl.js'
                        ]
                    });
                }
            }
        })
        .state('buyerlistdetail', {
            url: "/buyerlist/:title",
            templateUrl: "templates/buyer/buyer-list-detail.htm",
            controller: "BuyerListDetailCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/buyer/BuyerListDetailCtrl.js'
                        ]
                    });
                }
            }
        })
/**
 *
 * Order master router
 *
 */
        .state('ordermaster', {
            url: "/ordermaster/:serviceName",
            templateUrl: "templates/order/ordermaster.htm",
            controller: "OrderMasterCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/order/OrderMasterCtrl.js'
                        ]
                    });
                }
            }
        })

        .state('orderDetails', {
            url: "/orderdetails/:id",
            templateUrl: "templates/order/orderdetails.htm",
            controller: "OrderDetailCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/order/OrderDetailCtrl.js'
                        ]
                    });
                }
            }
        })

        .state('buyer-search-result', {
        url: "/buyer-search-result/:id",
        templateUrl: "templates/buyer/buyer-search-result.htm",
        controller: "BuyerSearchResultCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/buyer/BuyerSearchResultCtrl.js',
                        'modules/bluecollar/directives/bluecollar-directives.js',
                        'modules/messages/services/messageServices.js',
                        'modules/map/GoogleMapService.js'
                    ]
                });
            }
        }
    }).state('seller-post-list', {
        url: "/seller-post-list",
        templateUrl: "templates/seller/seller-post-list.htm",
        controller: "sellerListCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'modules/bluecollar/filters/bluecollar-filters.js',
                        'js/controller/seller/sellerListCtrl.js'
                    ]
                });
            }
        }
    })

    .state('sellerinbounddetail', {
        url: "/sellerinbounddetail/:title",
        templateUrl: "templates/seller/seller-inbound-post-detail.htm",
        controller: "SellerInboundDetailCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/seller/SellerInboundDetailCtrl.js',
                        'modules/messages/services/messageServices.js',
                        'modules/bluecollar/directives/bluecollar-directives.js'
                    ]
                });
            }
        }
    })

    .state('buyerDetails', {
        url: "/buyerDetails/:id",
        templateUrl: "templates/buyer/buyer-post-details.htm",
        controller: "postDetailCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/buyer/postDetailCtrl.js',
                        'modules/bluecollar/directives/bluecollar-directives.js'
                    ]
                });
            }
        }

    })
    
     .state('buyerLeadDetails', {
        url: "/buyerLeadDetails/:id",
        templateUrl: "templates/buyer/buyer-post-lead-details.htm",
        controller: "postDetailLeadCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/buyer/postDetailLeadCtrl.js',
                        'js/data.js',
                        'modules/map/GoogleMapService.js',
                        'modules/bluecollar/directives/bluecollar-directives.js'
                    ]
                });
            }
        }

    })

    .state('sellerDetails', {
        url: "/sellerDetails/:id",
        templateUrl: "templates/seller/seller-post-message.htm",
        controller: "postDetailCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/seller/postDetailCtrl.js',
                        'modules/messages/services/messageServices.js'
                    ]
                });
            }
        }

    })

    .state('seller-leads', {
        url: "/seller-leads/:id",
        templateUrl: "templates/seller/seller-leads.htm",
        controller: "postDetailCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/seller/postDetailCtrl.js',
                        'modules/messages/services/messageServices.js',
                        'modules/bluecollar/directives/bluecollar-directives.js'
                    ]
                });
            }
        }

    })

    .state('order-booknow', {
        url: "/booknow/:serviceId/:cartId",
        templateUrl: "templates/order/booknow.htm",
        controller: "OrderCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/order/OrderCtrl.js',
                        'js/data.js'
                    ]
                });
            }
        }


    })

    .state('cart', {
        url: "/cart",
        templateUrl: "templates/order/cart.htm",
        controller: "CartCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/order/CartCtrl.js'
                    ]
                });
            }
        }

    })

    .state('checkout', {
        url: "/checkout",
        templateUrl: "templates/order/checkout.htm",
        controller: "CheckoutCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/order/CheckoutCtrl.js'
                    ]
                });
            }
        }

    })

    .state('updateradecard', {
        url: "/seller-rate-card-edit/:id",
        templateUrl: "templates/seller/seller-rate-card.htm",
        controller: "SellerCtrl",
        resolve: {
            loadMyDirectives: function ($ocLazyLoad) {
                $('.loaderGif').show();
                setTimeout(function () {
                    $('.loaderGif').hide();
                }, 1000);
                return $ocLazyLoad.load({
                    name: 'lgApp',
                    files: [
                        'js/controller/seller/SellerCtrl.js'
                    ]
                });
            }
        }

    })
        .state('message', {
            url: "/message",
            templateUrl: "modules/messages/templates/message.htm",
            controller: "MessageCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/messages/controllers/MessageCtrl.js',
                            'css/message/message.css',
                            'modules/messages/services/messageServices.js',
                        ]
                    });
                }
            }

        })
        .state('getmessagedetails', {
            url: "/getmessagedetails",
            templateUrl: "modules/messages/templates/getmessagedetails.htm",
            controller: "MessageCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/messages/controllers/MessageCtrl.js',
                            'css/message/message.css',
                            'modules/messages/services/messageServices.js',
                            'modules/bluecollar/directives/bluecollar-directives.js'
                        ]
                    });
                }
            }

        })

        //bluecollar routing
        .state('bluecollar-seller-registration', {
            url: "/bluecollar-seller-registration",
            templateUrl: "modules/bluecollar/templates/seller-registration.htm",
            controller: "SellerRegistrationCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/services/SellerSearchServices.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/controllers/SellerRegistrationCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'bootstrap/js/common.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                        ]
                    });
                }
            }
        })
        .state('bluecollar-seller-reg-req-list', {
            url: "/bluecollar-seller-reg-req-list",
            templateUrl: "modules/bluecollar/templates/seller-registration-requests.htm",
            controller: "SellerRegistrationRequestsCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/services/SellerSearchServices.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/controllers/SellerRegistrationRequestsCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js'
                        ]
                    });
                }
            }
        })

        .state('bluecollar-seller-verification-details', {
            url: "/bluecollar-seller-verification-details/:sellerId",
            templateUrl: "modules/bluecollar/templates/seller-verification-details.htm",
            controller: "SellerVerificationCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/services/SellerSearchServices.js',
                            'modules/bluecollar/controllers/SellerVerificationCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js'
                        ]
                    });
                }
            }
        })

        .state('bluecollar-buyer-search', {
            url: "/bluecollar-buyer-search",
            templateUrl: "modules/bluecollar/templates/bluecollar-buyer-search.htm",
            controller: "BuyerSearchCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/services/BuyerSearchServices.js',
                            'modules/bluecollar/controllers/BuyerSearchCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/data.js'
                        ]
                    });
                }
            }
        })

        .state('bluecollar-buyer-search-results', {
            url: "/bluecollar-buyer-search-results",
            templateUrl: "modules/bluecollar/templates/bluecollar-buyer-search-results.htm",
            controller: "BuyerSearchResultsCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/bluecollar/services/BuyerSearchServices.js',
                            'modules/bluecollar/controllers/BuyerSearchResultsCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js'

                        ]
                    });
                }
            }
        })

        .state('bluecollar-buyer-service-book', {
            url: "/bluecollar-buyer-service-book",
            templateUrl: "modules/bluecollar/templates/bluecollar-buyer-service-book.htm",
            controller: "BuyerBookCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/bluecollar/services/BuyerSearchServices.js',
                            'modules/bluecollar/controllers/BuyerBookCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js'

                        ]
                    });
                }
            }
        })

        .state('bluecollar-buyer-post-card', {
            url: "/bluecollar-buyer-post-card",
            templateUrl: "modules/bluecollar/templates/bluecollar-buyer-post-card.htm",
            controller: "BuyerPostCardCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/bluecollar/services/BuyerSearchServices.js',
                            'modules/bluecollar/controllers/BuyerPostCardCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/data.js'
                        ]
                    });
                }
            }
        })

        .state('bluecollar-buyer-post-list', {
            url: "/bluecollar-buyer-post-list",
            templateUrl: "modules/bluecollar/templates/bluecollar-buyer-post-list.htm",
            controller: "BuyerPostListCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/bluecollar/services/BuyerSearchServices.js',
                            'modules/bluecollar/controllers/BuyerPostListCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js'

                        ]
                    });
                }
            }
        })

        .state('profile-edit', {
            url: "/profile-edit",
            templateUrl: "templates/profile/profile-edit.htm",
           // controller: "BuyerPostListCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/bluecollar/services/BuyerSearchServices.js',
                            'modules/bluecollar/controllers/BuyerPostListCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js'

                        ]
                    });
                }
            }
        })


        .state('bluecollar-seller-post-list', {
            url: "/bluecollar-seller-post-list",
            templateUrl: "modules/bluecollar/templates/bluecollar-seller-post-list.htm",
            controller: "SellerPostListCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/bluecollar/services/SellerSearchServices.js',
                            'modules/bluecollar/controllers/SellerPostListCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js'

                        ]
                    });
                }
            }
        })


        .state('bluecollar-seller-rate-card', {
            url: "/bluecollar-seller-rate-card",
            templateUrl: "modules/bluecollar/templates/bluecollar-seller-post-rate-card.htm",
            controller: "SellerPostRateCardCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/js/ui.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/bluecollar/services/BuyerSearchServices.js',
                            'modules/bluecollar/services/SellerSearchServices.js',
                            'modules/bluecollar/controllers/SellerPostRateCardCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/data.js'

                        ]
                    });
                }
            }
        })


        .state('bluecollar-seller-search', {
            url: "/bluecollar-seller-search",
            templateUrl: "modules/bluecollar/templates/bluecollar-seller-search.htm",
            controller: "BlueCollarSellerSearchCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/services/SellerSearchServices.js',
                            'modules/bluecollar/controllers/BlueCollarSellerSearchCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/data.js'
                        ]
                    });
                }
            }
        })


        .state('bluecollar-seller-search-results', {
            url: "/bluecollar-seller-search-results",
            templateUrl: "modules/bluecollar/templates/bluecollar-seller-search-result.htm",
            controller: "BlueCollarSellerSearchResultCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/bluecollar/services/SellerSearchServices.js',
                            'modules/bluecollar/controllers/BlueCollarSellerSearchResultCtrl.js',
                            'css/bluecollar/blue-collar.css',
                            'css/bluecollar/blue-collar-extra.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js'
                        ]
                    });
                }
            }
        })


        .state('hp-seller-search', {
            url: "/hp-seller-search",
            templateUrl: "modules/hyperlocal/seller/templates/hp-seller-search.htm",
            controller: "HpSellerSearch",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/buyer/controllers/HpSellerSearch.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            //'js/validation.js'
                        ]
                    });
                }
            }
        })


        .state('hp-seller-rate-card', {
            url: "/hp-seller-rate-card",
            templateUrl: "modules/hyperlocal/seller/templates/seller-rate-card.htm",
            controller: "HpSellerCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/seller/controllers/HpSellerCtrl.js',
                            'modules/hyperlocal/js/services/apiHyperlocalServices.js',
                            'js/directive/calenderdirective.js',
                            'js/data.js'
                        ]
                    });
                }
            }
        })

        .state('hp-seller-drafts-rate-card', {
            url: "/hp-seller-drafts-rate-card/:id",
            templateUrl: "modules/hyperlocal/seller/templates/seller-rate-drafts-card.htm",
            controller: "HpSellerDraftsCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/seller/controllers/HpSellerDraftsCtrl.js',
                            'modules/hyperlocal/js/services/apiHyperlocalServices.js',
                            'js/directive/calenderdirective.js',
                            'js/data.js'
                        ]
                    });
                }
            }
        })

        .state('hp-seller-list', {
            url: "/hp-seller-list",
            templateUrl: "modules/hyperlocal/seller/templates/seller-list.htm",
            controller: "HpSellerListCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/seller/controllers/HpSellerListCtrl.js',
                            'modules/hyperlocal/js/services/apiHyperlocalServices.js',
                            'modules/hyperlocal/directives/hyperlocal-directives.js',
                            'js/data.js'
                        ]
                    });
                }
            }
        })
        .state('hp-sellerinboundsgrouping/', {
            url: "/hp-sellerinboundsgrouping/:id",
            templateUrl: "modules/hyperlocal/seller/templates/hpsellerinboundsgrouping.htm",
            controller: "HpSellerCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/seller/controllers/HpSellerCtrl.js',
                            'modules/hyperlocal/js/services/apiHyperlocalServices.js',
                            'modules/hyperlocal/directives/hyperlocal-directives.js',
                            'js/data.js'
                        ]
                    });
                }
            }
        })
        .state('hp-buyer-post', {
            url: "/hp-buyer-post",
            templateUrl: "modules/hyperlocal/buyer/templates/buyer-post.htm",
            controller: "BuyerCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/buyer/controllers/BuyerCtrl.js',
                            'js/data.js',
                        ]
                    });
                }
            }
        })
        .state('success', {
            url: "/success",
            templateUrl: "templates/order/thankyou.htm",
            controller: "SuccessCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'js/controller/order/SuccessCtrl.js'
                        ]
                    });
                }
            }

        })
        .state('hp-buyer-list', {
            url: "/hp-buyer-list",
            templateUrl: "modules/hyperlocal/buyer/templates/hp-buyer-list.htm",
            controller: "BuyerListCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/buyer/controllers/BuyerListCtrl.js',
                            'modules/hyperlocal/js/services/apiHyperlocalServices.js',
                        ]
                    });
                }
            }
        })
        .state('hp-buyer-inbound-details', {
            url: "/hp-buyer-inbound-details/:id",
            templateUrl: "modules/hyperlocal/buyer/templates/hp-buyer-inbound-detail.htm",
            controller: "BuyerInboundCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/buyer/controllers/BuyerInboundCtrl.js',
                            'modules/hyperlocal/js/services/apiHyperlocalServices.js',
                        ]
                    });
                }
            }
        })
        .state('hp-buyer-search-list', {
            url: "/hp-buyer-search-list",
            templateUrl: "modules/hyperlocal/buyer/templates/hp-buyer-search-result.htm",
            controller: "buyerSearchResultCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/buyer/controllers/buyerSearchResultCtrl.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'modules/bluecollar/directives/bluecollar-directives.js'
                        ]
                    });
                }
            }
        })

        .state('hp-search-buyer', {
            url: "/hp-search-buyer",
            templateUrl: "modules/hyperlocal/buyer/templates/hp-buyer-search.htm",
            controller: "BuyerSearchCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/buyer/controllers/BuyerSearchCtrl.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                           // 'js/validation.js',
                            'modules/map/GoogleMapService.js',
                            'js/data.js',
                        ]
                    });
                }
            }
        })

        .state('hp-seller-search-results', {
            url: "/hp-seller-search-results",
            templateUrl: "modules/hyperlocal/seller/templates/hp-seller-search-result.htm",
            controller: "HpSellerSearchResult",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/seller/controllers/HpSellerSearchResult.js',
                            'modules/hyperlocal/js/services/apiHyperlocalServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'modules/bluecollar/directives/bluecollar-directives.js'
                        ]
                    });
                }
            }
        })

        .state('hyperlocal-buyer-post-Details', {
            url: "/hyperlocal-buyer-post-Details/:id",
            templateUrl: "modules/hyperlocal/buyer/templates/buyer-post-details.htm",
            controller: "postDetailCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/buyer/controllers/postDetailCtrl.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }

        })

        .state('hyperlocal-lead-post-list', {
            url: "/hyperlocal-lead-post-list/:id",
            templateUrl: "modules/hyperlocal/buyer/templates/buyer-post-lead-details.htm",
            controller: "postDetailLeadCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/buyer/controllers/postDetailLeadCtrl.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }

        })
        
        .state('hyperlocal-seller-post-lead-list', {
            url: "/hyperlocal-seller-post-lead-list/:id",
            templateUrl: "modules/hyperlocal/seller/templates/seller-post-lead-details.htm",
            controller: "postDetailLeadSellerCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/seller/controllers/postDetailLeadSellerCtrl.js',
                            'modules/messages/services/messageServices.js',
                            'modules/bluecollar/directives/bluecollar-directives.js',
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js',
                            'js/data.js'
                        ]
                    });
                }
            }

        })

        .state('hyperlocal-seller-post-details', {
            url: "/hyperlocal-seller-post-details/:id",
            templateUrl: "modules/hyperlocal/seller/templates/seller-post-details.htm",
            controller: "postSellerDetailCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/hyperlocal/seller/controllers/postSellerDetailCtrl.js',
                            'modules/hyperlocal/js/services/apiHyperlocalServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }

        }).state('bluecollar-seller-post-details', {
            url: "/bluecollar-seller-post-details/:id",
            templateUrl: "modules/bluecollar/templates/bluecollar-seller-post-details.htm",
            controller: "BcpostSellerDetailCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/controllers/BcpostSellerDetailCtrl.js',
                            'modules/bluecollar/services/SellerSearchServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }

        }).state('bluecollar-buyer-post-details', {
            url: "/bluecollar-buyer-post-details/:id",
            templateUrl: "modules/bluecollar/templates/buyer-post-details.htm",
            controller: "BcpostSellerDetailCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/controllers/BcpostSellerDetailCtrl.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }

        })
        .state('community', {
            url: "/community-profile",
            templateUrl: "modules/community/templates/community-profile.htm",
            controller: "profileCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/controllers/profileCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'js/directive/appDirective.js',
                            'css/community-profile.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })
        .state('single-profile', {
            url: "/community/profile/:slug",
            templateUrl: "modules/community/profile/templates/profile.htm",
            controller: "CommunityProfileCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/profile/controllers/CommunityProfileCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'js/directive/appDirective.js',
                            'css/community-profile.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })
        .state('recommendation', {
            url: "/recommendation",
            templateUrl: "modules/community/templates/recommendation.htm",
            controller: "RecommendationCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/controllers/RecommendationCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            
                            'css/community-profile.css',
                        ]
                    });
                }
            }
        })
        .state('connection', {
            url: "/connection",
            templateUrl: "modules/community/connection/templates/my-connection.htm",
            controller: "MyConnectionCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/connection/controllers/MyConnectionCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'js/directive/appDirective.js',
                            'css/community-profile.css',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })
        .state('individuals', {
            url: "/individuals",
            templateUrl: "modules/community/members/individual/templates/individual.htm",
            controller: "IndividualCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/members/individual/controllers/IndividualCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'js/directive/appDirective.js',
                            'css/community-profile.css',
                        ]
                    });
                }
            }
        })
        .state('individuals-invitation', {
            url: "/individuals-invitation",
            templateUrl: "modules/community/members/individual/templates/individuals-invitation.htm",
            controller: "IndividualInvitationCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/members/individual/controllers/IndividualInvitationCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'js/directive/appDirective.js',
                            'css/community-profile.css',
                            'css/chosen.css'
                        ]
                    });
                }
            }
        })  
        
        .state('free-event-list', {
            url: "/free-event-list",
            templateUrl: "modules/community/templates/events-listing-free.htm",
            controller: "eventfreeCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/controllers/eventfreeCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })
        .state('create-article', {
            url: "/create-article",
            templateUrl: "modules/community/articles/templates/create-article.htm",
            controller: "articleCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/articles/controllers/articleCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js',
                            'css/community-profile.css'
                        ]
                    });
                }
            }

        })
        .state('create-event', {
            url: "/create-event",
            templateUrl: "modules/community/events/templates/create-event.htm",
            controller: "EventCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/events/controllers/EventCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/directive/calenderdirective.js',
                            'css/community-profile.css',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })

        .state('edit-article', {
            url: "/edit-article/:id",
            templateUrl: "modules/community/articles/templates/create-article.htm",
            controller: "articleCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/articles/controllers/articleCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js',
                            'css/community-profile.css'
                        ]
                    });
                }
            }

        })

        .state('article-list', {
            url: "/article-list/:type",
            templateUrl: "modules/community/articles/templates/article-list.htm",
            controller: "articlelistCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/articles/controllers/articlelistCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'modules/community/js/services/apiShareServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'css/community-profile.css',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })        
        .state('event-list', {
            url: "/event-list/:type",
            templateUrl: "modules/community/events/templates/event-list.htm",
            controller: "EventListCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/events/controllers/EventListCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'modules/community/js/services/apiShareServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'css/community-profile.css',
                            'js/validation.js'
                        ]
                    });
                }
            }
        }) 
        .state('event-profile', {
            url: "/event-profile/:id",
            templateUrl: "modules/community/events/templates/event-profile.htm",
            controller: "EventProfileCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/events/controllers/EventProfileCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/directive/calenderdirective.js',
                            'css/community-profile.css',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })
        .state('event-register', {
            url: "/event-register/:title",
            templateUrl: "modules/community/events/templates/register.html",
            controller: "EventListCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/events/controllers/EventListCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'css/community-profile.css',
                            'js/validation.js'
                        ]
                    });
                }
            }
        }) 

        .state('artical-view', {
            url: "/artical-view/:id",
            templateUrl: "modules/community/templates/article-view.htm",
            controller: "articleviewCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/controllers/articleviewCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }

        })
        .state('business', {
            url: "/business",
            templateUrl: "modules/community/members/business/templates/business.htm",
            controller: "BusinessCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/members/business/controllers/BusinessCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })
        .state('businesspartnership-invitation', {
            url: "/businesspartnership-invitation",
            templateUrl: "modules/community/members/business/templates/businesspartnership-invitation.htm",
            controller: "BusinessCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/members/business/controllers/BusinessCtrl.js',
                            'modules/community/js/services/apiCommunityServices.js',
                            'js/directive/appDirective.js',
                            'css/community-profile.css',
                        ]
                    });
                }
            }
        }) 
        .state('groups', {
            url: "/groups",
            templateUrl: "modules/community/members/groups/templates/groups.htm",
            controller: "GroupsCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/members/groups/controllers/GroupsCtrl.js',
                            'css/community-profile.css'
                        ]
                    });
                }
            }
        })
        .state('new-groups', {
            url: "/create-new-group",
            templateUrl: "modules/community/members/groups/templates/create-new-group.htm",
            controller: "CreateNewGroupCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/members/groups/controllers/CreateNewGroupCtrl.js',
                            'css/community-profile.css',
                            'css/chosen.css'
                        ]
                    });
                }
            }
        })
        .state('community-services-listing', {
            url: "/community-services-listing",
            templateUrl: "modules/community/services/templates/serviceList.html",
            controller: "serviceCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/services/controllers/serviceCtrl.js',
                            'css/community-profile.css'
                        ]
                    });
                }
            }
        })
        .state('community-jobs', {
            url: "/community-jobs/:type",
            templateUrl: "modules/community/jobs/templates/jobs.htm",
            controller: "JobsCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/bluecollar/filters/bluecollar-filters.js',
                            'modules/community/jobs/controllers/JobsCtrl.js',
                            'modules/community/js/services/apiShareServices.js',
                            'css/community-profile.css'
                        ]
                    });
                }
            }
        })
        .state('contract-jobs', {
            url: "/contract-jobs",
            templateUrl: "modules/community/jobs/contract/templates/contract-jobs.htm",
            controller: "ContractJobsCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/jobs/contract/controllers/ContractJobsCtrl.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }
        })
        .state('individual-invitation', {
            url: "/individual-invitation",
            templateUrl: "modules/community/templates/individual-invitation.htm",
            controller: "eventfreeCtrl",
            resolve: {
                loadMyDirectives: function ($ocLazyLoad) {
                    $('.loaderGif').show();
                    setTimeout(function () {
                        $('.loaderGif').hide();
                    }, 1000);
                    return $ocLazyLoad.load({
                        name: 'lgApp',
                        files: [
                            'modules/community/controllers/eventfreeCtrl.js',
                            'css/dcalendar.picker.css',
                            'js/dcalendar.picker.js',
                            'js/validation.js'
                        ]
                    });
                }
            }

        })        

});
