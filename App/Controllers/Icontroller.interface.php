<?php

namespace App\Controllers;

/**
 * Interface pro controllery
 */
interface Icontroller {
    /**
     * Zajisti vypsani stranky
     *
     * @param string $pageTitle Nazev stranky
     * @return array    HTML stranky
     */
    public function show(string $pageTitle): array;
}
