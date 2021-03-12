<h3>Popups Library</h3>
<?php

if (isset($_GET['import_xml'])) {
    $id = intval($_GET['import_xml']);
    
    if ($id) {
        require_once 'wordpress-importer.php';
        
        $WP_Import = new WP_Import();
        $xml = wp_remote_get(SNP_API_URL.'ninja_popups_library/popup.php?id='.$id.'&site='.urlencode(get_bloginfo('url')).'&pc='.snp_get_option('purchasecode'));
        
        if ($xml['response']['code'] == 200) {
            $upload_dir = wp_upload_dir();
            $library_dir = $upload_dir['basedir'].'/'.SNP_LIBRARY_DIR;
            $tmpfname = tempnam($library_dir, "WP");
            $handle = fopen($tmpfname, "w");
            fwrite($handle, $xml['body']);
            fclose($handle);
            $WP_Import->import($tmpfname);
            unlink($tmpfname);
        } else if ($xml['response']['code'] == 403) {
            echo '<strong>Error: '.$xml['body'].'</strong>';
        } else {
            echo '<strong>Error: Connection problem!</strong>';
        }

        return; 
    }
}

if (isset($_GET['import_elements'])) {    
    $snp_library_ver = get_option( 'snp_library_ver' );
    $elements = wp_remote_get(SNP_API_URL.'ninja_popups_library/elements.php?site='.urlencode(get_bloginfo('url')).'&pc='.snp_get_option('purchasecode'));
    
    if ($elements['response']['code'] == 200) {
        $errors = false;
        $elements = json_decode($elements['body'],true);
        if ($snp_library_ver !== false && version_compare($snp_library_ver, $elements['ver'],'==')) {
            echo '<strong>Library is up to date.</strong>';

            return;
        }

        $el_list = array(
            'images_cats' => 'Images Categories',
            'images' => 'Images'
        );

        foreach($el_list as $el => $name) {
            if (isset($elements[$el])) {
                echo '<strong>'.$name.'</strong>: ';
                if ($el == 'images') {
                    echo '<br />';
                    
                    $count = count($elements[$el]); $i = 0;
                    echo '<div class="snp_progressbar" id="pb_'.$el.'"><div class="snp_progress" style="width: 1%;"><p>1%</p></div></div>';
                    echo '<a href="#" onclick="jQuery(this).hide().next().show(); return false;">Show log.</a>';
                    echo '<div class="snp_progresslog" id"=pl_'.$el.'">';
                    
                    foreach($elements[$el] as $img) {
                        $rimg = snp_library_fetch_remote_image($img['name']);
                        if (!is_wp_error($rimg) && ($rimg===true || $rimg['error'] === false)) {
                            echo '&nbsp;&nbsp;&nbsp;'.$img['name'].': OK</br />';
                        } else {
                            echo '&nbsp;&nbsp;&nbsp;'.$img['name'].': ERROR</br />';
                            if (is_wp_error($rimg)) {
                                echo '&nbsp;&nbsp;&nbsp;'.$rimg->get_error_message().'</br />';
                            }

                            $errors = true;
                        }

                        $p = round(100*++$i/$count);
                        
                        echo "<script>jQuery('#pb_".$el." .snp_progress').css('width','".$p."%').find('p').text('".$p."%');</script>";
                    }
                    echo '</div>';
                } else {
                    echo 'OK';
                }

                update_option('snp_library_'.$el, $elements[$el]);
                
                echo '<br />';
            }
        }

        if ($errors == false) {
            update_option('snp_library_ver',$elements['ver']);
        } else {
            echo '<div class="snp_error"><strong>An error occurred while importing. Try again later or contact with support.</strong></div>';
        }
    } else if ($elements['response']['code'] == 403) {
        echo '<strong>Error: '.$elements['body'].'</strong>';
    } else {
           echo '<strong>Error: Connection problem!</strong>';        
    }

    echo '<div style="text-align: center;"><a class="button button-primary" href="'. admin_url( 'edit.php?post_type=snp_popups&page=snp_library' )  .'">Back to Popups Library!</a></div>';
    echo '</div>';
    
    return;
}

