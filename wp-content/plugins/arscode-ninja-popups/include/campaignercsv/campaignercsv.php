<?php

/**
 * Class CampaignerCsv
 */
class CampaignerCsv {
    /**
     * @var
     */
    protected $baseUrl = 'https://secure.campaigner.com';
    
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

    public function login()
    {
        if (!$this->username || !$this->password) {
            return false;
        }

        $result = $this->curlGet('/Login/');

        preg_match('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $result, $viewStateMatch);

        preg_match('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $result, $viewStateGeneratorMatch);

        preg_match('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $result, $eventValidationMatch);
        
        preg_match('/"ChallengeScript":"~(.*?)"/', $result, $challengeScript);

        $result = $this->curlPost('/Login/Login.aspx', array(
            '__EVENTTARGET'                                           => '',
            '__EVENTARGUMENT'                                         => '',
            '__VIEWSTATE'                                             => $viewStateMatch[1],
            '__VIEWSTATEGENERATOR'                                    => $viewStateGeneratorMatch[1],
            '__EVENTVALIDATION'                                       => $eventValidationMatch[1],
            'ctl00$MPContent$txtUsername'                             => $this->username,
            'ctl00$MPContent$txtPassword'                             => $this->password,
            'ctl00$MPContent$btnSignIn'                               => 'Sign+In',
            'ctl00_MPContent_NoBot2_NoBot2_NoBotExtender_ClientState' => ($challengeScript[1]+1)
        ));
    }

    /**
     * @param array $data
     * @param integer $mailingListId
     * @return bool
     */
    public function importContact($data = null, $mailingListId = 0)
    {
        if (empty($data)) {
            return false;
        }

        if (!$this->username || !$this->password) {
            return false;
        }

        $result = $this->curlGet('/CSB/contacts/AddEditContact.aspx?mailingListId=' . $mailingListId);

        preg_match('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $result, $viewStateMatch);

        preg_match('/<input type="hidden" name="__EVENTTARGET" id="__EVENTTARGET" value="(.*?)" \/>/', $result, $eventTargetMatch);

        preg_match('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $result, $viewStateGeneratorMatch);

        preg_match('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $result, $eventValidationMatch);

        preg_match('/<input id="ctl00\_ctl00\_ContentPlaceHolder1\_ContentPlaceHolder1\_cntMailing\_chkMailingLists\_(.*?)"(.*?)checked="checked"(.*?)>/', $result, $mailingListCheckbox);

        $result = $this->curlPost('/CSB/contacts/AddEditContact.aspx?mailingListId=' . $mailingListId, array(
            '__EVENTTARGET' => 'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$btnSave',
            '__EVENTARGUMENT' => '',
            '__VIEWSTATE' => $viewStateMatch[1],
            '__VIEWSTATEGENERATOR' => $viewStateGeneratorMatch[1],
            '__EVENTVALIDATION' => $eventValidationMatch[1],
            'ctl00_ctl00_RadFormDecorator1_ClientState' => '',
            'ctl00_ctl00_RadWindowManagerMasterPage_ClientState' => '',
            'ctl00_ctl00_RadWindowPlaceHolder_ClientState' => '',
            'ctl00_ctl00_RadToolTipManager1_ClientState' => '',
            'ctl00_ctl00_rnSuccessToastWindow_ClientState' => '',
            'ctl00_ctl00_rnFailToastWindow_ClientState' => '',
            'ctl00_ctl00_helpToopTip_ClientState' => '',
            'ctl00_ctl00_supportToolTip_ClientState' => '',
            'ctl00_ctl00_notificationToolTip_ClientState' => '',
            'ctl00_ctl00_MainRadToolBar_ClientState' => '',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_DialogWindow_ClientState' => '',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_RadWindowManagerNewCustomAttribute_ClientState' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$hfKissanhnta' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntContactControl$txtEmailAddress' => $data['email'],
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntContactControl$StatusDDL' => 'Subscribed',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntContactControl_StatusDDL_ClientState' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntContactControl$txtFirstName' => $data['firstname'],
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntContactControl$txtPhoneNumber' => $data['phone'],
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntContactControl_txtPhoneNumber_ClientState' => '{"enabled":true,"emptyMessage":"","validationText":"'.$data['phone'].'","valueAsString":"'.$data['phone'].'","lastSetTextBoxValue":"'.$data['phone'].'"}',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntContactControl$txtLastName' => $data['lastname'],
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntContactControl$txtFaxNumber' => $data['fax'],
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntContactControl_txtFaxNumber_ClientState' => '{"enabled":true,"emptyMessage":"","validationText":"'.$data['fax'].'","valueAsString":"'.$data['fax'].'","lastSetTextBoxValue":"'.$data['fax'].'"}',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntContactControl$EmailFormatDDL' => 'Both',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntContactControl_EmailFormatDDL_ClientState' => '',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_RadTabStrip1_ClientState' => '{"selectedIndexes":["0"],"logEntries":[],"scrollState":{}}',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntCustValues_rgCustAttributes_ClientState' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntPurchaseHistory$rgOrders$ctl00$ctl02$ctl02$FilterTextBox_OrderNumber' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntPurchaseHistory$rgOrders$ctl00$ctl02$ctl02$FilterTextBox_Status' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntPurchaseHistory$rgOrders$ctl00$ctl03$ctl01$PageSizeComboBox' => '10',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntPurchaseHistory_rgOrders_ctl00_ctl03_ctl01_PageSizeComboBox_ClientState' => '',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntPurchaseHistory_rgOrders_rfltMenu_ClientState' => '',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntPurchaseHistory_rgOrders_ClientState' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntPurchaseHistory$hfSelectedIdsJSON' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntPurchaseHistory$hfOrderRowCount' => '',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntMailing$chkMailingLists$'.$mailingListCheckbox[1] => 'on',
            'ctl00$ctl00$ContentPlaceHolder1$ContentPlaceHolder1$cntMailing$NewMailingForm$txtName' => 'New mailing list name',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_cntMailing_NewMailingForm_txtName_ClientState' => '{"enabled":true,"emptyMessage":"New mailing list name","validationText":"","valueAsString":"","lastSetTextBoxValue":"New mailing list name"}',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_vwHistory_RadWindowDateRange_ClientState' => '',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_vwHistory_RadWindowManager1_ClientState' => '',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_vwHistory_gvContactHistory_ClientState' => '',
            'ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_RadMultiPage1_ClientState' => '{"selectedIndex":0,"changeLog":[]}',
            'ctl00$ctl00$hdnFullStoryURL' => '',
            'ctl00$ctl00$rnSuccessToastWindow$hiddenState' => '',
            'ctl00_ctl00_rnSuccessToastWindow_XmlPanel_ClientState' => '',
            'ctl00_ctl00_rnSuccessToastWindow_TitleMenu_ClientState' => '',
            'ctl00$ctl00$rnFailToastWindow$hiddenState' => '',
            'ctl00_ctl00_rnFailToastWindow_XmlPanel_ClientState' => '',
            'ctl00_ctl00_rnFailToastWindow_TitleMenu_ClientState' => '',
        ));

        return true;
    }

    /** 
     * Send a POST requst using cURL
     * 
     * @param string $url to request 
     * @param array $post values to send 
     * @param array $options for cURL 
     * @return string 
     */ 
    protected function curlPost($url, array $post = NULL, array $options = array()) 
    {
        $defaults = array( 
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_POST            => 1, 
            CURLOPT_HEADER          => 1, 
            CURLOPT_URL             => $this->baseUrl . $url, 
            CURLOPT_RETURNTRANSFER  => 1, 
            CURLOPT_TIMEOUT         => 300, 
            CURLOPT_POSTFIELDS      => !empty($post) ? http_build_query($post) : '',
            CURLOPT_COOKIEFILE      => 'cookie.txt',
            CURLOPT_COOKIEJAR       => 'cookie.txt',
            CURLOPT_SSL_VERIFYPEER  => false
        );
    
        $ch = curl_init(); 
        curl_setopt_array($ch, ($options + $defaults)); 
        if(!$result = curl_exec($ch)) { 
            trigger_error(curl_error($ch)); 
        } 
        curl_close($ch); 
        
        return $result; 
    }

    /**
     * Send a GET request using cURL
     *
     * @param string $url to request
     * @param array $get values to send
     * @param $options for cURL
     * @return string
     */
    protected function curlGet($url, array $get = NULL, array $options = array())
    {
        if (!is_null($get)) {
            $get_fields = (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($get);
        } else {
            $get_fields = '';
        }

        $defaults = array(
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_URL            => $this->baseUrl . $url . $get_fields, 
            CURLOPT_HEADER         => 1, 
            CURLOPT_RETURNTRANSFER => TRUE, 
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_COOKIEFILE     => 'cookie.txt',
            CURLOPT_COOKIEJAR      => 'cookie.txt',
            CURLOPT_SSL_VERIFYPEER => false
        ); 

        $ch = curl_init(); 
        curl_setopt_array($ch, ($options + $defaults)); 
        if(!$result = curl_exec($ch)) { 
            trigger_error(curl_error($ch)); 
        } 
        curl_close($ch);

        return $result;
    }
}