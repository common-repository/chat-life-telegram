<?php
namespace pechenki\ChatLifeForTelegram\src;

use pechenki\ChatLifeForTelegram\src\ChatCore;
use pechenki\ChatLifeForTelegram\src\Model;

/**
 *
 */
class AdminController extends ChatCore {
    /**
     * @var \pechenki\ChatLifeForTelegram\src\Model
     */
    public  $model;
    /**
     *
     */
    public function __construct()
    {
        $this->model = Model::get_instance();
    }

    /**
     * @return ControllerForTelegram
     */
    public static function get_instance()
    {
        if (empty(self::$instance)) :
            self::$instance = new self;
        endif;

        return self::$instance;
    }
    /**
     * @return void
     */

    public function actions(){
        $out = [];
        if ($this->post() && $this->post('func')){
            switch ($this->post('func')){
                case 'setWebHook':
                    $telegram = ControllerTelegramCore::$instance;
                   $out = $telegram->setWebHook();
                    break;
                case 'delWebHook':
                    $telegram = ControllerTelegramCore::$instance;
                    $out = $telegram->delWebHook();
                    break;
                case 'listChat':
                    wp_send_json($this->model->getMessagesBySessionId($this->post('id')));

                    break;

            }
        }
        wp_send_json(json_decode($out));
    }


}