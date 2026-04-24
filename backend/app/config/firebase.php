<?php

$credentials = require __DIR__ . '/firebase_credentials.php';

return array_merge($credentials, [
    'token_uri' => 'https://oauth2.googleapis.com/token',
    'firestore_base_url' => 'https://firestore.googleapis.com/v1',
    'collection_users' => 'users',
]);