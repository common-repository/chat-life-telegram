<?php /** @var [] $list */ ?>
<div id="tcl">

    <table class="table table-border tcl-table-list" border="1" style="padding: 10px;border-collapse: collapse; ">
        <thead>
        <tr>
            <th>Session</th>
            <th>name</th>
            <th>last message</th>
            <th>Create_at</th>
            <th>Count</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($list as $chat): ?>
            <tr>
                <td><button class="open-chat" data-id="<?php echo  esc_attr($chat['session_id']); ?>">Open</button></td>
                <td><?php echo esc_attr($chat['name']); ?></td>
                <td><?php echo  esc_attr(($chat['user_id'])?:sprintf('#User_%s',hash('crc32', $chat['session_id']) )); ?></td>
                <td><?php echo esc_attr($chat['data']); ?></td>
                <td><?php echo  esc_attr($chat['create_at']); ?></td>
                <td><?php echo  esc_attr($chat['count']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>



<!-- The Modal -->
<div id="listchat" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>List chat</p>
        <div id="tcl-root-chat" >
            <div class="chatbox chatbox22 chatbox--tray draggable">
                <div class="chatbox__body">
                    <div class="tcl-chat-rezult"></div>
                </div>

            </div>
        </div>
    </div>

</div>


<div class="tcl-preloader" style="display: none">
    <img src="<?php echo plugin_dir_url(__FILE__)  ?>/assets/media/Spinner.svg" alt="">
</div>