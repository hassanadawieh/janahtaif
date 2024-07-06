<?php 

if ($login_user->user_type == "staff") {
            if (get_array_value($login_user->permissions, "supply_mang") == "1") {
                //check is user a project member
               $my_url = "supply_mang";
            }else if (get_array_value($login_user->permissions, "reserv_mang") == "1") {
                //check is user a project member
               $my_url = "index";
           }else{
            $my_url ="index";
           }
        } 
        

        ?>
<div class="card bg-white">
    <div class="card-header clearfix">
        <i data-feather="list" class="icon-16"></i> &nbsp;<?php echo app_lang("all_tasks_overview"); ?>
        <div class="float-end">
                    <span class="float-end dropdown">
                        <div class="dropdown-toggle clickable" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="ml10 mr10"><i data-feather="more-horizontal" class="icon-16"></i></span>
                        </div>
                        <ul class="dropdown-menu" role="menu" style="">
                            <li>
                                <a href="#" class="dropdown-item load-subtask-status" data-value="reserv" data-currency-symbol=" SR "><?=app_lang("reserv_mang")?></a><a href="#" class="dropdown-item load-subtask-status" data-value="supply" data-currency-symbol=""><?=app_lang("supply_mang")?></a>                            </li>
                        </ul>
                    </span>
                </div>
    </div>
    <div class="card-body rounded-bottom" id="all-tasks-overview-widget">
        <div class="row">
            <div class="col-md-6">
                <canvas id="all-tasks-overview-chart" style="width: 100%; height: 160px;"></canvas>
            </div>
            <div class="col-md-6 pl20 <?php echo count($task_statuses) > 8 ? "" : "pt-4"; ?>">
                <?php
                foreach ($task_statuses as $task_status) {
                    ?>
                    <a href="<?php echo get_uri('subtasks/'.$my_url.'/0/tasks_list/' . $task_status->dynamic_status_id); ?>" class="text-default">
                        <div class="pb-2">
                            <div class="color-tag border-circle me-3 wh10" style="background-color: <?php echo $task_status->color; ?>;"></div><?php echo $task_status->key_name ? app_lang($task_status->key_name) : $task_status->title; ?>
                            <span class="strong float-end" style="color: <?php echo $task_status->color; ?>"><?php echo $task_status->total; ?></span>
                        </div>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
        
    </div>
</div>

<?php
$task_title = array();
$task_data = array();
$task_status_color = array();
foreach ($task_statuses as $task_status) {
    $task_title[] = $task_status->key_name ? app_lang($task_status->key_name) : $task_status->title;
    $task_data[] = $task_status->total;
    $task_status_color[] = $task_status->color;
}
?>
<script type="text/javascript">
    //for task status chart
    function ddf(){
    var labels = <?php echo json_encode($task_title) ?>;
    var taskData = <?php echo json_encode($task_data) ?>;
    var taskStatuscolor = <?php echo json_encode($task_status_color) ?>;
    var allTasksOverviewChart = document.getElementById("all-tasks-overview-chart");
    new Chart(allTasksOverviewChart, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [
                {
                    data: taskData,
                    backgroundColor: taskStatuscolor,
                    borderWidth: 0
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 87,
            tooltips: {
                callbacks: {
                    title: function (tooltipItem, data) {
                        return data['labels'][tooltipItem[0]['index']];
                    },
                    label: function (tooltipItem, data) {
                        return "";
                    },
                    afterLabel: function (tooltipItem, data) {
                        var dataset = data['datasets'][0];
                        var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][Object.keys(dataset["_meta"])[0]]['total']) * 100);
                        return '(' + percent + '%)';
                    }
                }
            },
            legend: {
                display: false
            },
            animation: {
                animateScale: true
            }
        }
    });
}

    $(document).ready(function () {

        initScrollbar('#all-tasks-overview-widget', {
            setHeight: 280
        });

ddf();
       /* $(".load-subtask-status").click(function () {
            var mang = $(this).attr("data-value");
            var currencySymbol = $(this).attr("data-currency-symbol");
            alert(mang);
            $.ajax({
                    
            url: '<?php //echo_uri("subtasks/get_status_charts") ?>/' +mang,
            type: "POST",
            data: {value: 2},
            success: function (response, newValue) {
                var obj = JSON.parse(response);
                if (result.success) {
                        $("#all-tasks-overview-widget").html(result.statistics);
                    }
                    //ddf();
                //$("#task-table").appTable({newData: obj.data, dataId: obj.id});
            }
        });
        });*/


    });

</script>

