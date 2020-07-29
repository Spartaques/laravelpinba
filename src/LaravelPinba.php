<?php


namespace spartaques\LaravelPinba;


class LaravelPinba
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * Установка времени для таймера pinba-board
     * @param $category
     * @param $group
     * @param $time
     * @param bool $server
     */
    public function setTimer($category, $group, $time, $server = false)
    {

        if (extension_loaded('pinba')) {

            $server_name = pathinfo(config('app.url'));
            if(!empty($server_name['basename'])) {
                pinba_server_name_set($server_name['basename']);
            }

            $pinbaData = pinba_get_info();

            $initTags = [];

            if ($server) {
                $initTags['server'] = $server;
            }
            $initTags['category'] = $category;
            $initTags['group'] = $category . '::' . $group;
            if (isset($pinbaData['hostname'])) {
                $initTags['__hostname'] = $pinbaData['hostname'];
            }
            if (isset($pinbaData['server_name'])) {
                $initTags['__server_name'] = $pinbaData['server_name'];
            }

            pinba_timer_add($initTags, $time);
        }

    }
}
