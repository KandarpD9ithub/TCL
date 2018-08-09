export class DeleteController{
    constructor(API, DialogService, locals, ToastService){
        'ngInject';

        this.DialogService = DialogService;
        this.API = API;
        this.locals = locals;
        this.ToastService = ToastService;
    }

    save(){
        var data = {
            password: this.password,
            reason: this.reason
        };
        this.API.all('customer/order/cancel/'+this.locals.order_id).post(data).then((response) => {
            if (response.status_code === 500) {
                this.ToastService.error(response.message);
            } else {
                this.ToastService.show(response.data.message);
                location.reload();
            }
            
        }, (error) => {
            this.ToastService.error(error);
        });
        this.DialogService.hide();
}

    cancel(){
        this.DialogService.cancel();
    }
}

