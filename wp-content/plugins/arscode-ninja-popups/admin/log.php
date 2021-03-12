<?php

if (!defined('ABSPATH')) {
    die('-1');
}

wp_enqueue_script('basicpluginscript', plugins_url( 'js/log_script.js' , __FILE__ ), array( 'jquery' ));

if (isset($_POST['delete']) && isset($_POST['checkbox_log'])) {
    $records = $_POST['checkbox_log'];
    $query = 'DELETE FROM '. $wpdb->prefix .'snp_log WHERE id='. $records[0];
    $n = count($records);
    for ($i = 1; $i<$n; $i++) {
        $query .= ' OR id='.$records[$i];
    }
    $wpdb->query($query);
}

if (isset($_POST['wipe'])) {
    $wpdb->query('DELETE FROM '. $wpdb->prefix .'snp_log');
}

if (isset($_GET['start']) && snp_is_valid_date($_GET['start'])) {
    $start = date('Y-m-d', strtotime($_GET['start']));
}

if (isset($_GET['end']) && snp_is_valid_date($_GET['end'])) {
    $end = date('Y-m-d', strtotime($_GET['end']));
}

$where = '1';
if (isset($start)) {
    $where .= ' AND `action_date`>="'.$start.'" ';
}

if (isset($end)) {
    $where .= ' AND DATE(action_date)<="'.$end.'" ';
}

$query = 'SELECT * FROM '. $wpdb->prefix .'snp_log WHERE '.$where.' ORDER BY action_date DESC';
$count_pages = $wpdb->get_row('SELECT COUNT(id) AS number FROM '. $wpdb->prefix .'snp_log WHERE '.$where);

if (isset($_GET['pages']) && !isset($_POST['period_button'])) {
    $pages = $_GET['pages'];
    $begin = ($pages-1) * 50;
    $query .= ' LIMIT '. $begin .', 50';
} else {
    $query .= ' LIMIT 50';
} 
?>

<h3>Log</h3>
<form method="GET" action="edit.php">
    <?php
    echo '<div class="smp-datepicker">';
    echo '<input type="hidden" name="post_type" value="snp_popups" />';
    echo '<input type="hidden" name="page" value="snp_log" />';
    echo '<label>From</label> <input type="text" name="start" value="'.(isset($start) ? $start : '').'"style="text-align: center;" class="datepicker-from snp-datepicker" />';
    echo '<label>To</label> <input type="text" name="end" value="'.(isset($end) ? $end : '').'"style="text-align: center;" class="datepicker-to snp-datepicker" />';
    echo '<input class="button button-primary button-large" type="submit" name="period_button" value="Show" />';
    echo '</div>';
    ?>
</form>
<form method="POST" action="" id="log_form">
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th class="manage-column column-snp_theme">Action</th>
                <th class="manage-column column-snp_theme">Date</th>
                <th class="manage-column column-snp_theme">Email</th>
                <th class="manage-column column-snp_theme">Popup</th>
                <th class="manage-column column-snp_theme">List</th>
                <th class="manage-column column-snp_theme">Browser</th>
                <th class="manage-column column-snp_theme">IP</th>
                <th class="manage-column column-snp_theme">Custom Fields</th>
                <th class="manage-column column-snp_theme">Referer</th>
                <th class="manage-column column-snp_theme">Errors</th>
                <th class="manage-column column-cb check-column"><input type="checkbox" class="log-checkbox" id="log_checkbox" ></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $wpdb->get_results($query);
            foreach ($result as $v) {
                echo '<tr><td>'. $v->action .'</td><td>'. $v->action_date .'</td><td>'. $v->email .'</td><td>'. $v->popup_id .'</td><td>'. $v->list .'</td><td>'. $v->browser .'</td><td>'. $v->ip .'</td><td>'. str_replace(array('"', '{', '}', ',', ':'), array('', '', '', '<br />', ': '), $v->custom_fields ).'</td><td>'.$v->referer .'</td><td>'.$v->errors .'</td><td><input type="checkbox" name="checkbox_log[]" class="snp_log_checkbox" value="'. $v->id .'"></td></tr>';
            }
            ?>
        </tbody>
    </table>
    <?php
    $paginate_args = array(
        'base'               => '%_%',
        'format'             => '?pages=%#%',
        'total'              => ceil(($count_pages->number)/50),
        'current'            => (isset($pages) && !isset($_POST['period_button']))?$pages:1,
        'show_all'           => False,
        'end_size'           => 3,
        'mid_size'           => 5,
        'prev_next'          => True,
        'prev_text'          => __('« Previous'),
        'next_text'          => __('Next »'),
        'type'               => 'plain',
        'add_args'           => False,
        'add_fragment'       => '',
        'before_page_number' => '',
        'after_page_number'  => ''
    );    
    echo paginate_links( $paginate_args );
    ?>
	<div class="smp-button">
        <input type="submit" name="delete" value="Delete Selected" class="button button-primary button-large">
        <input type="submit" name="wipe" value="Wipe All Logs" class="button button-primary button-large" >
	</div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".snp-datepicker").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });
</script>
<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-datepicker');
global $wp_scripts;
$ui = $wp_scripts->query('jquery-ui-core');
$protocol = is_ssl() ? 'https' : 'http';
$url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
wp_enqueue_style('jquery-ui-smoothness', $url, false, null);