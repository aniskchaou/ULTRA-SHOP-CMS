<?php
class API_SG
{
    private $_membreid;
    private $_codeactivation;
    private $_datas = array();
    private $_apiUrl = 'https://sg-autorepondeur.com/API_V2/';


    public function __construct($membreid, $codeactivation)
    {
        $this->_membreid        = $membreid;
        $this->_codeactivation  = $codeactivation;

        $this->_datas['membreid']       = $this->_membreid;
        $this->_datas['codeactivation'] = $this->_codeactivation;
    }

    public function set($name, $value = '')
    {
        if (is_array($name)) {
            foreach($name as $id => $value)
                $this->set($id, $value);
        } else {
            $this->_datas[$name] = $value;
        }
        return $this;
    }

    public function call($action)
    {
        $this->_datas['action'] = $action;

        $handle = curl_init($this->_apiUrl);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($this->_datas));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $req = curl_exec($handle);
        curl_close($handle);

        if ($req === FALSE) {
            throw new Exception('Aucun résultat renvoyé par SG-Autorépondeur');
        }
        
        return $req;
    }
}