export class InvoiceController{
    constructor(DialogService, API, locals, ToastService){
        'ngInject';

        this.DialogService = DialogService;
        this.API = API;
        this.locals = locals;
        this.ToastService = ToastService;
    }

    save(){
        //var data = {
        //    payment: this.payment
        //};
        //this.API.all('order/'+this.locals.orders.id+'/payment-method').post(data).then(() => {
        //    this.ToastService.show("Payment Selected");
        //});
    }

    cancel(){
        this.DialogService.cancel();
    }
    print(printSectionId) {
        var innerContents = document.getElementById(printSectionId).innerHTML;
        var popupWinindow = window.open('', '_blank', 'width=600,height=700,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no');
        popupWinindow.document.open();
        popupWinindow.document.write('<html><head><link rel="stylesheet" type="text/css" href="/css/print.css" /></head><body onload="window.print(); window.close(); this.DialogService.hide();">' + innerContents + '</html>');
        popupWinindow.document.close();
        this.DialogService.hide();
    }
}

