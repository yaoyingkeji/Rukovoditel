
<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo TEXT_NESTED_ENTITIES_MENU ?></h3>

<p><?php echo TEXT_NESTED_ENTITIES_MENU_INFO ?></p>

<?php echo button_tag(TEXT_ADD_NEW_MENU_ITEM, url_for('nested_entities_menu/form', 'entities_id=' . $_GET['entities_id']), true) . ' ' ?>
<?php echo button_tag(TEXT_SORT, url_for('nested_entities_menu/sort', 'entities_id=' . $_GET['entities_id']), true, ['class' => 'btn btn-default']) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>

                <th><?php echo TEXT_ACTION ?></th>    
                <th><?php echo TEXT_IS_ACTIVE ?></th>
                <th width="100%"><?php echo TEXT_NAME ?></th>           
                <th><?php echo TEXT_SORT_ORDER ?></th>    
            </tr>
        </thead>
        <tbody>
            <?php
            $menu_query = db_query("select * from app_nested_entities_menu where entities_id='" . _get::int('entities_id') . "' order by sort_order, name");

            if(db_num_rows($menu_query) == 0)
                echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';

            while($menu = db_fetch_array($menu_query)):
                ?>
                <tr>  
                    <td style="white-space: nowrap;">
                        <?php echo button_icon_delete(url_for('nested_entities_menu/delete', 'id=' . $menu['id'] . '&entities_id=' . $_GET['entities_id'])) . ' ' .
                        button_icon_edit(url_for('nested_entities_menu/form', 'id=' . $menu['id'] . '&entities_id=' . $_GET['entities_id']))
                        ?></td>
                    <td><?php echo render_bool_value($menu['is_active']) ?></td>
                    <td><?php 
                        echo app_render_icon($menu['icon'],(strlen($menu['icon_color']) ? 'style="color:' . $menu['icon_color'] . '"':'')) . ' ' . $menu['name'];
                        
                        foreach(explode(',',$menu['entities']) as $id)
                        {
                           echo '<br><small> - ' . entities::get_name_by_id($id) . '</small>';
                        }
                     ?></td>      
                    <td><?php echo $menu['sort_order'] ?></td>     
                </tr>  
            <?php endwhile ?>
        </tbody>
    </table>
</div>
