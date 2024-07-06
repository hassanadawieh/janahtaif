<div id="page-content" class="page-wrapper clearfix dashboard-view">

    <?php
    if (count($dashboards)) {
        echo view("dashboards/dashboard_header");
    }
    ?>

    <div class="clearfix row">
        <div class="col-md-12 widget-container">
            <?php
            //echo announcements_alert_widget();

            app_hooks()->do_action('app_hook_dashboard_announcement_extension');
            ?>
        </div>
    </div>

    <?php
    /*if ($widget_columns) {
        echo $widget_columns;
    } else {
        echo view("dashboards/custom_dashboards/no_widgets");
    }

    $dashboard_id = isset($dashboard_id) ? $dashboard_id : 0;*/
    ?>
    <?php 
    $is_show=false;
        
            if ($login_user->is_admin || (get_array_value($login_user->permissions, "can_manage_all_projects") == "1")) {
                
                $is_show=true;
            }else{
                if ((get_array_value($login_user->permissions, "reserv_mang") == "1")) {

                $is_show=true;
            }else if (get_array_value($login_user->permissions, "supply_mang") == "1") {
                //check is user a project member
               $is_show=false;
            }else if (get_array_value($login_user->permissions, "reserv_mang") == "1") {
                //check is user a project member
              $is_show=true;
           }
       
        } 
        

        ?>


          
                <div class="dashboards-row clearfix row">
                    <?php if($is_show){ ?>
                    <div class="col-md-12">
                    <div class="row">

                    
                    <div class="col-md-3">
                        <?php echo tasks_count_widget("no_invoice",app_lang('tasks_without_inv')); ?>
                            
                    </div>

                    <div class="col-md-3">
                        <?php echo tasks_count_widget("no_christening_number",app_lang('tasks_without_chri')); ?>
                            
                    </div>

                    <div class="col-md-3">
                        <?php echo tasks_count_widget("no_project",app_lang('tasks_without_project')); ?>
                            
                    </div>
                
                     <div class="widget-container col-md-3">
                            <?php echo my_subtasks_list_24hour(); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

               
                        <div class="widget-container col-md-4">
                            <?php echo all_tasks_overview_widget(); ?>
                        </div>
                        <div class="widget-container col-md-4">
                            <?php echo task_filter_overview_widget(); ?>
                        </div>
                        <div class="widget-container col-md-4">
                            <?php echo main_tasks_overview_widget("h370"); ?>
                        </div>
                       
                       
                       <?php echo my_tasks_list_widget(); ?>
                        

                        <div class="widget-container col-md-4">
                            <?php echo sticky_note_widget("h370"); ?>
                        </div>
                    </div>


                   


    

</div>

<?php echo view("dashboards/helper_js"); ?>

<script>
    $(document).ready(function () {
        //we have to reload the same page when editting title
        $("#dashboard-edit-title-button").click(function () {
            window.dashboardTitleEditMode = true;
        });

        //update dashboard link
        $(".dashboard-menu, .dashboard-image").closest("a").attr("href", window.location.href);

        onDashboardDeleteSuccess = function (result, $selector) {
            window.location.href = "<?php echo get_uri("dashboard"); ?>";
        };

        initScrollbar('#project-timeline-container', {
            setHeight: 719
        });

        initScrollbar('#upcoming-event-container', {
            setHeight: 330
        });

        initScrollbar('#client-projects-list', {
            setHeight: 316
        });

<?php if ($dashboard_id && $dashboard_id === get_setting("staff_default_dashboard") && $login_user->user_type === "staff") { ?>
            $(".dashboards-row").each(function () { //each widgets row
                var $rowInstance = $(this),
                        totalColumns = $rowInstance.find(".widget-container").length,
                        invalidWidgetRemoved = false;

                //remove invalid widgets and columns
                $rowInstance.find(".widget-container").each(function () { //each widgets column
                    var invalidWidget = $(this).find(".dashboard-invalid-widget");

                    if (invalidWidget) { //has invalid widget in this column
                        invalidWidget.remove(); //remove invalid widget
                        if ($(this).text() === '') { //if there is nothing else in this column the remove the column
                            $(this).remove();
                            invalidWidgetRemoved = true; //flag an invalid widget removed, to prevent extra operations
                        }
                    }
                });

                if (invalidWidgetRemoved) {
                    var totalNewColumns = $rowInstance.find(".widget-container").length,
                            columnsArray = {1: 12, 2: 6, 3: 4, 4: 3};

                    if (totalColumns !== totalNewColumns) { //any column has been totally removed in this row
                        $rowInstance.find(".widget-container").each(function () {
                            $(this).addClass("col-md-" + columnsArray[totalNewColumns]); //apply the appropriate column class
                        });
                    }
                }
            });
<?php } ?>

    });
</script>