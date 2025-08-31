<?php
$id = $args['id'] ?? get_the_ID();
?>
<article class="ielts-card">
  <h3><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a></h3>
  <?php if ( has_excerpt( $id ) ) : ?>
    <p><?php echo esc_html( get_the_excerpt( $id ) ); ?></p>
  <?php endif; ?>
</article>
