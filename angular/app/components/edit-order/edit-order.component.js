import {OrderEditController} from '../../../dialogs/orderEdit/orderEdit.dialog.js';
import {InvoiceController} from '../../../dialogs/invoice/invoice.dialog.js';
class EditOrderController{
    constructor(API, ToastService, DialogService, $timeout, $q, $log, $mdDialog, $state, $stateParams){
        'ngInject';

        this.API = API;
        this.ToastService = ToastService;
        this.order = [];
        this.total = '';
        this.DialogService = DialogService;
        this.$timeout = $timeout;
        this.$q = $q;
        this.$log = $log;
        this.customers = [];
        this.customer_id = '';
        this.products = [];
        this.taxes = [];
        this.$mdDialog = $mdDialog;
        this.$state = $state;
        this.stateParams = $stateParams;
        this.taxAndDiscount = [];
        this.isDisabled = false;
    }

    /**
     * Search for states... use $timeout to simulate
     * remote dataservice call.
     */
    querySearch (query) {
        var results = query ? this.customers.filter((customer) => {
            return customer.display.indexOf(query) === 0;
    }) : this.customers,
    deferred;
    if (this.simulateQuery) {
    deferred = this.$q.defer();
        this.$timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
        return deferred.promise;
} else {
    return results;
}
}

searchTextChange(text) {
    this.$log.info('Text changed to ' + text);
}

selectedCustomerChange(customer) {
    this.customer_id = customer.value;
    this.customer_name = this.order.customer_name;
}
/**
 * Build `states` list of key/value pairs
 */
loadAll() {
    this.API.all('customer').get('').then((response) => {
        var customersArray = [];
    for (var i=0; i< response.data.customers.length; i++) {
        customersArray.push({
            value: response.data.customers[i].id,
            display: response.data.customers[i].name
        });
    }
    this.customers = customersArray;
});
}

/**
 * Create filter function for a query string
 */
itemSearch (query) {
    var results = query ? this.products.filter((product) => {
        query = angular.lowercase(query);
        return product.search.indexOf(query) === 0 || product.product_code.indexOf(query) === 0;
}) : this.products,
    deferred;
if (this.simulateQuery) {
    deferred = this.$q.defer();
    this.$timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
    return deferred.promise;
} else {
    return results;
}
}

allProducts() {
    this.API.all('products').get('').then((response) => {
        var productsArray = [];
    for (var i=0; i< response.data.products.length; i++) {
        productsArray.push({
            value: response.data.products[i].id,
            id: response.data.products[i].id,
            name: response.data.products[i].name,
            price: response.data.products[i].price,
            base_price: response.data.products[i].base_price,
            product_code: response.data.products[i].product_code,
            tax_rate: response.data.products[i].tax_rate,
            tax_name: response.data.products[i].tax_name,
            tags: response.data.products[i].tags,
            search: response.data.products[i].name.toLowerCase()
        });
    }
    this.products = productsArray;
});
}

selectedItemChange(item) {
    if(item != null){
        this.addItems(item)
    }
    this.selectedItem = null;
    this.searchTextItem = null;
}
searchItemTextChange(text) {
    this.$log.info('Text changed to ' + text);
}
$onInit(){
    this.API.all('popularItems').get('').then((response) => {
        this.popularItems = response.data.popularItems;
    });

    this.API.all('customer').get('').then((response) => {
        this.customers = response.data.customers;
    });
    this.API.all('offers').get('').then((response) => {
    this.offers = response.data.offers;
});
this.API.all('discount').get('').then((response) => {
    this.discountAmounts = response.data.discounts;
});
 this.API.all('all/taxes').get('').then((response) => {
        this.allTaxes = response.data.allTaxes;
        this.serviceTax = response.data.service_tax;
        this.taxType = response.data.tax_type;
        this.allTaxType = response.data.all_tax_type;
    });
 this.API.all('nc/list').get('').then((response) => {
            this.ncList= response.data.ncPeoples;
        });
 this.API.all('getTables').get('').then((response) => {
            this.tables= response.data.tables;
        })
    this.loadAll();
    this.allProducts();
    this.API.all('customer/order/'+this.stateParams.order_id).get('').then((response) => {
        this.orders = response.data.orders;
        var ordersArray = [];
        for (var i=0; i< response.data.orders.order.length; i++) {
            ordersArray.push({
                value: response.data.orders.order[i].product_id,
                id: response.data.orders.order[i].product_id,
                offer: response.data.orders.order[i].offer,
                discount: response.data.orders.order[i].discount,
                name: response.data.orders.order[i].name,
                price: (response.data.orders.order[i].quantity * response.data.orders.order[i].price),
                quantity: parseFloat(response.data.orders.order[i].quantity),
                base_price: response.data.orders.order[i].price,
                tax_rate: response.data.orders.order[i].tax_rate,
                tax_name: response.data.orders.order[i].tax_name,
                tags: response.data.orders.order[i].tags,
            });
        }
        this.order = ordersArray;
        this.taxAndDiscount = response.data.orders;
        this.payment = response.data.orders.payment;
         if (this.orders.non_chargeable_people_id) {
            this.comment = this.orders.comment;
            this.ncselected = this.orders.non_chargeable_people_id;
            this.nc = '1'
            this.selectedIndex = 3;  
            this.ncDiscount()
        } else {
            this.getPrices();
        }
        if (this.payment === 1) {
            this.selectedIndex = 0;
            this.payment = '1'
        } else if (this.payment === 2) {
            this.selectedIndex = 1; 
            this.payment = '2'   
        } else if (this.payment === 3) {
             this.selectedIndex = 2;
             this.payment = '3'   
        } else {
           this.selectedIndex = 3; 
        }
    }); 

    this.API.all('menu-popular-products').get('').then((response) => {
        var categoriesArray = [];
        var subCategoriesArray = [];
        var categoryProducts = [];
        /*var subCategoryProducts = [];*/
        for (var i=0; i< response.data.menuItems.length; i++) {
            categoriesArray.push({
                category_name: response.data.menuItems[i].category_name,
                products: response.data.menuItems[i].products,
                tax_rate: response.data.menuItems[i].tax_rate,
                child: response.data.menuItems[i].child,
            });
            for (var j=0; j< response.data.menuItems[i].child.length; j++) {
                subCategoriesArray.push({
                    sub_category_name: response.data.menuItems[i].child[j].sub_category_name,
                    tax_rate: response.data.menuItems[i].child[j].tax_rate,
                    products: response.data.menuItems[i].child[j].products
                });
                for (var l=0; l< response.data.menuItems[i].child[j].products.length; l++) {
                    categoryProducts.push({
                        id: response.data.menuItems[i].child[j].products[l].id,
                        name: response.data.menuItems[i].child[j].products[l].name,
                        price: response.data.menuItems[i].child[j].products[l].price,
                        product_code: response.data.menuItems[i].child[j].products[l].product_code,
                        base_price: response.data.menuItems[i].child[j].products[l].base_price,
                        tax_rate: response.data.menuItems[i].child[j].products[l].tax_rate,
                        tax_name: response.data.menuItems[i].child[j].products[l].tax_name,
                        tags: response.data.menuItems[i].child[j].products[l].tags,
                    });
                }
            }
            for (var k=0; k< response.data.menuItems[i].products.length; k++) {
                categoryProducts.push({
                    id: response.data.menuItems[i].products[k].id,
                    name: response.data.menuItems[i].products[k].name,
                    price: response.data.menuItems[i].products[k].price,
                    product_code: response.data.menuItems[i].products[k].product_code,
                    base_price: response.data.menuItems[i].products[k].base_price,
                    tax_rate: response.data.menuItems[i].products[k].tax_rate,
                    tax_name: response.data.menuItems[i].products[k].tax_name,
                    tags: response.data.menuItems[i].products[k].tags,
                });
            }
        }
        this.categories = categoriesArray;
        this.subCategories = subCategoriesArray;
        this.categoryProducts = categoryProducts;
    });

this.options = {
     visible: 10,
    perspective: 10,
    startSlide: 0,
    border: 0,
    dir: 'ltr',
    width: 110,
    height: 64,
    space: 170,
    loop: false,
    controls: true}
this.API.all('franchise/detail').get('').then((response) => {
    this.franchiseDetail = response.data.franchiseDetail;
});
this.API.all('menu').get('').then((response) => {
    this.menu = response.data.menus;
});
this.API.all('date/time').get('').then((response) => {
            this.dateTime = response.data.dateTime;
            this.initializeClock();
        });
}
initializeClock() {
    this.timeinterval = setInterval(() => {
            this.dateTime++;
}, 1000);
}

addItems(item){
    item.new = true;
    var products = this.order.map(product => product.id);
    if(products.indexOf(item.id) < 0){
        item.quantity = 1;
        item.price = parseFloat(item.quantity * item.base_price).toFixed(2);
        this.order.push(item);
    } else {
        item.quantity ++;
        item.price = parseFloat(item.quantity * item.base_price).toFixed(2);
    }
    this.getPrices();
}

