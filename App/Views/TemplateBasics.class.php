<?php

namespace App\Views;

/** Trida vypisujici zakladni rozlozeni stranky */
class TemplateBasics {
    const PAGE_INTRODUCTION = "IntroductionTemplate.tpl.php";

    /**
     * Vrati vrsek stranky po hlavni cast
     * @param string $pageTitle Nazev stranky
     * @return void
     */
    public function getHTMLHeader(string $pageTitle): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en" data-bs-theme="dark">
        <head>
            <title><?= $pageTitle ?></title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="/node_modules/@fortawesome/fontawesome-free/css/all.min.css">

            <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
            <script src="/node_modules/jquery/dist/jquery.min.js"></script>

            <style>
                body {
                    display: flex;
                    flex-direction: column;
                    min-height: 100vh;
                }

                main {
                    flex: 1;
                }
            </style>
        </head>
        <body>
            <nav>navigace</nav>
            <main>
                <h1>Template: <?= $pageTitle ?></h1>
        <?php
    }

    /**
     * Vrati paticku a ukonci html
     * @return void
     */
    public function getHTMLFooter(): void {
        ?>
            </main>
            <footer class="border-top bg-body-tertiary text-center p-2 mt-3">
                <span class="align-middle text-secondary-emphasis">&copy; 2025 tomeng</span>
            </footer>
        </body>
        </html>
        <?php
    }

    /**
     * Zajisti vypsani prislusne sablony
     * @param array $data   Data pro sablonu
     * @param string $key   Jmeno sablony
     */
    public function getOutput(array $data, string $key = self::PAGE_INTRODUCTION): string {
        // Zapnuti output bufferu
        ob_start();
        global $tplData;
        $tplData = $data;

        // Vykonani sablony
        require($key);

        // Ziskani vypisu z bufferu
        return ob_get_clean();
    }
}