<?php

namespace Relio\Entity;

/**
 * Class MailLogger
 */
class MailLogger
{
    /**
     * @var array
     */
    protected $mail_log = array();

    /**
     * @param $to
     * @param $subject
     * @param $message
     * @param string $headers
     * @param string $attachments
     */
    public function log($to, $subject, $message, $headers = '', $attachments = '' ) {

        $this->mail_log[] = array(
            'to'      => $to,
            'subject' => $subject,
            'message' => $message,
            'headers' => $headers,
            'attachments' => $attachments,
            'timestamp' => time()
        );
    }

    /**
     * save
     *
     * @return TRUE|\WP_Error
     */
    public function save()
    {
        $error = false;
        foreach ($this->mail_log as $mail) {
            $post = $this->buildPost( $mail );
            $post_ID = wp_insert_post($post, true);
            if (is_wp_error($post_ID)) {
                if (!$error) {
                    $error = new \WP_Error;
                }
                $error->add(
                    $post_ID->get_error_code(),
                    $post_ID->get_error_message(),
                    $post_ID->get_error_data()
                );
            }
        }

        if ($error) {
            return $error;
        }

        return true;
    }

    /**
     * @param $mail
     * @return array
     */
    protected function buildPost($mail)
    {
        $to = 'To: ';
        $to .= is_array( $mail[ 'to' ] )
            ? implode( ',', $mail[ 'to' ] )
            : $mail[ 'to' ];

        $headers = is_array( $mail[ 'headers' ] )
            ? print_r( $mail[ 'headers' ], TRUE )
            : $mail[ 'headers' ];

        $content = $to
            . PHP_EOL
            . $headers
            . str_repeat( PHP_EOL, 2 )
            . $mail[ 'message' ];

        $post = array(
            'post_title'   => $mail[ 'subject' ],
            'post_content' => esc_html( $content ),
            'post_type'    => 'snp_mail_log',
            'post_date'    => date( 'Y-m-d H:i:s', $mail[ 'timestamp' ] ),
            'post_status'  => 'private'
        );

        return $post;
    }
}