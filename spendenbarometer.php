<?php
/*
Plugin Name: Spendenbarometer
Description: Ein einfaches Spendenbarometer mit manuellem Fortschrittsupdate.
Version: 1.0
Author: Geistesfunke / André Busch
*/

// Sicherstellen, dass das Plugin nicht direkt aufgerufen wird
if (!defined('ABSPATH')) {
    exit;
}

// Admin-Seite hinzufügen
function spendenbarometer_add_admin_menu() {
    add_menu_page(
        'Spendenbarometer',                // Seitentitel
        'Spendenbarometer',                // Menü-Name
        'manage_options',                  // Berechtigung
        'spendenbarometer',                // Slug
        'spendenbarometer_settings_page',  // Callback-Funktion
        'dashicons-chart-bar'              // Dashicon
    );
}
add_action('admin_menu', 'spendenbarometer_add_admin_menu');

// Einstellungen registrieren
function spendenbarometer_register_settings() {
    register_setting('spendenbarometer_settings', 'spendenziel');
    register_setting('spendenbarometer_settings', 'spendenstand');
}
add_action('admin_init', 'spendenbarometer_register_settings');

// Admin-Seiteninhalt
function spendenbarometer_settings_page() {
    ?>
    <div class="wrap">
        <h1>Spendenbarometer Einstellungen</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('spendenbarometer_settings');
            do_settings_sections('spendenbarometer_settings');
            ?>
            <div class="spendenbarometer-settings-layout">
                <!-- Linke Seite: Einstellungen -->
                <div class="spendenbarometer-box">
                <div class="spendenbarometer-settings">
                    <label for="spendenziel">Spendenziel (€)</label>
                    <input type="number" id="spendenziel" name="spendenziel" value="<?php echo esc_attr(get_option('spendenziel', 1000)); ?>" />

                    <label for="spendenstand">Aktueller Spendenstand (€)</label>
                    <input type="number" id="spendenstand" name="spendenstand" value="<?php echo esc_attr(get_option('spendenstand', 0)); ?>" />

                    <label for="balkenhintergrundfarbe">Hintergrundfarbe des Fortschrittsbalkens</label>
                    <input type="text" id="balkenhintergrundfarbe" name="balkenhintergrundfarbe" class="color-field" value="<?php echo esc_attr(get_option('balkenhintergrundfarbe', '#e0e0e0')); ?>" />

                    <label for="balkenfarbe">Farbe des Fortschritts</label>
                    <input type="text" id="balkenfarbe" name="balkenfarbe" class="color-field" value="<?php echo esc_attr(get_option('balkenfarbe', '#4caf50')); ?>" />

                    <label for="balkenhoehe">Stärke des Fortschrittsbalkens (in px)</label>
                    <input type="number" id="balkenhoehe" name="balkenhoehe" value="<?php echo esc_attr(get_option('balkenhoehe', 20)); ?>" min="10" max="200" />

                    <label for="balkenausrichtung">Ausrichtung des Barometers</label>
                    <select id="balkenausrichtung" name="balkenausrichtung">
                        <option value="horizontal" <?php selected(get_option('balkenausrichtung', 'horizontal'), 'horizontal'); ?>>Horizontal</option>
                        <option value="vertical" <?php selected(get_option('balkenausrichtung', 'horizontal'), 'vertical'); ?>>Vertikal</option>
                    </select>

                </div>
                <div>
                    <?php submit_button(); ?>
                    </div>
                </div>
                

                <div class="spendenbarometer-box">
                        <h2>Vorschau</h2>
                        <?php
                        // Hole die aktuelle Ausrichtung des Barometers
                        $ausrichtung = get_option('balkenausrichtung', 'horizontal');
                        $balkenhoehe = esc_attr(get_option('balkenhoehe', 20));
                        // Definiere die Stile basierend auf der Ausrichtung
                        $preview_styles = $ausrichtung === 'vertical'
                            ? "width: {$balkenhoehe}px; height: 200px;" // Vertikale Ausrichtung
                            : "height: {$balkenhoehe}px; width: 100%;"; // Horizontale Ausrichtung
                        ?>
                        <!-- Fortschrittsbalken-Vorschau -->
                        <div class="spendenbarometer-preview spendenbarometer" style="background-color: <?php echo esc_attr(get_option('balkenhintergrundfarbe', '#e0e0e0')); ?>; position: relative; <?php echo $preview_styles; ?>">
                            <div class="spendenbalken" style="position: absolute; left: 0; <?php 
                                if ($ausrichtung === 'vertical') {
                                    // Vertikale Balkenstile
                                    echo 'height: ' . min(100, round((get_option('spendenstand', 0) / get_option('spendenziel', 1000)) * 100)) . '%; width: 100%; background-color: ' . esc_attr(get_option('balkenfarbe', '#4caf50')) . ';';
                                } else {
                                    // Horizontale Balkenstile
                                    echo 'width: ' . min(100, round((get_option('spendenstand', 0) / get_option('spendenziel', 1000)) * 100)) . '%; height: 100%; background-color: ' . esc_attr(get_option('balkenfarbe', '#4caf50')) . ';';
                                }
                            ?>"></div>
                        </div>
                    </div>

                    

                    <div class="spendenbarometer-box">
                        <h2>Anleitung</h2>
                        <ul>
                            <li>Mit dem Spendenbarometer können Sie den Fortschritt Ihrer Spendensammlung visualisieren.</li>
                            <li>Im Dropdown-Menü können Sie die Ausrichtung (horizontal oder vertikal) des Fortschrittsbalkens auswählen.</li>
                            <li>Vergessen Sie nicht, Ihre Einstellungen zu speichern!</li>
                        </ul>
                        <br>
                        <p>Vielen Dank für das nutzen des Spendenbarometers von <a href="https://geistesfunke-design.de" target="_blank">Geistesfunke</a>!</p>
                        <p>André Busch</p>
                        <p><a href="https://geistesfunke-design.de" target="_blank"><img src="<?php echo plugins_url('img/newLogo_Lang_w400.png', __FILE__); ?>"></a></p>
                    </div>

                    
            </div>
            
        </form>
    </div>
    <?php
}


