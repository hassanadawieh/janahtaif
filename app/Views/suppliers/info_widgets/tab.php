<?php
$card = "";
$icon = "";
$value = "";
$link = "";

$view_type = "client_dashboard";
if ($login_user->user_type == "staff") {
    $view_type = "";
}

if (!is_object($supplier_info)) {
    $supplier_info = new stdClass();
}


if ($tab == "tasks") {
    $card = "bg-info";
    $icon = "grid";
    //if (property_exists($supplier_info, "total_projects")) {
        $value = total_suppliers_tasks_widget($supplier_info->id);
    //}
   $link = get_uri('suppliers/view/' . $supplier_info->id.'/tasks');

} else if ($tab == "total_invoiced") {
    $card = "bg-primary";
    $icon = "file-text";
    if (property_exists($supplier_info, "invoice_value")) {
        $value = to_currency($supplier_info->invoice_value, $supplier_info->currency_symbol);
    }
    if ($view_type == "client_dashboard") {
        $link = get_uri('invoices/index');
    } else {
        $link = get_uri('clients/view/' . $supplier_info->id . '/invoices');
    }
} else if ($tab == "payments") {
    $card = "bg-success";
    $icon = "check-square";
    if (property_exists($supplier_info, "payment_received")) {
        $value = to_currency($supplier_info->payment_received, $supplier_info->currency_symbol);
    }
    if ($view_type == "client_dashboard") {
        $link = get_uri('invoice_payments/index');
    } else {
        $link = get_uri('clients/view/' . $supplier_info->id . '/payments');
    }
} else if ($tab == "due") {
    $card = "bg-coral";
    $icon = "compass";
    if (property_exists($supplier_info, "invoice_value")) {
        $value = to_currency(ignor_minor_value($supplier_info->invoice_value - $supplier_info->payment_received), $supplier_info->currency_symbol);
    }
    if ($view_type == "client_dashboard") {
        $link = get_uri('invoices/index');
    } else {
        $link = get_uri('clients/view/' . $supplier_info->id . '/invoices');
    }
}
?>

<a href="<?php echo $link; ?>" class="white-link">
    <div class="card dashboard-icon-widget">
        <div class="card-body">
            <div class="widget-icon <?php echo $card ?>">
                <i data-feather="<?php echo $icon; ?>" class="icon"></i>
            </div>
            <div class="widget-details">
                <h1><?php echo $value; ?></h1>
                <span class="bg-transparent-white"><?php echo app_lang($tab); ?></span>
            </div>
        </div>
    </div>
</a>