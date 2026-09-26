<?php

return [
    'super_admin' => ['name' => 'Super Admin', 'email' => env('ADMIN_SUPER_EMAIL', 'super.admin@tapaklokal.test'), 'password' => env('ADMIN_SUPER_PASSWORD')],
    'content_admin' => ['name' => 'Content Admin', 'email' => env('ADMIN_CONTENT_EMAIL', 'content.admin@tapaklokal.test'), 'password' => env('ADMIN_CONTENT_PASSWORD')],
    'operations_admin' => ['name' => 'Operations Admin', 'email' => env('ADMIN_OPERATIONS_EMAIL', 'operations.admin@tapaklokal.test'), 'password' => env('ADMIN_OPERATIONS_PASSWORD')],
    'finance_admin' => ['name' => 'Finance Admin', 'email' => env('ADMIN_FINANCE_EMAIL', 'finance.admin@tapaklokal.test'), 'password' => env('ADMIN_FINANCE_PASSWORD')],
    'growth_admin' => ['name' => 'Growth Admin', 'email' => env('ADMIN_GROWTH_EMAIL', 'growth.admin@tapaklokal.test'), 'password' => env('ADMIN_GROWTH_PASSWORD')],
];
