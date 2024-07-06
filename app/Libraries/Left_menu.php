<?php

namespace App\Libraries;

use App\Controllers\Security_Controller;

class Left_menu {

    private $ci = null;

    public function __construct() {
        $this->ci = new Security_Controller(false);
    }

    function get_available_items($type = "default") {
        $items_array = $this->_prepare_sidebar_menu_items($type);

        //remove used items
        $default_left_menu_items = $this->_get_left_menu_from_setting($type);

        foreach ($default_left_menu_items as $default_item) {
            unset($items_array[get_array_value($default_item, "name")]);
        }

        //since all menu items will be added to the customization area when there is no item, don't show anything here
        if (!$default_left_menu_items) {
            $items_array = array();
        }

        $items = "";
        foreach ($items_array as $item) {
            $items .= $this->_get_item_data($item, true);
        }

        return $items ? $items : "<span class='text-off empty-area-text'>" . app_lang('no_more_items_available') . "</span>";
    }

    private function _prepare_sidebar_menu_items($type = "", $return_sub_menu_data = false) {
        $final_items_array = array();
        $items_array = $this->_get_sidebar_menu_items($type);

        foreach ($items_array as $item) {
            $main_menu_name = get_array_value($item, "name");

            if (isset($item["submenu"])) {
                //first add this menu removing the submenus
                $main_menu = $item;
                unset($main_menu["submenu"]);
                $final_items_array[$main_menu_name] = $main_menu;

                $submenu = get_array_value($item, "submenu");
                foreach ($submenu as $key => $s_menu) {
                    //prepare help items differently
                    if ($main_menu_name == "help_and_support") {
                        $s_menu = $this->_make_customized_sub_menu_for_help_and_support($key, $s_menu);
                    }

                    if ($return_sub_menu_data) {
                        $s_menu["is_sub_menu"] = true;
                    }

                    if (get_array_value($s_menu, "class")) {
                        $final_items_array[get_array_value($s_menu, "name")] = $s_menu;
                    }
                }
            } else {
                $final_items_array[$main_menu_name] = $item;
            }
        }

        //add todo
        $final_items_array["todo"] = array("name" => "todo", "url" => "todo", "class" => "check-square");

        return $final_items_array;
    }

    private function _make_customized_sub_menu_for_help_and_support($key, $s_menu) {
        if ($key == 1) {
            $s_menu["name"] = "help_articles";
        } else if ($key == 2) {
            $s_menu["name"] = "help_categories";
        } else if ($key == 4) {
            $s_menu["name"] = "knowledge_base_articles";
        } else if ($key == 5) {
            $s_menu["name"] = "knowledge_base_categories";
        }

        return $s_menu;
    }

    private function _get_left_menu_from_setting_for_rander($is_preview = false, $type = "default") {
        $user_left_menu = get_setting("user_" . $this->ci->login_user->id . "_left_menu");
        $default_left_menu = ($type == "client_default" || $this->ci->login_user->user_type == "client") ? get_setting("default_client_left_menu") : get_setting("default_left_menu");
        $custom_left_menu = "";

        //for preview, show the edit type preview
        if ($is_preview) {
            $custom_left_menu = $default_left_menu; //default preview
            if ($type == "user") {
                $custom_left_menu = $user_left_menu ? $user_left_menu : $default_left_menu; //user level preview
            }
        } else {
            $custom_left_menu = $user_left_menu ? $user_left_menu : $default_left_menu; //page rander
        }

        return $custom_left_menu ? json_decode(json_encode(@unserialize($custom_left_menu)), true) : array();
    }

    private function _get_left_menu_from_setting($type) {
        if ($type == "client_default") {
            $default_left_menu = get_setting("default_client_left_menu");
        } else if ($type == "user") {
            $default_left_menu = get_setting("user_" . $this->ci->login_user->id . "_left_menu");
        } else {
            $default_left_menu = get_setting("default_left_menu");
        }

        return $default_left_menu ? json_decode(json_encode(@unserialize($default_left_menu)), true) : array();
    }

