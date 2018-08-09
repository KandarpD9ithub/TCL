class LoginFormController {
	constructor($state, ToastService, $http, $window, $log) {
		'ngInject';
		this.state = $state;
		this.ToastService = ToastService;
		this.http = $http;
		this.$window = $window;
		this.$log = $log;
		this.$accessToken = this.$window.localStorage.getItem('access_token');
		this.$roleName = this.$window.localStorage.getItem('role_name')
	}

    $onInit(){
        this.email = '';
        this.password = '';
		if (this.$accessToken) {
			if (this.$roleName === '5') {
				this.state.go('app.order_list' , {}, {reload: true});
			} else {
				this.state.go('app.place_order', {}, {reload: true});
			}
		}

    }

	login() {
		let token = btoa(`${this.email}:${this.password}`);
		this.http.defaults.headers.common['Authorization'] = `Basic ${token}`;
		this.http.get('/api/profile').then((response) => {
			this.$window.localStorage.setItem('access_token', token);
			this.$window.localStorage.setItem('role_name', response.data.data.user[0].role_name);
			this.$window.localStorage.setItem('user_name', response.data.data.user[0].name);
			if (response.data.data.user[0].role_name === 5) {
				this.state.go('app.order_list', {}, {reload: true});
			} else {
				this.state.go('app.place_order', {}, {reload: true});
			}
		}).catch(this.failedLogin.bind(this));

	}

	failedLogin() {
		return this.ToastService.error('Username or password is invalid');
	}
}

export const LoginFormComponent = {
	templateUrl: './views/app/components/login-form/login-form.component.html',
	controller: LoginFormController,
	controllerAs: 'vm',
	bindings: {}
}
