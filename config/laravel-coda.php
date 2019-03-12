<?php

return [
    'airtimeURL' => 'https://sandbox.codapayments.com/airtime/',
    //Please change to production AirtimeURL when you release to production environment.
    //$airtimeURL = 'https://airtime.codapayments.com/airtime/';

    'airtimeRestURL' => 'https://sandbox.codapayments.com/airtime/api/restful/v1.0/Payment',
    //Please change to production AirtimeURL when you release to production environment.
    //$airtimeRestURL = 'https://airtime.codapayments.com/airtime/api/restful/v1.0/Payment';

    'apikey' => '<your API Key>',
    //Please change to your APIKey - https://online.codapayments.com/merchant/developer/references#api_key

    'country' 	 => '104',
    'currency' 	 => '104',
    //Please check link https://online.codapayments.com/merchant/developer/references#currency_codes

    'txnType' 	 => '1',
    //PaymentType => for example DCB = 1
    //Please check link https://online.codapayments.com/merchant/developer/documentation#paymentTypeInfo

    'requestType' => 'json',
    //json or xml

];