    public function _get_item_data($item, $is_default_item = false) {
        $name = get_array_value($item, "name");
        $language_key = get_array_value($item, "language_key");
        $url = get_array_value($item, "url");
        $is_sub_menu = get_array_value($item, "is_sub_menu");
        $open_in_new_tab = get_array_value($item, "open_in_new_tab");
        $icon = get_array_value($item, "icon");

        if ($name) {
            $sub_menu_class = "";
            $clickable_menu_class = "make-sub-menu";
            $clickable_icon = "<i data-feather='corner-right-down' class='icon-14'></i>";
            if ($is_sub_menu) {
                $sub_menu_class = "ml20";
                $clickable_menu_class = "make-root-menu";
                $clickable_icon = "<i data-feather='corner-up-left' class='icon-14'></i>";
            }

            $extra_attr = "";
            $edit_button = "";
            $name_lang = "";
            if ($is_default_item || !$url) {
                $name_lang = app_lang($name);
            } else {
                if ($language_key) {
                    $name_lang = app_lang($language_key);
                } else {
                    $name_lang = $name;
                }

                //custom menu item
                $extra_attr = "data-url='$url' data-icon='$icon' data-custom_menu_item_id='" . rand(2000, 400000000) . "' data-open_in_new_tab='$open_in_new_tab' data-language_key='$language_key'";
                $edit_button = modal_anchor(get_uri("left_menus/add_menu_item_modal_form"), "<i data-feather='edit' class='icon-14'></i> ", array("title" => app_lang('edit'), "class" => "custom-menu-edit-button", "data-post-title" => $name, "data-post-url" => $url, "data-post-is_sub_menu" => $is_sub_menu, "data-post-icon" => $icon, "data-post-open_in_new_tab" => $open_in_new_tab, "data-post-language_key" => $language_key));
            }

            return "<div data-value='" . $name . "' $extra_attr class='left-menu-item mb5 widget clearfix p10 bg-white $sub_menu_class'>
                        <span class='float-start text-start'><i data-feather='move' class='icon-16 text-off mr5'></i> " . $name_lang . "</span>
                        <span class='float-end invisible'>
                            <span class='clickable $clickable_menu_class toggle-menu-icon' title='" . app_lang("make_previous_items_sub_menu") . "'>$clickable_icon</span>
                            $edit_button
                            <span class='clickable delete-left-menu-item' title=" . app_lang("delete") . "><i data-feather='x' class='icon-14 text-danger'></i></span>
                        </span>
                    </div>";
        }
    }

    function get_sortable_items($type = "default") {
        $items = "<div id='menu-item-list-2' class='js-left-menu-scrollbar add-column-drop text-center p15 menu-item-list sortable-items-container'>";

        $default_left_menu_items = $this->_get_left_menu_from_setting($type);
        if (count($default_left_menu_items)) {
            foreach ($default_left_menu_items as $item) {
                $items .= $this->_get_item_data($item);
            }
        } else {
            //if there has no item in the customization panel, show the default items
            $items_array = $this->_prepare_sidebar_menu_items($type, true);
            foreach ($items_array as $item) {
                $items .= $this->_get_item_data($item, true);
            }
        }

        $items .= "</div>";

        return $items;
    }

