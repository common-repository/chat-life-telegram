<?php

namespace pechenki\ChatLifeForTelegram\src;
use pechenki\ChatLifeForTelegram\src\ControllerForTelegram;

/**
 * @property false|mixed|null $chat_id
 * @property string $token
 */
class ControllerTelegramCore extends ChatCore
{
    /**
     * @var
     */
    static $instance;
    /**
     * @var ControllerForTelegram
     */
    public $chatController;

    /**
     * @var bool
     */
    public $isKeyboard = false;
    /**
     * @var bool
     */
    public $force_reply = false;
    /**
     * @var string
     */
    /**
     * @var Model
     */
    public  $model;
    /**
     * @var string
     */
    private $url = 'https://api.telegram.org/bot%s/';

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = sprintf($this->url, $this->token);
        $this->chatController = ControllerForTelegram::get_instance();
        $this->model = Model::get_instance();

    }

    /**
     * @return ControllerTelegramCore
     */
    public static function get_instance()
    {
        if (empty(self::$instance)) :
            self::$instance = new self;
        endif;

        return self::$instance;
    }

    /**
     * @param $text
     * @return mixed
     * @throws \Exception
     */
    public function sendMessage($text)
    {
        $content = [
            'chat_id' => $this->chat_id,
            'text' => $text
        ];
        if ($this->isKeyboard){
            $content['reply_markup'] = $this->getInlineKeyboard();
        }
        if ($this->force_reply){
            $content['reply_markup'] = $this->force_reply();
        }

        do_action( "tcl_before_send_to_telegram",$content, $this);
        return $this->requst('sendMessage', $content);
    }

    /**
     * @param $text
     * @param $message_id
     * @return mixed
     * @throws \Exception
     */
    public function editMessageText($text,$message_id){

        $content = [
            'chat_id' => $this->chat_id,
            'text' => $text,
            'message_id'=>$message_id
        ];

        if ($this->isKeyboard){
            $content['reply_markup'] = $this->getInlineKeyboard();
        }
        if ($this->force_reply){
            $content['reply_markup'] = $this->force_reply();
        }

        do_action( "tcl_before_edit_to_telegram",$content, $this);
        return $this->requst('editMessageText', $content);
    }

    /**
     * Set telegram webHook
     * @return mixed
     * @throws \Exception
     */
    public function setWebHook()
    {
        $urlHook = sprintf('%s?action=tcl_chat_webhook',admin_url("admin-ajax.php"));

        return $this->requst('setwebhook', [
            'url' => $urlHook
        ]);
    }

    /**
     *  Delete telegram webHook
     * @return mixed
     * @throws \Exception
     */
    public function delWebHook()
    {
        return $this->requst('setwebhook', [
            'url' => '',
            'action' => 'tcl_chat_webhook'
        ]);
    }

    /**
     * @return void
     */
    public function sendFile()
    {

    }

    /**
     * @return void
     */
    public function sendPhoto()
    {

    }
    public function callback_query($data){
        $this->isKeyboard = false;
        $this->force_reply = false;
        $this->editMessageText($data->data,$data->message->message_id);
    }

    /**
     * @return void
     */
    public function hookInput()
    {

        $content = file_get_contents('php://input');
        $update = json_decode($content);
        $update = apply_filters( 'tcl_webhook_input', $update, $this);
        $this->log($update);
        if (isset($update->callback_query))  return $this->callback_query($update->callback_query);

        if (!empty($update->message->message_id)) {
            $dbmessage = $this->model->db->get_row( "SELECT * FROM ".$this->model->table." WHERE message_id  = ".$update->message->reply_to_message->message_id );
            if ($dbmessage->session_id){
                $this->model->insert(
                    array(
                        'text' => $update->message->text,
                        'message_id' => $update->message->message_id,
                        'first_name' => $update->message->from->first_name,
                        'chat_id' =>  $update->message->chat->id,
                        'user_id' => $dbmessage->user_id,
                        'reply_to_message_id' => $update->message->reply_to_message->message_id,
                        'position' => 1,
                        'session_id' => $dbmessage->session_id
                    ));
            }
        }

    }


    /**
     * @param $method
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    private function requst($method, $data)
    {
        $url = $this->url . $method;
        $response = wp_remote_post($url, array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => $data,
            'cookies' => array()
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            throw new \Exception($error_message);
        } else {
            return $response['body'];
        }
    }

    /**
     * @return false|string
     */
    public function getInlineKeyboard(){
        $keyboard = array(
            "inline_keyboard" => array(
                array(
//                    array(
//                        "text" => 'Ответить',
//                        "callback_data" => 'reply'
//                    ),
                    array(
                        "text" => '🗑',
                        "callback_data" => 'delete'
                    )
                )
            ),
        );
        return  json_encode( apply_filters( 'tcl_reply_markup', $keyboard, $this) );


    }

    /**
     * @return false|string
     */
    public function force_reply(){
        $keyboard = [
            'force_reply' => true,
            'selective' => true
        ];
        return  json_encode( apply_filters( 'tcl_force_reply_markup', $keyboard, $this) );
    }

    /**
     * @param $log
     * @return void
     */
    private function log($log) {
        $myFile = 'log.json';
        $fh = fopen($myFile, 'a') or die('can\'t open file');
        if ((is_array($log)) || (is_object($log))) {
            $updateArray = print_r($log, TRUE);
            fwrite($fh, $updateArray."\n");
        } else {
            fwrite($fh, $log . "\n");
        }
        fclose($fh);
    }


}