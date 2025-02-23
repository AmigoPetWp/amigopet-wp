<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Event;

class EventRepository extends AbstractRepository {
    protected function getTableName(): string {
        return 'apwp_events';
    }

    protected function createEntity(array $data): Event {
        $event = new Event(
            (int)$data['organization_id'],
            $data['title'],
            $data['description'],
            new \DateTime($data['date']),
            $data['location'],
            (int)$data['max_participants']
        );

        if (isset($data['id'])) {
            $event->setId((int)$data['id']);
        }

        if (isset($data['current_participants'])) {
            $event->setCurrentParticipants((int)$data['current_participants']);
        }

        if (isset($data['status'])) {
            $event->setStatus($data['status']);
        }

        if (isset($data['created_at'])) {
            $event->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $event->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $event;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof Event) {
            throw new \InvalidArgumentException('Entity must be an instance of Event');
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

    public function findByOrganization(int $organizationId): array {
        return $this->findAll(['organization_id' => $organizationId]);
    }

    public function findByStatus(string $status): array {
        return $this->findAll(['status' => $status]);
    }

    public function findUpcoming(): array {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} 
            WHERE date >= %s 
            AND status = 'active'
            ORDER BY date ASC",
            current_time('mysql')
        );
        
        return array_map(
            [$this, 'createEntity'],
            $this->wpdb->get_results($sql, ARRAY_A)
        );
    }

    public function findPast(): array {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} 
            WHERE date < %s 
            ORDER BY date DESC",
            current_time('mysql')
        );
        
        return array_map(
            [$this, 'createEntity'],
            $this->wpdb->get_results($sql, ARRAY_A)
        );
    }

    public function search(string $term): array {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} 
            WHERE title LIKE %s 
            OR description LIKE %s 
            OR location LIKE %s",
            "%{$term}%",
            "%{$term}%",
            "%{$term}%"
        );
        
        return array_map(
            [$this, 'createEntity'],
            $this->wpdb->get_results($sql, ARRAY_A)
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        $where = [];
        $params = [];
        
        if ($startDate) {
            $where[] = "date >= %s";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $where[] = "date <= %s";
            $params[] = $endDate;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $sql = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total,
                status,
                DATE(date) as date,
                SUM(current_participants) as total_participants
            FROM {$this->table}
            {$whereClause}
            GROUP BY status, DATE(date)
            ORDER BY date DESC",
            $params
        );
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function findByFilters(array $filters): array {
        $criteria = [];
        
        if (isset($filters['organization_id'])) {
            $criteria['organization_id'] = (int)$filters['organization_id'];
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
            $criteria['has_vacancy'] = (bool)$filters['has_vacancy'];
        }
        
        return $this->findAll($criteria);
    }

    public function hasVacancy(int $eventId): bool {
        $event = $this->findById($eventId);
        return $event && $event->getCurrentParticipants() < $event->getMaxParticipants();
    }

    public function incrementParticipants(int $eventId): bool {
        $event = $this->findById($eventId);
        if (!$event || !$this->hasVacancy($eventId)) {
            return false;
        }

        $event->setCurrentParticipants($event->getCurrentParticipants() + 1);
        return $this->save($event) > 0;
    }

    public function decrementParticipants(int $eventId): bool {
        $event = $this->findById($eventId);
        if (!$event || $event->getCurrentParticipants() <= 0) {
            return false;
        }

        $event->setCurrentParticipants($event->getCurrentParticipants() - 1);
        return $this->save($event) > 0;
    }
}
