<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\Event;

class EventRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = $this->getTableName('events');
    }

    protected function createEntity(array $row): object
    {
        $event = new Event(
            (int) $row['organization_id'],
            $row['title'],
            $row['description'],
            new \DateTime($row['date']),
            $row['location'],
            (int) $row['max_participants']
        );

        if (isset($row['id'])) {
            $event->setId((int) $row['id']);
        }

        if (isset($row['current_participants'])) {
            $event->setCurrentParticipants((int) $row['current_participants']);
        }

        if (isset($row['status'])) {
            $event->setStatus($row['status']);
        }

        if (isset($row['created_at'])) {
            $event->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $event->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $event;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Event) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of Event', 'amigopet'));
        }

        return [
            'organization_id' => $entity->getOrganizationId(),
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            'date' => $entity->getDate()->format('Y-m-d H:i:s'),
            'location' => $entity->getLocation(),
            'max_participants' => $entity->getMaxParticipants(),
            'current_participants' => $entity->getCurrentParticipants(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    public function findByOrganization(int $organizationId): array
    {
        return $this->findAll(['organization_id' => $organizationId]);
    }

    public function findByStatus(string $status): array
    {
        return $this->findAll(['status' => $status]);
    }

    public function findUpcoming(): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }

        return array_map(
            [$this, 'createEntity'],
            $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM %i WHERE date >= %s AND status = %s ORDER BY date ASC',
                    $table,
                    current_time('mysql'),
                    'active'
                ),
                ARRAY_A
            )
        );
    }

    public function findPast(): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }

        return array_map(
            [$this, 'createEntity'],
            $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM %i WHERE date < %s ORDER BY date DESC',
                    $table,
                    current_time('mysql')
                ),
                ARRAY_A
            )
        );
    }

    public function search(string $term): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $search = '%' . $wpdb->esc_like($term) . '%';

        return array_map(
            [$this, 'createEntity'],
            $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM %i WHERE title LIKE %s OR description LIKE %s OR location LIKE %s',
                    $table,
                    $search,
                    $search,
                    $search
                ),
                ARRAY_A
            )
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';

        return $wpdb->get_results(
            $wpdb->prepare(
                'SELECT COUNT(*) as total, status, DATE(date) as date, SUM(current_participants) as total_participants FROM %i WHERE date BETWEEN %s AND %s GROUP BY status, DATE(date) ORDER BY date DESC',
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );
    }

    public function findByFilters(array $filters): array
    {
        $criteria = [];

        if (isset($filters['organization_id'])) {
            $criteria['organization_id'] = (int) $filters['organization_id'];
        }

        if (isset($filters['status'])) {
            $criteria['status'] = $filters['status'];
        }

        if (isset($filters['date_start'])) {
            $criteria['date_start'] = $filters['date_start'];
        }

        if (isset($filters['date_end'])) {
            $criteria['date_end'] = $filters['date_end'];
        }

        if (isset($filters['has_vacancy'])) {
            $criteria['has_vacancy'] = (bool) $filters['has_vacancy'];
        }

        return $this->findAll($criteria);
    }

    public function hasVacancy(int $eventId): bool
    {
        $event = $this->findById($eventId);
        return $event && $event->getCurrentParticipants() < $event->getMaxParticipants();
    }

    public function incrementParticipants(int $eventId): bool
    {
        $event = $this->findById($eventId);
        if (!$event || !$this->hasVacancy($eventId)) {
            return false;
        }

        $event->setCurrentParticipants($event->getCurrentParticipants() + 1);
        return $this->save($event) > 0;
    }

    public function decrementParticipants(int $eventId): bool
    {
        $event = $this->findById($eventId);
        if (!$event || $event->getCurrentParticipants() <= 0) {
            return false;
        }

        $event->setCurrentParticipants($event->getCurrentParticipants() - 1);
        return $this->save($event) > 0;
    }
}