    getPrices(){
        /* var offer = 0;
         var totalDiscount = 0
         //var totalDiscount = this.getDiscount()+discount;
         if(this.getOffer()){
         offer = this.getOffer();
         totalDiscount = 0
         } else {
         offer = 0;
         totalDiscount = this.discounts ? this.discounts : this.getDiscount();
         }
         var total = this.getSum()[0].amount-totalDiscount-offer;*/
        /*var i = 0, tax = 0;
         for(; i < this.order.length; i++) {
         tax += this.order[i].tax_rate ? parseFloat(Math.round(this.getSum()[0].dsum * this.order[i].tax_rate) / 100): 0;
         }*/
        /*var sgst = this.getTaxes()/2;
         var cgst = this.getTaxes()/2;*/
        /* var serviceCharge = this.allTaxes.Service_Charge ? parseFloat(Math.round(total * this.allTaxes.Service_Charge) / 100): 0;
         var subTotal = parseFloat(total+serviceCharge);
         var vat = this.allTaxes.VAT ? parseFloat(Math.round(subTotal * this.allTaxes.VAT) / 100) : 0;*/
        /*var serviceTaxLevied = parseFloat(Math.round(subTotal * 40) / 100);*/
        /* var serviceTax = this.allTaxes.Service_Tax ? parseFloat(Math.round(subTotal * this.allTaxes.Service_Tax) / 100) : 0;
         var billAmount = Math.round(total+serviceTax+serviceCharge+vat);*/
        /*var billAmount = Math.round(total+this.getSum()[0].taxAmount);*/
        var taxes = {};
        taxes = {
            subtotal: this.getSum()[0].amount,
            offer: this.getSum()[0].offerAmount,
            discount: this.getSum()[0].discountAmount,
            taxes: this.getSum()[0].taxes,
            totalTax: this.getSum()[0].taxAmount,
            serviceTaxAmount:this.getSum()[0].serviceTaxAmount,
            grand_total: this.getSum()[0].grand_total,
        };
        this.taxAndDiscount = taxes;
        this.$log.info(this.taxAndDiscount)
    }
change(index) {
    if(!/^[0-9]*$/.test(this.order[index].quantity)) {
            this.isDisabled = true;
            this.order[index].price = 0;
            this.getPrices();
            return this.ToastService.error('Quantity must be integer value');
        }
        if (this.order[index].quantity > 0) {
            if (parseInt(this.order[index].quantity) >= 301) {
                this.order[index].quantity = 300;
                this.getPrices();
                return this.ToastService.error('Quantity must be less then or equal to 300');
                
            } else {
                this.isDisabled = false;
                this.order[index].price = parseFloat(this.order[index].quantity * this.order[index].base_price).toFixed(2);
            }
            
        } else {
            this.order[index].price = 0;
        }
    this.getPrices();
}
    getSum(){
        var i = 0,
            sum = 0,
            dsum = 0,
            taxAmount = 0,
            taxName = '',
            taxRate = 0;
        var offer = 0;
        var totalDiscount = 0, offerAmount = 0, discountAmount = 0, billAmount = 0;
        var  priceArray = [];
        var taxArray={};
        var serviceTaxArray = 0;
        var serviceTaxRate = 0;
        for(; i < this.order.length; i++) {
           if(this.getOffer(this.order[i])){
                offer = this.getOffer(this.order[i]);
                this.$log.info(offer);
                offerAmount += offer;
                totalDiscount = 0
            } else {
                offer = 0;
                if (this.discounts) {
                    totalDiscount = this.discounts
                    discountAmount = totalDiscount;

                } else {
                    totalDiscount = this.getDiscount(this.order[i]);
                    discountAmount += totalDiscount;
                }
            }
            sum += parseFloat((this.order[i].price));
            if (this.serviceTax) {
                serviceTaxRate = parseFloat(((this.order[i].price-totalDiscount-offer) * this.serviceTax.tax_rate) / 100);
                serviceTaxArray += parseFloat((serviceTaxRate));
            }
            dsum += (this.order[i].price-totalDiscount-offer)+serviceTaxRate;
            taxAmount += this.order[i].tax_rate ? parseFloat((((this.order[i].price-totalDiscount-offer)+serviceTaxRate) * this.order[i].tax_rate) / 100): 0;
            taxName = this.order[i].tax_name;
            /* taxRate = this.order[i].tax_rate;*/
            taxRate = this.order[i].tax_rate ? parseFloat((((this.order[i].price-totalDiscount-offer)+serviceTaxRate) * this.order[i].tax_rate) / 100).toFixed(2): 0;
            if (taxArray[taxName]) {
                taxArray[taxName] += parseFloat(this.trimDecimal(taxRate, 2));
            } else {
                taxArray[taxName] = parseFloat(taxRate);
            }
            /*if (taxesArray[taxType]) {
             taxesArray[taxType] = allTaxesArray.push(taxArray);
             } else {
             taxesArray[taxType] = taxArray;
             }*/
            billAmount += parseFloat(((this.order[i].price-totalDiscount-offer)+serviceTaxRate)+ (this.order[i].tax_rate ? parseFloat((((this.order[i].price-totalDiscount-offer)+serviceTaxRate) * this.order[i].tax_rate) / 100): 0));
        }
        var bill = 0;
        if (this.discounts) {
            bill = parseFloat(((sum-discountAmount-offer)+serviceTaxArray)+taxRate)
        } else {
            bill = billAmount;
        }
        this.$log.info(taxArray);
        priceArray.push({
            amount:parseFloat(sum).toFixed(2),
            dsum:dsum,
            taxes:taxArray,
            taxAmount:parseFloat(taxAmount).toFixed(2),
            offer: offer,
            discount: totalDiscount,
            offerAmount: parseFloat(offerAmount).toFixed(2),
            discountAmount:parseFloat(discountAmount).toFixed(2),
            serviceTaxAmount: parseFloat(serviceTaxArray).toFixed(2),
            grand_total: parseFloat(bill).toFixed(2),
        })
        return priceArray;
    }

deleteItem(index) {
    this.order[index].price = this.order[index].base_price
    this.order.splice(index,1);
    this.getPrices();
}
getCurrentDateTime() {
    var currentTime = new Date().toUTCString();
    return currentTime;
}
placeOrder(id){
     if (this.selectedIndex === 0) {
                this.payment = '1'
                this.cash = this.cash;
                this.card_number = null;
                this.paytm_mobile = null;
                this.nc = null;
                this.comment = null;
                this.ncSelected = null;
            } else if (this.selectedIndex === 1) {
                this.payment = '2'
                this.card_number = this.card_number;
                this.cash = null;
                this.paytm_mobile = null;
                this.nc = null;
                this.comment = null;
                this.ncSelected = null;
                if(!this.card_number) {
                    this.isDisabled = false;
                    return this.ToastService.error('Enter Card Detail');
                }
                if(!/^[0-9]{4}$/.test(this.card_number)) {
                    return this.ToastService.error('Enter Last 4 digits of card number');
                }
            } else if (this.selectedIndex === 2) {
                this.payment = '3'
                this.paytm_mobile = this.paytm_mobile;
                this.cash = null;
                this.card_number = null;
                this.nc = null;
                this.comment = null;
                this.ncSelected = null;
                if(!this.paytm_mobile) {
                    this.isDisabled = false;
                    return this.ToastService.error('Paytm number is required');
                }
                 if(!/^[0-9]{10}$/.test(this.paytm_mobile)) {
                    return this.ToastService.error('Mobile Number must be in 10 digits');
                }
            } else {
                this.nc = '1';
                this.cash = null;
                this.card_number = null;
                this.paytm_mobile = null;
                this.ncselected = this.ncselected;
                this.comment = this.comment;
            }
    if(this.payment === '1'){
        this.isDisabled = false;
        if(this.cash >= this.taxAndDiscount.grand_total) {
            this.cash = this.cash;
            this.$log.info(this.cash);
        } else {
            this.ToastService.error('Cash must be greater then or equal to Total value');
            return false;
        }
    }
    if(this.amountType){
        this.isDisabled = false;
        if(this.discount) {
            this.discount = this.discount;
            this.$log.info(this.cash);
        } else {
            this.ToastService.error('Enter discount amount');
            return false;
        }
    }
     if(this.order.length === 0){
                this.isDisabled = false;
                return this.ToastService.error('Item is required');
            }
            if(!this.table_no){
                this.isDisabled = false;
                return this.ToastService.error('Table number is required');
            }
    var data = {
        order_id: id,
        order: this.order,
        payment : this.payment,
        cash_given: this.cash,
         card_number: this.card_number,
         paytm_mobile: this.paytm_mobile ,
        sub_total: this.taxAndDiscount.subtotal,
        discounts: this.taxAndDiscount.discount,
        offer: this.taxAndDiscount.offer,
        tax_collected: (this.taxAndDiscount.totalTax),
        grand_total: this.taxAndDiscount.grand_total,
        discount_type: this.amountType,
        discount_amount: this.discount,
        taxes: this.taxAndDiscount.taxes,
        service_tax: this.serviceTax?this.serviceTax.tax_rate:0,
        nc:this.nc,
        nc_id: this.ncselected,
        nc_comment: this.comment,
        created_at: this.dateTime,
        table_no:this.table_no,
        _method: "PUT"
    };
    this.isDisabled = true;
    this.API.all('customer/order/'+id).post(data).then((response) => {
        this.ToastService.show(response.data.message);
    /*this.API.all('customer/order/'+id).get('').then((response) => {
        this.invoiceOrder = response.data.orders;
        this.invoice(this.invoiceOrder);
        this.clearOrder()
});*/
var htmlcontent = this.printHtml(data);
                    /*var orderDetail = [];*/
                    let confirm =this.$mdDialog.confirm(htmlcontent)
                        .title('Invoice')
                        .htmlContent(htmlcontent)
                        .ariaLabel('test')
                        .ok('Print')
                        .cancel('Cancel');

                    this.$mdDialog.show(confirm).then(() => {
                        // window.print();
                        // window.document.write('<html><head></head><body onload="window.print(); window.close(); this.DialogService.hide();">' + html + '</html>');
                        var popupWinindow = window.open('', '_blank', 'width=600,height=700,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no');
                    popupWinindow.document.open();
                    this.$log.info(popupWinindow);
                    popupWinindow.document.write(
                        '<html>' +
                        '<head>' +
                        '<link rel="stylesheet" type="text/css" href="/css/print.css" />' +
                        '</head>' +
                        '<body onload="window.print(); window.close(); this.DialogService.hide();">' +
                        '' + htmlcontent + '' +
                        '</html>'
                    );
                    popupWinindow.document.close();
                    window.close();
                    this.DialogService.hide();
                    }, () => {
                        this.DialogService.cancel();
                    });
                    this.clearOrder();
}, () => {
    this.ToastService.error('Internal server error');
});
}
clearOrder(){
    this.$state.go('app.order_list')
}

cancelOrder(index, requestType){
    let options = {
        controller: OrderEditController,
        controllerAs: 'vm',
        locals: {
            requestFor: requestType,
        }
    };
    this.DialogService.fromTemplate('orderEdit', options).then((data) => {
        this.order[index].deleted = requestType;
        if (this.order[index].deleted === 'edit') {
            if(!/^[0-9]*$/.test(data.quantity)) {
                this.isDisabled = true;
                return this.ToastService.error('Quantity must be integer value');
            }
        }
        if(data.quantity > 0) {
            if (parseInt(data.quantity) >= 300) {
                this.isDisabled = true;
                this.order[index].price = 0;
                this.getPrices();
                return this.ToastService.error('Quantity must be less than or equal to 300');
            } else {
                this.order[index].price = parseFloat(data.quantity* this.order[index].base_price);
            }  
        } else {
             if (this.order[index].deleted === 'edit') {
                this.isDisabled = true;
                return this.ToastService.error('Quantity must not be less than 1');
            } else {
                this.order[index].price =0;
            }
        }
        if(this.order[index].deleted === 'delete') {

            this.order[index].price =0;
         }
    angular.extend(this.order[index], data);
    this.getPrices();
    });
}

cashInput(){
    this.DialogService.prompt('Cash Given', '', 'Please enter cash').then((data) => {
        if(data >= this.taxAndDiscount.grand_total) {
        this.cash = data;
        this.$log.info(this.cash);
        } else {
            this.payment = '',
                this.ToastService.error('Cash must be greater then or equal to Total value');
            return false;
        }
    }, () => {
        this.payment = '';
    });

}
invoice(data){
    let options = {
        controller: InvoiceController,
        controllerAs: 'vm',
        locals:{
            orders: data,
        }
    };
    this.DialogService.fromTemplate('invoice', options);
}
up(index) {
    if (parseInt(this.order[index].quantity) >= 300) {
            return this.ToastService.error('Quantity must be less then or equal to 300');
            
        } else {
        this.order[index].quantity ++;
        this.order[index].price = parseFloat(this.order[index].quantity * this.order[index].base_price).toFixed(2);
        this.getPrices();
        }
}
down(index){
    if (this.order[index].quantity > 1) {
        this.order[index].quantity--;
        this.order[index].price = parseFloat(this.order[index].quantity * this.order[index].base_price).toFixed(2);
        this.getPrices();
    }
}
trimDecimal(figure, decimals){
    if (!decimals) decimals = 2;
    var d = Math.pow(10,decimals);
    return (parseInt(figure*d)/d).toFixed(decimals);
}
categoryButton(index){
    var categoryProduct = [];
    var subCategories = [];
    for (var i=0; i< this.categories[index].products.length; i++) {
        categoryProduct.push({
            id: this.categories[index].products[i].id,
            name: this.categories[index].products[i].name,
            price: this.categories[index].products[i].price,
            product_code: this.categories[index].products[i].product_code,
            base_price: this.categories[index].products[i].base_price,
            tax_rate: this.categories[index].products[i].tax_rate,
            tax_name: this.categories[index].products[i].tax_name,
            tags: this.categories[index].products[i].tags,
        });
    }
    for (var j=0; j< this.categories[index].child.length; j++) {
        subCategories.push({
         sub_category_name: this.categories[index].child[j].sub_category_name,
         products: this.categories[index].child[j].products,
         });
        for (var l=0; l< this.categories[index].child[j].products.length; l++) {
            categoryProduct.push({
                id: this.categories[index].child[j].products[l].id,
                name: this.categories[index].child[j].products[l].name,
                price: this.categories[index].child[j].products[l].price,
                product_code: this.categories[index].child[j].products[l].product_code,
                base_price: this.categories[index].child[j].products[l].base_price,
                tax_rate: this.categories[index].child[j].products[l].tax_rate,
                tax_name: this.categories[index].child[j].products[l].tax_name,
                tags: this.categories[index].child[j].products[l].tags,
            });
        }
    }
    this.subCategories = subCategories;
    this.categoryProducts = categoryProduct;
    this.$log.info(categoryProduct);
}

subCategoryButton(index){
    var categoryProduct = [];
    for (var l=0; l< this.subCategories[index].products.length; l++) {
        categoryProduct.push({
            id: this.subCategories[index].products[l].id,
            name: this.subCategories[index].products[l].name,
            price: this.subCategories[index].products[l].price,
            product_code: this.subCategories[index].products[l].product_code,
            base_price: this.subCategories[index].products[l].base_price,
            tax_rate: this.subCategories[index].products[l].tax_rate,
            tax_name: this.subCategories[index].products[l].tax_name,
            tags: this.subCategories[index].products[l].tags,
        });
    }
    this.categoryProducts = categoryProduct;
}

printHtml(data){
    this.$log.info(data.order);
    var ordersHtml = '';
    for (var i=0; i< data.order.length; i++) {
        if (data.order[i].quantity > 0) {
            ordersHtml += `<md-list-item class="md-3-line">
                        <div class="md-list-item-text layoutRow item-font" layout="row">
                        <h3 class="custom-width-name">
                            ${data.order[i].name}
                        </h3>
                        <h4 class="custom-width address_align layoutCenter">
                            ${data.order[i].quantity}
                        </h4>
                        <p class="custom-width address_align_right layoutCenter">
                            ${parseFloat(data.order[i].price).toFixed(2)}</p>
                        </div>
                        </md-list-item>`;
        }
        
    }
    var addressHtml = '';
    if(this.franchiseDetail.address_line_two) {
        addressHtml = `${this.franchiseDetail.address_line_two},<br>`;
    }
    var discountHtml = ''
    if(this.taxAndDiscount.discount){
        discountHtml = `
        <div class="md-list-item-text layoutRow" layout="row">
            <h3 class="amount-width">Discount</h3>
            <h3 class="amount-width-right address_align_right">${this.taxAndDiscount.discount ? this.taxAndDiscount.discount : '0'}</h3>
        </div>`;
    }
    var offerHtml = '';
    if(this.taxAndDiscount.offer) {
        offerHtml = `
        <div class="md-list-item-text layoutRow" layout="row">
            <h3 class="amount-width">Offer Discount</h3>
            <h3 class="amount-width-right address_align_right">${this.taxAndDiscount.offer ? this.taxAndDiscount.offer : '0'}</h3>
        </div>`;
    }
    var paymentMode = ''
    if (data.payment === '1') {
        paymentMode = 'Cash'
    } else if(data.payment === '2') {
        paymentMode = 'Card'
    } else {
        paymentMode = 'PayTM'
    }
   /* var serviceChargeHtml = '';
    if(this.taxAndDiscount.service_charge) {
        serviceChargeHtml = `<h3>Service Charges ${ this.taxAndDiscount.service_charge_amount } % : &nbsp;&nbsp;${ this.taxAndDiscount.service_charge }</h3>`;
    }
    var vatHtml = '';
    if(this.taxAndDiscount.vat) {
        vatHtml = `<h3>Vat ${ this.taxAndDiscount.vat_amount } % : &nbsp;&nbsp;${ this.taxAndDiscount.vat }</h3>`;
    }
    var serviceTaxHtml = '';
    if(this.taxAndDiscount.service_tax){
        serviceTaxHtml = `<h3>Service Tax ${ this.taxAndDiscount.service_tax_amount } % : &nbsp;&nbsp;${this.taxAndDiscount.service_tax }</h3>`;
    }*/
    var taxHtml = '';
        var allTaxes = this.allTaxes;
        var serviceTaxs = this.serviceTax;
        var taxType = this.taxType
        var allTaxType =  this.allTaxType;
        var serviceTaxHtml = ''
        if (this.serviceTax) {
            serviceTaxHtml =   `
            <div class="md-list-item-text layoutRow" layout="row">
            <h3 class="amount-width">${parseFloat(Math.round(serviceTaxs.tax_rate))}%
            ${allTaxType[serviceTaxs.tax_type]}</h3>
            <h3 class="amount-width-right address_align_right">
            ${this.taxAndDiscount.serviceTaxAmount}
            </h3></div>`;
        }
        angular.forEach(this.taxAndDiscount.taxes, function(value, key) {
            var taxRate = parseFloat(Math.round(allTaxes[key]/2));
            var taxName = key;
            var taxAmount = parseFloat(value).toFixed(2);
            if (taxType[key] === 1) {
                taxHtml += `
                <div class="md-list-item-text layoutRow" layout="row">
                    <h3 class="amount-width">Tax (CGST: ${taxRate}%, SGST: ${taxRate}%)
                ${allTaxType[taxType[key]]} on ${taxName} </h3>
                    <h3 class="amount-width-right address_align_right">${taxAmount}</h3></div>
                    `;
            } else {
                taxHtml += `
                <div class="md-list-item-text layoutRow" layout="row">
            <h3 class="amount-width">Tax ${parseFloat(Math.round(allTaxes[key]))}%
                ${allTaxType[taxType[key]]} on ${taxName}</h3>
            <h3 class="amount-width-right address_align_right">${taxAmount}</h3></div>`;
            }

        });
        var changeAmount = data.cash_given?(data.cash_given - this.taxAndDiscount.grand_total):0;
        var cash = ''
        if (data.cash_given) {
            cash = `<b>Cash : &nbsp;&nbsp;${ data.cash_given?data.cash_given:0 }</b>
                &nbsp;&nbsp;<b>Change : &nbsp;&nbsp;${ parseFloat(changeAmount).toFixed(2) }</b><br>`;
        }
        var cardNumber = '';
        if (data.card_number) {
            cardNumber = `<b>Card Number : &nbsp;&nbsp;${data.card_number}</b>`;
        }
        var paytmNumber = '';
        if (data.paytm_mobile) {
            paytmNumber = `<b>Paytm Number : &nbsp;&nbsp;${data.paytm_mobile}</b>`;
        }
        var createdAt = new Date(data.created_at * 1000);
        var hours = createdAt.getHours();
        var minutes = createdAt.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
        var createdAtDate = createdAt.getDate()+'-'+(createdAt.getMonth() + 1)+'-'+createdAt.getFullYear()+
            ' '+hours+':'+minutes+' '+ampm;
    var html = `<div layout="row">
        <md-dialog-content class="printSectionId">
        <md-list class="md-dense" flex>
    <div layout="column" class="address_align md-subheader colour_class">
        <md-subheader class="colour_class">
        <b>${this.franchiseDetail.franchise_name}</b><br>
        ${this.franchiseDetail.address_line_one},<br>
        ${addressHtml}
        ${this.franchiseDetail.city}, ${this.franchiseDetail.region},<br>
        ${this.franchiseDetail.country}
    </md-subheader>
    </div>
    <br>
    <div layout="row" layout-align="center center" class="address_align layoutRow layoutCenter md-subheader colour_class">
        <md-subheader class="colour_class">Table Number:  ${this.tables[data.table_no]}</md-subheader>

    </div>
    <div layout="row" layout-align="center center" class="address_align layoutRow layoutCenter md-subheader colour_class">
        <md-subheader class="colour_class">GSTIN:  ${this.franchiseDetail.gst_number}</md-subheader>

    </div>
     <div layout="row" layout-align="center center" class="address_align layoutRow layoutCenter md-subheader colour_class">
            <md-subheader class="colour_class">Order Number:  ${this.orders.order_number}</md-subheader>

        </div>
    <div layout="row" layout-align="center center" class="address_align layoutRow layoutCenter md-subheader colour_class">
        <md-subheader class="colour_class">Transaction Id:  ${this.orders.transaction_id}</md-subheader>

    </div>
    <div layout="row" layout-align="center center" class="address_align layoutRow layoutCenter md-subheader colour_class">
        <md-subheader class="colour_class">Date: ${createdAtDate}</md-subheader>

    </div>
    <div layout="row" layout-align="center center" class="address_align layoutRow layoutCenter md-subheader colour_class">
        <md-subheader class="colour_class">Counter Person: ${this.franchiseDetail.order_taken_by}</md-subheader>

    </div>
    <md-list-item>
    <div class="md-list-item-text layoutRow" layout="row">
        <h3 class="custom-width-name">Item</h3>
        <h3 class="custom-width address_align layoutCenter">Qty</h3>
        <h3 class="custom-width address_align layoutCenter">Price</h3>
        </div>
        </md-list-item>
        <div class="divider"></div>
            ${ordersHtml}
        <div class="divider"></div>
        <md-list-item>
        <div class="md-list-item-text layoutColumn layoutCenter item-font" layout="column" layout-align="center center">
            <div class="md-list-item-text layoutRow" layout="row">
                <h3 class="amount-width">Total</h3>
                <h3 class="amount-width-right address_align_right">${ this.taxAndDiscount.subtotal }</h3>
            </div>
            ${discountHtml}
            ${offerHtml}
            ${serviceTaxHtml}
            ${taxHtml}
            <div class="md-list-item-text layoutRow" layout="row">
                <h3 class="amount-width">Net Total</h3>
                <h3 class="amount-width-right address_align_right">${ this.taxAndDiscount.grand_total  }</h3>
            </div>
    </md-list-item>
    <div class="divider"></div>
    <div layout="row" layout-align="center center" class="layoutRow layoutCenter md-list-item-text">
        ${cash}
         ${cardNumber}
        ${paytmNumber} 
    </div>
    <div class="divider"></div>
        <div layout="row" layout-align="center center" class="layoutRow layoutCenter">
        Mode of Payment:&nbsp;&nbsp;
<span class="marginTop">${paymentMode}</span>
        </div>
        <div class="divider"></div>
        <div layout="column" layout-align="center center" class="layoutColumn layoutCenter address_align md-subheader colour_class">
        <md-subheader>
        FEEDBACK ${this.franchiseDetail.store_manager_email}<br>
        HAVE A NICE DAY</md-subheader>
    </div>
    </md-list>
    </md-dialog-content>
    </div>`
    return html;
}
    getOffer(order){
        var offerDiscount = 0;
        var dateCurrent = new Date(this.dateTime * 1000);
        //var currentDate = dateCurrent.getFullYear()+'-'+(dateCurrent.getMonth() + 1)+'-'+dateCurrent.getDate();
        //this.$log.info(this.offers);
        for(var i=0; i< this.offers.length; i++) {
            var condition = angular.fromJson(this.offers[i].conditions);
            var type = condition.type;
            var amount = parseInt(this.offers[i].amount);
            var offerProducts = Math.round(amount+parseInt(this.offers[i].discount_qty_step));
            var fromDate = new Date(this.offers[i].from_date);
            //var from_date = fromDate.getFullYear()+'-'+(fromDate.getMonth() + 1)+'-'+fromDate.getDate();
            var toDate = new Date(this.offers[i].to_date);
            //var to_date = toDate.getFullYear()+'-'+(toDate.getMonth() + 1)+'-'+toDate.getDate();
            if(fromDate.getTime()<=dateCurrent.getTime() &&  toDate.getTime()>=dateCurrent.getTime()){
           // if(from_date<=currentDate && to_date>=currentDate){
                if (type == 'products') {
                    this.id = condition.ids;
                    for (var j=0; j<this.id.length; j++) {
                        //for (var k=0; k<this.order.length; k++) {
                        if(order.id == this.id[j]){
                            this.message = 'buy '+parseInt(this.offers[i].discount_qty_step)+' get '+parseInt(this.offers[i].amount)+' free'
                            for (var q=1; q<2000; q++) {
                                if (order.quantity >= (offerProducts*q)) {
                                    offerDiscount += amount * order.base_price;
                                }
                            }
                        }
                        //}
                    }
                } else if (type =='all') {
                    // for (var l=0; l<this.order.length; l++) {
                    for (var r=1; r<2000; r++) {
                        if (order.quantity >= (offerProducts*r)) {
                            offerDiscount += amount * order.base_price;
                        }
                    }
                    // }
                } else {
                        this.categoryid = condition.ids;
                        for (var m=0; m<this.categoryid.length; m++) {
                            for (var n=0; n<this.menu.length; n++) {
                                if(this.menu[n].category_id == this.categoryid[m]){
                                    if(this.menu[n].products.length) {
                                         for(var k=0; k<this.menu[n].products.length; k++){
                                             if(this.menu[n].products[k].product_id == order.id) {
                                                for (var t=1; t<2000; t++) {
                                                    if (order.quantity >= (offerProducts*t)) {
                                                        offerDiscount += amount * order.base_price;
                                                         this.$log.info(offerDiscount+'dfkjhh');
                                                    }
                                                }
                                             }
                                         }
                                    }
                                    i/*f(this.menu[n].child.length){
                                         for(var a=0; a<this.menu[n].child.length; a++){
                                             for(var b=0; b<this.menu[n].child[a].products.length; b++){
                                                 if(this.menu[n].child[a].products[b].product_id == order.id) {
                                                     this.$log.info('dkjjkjkfh');
                                                    for (var l=1; l<2000; l++) {
                                                        if (order.quantity >= (offerProducts*l)) {
                                                            offerDiscount += amount * order.base_price;
                                                             this.$log.info(offerDiscount+'dfkjhh');
                                                        }
                                                    }
                                                 }
                                             }
                                         }
                                    }*/    
                                }
                                if(this.menu[n].child.length){
                                     for(var p=0; p<this.menu[n].child.length; p++){
                                         if(this.menu[n].child[p].category_id == this.categoryid[m]){
                                             //for (var u=0; u<this.order.length; u++) {
                                                 for(var u=0; u<this.menu[n].child[p].products.length; u++){
                                                     if(this.menu[n].child[p].products[u].product_id == order.id) {
                                                         for (var v=1; v<2000; v++) {
                                                            if (order.quantity >= (offerProducts*v)) {
                                                                offerDiscount += amount * order.base_price;
                                                                 this.$log.info(offerDiscount+'dfkjhh');
                                                            }
                                                        }
                                                     }
                                                 }

                                             //}
                                         }
                                     }
                                 }
                            }
                        }
                    }
            }
        }
        return offerDiscount;
    }
    changeDiscount(){
        var discountAmount = 0;
        if(this.amountType === 'fixed'){
            if(this.getSum()[0].grand_total >= this.discount) {
                this.taxAndDiscount.discount = parseInt(this.discount);
                this.taxAndDiscount.grand_total = parseInt(this.getSum()[0].grand_total - this.discount);
            } else {
                this.ToastService.error('Discount must be less then or equal to Sub total value');
                return false;
            }
        } else {
            discountAmount = (this.getSum()[0].grand_total*parseInt(this.discount) / 100);
            if(this.getSum()[0].grand_total >= discountAmount) {
                this.taxAndDiscount.discount = parseInt(discountAmount);
                this.taxAndDiscount.grand_total = parseInt(this.getSum()[0].grand_total - discountAmount)
                //discountAmount = (this.getSum()[0].amount*parseInt(this.discount) / 100);
            } else {
                this.ToastService.error('Discount must be less then or equal to Sub total value');
                return false;
            }
        }
        //this.getPrices();
    }

