 <?php

 /** @var array $theme_list - list theme*/
 /** @var int $theme - value  theme */

 ?>


<div id="telsenderevent">
    <form action="options.php" method="post">
        <h2>TCL</h2>
        <?php echo wp_nonce_field('update-options'); ?>
        <fieldset>
            <legend>Telegram</legend>
            <label>
                <input value="0" type="hidden" name="tcl[enabled]" type="checkbox"/>
                <input value="1" <?php echo checked($enabled); ?> name="tcl[enabled]" type="checkbox"/>
                <span>Enabled</span></label>
            <div>
                <label>
                    <input type="text" value="<?php echo esc_attr($token); ?>" name="tcl[token]"   placeholder=""/><span>Token Telegram</span></label>
            </div>
            <div>
                <label><input type="text" value="<?php echo esc_attr($chat_id); ?>" name="tcl[chat_id]" placeholder=""/><span> Send Chat id</span></label>
            </div>
        </fieldset>

        <fieldset>
            <legend>General</legend>

            <div>
                <label>
                    <input type="text" value="<?php echo esc_attr($title); ?>" name="tcl[title]"
                              placeholder=""/><span>title </span>
                </label>
            </div>

            <div>
                <label>
                    <input type="text" value="<?php echo esc_attr($captionMessage); ?>" name="tcl[captionMessage]"
                              placeholder=""/><span>Сaption Message </span>
                </label>
            </div>
            <div>
                <label><textarea name="tcl[messageOne]" placeholder=""><?php echo esc_attr($messageOne); ?></textarea>
                    <span> Мessage one</span></label>
            </div>
        </fieldset>
        <fieldset>
            <legend>WebHook</legend>
            <input type="text" name="tcl[webHook]" value="<?php echo esc_attr($webHook); ?>">
            <div class="btn-group">
                <button type="button" id="tcl-wh-add">Set</button>
                <button type="button" id="tcl-wh-del">Delele</button>
            </div>
        </fieldset>

        <fieldset>
            <legend>Theme</legend>
            <div class="btn-group">
                <select name="tcl[theme]">
                    <?php
                    foreach ($theme_list as $itemkey => $item):?>
                        <option  <?php  selected( $itemkey, $theme );?> value="<?php echo esc_attr($itemkey); ?>"><?php echo esc_attr($item['name']); ?> </option>
                    <?php endforeach;?>

                </select>
            </div>
        </fieldset>
        <fieldset>
            <legend>Info</legend>
            <p>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telegram" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.287 5.906c-.778.324-2.334.994-4.666 2.01-.378.15-.577.298-.595.442-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294.26.006.549-.1.868-.32 2.179-1.471 3.304-2.214 3.374-2.23.05-.012.12-.026.166.016.047.041.042.12.037.141-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8.154 8.154 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629.093.06.183.125.27.187.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.426 1.426 0 0 0-.013-.315.337.337 0 0 0-.114-.217.526.526 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09z"/>
                </svg> <a href="https://t.me/Pechenki_Blog" target="_blank">Telegram support</a>
            </p>
            <p>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-diagram-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H11a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 7h2.5V6A1.5 1.5 0 0 1 6 4.5v-1zM8.5 5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1zM3 11.5A1.5 1.5 0 0 1 4.5 10h1A1.5 1.5 0 0 1 7 11.5v1A1.5 1.5 0 0 1 5.5 14h-1A1.5 1.5 0 0 1 3 12.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm4.5.5a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 9 12.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z"/>
                </svg> <a href="https://pechenki.top/blog/plugin-and-modules/tcl-telegram-chat-life" target="_blank">Site plugin</a>
            </p>
        </fieldset>

        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="page_options" value="tcl"/>


        <button class="button-primary" type="submit">Save</button>
    </form>


    <div class="tcl-preloader" style="display: none">
        <img src="<?php echo plugin_dir_url(__FILE__)  ?>/assets/media/Spinner.svg" alt="">
    </div>
</div>
