<?php

namespace App\View\Composers;

use Illuminate\View\View;

class WarehouseComposer
{
    public function compose(View $view)
    {
        $menuItems = [
            ['path' => route('dashboard'), 'label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard'],
            ['divider' => 'Management'],
            ['path' => route('user.getTable'), 'label' => 'Users', 'icon' => 'manage_accounts', 'route' => 'user.*'],
        ];

        $view->with('menuItems', $menuItems);
    }
}
