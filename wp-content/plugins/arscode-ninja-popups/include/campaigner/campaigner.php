<?php

/**
 * Class Campaigner
 */
class Campaigner {
    /**
     * @var
     */
    protected $username;

    /**
     * @var
     */
    protected $password;

    /**
     * Campaigner constructor.
     */
    public function __construct() {}

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Pobieranie listy grupy do których można dodać subskrybenta
     *
     * @return array
     */
    public function getLists()
    {
        $service = 'list';
        $method = 'ListContactGroups';

        $lists = array();

        try {
            $response = $this->sendCommand($service, $method);
        } catch (Exception $e) {
            return $lists;
        }

        if (isset($response->ContactGroupDescription)) {
            foreach ($response->ContactGroupDescription as $list) {
                if ($list->Type == 'MailingList') {
                    $lists[$list->Id] = $list->Name;
                }
            }
        }

        return $lists;
    }

    /**
     * @param        $listId Identyfikator listy
     * @param string $email Adres e-mail użytkownika
     * @param string $firstname Imię użytkownika
     * @param string $lastname Nazwisko użytkownika
     * @param array  $custom_attributes Dodatkowe pola  w postaci tablicy array(
     *  "IsNull" => FALSE,
     *  "Id" => 5393483,
     *  "Value" => "United States",
     * );.
     *
     * @return array|bool|mixed
     */
    public function addContact($listId, $email = '', $firstname = '', $lastname = '', array $custom_attributes = array())
    {
        $service = 'contact';
        $method = "ImmediateUpload";

        $attributes = array();
        foreach ($custom_attributes as $custom_attribute) {
            $attributes[] = array(
                "IsNull" => isset($custom_attribute['IsNull']) ? $custom_attribute['IsNull'] : FALSE,
                "Id" => isset($custom_attribute['Id']) ? $custom_attribute['Id'] : '',
                "_" => isset($custom_attribute['Value']) ? $custom_attribute['Value'] : '',
            );
        }

        $data = array(
            'UpdateExistingContacts' => true,
            'TriggerWorkflow' => true,
            'contacts' => Array(
                'ContactData' => array(
                    'ContactKey' => array(
                        'ContactId' => 0,
                        'ContactUniqueIdentifier' => $email
                    ),
                    'FirstName' => $firstname,
                    'LastName' => $lastname,
                    'MailFormat' => 'both',
                    'IsTestContact' => false,
                    'CustomAttributes' => $attributes,
                    'AddToGroup' => array($listId)
                )
            ),
        );

        return $this->sendCommand($service, $method, null, $data);
    }

    /**
     * @param      $service
     * @param      $method
     * @param null $tag
     * @param null $data
     *
     * @return array|mixed
     */
    public function sendCommand($service, $method, $tag = null, $data = null)
    {
        $username = $this->username;
        $password = $this->password;

        if (empty($username) || empty($password)) {
            return $this->createErrorResponse("Invalid or missing credentials");
        }

        $response = $this->sendCommandDirect($username, $password, $service, $method, $tag, $data);

        return $response;
    }

    /**
     * @param     $message
     * @param int $code
     *
     * @return array
     */
    private function createErrorResponse($message, $code = 0)
    {
        $data = array(
            'ErrorFlag'     => true,
            'ReturnCode'    => array('M',$code,$message),
            'ReturnMessage' => $message,
            'Response'      => null
        );

        return $data;
    }

    /**
     * @param      $username
     * @param      $password
     * @param      $service
     * @param      $method
     * @param null $tag
     * @param null $data
     *
     * @return mixed
     * @throws Exception
     */
    public function sendCommandDirect($username, $password, $service, $method, $tag = null, $data = null)
    {
        if ((!$username || $username == "") || (!$password || $password == "")) {
            throw new Exception("Invalid or missing credentials");
        }

        $url = "https://ws.campaigner.com/2013/01/{$service}management.asmx?WSDL";

        $client = new SoapClient($url, array(
            'exceptions'         => false,
            'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'soap_version'       => 'SOAP_1_1',
            'trace'              => true,
            'connection_timeout' => 300
        ));

        $authentication = array(
            "Username"  => $username,
            "Password"  => $password
        );

        $request = array(
            'authentication' => $authentication,
        );

        if ($data) {
            if ($tag) {
                $request[$tag] = $data;
            } else {
                $request = array_merge($request,$data);
            }
        }

        $response = $client->$method($request);

        $lastResponse = $client->__getLastResponse();

        $finalResponse = $this->processResponse(
            $lastResponse,
            $response,
            "{$method}Result"
        );

        return $finalResponse;
    }

    /**
     * @param $returnCode
     *
     * @return array
     */
    private function processReturnCode($returnCode)
    {
        $parts = explode("_", $returnCode);

        $pieces = array(
            0 => 'type',
            1 => 'code',
            2 => 'label'
        );

        $data = array();

        foreach ($pieces as $index => $key) {
            if (isset($parts[$index])) {
                $data[$key] = $parts[$index];
            }
        }

        return $data;
    }

    /**
     * @param $responseXml
     * @param $response
     * @param $result
     *
     * @return mixed
     * @throws Exception
     */
    private function processResponse($responseXml, $response, $result)
    {
        $soap = simplexml_load_string($responseXml);

        if ($soap === false) {
            throw new Exception("There was a problem communicating with the Campaigner Server", 1);
        }

        $header = $soap->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->Header
            ->children()
            ->ResponseHeader;

        if (((string) $header->ErrorFlag) == "true") {
            if (isset($response->$result)) {
                throw new Exception('Campaigner Error: ' . (string) $header->ReturnMessage . '; Details: ' . $response->$result);
            } else {
                throw new Exception('Campaigner Error: ' . (string) $header->ReturnMessage);
            }
        }

        if (!isset($response->$result)) {
            throw new Exception("Result not found", 1);
        }

        return $response->$result;
    }
}