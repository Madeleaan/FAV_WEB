<?php

namespace App;

require_once 'settings.inc.php';
use App\Controllers\IController;
use App\Views\TemplateBasics;

class ApplicationStart {
    public function appStart(): void {
        $pageKey = DEFAULT_WEBPAGE;
        if (!empty($_GET['page']) && array_key_exists($_GET['page'], WEB_PAGES)) {
            $pageKey = $_GET['page'];
        }

        $pageInfo = WEB_PAGES[$pageKey];

        /** @var IController $controller */
        $controller = new $pageInfo['controller_class_name']();
        $data = $controller->show($pageInfo['title']);

        $template = new TemplateBasics();
        echo $template->getOutput($data, $pageInfo['template_type']);
    }
}
