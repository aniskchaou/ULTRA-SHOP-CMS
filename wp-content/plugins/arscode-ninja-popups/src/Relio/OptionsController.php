<?php

namespace Relio;

/**
 * Class OptionsController
 * @package Relio
 */
class OptionsController
{
    /**
     * Run
     */
    public function init()
    {
        self::addOptionsMenu();

        self::addGeneralSection();

        self::addMailingListManagerSection();

        self::addAutoUpdateSection();

        self::addPromoteSection();

        self::addAdvancedSettingsSection();

        self::addLogsSection();

        self::addBackupSection();

        do_action('ninja_popups_options_init');
    }

    /**
     * Main method that adds options menu
     */
    private function addOptionsMenu()
    {
        \CSF::createOptions(NINJA_POPUP_OPTIONS, [
            'framework_title' => 'Ninja Pop-ups <small>by Arscode</small>',
            'menu_title'  => 'New Settings',
            'menu_slug'   => 'ninja_options',
            'menu_type'   => 'submenu',
            'menu_parent' => 'edit.php?post_type=snp_popups',
            'menu_hidden' => false,
        ]);
    }

    /**
     * Method that adds general section in settings
     */
    private function addGeneralSection()
    {
        \CSF::createSection(NINJA_POPUP_OPTIONS, [
            'title' => 'General settings',
            'icon' => 'fa fa-cog',
            'fields' => [
                [
                    'id'      => 'enable-plugin',
                    'type'    => 'switcher',
                    'title'   => 'Enable plugin',
                    'default' => true,
                ],
                [
                    'id'      => 'enable-plugin-mobile',
                    'type'    => 'switcher',
                    'title'   => 'Enable plugin on Mobile Devices',
                    'default' => true,
                ],
                [
                    'id'      => 'enable-geoip',
                    'type'    => 'switcher',
                    'title'   => 'Enable GeoIP Pop-up\'s',
                    'default' => false,
                ],
            ]
        ]);

        do_action('ninja_popups_options_general_section');
    }

    /**
     * Method that adds mailing list managers section in settings
     */
    private function addMailingListManagerSection()
    {
        \CSF::createSection(NINJA_POPUP_OPTIONS, array(
            'id' => 'mailing_managers',
            'title' => 'Mailing List Managers',
            'icon' => 'fa fa-envelope',
        ));

        do_action('ninja_popups_options_mailing_list_manager_section');
    }

    /**
     * Method that adds auto update section in settings
     */
    private function addAutoUpdateSection()
    {
        \CSF::createSection(NINJA_POPUP_OPTIONS, [
            'title' => 'Auto Updates',
            'icon' => 'fa fa-cloud-download',
            'fields' => [
                [
                    'id'       => 'enable-autoupdate',
                    'type'     => 'switcher',
                    'title'    => 'Auto Updates',
                    'default'  => false,
                ],
                [
                    'id'    => 'purchasecode',
                    'type'  => 'text',
                    'title' => 'Purchase Code',
                    'subtitle' => '<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-can-I-find-my-Purchase-Code-" target="_blank">Where can I find my Purchase Code?</a>',
                    'after' => '
			<input type="button" id="purchasecode_check" value="Verify" class="button"/>
			<script>
			jQuery(document).ready(function(){
				jQuery(\'#purchasecode_check\').click(function(){
					jQuery.ajax({
						url: ajaxurl,
						data:{
							\'action\': \'snp_purchasecode_check\',
							\'purchasecode\': jQuery(\'#purchasecode\').val(),
						},
						type: \'POST\',
						success:function(response){
							alert(response);
						},
						error: function(errorThrown){
							alert(\'Error occurred during the request!\');
						}
					});
				});
			});
			</script>
			',
                    'dependency' => [
                        'enable-autoupdate', '==', 'true'
                    ],
                ],
            ]
        ]);

        do_action('ninja_popups_options_auto_update_section');
    }

    /**
     * Method that adds promote section in settings
     */
    private function addPromoteSection()
    {
        \CSF::createSection(NINJA_POPUP_OPTIONS, [
            'title' => 'Promote',
            'icon' => 'fa fa-money',
            'description' => 'Earn with Envato Affiliate Program! <a href="http://codecanyon.net/make_money/affiliate_program" target="_blank">click here</a> to get more information',
            'fields' => [
                [
                    'id'       => 'enable-promote',
                    'type'     => 'switcher',
                    'title'    => 'Promote Ninja Pop-ups with Your Affiliate link',
                    'default'  => false,
                ],
                [
                    'id'    => 'promote-envato-username',
                    'type'  => 'text',
                    'title' => 'Your Envato Username',
                    'dependency' => [
                        'enable-promote', '==', 'true'
                    ],
                ],
                [
                    'type'    => 'content',
                    'content' => '<div style="text-align: center;"><img src="' . SNP_URL . '/admin/img/promote.png" /></div>',
                ],
            ]
        ]);

        do_action('ninja_popups_options_promote_section');
    }

