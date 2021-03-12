<?php
	
require_once dirname(__FILE__).'/MC_Rest_Base.php';

/**
 * Class MC_Rest
 */
class MC_Rest extends MC_Rest_Base
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var null
     */
	public $id = null;

    /**
     * MC_Rest constructor.
     * @param $api_key
     */
	public function __construct( $api_key )
    {
        $dc = explode('-', $api_key);
		$dc = end($dc);

		parent::__construct($dc);

		$this->apiKey = $api_key;

		$this->path = $this->url . $this->name . '/';
    }

    /**
     * @param null $data
     * @return |null
     * @throws Exception
     */
    function getAll( $data = null )
	{
	    $this->request = '';

	    return $this->execute( 'GET', $data );
	}
}
