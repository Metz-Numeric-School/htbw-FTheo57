<?php
namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Mns\Buggy\Core\AbstractController;

class UserController extends AbstractController
{

    private UserRepository $userRepository;

    public function __construct()
    {   
        $this->userRepository = new UserRepository();
    }


    public function index()
    {
        $users = $this->userRepository->findAll();
        return $this->render('admin/user/index.html.php', [
            'users' => $users,
        ]);
    }

    public function new()
    {
        $errors = [];

        if(!empty($_POST['user']))
        {
            $user = $_POST['user'];
            
            if(empty($user['lastname']))
                $errors['lastname'] = 'Le Nom est obligatoire';

            if(empty($user['firstname']))
                $errors['firstname'] = 'Le Prénom est obligatoire';

            if(empty($user['email']))
                $errors['email'] = 'L\'email est obligatoire';
            elseif(!filter_var($user['email'], FILTER_VALIDATE_EMAIL))
                $errors['email'] = 'L\'email n\'est pas valide';
            elseif($this->userRepository->findByEmail($user['email']))
                $errors['email'] = 'Cet email est déjà utilisé';

            if(empty($user['password']))
                $errors['password'] = 'Le mot de passe est obligatoire';

            
            if(count($errors) == 0)
            {
                $id = $this->userRepository->insert($user);
                header('Location: /admin/user');
                exit;
            }
        }

        return $this->render('admin/user/new.html.php', [
            'errors' => $errors,
        ]);
    }
}