function spendenbarometer_save_params() {
    if (isset($_POST['balkenhintergrundfarbe'])) {
        update_option('balkenhintergrundfarbe', sanitize_hex_color($_POST['balkenhintergrundfarbe']));
    }
    if (isset($_POST['balkenfarbe'])) {
        update_option('balkenfarbe', sanitize_hex_color($_POST['balkenfarbe']));
    }
    if (isset($_POST['balkenhoehe'])) {
        update_option('balkenhoehe', intval($_POST['balkenhoehe']));
    }
    if (isset($_POST['balkenausrichtung'])) {
        update_option('balkenausrichtung', sanitize_text_field($_POST['balkenausrichtung']));
    }
}
add_action('admin_init', 'spendenbarometer_save_params');


// Shortcode für das Spendenbarometer
function spendenbarometer_shortcode() {
    $ziel = get_option('spendenziel', 1000);
    $stand = get_option('spendenstand', 0);
    $prozent = ($ziel > 0) ? min(100, round(($stand / $ziel) * 100)) : 0;

    $balkenhintergrundfarbe = esc_attr(get_option('balkenhintergrundfarbe', '#e0e0e0'));
    $balkenfarbe = esc_attr(get_option('balkenfarbe', '#4caf50'));
    $balkenhoehe = esc_attr(get_option('balkenhoehe', 20));
    $ausrichtung = get_option('balkenausrichtung', 'horizontal');

    $styles = $ausrichtung === 'vertical'
        ? "width: {$balkenhoehe}px; height: 200px;"
        : "height: {$balkenhoehe}px; width: 100%;";

    $balken_styles = $ausrichtung === 'vertical'
        ? "width: 100%; height: {$prozent}%; background-color: {$balkenfarbe}; bottom: 0px; position: absolute;"
        : "width: {$prozent}%; height: 100%; background-color: {$balkenfarbe};";

    ob_start();
    ?>
    <div class="spendenbarometer" style="background-color: <?php echo $balkenhintergrundfarbe; ?>; <?php echo $styles; ?>">
        <div class="spendenbalken" style="<?php echo $balken_styles; ?>"></div>
    </div>
    <p><?php echo esc_html($prozent); ?>% erreicht – <?php echo esc_html($stand); ?>€ von <?php echo esc_html($ziel); ?>€</p>
    <?php
    return ob_get_clean();
}
add_shortcode('spendenbarometer', 'spendenbarometer_shortcode');

// CSS einbinden (Frontend)
function spendenbarometer_enqueue_styles() {
    wp_enqueue_style('spendenbarometer-style', plugins_url('css/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'spendenbarometer_enqueue_styles');

// CSS einbinden (Admin)
function spendenbarometer_admin_styles($hook) {
    // Nur für die Spendenbarometer-Seite CSS laden
    if ($hook !== 'toplevel_page_spendenbarometer') {
        return;
    }

    wp_enqueue_style('spendenbarometer-admin-style', plugins_url('css/admin-style.css', __FILE__));
    wp_enqueue_style('wp-color-picker'); // WordPress-Farbauswahl
    wp_enqueue_script('spendenbarometer-color-picker', plugins_url('js/color-picker.js', __FILE__), ['wp-color-picker'], false, true);
}
add_action('admin_enqueue_scripts', 'spendenbarometer_admin_styles');
