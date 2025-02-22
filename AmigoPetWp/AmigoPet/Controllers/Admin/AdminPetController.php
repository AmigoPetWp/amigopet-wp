<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\PetService;

class AdminPetController extends BaseAdminController {
    private $petService;

    public function __construct() {
        parent::__construct();
        $this->petService = new PetService($this->db->getPetRepository());
    }

    protected function registerHooks(): void {
        // Menu e submenu
        add_action('admin_menu', [$this, 'addMenus']);

        // Ações para pets
        add_action('admin_post_apwp_save_pet', [$this, 'savePet']);
        add_action('admin_post_nopriv_apwp_save_pet', [$this, 'savePet']);
        add_action('admin_post_apwp_delete_pet', [$this, 'deletePet']);
        add_action('admin_post_nopriv_apwp_delete_pet', [$this, 'deletePet']);
    }

    public function addMenus(): void {
        // Submenu de Pets
        add_submenu_page(
            'amigopet-wp',
            __('Pets', 'amigopet-wp'),
            __('Pets', 'amigopet-wp'),
            'manage_amigopet_pets',
            'amigopet-wp-pets',
            [$this, 'renderPets']
        );

        // Submenu oculto para o formulário de pets
        add_submenu_page(
            null,
            __('Adicionar/Editar Pet', 'amigopet-wp'),
            __('Adicionar/Editar Pet', 'amigopet-wp'),
            'manage_amigopet_pets',
            'apwp-pet-form',
            [$this, 'renderPetForm']
        );
    }

    public function renderPets(): void {
        $this->checkPermission('manage_amigopet_pets');

        $list_table = new \AmigoPetWp\Admin\Tables\APWP_Pets_List_Table();
        $list_table->prepare_items();

        $this->loadView('admin/pets/pets-list', [
            'list_table' => $list_table
        ]);
    }

    public function renderPetForm(): void {
        $this->checkPermission('manage_amigopet_pets');

        $pet_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $pet = $pet_id ? $this->petService->findById($pet_id) : null;

        $this->loadView('admin/pets/pet-form', [
            'pet' => $pet,
            'species' => $this->petService->getSpecies(),
            'sizes' => $this->petService->getSizes(),
            'genders' => $this->petService->getGenders()
        ]);
    }

    public function savePet(): void {
        $this->checkPermission('manage_amigopet_pets');
        $this->verifyNonce('apwp_save_pet');

        $id = isset($_POST['pet_id']) ? (int)$_POST['pet_id'] : 0;
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'species' => sanitize_text_field($_POST['species']),
            'breed' => sanitize_text_field($_POST['breed']),
            'gender' => sanitize_text_field($_POST['gender']),
            'size' => sanitize_text_field($_POST['size']),
            'age' => (int)$_POST['age'],
            'description' => wp_kses_post($_POST['description']),
            'health_status' => wp_kses_post($_POST['health_status']),
            'is_available' => isset($_POST['is_available']),
            'organization_id' => get_current_user_id()
        ];

        try {
            if ($id) {
                $this->petService->updatePet($id, $data);
                $message = __('Pet atualizado com sucesso!', 'amigopet-wp');
            } else {
                $this->petService->createPet($data);
                $message = __('Pet cadastrado com sucesso!', 'amigopet-wp');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-pets'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-pets'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deletePet(): void {
        $this->checkPermission('manage_amigopet_pets');
        $this->verifyNonce('apwp_delete_pet');

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            wp_die(__('ID do pet não fornecido', 'amigopet-wp'));
        }

        try {
            $this->petService->deletePet($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-pets'),
                __('Pet excluído com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-pets'),
                $e->getMessage(),
                'error'
            );
        }
    }
}
