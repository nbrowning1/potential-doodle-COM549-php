<?php

/* Everything needed to perform an OAuth authentication
    request to Instagram - our own client ID and client
    secret keys from API registration for our site.
    Scopes array defines what we're asking the user to
    access of theirs when we perform authentication - 
    resources specific to the user that we want access to.
*/
return [
  'client_id' => '7a77e55da23647e1bb88aee63d14c419',
  'client_secret' => '6c00f9e6c188431cac3de27629f5d6c7',
  'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'] . '/~b00652112/COM549/Practical5/complete-oauth.php',
  'scopes' => [
    'likes',
    'basic',
    'public_content'
  ]
];