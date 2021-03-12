<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WP_QueryWoofCounter
{

    public $post_count = 0;
    public $found_posts = 0;
    public $key_string = "";
    public $table = "";

    //public static $collector = array();

    public function __construct($query)
    {
        $saving_memory=apply_filters('woof_counter_method',false);
        global $wpdb;
        global $WOOF;
        $query = (array) $query;
        if($saving_memory){
            $query["nopaging"]=false;
            $query["posts_per_page"]=1;
        }
        
        $key = md5(json_encode($query));
        //***
        $this->key_string = 'woof_count_cache_' . $key;
        $this->table = WOOF::$query_cache_table;
        //***
        $woof_settings = get_option('woof_settings', array());

        $_REQUEST['woof_before_recount_query'] = 1;
        if ($woof_settings['cache_count_data'])
        {
            $value = $this->get_value();
            if ($value != -1)
            {
                $this->post_count = $this->found_posts = $value;
            } else
            {
                $q = new WP_QueryWOOFCounterIn($query);
                if($saving_memory){
                    $this->post_count = $this->found_posts = $q->found_posts;  
                }else{
                    $this->post_count = $this->found_posts = $q->post_count;
                }
                unset($q);
                $this->set_value();
            }
        } else
        {
            $q = new WP_QueryWOOFCounterIn($query);
            if($saving_memory){
                $this->post_count = $this->found_posts = $q->found_posts;
            }else{
                $this->post_count = $this->found_posts = $q->post_count;
            }
            unset($q);
        }
        unset($_REQUEST['woof_before_recount_query']);
    }

    private function set_value()
    {
        global $wpdb;
        $data=array(
            array(
                'type'=>'string',
                'val'=>$this->key_string
            ),
            array(
                'type'=>'int',
                'val'=>$this->post_count,
            ),
        );
        $wpdb->query(WOOF_HELPER::woof_prepare("INSERT INTO {$this->table} (mkey, mvalue) VALUES (%s, %d)", $data));
    }

    private function get_value()
    {
        global $wpdb;
        $result = -1;
        $data=array(
            array(
                'type'=>'string',
                'val'=>$this->key_string
            )
        );        
        $sql = WOOF_HELPER::woof_prepare("SELECT mkey,mvalue FROM {$this->table} WHERE mkey='%s'", $data);
        $value = $wpdb->get_results($sql);

        if (!empty($value))
        {
            $value = end($value);
            if (isset($value->mkey))
            {
                $result = $value->mvalue;
            }
        }

        return $result;
    }

}

final class WP_QueryWOOFCounterIn extends WP_Query
{

    function __construct($query = '')
    {
        parent::__construct($query);
    }

    function set_found_posts($q, $limits)
    {
        return false;
    }

    function setup_postdata($post)
    {
        return false;
    }

    function the_post()
    {
        return FALSE;
    }

    function have_posts()
    {
        return FALSE;
    }

}
