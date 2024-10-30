<?php
namespace pechenki\ChatLifeForTelegram\src;

class Model extends ChatCore {
    /**
     * @var \wpdb
     */
    public $db;
    /**
     * @var array
     */
    public $errors;
    /**
     * @var string
     */
    public $table;
    /**
     * @var
     */
    static $instance;


    public function __construct()
    {
        global $wpdb;

        parent::__construct();
        $this->table = $wpdb->get_blog_prefix() . 'tcl_chat';
        $this->db = $wpdb;


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
                    'REFERER' => $this->server('HTTP_REFERER'),
                    'USER_AGENT' => $this->server('HTTP_USER_AGENT'),

                ]),
                'message_id' => $data['message_id'],
                'chat_id' => $data['chat_id'],
                'reply_to_message_id' => ($data['reply_to_message_id'] ?? null),
                "session_id" => $data['session_id']?? $this->userId,
                "create_at" => time()
            ));
        $this->errors[] = $this->db->last_error;
        return  $result;
    }

    /**
     * @return Model
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