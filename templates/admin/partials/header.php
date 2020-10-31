<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

?>

<div class="header">
    <div class="user-info">
        <div class="user-detail">
            <h4><?php echo userFullName(); ?></h4>
            <p><?php echo aMLMCurrentUserRole(); ?></p>
        </div>
        <div class="user-avater">
            <?php printf('<img src="%s">', get_avatar_url(get_current_user_id()));?>
        </div>
    </div>
</div>