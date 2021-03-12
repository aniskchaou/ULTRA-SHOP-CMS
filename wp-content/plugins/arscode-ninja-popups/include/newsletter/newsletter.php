<?php

/**
 * Class RelioNewsletterApi
 */
class RelioNewsletterApi {
    /**
     * @param $email
     * @param $firstname
     * @param $lastname
     * @param $lists
     * @param $additional
     * @param null $status
     * @return array|bool
     */
    public function subscribe($email, $firstname, $lastname, $lists, $additional, $status = null)
    {
        $newsletterSubscription = NewsletterSubscription::instance();

        $opt_in = (int) $newsletterSubscription->options['noconfirmation']; // 0 - double, 1 - single

        $email = $newsletterSubscription->normalize_email(stripslashes($email));

        // Shound never reach this point without a valid email address
        if ($email == null) {
            die('Wrong email');
        }

        $user = $newsletterSubscription->get_user($email);

        if ($user != null) {
            // Email already registered in our database
            $newsletterSubscription->logger->info('Subscription of an address with status ' . $user->status);

            // Bounced
            // TODO: Manage other cases when added
            if ($user->status == 'B') {
                // Non persistent status to decide which message to show (error)
                $user->status = 'E';
                return $user;
            }

            // Is there any relevant data change? If so we can proceed otherwise if repeated subscriptions are disabled
            // show an already subscribed message

            if (empty($newsletterSubscription->options['multiple'])) {
                $user->status = 'E';
                return $user;
            }

            // If the subscriber is confirmed, we cannot change his data in double opt in mode, we need to
            // temporary store and wait for activation
            if ($user->status == Newsletter::STATUS_CONFIRMED && $opt_in == NewsletterSubscription::OPTIN_DOUBLE) {

                set_transient($newsletterSubscription->get_user_key($user), $_REQUEST, 3600 * 48);

                // This status is *not* stored it indicate a temporary status to show the correct messages
                $user->status = 'S';

                $newsletterSubscription->send_message('confirmation', $user);

                return $user;
            }
        }

        // Here we have a new subscription or we can process the subscription even with a pre-existant user for example
        // because it is not confirmed
        if ($user != null) {
            $newsletterSubscription->logger->info("Email address subscribed but not confirmed");
            $user = array('id' => $user->id);
        } else {
            $newsletterSubscription->logger->info("New email address");
            $user = array('email' => $email);
        }

        $user = $newsletterSubscription->update_user_from_request($user);

        $user['token'] = $newsletterSubscription->get_token();
        $ip = $newsletterSubscription->get_remote_ip();
        $ip = $newsletterSubscription->process_ip($ip);
        $user['ip'] = $ip;
        $user['geo'] = 0;
        $user['status'] = $opt_in == NewsletterSubscription::OPTIN_SINGLE ? Newsletter::STATUS_CONFIRMED : Newsletter::STATUS_NOT_CONFIRMED;
        $user['updated'] = time();

        $user['name'] = $newsletterSubscription->normalize_name($firstname);
        $user['surname'] = $newsletterSubscription->normalize_name($lastname);
        foreach ($lists as $k => $v) {
            $user['list_'.trim($v)] = 1;
        }
        if (count($additional) > 0) {
            foreach ($additional as $k => $v) {
                $user[$k] = $v;
            }
        }

        $user = apply_filters('newsletter_user_subscribe', $user);

        $user = $newsletterSubscription->save_user($user);

        $newsletterSubscription->add_user_log($user, 'subscribe');

        // Notification to admin (only for new confirmed subscriptions)
        if ($user->status == Newsletter::STATUS_CONFIRMED) {
            do_action('newsletter_user_confirmed', $user);
            $newsletterSubscription->notify_admin($user, 'Newsletter subscription');
            setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');
        }

        $newsletterSubscription->send_message(($user->status == Newsletter::STATUS_CONFIRMED) ? 'confirmed' : 'confirmation', $user);

        return $user;
    }
}