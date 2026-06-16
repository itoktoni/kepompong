<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Menu Configuration
    |--------------------------------------------------------------------------
    |
    | Define menu items for desktop sidebar, mobile drawer, and bottom nav.
    | Each item: route (string), icon (string), label (string)
    | Sections: label (string), items (array)
    | Bottom nav: only 5 items max, uses short label
    |
    */

    'sidebar' => [
        [
            'label' => null,
            'items' => [
                ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                ['route' => 'user.getTable', 'icon' => 'manage_accounts', 'label' => 'Users'],
                ['route' => 'subscribe.getTable', 'icon' => 'subscriptions', 'label' => 'Subscribes'],
                ['route' => 'plan.getTable', 'icon' => 'payments', 'label' => 'Plans'],
                ['route' => 'payment.getTable', 'icon' => 'receipt', 'label' => 'Payments'],
                ['route' => 'cashout.getTable', 'icon' => 'account_balance_wallet', 'label' => 'Cashouts'],
                ['route' => 'affiliate.getTable', 'icon' => 'group', 'label' => 'Affiliates'],
                ['route' => 'activity.getTable', 'icon' => 'local_activity', 'label' => 'Activities'],
                ['route' => 'discount.getTable', 'icon' => 'percent', 'label' => 'Discounts'],
            ],
        ],
        [
            'label' => 'Settings',
            'items' => [
                ['route' => 'profile.edit', 'icon' => 'person', 'label' => 'My Profile'],
                ['route' => 'settings.env', 'icon' => 'settings', 'label' => 'Environment'],
            ],
        ],
    ],

    'bottom_nav' => [
        ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Home'],
        ['route' => 'profile.edit', 'icon' => 'person', 'label' => 'Profile'],
    ],

];
