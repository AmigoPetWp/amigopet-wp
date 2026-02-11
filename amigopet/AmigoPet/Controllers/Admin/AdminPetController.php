<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Services\PetService;

use AmigoPetWp\Domain\Services\PetBreedService;

class AdminPetController extends BaseAdminController
{
    private $petService;
    private $petBreedService;

    public function __construct()
    {
        parent::__construct();
        $this->petService = new PetService($this->db->getPetRepository());
        $this->petBreedService = new PetBreedService(
            $this->db->getPetBreedRepository(),
            $this->db->getPetSpeciesRepository()
        );
    }

    protected function registerHooks(): void
    {
        // Menu e submenu
        add_action('admin_menu', [$this, 'addMenus']);

        // Ações para pets
        add_action('admin_post_apwp_save_pet', [$this, 'savePet']);
        add_action('admin_post_nopriv_apwp_save_pet', [$this, 'savePet']);
        add_action('admin_post_apwp_delete_pet', [$this, 'deletePet']);
        add_action('admin_post_nopriv_apwp_delete_pet', [$this, 'deletePet']);
    }

    public function addMenus(): void
    {
        // Submenu de Pets
        add_submenu_page(
            'amigopet',
            esc_html__('Pets', 'amigopet'),
            esc_html__('Pets', 'amigopet'),
            'manage_amigopet_pets',
            'amigopet-pets',
            [$this, 'renderPets']
        );
    }

    public function renderPets(): void
    {
        $this->checkPermission('manage_amigopet_pets');
        $this->loadView('admin/pets/pets-list-combined');
    }

    public function renderPetForm(): void
    {
        $this->checkPermission('manage_amigopet_pets');
        $this->loadView('admin/pets/pet-form-combined');
    }


    public function savePet(): void
    {
        $this->checkPermission('manage_amigopet_pets');
        check_admin_referer('apwp_save_pet');

        $id = isset($_POST['pet_id']) ? (int) $_POST['pet_id'] : 0;

        $name = sanitize_text_field($_POST['pet_name'] ?? '');
        // Note: View uses 'pet_name', not 'name' in input! (Line 59 of view)
        // Wait, looking at Step 520: <input type="text" id="pet_name" name="pet_name" ...>
        // But the previous controller code used $_POST['name']. That was a bug or mismatch! 
        // I will use 'pet_name' as per View.

        $speciesId = (int) ($_POST['species_id'] ?? 0);
        $breedName = sanitize_text_field($_POST['breed'] ?? '');
        $organizationId = get_current_user_id();
        $size = sanitize_text_field($_POST['size'] ?? 'medium');
        $age = (int) ($_POST['age'] ?? 0);
        $description = wp_kses_post($_POST['description'] ?? '');

        // Optional fields not yet in UI
        $rga = sanitize_text_field($_POST['rga'] ?? '');
        $microchip = sanitize_text_field($_POST['microchip_number'] ?? '');
        $healthInfo = [];
        if (isset($_POST['vaccinated']))
            $healthInfo['vaccinated'] = true;
        if (isset($_POST['neutered']))
            $healthInfo['neutered'] = true;

        // Resolve Breed
        $breedId = 0;
        if ($breedName && $speciesId) {
            $breedId = $this->petBreedService->getOrCreate($breedName, $speciesId);
        }

        try {
            if ($id) {
                $this->petService->updatePet(
                    $id,
                    $name,
                    $speciesId,
                    $breedId,
                    $organizationId,
                    $size,
                    $description,
                    $age,
                    $rga,
                    $microchip,
                    $healthInfo
                );
                $message = esc_html__('Pet atualizado com sucesso!', 'amigopet');
            } else {
                $this->petService->createPet(
                    $name,
                    $speciesId,
                    $breedId,
                    $organizationId,
                    $size,
                    $description,
                    $age,
                    $rga,
                    $microchip,
                    $healthInfo
                );
                $message = esc_html__('Pet cadastrado com sucesso!', 'amigopet');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-pets'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-pets'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deletePet(): void
    {
        $this->checkPermission('manage_amigopet_pets');
        check_admin_referer('apwp_delete_pet');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do pet não fornecido', 'amigopet'));
        }

        try {
            $this->petService->deletePet($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-pets'),
                esc_html__('Pet excluído com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-pets'),
                $e->getMessage(),
                'error'
            );
        }
    }
}