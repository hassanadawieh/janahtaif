<div class="row clearfix">
    <?php if ($show_invoice_info) { ?>
        <?php if (!in_array("tasks", $hidden_menu) && $show_project_info) { ?>
            <div class="col-md-3 col-sm-6 widget-container">
                <?php echo view("suppliers/info_widgets/tab", array("tab" => "tasks")); ?>
                <?php //echo total_suppliers_tasks_widget($supplier_info->id); ?>
                
            </div>
        <?php } ?>

       

    <?php } ?>
</div>