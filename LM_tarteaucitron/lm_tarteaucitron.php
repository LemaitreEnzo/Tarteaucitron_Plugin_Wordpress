<?php

/**
 * Plugin Name: LM_tarteaucitron
 * Description: Ajoute le script tarteaucitron.js à l'ouverture du site.
 * Version: 1.0
 * Author: Enzo Lemaitre
 */

function lm_enqueue_tarteaucitron_script()
{
    wp_enqueue_script('tarteaucitron', plugin_dir_url(__FILE__) . '/tarteaucitron/tarteaucitron.min.js', array(), null, true);

    $options = get_option('lm_tarteaucitron_settings');
    $hashtag = isset($options['hashtag']) ? $options['hashtag'] : '#tarteaucitron';
    $highPrivacy = isset($options['highPrivacy']) ? $options['highPrivacy'] : 'true';
    $acceptAllCta = isset($options['AcceptAllCta']) ? $options['AcceptAllCta'] : 'true';
    $orientation = isset($options['orientation']) ? $options['orientation'] : 'middle';
    $adblocker = isset($options['adblocker']) ? $options['adblocker'] : 'false';
    $showAlertSmall = isset($options['showAlertSmall']) ? $options['showAlertSmall'] : 'false';
    $cookieslist = isset($options['cookieslist']) ? $options['cookieslist'] : 'false';

    wp_add_inline_script(
        'tarteaucitron',
        'tarteaucitron.init({
            privacyUrl: "",
            bodyPosition: "top", 
            hashtag: "' . esc_js($hashtag) . '",
            cookieName: "tarteaucitron",
            orientation: "' . esc_js($orientation) . '",
            groupServices: true,
            showDetailsOnClick: true,
            serviceDefaultState: "wait",                    
            showAlertSmall: ' . esc_js($showAlertSmall) . ',
            cookieslist: ' . esc_js($cookieslist) . ',
            closePopup: true,
            showIcon: true,
            iconPosition: "BottomRight",
            adblocker: ' . esc_js($adblocker) . ',              
            DenyAllCta: true,
            AcceptAllCta: ' . esc_js($acceptAllCta) . ',
            highPrivacy: ' . esc_js($highPrivacy) . ',
            alwaysNeedConsent: false,
            "handleBrowserDNTRequest": false,
            "removeCredit": false,
            "moreInfoLink": true,
            "useExternalCss": false,
            "useExternalJs": false,        
            "readmoreLink": "",
            "mandatory": true,
            "mandatoryCta": false,
            "googleConsentMode": true,
            "partnersList": true,
        });
        
        tarteaucitron.user.googleFonts = "families";

        (tarteaucitron.job = tarteaucitron.job || []).push("googlefonts");

        (tarteaucitron.job = tarteaucitron.job || []).push("linkedin");

        (tarteaucitron.job = tarteaucitron.job || []).push("m6meteo");

        (tarteaucitron.job = tarteaucitron.job || []).push("amazon");
        '

    );
}

add_action('wp_enqueue_scripts', 'lm_enqueue_tarteaucitron_script');


function lm_add_admin_menu()
{
    add_menu_page(
        'Réglages Tarteaucitron',
        'Tarteaucitron',
        'manage_options',
        'lm-tarteaucitron',
        'lm_tarteaucitron_options_page'
    );

    add_submenu_page(
        'lm-tarteaucitron',
        'Sous-menu Tarteaucitron',
        'Sous-menu',
        'manage_options',
        'lm-tarteaucitron-sousmenu',
        'lm_tarteaucitron_sousmenu_page'
    );
}
add_action('admin_menu', 'lm_add_admin_menu');


function lm_tarteaucitron_options_page()
{
?>
    <div class="wrap">
        <h1>Paramètres de Tarteaucitron.js</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('lm_tarteaucitron_settings_group');
            do_settings_sections('lm_tarteaucitron');
            submit_button();
            ?>
        </form>
    </div>
<?php
}


function lm_tarteaucitron_sousmenu_page()
{
?>
    <div class="wrap">
        <h1>Sous-menu Tarteaucitron</h1>
        <p>Contenu du sous-menu Tarteaucitron.</p>
    </div>
<?php
}

function lm_tarteaucitron_settings_init()
{
    register_setting('lm_tarteaucitron_settings_group', 'lm_tarteaucitron_settings');

    add_settings_section('lm_tarteaucitron_main', 'Configuration', null, 'lm_tarteaucitron');

    lm_add_text_field_with_description('hashtag', 'Hashtag', 'Définissez le hashtag pour le service.');

    lm_add_select_field_with_description('highPrivacy', 'High Privacy', ['false' => 'Non', 'true' => 'Oui'], 'Activer la confidentialité élevée.');
    lm_add_select_field_with_description('AcceptAllCta', 'Accept All CTA', ['false' => 'Non', 'true' => 'Oui'], 'Afficher un bouton d’acceptation pour tout.');
    lm_add_select_field_with_description('orientation', 'Orientation', ['top' => 'Haut', 'bottom' => 'Bas', 'middle' => 'Milieu', 'popup' => 'Popup', 'banner' => 'Bannière'], 'Définir la position du bandeau.');
    lm_add_select_field_with_description('adblocker', 'Adblocker', ['false' => 'Non', 'true' => 'Oui'], 'Activer la détection d’adblocker.');
    lm_add_select_field_with_description('showAlertSmall', 'Show Alert Small', ['false' => 'Non', 'true' => 'Oui'], 'Afficher un petit message d’alerte.');
    lm_add_select_field_with_description('cookieslist', 'Cookies List', ['false' => 'Non', 'true' => 'Oui'], 'Afficher la liste des cookies.');
}
add_action('admin_init', 'lm_tarteaucitron_settings_init');

function lm_add_text_field_with_description($key, $label, $description)
{
    add_settings_field(
        $key,
        $label,
        function () use ($key, $description) {
            $options = get_option('lm_tarteaucitron_settings');
            $value = isset($options[$key]) ? esc_attr($options[$key]) : '#tarteaucitron';
            echo "<input type='text' name='lm_tarteaucitron_settings[$key]' value='$value' />";
            echo "<span class='description' style='margin-left: 100px;'>$description</span>";
        },
        'lm_tarteaucitron',
        'lm_tarteaucitron_main'
    );
}

function lm_add_select_field_with_description($key, $label, $options_array, $description)
{
    add_settings_field(
        $key,
        $label,
        function () use ($key, $options_array, $description) {
            $options = get_option('lm_tarteaucitron_settings');
            $value = isset($options[$key]) ? esc_attr($options[$key]) : '';
            echo "<select name='lm_tarteaucitron_settings[$key]'>";
            foreach ($options_array as $opt_value => $opt_label) {
                $selected = ($value === $opt_value) ? 'selected' : '';
                echo "<option value='$opt_value' $selected>$opt_label</option>";
            }
            echo "</select>";
            echo "<span class='description' style='margin-left: 200px;'>$description</span>";
        },
        'lm_tarteaucitron',
        'lm_tarteaucitron_main'
    );
}


function m6_meteo()
{
    echo '<div class="tac_m6meteo" data-id="id" width="width" height="height"></div>';
}
add_action('wp_footer', 'm6_meteo');

function instagram()
{
    echo '<span class="tacLinkedin"></span><script type="IN/Share" data-counter="top"></script>';
}
add_action('wp_footer', 'instagram');

function amazon(){
    echo '<div class="amazon_product" amazonid="1740576742" productid="B0CJ2STZGN"></div>';
}
add_action('wp_footer', 'amazon');