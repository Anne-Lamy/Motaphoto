<?php

function enqueue_custom_scripts_styles() {
    // Style principal du thème
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    // Style Sass du thème
    wp_enqueue_style('sass-style', get_template_directory_uri() . '/sass/style.css', array('parent-style'));
    // Script du menu responsive
    wp_enqueue_script('menu-script', get_template_directory_uri() . '/js/menu-responsive.js', array(), true);
    // Script de la modale
    wp_enqueue_script('modal-script', get_template_directory_uri() . '/js/contact-modal.js', array(), true);
    // Script du l'affichage des photos
    wp_enqueue_script('photo-layout', get_template_directory_uri() . '/js/photo-layout.js', array('jquery'), true);
    // Script de la lightbox
    wp_enqueue_script('lightbox-script', get_template_directory_uri() . '/js/lightbox.js', array('jquery'), true);
    // Script du filtre des photos.
    wp_enqueue_script('photo-filter', get_template_directory_uri() . '/js/photo-filter.js', array('jquery'), true);

    // Création et ajout du nonce pour le script 'photo-filter'
    $nonce = wp_create_nonce('photo_filter_nonce');
    wp_localize_script('photo-filter', 'photos_ajax_js', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'permalink' => get_the_permalink(), // Ajouter la valeur de get_the_permalink()
        'nonce' => $nonce, // Ajout du nonce dans les données localisées
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts_styles');


// _______________________________________________________________
// AJOUT DU MENU WP + CONTACT :


function register_my_menus() {
    register_nav_menus(
        array(
            'principal-menu' => __( 'Menu Principal' ),
            'footer-menu' => __( 'Menu Footer' ),
        )
    );
}
add_action( 'init', 'register_my_menus' );

// Ajouter un filtre pour ajouter un lien de contact au menu principal uniquement.
function add_custom_nav_menu_items($items, $args) {
    if ($args->theme_location == 'principal-menu') {
        // enregistrer un identifiant personnalisé pour le lien de contact.
        $items .= '<li><a href="#" id="contact-link" class="motaphoto-menu">Contact</a></li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'add_custom_nav_menu_items', 10, 2);


// _______________________________________________________________
// THEME "MOTAPHOTO" :


// On créer notre lien Motaphto dans le menu de Wordpress.
function motaphoto_add_admin_pages()
{
    // Ajout de la page d'administration de Motaphoto.
    add_menu_page('Paramètre du thème Motaphoto', 'Motaphoto', 'manage_options', 'motaphoto_settings', 'motaphoto_theme_settings', 'dashicons-admin-settings', 60);
}
// On créer le contenu de notre page Metaphoto.
function motaphoto_theme_settings()
{
    echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
    echo '<form action="options.php" method="post" name="motaphoto_settings">';
    echo '<div>';

    settings_fields('motaphoto_settings_fields');
    do_settings_sections('motaphoto_settings_section');
    submit_button();

    echo '</div>';
    echo '</form>';
}


// On créer notre function de lots de réglages.
function motaphoto_settings_register()
{
    // Déclare à WordPress l’existence d’un lot de réglages.
    register_setting('motaphoto_settings_fields', 'motaphoto_settings_fields', 'motaphoto_settings_fields_validate');

    //On créer une section Paramètres où le premier paramètre sera rangé.
    add_settings_section('motaphoto_settings_section', __('Paramètres', 'motaphoto'), 'motaphoto_settings_section_description', 'motaphoto_settings_section');

    // On créer nos paramètres de champs.
                        // 1- Clef unique du champ 2- Nom dans l'interface 3- fonction a appeller pour générer le champ 4- la page ou on veut afficher ce champ 5- la section souhaité.
    add_settings_field('motaphoto_settings_field_description', __('Description', 'motaphoto'), 'motaphoto_settings_field_description_output', 'motaphoto_settings_section', 'motaphoto_settings_section');
    add_settings_field('motaphoto_settings_field_phone_number', __('Numéro de téléphone', 'motaphoto'), 'motaphoto_settings_field_phone_number_output', 'motaphoto_settings_section', 'motaphoto_settings_section');
    add_settings_field('motaphoto_settings_field_email', __('Email', 'motaphoto'), 'motaphoto_settings_field_email_output', 'motaphoto_settings_section', 'motaphoto_settings_section');
}

function motaphoto_settings_section_description()
{
    echo __('Paramètrez les différentes options du thème Motaphoto.', 'motaphoto');
}


// On créer une function pour nettoyer et adapter les valeurs de nos réglages.
function motaphoto_settings_fields_validate($inputs)
{
    // On enregistre le champ quand il est modifié.
    if (isset($_POST) && !empty($_POST)) {
        //Si le champ est défini et non vide dans les données POST.
        if (isset($_POST['motaphoto_settings_field_description']) && !empty($_POST['motaphoto_settings_field_description'])) {
            // On met à jour cette option avec la valeur correspondante.
            update_option('motaphoto_settings_field_description', $_POST['motaphoto_settings_field_description']);
        }
        if (isset($_POST['motaphoto_settings_field_phone_number']) && !empty($_POST['motaphoto_settings_field_phone_number'])) {
            update_option('motaphoto_settings_field_phone_number', $_POST['motaphoto_settings_field_phone_number']);
        }
        if (isset($_POST['motaphoto_settings_field_email']) && !empty($_POST['motaphoto_settings_field_email'])) {
            update_option('motaphoto_settings_field_email', $_POST['motaphoto_settings_field_email']);
        }
    }
    return $inputs;
}


// On paramètres les champs après récupération.
function motaphoto_settings_field_description_output()
{
    // On récupère la valeur de l'option et la clef du champ description.
    $value = get_option('motaphoto_settings_field_description');
    // On affiche le champ.
    echo '<input name="motaphoto_settings_field_description" type="text" value="' . $value . '" />';
}

function motaphoto_settings_field_phone_number_output()
{
    $value = get_option('motaphoto_settings_field_phone_number');
    echo '<input name="motaphoto_settings_field_phone_number" type="text" value="' . $value . '" />';
}

function motaphoto_settings_field_email_output()
{
    $value = get_option('motaphoto_settings_field_email');
    echo '<input name="motaphoto_settings_field_email" type="text" value="' . $value . '" />';
}



// Appelle le lien Motaphoto dans le menu de Wordpress.
add_action('admin_menu', 'motaphoto_add_admin_pages', 10);
// Appelle la fonction d’un lot de réglages.
add_action('admin_init', 'motaphoto_settings_register');


// _______________________________________________________________
// FONCTION POUR CHARGER PLUS DE PHOTOS :

// Définition de la fonction pour la requête AJAX côté serveur
function custom_api_get_photos_callback() {

    // Initialisation d'un tableau pour stocker les données des publications
    $posts_data = array();    

    // Récupération du paramètre de pagination de la requête
    $paged = $_POST['page'];   

    // Si le paramètre de pagination est défini et non vide, l'utiliser, sinon le définir à 1
    $paged = (isset($paged) || !(empty($paged))) ? $paged : 1;

    // Récupération des publications de type 'photos' depuis la base de données
    $posts = get_posts( array(
        'post_type'       => 'photos',    // Type de publication à récupérer
        'status'          => 'published', // Filtre pour récupérer uniquement les publications publiées
        'posts_per_page'  => 8,           // Limite le nombre de publications à 8 par page
        'orderby'         => 'post_date', // Trie les publications par date de publication
        'order'           => 'DESC',      // Trie les publications en ordre décroissant (du plus récent au plus ancien)
        'paged'           => $paged       // Utilisation du paramètre de pagination
    ));

    // Parcours de chaque publication récupérée
    foreach($posts as $post){
        // Récupération de l'identifiant de la publication
        $id = $post->ID;
        
        // Récupération de l'URL de l'image à la une de la publication, si elle existe
        $post_thumbnail = (has_post_thumbnail($id)) ? get_the_post_thumbnail_url($id) : null;

        $post_categories = wp_get_post_terms($id, 'categories');
        $categories = array();
        foreach ($post_categories as $category) {
            $categories[] = $category->name;
        }
        
        // Ajout des données de la publication à un objet et ajout de cet objet au tableau $posts_data
        $posts_data[] = (object)array(
            'id' => $id,
            'slug' => $post->post_name,
            'type' => $post->post_type,
            'title' => $post->post_title,
            'featured_img_src' => $post_thumbnail,
            'categories' => $categories
        );
    }

    // Retourne les données des publications au format JSON
    wp_send_json($posts_data);

    // Arrête l'exécution du script
    wp_die();
}

// Fonction pour la requête AJAX côté serveur.
add_action('wp_ajax_custom_api_get_photos', 'custom_api_get_photos_callback');
add_action('wp_ajax_nopriv_custom_api_get_photos', 'custom_api_get_photos_callback');


// _______________________________________________________________
// FONCTION POUR CHARGER UNE IMAGE ALEATOIRE DANS LE HERO :

function load_random_image_callback() {

    // Configuration des arguments pour la requête WP_Query.
    $args = array(
        'post_type' => 'photos',
        'posts_per_page' => 1,
        'orderby' => 'rand' // Trie les résultats de manière aléatoire
    );

    // Effectue la requête WP_Query avec les arguments définis
    $query = new WP_Query($args);

    // Vérifie si la requête a des résultats
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            echo '<img src="' . get_the_post_thumbnail_url() . '" alt="' . get_the_title() . '">';
        }
        // Réinitialise les données de la requête pour éviter les conflits
        wp_reset_postdata();
    }

    // Arrête le script PHP après avoir envoyé la réponse (l'image)
    wp_die();
}

// Appelle la function de la requette et indique à WP qu'elle est à utiliser via un appel Ajax.
add_action('wp_ajax_load_random_image', 'load_random_image_callback');
// Rend également la function accessible pour les utilisateurs non connectés.
add_action('wp_ajax_nopriv_load_random_image', 'load_random_image_callback');



// _______________________________________________________________
// FONCTION POUR CHARGER UNE IMAGE PAR CATEGORIE :

/* function load_category_image() {
    // Vérifie si la catégorie est définie dans la requête
    if (isset($_POST['category'])) {
        $category = sanitize_text_field($_POST['category']);
        
        // Récupère les images de la même catégorie
        $query_args = array(
            'post_type' => 'photos',
            'posts_per_page' => 2,
            'orderby' => 'rand',
            'tax_query' => array(
                array(
                    'taxonomy' => 'categories',
                    'field' => 'slug',
                    'terms' => $category,
                ),
            ),
        );
    } else {
        // Si aucune catégorie n'est spécifiée, récupère toutes les images
        $query_args = array(
            'post_type' => 'photos',
            'posts_per_page' => 8,
            'orderby' => 'rand',
        );
    }

    $query = new WP_Query($query_args);

    $like_also = array();

    // Vérifie si la requête a des résultats
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $like_also[] = array(
                'url' => get_the_post_thumbnail_url(),
                'title' => get_the_title(get_the_ID(), 'title', true),
                'category' => get_the_terms(get_the_ID(), 'categories')[0]->name,
            );

        }
        // Réinitialise les données de la requête pour éviter les conflits
        wp_reset_postdata();
    }

    // Retourne les images au format JSON
    wp_send_json(array('images' => $like_also));
}

add_action('wp_ajax_load_category_image', 'load_category_image');
add_action('wp_ajax_nopriv_load_category_image', 'load_category_image'); */



// _______________________________________________________________
// FONCTION POUR CHARGER LES IMAGES FILTREES :

function filter_photos() {

    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $format = isset($_POST['format']) ? $_POST['format'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';

    $args = array(
        'post_type' => 'photos',
        'posts_per_page' => 8,
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'categories',
                'field'    => 'id',
                'terms'    => $category,
            ),
            array(
                'taxonomy' => 'formats',
                'field'    => 'id',
                'terms'    => $format,
            ),
        ),
        'date_query' => array(
            array(
                'year' => $date,
            ),
        ),
    );

    $query = new WP_Query($args);

    // Boucle pour afficher les résultats
    
    wp_die();
}

add_action('wp_ajax_filter_photos', 'filter_photos');
add_action('wp_ajax_nopriv_filter_photos', 'filter_photos');



// _______________________________________________________________
// FONCTION POUR CHARGER LES IMAGES DANS LA LIGHTBOX :

function full_image_lightbox() {

    $args = array(
        'post_type' => 'photos',
        'posts_per_page' => -1, // Récupérer toutes les images
    );

    // Effectue la requête WP_Query avec les arguments définis
    $query = new WP_Query($args);

    $images = array();

    // Vérifie si la requête a des résultats
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Récupère l'URL de l'image, la référence et la catégorie.
            $images[] = array(
                'url' => get_the_post_thumbnail_url(),
                'reference' => get_post_meta(get_the_ID(), 'ref', true),
                'category' => get_the_terms(get_the_ID(), 'categories')[0]->name,
            );

        }
        // Réinitialise les données de la requête pour éviter les conflits
        wp_reset_postdata();
    }

    // Retourne les images au format JSON
    wp_send_json(array('images' => $images));

    wp_die();
}

