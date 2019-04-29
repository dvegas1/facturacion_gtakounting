<?php
/*

 */

if (!function_exists('get_gravatar')) {

    function get_gravatar($email, $size = 80)
    {
        return "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=" . $size;
    }
}

if (!function_exists('adminlte_menu_icon')) {

    function adminlte_menu_icon($value)
    {
        $icon = '<i class="fa fa-th-large"></i>';
        switch ($value) {
            case 'admin':
                $icon = '<i class="fa fa-flask"></i>';
                break;

            case 'compras':
                $icon = '<i class="fa fa-ship"></i>';
                break;

            case 'contabilidad':
                $icon = '<i class="fa fa-balance-scale"></i>';
                break;

            case 'CRM':
                $icon = '<i class="fa fa-address-book"></i>';
                break;

            case 'Expedientes':
                $icon = '<i class="fa fa-suitcase"></i>';
                break;

            case 'informes':
                $icon = '<i class="fa fa-bar-chart-o"></i>';
                break;

            case 'TPV':
                $icon = '<i class="fa fa-cc-visa"></i>';
                break;

            case 'ventas':
                $icon = '<i class="fa fa-shopping-cart"></i>';
                break;
        }
        return $icon;
    }
}

if (!function_exists('adminlte_page_icon')) {

    function adminlte_page_icon($value)
    {
        $icon = '<i class="fa fa-circle-o"></i>';
        if ($value->showing()) {
            $icon = '<i class="fa fa-check-circle"></i>';
        }
        switch ($value->name) {
            case 'admin_empresa':
                $icon = '<i class="fa fa-suitcase"></i>';
                break;

            case 'admin_home':
                $icon = '<i class="fa fa-cogs"></i>';
                break;

            case 'admin_prestashop':
            case 'pedidos_prestashop':
                $icon = '<i class="fa fa-shopping-bag"></i>';
                break;

            case 'admin_users':
            case 'admin_agentes':
            case 'compras_proveedores':
            case 'ventas_clientes':
                $icon = '<i class="fa fa-users"></i>';
                break;

            case 'admin_woocommerce':
                $icon = '<i class="fa fa-wordpress"></i>';
                break;

            case 'compras_articulos':
            case 'ventas_articulos':
                $icon = '<i class="fa fa-cubes"></i>';
                break;

            case 'contabilidad_ejercicios':
                $icon = '<i class="fa fa-calendar"></i>';
                break;

            case 'crm_contactos':
                $icon = '<i class="fa fa-address-book"></i>';
                break;

            case 'dashboard':
                $icon = '<i class="fa fa-dashboard"></i>';
                break;

            case 'informe_contabilidad':
                $icon = '<i class="fa fa-balance-scale"></i>';
                break;
        }
        return $icon;
    }
}

if (!function_exists('fs_honest_orig')) {

    function fs_honest_orig()
    {
        $fname = 'view/login/default.html';
        foreach ($GLOBALS['plugins'] as $plugin) {
            if (file_exists('plugins/' . $plugin . '/view/login/default.html')) {
                $fname = 'plugins/' . $plugin . '/view/login/default.html';
                break;
            }
        }

        $txt = file_get_contents($fname);
        if ($txt) {
            if (stripos($txt, 'facturascripts') === FALSE) {
                return FALSE;
            }
            
            return TRUE;
        }
        
        return FALSE;
    }
}

if (!function_exists('fs_fake_msg')) {

    function fs_fake_msg()
    {
        return base64_decode('PGgxPkZhY3R1cmFTY3JpcHRzPC9oMT48bWFyaz5Fc3TDoSB1c2Fu'
            . 'ZG8gZWwgc29mdHdhcmUgZGUgY8OzZGlnbyBhYmllcnRvIEZhY3R1cmFTY3JpcHRz'
            . 'LCBwZXJvIDxiPnN1IHByb3ZlZWRvciBsZSBoYSBjYW1iaWFkbyBlbCBub21icmU8'
            . 'L2I+LiBFcyBwcmVmZXJpYmxlIHF1ZSB1c2Ugc29mdHdhcmUgb3JpZ2luYWwsIGFj'
            . 'dHVhbGl6YWRvIHkgY29uIHNvcG9ydGUgZGUgbG9zIHByb2dyYW1hZG9yZXMgb3Jp'
            . 'Z2luYWxlcy48L21hcms+PHA+VmlzaXRlIDxhIGhyZWY9Imh0dHBzOi8vd3d3LmZh'
            . 'Y3R1cmFzY3JpcHRzLmNvbSI+ZmFjdHVyYXNjcmlwdHMuY29tPC9hPjwvcD4=');
    }
}