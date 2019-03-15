# Laravel Coda Redirect API , Payment Gateway Api & rest Api

[![StyleCI](https://styleci.io/repos/98979571/shield?branch=master)](https://styleci.io/repos/98979571)
[![Latest Stable Version](https://poser.pugx.org/php-coda/laravel-coda/v/stable)](https://packagist.org/packages/php-coda/laravel-coda)
[![Total Downloads](https://poser.pugx.org/php-coda/laravel-coda/downloads)](https://packagist.org/packages/php-coda/laravel-coda)


Laravel Coda package

## Laravel version 5.x.x

## Installation

Install using composer:
```php
composer require php-coda/laravel-coda
```

Once installed, in your project's config/app.php file replace the following entry from the providers array:

```php
PhpCoda\LaravelCoda\LaravelCodaServiceProvider::class,
```

And 
```php 
php artisan vendor:publish --provider="PhpCoda\LaravelCoda\LaravelCodaServiceProvider" --force
```
This is the contents of the published config file:

```php
return [
    'airtimeURL' => env('airtimeURL'),
   
    'airtimeRestURL' => env('airtimeRestURL'),

    'apikey' => env('apikey'),

    'country'    => env('country'),

    'currency'   => env('currency'),

    'txnType'    => env('txnType'),
    
    'requestType' => env('requestType','json'),
];
```

Add your application configuration to a .env file
```php
airtimeURL=*********
airtimeRestURL=*******
apikey=********
country=********
currency=********
txnType=*********
requestType=********
```


#### Payment Request [ Using the Payment Gateway API and SecurePay ]

Sample Construct Payment Form

Add the `data-encrypt` fields into the form to capture card information securely.

```html
<link rel="stylesheet" href="https://sandbox.codapayments.com/airtime/css/airtime_v1.0.css">
<script type="text/javascript" src="https://sandbox.codapayments.com/airtime/js/airtime_v1.0.js"></script>
<script type="text/javascript">
       function processPayment (obj) {
               initTxn (obj, 1);
       }        function initTxn (obj, payType) {
           $.ajax ( {
               type: "POST",
               url: "<your payment request link>",
               data: $('#itemForm').serialize() + "&type=InitTxn",
               // console.log(data);
               success: function (data) {
                   if (payType == 1) {
                       airtime_checkout(data);
                   } else if (payType == 2) {
                       $("#txn_id").val(data);
                       $("#itemForm").attr("action","/php-rest/iframeDesktop.php");
                       $("#itemForm").submit();
                   } else if (payType == 3) {
                       $("#txn_id").val(data);
                       $("#itemForm").attr("action","/php-rest/iframeMobile.php");
                       $("#itemForm").submit();
                   }
               },
               error: function (jqXHR, textSatus, err) {
                   alert (err);
               }
           } );
       }    
</script>
<form method="POST" action="#" accept-charset="UTF-8" id="itemForm">
    <input type="text" id="Name" placeholder="Enter Name" name="Name" required="required">
    <input type="text" id="Item" placeholder="Enter amount" name="Item" required="required">
    <div class="form-actions">
        <a href="javascript:processPayment(this);">Payment Now</a>
    </div>
</form>
```

Submit the request your back end code will receives

##### Preparation 

```php
$payload = \PaymentCoda::CodaRequest($request);
```

Submit the Payment Request:

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.