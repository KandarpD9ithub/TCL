export class CreateCustomerController{
    constructor(DialogService, API, ToastService, $log, locals, Offline){
        'ngInject';
        this.API = API;
        this.ToastService = ToastService;
        this.DialogService = DialogService;
        //this.customers = [];
        this.locals = locals;
        this.$log = $log;
        this.Offline = Offline;
        this.youAreOnline = !!(this.Offline.state && this.Offline.state === 'up');
        this.Offline.on('confirmed-up', () => {
            this.youAreOnline = true;
        });
        this.Offline.on('confirmed-down', () => {
            this.youAreOnline = false;
        });
    }

    save(){
    /*    this.API.all('customer').get('').then((response) => {
        this.customers = response.data.customers;
    });*/
    for (var i=0; i< this.locals.customers.length; i++) {
        if(this.locals.customers[i].contact_number.indexOf(this.contact_number) !== -1) {
            return this.ToastService.error("Mobile number already taken");
    }
    }
        /*for (var j=0; j< this.locals.customers.length; j++) {
            if(this.locals.customers[j].email.indexOf(this.email) !== -1) {
                return this.ToastService.error("Email already taken");
            }
        }*/
    if(!/^[0-9]{10}$/.test(this.contact_number)) {
        return this.ToastService.error('Mobile Number must be in 10 digits');
    }
        if(!/^[a-zA-Z\s]*$/.test(this.name)) {
            return this.ToastService.error('Name must be letters only.');
        }
        var customerData = {
            name: this.name,
            contact_number: this.contact_number,
            email: this.email
        };
        if(!this.youAreOnline){
            this.DialogService.hide(customerData);
        } else {
            this.API.all('customer').post(customerData).then((response) => {
                if (response.message) {
                    if (response.message.email) {
                        for (var j =0; j<response.message.email.length; j++) {
                            return this.ToastService.error(response.message.email[j]);
                        }
                        
                    } else if (response.message.contact_number) {
                        for (var l =0; l<response.message.contact_number.length; l++) {
                            return this.ToastService.error(response.message.contact_number[l]);
                        }
                        
                    } else if (response.message.name) {
                        for (var k =0; k<response.message.name.length; k++) {
                             return this.ToastService.error(response.message.name[k]);
                        }
                    } 
                } else {
                    this.ToastService.show("Customer account created");
                }
                this.DialogService.hide(response.data.customer);
                
            });
        }
    }

    cancel(){
        this.DialogService.cancel();
    }
}

