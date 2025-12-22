<?php

return [
    'allowed_domains' => array_values(array_filter(array_map('trim',
        explode(',', env('ALLOWED_SIGNUP_DOMAINS', 'mwmotor.com'))
    ))),
];