snp_init_fontawesome();
?>
<div class="snp-library-cont">
    <div id="snp-library-purchasecode">
            <div class="snp-library-purchasecode-left">
                <strong>Enter Purchase Code</strong>
                <br />
                <span>To import & images element library and popups presets please enter Purchase Code</span>
                <br />
                <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-can-I-find-my-Purchase-Code-" target="_blank">Where can I find my Purchase Code?</a>
            </div>
            <div class="snp-library-purchasecode-right">
                <input type="text" id="purchasecode" value="<?php if(snp_get_option('purchasecode')) {echo snp_get_option('purchasecode'); }?>" />
                <button class="button button-primary" id="purchasecode_check">Verify & Save</button>
            </div>
        <?php
            echo '<script>jQuery(document).ready(function(){jQuery(\'#purchasecode_check\').click(function(){';
            echo "jQuery.ajax({";
            echo "	url: ajaxurl,";
            echo "	data:{";
            echo "		'action': 'snp_purchasecode_check',";
            echo "		'purchasecode': jQuery('#purchasecode').val(),";
            echo "		'save': true";
            echo "	},";
            echo "	type: 'POST',";
            echo "	success:function(response){";
            echo "	    alert(response);";
            echo "	},";
            echo "	error: function(errorThrown){";
            echo "	   alert('Error occurred during the request!');";
            echo "	}";
            echo "});";
            echo '});});</script>';
        ?>
    </div>
    <div id="snp-library-elements">
        <div class="snp-library-elements-import">
            <?php
            $snp_library_ver_local = get_option( 'snp_library_ver' );
            $snp_library_ver_remote =  snp_ajax_snp_get_elements_version();
            $snp_library_uptodate = false;
            if ($snp_library_ver_remote == 'Error') {
                echo '<h3>Error: Unable to connect to the server.</h3>';
            } else if ($snp_library_ver_local!==false && version_compare($snp_library_ver_local, $snp_library_ver_remote,'==')) {
                $snp_library_uptodate = true;
                echo '<div class="snp-library-elements-uptodate"><img src="'.SNP_NHP_OPTIONS_URL.'img/up-to-date_ico.gif" /><h4>Elements & Images Library is up to date!</h4></div>';
            } else {
                ?>  
                <div>
                    <img src="<?php echo SNP_NHP_OPTIONS_URL.'img/import_ico.gif' ?>" />
                    <h4><?php echo (!$snp_library_ver_local ? 'Import' : 'Update'); ?> Elements & Images Library
                    <br /><span>Many ready to use Images and Elements (button, inputs, etc.)</span></h4>
                </div>
                <a class="button button-primary" href="edit.php?post_type=snp_popups&page=snp_library&import_elements=1">Import</a>
                <?php
            }
            ?>
        </div>
    </div>
    <div id="snp-library">
        <div id="snp-library-loading">
        <i class="fa fa-cog fa-spin"></i> Loading...
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function($) { 
    $.ajax({
        dataType: "json",
        url: ajaxurl,
        data: {'action' : 'snp_load_library'},
        success: function(data) {
            if (data.Error != undefined && data.Error == 404) {
                $('#snp-library').css('text-align', 'center');
                $('#snp-library').append('<h1 style="padding-top: 50px">Unable to connect to the server. Please try again later.</h1>');
                $('#snp-library-loading').hide();
            } else {
                $.each(data, function(i, item) {
                    $('#snp-library').append('<div class="snp-item"><a target="_blank" href="'+item.demo_link+'"><img src="'+item.img+'" /></a>' +
                        (item.desc != '' ? '<h4>'+item.desc+'</h4>' : '') +
                        '<a class="button-demo button" target="_blank" href="'+item.demo_link+'">Demo</a>' +
                        '<a class="button button-primary" <?php echo ($snp_library_uptodate ? 'href="edit.php?post_type=snp_popups&page=snp_library&import_xml=\'+item.id+\'"' : ' href="#" onclick="alert(\\\'First '.(!$snp_library_ver_local ? 'import' : 'update').' Elements & Images Library!\\\');"');?>>Import</a>' +
                        '</div>');
                });

                $('#snp-library-loading').hide();
                $('#snp-library').show();
            }
        }
    });    
});    
</script>