<?php

use App\Models\Contact;

echo PHP_EOL;

echo "Contacts:", PHP_EOL;

print_r(Contact::all()->toArray());

echo PHP_EOL;

echo "Params:", PHP_EOL;

print_r($params);
