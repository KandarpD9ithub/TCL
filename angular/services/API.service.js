export class APIService {
	constructor(Restangular, ToastService, $window, $state) {
		'ngInject';
		//content negotiation
		let headers = {
			'Accept': "application/json, text/plain, */*"
		};

		return Restangular.withConfig(function(RestangularConfigurer) {
			RestangularConfigurer
				.setBaseUrl('/api/')
				.setDefaultHeaders(headers)
				.setErrorInterceptor(function(response) {
					if (response.status === 422) {
						for (let error in response.data.errors) {
							return ToastService.error(response.data.errors[error][0]);
						}
					}
                    if (response.status === 500) {
                      return ToastService.error(response.statusText)
                    }
					if(response.status === 401) {
						ToastService.error('Unauthorized');
						$state.go('app.landing');

					}
				})
				.addFullRequestInterceptor(function(element, operation, what, url, headers) {
					let token = $window.localStorage.getItem('access_token');
					headers.Authorization = 'Basic ' + token;
				});
		});
	}
}
