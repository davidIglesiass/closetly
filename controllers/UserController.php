<?php

require_once 'models/UserRepository.php';

class UserController
{
    private const ROLES = ['user', 'vendedor', 'admin'];

    public function index()
    {
        echo 'User controller, action index';
    }

    public function create()
    {
        require_once 'views/user/create.php';
    }

    public function save(){
        if(!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['email']) && !empty($_POST['password'])) {

            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $_SESSION['registerfailed'] = 'unable to save';
                return header('Location: /user/create');
            }

            $user = new User();
            $user->name = $_POST['firstname'].' '.$_POST['lastname'];
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];
            $user->rol = 'user'; // self-registration never takes a role from form input

            (new UserRepository())->insert($user);

            $_SESSION['registersaved'] = 'completed successfully';
        }else{
            $_SESSION['registerfailed'] = 'Please fill all the fields';
        }

        header('Location: /user/create');
    }

    public function login(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $_SESSION['loginfailed'] = 'unable to login';
            return header('Location: /');
        }

        if(!isset($_POST['email']) || !isset($_POST['password'])){
            $_SESSION['loginfailed'] = 'unable to login';
            return header('Location: /');
        }

        $identity = (new UserRepository())->login($_POST['email'], $_POST['password']);

        if(!$identity){
            $_SESSION['loginfailed'] = 'unable to login';
            return header('Location: /');
        }

        $_SESSION['identity'] = $identity;

        if($identity['rol'] == 'admin') $_SESSION['admin'] = true;

        return header('Location: /');
    }

    public function logout(){
        if(isset($_SESSION['identity']) || isset($_SESSION['admin'])) unset($_SESSION['identity'], $_SESSION['admin']);
        unset($_SESSION['carshop']);

        header('Location: /');
    }

    public function manage(){
        Utils::isAdmin();
        $repository = new UserRepository();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $users = $repository->findAll($page, $perPage);
        $totalPages = max(1, (int) ceil($repository->countAll() / $perPage));
        $paginationBase = '/user/manage';
        require_once 'views/user/manage.php';
    }

    public function adminCreate(){
        Utils::isAdmin();
        $roles = self::ROLES;
        require_once 'views/user/admin_create.php';
    }

    public function edit(){
        Utils::isAdmin();

        $user = isset($_GET['id']) ? (new UserRepository())->find($_GET['id']) : null;
        if(!$user){
            $_SESSION['userunsaved'] = 'User not found';
            return header('Location: /user/manage');
        }

        $edit = true;
        $roles = self::ROLES;
        require_once 'views/user/admin_create.php';
    }

    public function adminSave(){
        Utils::isAdmin();

        if(!Utils::validateCsrfToken($_POST['csrf_token'] ?? null)) return header('Location: /user/manage');

        $editingId = $_GET['id'] ?? null;
        $passwordProvided = !empty($_POST['password']);

        if(!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['email']) && !empty($_POST['rol']) && ($editingId || $passwordProvided)) {

            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !in_array($_POST['rol'], self::ROLES, true)){
                $_SESSION['userunsaved'] = 'Invalid email or role';
                return header('Location: /user/manage');
            }

            $user = new User();
            $user->name = $_POST['firstname'].' '.$_POST['lastname'];
            $user->email = $_POST['email'];
            $user->rol = $_POST['rol'];

            $repository = new UserRepository();

            if($editingId){
                $user->id = $editingId;
                if($passwordProvided) $user->password = $_POST['password'];
                $saved = $repository->update($user, $passwordProvided);
                $_SESSION[$saved ? 'usersaved' : 'userunsaved'] = $saved ? 'User updated successfully' : 'User not updated';
            }else{
                $user->password = $_POST['password'];
                $saved = $repository->insert($user);
                $_SESSION[$saved ? 'usersaved' : 'userunsaved'] = $saved ? 'User created successfully' : 'User not created';
            }
        }else{
            $_SESSION['userunsaved'] = 'Please fill all the fields';
        }

        header('Location: /user/manage');
    }

    public function delete(){
        Utils::isAdmin();

        if(!Utils::validateCsrfToken($_POST['csrf_token'] ?? null)) return header('Location: /user/manage');

        if(isset($_POST['id']) && $_POST['id'] != $_SESSION['identity']['id']){
            $deleted = (new UserRepository())->delete($_POST['id']);
            $_SESSION[$deleted ? 'userdeleted' : 'userundeleted'] = $deleted ? 'User deleted successfully' : 'User not deleted';
        }else{
            $_SESSION['userundeleted'] = 'You cannot delete your own account';
        }

        header('Location: /user/manage');
    }
}
