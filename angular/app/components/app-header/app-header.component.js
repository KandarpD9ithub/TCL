class AppHeaderController{
        constructor($auth, $log, API, ToastService, Restangular, $window, $state, indexeddb, Offline, $location){
        'ngInject';
        this.$auth = $auth;
        this.$log = $log;
        this.API = API;
        this.ToastService = ToastService;
        this.api = Restangular;
        this.show = false;
        this.window = $window;
        this.location = $location;
        this.state = $state;
        this.indexeddb = indexeddb;
        this.rcm = 'no record found';
        this.Offline = Offline;
    }
    $onInit(){
        setInterval(() => {
            this.show = !!this.window.localStorage.getItem('access_token');
        }, 1000);
        /*this.youAreOnline = !!(this.Offline.state && this.Offline.state === 'up');
        this.Offline.on('confirmed-up', () => {
            this.ToastService.show('You are running in online mode');
            this.youAreOnline = true;
        });
        this.Offline.on('confirmed-down', () => {
            this.youAreOnline = false;
        });*/
        this.roleName = this.window.localStorage.getItem('role_name');
        this.userName = this.window.localStorage.getItem('user_name');
        this.$log.info(this.userName);
    }

    loggedOut() {
        this.$log.info(this.show);
        this.show = false;
        this.window.localStorage.removeItem('access_token');
        this.window.localStorage.removeItem('role_name');
        this.window.localStorage.removeItem('user_name');
        this.state.go('app.landing', {}, {reload: true});

    }
    isActive(viewLocation) {
     var active = (viewLocation === this.state.current.name);
        this.$log.info(active);
          return active;
    }
}
export const AppHeaderComponent = {
    templateUrl: './views/app/components/app-header/app-header.component.html',
    controller: AppHeaderController,
    controllerAs: 'vm',
    bindings: {}
}