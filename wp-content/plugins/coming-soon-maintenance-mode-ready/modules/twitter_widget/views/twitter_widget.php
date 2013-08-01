<?php
class twitter_widgetViewCsp extends viewCsp {
    public function display($instance) {
        $this->assign('uniqBoxId', 'toeTwitterWidget'. mt_rand(1, 9999));
        $this->assign('instance', $instance);
        parent::display('twitter');
    }
    public function displayForm($data, $widget) {
		$this->displayWidgetForm($data, $widget, 'twitterForm');
    }
}

