<?php
require_once(plugin_dir_path(__FILE__) . 'iStbWiget.php');

class stb_advertisement implements iStbWiget {

    private $widget;

    function __construct($widget) {
        $this->widget = $widget;
    }

    public function render() {
        $url = $this->widget->data->url;
        $target = $this->widget->data->target;
        $imageUrl = $this->widget->data->imageUrl;
        return "
        <a class=\"addvertise\" href=\"$url\" target=\"$target\" class=\"stb-advertise\">
            <img src=\"$imageUrl\" alt=\"\" />
        </a>
        ";
    }
}
?>
