<?php

namespace App\Views;

use App\Models\AuthModel;
use buzzingpixel\twigswitch\SwitchTwigExtension;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once("vendor/autoload.php");

/** Trida vypisujici zakladni rozlozeni stranky */
class TemplateBasics {
    private AuthModel $authModel;

    const NAVIGATION = "NavigationTemplate.twig";
    const PAGE_INTRODUCTION = "IntroductionTemplate.twig";
    const PAGE_PUBLIC_ARTICLES = "PublicArticlesTemplate.twig";
    const PAGE_AUTHOR_ARTICLES = "AuthorArticlesTemplate.twig";
    const PAGE_LOGIN = "LoginTemplate.twig";
    const PAGE_EDITOR_REVIEWS = "EditorReviewsTemplate.twig";
    const PAGE_ADMIN_USERS = "AdminUsersTemplate.twig";
    const PAGE_ADMIN_ARTICLES = "AdminArticlesTemplate.twig";

    public function __construct() {
        $this->authModel = new AuthModel();
    }

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
            <link rel="stylesheet" href="/node_modules/quill/dist/quill.bubble.css">
            <link rel="stylesheet" href="/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css">

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
        <?php
    }

    /**
     * Vrati paticku a ukonci html
     * @return void
     */
    public function getHTMLFooter(): void {
        ?>
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

        // Vykonani sablony
        $loader = new FilesystemLoader('App/Views');
        $twig = new Environment($loader, ['debug' => true, 'strict_variables' => true]);
        $twig->addExtension(new SwitchTwigExtension());

        try {
            $this->getHTMLHeader($data['title']);

            echo $twig->render(self::NAVIGATION, [
                'uri' => $_SERVER['REQUEST_URI'],
                'logged' => $this->authModel->getLoggedUser()
            ]);

            echo '<main class="mx-2 mx-md-4">';
            echo '<h2 class="text-center">'. $data['title'] .'</h2>';
            echo $twig->render($key, $data);
            echo '</main>';

            $this->getHTMLFooter();
        } catch (Exception $e) {
            echo "Error while loading template: " . $e->getMessage();
            die();
        }

        // Ziskani vypisu z bufferu
        return ob_get_clean();
    }
}