    /**
     * Method that adds advanced settings section in settings
     */
    private function addAdvancedSettingsSection()
    {
        \CSF::createSection(NINJA_POPUP_OPTIONS, array(
            'id'    => 'advanced_settings',
            'title' => 'Advanced Settings',
            'icon'  => 'fa fa-cogs',
        ));

        \CSF::createSection(NINJA_POPUP_OPTIONS, array(
            'parent'      => 'advanced_settings',
            'title'       => 'Whitelist',
            'fields'      => array(
                array(
                    'id'       => 'enable-whitelist',
                    'type'     => 'switcher',
                    'title'    => 'Enabled',
                    'default'  => false,
                ),
                array(
                    'id'     => 'whitelist-emails',
                    'type'   => 'repeater',
                    'title'  => 'Domains',
                    'subtitle' => 'Enter only domain you wish to whitelist ie. gmail.com',
                    'fields' => array(
                        array(
                            'id'    => 'email',
                            'type'  => 'text',
                        ),
                    ),
                    'dependency' => array(
                        'enable-whitelist', '==', 'true'
                    ),
                ),
            )
        ));

        \CSF::createSection(NINJA_POPUP_OPTIONS, array(
            'parent'      => 'advanced_settings',
            'title'       => 'Blacklist',
            'fields'      => array(
                array(
                    'id'       => 'enable-blacklist',
                    'type'     => 'switcher',
                    'title'    => 'Enabled',
                    'default'  => false,
                ),
                array(
                    'id'     => 'blacklist-emails',
                    'type'   => 'repeater',
                    'title'  => 'Domains',
                    'subtitle' => 'Enter only domain you wish to blacklist ie. gmail.com',
                    'fields' => array(
                        array(
                            'id'    => 'email',
                            'type'  => 'text',
                        ),
                    ),
                    'dependency' => array(
                        'enable-blacklist', '==', 'true'
                    ),
                ),
            )
        ));

        \CSF::createSection(NINJA_POPUP_OPTIONS, array(
            'parent'      => 'advanced_settings',
            'title'       => 'Look & Feel',
            'fields'      => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'info',
                    'content' => 'If Theme or another Plugin is loading any of these scripts, you can turn it off to avoid conflict.',
                ),
                array(
                    'id'       => 'enable-fontawesome',
                    'type'     => 'switcher',
                    'title'    => 'Font Awesome',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-material-design',
                    'type'     => 'switcher',
                    'title'    => 'Material design for inputs',
                    'default'  => false,
                ),

                array(
                    'id'       => 'enable-jqueryui',
                    'type'     => 'switcher',
                    'title'    => 'jQuery UI theme',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-jqueryui-accordion',
                    'type'     => 'switcher',
                    'title'    => 'jQuery UI Accordion',
                    'subtitle' => 'Enable this if in JS Console you have errors related to jQuery UI Accordion',
                    'default'  => false,
                ),
                array(
                    'id'       => 'enable-fancybox',
                    'type'     => 'switcher',
                    'title'    => 'Fancybox 2',
                    'subtitle' => 'jquery.fancybox.pack.js',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-tooltipster',
                    'type'     => 'switcher',
                    'title'    => 'Tooltipster',
                    'subtitle' => 'tooltipster.bundle.min.js',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-facebook',
                    'type'     => 'switcher',
                    'title'    => 'Facebook JS',
                    'subtitle' => 'https://connect.facebook.net/en_GB/all.js#xfbml=1',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-googleplus',
                    'type'     => 'switcher',
                    'title'    => 'Google+ JS',
                    'subtitle' => 'https://apis.google.com/js/plusone.js',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-twitter',
                    'type'     => 'switcher',
                    'title'    => 'Twitter JS',
                    'subtitle' => 'https://platform.twitter.com/widgets.js',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-pinterest',
                    'type'     => 'switcher',
                    'title'    => 'Pinterest JS',
                    'subtitle' => 'https://assets.pinterest.com/js/pinit.js',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-linkedin',
                    'type'     => 'switcher',
                    'title'    => 'LinkedIn JS',
                    'subtitle' => 'https://platform.linkedin.com/in.js',
                    'default'  => true,
                ),
                array(
                    'id'       => 'enable-recaptcha',
                    'type'     => 'switcher',
                    'title'    => 'Recaptcha JS',
                    'subtitle' => 'https://www.google.com/recaptcha/api.js',
                    'default'  => true,
                ),
            )
        ));

        \CSF::createSection(NINJA_POPUP_OPTIONS, array(
            'parent'      => 'advanced_settings',
            'title'       => 'ReCaptcha integration',
            'description' => 'Visit <a href="https://www.google.com/recaptcha" target="_blank">google.com/recaptcha</a> to get more information',
            'fields'      => array(
                array(
                    'id'    => 'recaptcha-sitekey',
                    'type'  => 'text',
                    'title' => 'Site Key'
                ),
                array(
                    'id'    => 'recaptcha-secretkey',
                    'type'  => 'text',
                    'title' => 'Secret Key'
                ),
            )
        ));

        /**
         * Thechecker.co integration
         */
        \CSF::createSection(NINJA_POPUP_OPTIONS, array(
            'parent'      => 'advanced_settings',
            'title'       => 'Thechecker.co integration',
            'description' => 'Visit <a href="https://thechecker.co" target="_blank">thechecker.co</a> to get more information',
            'fields'      => array(
                array(
                    'id'       => 'enable-thechecker',
                    'type'     => 'switcher',
                    'title'    => 'Enable',
                    'subtitle' => 'Enable e-mail address validations with thechecker.co service',
                    'default'  => false,
                ),
                array(
                    'id'    => 'thechecker-apikey',
                    'type'  => 'text',
                    'title' => 'API Key',
                    'dependency' => array(
                        'enable-thechecker', '==', 'true'
                    ),
                ),
            )
        ));

        /**
         * WP-AJAX
         */
        \CSF::createSection(NINJA_POPUP_OPTIONS, [
            'parent' => 'advanced_settings',
            'title' => 'WP-AJAX',
            'fields' => [
                [
                    'id' => 'wp-ajax-ping-time',
                    'type' => 'text',
                    'title' => 'Ping time',
                    'subtitle' => 'How often should WP-AJAX be pinged. This setting is used when "When user spends X seconds on page" open method is being used',
                ],
                [
                    'id' => 'wp-ajax-request-handler',
                    'type' => 'text',
                    'title' => 'Request handler',
                    'subtitle' => 'Url to script that will handle ajax requests (used for statistics and subscriptions). Leave empty to use default wp-ajax',
                ]
            ]
        ]);

        /**
         * Others
         */
        \CSF::createSection(NINJA_POPUP_OPTIONS, array(
            'parent'      => 'advanced_settings',
            'title'       => 'Others',
            'fields'      => array(
                array(
                    'id'       => 'enable-selftest',
                    'type'     => 'switcher',
                    'title'    => 'Disable Self Test Warning',
                    'default'  => false,
                ),
                array(
                    'id'       => 'enable-affiliate-message',
                    'type'     => 'switcher',
                    'title'    => 'Disable Afilliate Program Notice',
                    'default'  => false,
                ),
                array(
                    'id'       => 'enable-np-columns',
                    'type'     => 'switcher',
                    'title'    => 'Disable Ninja Pop-up\'s column in Posts/Pages Lists',
                    'default'  => false,
                ),
            )
        ));

        do_action('ninja_popups_options_advanced_settings_section');
    }

    /**
     * Method that adds logs section in settings
     */
    private function addLogsSection()
    {
        \CSF::createSection(NINJA_POPUP_OPTIONS, [
            'title' => 'Logging',
            'icon' => 'fa fa-filter',
            'fields' => [
                [
                    'id'       => 'enable-logging',
                    'type'     => 'switcher',
                    'title'    => 'Enable Log Gathering',
                    'default'  => true,
                ],
                [
                    'id'       => 'enable-logging-subscribe',
                    'type'     => 'switcher',
                    'title'    => 'Collect Subscription Events',
                    'subtitle' => 'Log will be updated when subscription takes place.',
                    'dependency' => [
                        'enable-logging', '==', 'true'
                    ],
                    'default'  => true,
                ],
                [
                    'id'       => 'enable-logging-view',
                    'type'     => 'switcher',
                    'title'    => 'Collect Popup View Events',
                    'subtitle' => 'Log will be updated when popup is viewed on a website.',
                    'dependency' => [
                        'enable-logging', '==', 'true'
                    ],
                    'default'  => true,
                ],
            ]
        ]);

        do_action('ninja_popups_options_logs_section');
    }

    /**
     * Method that adds backup section in settings
     */
    private function addBackupSection()
    {
        \CSF::createSection(NINJA_POPUP_OPTIONS, [
            'title'       => 'Backup',
            'icon'        => 'fa fa-shield',
            'fields'      => [
                [
                    'type' => 'backup',
                ],
            ]
        ]);

        do_action('ninja_popups_options_backup_section');
    }
}