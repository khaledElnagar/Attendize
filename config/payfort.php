<?php
return [
    'sandbox' => env('PAYFORT_USE_SANDBOX', true),
    'merchant_identifier' => env('PAYFORT_MERCHANT_IDENTIFIER','VIaNcEHz'),
    'access_code' => env('PAYFORT_ACCESS_CODE','NkWusiYB8SnUWn9SYSBW'),
    'sha_type' => env('PAYFORT_SHA_TYPE', 'sha256'),
    'sha_request_phrase' => env('PAYFORT_SHA_REQUEST_PHRASE','TESTSHAIN'),
    'sha_response_phrase' => env('PAYFORT_SHA_RESPONSE_PHRASE','TESTSHAOUT'),
    'currency' => env('PAYFORT_CURRENCY', 'SAR'),
    'return_url' => env('PAYFORT_RETURN_URL', '/payfort/payment')
];