    getDiscount(order){
        var discountAmounts = 0;
        var discountCurrentDate = new Date(this.dateTime * 1000);
        //var currentDateDiscount = discountCurrentDate.getFullYear()+'-'+(discountCurrentDate.getMonth() + 1)+'-'+discountCurrentDate.getDate();
        for(var i=0; i< this.discountAmounts.length; i++) {
            var conditions = angular.fromJson(this.discountAmounts[i].conditions);
            var types = conditions.type;
            var amounts = parseInt(this.discountAmounts[i].amount);
            var fromDateDiscount = new Date(this.discountAmounts[i].from_date);
           // var from_dateDiscount = fromDateDiscount.getFullYear()+'-'+(fromDateDiscount.getMonth() + 1)+'-'+fromDateDiscount.getDate();
            var toDateDiscount = new Date(this.discountAmounts[i].to_date);
            //var to_dateDate = toDateDiscount.getFullYear()+'-'+(toDateDiscount.getMonth() + 1)+'-'+toDateDiscount.getDate();
            if(fromDateDiscount.getTime()<=discountCurrentDate.getTime() &&  toDateDiscount.getTime()>=discountCurrentDate.getTime()){
                this.$log.info(types);
                if (types == 'products') {
                    this.id = conditions.ids;
                    for (var j=0; j<this.id.length; j++) {
                        //for (var k=0; k<this.order.length; k++) {
                        if(order.id == this.id[j]){
                            if (this.discountAmounts[i].amount_type == 'fixed') {
                                if (order.price >= amounts) {
                                            discountAmounts = amounts * order.quantity;
                                        }
                            } else {
                                discountAmounts += (amounts* order.price)/100;
                            }
                        }

                        // }
                    }
                } else if (types =='all') {
                    //for (var l=0; l<this.order.length; l++) {
                    if (this.discountAmounts[i].amount_type == 'fixed') {
                        if (order.price >= amounts) {
                                            discountAmounts = amounts * order.quantity;
                                        }
                    } else {
                        discountAmounts += (amounts*order.price)/100;
                    }
                    // }
                } else {
                    this.ids = conditions.ids;
                    for (var m=0; m<this.ids.length; m++) {
                        for (var n=0; n<this.menu.length; n++) {
                            if(this.menu[n].category_id == this.ids[m]){
                                //for (var p=0; p<this.order.length; p++) {
                                if(this.menu[n].products.length) {
                                    for(var q=0; q<this.menu[n].products.length; q++){
                                        if(this.menu[n].products[q].product_id == order.id) {
                                            if (this.discountAmounts[i].amount_type == 'fixed') {
                                                if (order.price >= amounts) {
                                            discountAmounts = amounts * order.quantity;
                                        }
                                            } else {
                                                discountAmounts += (amounts * order.price) / 100;
                                            }
                                        }
                                    }
                                }
                               /* if(this.menu[n].child.length){
                                    for(var a=0; a<this.menu[n].child.length; a++){
                                        for(var b=0; b<this.menu[n].child[a].products.length; b++){
                                            if(this.menu[n].child[a].products[b].product_id == order.id) {
                                                this.$log.info('dkjjkjkfh');
                                                if (this.discountAmounts[i].amount_type == 'fixed') {
                                                    if (order.price >= amounts) {
                                            discountAmounts = amounts * order.quantity;
                                        }
                                                } else {
                                                    discountAmounts += (amounts * order.price) / 100;
                                                }
                                            }
                                        }
                                    }
                                }*/
                                //}
                            }
                            if(this.menu[n].child.length){
                                for(var t=0; t<this.menu[n].child.length; t++){
                                    if(this.menu[n].child[t].category_id == this.ids[m]){
                                        this.$log.info(this.menu[n].child[t].category_id == this.ids[m]);
                                        //for (var u=0; u<this.order.length; u++) {
                                        for(var r=0; r<this.menu[n].child[t].products.length; r++){
                                            if(this.menu[n].child[t].products[r].product_id == order.id) {this.$log.info('dkjjkjkfh');
                                                if (this.discountAmounts[i].amount_type == 'fixed') {
                                                    if (order.price >= amounts) {
                                            discountAmounts = amounts * order.quantity;
                                        }
                                                } else {
                                                    discountAmounts += (amounts * order.price) / 100;
                                                }
                                            }
                                        }

                                        //}
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return discountAmounts;
    }
    ncDiscount() {
            if (this.selectedIndex === 3) {
                this.nc = '1';
                 this.taxAndDiscount.discount = this.taxAndDiscount.subtotal;
                this.taxAndDiscount.offer = 0;
                this.taxAndDiscount.totalTax = 0;
                this.taxAndDiscount.taxes = 0;
                this.taxAndDiscount.grand_total = 0;
                this.taxAndDiscount.taxes = [];
                this.taxAndDiscount.serviceTaxAmount = 0;
                this.cash = 0;
                this.payment = '1'
            } else {
                this.nc = null;
                 this.getPrices()
            }

        }
}


export const EditOrderComponent = {
    templateUrl: './views/app/components/edit-order/edit-order.component.html',
    controller: EditOrderController,
    controllerAs: 'vm',
    bindings: {}
}
