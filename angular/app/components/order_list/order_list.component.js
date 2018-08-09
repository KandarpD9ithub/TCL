import {DeleteController} from '../../../dialogs/delete/delete.dialog.js';
class OrderListController{
    constructor(API, ToastService, DialogService, $state, $log, $window){
        'ngInject';

        this.log = $log;
        this.API = API;
        this.ToastService = ToastService;
        this.DialogService = DialogService;
        this.window = $window;
        this.$state = $state;
        this.autorefresh = true;
    }

    $onInit(){
        this.autorefresh = true;
        this.log.info(this.$state.current.name);
        if (this.$state.current.name === 'app.order_list') {
             if (this.autorefresh === true) {
             this.timer = setInterval(() => {
                this.home();

            }, 10000);
            }
        } else {
             if (angular.isDefined(this.timer)) {
                    this.log.info('ksjfhjh');
                    clearInterval(this.timer);
                }
                this.timer=undefined;
        }
        this.home();
        this.roleName = this.window.localStorage.getItem('role_name');
        //for( var i =0; i< response.data.orders.length; i++) {
        //    this.given_time = response.data.orders[i].created_at;
        //    this.orders[i].enabled=this.checkTime(this.given_time);
        //    this.log.info(this.orders[i].enabled)
        //}

    }
    // auto refresh on/off
    killtimer(changeState) {
        if (this.$state.current.name === 'app.order_list') {
             if (changeState === true) {
            this.timer = setInterval(() => {
                this.home();

            }, 10000);
            } else {
                if (angular.isDefined(this.timer)) {
                    this.log.info('ksjfhjh');
                    clearInterval(this.timer);
                }
                this.timer=undefined;
                
            }
        }
       
    }

    $onDestroy() {
        if(this.timer)
            clearInterval(this.timer);   
    }
    // manual refresh 
    refresh() {
        this.home();
    }
    home() {
        this.API.all('getTables').get('').then((response) => {
            this.tables= response.data.tables;
        });
        this.API.all('orders').get('').then((response) => {
            this.orders = response.data.orders;
    });
    }
    //checkTime(given_time) {
    //
    //    var current_time = new Date();
    //    var t = given_time.split(/[- :]/);
    //    var orderTime = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
    //    var minutes = ((current_time - orderTime) / (60 * 1000))%60;
    //     return (minutes < 60)
    //}



    statusChange(id){
    this.API.all('order/'+id+'/status').post('').then(() => {
        this.ToastService.show("Created");
    });
    location.reload()
    }

    deleteOrder(id){
        let options = {
            controller: DeleteController,
            controllerAs: 'vm',
            locals: {
                order_id : id,
            }
        };
        this.DialogService.fromTemplate('delete', options)
    }

    editOrder(id){
        this.$state.go('app.edit_order', {order_id: id})
    }

    toTimeStamp (date) {
        if (date == null) { return ""; }
    var _timestamp = new Date(date+" UTC").getTime();
    return _timestamp;
  }
}

export const OrderListComponent = {
    templateUrl: './views/app/components/order_list/order_list.component.html',
    controller: OrderListController,
    controllerAs: 'vm',
    bindings: {}
}
