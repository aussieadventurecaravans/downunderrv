<?php
/*
Template Name: All Caravans
*/

get_header(); ?>

<?php $kokoda_dreamseeker_cat = array(); ?>
<div id="primary" class="fullwidth">
    <div class="container">
        <div class="row">
            <div class="col-md-2" style=" margin-bottom: 46px;">
                <h2 class="filter-title">Refine By: </h2>
                <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter">
                    <?php


                        echo ' <div class="form-check">';
                            if( $terms = get_terms( 'locations', 'orderby=name' ) ) : // to make it simple I use default categories
                            echo '<h3 class="filter-heading">Location</h3>';
                                foreach ( $terms as $term ) :
                                    echo  '<p class="location-filter"><input type="checkbox" class="form-check-input" name="locationfilter[]" value="'.$term->term_id.'" id ="'.$term->name.'" >'
                                            .'<label class="form-check-label" for="'.$term->name.'">'. $term->name .'</label>'
                                            .'</p>';
                                endforeach;
                            endif;
                            if( $terms = get_terms( 'category', 'orderby=name' ) ) : // to make it simple I use default categories
                                echo '<h3 class="filter-heading">Types</h3>';
                                foreach ( $terms as $term ) :
                                    if(in_array( $term->name ,array('New Caravans','Used Caravans'))):
                                        echo  '<p class="location-filter"><input type="checkbox" class="form-check-input" name="typefilter[]" value="'.$term->term_id.'" id ="'.$term->name.'" >'
                                            .'<label class="form-check-label" for="'.$term->name.'">'. $term->name .'</label>'
                                            .'</p>';
                                    endif;
                                endforeach;
                                echo '<h3 class="filter-heading">Brands</h3>';
                                foreach ( $terms as $term ) :
                                    if(in_array( $term->name ,array('Kokoda','Dreamseeker'))):
                                        $kokoda_dreamseeker_cat[] =$term->term_id;
                                        echo  '<p class="location-filter"><input type="checkbox" class="form-check-input" name="brandfilter[]" value="'.$term->term_id.'" id ="'.$term->name.'" >'
                                            .'<label class="form-check-label" for="'.$term->name.'">'. $term->name .'</label>'
                                            .'</p>';
                                    endif;
                                endforeach;
                            endif;
                        echo '</div>';
                    ?>
                    <button class="filter-button">Filter</button>
                    <input type="hidden" name="action" value="myfilter">
                </form>
            </div>
            <div class="col-md-10">
                <div id="caravans-category">
                    <?php

                        //load all uncategorized caravans query
                        $args = array(
                            'post_type' => 'post',
                            'orderby' => 'modified',
                            'order' => 'DESC',
                            'nopaging' => true,
                            'post_status'  => 'publish',
                            'tax_query' => array(
                                array
                                (
                                    'taxonomy' => 'category',
                                    'field' => 'id',
                                    'terms' => $kokoda_dreamseeker_cat,
                                    'operator' => 'NOT IN'
                                )
                            )
                        );
                        $brand_args=array(
                            'post_type' => 'post',
                            'orderby' => 'modified',
                            'order' => 'DESC',
                            'nopaging' => true,
                            'post_status'  => 'publish',
                            'tax_query'=> array(
                                array(
                                    'taxonomy' => 'category',
                                    'field' => 'id',
                                    'terms' => $kokoda_dreamseeker_cat,
                                    'operator' => 'IN'
                                )
                            )
                        );

                        $dreamseekers_kokoda_caravans = get_posts( $brand_args );
                        $not_dreamseeker_kokoda_caravans =  get_posts( $args );

                        $caravans = array_merge($dreamseekers_kokoda_caravans,$not_dreamseeker_kokoda_caravans)

                    ?>

                    <?php //query_posts($args);
                            $count = 0;
                            ?>
                    <?php foreach ($caravans as $caravan):  ?>
                            <?php //Starting Element Row ?>
                            <?php if($count ==  0): ?>
                               <div class="row">
                            <?php endif; ?>

                            <?php
                            $post_price = get_field( "post_price" ,$caravan->ID);
                            if(!empty($post_price)): ?>

                                <?php if($count <  3): ?>
                                    <?php $product_img = get_the_post_thumbnail_url($caravan,'west-large-thumb'); ?>
                                    <div class="product-list col-sm-4">
                                        <div class="item-img">
                                            <?php if($product_img): ?>
                                                <a href="<?php the_permalink($caravan); ?>" >
                                                   <img src="<?php echo $product_img ?>" class="product-img"/>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-details">
                                            <div class="details">
                                                <a href="<?php the_permalink($caravan); ?>" >
                                                    <h4 class="item-title"><?php echo get_the_title($caravan); ?></h4>
                                                    <?php
                                                    $post_price = get_field( "post_price",$caravan->ID );
                                                    $orc_field = get_field( "orc_field",$caravan->ID );
                                                    ?>
                                                    <h3 class="price"><?php if(!empty($post_price)) { echo '$'. $post_price; }
                                                        if(!empty($orc_field))
                                                        {
                                                            echo ' <span class="orc-field">  '.$orc_field . '</span>';
                                                        }
                                                        else
                                                        {
                                                            echo '<span class="orc-field"> Drive Away </span>';
                                                        }
                                                        ?>
                                                    </h3>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php  $count++; $open_element = true ;?>
                                <?php endif; ?>

                                <?php //close element Row ?>
                                <?php if($count ==  3): ?>
                                       </div>
                                    <?php  $count= 0; $open_element = false; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                    <?php endforeach; ?>


                    <?php //close element Row at last product ?>
                    <?php if($open_element ==  true): ?>
                        </div>
                    <?php endif; ?>
                    <?php //wp_reset_query(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#filter').submit(function(){
            var filter = $('#filter');
            $.ajax({
                url:filter.attr('action'),
                data:filter.serialize(), // form data
                type:filter.attr('method'), // POST
                beforeSend:function(xhr){
                    filter.find('button').text('Processing...'); // changing the button label
                },
                success:function(data){
                    filter.find('button').text('Filter'); // changing the button label back
                    $('#caravans-category').html(data); // insert data
                }
            });
            return false;
        });

    });

</script>


<?php get_footer(); ?>
