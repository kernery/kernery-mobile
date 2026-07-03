<?php

return [
    'default_page' => env('UI_DEFAULT_PAGE', '/'),
    'project_name' => env('APP_NAME', 'Kitchen Sink'),
    'app_max_execution_time' => env('MAX_EXECUTION_TIME', -1),
    'redirect_404_to_homepage' => env('REDIRECT_404_TO_HOME', false),
    'memory_limit' => env('APP_MEMORY_LIMIT', 300),
    'locale' => env('APP_LOCALE', 'en'),
    'disable_verify_csrf_token' => env('APP_DISABLE_VERIFY_CSRF_TOKEN', false),
    'enable_less_secure_app' => env('APP_ENABLE_LESS_SECURE_APP', false),
    'enable_ini_set' => env('APP_ENABLE_INI_SET', true),
    'using_uuids_for_id' => env('APP_USING_UUIDS_FOR_ID', true),
    'using_ulids_for_id' => env('APP_USING_ULIDS_FOR_ID', false),
    'type_id' => env('APP_USING_TYPE_ID', 'BIGINT'),
    'default_database_directory' => env('DEFAULT_DATABASE_DIRECTORY', base_path('mobile/database/database.sqlite')),
];
