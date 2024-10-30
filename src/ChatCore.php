<?php

namespace pechenki\ChatLifeForTelegram\src;


/**
 * @property mixed|string $userId
 */
class ChatCore
{


    /**
     * @var array
     */
    public $settings = [];

    /**
     *
     */
    public function __construct()
    {
        add_filter('sanitize_option_tcl', array($this, 'filter_function_save_option'), 10, 3);
        add_filter('tcl_theme_list', function ($data) {
            $data[] = [
                'name' => 'Ios',
                'url' => TCL_URL_PLUGIN . 'frontend/css/ios-theme.css',
            ];
            $data[] = [
                'name' => 'White',
                'url' => TCL_URL_PLUGIN . 'frontend/css/white-theme.css',
            ];
            return $data;
        });
        $this->loadSettings();
    }

    /**
     * @return void
     */
    public function loadSettings()
    {
        $a = [
            'messageOne' => '',
            'title' => '',
            'token' => '',
            'enabled' => 0,
            'chat_id' => '',
            'captionMessage' => '',
            'webHook' => '',
            'theme' => '1',
        ];
        $option = get_option('tcl');
        if (is_array($option)) {
            $this->settings = array_merge($a, $option);
        } else {
            $this->settings = array_merge($a, []);
        }
        $this->listTheme();

    }

    /**
     * @return void
     */
    public function listTheme()
    {
        $option = [
            1 => [
                'name' => 'default',
                'url' => '/default.css',
            ]
        ];

        $option = apply_filters('tcl_theme_list', $option, $this);
        $this->settings['theme_list'] = $option;

    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->settings) && !empty($this->settings[$name]) && $this->settings[$name]) {

            return $this->settings[$name];
        }

        return false;

    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {

        $this->settings[$name] = $value;
    }

    /**
     * @param $path
     * @param array $vars
     * @return mixed
     */
    protected function render($path, $vars = [])
    {
        $pathBase = TCL_DIR;
        extract($vars);
        return require $pathBase . $path . '.php';

    }

    /**
     * filter save data in admin
     * @param $value
     * @return mixed
     */
    public function filter_function_save_option($value)
    {

        foreach (array_keys($this->settings) as $key) {
            if (isset($value[$key])) {
                $value[$key] = sanitize_text_field($value[$key]);
            }
        }
        return $value;

    }

    /**
     * @return array|false
     */
    protected function post($param = null)
    {
        if (isset( $_POST)) {
            $post =  $_POST;
            if (isset($post[$param])) {
                return filter_input(INPUT_POST,$param);
            }
            if ($param) {
                return false;
            }
            return array_combine(array_keys($post), array_map(function ($s){
                return filter_input(INPUT_POST,$s);
            },array_keys($post)));
        }
        return false;


    }

    /**
     * @param $param
     * @return array|false|mixed
     */
    protected function server($param = null){
        if (isset($_SERVER)) {
            $server = $_SERVER;
            if (isset($server[$param])) {
                return filter_input(INPUT_SERVER,$param);
            }
            if ($param) {
                return false;
            }

            return array_combine(array_keys($server), array_map(function ($s){
                return filter_input(INPUT_SERVER,$s);
            },array_keys($server)));
        }
        return false;
    }

    /**
     * @param $param
     * @return array|false|mixed
     */
    protected function session($param = null){
        if (isset($_SESSION)) {
            $session = $_SESSION;
            if (isset($session[$param])) {
                return filter_var($session[$param],FILEINFO_RAW);
            }
            if ($param) {
                return false;
            }

        }
        return false;
    }

}