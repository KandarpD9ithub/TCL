export class OrderEditController{
    constructor(DialogService, $log, locals, ToastService){
        'ngInject';

        this.DialogService = DialogService;
        this.$log = $log;
        this.locals = locals;
        this.ToastService = ToastService;
    }

    save(){
        var data = {
            reason: this.reason,
            password: this.password,
            quantity: this.quantity
        };
        this.DialogService.hide(data);
    }

    cancel(){
        this.DialogService.cancel();
    }
}

