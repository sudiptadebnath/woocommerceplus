<div class="table-responsive">
    <table class="table table-hover" style="font-size: 14px">
        <thead>
            <tr>
                <th>Sl.</th>
                <th>Product</th>                
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if( $pagedata ){
                $i = 1;
                foreach( $pagedata as $recentItems ){
                    $product = wc_get_product( $recentItems['product_id'] );                   
            ?>
                    <tr>
                        <td><?php echo $i; ?>.</td>
                        <td>
                        <img src="<?php echo get_the_post_thumbnail_url( $product->get_id() ); ?>" style="height: 50px"><br>
                        <a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_title(); ?></a>
                        </td>
                        <td><?php echo date_i18n('d M, Y h:ia', strtotime($recentItems['viewed_at'])); ?></td>
                        
                    </tr>
            <?php
                    $i++;
                }
            }
            else{
                echo '<tr><td colspan=4 class="text-center">No recent viewed item</td></tr>';
            }
            ?>                        
        </tbody>
    </table>
</div>