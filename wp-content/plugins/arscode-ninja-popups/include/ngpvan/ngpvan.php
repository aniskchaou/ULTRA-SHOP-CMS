<?php

require_once SNP_DIR_PATH . '/include/httpful.phar';

/**
 * Class Ngpvan
 */
class Ngpvan
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $baseUrl = 'https://api.myngp.com/v2/';

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getContactCodes()
    {
        $response = \Httpful\Request::get($this->baseUrl . 'contactCodes')
            ->authenticateWith($this->username, $this->password)
            ->expectsJson()
            ->send();

        return $response;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function submitContact($data)
    {
        $response = \Httpful\Request::post($this->baseUrl . 'contacts/findOrCreate')
            ->authenticateWith($this->username, $this->password)
            ->sendsJson()
            ->body(json_encode($data))
            ->expectsJson()
            ->send();

        return $response;
    }
}