<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * RIMA Academy - Product Meta: Zoom & PDF Documents
 */

// Add custom product data tab
add_filter( 'woocommerce_product_data_tabs', 'rima_add_product_tabs' );
function rima_add_product_tabs( $tabs ) {
    $tabs['rima_zoom'] = array(
        'label'    => __( '🎥 Zoom Sessions', 'rima-academy' ),
        'target'   => 'rima_zoom_data',
        'class'    => array(),
        'priority' => 21,
    );
    $tabs['rima_docs'] = array(
        'label'    => __( '📄 Documente Curs', 'rima-academy' ),
        'target'   => 'rima_docs_data',
        'class'    => array(),
        'priority' => 22,
    );
    return $tabs;
}

// Render Zoom tab content
add_action( 'woocommerce_product_data_panels', 'rima_zoom_panel' );
function rima_zoom_panel() {
    global $post;
    $sessions = get_post_meta( $post->ID, '_rima_zoom_sessions', true );
    if ( ! is_array( $sessions ) ) $sessions = array();
    ?>
    <div id="rima_zoom_data" class="panel woocommerce_options_panel">
        <div class="options_group">
            <h4 style="padding:12px 12px 0"><?php _e( 'Sesiuni Zoom pentru acest curs', 'rima-academy' ); ?></h4>
            <p style="padding:0 12px;color:#888;font-size:12px"><?php _e( 'Adaugă una sau mai multe sesiuni Zoom. Acestea vor apărea în My Account și vor fi trimise prin email la confirmarea comenzii.', 'rima-academy' ); ?></p>
            <div id="rima-zoom-sessions-wrap">
                <?php foreach ( $sessions as $i => $session ) : ?>
                <div class="rima-zoom-session" style="background:#f9f9f9;border:1px solid #ddd;padding:12px;margin:12px;border-radius:6px">
                    <p><label><?php _e('Titlu Sesiune', 'rima-academy'); ?></label><br>
                        <input type="text" name="rima_zoom_sessions[<?php echo $i; ?>][title]" value="<?php echo esc_attr( $session['title'] ?? '' ); ?>" class="wide" />
                    </p>
                    <p><label><?php _e('Data', 'rima-academy'); ?></label><br>
                        <input type="date" name="rima_zoom_sessions[<?php echo $i; ?>][date]" value="<?php echo esc_attr( $session['date'] ?? '' ); ?>" />
                    </p>
                    <p><label><?php _e('Ora (24h)', 'rima-academy'); ?></label><br>
                        <input type="time" name="rima_zoom_sessions[<?php echo $i; ?>][time]" value="<?php echo esc_attr( $session['time'] ?? '' ); ?>" />
                    </p>
                    <p><label><?php _e('Zoom Meeting ID', 'rima-academy'); ?></label><br>
                        <input type="text" name="rima_zoom_sessions[<?php echo $i; ?>][meeting_id]" value="<?php echo esc_attr( $session['meeting_id'] ?? '' ); ?>" />
                    </p>
                    <p><label><?php _e('Zoom Parolă', 'rima-academy'); ?></label><br>
                        <input type="text" name="rima_zoom_sessions[<?php echo $i; ?>][password]" value="<?php echo esc_attr( $session['password'] ?? '' ); ?>" />
                    </p>
                    <p><label><?php _e('Zoom Link (Join URL)', 'rima-academy'); ?></label><br>
                        <input type="url" name="rima_zoom_sessions[<?php echo $i; ?>][join_url]" value="<?php echo esc_attr( $session['join_url'] ?? '' ); ?>" class="wide" />
                    </p>
                    <button type="button" class="button rima-remove-session" style="background:#FF1949;color:white;border-color:#FF1949"><?php _e('Șterge sesiunea', 'rima-academy'); ?></button>
                </div>
                <?php endforeach; ?>
            </div>
            <p style="padding:12px">
                <button type="button" id="rima-add-session" class="button button-primary"><?php _e( '+ Adaugă Sesiune Zoom', 'rima-academy' ); ?></button>
            </p>
            <script>
            (function($){
                var idx = <?php echo count($sessions); ?>;
                $('#rima-add-session').on('click', function(){
                    var tpl = `<div class="rima-zoom-session" style="background:#f9f9f9;border:1px solid #ddd;padding:12px;margin:12px;border-radius:6px">
                        <p><label>Titlu Sesiune</label><br><input type="text" name="rima_zoom_sessions[${idx}][title]" value="" class="wide" /></p>
                        <p><label>Data</label><br><input type="date" name="rima_zoom_sessions[${idx}][date]" value="" /></p>
                        <p><label>Ora (24h)</label><br><input type="time" name="rima_zoom_sessions[${idx}][time]" value="" /></p>
                        <p><label>Zoom Meeting ID</label><br><input type="text" name="rima_zoom_sessions[${idx}][meeting_id]" value="" /></p>
                        <p><label>Zoom Parolă</label><br><input type="text" name="rima_zoom_sessions[${idx}][password]" value="" /></p>
                        <p><label>Zoom Link (Join URL)</label><br><input type="url" name="rima_zoom_sessions[${idx}][join_url]" value="" class="wide" /></p>
                        <button type="button" class="button rima-remove-session" style="background:#FF1949;color:white;border-color:#FF1949">Șterge sesiunea</button>
                    </div>`;
                    $('#rima-zoom-sessions-wrap').append(tpl);
                    idx++;
                });
                $(document).on('click', '.rima-remove-session', function(){ $(this).closest('.rima-zoom-session').remove(); });
            })(jQuery);
            </script>
        </div>
    </div>
    <?php
}

