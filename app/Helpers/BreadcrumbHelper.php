<?php

namespace App\Helpers;

/**
 * Generate a breadcrumb
 *
 * @param  array  $list
 * @return string
 */

class BreadcrumbHelper {
    public static function generateList($list)
    {
        $breadcrumbs = '';
        foreach ($list as $url => $name) {
            $breadcrumbs .= '<li class="breadcrumb-item"><a href="' . $url . '">' . $name . '</a></li>';
        }
        
        return $breadcrumbs;
    }
}
