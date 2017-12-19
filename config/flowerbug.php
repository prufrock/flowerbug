<?php

return [
  'simpledb' => [
    'projects_domain' => env('FLOWERBUG_SIMPLEDB_PROJECTS_DOMAIN'),
    'ipn_messages_domain' => env('FLOWERBUG_SIMPLEDB_IPN_MESSAGES_DOMAIN')
  ],
  's3' => [
    'projects_bucket' => env('FLOWERBUG_S3_PROJECTS_BUCKET'),
    'signed_url_expiration' => env('FLOWERUBG_SIGNED_URL_EXPIRATION', '+1 year')
  ],
  'sale_message' => env('FLOWERBUG_SALE_MESSAGE', 'Thank you for purchase. Here are your files:'),
  'seller_address' => env('FLOWERBUG_SELLER_ADDRESS'),
  'email_subject' => env('FLOWERBUG_EMAIL_SUBJECT'),
  'aws_region' => env('AWS_REGION'),
  'paypal' => [
    'ipn_verify_resource' => env('PAYPAL_IPN_VERIFY_RESOURCE'),
    'ipn_verify_host' => env('PAYPAL_IPN_VERIFY_HOST'),
    'ipn_verify_url' => env('PAYPAL_IPN_VERIFY_URL'),
    'ipn_verify_port' => env('PAYPAL_IPN_VERIFY_PORT')
  ],
  'test' => [
    'receiver_email' => env('FLOWERBUG_TEST_RECEIVER_EMAIL'),
    'payer_email' => env('FLOWERBUG_TEST_PAYER_EMAIL') 
  ]
];