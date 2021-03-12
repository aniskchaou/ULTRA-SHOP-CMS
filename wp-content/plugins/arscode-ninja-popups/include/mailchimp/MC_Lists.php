<?php

require_once dirname(__FILE__) . '/base/MC_Rest.php';

/**
 * Class MC_Lists
 */
class MC_Lists extends MC_Rest
{
    /**
     * MC_Lists constructor.
     * @param $api_key
     */
    public function __construct($api_key)
    {
        $this->name = 'lists';

        parent::__construct($api_key);
    }

    /**
     * @param string $list
     * @param null $email
     * @return |null
     * @throws Exception
     */
    public function searchMember($list = '', $email = null)
    {
        $this->path = $this->url;

        $this->request = 'search-members';

        return $this->execute('GET', array(
            'query' => $email,
            'list_id' => $list
        ));
    }

    /**
     * @param string $list
     * @param null $subscriber
     * @return |null
     * @throws Exception
     */
    public function addMember($list = '', $subscriber = null)
    {
        $this->path = $this->url . $this->name . '/';

        $this->request = $list. '/members/'. md5(strtolower($subscriber['email_address']));

        return $this->execute('PUT', json_encode($subscriber));
    }

    /**
     * @param string $list
     * @return |null
     * @throws Exception
     */
	public function mergeFields($list = '')
	{
        $this->path = $this->url . $this->name . '/';

		$this->request = $list.'/merge-fields/';

		return $this->execute( 'GET' );
	}

    /**
     * @param string $list
     * @return |null
     * @throws Exception
     */
	public function getGroups($list = '')
	{
        $this->path = $this->url . $this->name . '/';

		$this->request = $list.'/interest-categories/';

		return $this->execute( 'GET', array(
		    'count' => 100
        ));
	}

    /**
     * @param string $list
     * @param string $group
     * @return |null
     * @throws Exception
     */
	public function getGroupFields($list = '', $group = '')
	{
        $this->path = $this->url . $this->name . '/';

		$this->request = $list.'/interest-categories/'.$group.'/interests/';

		return $this->execute( 'GET', array(
		    'count' => 100
        ));
	}

    /**
     * @param $list
     * @param $subscriber
     * @param $tags
     * @return |null
     * @throws Exception
     */
	public function addTag($list, $subscriber, $tags)
    {
        $this->path = $this->url . $this->name . '/';

        $this->request = $list . '/members/' . $subscriber . '/tags';

        return $this->execute('POST', $tags);
    }
}
