<?php

echo '<h3>'.__('Analytics', 'nhp-opts').'</h3>';
echo '<div class="snp-library-cont">';

if (isset($_REQUEST['popup_ID'])) {
	$popup_ID = htmlspecialchars(addslashes($_REQUEST['popup_ID']));
}

$Popups = snp_get_popups();
$ABTesting = snp_get_ab();
$Bars=(array)$Popups + (array)$ABTesting;

if (isset($_REQUEST['start']) && snp_is_valid_date($_REQUEST['start'])) {
	$start = date('Y-m-d', strtotime($_REQUEST['start']));
}

if (isset($_REQUEST['end']) && snp_is_valid_date($_REQUEST['end'])) {
	$end = date('Y-m-d', strtotime($_REQUEST['end']));
}

if (isset($popup_ID)) {
	$table_name = $wpdb->prefix . "snp_stats";
	
	$where = '';
	$where2 = '';
	
	if (strpos($popup_ID, 'ab_') !== FALSE) {
		$AB = true;
		$where = "AB_ID = '".str_replace('ab_', '', $popup_ID)."'"; 
	} else {
		$where = "ID = '$popup_ID'";
	}

	if (isset($start)) {
		$where2 .= ' AND `date`>="'.$start.'" ';
	}

	if (isset($end)) {
		$where2 .= ' AND `date`<="'.$end.'" ';
	}

	$stats_sum = $wpdb->get_results("
		SELECT SUM(imps) as imps, SUM(convs) as convs, FORMAT((SUM(convs)/SUM(imps))*100,2) as rate
		FROM $table_name
		WHERE $where $where2
	");

	$stats = $wpdb->get_results("
		SELECT date, SUM(imps) as imps, SUM(convs) as convs, FORMAT((SUM(convs)/SUM(imps))*100,2) as rate
		FROM $table_name
		WHERE $where $where2
		GROUP BY date
		ORDER BY date ASC
	");
}

echo '<div id="snp-library-purchasecode" style="padding: 25px;">';
echo '<form method="post">';
echo '<div>';
echo '<label>Select Popup</label> <select name="popup_ID">';
echo '<option '.((!isset($popup_ID) || $popup_ID=='') ? 'selected' : '').' value="">-- select --</option>';

foreach($Bars as $ID => $Name) {
	echo '<option '.((isset($popup_ID) && $popup_ID==$ID )? 'selected' : '').' value="'.$ID.'">'.$Name.'</option>';
}

echo '</select>';
    echo '</div>';
    echo '<div>';
    echo '<label>From</label> <input type="text" name="start" value="'.(isset($start) ? $start : '').'" style="text-align: center;" class="snp-datepicker" />';
    echo '</div>';
    echo '<div>';
    echo '<label>To</label> <input type="text" name="end" value="'.(isset($end) ? $end : '').'"style="text-align: center;" class="snp-datepicker" />';
    echo '</div>';
    echo '<div>';
    echo '<input class="button button-primary button-large" type="submit" value="Show" />';
    echo '</div>';
    echo '</form>';
    echo '<script type="text/javascript"> jQuery(document).ready(function(){ jQuery(".snp-datepicker").datepicker({dateFormat: "yy-mm-dd"});  }); </script>';
    echo '</div>';
    if (isset($stats) && $stats)
    {
        echo '<div id="snp-library-elements">';
        echo '<div class="snp-library-elements-import" style="margin: 25px 0;">';
	echo '<div class="snp-library-elements-uptodate"><h4 style="margin: 0;">'.__('Impressions:', 'nhp-opts').' '.$stats_sum[0]->imps.' / '.__('Conversions:', 'nhp-opts').' '.$stats_sum[0]->convs.' / '.__('Rate:', 'nhp-opts').' '.$stats_sum[0]->rate.'%</h4></div>';
        echo '</div>';
        echo '</div>';
	?>
	<div id="chart_div_main" style="width: 100%; height: 600px;"></div>
	<?php
	if($AB)
	{
	    echo '<h3>'.__('Impressions', 'nhp-opts').'';
	    echo '<div id="chart_div_i" style="width: 100%; height: 600px;"></div>';
	    echo '<h3>'.__('Conversions', 'nhp-opts').'';
	    echo '<div id="chart_div_c" style="width: 100%; height: 600px;"></div>';
	}
	?>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">
	  google.load("visualization", "1", {packages:["corechart"]});
	  google.setOnLoadCallback(drawChart);
	  function drawChart() 
	  {
	    var data = new google.visualization.DataTable();
	    data.addColumn('string', 'Date');
	    data.addColumn('number', 'Impressions');
	    data.addColumn('number', 'Conversions');
	    data.addRows([
	    <?php
	    $i=1;
	    foreach ($stats as $data)
	    {
		if ($i!=1) echo ',';
		echo "['".$data->date."', {v: ".$data->imps."}, {v: ".$data->convs.", f: '".$data->convs." (".$data->rate."%)'}]"; 
		$i++;
	    }
	    ?>
	    ]);
	    var options = {
	      title: '',
	      hAxis: {title: '', titleTextStyle: {color: '#333'}},
	      vAxis: {minValue: 0, gridlines:{count: -1}}
	    };
	    var chart = new google.visualization.AreaChart(document.getElementById('chart_div_main'));
	    chart.draw(data, options);
	    <?php
	    if($AB)
	    {
		?>
		var data_i = new google.visualization.DataTable();
		data_i.addColumn('string', 'Date');
		var data_c = new google.visualization.DataTable();
		data_c.addColumn('string', 'Date');
		<?php
		$AB_META = get_post_meta(str_replace('ab_', '', $popup_ID));
		$stat_arr = array();
		if(isset($AB_META['snp_forms']))
		{
		    $snp_forms = array_keys(unserialize($AB_META['snp_forms'][0]));
		    $IDs=array();
		    foreach($snp_forms as $ID)
		    {
			echo "data_i.addColumn('number', '".get_the_title($ID)."');";
			echo "data_c.addColumn('number', '".get_the_title($ID)."');";
			$IDs[]=$ID;
			$where1 = " AND ID = '$ID' ";
			$stats2 = $wpdb->get_results(
			"
			SELECT date,SUM(imps) as imps, SUM(convs) as convs,FORMAT(SUM(convs)/SUM(imps),2) as rate
			FROM $table_name
			WHERE $where $where1 $where2
			GROUP BY date
			ORDER BY date ASC
			"
			);
			if ($stats2)
			{
			    foreach ($stats2 as $data)
			    {
				$stat_arr['imps'][$data->date][$ID]=$data->imps;
				$stat_arr['convs'][$data->date][$ID]=$data->convs;
				$stat_arr['rate'][$data->date][$ID]=$data->rate;
			    }
			}
		    }
		    ?>
		    data_i.addRows([
		    <?php
		    $i=1;
		    foreach ($stat_arr['imps'] as $date => $data)
		    {
			if ($i!=1) echo ',';
			echo "['".$date."'";
			foreach ($IDs as $ID)
			{
			    echo ", {v: ". ($stat_arr['imps'][$date][$ID] ? $stat_arr['imps'][$date][$ID] : '0') ."}";
			}
			echo "]"; 
			$i++;
		    }
		    ?>
		    ]);
		    data_c.addRows([
		    <?php
		    $i=1;
		    foreach ($stat_arr['convs'] as $date => $data)
		    {
			if ($i!=1) echo ',';
			echo "['".$date."'";
			foreach ($IDs as $ID)
			{
			    echo ", {v: ". ($stat_arr['convs'][$date][$ID] ? $stat_arr['convs'][$date][$ID].", f: '".$stat_arr['convs'][$date][$ID]." (".$stat_arr['rate'][$date][$ID]."%)'" : '0') ."}";
			}
			echo "]"; 
			$i++;
		    }
		    ?>
		    ]);
		    <?php
		}
		?>
		var chart_i = new google.visualization.AreaChart(document.getElementById('chart_div_i'));
		chart_i.draw(data_i, options);
		var chart_c = new google.visualization.AreaChart(document.getElementById('chart_div_c'));
		chart_c.draw(data_c, options);
		<?php
	    }
	    ?>
	  }
	</script>
	<?php
    }
    else
    {
	echo '<div class="error"><p><strong>'.__('Nothing to show.', 'nhp-opts').'</strong></p></div>';
    }
    echo '</div>';
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    global $wp_scripts;
    $ui = $wp_scripts->query('jquery-ui-core');
    // tell WordPress to load the Smoothness theme from Google CDN
    $protocol = is_ssl() ? 'https' : 'http';
    $url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
    wp_enqueue_style('jquery-ui-smoothness', $url, false, null);