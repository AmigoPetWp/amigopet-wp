<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\EventRepository;
use AmigoPetWp\Domain\Entities\Event;

class EventService {
    private $repository;

    public function __construct(EventRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria um novo evento
     */
    public function createEvent(
        int $organizationId,
        string $title,
        string $description,
        string $location,
        \DateTimeInterface $date,
        ?int $maxParticipants = null
    ): int {
        $event = new Event(
            $organizationId,
            $title,
            $description,
            $date,
            $location,
            $maxParticipants
        );

        return $this->repository->save($event);
    }

    /**
     * Atualiza um evento existente
     */
    public function updateEvent(
        int $id,
        string $title,
        string $description,
        string $location,
        \DateTimeInterface $date,
        ?int $maxParticipants = null
    ): bool {
        $event = $this->repository->findById($id);
        if (!$event) {
            return false;
        }

        $event = new Event(
            $event->getOrganizationId(),
            $title,
            $description,
            $date,
            $location,
            $maxParticipants
        );
        $event->setId($id);

        return $this->repository->save($event) > 0;
    }

    /**
     * Inicia um evento
     */
    public function startEvent(int $eventId): bool {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            return false;
        }

        $event->start();
        return $this->repository->save($event) > 0;
    }

    /**
     * Completa um evento
     */
    public function completeEvent(int $eventId): bool {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            return false;
        }

        $event->complete();
        return $this->repository->save($event) > 0;
    }

    /**
     * Cancela um evento
     */
    public function cancelEvent(int $eventId): bool {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            return false;
        }

        $event->cancel();
        return $this->repository->save($event) > 0;
    }

    /**
     * Busca eventos por organização
     */
    public function findByOrganization(int $organizationId): array {
        return $this->repository->findByOrganization($organizationId);
    }

    /**
     * Busca eventos por status
     */
    public function findByStatus(string $status): array {
        return $this->repository->findByStatus($status);
    }

    /**
     * Busca eventos futuros
     */
    public function findUpcoming(): array {
        return $this->repository->findUpcoming();
    }

    /**
     * Busca eventos passados
     */
    public function findPast(): array {
        return $this->repository->findPast();
    }

    /**
     * Busca eventos por termo
     */
    public function search(string $term): array {
        return $this->repository->search($term);
    }

    /**
     * Gera relatório de eventos
     */
    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($startDate, $endDate);
    }

    /**
     * Busca eventos por filtros
     */
    public function findByFilters(array $filters): array {
        return $this->repository->findByFilters($filters);
    }

    /**
     * Verifica se há vagas disponíveis
     */
    public function hasVacancy(int $eventId): bool {
        return $this->repository->hasVacancy($eventId);
    }

    /**
     * Incrementa o número de participantes
     */
    public function incrementParticipants(int $eventId): bool {
        return $this->repository->incrementParticipants($eventId);
    }

    /**
     * Decrementa o número de participantes
     */
    public function decrementParticipants(int $eventId): bool {
        return $this->repository->decrementParticipants($eventId);
    }

    /**
     * Busca um evento por ID
     */
    public function findById(int $id): ?Event {
        return $this->repository->findById($id);
    }
}
