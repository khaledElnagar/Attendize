<?php
return [
    'sandbox' => env('PAYFORT_USE_SANDBOX', true),
    'merchant_identifier' => env('PAYFORT_MERCHANT_IDENTIFIER','kSyYGeXA'),
    'access_code' => env('PAYFORT_ACCESS_CODE','sl3PBwRqlgaoriBtLuJm'),
    'sha_type' => env('PAYFORT_SHA_TYPE', 'sha256'),
    'sha_request_phrase' => env('PAYFORT_SHA_REQUEST_PHRASE','cxzaw4332'),
    'sha_response_phrase' => env('PAYFORT_SHA_RESPONSE_PHRASE','fvgr4322'),
    'currency' => env('PAYFORT_CURRENCY', 'USD'),
    'return_url' => env('PAYFORT_RETURN_URL', '/payfort/payment')
];