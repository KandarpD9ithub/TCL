<?php

return [
    'TAX_NAME' => [
        'service_tax'   => 'Service Tax',
        'service_charge'=> 'Service Charge',
        'vat'           => 'VAT',
        'sgst'          => 'SGST',
        'cgst'          => 'CGST'
    ],

    'ROLE_NAME' => [
        '2' => 'Accountant',
        '3' => 'Cashier',
        '4' => 'Store Manager',
        '5' => 'Chef'
    ],
    'RULE_TYPE' => [
        'discount'  => 'Discount',
        'offer'     => 'Offer'
    ],
    'RULE_ON' => [
        'all' => 'All',
        'products'  => 'Products',
        'categories'=> 'Categories'
    ],
    'AMOUNT_TYPE' => [
        'fixed'     => 'Fixed',
        'percent'   => 'Percent'
    ],
    'OFFER_AMOUNT_TYPE' => [
        'buy_x_get_y_free' => 'Buy X get Y free (amount is Y)'
    ],
    'PaymentMethod' => [
        ''=>'All',1=>'Cash',2=>'Card',3=>'PayTM',4=>'Wallet',
    ],
    'nfcBandStatus' => [
        '0'=>'All',1=>'New',2=>'In-Use',3=>'Damaged',4=>'Lost',
    ],
    'RECORDS_PER_PAGE' => 20,

    'TAX_TYPE' => [
        '1' => 'GST',
        '2' => 'Service Charge',
        '3' => 'VAT'
    ],
    'Payment_Method' => [
        ''=>'All',1=>'Cash',2=>'Card',3=>'PayTM',4=>'Wallet', 5 => 'NC'
    ],
];
