<?php

return [
    'enabled' => filter_var(env('INBOUND_EMAIL_ENABLED', false), FILTER_VALIDATE_BOOL),

    'provider' => env('INBOUND_EMAIL_PROVIDER', 'mailgun'),

    'allowed_domain' => env('INBOUND_EMAIL_ALLOWED_DOMAIN', 'mwmotor.com'),

    'mailgun' => [
        'signing_key' => env('MAILGUN_INBOUND_SIGNING_KEY'),
    ],

    'send_rejection_notice' => filter_var(env('INBOUND_EMAIL_SEND_REJECTION_NOTICE', false), FILTER_VALIDATE_BOOL),

    'rejection_notice_subject' => env('INBOUND_EMAIL_REJECTION_SUBJECT', 'Unable to process your request'),

    'rejection_notice_body' => env(
        'INBOUND_EMAIL_REJECTION_BODY',
        'Your email could not be processed because only @mwmotor.com sender addresses are accepted.'
    ),

    'attachments' => [
        'disk' => env('INBOUND_EMAIL_ATTACHMENTS_DISK', 'local'),
        'max_total_size_mb' => (int) env('INBOUND_EMAIL_ATTACHMENTS_MAX_TOTAL_MB', 20),
        'allowed_mime_types' => array_values(array_filter(array_map(
            'trim',
            explode(',', env(
                'INBOUND_EMAIL_ALLOWED_MIME_TYPES',
                'image/jpeg,image/png,image/gif,image/webp,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv'
            ))
        ))),
    ],

    'rate_limit_per_minute' => (int) env('INBOUND_EMAIL_RATE_LIMIT_PER_MINUTE', 30),
];
