<?php
/*
Plugin Name: Simple Target Meter
Description: Ein einfaches Spendenbarometer mit manuellem Fortschrittsupdate.
Version: 1.0
Author: Geistesfunke / André Busch
*/

// Sicherstellen, dass das Plugin nicht direkt aufgerufen wird
if (!defined('ABSPATH')) {
    exit;
}

// Admin-Seite hinzufügen
function simpletargetmeter_add_admin_menu() {
    add_menu_page(
        'Simple Target Meter',                // Seitentitel
        'Simple Target Meter',                // Menü-Name
        'manage_options',                  // Berechtigung
        'simpletargetmeter',                // Slug
        'simpletargetmeter_settings_page',  // Callback-Funktion
        'dashicons-chart-bar'              // Dashicon
    );
}
add_action('admin_menu', 'simpletargetmeter_add_admin_menu');

// Einstellungen registrieren
function simpletargetmeter_register_settings() {
    register_setting('simpletargetmeter_settings', 'spendenziel');
    register_setting('simpletargetmeter_settings', 'spendenstand');
}
add_action('admin_init', 'simpletargetmeter_register_settings');

// Admin-Seiteninhalt
function simpletargetmeter_settings_page() {
    ?>
    <div class="wrap">
        <h1>Simple Target Meter Einstellungen</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('simpletargetmeter_settings');
            do_settings_sections('simpletargetmeter_settings');
            ?>
            <div class="simpletargetmeter-settings-layout">
                <!-- Linke Seite: Einstellungen -->
                <div class="simpletargetmeter-box">
                <div class="simpletargetmeter-settings">
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
                

                <div class="simpletargetmeter-box">
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
                        <div class="simpletargetmeter-preview simpletargetmeter" style="background-color: <?php echo esc_attr(get_option('balkenhintergrundfarbe', '#e0e0e0')); ?>; position: relative; <?php echo $preview_styles; ?>">
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

                    

                    <div class="simpletargetmeter-box">
                        <h2>Anleitung</h2>
                        "Simple Target Meter" ist ein kleines und einfaches Tool, um den Fortschritt Ihrer Spendensammlung oder jedes anderen Fortschritts zu visualisieren. Es ist für Spendenzeiele gedacht, kann aber für jeden anderen Bereich verwendet werden.
                        <ul>
                            
                            <li><strong>Ziel:</strong> Definieren Sie hier das Ziel, das Sie erreichen wollen.</li>
                            <li><strong>Aktuell:</strong> Geben Sie hier ein, wie viel bereits gesammelt oder erreicht wurde.</li>
                            <li><strong>Farben:</strong> Benutzen Sie die Farbwähler, um die Farben des "Simple Target Meter" an ihr Design anzupassen.</li>
                            <li><strong>Sträke:</strong> Legen Sie die Stärke des Fortschrittsbalkens in Pixeln fest.</li>
                            <li>Im Dropdown-Menü können Sie die Ausrichtung (horizontal oder vertikal) des Fortschrittsbalkens auswählen.</li>
                            <li>Vergessen Sie nicht, Ihre Einstellungen zu speichern!</li>
                        </ul>
                        <br>
                        <p>Vielen Dank für das nutzen des simpletargetmeters von <a href="https://geistesfunke-design.de" target="_blank">Geistesfunke</a>!</p>
                        <p>André Busch</p>
                        <p><a href="https://geistesfunke-design.de" target="_blank"><img src="<?php echo plugins_url('img/newLogo_Lang_w400.png', __FILE__); ?>"></a></p>
                    </div>

                    
            </div>
            
        </form>
    </div>
    <?php
}


function simpletargetmeter_save_params() {
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
add_action('admin_init', 'simpletargetmeter_save_params');


// Shortcode für das simpletargetmeter
function simpletargetmeter_shortcode() {
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
    <div class="simpletargetmeter" style="background-color: <?php echo $balkenhintergrundfarbe; ?>; <?php echo $styles; ?>">
        <div class="spendenbalken" style="<?php echo $balken_styles; ?>"></div>
    </div>
    <p><?php echo esc_html($prozent); ?>% erreicht – <?php echo esc_html($stand); ?>€ von <?php echo esc_html($ziel); ?>€</p>
    <?php
    return ob_get_clean();
}
add_shortcode('simpletargetmeter', 'simpletargetmeter_shortcode');

// CSS einbinden (Frontend)
function simpletargetmeter_enqueue_styles() {
    wp_enqueue_style('simpletargetmeter-style', plugins_url('css/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'simpletargetmeter_enqueue_styles');

// CSS einbinden (Admin)
function simpletargetmeter_admin_styles($hook) {
    // Nur für die simpletargetmeter-Seite CSS laden
    if ($hook !== 'toplevel_page_simpletargetmeter') {
        return;
    }

    wp_enqueue_style('simpletargetmeter-admin-style', plugins_url('css/admin-style.css', __FILE__));
    wp_enqueue_style('wp-color-picker'); // WordPress-Farbauswahl
    wp_enqueue_script('simpletargetmeter-color-picker', plugins_url('js/color-picker.js', __FILE__), ['wp-color-picker'], false, true);
}
add_action('admin_enqueue_scripts', 'simpletargetmeter_admin_styles');
