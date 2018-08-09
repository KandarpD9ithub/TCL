class InvoiceController{
    constructor(API, $stateParams, $log){
        'ngInject';
        this.$log = $log;
        this.API = API;
        this.stateParams = $stateParams;
    }

    $onInit(){
        this.API.all('customer/order/'+this.stateParams.order_id).get('').then((response) => {
            this.$log.info(response);
            this.orders = response.data.orders;
    });
    }

    print() {
    window.print();
}
}

export const InvoiceComponent = {
    templateUrl: './views/app/components/invoice/invoice.component.html',
    controller: InvoiceController,
    controllerAs: 'vm',
    bindings: {}
}
