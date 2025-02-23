<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\PetSpecies;

/**
 * Repositório para gerenciar espécies de pets
 * 
 * @package AmigoPetWp\Domain\Database\Repositories
 */
class PetSpeciesRepository extends AbstractRepository {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    protected function getTableName(): string {
        return 'apwp_pet_species';
    }

    /**
     * {@inheritDoc}
     */
    protected function createEntity(array $data): PetSpecies {
        $species = new PetSpecies();
        $species->setId((int)$data['id']);
        $species->setName($data['name']);
        $species->setDescription($data['description'] ?? '');
        $species->setStatus($data['status']);
        $species->setCreatedAt(new \DateTime($data['created_at']));
        $species->setUpdatedAt(new \DateTime($data['updated_at']));
        return $species;
    }

    /**
     * {@inheritDoc}
     */
    protected function toDatabase($entity): array {
        if (!$entity instanceof PetSpecies) {
            throw new \InvalidArgumentException('Entity must be an instance of PetSpecies');
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
    public function findByStatus(string $status): array {
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
    public function findActive(): array {
        return $this->findByStatus('active');
    }

    /**
     * Encontra espécies por nome ou descrição
     *
     * @param string $term Termo de busca
     * @return array Lista de espécies
     */
    public function search(string $term): array {
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
    public function getSpeciesReport(?string $startDate = null, ?string $endDate = null): array {
        $where = ['1=1'];
        $params = [];

        if ($startDate && $endDate) {
            $where[] = 'created_at BETWEEN %s AND %s';
            $params[] = $startDate . ' 00:00:00';
            $params[] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $query = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total_species,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_species,
                COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_species,
                (SELECT COUNT(*) 
                 FROM {$this->wpdb->prefix}apwp_pets                 WHERE p.species_id IN (SELECT id FROM {$this->table})) as total_pets,
                (SELECT species_id 
                 FROM {$this->wpdb->prefix}apwp_pets 
                 GROUP BY species_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_common_species_id,
                (SELECT COUNT(*) 
                 FROM {$this->wpdb->prefix}apwp_pets                 INNER JOIN {$this->wpdb->prefix}apwp_adoptions a ON p.id = a.pet_id 
                 WHERE p.species_id IN (SELECT id FROM {$this->table})
                 AND a.status = 'completed') as total_adoptions
            FROM {$this->table}
            WHERE {$whereClause}",
            $params
        );

        $report = $this->wpdb->get_row($query, ARRAY_A);

        // Adiciona informações da espécie mais comum
        if ($report['most_common_species_id']) {
            $mostCommonSpecies = $this->findById((int)$report['most_common_species_id']);
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