// Render Docs tab content
add_action( 'woocommerce_product_data_panels', 'rima_docs_panel' );
function rima_docs_panel() {
    global $post;
    $docs = get_post_meta( $post->ID, '_rima_course_documents', true );
    if ( ! is_array( $docs ) ) $docs = array();
    ?>
    <div id="rima_docs_data" class="panel woocommerce_options_panel">
        <div class="options_group">
            <h4 style="padding:12px 12px 0"><?php _e( 'Setări Pacing (Profesor Supraveghetor)', 'rima-academy' ); ?></h4>
            <?php
            $min_days = get_post_meta( $post->ID, '_rima_min_study_days', true );
            $daily_limit = get_post_meta( $post->ID, '_rima_daily_time_limit', true );
            if ( ! $min_days ) $min_days = '';
            if ( ! $daily_limit ) $daily_limit = '';
            
            woocommerce_wp_text_input( array(
                'id'          => '_rima_min_study_days',
                'label'       => __( 'Zile Minime de Studiu', 'rima-academy' ),
                'description' => __( 'Ex: 5. Sistemul va împărți numărul total de pagini al fiecărui document la acest număr pentru a bloca accesul zilnic (limita dinamică de pagini). Lasă gol pentru a dezactiva.', 'rima-academy' ),
                'desc_tip'    => true,
                'type'        => 'number',
                'value'       => $min_days,
                'custom_attributes' => array( 'step' => '1', 'min' => '1' )
            ) );
            
            woocommerce_wp_text_input( array(
                'id'          => '_rima_daily_time_limit',
                'label'       => __( 'Limită Zilnică de Timp (Minute)', 'rima-academy' ),
                'description' => __( 'Ex: 90 (reprezintă 1h 30m). Studentul nu va putea citi PDF-urile din acest curs mai mult de X minute pe zi.', 'rima-academy' ),
                'desc_tip'    => true,
                'type'        => 'number',
                'value'       => $daily_limit,
                'custom_attributes' => array( 'step' => '1', 'min' => '1' )
            ) );
            ?>
        </div>
        
        <div class="options_group">
            <h4 style="padding:12px 12px 0"><?php _e( 'Documente PDF pentru curs', 'rima-academy' ); ?></h4>
            <p style="padding:0 12px;color:#888;font-size:12px"><?php _e( 'Încarcă fișiere PDF. Accesul este securizat — doar studenții care au cumpărat cursul pot vizualiza documentele.', 'rima-academy' ); ?></p>
            <div id="rima-docs-wrap">
                <?php foreach ( $docs as $i => $doc ) : ?>
                <div class="rima-doc-item" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-bottom:1px solid #eee">
                    <span style="flex:1"><strong><?php echo esc_html($doc['name']); ?></strong> — <a href="<?php echo esc_url( wp_get_attachment_url($doc['attachment_id']) ); ?>" target="_blank">preview</a></span>
                    <input type="hidden" name="rima_docs[<?php echo $i; ?>][attachment_id]" value="<?php echo esc_attr($doc['attachment_id']); ?>" />
                    <input type="text" name="rima_docs[<?php echo $i; ?>][name]" value="<?php echo esc_attr($doc['name']); ?>" placeholder="Nume document" style="width:200px" />
                    <button type="button" class="button rima-remove-doc">✕</button>
                </div>
                <?php endforeach; ?>
            </div>
            <p style="padding:12px">
                <button type="button" id="rima-add-doc" class="button button-primary"><?php _e( '+ Adaugă Document PDF', 'rima-academy' ); ?></button>
            </p>
            <script>
            (function($){
                var docIdx = <?php echo count($docs); ?>;
                $('#rima-add-doc').on('click', function(){
                    var frame = wp.media({ title: 'Selectează PDF', library: { type: 'application/pdf' }, button: { text: 'Selectează' }, multiple: false });
                    frame.on('select', function(){
                        var att = frame.state().get('selection').first().toJSON();
                        var row = `<div class="rima-doc-item" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-bottom:1px solid #eee">
                            <span style="flex:1"><strong>${att.filename}</strong></span>
                            <input type="hidden" name="rima_docs[${docIdx}][attachment_id]" value="${att.id}" />
                            <input type="text" name="rima_docs[${docIdx}][name]" value="${att.title}" placeholder="Nume document" style="width:200px" />
                            <button type="button" class="button rima-remove-doc">✕</button>
                        </div>`;
                        $('#rima-docs-wrap').append(row);
                        docIdx++;
                    });
                    frame.open();
                });
                $(document).on('click', '.rima-remove-doc', function(){ $(this).closest('.rima-doc-item').remove(); });
            })(jQuery);
            </script>
        </div>
    </div>
    <?php
}

