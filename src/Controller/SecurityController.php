<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Mns\Buggy\Core\AbstractController;

class SecurityController extends AbstractController
{

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function login()
    {

        if(!empty($_SESSION['user']))
        {
            if(!empty($_SESSION['admin'])) {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /dashboard');
            }
            exit;
        }

        if(!empty($_POST)) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userRepository->findByEmail($username);

            if($user) {
                // On vérifie le mot de passe
                // Support pour les mots de passe hashés (password_verify) et texte clair (pour compatibilité)
                $passwordMatch = false;
                $storedPassword = $user->getPassword();
                
                // Si le mot de passe stocké commence par $2y$ ou $2a$, c'est un hash bcrypt
                if (preg_match('/^\$2[ay]\$/', $storedPassword)) {
                    $passwordMatch = password_verify($password, $storedPassword);
                } else {
                    // Sinon, comparaison en texte clair (pour les données de test)
                    $passwordMatch = ($password === $storedPassword);
                }
                
                if($passwordMatch){
    
                    $_SESSION['user'] = [
                        'id' => $user->getId(),
                        'username' => $user->getFirstname(),
                        'firstname' => $user->getFirstname(),
                    ];

                    if($user->getIsadmin()) {
                        $_SESSION['admin'] = $user->getIsadmin();
                        header('Location: /admin/dashboard');
                        exit;
                    }
                    else
                    {
                        header('Location: /dashboard');
                        exit;
                    }
                }
                else
                {
                    $error = 'Invalid username or password';
                }
            } else {
                $error = 'Invalid username or password';
            }
        }

        return $this->render('security/login.html.php', [
            'title' => 'Login',
            'error' => $error ?? null,
        ]);
    }

    public function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['admin']);
        session_destroy();
        header('Location: /login');
        exit;
    }
}