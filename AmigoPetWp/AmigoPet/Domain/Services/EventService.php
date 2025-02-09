<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\DomainDatabase\EventRepository;
use AmigoPetWp\DomainEntities\Event;

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
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        int $maxParticipants
    ): int {
        $event = new Event(
            $organizationId,
            $title,
            $description,
            $location,
            $startDate,
            $endDate,
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
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        int $maxParticipants
    ): void {
        $event = $this->repository->findById($id);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event = new Event(
            $event->getOrganizationId(),
            $title,
            $description,
            $location,
            $startDate,
            $endDate,
            $maxParticipants
        );

        $this->repository->save($event);
    }

    /**
     * Cancela um evento
     */
    public function cancelEvent(int $eventId): void {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event->cancel();
        $this->repository->save($event);
    }

    /**
     * Adiciona um participante ao evento
     */
    public function addParticipant(int $eventId): void {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event->addParticipant();
        $this->repository->save($event);
    }

    /**
     * Remove um participante do evento
     */
    public function removeParticipant(int $eventId): void {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event->removeParticipant();
        $this->repository->save($event);
    }

    /**
     * Busca um evento por ID
     */
    public function findById(int $id): ?Event {
        return $this->repository->findById($id);
    }

    /**
     * Lista eventos de uma organização
     */
    public function findByOrganization(int $organizationId): array {
        return $this->repository->findByOrganization($organizationId);
    }

    /**
     * Lista eventos futuros
     */
    public function findUpcoming(): array {
        return $this->repository->findUpcoming();
    }
}
