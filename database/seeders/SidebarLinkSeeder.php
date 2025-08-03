<?php

namespace Database\Seeders;

use App\Models\SidebarLink;
use Illuminate\Database\Seeder;

class SidebarLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Clear existing sidebar links
        SidebarLink::truncate();
         $links = [
            [
                'title' => 'الرئيسية',
                'url' => 'dashboard',
                'icon' => 'fa fa-tachometer-alt',
                'permission' => null,
                'order' => 0,
            ],
            [
                'title' => 'موازنة الجمعية',
                'url' => 'finance-items',
                'icon' => 'fa fa-tags',
                'permission' => null,
                'order' => 1,
            ],
            [
                'title' => 'انواع الايرادات',
                'url' => 'income-categories',
                'icon' => 'fa fa-plus-circle',
                'permission' => null,
                'order' => 2,
            ],
            [
                'title' => 'انواع المصروفات',
                'url' => 'expense-categories',
                'icon' => 'fa fa-plus-circle',
                'permission' => null,
                'order' => 3,
            ],
            [
                'title' => 'الايرادات',
                'url' => 'incomes',
                'icon' => 'fa fa-credit-card',
                'permission' => null,
                'order' => 4,
            ],

            [
                'title' => 'المصروفات',
                'url' => 'expenses',
                'icon' => 'fa fa-minus-circle',
                'permission' => 'expense_access',
                'order' => 8,
            ],

            // [
            //     'title' => 'Expense Reports',
            //     'url' => 'admin/expense-reports',
            //     'icon' => 'fa fa-file-alt',
            //     'permission' => 'expense_report_access',
            //     'order' => 10,
            // ],
            
        ];

        foreach ($links as $link) {
            SidebarLink::updateOrCreate(
                ['url' => $link['url']],
                [
                    'title'       => $link['title'],
                    'icon'        => $link['icon'],
                    'permission'  => $link['permission'],
                    'visibility'  => 'authenticated',
                    'is_external' => false,
                    'is_active'   => true,
                    'order'       => $link['order'],
                ]
            );
        }

    }
} 