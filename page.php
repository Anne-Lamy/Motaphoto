<?php
/**
* Le modèle pour afficher une page unique.
 *
 * @package Motaphoto
 */

get_header();
?>

<main id="main" class="site-main" role="main">

<?php if (have_posts()) : while (have_posts()) : the_post();?>

<div class="page">
    <div class="content-page">
        <!-- Affichage du contenu -->
        <?php the_content(); ?>
    </div>
</div>

</main>

<?php
/* Termine la boucle */
endwhile; endif;

get_footer() ?>