add_action('wp_ajax_full_image_lightbox', 'full_image_lightbox');
add_action('wp_ajax_nopriv_full_image_lightbox', 'full_image_lightbox');


// _______________________________________________________________
// RECEPTION DU MESSAGE DE CONFIRMATION DU FORMULAIRE :

function traitement_formulaire_callback() {
    // Récupérer les données du formulaire
    $nom = $_POST['your-name'];
    $email = $_POST['your-email'];
    $ref_photo = $_POST['your-subject'];
    $message = $_POST['your-message'];

    // Connexion à la base de données WordPress
    global $wpdb;

    // Récupérer les adresses e-mail des utilisateurs
    $users_emails = $wpdb->get_col("SELECT user_email FROM $wpdb->users");

    // Sujet de l'e-mail
    $sujet = "Nouveau message depuis le formulaire de contact";

    // Corps de l'e-mail
    $contenu = "Nom: $nom\n";
    $contenu .= "E-mail: $email\n";
    $contenu .= "Ref. Photo: $ref_photo\n\n";
    $contenu .= "Message:\n$message";

    // En-têtes de l'e-mail
    $headers = "From: $nom <$email>";

    // Envoyer l'e-mail à chaque utilisateur
    foreach ($users_emails as $user_email) {
        mail($user_email, $sujet, $contenu, $headers);
    }

    // Rediriger l'utilisateur après le traitement du formulaire
    wp_redirect(home_url());
    exit;
}

// Ajouter un gestionnaire d'action pour le traitement du formulaire
add_action('admin_post_traitement_formulaire', 'traitement_formulaire_callback');
add_action('admin_post_nopriv_traitement_formulaire', 'traitement_formulaire_callback');