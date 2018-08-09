import {EditOrderComponent} from './app/components/edit-order/edit-order.component';
import {InvoiceComponent} from './app/components/invoice/invoice.component';
import {CustomerOrderComponent} from './app/components/customer_order/customer_order.component';
import {CustomerListComponent} from './app/components/customer_list/customer_list.component';
import {OrderListComponent} from './app/components/order_list/order_list.component';
import {PlaceOrderComponent} from './app/components/place-order/place-order.component';
import {AppHeaderComponent} from './app/components/app-header/app-header.component';
import {AppRootComponent} from './app/components/app-root/app-root.component';
import {AppShellComponent} from './app/components/app-shell/app-shell.component';
import {ResetPasswordComponent} from './app/components/reset-password/reset-password.component';
import {ForgotPasswordComponent} from './app/components/forgot-password/forgot-password.component';
import {LoginFormComponent} from './app/components/login-form/login-form.component';
import {RegisterFormComponent} from './app/components/register-form/register-form.component';
import {CreateCustomerFormComponent} from './app/components/create_customer_form/create_customer_form.component';


angular.module('app.components')
	.component('editOrder', EditOrderComponent)
	.component('invoice', InvoiceComponent)
	.component('customerOrder', CustomerOrderComponent)
	.component('customerList', CustomerListComponent)
	.component('orderList', OrderListComponent)
	.component('placeOrder', PlaceOrderComponent)
	.component('appHeader', AppHeaderComponent)
	.component('appRoot', AppRootComponent)
	.component('appShell', AppShellComponent)
	.component('resetPassword', ResetPasswordComponent)
	.component('forgotPassword', ForgotPasswordComponent)
	.component('loginForm', LoginFormComponent)
	.component('registerForm', RegisterFormComponent)
	.component('createCustomerForm', CreateCustomerFormComponent);

