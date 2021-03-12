<?php

if (!class_exists('MailUpClient')) {
    require 'MailUpClient.php';
}

class snp_mailup extends MailUpClient
{
    public function __construct($client_id, $secret, $login, $password)
    {
        $this->logonEndpoint = "https://services.mailup.com/Authorization/OAuth/LogOn";
        $this->authorizationEndpoint = "https://services.mailup.com/Authorization/OAuth/Authorization";
        $this->tokenEndpoint = "https://services.mailup.com/Authorization/OAuth/Token";
        $this->consoleEndpoint = "https://services.mailup.com/API/v1.1/Rest/ConsoleService.svc";
        $this->mailstatisticsEndpoint = "https://services.mailup.com/API/v1.1/Rest/MailStatisticsService.svc";

        $this->clientId = $client_id;
        $this->clientSecret = $secret;
        
        $this->loadToken();
        $this->logOnWithPassword($login, $password);
    }
    
    public function getLists()
    {
        $url = 'https://services.mailup.com/API/v1.1/Rest/ConsoleService.svc/Console/User/Lists';
        $response = json_decode($this->callMethod($url, 'GET', "", "JSON", false));
        return $response->Items;
    }
    
    public function subscribe($list_id, $data, $confirm = false)
    {
        $url = 'https://services.mailup.com/API/v1.1/Rest/ConsoleService.svc/Console/List/'. $list_id .'/Recipient'. ($confirm?'?ConfirmEmail=true':'');
        $response = json_decode($this->callMethod($url, 'POST', json_encode($data), "JSON", false));
        return $response;
    }
}