<div class="card bg-white">
    <div class="card-header clearfix">
        <i data-feather="list" class="icon-16"></i> &nbsp;<?php echo app_lang("all_main_tasks_overview"); ?>
    </div>
    <div class="card-body rounded-bottom" id="all-taskss-overview-widget">
        <div class="row">
            <div class="col-md-6">
                <canvas id="all-taskss-overview-chart" style="width: 100%; height: 160px;"></canvas>
            </div>
            <div class="col-md-6 pl20 <?php echo count($task_statuses) > 8 ? "" : "pt-4"; ?>">
                <?php
                if(count($task_statuses)>0){
                foreach ($task_statuses as $task_status) {
                    ?>
                    <a href="<?php echo get_uri('tasks/all_tasks'); ?>" class="text-default">
                        <div class="pb-2">
                            <div class="color-tag border-circle me-3 wh10" style="background-color: <?php echo $task_status->is_closed==1 ?'#4a9d27':'#e50f16bd'; ?>;"></div><?php echo $task_status->is_closed==1 ? app_lang('open') : app_lang('closed'); ?>
                            <span class="strong float-end" style="color: <?php echo  $task_status->is_closed==1 ?'#4a9d27':'#e50f16bd'; ?>"><?php echo $task_status->total; ?></span>
                        </div>
                    </a>
                    <?php
                }
            }else {
                ?>
                <div class="pb-2">
                            <div class="color-tag border-circle me-3 wh10" ></div>No Data
                            <span class="strong float-end" >No Tasks</span>
                        </div>
            <?php } ?>
            
            </div>
        </div>
        
    </div>
</div>

<?php
$task_title = array();
$task_data = array();
$task_status_color = array();
foreach ($task_statuses as $task_status) {
    $task_title[] = $task_status->is_closed==1 ? app_lang('open') : app_lang('closed');
    $task_data[] = $task_status->total;
    $task_status_color[] = $task_status->is_closed==1 ?'#4a9d27':'#e50f16bd';
}
?>
<script type="text/javascript">
    //for task status chart
    var labels = <?php echo json_encode($task_title) ?>;
    var taskData = <?php echo json_encode($task_data) ?>;
    var taskStatuscolor = <?php echo json_encode($task_status_color) ?>;
    var allTasksOverviewChart = document.getElementById("all-taskss-overview-chart");
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

    $(document).ready(function () {
        initScrollbar('#all-taskss-overview-widget', {
            setHeight: 280
        });
    });

</script>