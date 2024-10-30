<?php

namespace pechenki\ChatLifeForTelegram\src;
use pechenki\ChatLifeForTelegram\src\ChatCore;

/**
 *
 */
class ControllerForTelegram extends ChatCore
{
    /**
     * @var
     */
    static $instance;
    /**
     * @var \wpdb
     */
    public $db;
    /**
     * @var mixed|string
     */
    public $userId;
    /**
     * @var array
     */
    private $errors;
    /**
     * @var string
     */
    public $table;
    /**
     * @var
     */
    public $first_name;

    /**
     * @param $userId
     */
    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->get_blog_prefix() . 'tcl_chat';
        $this->db = $wpdb;
        $this->userId = $this->getUserId();

        add_action('init', function () {

            if (get_current_user_id()) {
                $user = wp_get_current_user();
                $this->userId = $user->ID;
                $this->first_name = $user->first_name;
            }
        });

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
     * @return mixed
     */
    public function insert($data)
    {

       $result = $this->db->insert($this->table,
            array(
                "data" => htmlspecialchars($data['text']),
                "name" => ($data['first_name'] ?: 'Anonim'),
                'position' => $data['position'],
                "user_id" => $data['user_id'],
                "meta_data" => serialize([
                    'REMOTE_ADDR' => $this->server('REMOTE_ADDR'),
//                    'REFERER' => $this->server('HTTP_REFERER'),
//                    'USER_AGENT' => $this->server('HTTP_USER_AGENT'),
                    'REMOTE_HOST' => $this->server('REMOTE_HOST')
                ]),
                'message_id' => $data['message_id'],
                'chat_id' => $data['chat_id'],
                'reply_to_message_id' => ($data['reply_to_message_id'] ?? null),
                "session_id" => $data['session_id']??  $this->userId,
                "create_at" => time()
            ));
        $this->errors[] = $this->db->last_error;
        return  $result;
    }


    /**
     * @return false
     */
    public function getMessage($json = false)
    {
        $result = $this->getMessagesBySessionId( $this->userId);


        if (count($result) > 0 && $json) {
            $messages = $result;
            if (!is_array($messages)) $messages = [];

            return array_map(function ($a) {
                return [
                    'avatar' => 'https://www.gstatic.com/webp/gallery/2.jpg',
                    'date' => date('Y-m-d', $a['create_at']),
                    'time' => date('H:i:s', $a['create_at']),
                    'timestamp' =>  $a['create_at'],
                    'position' => $a['position'],
                    'send' => true,
                    'message_id' => $a['message_id'],
                    'reply_to_message_id' => $a['reply_to_message_id'],
                    'person' => [
                        'name' => $a['name'],
                        'textMessage' => htmlspecialchars_decode($this->formatText($a['data']))
                    ]
                ];
            }, $messages);


        }

        if (count($result) > 0) return $result;

        return false;
    }

    /**
     * @return mixed|void
     */
    public function fetch()
    {


        if ($post = $this->Post()) {

            if ($post['method'] == 'update') {
                return $this->renderJson([
                    'status' => true,
                    'messages' => $this->getMessage(true)
                ]);
            }

            $telegram = $this->sendTelegram();
            if (!empty($telegram->message_id)) {

                $data = array(
                    'user_id' => get_current_user_id(),
                    'text' => $post['text'],
                    'message_id' => $telegram->message_id,
                    'first_name' => $this->first_name ?: 'Anonim',
                    'chat_id' => $telegram->chat->id,
                    'position' => 0,
                );

                if ($this->insert($data)) {
                    return $this->renderJson([
                        'status' => true,
                        'messages' => [
                            'message_id' => $data['message_id'],
                            'first_name' => $data['first_name'],
                            'text' => $data['text']
                        ]
                    ]);
                }else{

                }
            }

            return $this->renderJson([
                'status' => false,
                'errorMessage' => $this->errors
            ]);


            wp_die();


        }
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    private function sendTelegram()
    {
        $telegram = ControllerTelegramCore::get_instance();
        $post = $this->post();
        $user = $this->first_name ?: hash('crc32', $this->userId);
        $message = sprintf('#User_%s %s %s', $user, PHP_EOL, $post['text']);
        //
        $message = apply_filters('tcl_send_telegram_before_text', $message, $this);
        $telegram->isKeyboard = false;
        $result = json_decode($telegram->sendMessage($message));
        do_action("tcl_after_send_to_telegram", $result);
        if ($result->ok) {
            return $result->result;
        } else {
            throw new \Exception($result);
        }

    }



    /**
     * @param $text
     * @param int $user 0 - user 1 - admin
     * @return array
     */
    private function setText($text, $user = 0)
    {
        $post = $this->post();
        return [
            'text' => $text,
            'time' => time(),
            'user' => $user
        ];
    }

    /**
     * @param [] $data
     * @return void
     */
    public function renderJson($data = [])
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        wp_die();
    }

    /**
     * @return mixed|string
     */
    public function getUserId()
    {
        session_start();

        if ($this->session('tcl_user_id')) {
            $this->userId = $this->session('tcl_user_id');
            return $this->userId;
        }

        srand((double)microtime() * 1000000);
        $uniq_id = uniqid(rand());

        $_SESSION["tcl_user_id"] = $uniq_id;
        $this->userId = $uniq_id;
        return $uniq_id;

    }

    /**
     * @param $text
     * @return array|string|string[]|null
     */
    public function formatText($text)
    {
        return preg_replace('((http|https|ftp|ftps)://[^\s]+(\/.*.))', "<a href='$0' target='_blank'>$0</a>", $text);
    }
    /*
     *
     */
    public function listChats()
    {
        $result = $this->db->get_results(
            sprintf("SELECT *, COUNT(*) as count FROM  %s GROUP BY session_id  ASC", $this->table),
            ARRAY_A
        );
        return  $result;
    }

    /**
     * @param $session_id
     * @return array|object|null
     */
    public function getMessagesBySessionId($session_id){
       return  $this->db->get_results(
            sprintf("SELECT * FROM  %s WHERE session_id='%s' ORDER BY id ASC", $this->table, $session_id),
            ARRAY_A
        );
    }

}


