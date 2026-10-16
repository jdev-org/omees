<div class="row">
<div class="col-md-8 offset-md-2">
                    <?php if (has_post_thumbnail()): ?>

                        <?php if (has_post_thumbnail()): ?>

                        <?php
                        // Récupérer l'ID de l'image mise en avant
                        $image_id = get_post_thumbnail_id();

                        // Récupérer sa légende
                        $legende = wp_get_attachment_caption($image_id);
                        ?>

                        <figure class="single__thumbnail mb-4">

                            <?php the_post_thumbnail("large", [
                                "class" => "img-fluid media__rounded",
                                "loading" => "eager",
                            ]); ?>

                            <?php if ($legende): ?>
                                <figcaption class="my-2">
                                    <?php echo esc_html($legende); ?>
                                </figcaption>
                            <?php endif; ?>

                        </figure>

                        <?php endif; ?>

                    <?php endif; ?>
                    
            
                    <?php the_content(); ?>

                    <?php
                    get_template_part('template-parts/components/keywords');
                    ?>
                </div>                
                            </div>