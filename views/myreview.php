<?php
foreach($pagedata as $datarow){
    if ( has_post_thumbnail( $datarow->comment_post_ID ) ) {
        $productPhoto = get_the_post_thumbnail($datarow->comment_post_ID, 'woocommerce_thumbnail' ); 
    }else{ 
        $productPhoto = '<img src="' . woocommerce_placeholder_img_src() . '" alt="Placeholder" />';
    }

    $pUrl = get_the_permalink($datarow->comment_post_ID);
    $commentDate = date( 'd/m/Y', strtotime($datarow->comment_date));
?>
<div>
    <p><?php echo $datarow->comment_content; ?></p>
    <p>Poster: <?php echo $datarow->comment_author; ?></p>
    <p>Date: <?php echo $commentDate; ?></p>
    <p>Product Name: <a href="<?php echo $pUrl; ?>"><?php echo get_the_title($datarow->comment_post_ID); ?></a></p>
    <p><a href="<?php echo $pUrl; ?>"><?php  echo $productPhoto; ?></a></p>
</div>
<hr>
<?php    
}
?>