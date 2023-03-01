<div class="table-responsive">
    <table class="table table-hover" style="font-size: 14px">
        <thead>
            <tr>
                <th>Sl.</th>
                <th>Keyword</th>
                <th>Last Searched</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if( $pagedata ){
                $i = 1;
                foreach( $pagedata as $history ){                    
            ?>
                    <tr>
                        <td><?php echo $i; ?>.</td>
                        <td><?php echo $history['keyword']; ?></td>
                        <td><?php echo date_i18n('d M, Y h:ia', strtotime($history['searched_at'])); ?></td>
                        <td><a href="<?php echo site_url().'?s='.esc_attr( $history['keyword'] ); ?>&post_type=product" target="_blank" class="btn">Search again</a></td>
                    </tr>
            <?php
                    $i++;
                }
            }
            else{
                echo '<tr><td colspan=4 class="text-center">No search history</td></tr>';
            }
            ?>                        
        </tbody>
    </table>
</div>