<?php
namespace App\Controller\Member;

use App\Repository\HabitRepository;
use App\Repository\HabitLogRepository;
use Mns\Buggy\Core\AbstractController;

class HabitsController extends AbstractController
{
    private HabitRepository $habitRepository;
    private HabitLogRepository $habitLogRepository;

    public function __construct()
    {
        $this->habitRepository = new HabitRepository();
        $this->habitLogRepository = new HabitLogRepository();
    }

    /**
     * Liste les habitudes de l'utilisateur
     */
    public function index()
    {

        $userId = $_SESSION['user']['id'];
        $habits = $this->habitRepository->findByUser($userId);

        return $this->render('member/habits/index.html.php', [
            'habits' => $habits,
        ]);
    }

    /**
     * Crée une nouvelle habitude
     */
    public function new()
    {

        $errors = [];

        if (!empty($_POST['habit'])) {
            $habit = $_POST['habit'];

            if (empty($habit['name'])) {
                $errors['name'] = 'Le nom de l’habitude est obligatoire';
            }

            if (count($errors) === 0) {
                $this->habitRepository->insert([
                    'user_id' => $_SESSION['user']['id'],
                    'name' => $habit['name'],
                    'description' => $habit['description'] ?? null
                ]);

                header('Location: /habits');
                exit;
            }
        }

        return $this->render('member/habits/new.html.php', [
            'errors' => $errors
        ]);
    }

    /**
     * Marque ou décoche une habitude pour aujourd'hui
     */
    public function toggle()
    {

        if (!empty($_POST['habit_id'])) {
            $habitId = (int)$_POST['habit_id'];
            $userId = $_SESSION['user']['id'];
            
            // Vérifier que l'habitude appartient à l'utilisateur connecté
            $habit = $this->habitRepository->find($habitId);
            if ($habit && $habit->getUserId() === $userId) {
                $this->habitLogRepository->toggleToday($habitId);
            } else {
                // Habitude non trouvée ou n'appartient pas à l'utilisateur
                http_response_code(403);
                header('Location: /habits');
                exit;
            }
        }

        header('Location: /dashboard');
        exit;
    }
}
