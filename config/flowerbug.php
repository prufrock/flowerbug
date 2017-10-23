<?php

return [
  'simpledb' => [
    'projects_domain' => env('FLOWERBUG_SIMPLEDB_PROJECTS_DOMAIN',''),
    'ipn_messages_domain' => env('FLOWERBUG_SIMPLEDB_IPN_MESSAGES_DOMAIN','')
  ],
  'sale_message' => env('FLOWERBUG_SALE_MESSAGE', 'Thank you for purchase. Here are your files:')
];