    function rander_left_menu($is_preview = false, $type = "default") {
        $final_left_menu_items = array();
        $custom_left_menu_items = $this->_get_left_menu_from_setting_for_rander($is_preview, $type);

        if ($custom_left_menu_items) {
            $left_menu_items = $this->_prepare_sidebar_menu_items($type);
            $last_final_menu_item = ""; //store the last menu item of final left menu to add submenu to this item

            foreach ($custom_left_menu_items as $custom_left_menu_item) {
                $item_value_array = $this->_get_item_array_value($custom_left_menu_item, $left_menu_items);
                $is_sub_menu = get_array_value($custom_left_menu_item, "is_sub_menu");

                if ($is_sub_menu) {
                    //this is a sub menu, move it to it's parent item
                    $final_left_menu_items[$last_final_menu_item]["submenu"][] = $item_value_array;
                } else {
                    $final_left_menu_items[] = $item_value_array;
                    $last_final_menu_item = end($final_left_menu_items);
                    $last_final_menu_item = key($final_left_menu_items);
                }
            }
        }

        if (count($final_left_menu_items)) {
            $view_data["sidebar_menu"] = $final_left_menu_items;
        } else {
            $view_data["sidebar_menu"] = $this->_get_sidebar_menu_items($type);
        }

        $view_data["is_preview"] = $is_preview;
        $view_data["login_user"] = $this->ci->login_user;
        return view("includes/left_menu", $view_data);
    }

    private function _get_item_array_value($data_array, $left_menu_items) {
        $name = get_array_value($data_array, "name");
        $language_key = get_array_value($data_array, "language_key");
        $url = get_array_value($data_array, "url");
        $icon = get_array_value($data_array, "icon");
        $open_in_new_tab = get_array_value($data_array, "open_in_new_tab");
        $item_value_array = array();

        if ($url) { //custom menu item
            $item_value_array = array("name" => $name, "language_key" => $language_key, "url" => $url, "is_custom_menu_item" => true, "class" => "$icon", "open_in_new_tab" => $open_in_new_tab);
        } else if (array_key_exists($name, $left_menu_items)) { //default menu items
            $item_value_array = get_array_value($left_menu_items, $name);
        }

        return $item_value_array;
    }

