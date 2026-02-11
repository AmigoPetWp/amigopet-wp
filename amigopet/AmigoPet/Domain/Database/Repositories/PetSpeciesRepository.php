<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\PetSpecies;

/**
 * Repositório para gerenciar espécies de pets
 * 
 * @package AmigoPetWp\Domain\Database\Repositories
 */
class PetSpeciesRepository extends AbstractRepository
{


    protected function initTable(): void
    {
        $this->table = $this->getTableName('pet_species');
    }

    /**
     * {@inheritDoc}
     */
    protected function createEntity(array $row): object
    {
        $species = new PetSpecies();
        $species->setId((int) $row['id']);
        $species->setName($row['name']);
        $species->setDescription($row['description'] ?? '');
        $species->setStatus($row['status']);
        $species->setCreatedAt(new \DateTime($row['created_at']));
        $species->setUpdatedAt(new \DateTime($row['updated_at']));
        return $species;
    }

    /**
     * {@inheritDoc}
     */
    protected function toDatabase($entity): array
    {
        if (!$entity instanceof PetSpecies) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of PetSpecies', 'amigopet'));
        }

        return [
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Encontra espécies por status
     *
     * @param string $status Status da espécie
     * @return array Lista de espécies
     */
    public function findByStatus(string $status): array
    {
        return $this->findAll([
            'status' => $status,
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    /**
     * Encontra espécies ativas
     *
     * @return array Lista de espécies ativas
     */
    public function findActive(): array
    {
        return $this->findByStatus('active');
    }

    /**
     * Encontra espécies por nome ou descrição
     *
     * @param string $term Termo de busca
     * @return array Lista de espécies
     */
    public function search(string $term): array
    {
        return $this->findAll([
            'search' => $term,
            'search_columns' => ['name', 'description'],
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    /**
     * Gera relatório de espécies
     *
     * @param string|null $startDate Data inicial (Y-m-d)
     * @param string|null $endDate Data final (Y-m-d)
     * @return array Relatório de espécies
     */
    public function getSpeciesReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        $petsTable = $this->sanitizeIdentifier($this->wpdb->prefix . 'apwp_pets');
        $adoptionsTable = $this->sanitizeIdentifier($this->wpdb->prefix . 'apwp_adoptions');
        if ($table === '' || $petsTable === '' || $adoptionsTable === '') {
            return [];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';
        $report = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                COUNT(*) as total_species,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_species,
                COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_species,
                (SELECT COUNT(*) 
                 FROM %i p 
                 WHERE p.species_id IN (SELECT id FROM %i)) as total_pets,
                (SELECT species_id 
                 FROM %i
                 GROUP BY species_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_common_species_id,
                (SELECT COUNT(*) 
                 FROM %i p 
                 INNER JOIN %i a ON p.id = a.pet_id 
                 WHERE p.species_id IN (SELECT id FROM %i)
                 AND a.status = 'completed') as total_adoptions
            FROM %i
            WHERE created_at BETWEEN %s AND %s",
                $petsTable,
                $table,
                $petsTable,
                $petsTable,
                $adoptionsTable,
                $table,
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );

        // Adiciona informações da espécie mais comum
        if ($report['most_common_species_id']) {
            $mostCommonSpecies = $this->findById((int) $report['most_common_species_id']);
            if ($mostCommonSpecies) {
                $report['most_common_species'] = [
                    'id' => $mostCommonSpecies->getId(),
                    'name' => $mostCommonSpecies->getName(),
                    'description' => $mostCommonSpecies->getDescription()
                ];
            }
        }

        return $report;
    }
}