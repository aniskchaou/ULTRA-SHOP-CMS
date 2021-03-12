<?php

add_filter('ninja_popups_subscribe_by_zoho', 'ninja_popups_subscribe_by_zoho', 10, 1);

function ninja_popups_subscribe_by_zoho($params = array())
{
    if (snp_get_option('ml_manager') != 'zoho') {
        return;
    }

    require_once SNP_DIR_PATH . '/include/zoho/vendor/autoload.php';

    $ml_zoho_campaign = $params['popup_meta']['snp_ml_zoho_campaign'][0];
    if (!$ml_zoho_campaign) {
        $ml_zoho_campaign = snp_get_option('ml_zoho_campaign');
    }

    $result = array(
        'status' => false,
        'log' => array(
            'listId' => $ml_zoho_campaign,
            'errorMessage' => '',
        )
    );

    if (($info = get_option('snp_ml_zoho_auth_info'))) {
        $client = new CristianPontes\ZohoCRMClient\ZohoCRMClient('Leads', $info['accessToken']);
        $clientCampaign = new CristianPontes\ZohoCRMClient\ZohoCRMClient('Campaigns', $info['accessToken']);

        $data = array();
        $data['Email'] = snp_trim($params['data']['post']['email']);
        if (!empty($params['data']['post']['name'])) {
            $data = array(
                'First Name' => $params['data']['names']['first'],
                'Last Name' => $params['data']['names']['last']
            );
        }

        if (count($params['data']['cf']) > 0) {
            foreach ($params['data']['cf'] as $k => $v) {
                $data[$k] = $v;
            }
        }

        try {
            $records = $client->insertRecords()
                ->setRecords([
                    $data
                ])
                ->onDuplicateError()
                ->triggerWorkflow()
                ->request();

            foreach ($records as $record) {
                if ($record->isInserted()) {
                    $clientCampaign->updateRelatedRecords()
                        ->id($ml_zoho_campaign)
                        ->relatedModule('Leads')
                        ->addRecord([
                            'LEADID' => $record->id
                        ])
                        ->request();
                }
            }

            $result['status'] = true;
        } catch (Exception $e) {
            $result['log']['errorMessage'] = $e->getMessage();
        }
    } else {
        $result['log']['errorMessage'] = 'No access token for ZOHO API';
    }

    return $result;
}