// Save product meta
add_action( 'woocommerce_process_product_meta', 'rima_save_product_meta' );
function rima_save_product_meta( $post_id ) {
    // Save Zoom sessions
    if ( isset( $_POST['rima_zoom_sessions'] ) ) {
        $sessions = array();
        foreach ( $_POST['rima_zoom_sessions'] as $session ) {
            $sessions[] = array(
                'title'      => sanitize_text_field( $session['title'] ?? '' ),
                'date'       => sanitize_text_field( $session['date'] ?? '' ),
                'time'       => sanitize_text_field( $session['time'] ?? '' ),
                'meeting_id' => sanitize_text_field( $session['meeting_id'] ?? '' ),
                'password'   => sanitize_text_field( $session['password'] ?? '' ),
                'join_url'   => esc_url_raw( $session['join_url'] ?? '' ),
            );
        }
        update_post_meta( $post_id, '_rima_zoom_sessions', $sessions );
    } else {
        delete_post_meta( $post_id, '_rima_zoom_sessions' );
    }

    // Save Documents
    if ( isset( $_POST['rima_docs'] ) ) {
        $docs = array();
        foreach ( $_POST['rima_docs'] as $doc ) {
            if ( ! empty( $doc['attachment_id'] ) ) {
                $docs[] = array(
                    'attachment_id' => absint( $doc['attachment_id'] ),
                    'name'          => sanitize_text_field( $doc['name'] ?? '' ),
                );
            }
        }
        update_post_meta( $post_id, '_rima_course_documents', $docs );
    } else {
        delete_post_meta( $post_id, '_rima_course_documents' );
    }
    
    if ( isset( $_POST['_rima_min_study_days'] ) ) {
        update_post_meta( $post_id, '_rima_min_study_days', absint( $_POST['_rima_min_study_days'] ) );
    }
    
    if ( isset( $_POST['_rima_daily_time_limit'] ) ) {
        update_post_meta( $post_id, '_rima_daily_time_limit', absint( $_POST['_rima_daily_time_limit'] ) );
    }
}
