<?php
declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

/**
 * Interface base para todos os repositórios do AmigoPet
 * 
 * @package AmigoPetWp\Domain\Database\Repositories
 */
interface BaseRepository
{
    /**
     * Encontra uma entidade pelo ID
     *
     * @param int $id ID da entidade
     * @return mixed|null Retorna a entidade ou null se não encontrada
     */
    public function findById(int $id): ?object;

    /**
     * Busca todas as entidades com base nos argumentos fornecidos
     *
     * @param array $args Argumentos de busca (opcional)
     * @return array Lista de entidades
     */
    public function findAll(array $args = []): array;

    /**
     * Salva uma entidade (insere ou atualiza)
     *
     * @param mixed $entity Entidade a ser salva
     * @return int ID da entidade salva
     * @throws \Exception Se houver erro ao salvar
     */
    public function save($entity): int;

    /**
     * Exclui uma entidade pelo ID
     *
     * @param int $id ID da entidade a ser excluída
     * @return bool true se excluído com sucesso, false caso contrário
     */
    public function delete(int $id): bool;
}
