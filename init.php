<?php

namespace pechenki\ChatLifeForTelegram;

use pechenki\ChatLifeForTelegram\src\AdminController;
use pechenki\ChatLifeForTelegram\src\ControllerTelegramCore;
use pechenki\ChatLifeForTelegram\src\Model;
use pechenki\ChatLifeForTelegram\src\ChatCore;
use pechenki\ChatLifeForTelegram\src\ControllerForTelegram;


/**
 *
 * @property false|mixed|null $enabled
 */
class init extends ChatCore
{

    /**
     * @var
     */
    static $instance;
    /**
     * @var string
     */
    public $version = '0.1.2';
    /**
     * @var ControllerForTelegram
     */
    public $chat;
    /**
     * @var icon string
     */
    private $icon = "data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3Csvg fill='white'  width='20' height='20' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 60 60' style='enable-background:new 0 0 60 60;' xml:space='preserve'%3E%3Cg id='Capa_1'%3E%3Cg%3E%3Cpath d='M54.1,3.5L20,3.4c-3.3,0-5.9,2.7-5.9,5.9v5.1l-8.1,0c-3.3,0-5.9,2.7-5.9,5.9v21.1c0,3.3,2.7,5.9,5.9,5.9H12v10 c0,0.4,0.3,0.8,0.6,0.9c0.1,0,0.2,0.1,0.4,0.1c0.3,0,0.5-0.1,0.7-0.3l9.7-10.7l16.6-0.1c3.3,0,5.9-2.7,5.9-5.9v-0.1l5.3,5.8 c0.2,0.2,0.5,0.3,0.7,0.3c0.1,0,0.2,0,0.4-0.1c0.4-0.1,0.6-0.5,0.6-0.9v-10h1.1c3.3,0,5.9-2.7,5.9-5.9V9.4 C60,6.1,57.4,3.5,54.1,3.5z M44,41.5c0,2.2-1.8,3.9-3.9,3.9L23,45.5c-0.3,0-0.5,0.1-0.7,0.3L14,54.9v-8.4c0-0.6-0.4-1-1-1H6 c-2.2,0-3.9-1.8-3.9-3.9V20.4c0-2.2,1.8-3.9,3.9-3.9l9.1,0l0,0c0,0,0,0,0,0l25.1-0.1c2.2,0,3.9,1.8,3.9,3.9v18.4V41.5z M58,30.5 c0,2.2-1.8,3.9-3.9,3.9H52c-0.6,0-1,0.4-1,1v8.4l-5-5.5V20.3c0-3.3-2.7-5.9-5.9-5.9l-24.1,0V9.3c0-2.2,1.8-3.9,3.9-3.9l34.1,0.1 c0,0,0,0,0,0c2.2,0,3.9,1.8,3.9,3.9L58,30.5L58,30.5z'/%3E%3Cpath d='M35.5,22.5l-3.8,18.4c-0.3,1.3-1,1.6-2.1,1l-5.8-4.4l-2.8,2.8c-0.3,0.3-0.6,0.6-1.2,0.6l0.4-6.1L31,24.9 c0.5-0.4-0.1-0.7-0.7-0.3l-13.2,8.6l-5.7-1.9c-1.2-0.4-1.2-1.2,0.3-1.9l22.2-8.9C34.9,20.1,35.9,20.9,35.5,22.5z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E%0A";
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        if (is_admin()) {
            add_action("admin_menu", array($this, "settingsTemplete"));
            $admin = new AdminController();
            add_action('wp_ajax_tcl_admin_action', array($admin, 'actions'));

        }
        $this->chat = ControllerForTelegram::get_instance();
        $telegram = ControllerTelegramCore::get_instance();
        if ($this->enabled) {


            add_action('wp_ajax_tcl_action', array($this->chat, 'fetch'));
            add_action('wp_ajax_nopriv_tcl_action', array($this->chat, 'fetch'));

            add_action('wp_ajax_tcl_chat_webhook', array($telegram, 'hookInput'));
            add_action('wp_ajax_nopriv_tcl_chat_webhook', array($telegram, 'hookInput'));
            $this->renderWidget();
        }
    }

    /**
     * @return init
     */
    public static function get_instance()
    {
        if (empty(self::$instance)) :
            self::$instance = new self;
        endif;

        return self::$instance;
    }

    /**
     *
     */
    public function settingsTemplete()
    {
        add_menu_page('Chat life for telegram', 'Chat-life for telegram', 'manage_options', 'Chat-life-for-telegram', array($this, 'renderSettings'),$this->icon);
        add_submenu_page('Chat-life-for-telegram', 'Chat List', 'Chat List', 'manage_options', 'tcl-chatList', array($this, 'renderChatList'));
    }

    /**
     * @return mixed
     */
    public function renderSettings()
    {



        wp_enqueue_style('tcl-css', TCL_URL_PLUGIN . '/admin/assets/css/css.css', '0.1');
        wp_enqueue_script('tcl-admin', TCL_URL_PLUGIN . 'admin/assets/js/admin-tcl.js');
        wp_localize_script('tcl-admin', 'Tcl',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
            )
        );


        return $this->render('/admin/settings', $this->settings);

    }

    /**
     * @return mixed
     */
    public function renderChatList()
    {

        wp_enqueue_style('tcl-css', TCL_URL_PLUGIN . 'admin/assets/css/css.css', '0.1');
        wp_enqueue_script('tcl-admin', TCL_URL_PLUGIN . 'admin/assets/js/admin-tcl.js');
        wp_localize_script('tcl-admin', 'Tcl',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
            )
        );
        $chat = ControllerTelegramCore::get_instance();
        return $this->render('/admin/chat-list', [
            'list' => $chat->model->listChats()
        ]);

    }


    /**
     * @return void
     */
    public function widget_chat_life()
    {

        $chatModel = Model::get_instance();

        wp_enqueue_style('tcl-css', TCL_URL_PLUGIN . 'frontend/css/chat.css', '', $this->version);


        if ($this->theme && isset($this->theme_list[$this->theme]['name']) && isset($this->theme_list[$this->theme]['url'])) {

            wp_enqueue_style($this->theme_list[$this->theme]['name'], $this->theme_list[$this->theme]['url'], '', $this->version);
        }

        wp_enqueue_script('tcl-vue', TCL_URL_PLUGIN . 'frontend/js/vue.global.js', '', $this->version);
        wp_enqueue_script('EmojiPicker', TCL_URL_PLUGIN . 'frontend/js/vanillaEmojiPicker.js', '', $this->version);
        wp_enqueue_script('tcl-core', TCL_URL_PLUGIN . 'frontend/js/tcl_script.js', '', $this->version);
        wp_localize_script('tcl-core', 'Tcl',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'tclUserId' => $this->userId,
                'tclUserName' => $this->first_name,
                'messageOne' => $this->messageOne,
            )
        );
        $this->render('/frontend/Widget', $this->settings);
    }

    /**
     * @return void
     */
    private function renderWidget()
    {

        add_action('wp_footer', array($this, 'widget_chat_life'));

    }


}
