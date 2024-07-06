<?php

try {
    $db = db_connect('default');
    $dbprefix = $db->getPrefix();

    $upgrade_sql = "upgrade-3.3.sql";
    $sql = file_get_contents($upgrade_sql);

    if ($dbprefix) {
        $sql = str_replace('ALTER TABLE `', 'ALTER TABLE `' . $dbprefix, $sql);
    }

    foreach (explode(";#", $sql) as $query) {
        $query = trim($query);
        if ($query) {
            try {
                $db->query($query);
            } catch (\Exception $ex) {
                log_message("error", $ex->getTraceAsString());
            }
        }
    }

    unlink($upgrade_sql);
   
} catch (\Exception $exc) {
    log_message("error", $exc->getTraceAsString());
    echo $exc->getTraceAsString();
}