<?php
namespace App\Models;
use App\Core\Model;
use PDO;
define('COOKIE_DUREE', 7 * 24 * 3600);
define('COOKIE_NOM', 'remember_token');
class AccessModel extends Model
{

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => false,      // ← passer à true en HTTPS (production)
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    public function connect(string $email, string $motDePasse, bool $sesouvenir = false): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT ID_user, Email, Password, ID_role
             FROM User_
             WHERE Email = :email'
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        // --- FIX : password_verify() au lieu d'une comparaison directe -------
        if (!$user || !password_verify($motDePasse, $user['Password'])) {
            // Message volontairement vague pour ne pas révéler si l'email existe
            return ['succes' => false, 'message' => 'Email ou mot de passe incorrect.'];
        }

        $this->startSession();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['ID_user'];
        $_SESSION['user_pseudo'] = $user['Email'];
        $_SESSION['user_admin'] = $user['ID_role'];
        $_SESSION['connecte_le'] = time();

        if ($sesouvenir) {
            $this->createToken((int) $user['ID_user']);
        }

        return [
            'succes' => true,
            'message' => 'Connexion réussie.',
            'user' => [
                'id' => $user['ID_user'],
                'pseudo' => $user['Email'],
                'admin' => (bool) $user['ID_role'],
            ],
        ];
    }

    public function deconnect(): void
    {
        $this->startSession();

        // Invalider le token "remember me" en base avant de supprimer le cookie
        if (!empty($_COOKIE[COOKIE_NOM])) {
            $this->deleteToken($_COOKIE[COOKIE_NOM]);
        }

        $_SESSION = [];

        // --- FIX : syntaxe tableau cohérente pour setcookie() -----------------
        if (ini_get('session.use_cookies')) {
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        setcookie(COOKIE_NOM, '', [
            'expires' => time() - 42000,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_destroy();
    }

    public function isConnect(): bool
    {
        $this->startSession();

        if (!empty($_SESSION['user_id'])) {
            return true;
        }

        if (!empty($_COOKIE[COOKIE_NOM])) {
            $tokenBrut = $_COOKIE[COOKIE_NOM];
            $tokenHash = hash('sha256', $tokenBrut);

            $stmt = $this->pdo->prepare(
                'SELECT u.ID_user, u.Email, u.ID_role
                 FROM Remember_tokens rt
                 JOIN User_ u ON u.ID_user = rt.ID_user
                 WHERE rt.token = :hash
                   AND rt.expires > NOW()'
            );
            $stmt->bindValue(':hash', $tokenHash);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user) {
                // Rotation du token à chaque utilisation (prévention du vol de cookie)
                $this->deleteToken($tokenBrut);
                $this->createToken((int) $user['ID_user']);

                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['ID_user'];
                $_SESSION['user_pseudo'] = $user['Email'];
                $_SESSION['user_admin'] = (int) $user['ID_role'];
                $_SESSION['connecte_le'] = time();
                return true;
            }

            // Token invalide ou expiré → on supprime le cookie
            setcookie(COOKIE_NOM, '', [
                'expires' => time() - 42000,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        return false;
    }

    public function currentUser(): array
    {
        $this->startSession();
        if (!$this->isConnect()) {
            return [];
        }
        return [
            'id' => $_SESSION['user_id'],
            'pseudo' => $_SESSION['user_pseudo'],
            'admin' => $_SESSION['user_admin'],
        ];
    }
    private function createToken(int $userId): void
    {
        $tokenBrut = bin2hex(random_bytes(32));   // 64 caractères hex
        $tokenHash = hash('sha256', $tokenBrut);  // ce qu'on stocke en base
        $expiration = date('Y-m-d H:i:s', time() + COOKIE_DUREE);

        $stmt = $this->pdo->prepare(
            'INSERT INTO Remember_tokens (ID_user, token, expires)
             VALUES (:user, :hash, :exp)'
        );
        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':hash', $tokenHash);
        $stmt->bindValue(':exp',  $expiration);
        $stmt->execute();

        setcookie(COOKIE_NOM, $tokenBrut, [
            'expires'  => time() + COOKIE_DUREE,
            'path'     => '/',
            'secure'   => false,   // ← passer à true en HTTPS
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    /**
     * Supprime le token brut (via son hash) de la base de données.
     */
    private function deleteToken(string $tokenBrut): void
    {
        $tokenHash = hash('sha256', $tokenBrut);
        $stmt = $this->pdo->prepare(
            'DELETE FROM Remember_tokens WHERE token = :hash'
        );
        $stmt->bindValue(':hash', $tokenHash);
        $stmt->execute();
    }
}