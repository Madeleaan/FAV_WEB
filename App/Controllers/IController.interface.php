<?php

namespace App\Controllers;

/**
 * Interface pro controllery
 */
interface IController {
    /**
     * Zajisti vypsani stranky
     *
     * @param string $pageTitle Nazev stranky
     * @return array    HTML stranky
     */
    public function show(string $pageTitle): array;
}
