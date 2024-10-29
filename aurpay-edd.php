<?php

/**
 * Plugin Name: Aurpay Crypto payment for Easy Digital Downloads
 * Plugin URI: https://dashboard.aurpay.net
 * Description: Pay with Crypto For Easy Digital Downloads, Let your customer pay with ETH, USDC, USDT, DAI, lowest fees, non-custodail & no fraud/chargeback, 50+ cryptos. Invoice, payment link, payment button.
 * Version: 1.2.1
 * Author: Aurpay
 * Author URI: https://www.aurpay.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: Expand customer base with crypto payment, non-custodail & no fraud/chargeback, low fees, 50+ cryptos. Invoice, payment link, payment button.
 * Tags: Crypto, cryptocurrency, crypto payment, erc20, cryptocurrency, e-commerce, bitcoin, bitcoin lighting network, ethereum, crypto pay, smooth withdrawals, cryptocurrency payments, low commission, pay with meta mask, payment button, invoice, crypto woocommerce，bitcoin woocommerce，ethereum，pay crypto，virtual currency，bitcoin wordpress plugin，free crypto plugin
 * Requires at least: 5.8
 * Requires PHP: 7.2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define gateway name
define("AURPAY_GATEWAY_NAME", "edd_aurpay_gateway");

// Registering Aurpay Gateway as a Payment Gateway in EDD
function aurpay_edd_register_gateway($gateways)
{
    $gateways[AURPAY_GATEWAY_NAME] = array(
        'admin_label' => 'Aurpay Gateway',
        'checkout_label' => __('Aurpay Crypto Payment Gateway', 'easy-digital-downloads'),
    );
    return $gateways;
}
add_filter('edd_payment_gateways', 'aurpay_edd_register_gateway');

// Register a subsection for Aurpay Gateway in gateway options tab
function aurpay_edd_register_gateway_section($gateway_sections)
{
    $gateway_sections[AURPAY_GATEWAY_NAME] = __('Aurpay Gateway', 'easy-digital-downloads');
    return $gateway_sections;
}
add_filter('edd_settings_sections_gateways', 'aurpay_edd_register_gateway_section');


$aurpay_edd_title = "";
$aurpay_edd_merchant_id = "";
$aurpay_edd_merchant_key = "";

// Register the Aurpay Gateway settings for Aurpay Gateway subsection
function aurpay_edd_add_gateway_settings($gateway_settings)
{
    global $aurpay_edd_title, $aurpay_edd_merchant_id, $aurpay_edd_merchant_key;

    $aurpay_intro = '<p style="color:blue"><b>Remember to select Aurpay as one of your active payment gateway.</b></p>';
    $aurpay_intro .= '<p style="margin-top: 10px"><b>AURPAY official <a href="https://aurpay.net/" target="_blank">website.</a></b></p>';
    $aurpay_intro .= '<p style="margin-top: 10px;">Aurpay has no setup fees, no subscription fees, no hidden costs, no chargebacks. Pure non-custodial, no third party charge, all transactions are peer-to-peer. Merchants send crypto payment link directly to customers with no middleman, no code required.</p>';
    $aurpay_intro .= '<p style="margin-top: 20px;"><b>PARTNER INCENTIVE REWARD PROGRAM!</b></p>';
    $aurpay_intro .= '<p style="margin-top: 10px;">Join hundreds of popular WordPress, WooCommerce sellers benefiting from using Aurpay as their global growth partner. Start accepting Crypto in 1 minute and see the immediate impact of our managed platform.</p>';
    $aurpay_intro .= '<p style="margin-top: 20px;"><b>Learn more about <a href="https://aurpay.net/partner/" target="_blank">Partner</a> Program!</b></p>';
    $aurpay_intro .= '<p style="margin-top: 10px;">Register a partner account and get a percentage of their transaction-based profit. dashboard.</p>';
    $aurpay_intro .= '<p>Easy sign-up referral link to get merchants. Lifetime reward. Manage your merchants in the partner dashboard. The more merchants you bring, the more reward you get!</p>';
    $aurpay_intro .= '<p style="margin-top: 20px;"><a href="https://dashboard.aurpay.net/" target="_blank">Get Started</a></p>';

    $aurpay_settings = array(
        AURPAY_GATEWAY_NAME => array(
            'id' => AURPAY_GATEWAY_NAME,
            'name' => '<a id="aurpay"></a><strong>' . __('Aurpay', 'easy-digital-downloads') . '</strong>',
            'desc' => __('AURPAY official website.', 'easy-digital-downloads'),
            'type' => 'header',
        ),
        AURPAY_GATEWAY_NAME . '_intro' => array(
            'id' => AURPAY_GATEWAY_NAME . '_intro',
            'name' => "<a target='_blank' href='https://aurpay.net/'><img border='0' style='width: 190px;height: 60px;'src='" . plugins_url('/images/aurpay.png', __FILE__) . "'></a>",
            'desc' => $aurpay_intro,
            'type' => 'descriptive_text',
        ),
        AURPAY_GATEWAY_NAME . '_title' => array(
            'id' => AURPAY_GATEWAY_NAME . '_title',
            'name' => __('Title', 'easy-digital-downloads'),
            'desc' => __('Payment method title that the customer will see on your checkout page', 'easy-digital-downloads'),
            'type' => 'text',
            'size' => 'regular',
            'std' => $aurpay_edd_title
        ),
        AURPAY_GATEWAY_NAME . '_merchant_id' => array(
            'id' => AURPAY_GATEWAY_NAME . '_merchant_id',
            'name' => __('MerchantID', 'easy-digital-downloads'),
            'desc' => __('Aurpay Merchant ID', 'easy-digital-downloads'),
            'type' => 'text',
            'size' => 'regular',
            'std' => $aurpay_edd_merchant_id
        ),
        AURPAY_GATEWAY_NAME . '_merchant_key' => array(
            'id' => AURPAY_GATEWAY_NAME . '_merchant_key',
            'name' => __('PublicKey', 'easy-digital-downloads'),
            'desc' => __('Aurpay Merchant Public Key', 'easy-digital-downloads'),
            'type' => 'text',
            'size' => 'regular',
            'std' => $aurpay_edd_merchant_key
        ),
    );

    $aurpay_settings = apply_filters('edd_' . AURPAY_GATEWAY_NAME . '_settings', $aurpay_settings);
    $gateway_settings[AURPAY_GATEWAY_NAME] = $aurpay_settings;
    return $gateway_settings;
}
add_filter('edd_settings_gateways', 'aurpay_edd_add_gateway_settings');

function aurpay_edd_init_settings()
{
    global $edd_options;
    
    $aurpay_edd_title = edd_get_option(AURPAY_GATEWAY_NAME . '_title', '');
    $aurpay_edd_merchant_id = edd_get_option(AURPAY_GATEWAY_NAME . '_merchant_id', '');
    $aurpay_edd_merchant_key = edd_get_option(AURPAY_GATEWAY_NAME . '_merchant_key', '');
    
    $arr = array(AURPAY_GATEWAY_NAME . '_title', AURPAY_GATEWAY_NAME . '_merchant_id', AURPAY_GATEWAY_NAME . '_merchant_key');

    foreach ($arr as $v) {
        $k = str_replace(AURPAY_GATEWAY_NAME . '_', '', $v);
        $k = (isset($edd_options[$v])) ? $edd_options[$v] : '';
    }

    if (!$aurpay_edd_title) {
        $aurpay_edd_title = __('Aurpay Crypto Payment Gateway', 'easy-digital-downloads');
        edd_update_option(AURPAY_GATEWAY_NAME . '_title', $aurpay_edd_title);
    }

    $aurpay_edd_merchant_id = trim($aurpay_edd_merchant_id);
    edd_update_option(AURPAY_GATEWAY_NAME . '_merchant_id', $aurpay_edd_merchant_id);

    $aurpay_edd_merchant_key = trim($aurpay_edd_merchant_key);
    edd_update_option(AURPAY_GATEWAY_NAME . '_merchant_key', $aurpay_edd_merchant_key);

    if (isset($_GET["page"]) && isset($_GET["tab"]) && $_GET["page"] == "edd-settings" && $_GET["tab"] == "gateways") {
        try {
            aurpay_edd_verify_aurpay_key($aurpay_edd_merchant_id, $aurpay_edd_merchant_key);
        } catch (Exception $e) {
            aurpay_edd_log_error("[aurpay_edd_init_settings] request to aurpay key verification failed, error:" . json_encode($e));
        }
    }
}

function aurpay_edd_verify_aurpay_key($merchant_id, $merchant_key)
{
    $key_result = wp_remote_get('https://dashboard.aurpay.net/api/plugin/key/verify?id=' . $merchant_id . '&key=' . $merchant_key . '&name=EASYDIGITALDOWNLOADS&url=' . parse_url(site_url(), PHP_URL_HOST));
    $response_data = json_decode($key_result['body'], true);

    if (!($response_data['data'])) {
        add_action('admin_notices', 'aurpay_edd_admin_notice_for_key');
        add_action('admin_notices', 'aurpay_edd_admin_notice_for_aurpay_active');
    }
}

function aurpay_edd_admin_notice_for_key()
{
?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('[Aurpay EDD] The Aurpay MerchantID and PublicKey you entered is incorrect. Please check the video link for more information.', 'easy-digital-downloads'); ?>
            (<a href="https://youtu.be/zLLLjBnuc3g" target="blank">https://youtu.be/zLLLjBnuc3g</a>)</p>
    </div>
<?php
}


function aurpay_edd_admin_notice_for_aurpay_active()
{
?>
    <div class="notice notice-info is-dismissible" style="color: #fff; background-image: linear-gradient(to right , #529df8, #541ccc);">
        <p><?php _e('[Aurpay EDD] Remember to select Aurpay as one of your active payment gateway.', 'easy-digital-downloads'); ?></p>
    </div>
    <?php
}

function aurpay_edd_log_error($message)
{
    error_log(date('Y-m-d H:i:s') . ' ERROR: ' . $message . "\n", 3, WP_PLUGIN_DIR . "/aurpay-crypto-payment-for-easy-digital-downloads/logs/aurpay-edd-error-log.log");
}

function aurpay_edd_process_payment($purchase_data)
{
    if (!wp_verify_nonce($purchase_data['gateway_nonce'], 'edd-gateway')) {
        aurpay_edd_log_error("[aurpay_edd_process_payment] gateway_nonce is invalid: " . $purchase_data['gateway_nonce']);
        wp_die(__('Nonce verification has failed', 'aurpay-edd'), __('Error', 'aurpay-edd'), array('response' => 403));
    }

    $payment_data = array(
        "price" => $purchase_data['price'],
        "date" => $purchase_data['date'],
        "user_email" => $purchase_data['user_email'],
        "purchase_key" => $purchase_data['purchase_key'],
        "currency" => edd_get_currency(),
        "downloads" => $purchase_data['downloads'],
        "user_info" => $purchase_data['user_info'],
        "cart_details" => $purchase_data['cart_details'],
        "status" => "pending"
    );

    $payment = edd_insert_payment($payment_data);

    if ($payment) {
        $userID = edd_get_payment_user_id($payment);

        if ($userID == "-1") {
            $userID = 0;
        }

        $user = (!$userID) ? __('Guest', 'aurpay-edd') : "<a href='" . admin_url("user-edit.php?user_id=" . $userID) . "'>user" . $userID . "</a>";
        edd_insert_payment_note($payment, sprintf(__('Order Created by %s. <br/> Awaiting cryptocurrency payment ...', 'aurpay-edd'), $user) . '<br/>');

        edd_empty_cart();
        edd_send_to_success_page();
    } else {
        aurpay_edd_log_error("[aurpay_edd_process_payment] Payment creation failed while processing aurpay crypto payment. Payment data: " . json_encode($payment_data));
        edd_record_gateway_error(__('Payment Error', 'aurpay-edd'), sprintf(__('Payment creation failed while processing crypto purchase. Payment data: %s', 'aurpay-edd'), json_encode($payment_data)), $payment);
        edd_send_back_to_checkout('?payment-mode=' . $purchase_data['post_data']['edd-gateway']);
    }
}
add_action('edd_gateway_' . AURPAY_GATEWAY_NAME, 'aurpay_edd_process_payment');

function aurpay_edd_cryptocoin_payment($payment)
{
    if (edd_get_payment_gateway($payment->ID) == AURPAY_GATEWAY_NAME && is_object($payment)) {
        $status = $payment->status;
        $amount = edd_get_payment_amount($payment->ID);
        $currency = edd_get_payment_currency_code($payment->ID);
        $orderID = $payment->ID;
        $userID = edd_get_payment_user_id($payment->ID);

        if (!$userID) {
            $userID = "guest";
        } elseif ($userID == "-1") {
            $userID = 0;
        }

        if ($status == "complete") {
            return true;
        }

        $aurpay_edd_merchant_id = edd_get_option(AURPAY_GATEWAY_NAME . '_merchant_id', '');
        $aurpay_edd_merchant_key = edd_get_option(AURPAY_GATEWAY_NAME . '_merchant_key', '');

        if (!$payment || !$payment->ID) {
            aurpay_edd_log_error("[cryptocoin_payment] Unable to get payment object. Payment data: " . json_encode($payment));
            echo '<h3>' . esc_html(__('ERROR', 'aurpay-edd')) . '</h3>' . esc_html(PHP_EOL);
            echo "<p class='edd-alert edd-alert-error'>" . esc_html(__('Unable to get payment object. You can contact the email(contact@aurpay.net) to get more help.', 'aurpay-edd')) . '</p>';
            return false;
        } else {
            if ($amount < 0) {
                aurpay_edd_log_error("[cryptocoin_payment] Order amount < 0, amount: " . $amount);
                echo '<h3>' . esc_html(__('ERROR', 'aurpay-edd')) . '</h3>' . esc_html(PHP_EOL);
                echo "<p class='edd-alert edd-alert-error'>" . esc_html(__("The order amount must be greater than or equal to 0. Please contact us(contact@aurpay.net) if you need assistance.", 'aurpay-edd') . esc_html(" ") . esc_html($currency)) . "</p>";
                return false;
            } elseif (!$aurpay_edd_merchant_id || $aurpay_edd_merchant_id == "" || !$aurpay_edd_merchant_key || $aurpay_edd_merchant_key == "") {
                aurpay_edd_log_error("[cryptocoin_payment] merchant_id or mercahnt_key is invalid, merchant_id: " . $aurpay_edd_merchant_id . " merchant_key: " . $aurpay_edd_merchant_key);
                echo '<h3>' . esc_html(__('ERROR', 'aurpay-edd')) . '</h3>' . esc_html(PHP_EOL);
                echo "<p class='edd-alert edd-alert-error'>" . esc_html(__("The merchant did not set the plugin configuration. Please contact merchant or us(contact@aurpay.net) if you need assistance.", 'aurpay-edd')) . "</p>";
                return false;
            } else {
                aurpay_edd_generate_checkout_token($orderID, $amount, $currency);
                return true;
            }
        }
    }

    return false;
}

function aurpay_edd_generate_checkout_token($orderID, $amount, $currency_code)
{
    global $wp;

    $aurpay_edd_merchant_id = edd_get_option(AURPAY_GATEWAY_NAME . '_merchant_id', '');
    $aurpay_edd_merchant_key = edd_get_option(AURPAY_GATEWAY_NAME . '_merchant_key', '');

    $aurpay_generate_checkout_token = "https://dashboard.aurpay.net/api/order/pay/token";
    $aurpay_checkout_url = "https://dashboard.aurpay.net/#/cashier/choose?token=";

    $platform = "EASYDIGITALDOWNLOADS";
    $callback_url = trim(get_site_url(), "/ ") . "/aurpay.edd.callback.php?status=completed&type=AURPAYEDD&platform=AURPAY&order_id=" . $orderID;

    $current_url = home_url(add_query_arg(array(), $wp->request));
    $succeed_url = $current_url;

    $origin = array(
        'id' => $orderID,
        'price' => $amount,
        'currency' => $currency_code,
        'callback_url' => $callback_url,
        'succeed_url' => $succeed_url,
        'url' => trim(get_site_url(), "/ "),
    );

    $data = array(
        'platform' => $platform,
        'origin' => $origin,
        'user_id' => $aurpay_edd_merchant_id,
        'key' => $aurpay_edd_merchant_key
    );

    $token_result = aurpay_edd_http_post($aurpay_generate_checkout_token, json_encode($data), $aurpay_edd_merchant_key);
    $response_data = json_decode($token_result['body'], true);
    if (isset($response_data['data']) && $response_data['code'] == 0 && isset($response_data['data']['token']) && $response_data['data']['token'] != "") {
        $token = $response_data['data']['token'];
        $redirect_url = "Location: " . $aurpay_checkout_url . $token;
        header($redirect_url);
        die();
    } else {
        aurpay_edd_log_error("[aurpay_edd_generate_checkout_token] request to aurpay failed, response_data:" . json_encode($response_data));
    }

    return $response_data;
}

function aurpay_edd_http_post($url, $data, $API_KEY)
{
    $body = $data;
    $headers = array(
        'Content-Type' => 'application/json; charset=utf-8',
        'Content-Length' => strlen($data),
        'API-KEY' => $API_KEY,
    );
    $args = array(
        'body' => $body,
        'timeout'     => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $headers,
    );

    $response = wp_remote_post($url, $args);

    if ($response) {
        return $response;
    }
    return [];
}

add_action('edd_order_receipt_before_table', 'aurpay_edd_cryptocoin_payment');

function aurpay_edd_callback_parse_request()
{
    ob_start();

    include_once(plugin_dir_path(__FILE__) . "includes/aurpay.edd.callback.php");

    if (ob_get_level() > 0) {
        ob_flush();
    }

    return true;
}

add_action('parse_request', 'aurpay_edd_callback_parse_request');

function aurpay_edd_disable_checkout_userInfo_details()
{
    remove_action('edd_after_cc_fields', 'edd_default_cc_address_fields');
    remove_action('edd_cc_form', 'edd_get_cc_form');

    aurpay_edd_init_settings();
}

add_action('init', 'aurpay_edd_disable_checkout_userInfo_details');

function aurpay_edd_payment_icon($icons = array())
{
    $icons[plugins_url('assets/images/img_logo_1.png', __FILE__)] = 'Aurpay';

    return $icons;
}

add_filter('edd_accepted_payment_icons', 'aurpay_edd_payment_icon');

if (!function_exists('aurpay_edd_render_usage_notice')) {
    function aurpay_edd_render_usage_notice()
    {
        global $pagenow;
        $admin_pages = ['index.php', 'plugins.php'];
        if (in_array($pagenow, $admin_pages)) {
    ?>
            <div class="ap-connection-banner aurpay-usage-notice">

                <div class="ap-connection-banner__container-top-text">
                    <span class="notice-dismiss aurpay-usage-notice__dismiss" title="Dismiss this notice"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <rect x="0" fill="none" width="24" height="24" />
                        <g>
                            <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2l-.5-6h3l-.5 6z" />
                        </g>
                    </svg>
                    <span>You're almost done. Setup Aurpay to enable Crypto Payment for you Easy Digital Downloads site.</span>
                </div>
                <div class="ap-connection-banner__inner">
                    <div class="ap-connection-banner__content">
                        <div class="ap-connection-banner__logo">
                            <img src="<?php echo esc_url(plugins_url('assets/images/logo_aurpay.svg', __FILE__)); ?>" alt="logo">
                        </div>
                        <h2 class="ap-connection-banner__title">Empower Your Business with Aurpay Crypto Payment</h2>
                        <div class="ap-connection-banner__columns">
                            <div class="ap-connection-banner__text">⭐ Get listed on our online directory to attract <span style="color: #007AFF">300 millions</span> of crypto owners. </div>
                            <div class="ap-connection-banner__text">⭐ Earn up to <span style="color: #007AFF">150,000 satoshi</span> rewards for merchants who finished all settings and more. </div>
                        </div>
                        <div class="ap-connection-banner__rows">
                            <div class="ap-connection-banner__text ap-connection-banner__step">By setting up Aurpay, get a merchant account and save your "<span style="color: #007AFF">Merchant ID</span>" & "<span style="color: #007AFF">Public Key</span>" in Easy Digital Downloads Payment settings. </div>
                            <a id="ap-connect-button--alt" rel="external" target="_blank" href="https://dashboard.aurpay.net/#/login?cur_url=/integration&platform=EASYDIGITALDOWNLOADS" class="ap-banner-cta-button ap_step_edd_1">Setup Aurpay</a>
                        </div>
                        <div class="ap-connection-banner__rows" style="display: none;">
                            <div class="ap-connection-banner__text ap-connection-banner__step">Save your PublicKey in EasyDigitalDownloads Payment settings.</div>
                            <a id="ap-connect-button--alt" target="_self" href="<?php echo admin_url('edit.php?post_type=download&page=edd-settings&tab=gateways&section=edd_aurpay_gateway') ?>" class="ap-banner-cta-button ap_step_edd_2">Settings</a>
                        </div>
                    </div>
                    <div class="ap-connection-banner__image-container">
                        <picture>
                            <source type="image/webp" srcset="<?php echo esc_url(plugins_url('assets/images/img_aurpay.webp', __FILE__)); ?> 1x, <?php echo esc_url(plugins_url('assets/images/img_aurpay-2x.webp', __FILE__)); ?> 2x">
                            <img class="ap-connection-banner__image" srcset="<?php echo esc_url(plugins_url('assets/images/img_aurpay.png', __FILE__)); ?> 1x, <?php echo esc_url(plugins_url('assets/images/img_aurpay-2x.png', __FILE__)); ?> 2x" src="<?php echo esc_url(plugins_url('assets/images/img_aurpay.png', __FILE__)); ?>" alt="">
                        </picture>
                        <img class="ap-connection-banner__image-background" src="<?php echo esc_url(plugins_url('assets/images/background.svg', __FILE__)); ?>" />
                    </div>
                </div>
            </div>

<?php

            wp_enqueue_script(
                'aurpay-notice-banner-js',
                plugin_dir_url(__FILE__) . 'assets/js/aurpay-usage-notice.js',
                array('jquery')
            );
        }
    }
}

function aurpay_edd_plugins_loaded()
{
    if (!function_exists('EDD')) {
        return false;
    }

    $aurpay_edd_merchant_id = edd_get_option(AURPAY_GATEWAY_NAME . '_merchant_id', '');
    $aurpay_edd_merchant_key = edd_get_option(AURPAY_GATEWAY_NAME . '_merchant_key', '');

    if (isset($aurpay_edd_merchant_id) && $aurpay_edd_merchant_id != "" && isset($aurpay_edd_merchant_key) && $aurpay_edd_merchant_key != "") {
        return false;
    } else {
        wp_enqueue_style('aurpay-edd-notice-banner-style', plugin_dir_url(__FILE__) . 'assets/css/aurpay-usage-notice.css');
        add_action('admin_notices', 'aurpay_edd_render_usage_notice');
    }
}

add_action('plugins_loaded', 'aurpay_edd_plugins_loaded');

function aurpay_edd_action_links($links, $file)
{
    static $this_plugin;

    if (!class_exists('Easy_Digital_Downloads')) return $links;

    if (false === isset($this_plugin) || true === empty($this_plugin)) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $aurpay_link = '<a href="https://dashboard.aurpay.net/#/login?cur_url=/integration&platform=EASYDIGITALDOWNLOADS" target="_blank" style="color: #39b54a; font-weight: bold;">' . __( 'Get Aurpay', 'aurpay' ) . '</a>';
        $settings_link = '<a href="' . admin_url('edit.php?post_type=download&page=edd-settings&tab=gateways#aurpay') . '">' . __('Settings', 'aurpay-edd') . '</a>';
        array_unshift($links, $aurpay_link, $settings_link);
    }

    return $links;
}

add_filter('plugin_action_links', 'aurpay_edd_action_links', 10, 2);

function aurpay_edd_plugin_row_meta( $plugin_meta, $plugin_file ) 
{
    static $this_plugin;

    if (isset($this_plugin) === false || empty($this_plugin) === true) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ( $this_plugin === $plugin_file ) {
        $row_meta = [
            'dome' => '<a style="color: #39b54a;" href="https://example-wp.aurpay.net/downloads/" aria-label="' . esc_attr( __( 'View Aurpay Demo', 'aurpay-wc' ) ) . '" target="_blank">' . __( 'Demo', 'aurpay-wc' ) . '</a>',
            'video' => '<a style="color: #39b54a;" href="https://youtu.be/zLLLjBnuc3g" aria-label="' . esc_attr( __( 'View Aurpay Video Tutorials', 'aurpay-wc' ) ) . '" target="_blank">' . __( 'Video Tutorials', 'aurpay-wc' ) . '</a>',
        ];

        $plugin_meta = array_merge( $plugin_meta, $row_meta );
    }

    return $plugin_meta;
}

add_filter('plugin_row_meta', 'aurpay_edd_plugin_row_meta', 10, 2);