class CustomerOrderController{
    constructor(API, ToastService, $stateParams, $log, $mdDialog){
        'ngInject';
        this.stateParams = $stateParams;
        this.$log =$log;
        this.API = API
        this.ToastService = ToastService;
        this.$mdDialog = $mdDialog;
    }

    $onInit(){
        this.API.all('/customer/'+this.stateParams.customer_id+'/orders')
            .get('').then((response) => {
                this.customerOrder = response.data.orders;
                this.$log.info(this.customerOrder);
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
                }) ;
                this.API.all('franchise/detail').get('').then((response) => {
                this.franchiseDetail = response.data.franchiseDetail;
            });
            this.API.all('menu').get('').then((response) => {
                this.menu = response.data.menus;
            });
  
    }

    print(id) {
        this.API.all('customer/order/'+id).get('').then((response) => {
        this.orders = response.data.orders;
        this.$log.info(this.orders);
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
           var l = 0,
            taxName = '',
            taxRate = 0;
        var taxArray={};
        var serviceTaxArray = 0;
        var serviceTaxRate = 0;
        for(; l < this.order.length; l++) {
            if (this.serviceTax) {
                serviceTaxRate = parseFloat(((this.order[l].price-this.order[l].discount-this.order[l].offer) * this.serviceTax.tax_rate) / 100);
                serviceTaxArray += parseFloat((serviceTaxRate));
            }
            taxName = this.order[l].tax_name;
            /* taxRate = this.order[i].tax_rate;*/
            taxRate = this.order[l].tax_rate ? parseFloat((((this.order[l].price-this.order[l].discount-this.order[l].offer)+serviceTaxRate) * this.order[l].tax_rate) / 100): 0;
            if (taxArray[taxName]) {
                taxArray[taxName] += parseFloat((taxRate));
            } else {
                taxArray[taxName] = parseFloat((taxRate));
            }
            /*if (taxesArray[taxType]) {
             taxesArray[taxType] = allTaxesArray.push(taxArray);
             } else {
             taxesArray[taxType] = taxArray;
             }*/
            

        }
        var data = {
            order_id: id,
            order: this.order,
            payment : this.payment,
            cash_given: this.orders.cash,
             card_number: this.orders.card_number,
             paytm_mobile: this.orders.paytm_mobile ,
            sub_total: this.taxAndDiscount.subtotal,
            discounts: this.taxAndDiscount.discount,
            offer: this.taxAndDiscount.offer,
            tax_collected: (this.taxAndDiscount.totalTax),
            grand_total: this.taxAndDiscount.grand_total,
            taxes: taxArray,
            service_tax: this.serviceTax?this.serviceTax.tax_rate:0,
            serviceTaxAmount: serviceTaxArray,
            table_no:this.orders.table_no,
            created_at: this.orders.created_at,
        };
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
    });
    }
     toTimeStamp (date) {
        if (date == null) { return ""; }
    var _timestamp = new Date(date+" UTC").getTime();
    return _timestamp;
  }

  printHtml(data){
    var ordersHtml = '';
    for (var i=0; i< data.order.length; i++) {
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
        </div>
        `;
    }
    var offerHtml = '';
    if(this.taxAndDiscount.offer) {
        offerHtml = `
        <div class="md-list-item-text layoutRow" layout="row">
            <h3 class="amount-width">Offer Discount</h3>
            <h3 class="amount-width-right address_align_right">${this.taxAndDiscount.offer ? this.taxAndDiscount.offer : '0'} </h3>
        </div>`;
    }
    var paymentMode = ''
    if (this.orders.payment === 1) {
        paymentMode = 'Cash'
    } else if(this.orders.payment === 2) {
        paymentMode = 'Card'
    } else if(this.orders.payment === 3) {
        paymentMode = 'PayTM'
    } else {
        paymentMode = 'Wallet'
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
            serviceTaxHtml =   `<div class="md-list-item-text layoutRow" layout="row">
            <h3 class="amount-width">${parseFloat(serviceTaxs.tax_rate).toFixed(2)}%
            ${allTaxType[serviceTaxs.tax_type]}</h3>
            <h3 class="amount-width-right address_align_right">
            ${this.orders.non_chargeable_people_id?0:parseFloat(data.serviceTaxAmount).toFixed(2)}
            </h3></div>`;
        }
        var nc = this.orders.non_chargeable_people_id;
        angular.forEach(data.taxes, function(value, key) {
            var taxRate = parseFloat(Math.round(allTaxes[key]/2));
            var taxName = key;
            var taxAmount = parseFloat(value).toFixed(2);
            if (taxType[key] === 1) {
                taxHtml += `<div class="md-list-item-text layoutRow" layout="row">
            <h3 class="amount-width">Tax (CGST: ${taxRate}%, SGST: ${taxRate}%)
                ${allTaxType[taxType[key]]} on ${taxName} </h3>
            <h3 class="amount-width-right address_align_right">${nc? 0 :taxAmount}</h3></div>`;
            } else {
                taxHtml += `<div class="md-list-item-text layoutRow" layout="row">
            <h3 class="amount-width">Tax ${parseFloat((allTaxes[key]))}%
                ${allTaxType[taxType[key]]} on ${taxName}</h3>
            <h3 class="amount-width-right address_align_right">${nc? 0: taxAmount}</h3></div>`;
            }

        });
        var changeAmount = this.orders.cash_given?(this.orders.cash_given - this.taxAndDiscount.grand_total):0;
        /*var createdDate = new Date(data.created_at+" UTC").getTime()*/
        var cash = ''
        if (this.orders.cash_given) {
            cash = `<b>Cash : &nbsp;&nbsp;${ this.orders.cash_given?this.orders.cash_given:0 }</b><br>
            &nbsp;&nbsp;<b>Change : &nbsp;&nbsp;${ parseFloat(changeAmount).toFixed(2) }</b>`
        }
        var cardNumber = '';
        if (data.card_number) {
            cardNumber = `<b>Card Number : &nbsp;&nbsp;${data.card_number}</b>`;
        }
        var paytmNumber = '';
        if (data.paytm_mobile) {
            paytmNumber = `<b>Paytm Number : &nbsp;&nbsp;${data.paytm_mobile}</b>`;
        }

        var createdAt = new Date(data.created_at);
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
            <h3 class="custom-width address_align_right layoutCenter">Price</h3>
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
                <h3 class="amount-width-right address_align_right">${ this.taxAndDiscount.grand_total }</h3>
            </div>   
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
        <b>Mode of Payment:</b>&nbsp;&nbsp;
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

}

export const CustomerOrderComponent = {
    templateUrl: './views/app/components/customer_order/customer_order.component.html',
    controller: CustomerOrderController,
    controllerAs: 'vm',
    bindings: {}
}
