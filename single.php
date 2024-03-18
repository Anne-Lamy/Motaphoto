<?php
/**
 * Le modèle pour afficher une publication unique.
 *
 * @package Motaphoto
 */

get_header();

/* Démarre la boucle. */
if (have_posts()) : while (have_posts()) : the_post();?>

<div class="page framed-layout">

    <article class="single">

        <div class="content-single">
            <div id="" class="single-left"> 
                <!-- Affichage du titre -->
                <h2><?php the_title(); ?></h2>
                <!-- Affichage de la référence -->
                <h3>Référence : <?php echo get_post_meta(get_the_ID(), 'ref', true); ?></h3>
                <!-- Affichage des catégories -->
                <h3>Catégories : 
                <?php
                    $categories = get_the_terms(get_the_ID(), 'categories');
                    if ($categories) {
                        foreach ($categories as $category) {
                            echo $category->name . ', ';
                        }
                    }
                ?>
                </h3>
                <!-- Affichage des formats -->
                <h3>Formats : 
                <?php
                    $formats = get_the_terms(get_the_ID(), 'formats');
                    if ($formats) {
                        foreach ($formats as $format) {
                            echo $format->name . ', ';
                        }
                    }
                ?>
                </h3>
                <!-- Affichage du type -->
                <h3>Type : <?php echo get_post_meta(get_the_ID(), 'type', true); ?></h3>
                <!-- Affichage de l'année -->
                <h3>Année : <?php echo get_the_date('Y'); ?></h3>
            </div>
            <div class="single-right">
                <!-- Affichage de la photo -->            
                <?php the_post_thumbnail('large', array('class' => 'single-thumbnail')); ?>
            </div>
        </div>
    
        <div class="content-interaction interest">
            <div class="single-left-bottom">
                <div class="half-content">
                    <p>Cette photo vous intéresse ?</p>
                </div>
                <div class="half-content">
                    <button id="btnContact">Contact</button>
                </div>

            </div>
            <div class="single-right-bottom none">
                <div class="mini-slide">
                <?php the_post_thumbnail(array(80, 70)); ?>
                    <div class="arrow">
                        <img src="<?= site_url() ?>/wp-content/themes/motaphoto/assets/images/left-arrow.png">
                        <img src="<?= site_url() ?>/wp-content/themes/motaphoto/assets/images/right-arrow.png">
                    </div>
                </div>
            </div>
        </div>

        <div class="content-presentation">
            <h3>VOUS AIMEREZ AUSSI</h3>
        </div>
        <div class="center-container">
            <div class="portfolio-container">
                <?php // Récupère les catégories de la publication actuelle
                $categories = get_the_terms(get_the_ID(), 'categories');
                if ($categories) { // Vérifie s'il y a des catégories associées à la publication
                    foreach ($categories as $category) { // Parcourt chaque catégorie
                        // Définit les arguments pour la requête WP_Query
                        $args = array(
                            'post_type' => 'photos', // Type de publication à rechercher.
                            'posts_per_page' => 2, // Nombre de photos à afficher par catégorie.
                            'tax_query' => array( // Définit une requête basée sur la taxonomie (catégorie ici)
                                array(
                                    'taxonomy' => 'categories', // Taxonomie à interroger.
                                    'field' => 'slug', // Champ à comparer (slug est une option ici).
                                    'terms' => $category->slug, // Terme de la taxonomie à rechercher (slug de la catégorie)
                                ),
                            ),
                        );
                        $query = new WP_Query($args);  // Effectue une requette auprés de la base de données.
                        // On vérifie si on obtient des résultats.
                        if ($query->have_posts()) {    // Si on récupère des résultats ...
                            while ($query->have_posts()) {
                                $query->the_post();    // On envois les résultats au script (sous forme de données JSON) ...
                                ?>
                <article class="portfolio-item">
                    
                    <div class="post-content">
                        <?php the_post_thumbnail(); ?>
                        <div id="full-screen">
                            <img class="screen-link" src="<?= site_url() ?>/wp-content/themes/motaphoto/assets/images/screen.png">
                        </div>
                        <a href="<?php the_permalink(); ?>">
                        <div id="info-single">
                            <h3><?php the_title(); ?></h3>
                                <h3><?php $categories = get_the_terms(get_the_ID(), 'categories');
                                        if ($categories) {
                                            foreach ($categories as $category) {
                                                echo $category->name;
                                            }
                                        }
                                    ?>
                            </h3>
                        </div>
                        </a>
                    </div>  
                    
                </article>
                                <?php
                            }
                        }
                        wp_reset_postdata(); // Réinitialiser les données de la requête.
                    }
                }
                ?>
            </div>
        </div>

    </article>

</div>    

<?php 
/* Termine la boucle */
endwhile; endif;

get_footer();

