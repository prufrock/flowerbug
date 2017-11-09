<?php

return [
  'simpledb' => [
    'projects_domain' => env('FLOWERBUG_SIMPLEDB_PROJECTS_DOMAIN'),
    'ipn_messages_domain' => env('FLOWERBUG_SIMPLEDB_IPN_MESSAGES_DOMAIN')
  ],
  's3' => [
    'projects_bucket' => env('FLOWERBUG_S3_PROJECTS_BUCKET')
  ],
  'sale_message' => env('FLOWERBUG_SALE_MESSAGE', 'Thank you for purchase. Here are your files:'),
  'seller_address' => env('FLOWERBUG_SELLER_ADDRESS'),
  'email_subject' => env('FLOWERBUG_EMAIL_SUBJECT'),
  'aws_region' => env('AWS_REGION')
];