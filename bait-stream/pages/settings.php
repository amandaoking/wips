<?php
if (!defined('ABSPATH')) {
    die(__('No'));
}
?>

<div id="wrap">
    <h1><?php _e('Bait Stream Settings', 'baitStream'); ?></h1>

    <form method="post" action="options.php">

        <?php settings_fields($this->pluginPrefix . '-settings-group'); ?>
        <?php do_settings_sections($this->pluginPrefix . '-settings-group'); ?>


        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><label
                        for="<?php echo $this->pluginPrefix; ?>-channelname"><?php _e('Twitch Channel', 'baitStream'); ?></label></th>
                <td><input name="<?php echo $this->pluginPrefix; ?>-channelname" type="text" id="<?php echo $this->pluginPrefix; ?>-channelname" value="<?php echo get_option($this->pluginPrefix .  '-channelname',$this->channelname) ?>" class="regular-text">

                    <p class="description"><?php _e('The Twitch.TV channel/user name. (Example: a URL of www.twitch.tv/pcgamergirlgk - pcgamergirlgk would be the entry for this field.)', 'baitStream'); ?></p>
                    <p><b>NOTE:</b> This plugin will <i>only</i> display an alert on the <b>front page</b> of your site when your Twitch stream is active. If your stream is <b>not</b> active, no message will be displayed.</p><p>&nbsp;</p>
                    <p><b>Plugin Created By:</b> <a href="http://www.pcgamergirl.com" target="_BLANK">PCGamerGirl.COM</a> | <a href="http://www.pcgamergirl.com/wordpress-plugin-donations/" target="_BLANK">Donate</a></p>
                </td>
            </tr>

            </tbody>
        </table>

        <?php submit_button(); ?>

    </form>
</div>