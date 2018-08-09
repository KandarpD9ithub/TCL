export function RoutesConfig($stateProvider, $urlRouterProvider) {
	'ngInject';

	let getView = (viewName) => {
		return `./views/app/pages/${viewName}/${viewName}.page.html`;
	};

	$urlRouterProvider.otherwise('/');

    /*
        data: {auth: true} would require JWT auth
        However you can't apply it to the abstract state
        or landing state because you'll enter a redirect loop
    */

	$stateProvider
		.state('app', {
			abstract: true,
            data: {},
			views: {
				header: {
					templateUrl: getView('header')
				},
				footer: {
					templateUrl: getView('footer')
				},
				main: {}
			}
		})
        .state('app.landing', {
            url: '/',
            views: {
                'main@': {
                    templateUrl: getView('login')
                }
            }
        })
       //.state('app.login-form', {
		//	url: '/login',
		//	views: {
		//		'main@': {
		//			templateUrl: getView('login-form')
		//		}
		//	}
		//})
        /*.state('app.register', {
            url: '/register',
            views: {
                'main@': {
                    templateUrl: getView('register')
                }
            }
        })
        .state('app.forgot_password', {
            url: '/forgot-password',
            views: {
                'main@': {
                    templateUrl: getView('forgot-password')
                }
            }
        })
        .state('app.reset_password', {
            url: '/reset-password/:email/:token',
            views: {
                'main@': {
                    templateUrl: getView('reset-password')
                }
            }
        })*/
        .state('app.create_customer', {
            url: '/create-customer',
            views: {
                'main@': {
                    templateUrl: getView('create_customer')
                }
            }
        }).state('app.place_order', {
            url: '/place-order',
            views: {
                'main@': {
                    templateUrl: getView('place-order')
                }
            }
        })
        .state('app.invoice', {
            url: '/invoice/{order_id}',
            views: {
                'main@': {
                    templateUrl: getView('invoice')
                }
            }
        })
        .state('app.order_list', {
            url: '/order-list',
            views: {
                'main@': {
                    templateUrl: getView('order_list')
                }
            }
        })
        .state('app.customer_list', {
            url: '/customer',
            views: {
                'main@': {
                    templateUrl: getView('customer_list')
                }
            }
        })
        .state('app.customer_order', {
            url: '/customer/{customer_id}/orders',
            views: {
                'main@': {
                    templateUrl: getView('customer_order')
                }
            }
        })
        .state('app.edit_order', {
            url: '/edit/order/{order_id}',
            views: {
                'main@': {
                    templateUrl: getView('edit-order')
                }
            }
        });
}
