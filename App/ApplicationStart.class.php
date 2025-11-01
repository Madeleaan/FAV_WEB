<?php

namespace App;

use App\Controllers\Icontroller;
use App\Views\TemplateBasics;

class ApplicationStart {
    public function appStart(): void {
        $pageKey = DEFAULT_WEBPAGE;
        if (!empty($_GET['page']) && array_key_exists($_GET['page'], WEB_PAGES)) {
            $pageKey = $_GET['page'];
        }

        $pageInfo = WEB_PAGES[$pageKey];

        /** @var Icontroller $controller */
        $controller = new $pageInfo['controller_class_name']();
        $data = $controller->show($pageInfo['title']);

        /** @var TemplateBasics $template */
        $template = new $pageInfo['view_class_name']();
        echo $template->getOutput($data, $pageInfo['template_type']);
    }
}