    private function _get_sidebar_menu_items($type = "") {
        $dashboard_menu = array("name" => "dashboard", "url" => "dashboard", "class" => "monitor");

        $selected_dashboard_id = get_setting("user_" . $this->ci->login_user->id . "_dashboard");
        if ($selected_dashboard_id) {
            $dashboard_menu = array("name" => "dashboard", "url" => "dashboard/view/" . $selected_dashboard_id, "class" => "monitor", "custom_class" => "dashboard-menu");
        }

        if ($this->ci->login_user->user_type == "staff" && $type !== "client_default") {

            $sidebar_menu = array("dashboard" => $dashboard_menu);

            $permissions = $this->ci->login_user->permissions;

            $access_expense = get_array_value($permissions, "expense");
            $access_invoice = get_array_value($permissions, "invoice");
            $access_ticket = get_array_value($permissions, "ticket");
            $access_client = get_array_value($permissions, "client");
            $supplier_permission = get_array_value($permissions, "supplier_permission");
            $isSupply = get_array_value($permissions, "supply_mang");
            $isReserv = get_array_value($permissions, "reserv_mang");
            $can_show_subtasks_report = get_array_value($permissions, "can_show_subtasks_report");
            $drivers_permission = get_array_value($permissions, "drivers_permission");
            $car_type_permission = get_array_value($permissions, "car_type_permission");
            $access_leave = get_array_value($permissions, "leave");
            $access_estimate = get_array_value($permissions, "estimate");
            $access_contract = get_array_value($permissions, "contract");
            $access_proposal = get_array_value($permissions, "proposal");
            $access_order = get_array_value($permissions, "order");
            $access_items = ($this->ci->login_user->is_admin || $access_invoice || $access_estimate);

            $client_message_users = get_setting("client_message_users");
            $client_message_users_array = explode(",", $client_message_users);
            $access_messages = ($this->ci->login_user->is_admin || get_array_value($permissions, "message_permission") !== "no" || in_array($this->ci->login_user->id, $client_message_users_array));

            $manage_help_and_knowledge_base = ($this->ci->login_user->is_admin || get_array_value($permissions, "help_and_knowledge_base"));
            $access_timeline = ($this->ci->login_user->is_admin || get_array_value($permissions, "timeline_permission") !== "no");



            if ($this->ci->login_user->is_admin || $access_client) {
                $sidebar_menu["clients"] = array("name" => "clients", "url" => "clients", "class" => "briefcase");
            }

            if ($this->ci->login_user->is_admin || $supplier_permission) {
                $sidebar_menu["suppliers"] = array("name" => "suppliers", "url" => "suppliers", "class" => "user");
            }
            

            //if ($this->ci->login_user->is_admin) {
                $sidebar_menu["cities"] = array("name" => "cities", "url" => "cities", "class" => "book-open");
            //}

            $subtask_submenu = array();
            if ($this->ci->login_user->is_admin || $isReserv) {
            $subtask_submenu["reserv_mang"] = array("name" => "reserv_mang", "url" => "subtasks", "class" => "check-circle");
        }
             if ($this->ci->login_user->is_admin || $isSupply) {
                $subtask_submenu["supply_mang"] = array("name" => "supply_mang", "url" => "subtasks/supply_mang", "class" => "check-circle");
            }


            //if ($this->ci->login_user->is_admin) {
             if ($this->ci->login_user->is_admin || !get_array_value($this->ci->login_user->permissions, "do_not_show_projects")) {
                $sidebar_menu["projects"] = array("name" => "projects", "url" => "projects/all_projects", "class" => "grid");
            }
                $sidebar_menu["tasks"] = array("name" => "tasks", "url" => "tasks/all_tasks", "class" => "check-circle");
                if ($this->ci->login_user->is_admin || $can_show_subtasks_report) {
                $sidebar_menu["subtasks_report"] = array("name" => "subtasks_report", "url" => "subtasks/subtasks_report", "class" => "file-text");
            }

            if ($this->ci->login_user->is_admin || $drivers_permission) {
                $sidebar_menu["drivers"] = array("name" => "drivers", "url" => "drivers", "class" => "user");
            }
            if ($this->ci->login_user->is_admin || $car_type_permission) {
                $sidebar_menu["carstype"] = array("name" => "carstype", "url" => "carstype", "class" => "truck");
            }
            //}
             if ($this->ci->login_user->is_admin || $supplier_permission) {
                $sidebar_menu["cls_list"] = array("name" => "cls_list", "url" => "maintask_clsifications", "class" => "settings");
            }


            


            $show_payments_menu = false;
            $show_expenses_menu = false;

           


            $prospects_submenu = array();

           if (get_array_value($this->ci->login_user->permissions, "hide_team_members_list") != "1") {
           $sidebar_menu["team_members"] = array("name" => "team_members", "url" => "team_members", "class" => "users");
       }
            if (get_setting("module_note") == "1") {
                $sidebar_menu["notes"] = array("name" => "notes", "url" => "notes", "class" => "book");
            }

            if (get_setting("module_message") == "1" && $access_messages) {
                $sidebar_menu["messages"] = array("name" => "messages", "url" => "messages", "class" => "message-circle", "badge" => count_unread_message(), "badge_class" => "bg-primary");
            }



            $team_submenu = array();

            


          
           


            
            


           
            



            $module_knowledge_base = get_setting("module_knowledge_base") == "1" ? true : false;

            



            if ($this->ci->login_user->is_admin || get_array_value($this->ci->login_user->permissions, "can_manage_all_kinds_of_settings")) {
                $sidebar_menu["settings"] = array("name" => "settings", "url" => "settings/general", "class" => "settings");
            }

            $sidebar_menu = app_hooks()->apply_filters('app_filter_staff_left_menu', $sidebar_menu);
        } else {
            //client menu
            //get the array of hidden menu
            $hidden_menu = explode(",", get_setting("hidden_client_menus"));

            $sidebar_menu[] = $dashboard_menu;

            if (get_setting("module_event") == "1" && !in_array("events", $hidden_menu)) {
                $sidebar_menu[] = array("name" => "events", "url" => "events", "class" => "calendar");
            }

            //check message access settings for clients
            if (get_setting("module_message") && get_setting("client_message_users")) {
                $sidebar_menu[] = array("name" => "messages", "url" => "messages", "class" => "message-circle", "badge" => count_unread_message());
            }

            if (!in_array("projects", $hidden_menu)) {
                $sidebar_menu[] = array("name" => "projects", "url" => "projects/all_projects", "class" => "grid");
            }
            if (!in_array("subtasks", $hidden_menu)) {
                $sidebar_menu["subtasks"] = array("name" => "subtasks", "url" => "subtasks", "class" => "check-circle");
            }

            

            if (get_setting("module_contract") && !in_array("contracts", $hidden_menu)) {
                $sidebar_menu[] = array("name" => "contracts", "url" => "contracts", "class" => "book-open");
            }

            if (get_setting("module_proposal") && !in_array("proposals", $hidden_menu)) {
                $sidebar_menu[] = array("name" => "proposals", "url" => "proposals", "class" => "coffee");
            }

            if (get_setting("module_estimate") && !in_array("estimates", $hidden_menu)) {
                $sidebar_menu[] = array("name" => "estimates", "url" => "estimates", "class" => "file");
            }

            if (get_setting("module_invoice") == "1") {
                if (!in_array("invoices", $hidden_menu)) {
                    $sidebar_menu[] = array("name" => "invoices", "url" => "invoices", "class" => "file-text");
                }
                if (!in_array("payments", $hidden_menu)) {
                    $sidebar_menu[] = array("name" => "invoice_payments", "url" => "invoice_payments", "class" => "dollar-sign");
                }
            }

            if (!in_array("store", $hidden_menu) && get_setting("client_can_access_store")) {
                $sidebar_menu[] = array("name" => "store", "url" => "items/grid_view", "class" => "truck");
                $sidebar_menu[] = array("name" => "orders", "url" => "orders", "class" => "shopping-cart");
            }

            if (get_setting("module_ticket") == "1" && !in_array("tickets", $hidden_menu)) {
                $sidebar_menu[] = array("name" => "tickets", "url" => "tickets", "class" => "life-buoy");
            }

            if (get_setting("module_announcement") == "1" && !in_array("announcements", $hidden_menu)) {
                $sidebar_menu[] = array("name" => "announcements", "url" => "announcements", "class" => "bell");
            }

            $sidebar_menu[] = array("name" => "users", "url" => "clients/users", "class" => "users");

            if (get_setting("client_can_view_files")) {
                $sidebar_menu[] = array("name" => "files", "url" => "clients/files/" . $this->ci->login_user->id . "/page_view", "class" => "image");
            }

            $sidebar_menu[] = array("name" => "my_profile", "url" => "clients/contact_profile/" . $this->ci->login_user->id, "class" => "settings");

            if (get_setting("module_knowledge_base") == "1" && !in_array("knowledge_base", $hidden_menu)) {
                $sidebar_menu[] = array("name" => "knowledge_base", "url" => "knowledge_base", "class" => "help-circle");
            }

            $sidebar_menu = app_hooks()->apply_filters('app_filter_client_left_menu', $sidebar_menu);
        }

        return $this->position_items_for_default_left_menu($sidebar_menu);
    }

    //position items for plugins
    private function position_items_for_default_left_menu($sidebar_menu = array()) {
        foreach ($sidebar_menu as $key => $menu) {
            $position = get_array_value($menu, "position");
            if ($position) {
                $position = $position - 1;
                $sidebar_menu = array_slice($sidebar_menu, 0, $position, true) +
                        array($key => $menu) +
                        array_slice($sidebar_menu, $position, NULL, true);
            }
        }

        return $sidebar_menu;
    }

}
