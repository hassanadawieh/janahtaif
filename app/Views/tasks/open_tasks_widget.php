<?php echo form_open(get_uri("tasks/all_tasks"), array("id" => "task-form_".$my_option, "class" => "general-form", )); ?>
<input type="hidden" name="filter" value="<?php echo $my_option; ?>">

<div class="card dashboard-icon-widget"> <div class="card-body" style="padding-left: 10px;padding-right: 10px;"><button style="border: none;background: transparent;width: 100%; padding: 0;" type="submit" >
       
            <div class="widget-icon <?php echo $my_option=='no_invoice'?"bg-coral":($my_option == "tasks_deleted"?"bg-danger":"bg-info");?>">
                <i data-feather="alert-circle" class="icon"></i>
            </div>
            <div class="widget-details">
                <h1><?php echo $totals; ?></h1>
                <span class="bg-transparent-white"><?php echo $title; ?></span>
            </div>
        
     </button></div></div>
       
   
<?php echo form_close(); ?>



<script type="text/javascript">
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>