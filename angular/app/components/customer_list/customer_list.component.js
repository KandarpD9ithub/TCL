class CustomerListController{
    constructor(API, ToastService, $log, $state){
        'ngInject';
        this.$state = $state;
        this.log = $log;
        this.API = API;
        this.ToastService = ToastService;
    }

    $onInit(){
            this.API.all('customer').get('').then((response) => {
                this.customers = response.data.customers;
        });
    }

    customerOrders(id){
        this.$state.go('app.customer_order', {customer_id:id});
    }
}

export const CustomerListComponent = {
    templateUrl: './views/app/components/customer_list/customer_list.component.html',
    controller: CustomerListController,
    controllerAs: 'vm',
    bindings: {}
}
