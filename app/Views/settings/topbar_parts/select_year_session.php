<?php
$s_year=$session_selected_value? $session_selected_value:$selected_value;
$s_year=$s_year==1? "الكل ":$s_year;
$year_links = "";

         
         foreach ($years_dropdown as $years_dropdownr) {
         	$year_links .= '<li><a href="javascript:void(0);" data-value="'.$years_dropdownr->year.'" class="dropdown-item text-center align-items-center" >'.$years_dropdownr->year.'</a></li>';
              //js_anchor($years_dropdownr->year, array("id" => "quick-add-icon", "class" => "dropdown-item clearfix","data-value"=>$years_dropdownr->year));
         	

         }
         $year_links .= '<li><a href="javascript:void(0);" data-value="1" class="dropdown-item text-center align-items-center" >الكل</a></li>';
    ?>
    
    <li class="nav-item dropdown">
        <a href="javascript:void(0);" class=" nav-link dropdown-toggle badge" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="background-color: #8fadbb;color:#000;font-size:16px;padding-left: 5px !important;padding-right: 5px !important;padding-bottom: 5px !important;padding-top: 5px !important;margin-top: 15% !important;"><span style="font-size: 13px">السنة</span> <?php echo $s_year; ?></a>

        <ul class="dropdown-menu dropdown-menu-end" id="select_years_dropdown">
            
                <?php echo $year_links; ?>
        </ul>
    </li>
    
    <script type="text/javascript">
    	$("#select_years_dropdown li a").click(function() {
            $(this).parents(".dropdown").find('a.nav-link').html($(this).text());
            var selected_y = $(this).data('value');
            //alert(selected_y);
            $.ajax({
                url: '<?php echo_uri("subtasks/set_year") ?>',
                data: {value: selected_y},
                
                type: 'POST',
                success: function (response) {
                	console.log(response);
                	var mres=JSON.parse(response);
                	

                    if (mres.success) {
                        location.reload(true);
                       // $("#task-table").appTable({newData: response.data, dataId: response.id});
                    }
                }
            });


        });
    	
    </script>