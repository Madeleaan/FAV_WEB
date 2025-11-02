<?php

namespace App\Models;

class SessionModel {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) session_start(['cookie_httponly' => true]);
    }

    /** Nastavi session na hodnotu
     * @param string $key Klic do pole
     * @param mixed $value  hodnota
     * @return void
     */
    public function setSession(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    /** Je session nastavena?
     * @param string $key   Klic do pole
     * @return bool
     */
    public function isSessionSet(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /** Vrati hodnotu dane session nebo null
     * @param string $key   Klid do pole
     * @return mixed    Hodnota nebo null
     */
    public function readSession(string $key): mixed {
        if ($this->isSessionSet($key)) {
            return $_SESSION[$key];
        } else {
            return null;
        }
    }

    /** Odstrani danou session
     * @param string $key   Klic do pole
     * @return void
     */
    public function removeSession(string $key): void {
        unset($_SESSION[$key]);
    }

    /** Vyprazdni cele pole $_SESSION
     * @return void
     */
    public function removeAllSessions(): void {
        session_unset();
    }
}