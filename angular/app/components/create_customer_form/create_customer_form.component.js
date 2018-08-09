class CreateCustomerFormController{
    constructor(API, ToastService, $state){
        'ngInject';

        this.API = API;
        this.ToastService = ToastService;
        this.$state = $state;
        this.customers = [];
    }

    $onInit(){
        this.API.all('customer').get('').then((response) => {
            var customersArray = [];
        for (var i=0; i< response.data.customers.length; i++) {
            customersArray.push({
                value: response.data.customers[i].id,
                display: response.data.customers[i].name,
                contact_number: response.data.customers[i].contact_number,
                email: response.data.customers[i].email,
            });
        }
        this.customers = customersArray;
    });
    }
    submit(){
    for (var i=0; i< this.customers.length; i++) {
        if(this.customers[i].contact_number.indexOf(this.contact_number) !== -1) {
            return this.ToastService.error("Mobile number already taken");
        }
    }
        /*for (var j=0; j< this.customers.length; j++) {
            if(this.customers[j].email.indexOf(this.email) !== -1) {
                return this.ToastService.error("Email already taken");
            }
        }*/
        if(!/^[a-zA-Z\s]*$/.test(this.name)) {
            return this.ToastService.error('Name must be letters only.');
        }
    if(!/^[0-9]{10}$/.test(this.contact_number)) {
        return this.ToastService.error('Mobile Number must be in 10 digits');
    }

        var data = {
            name: this.name,
            contact_number: this.contact_number,
            email: this.email
        };

        this.API.all('customer').post(data).then((response) => {
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
            } }
            else {
                this.ToastService.show("Customer account created");
            }
        this.contact_number = '';
        this.$state.go('app.customer_list')
        });
    }
}

export const CreateCustomerFormComponent = {
    templateUrl: './views/app/components/create_customer_form/create_customer_form.component.html',
    controller: CreateCustomerFormController,
    controllerAs: 'vm',
    bindings: {}
}
