<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\DomainEntities\Pet;

class PetRepository {
    public function __construct() {}

    public function save(Pet $pet): int {
        $post_data = [
            'post_type' => 'apwp_pet',
            'post_status' => 'publish',
            'post_title' => $pet->getName()
        ];

        $post_id = wp_insert_post($post_data);
        
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, 'pet_species', $pet->getSpecies());
            update_post_meta($post_id, 'pet_size', $pet->getSize());
            update_post_meta($post_id, 'pet_status', 'available');
        }

        return $post_id;
    }

    public function findById(int $id): ?Pet {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== 'apwp_pet') {
            return null;
        }

        return $this->hydrate($post);
    }

    public function findAll(int $perPage = 10, int $page = 1): array {
        $args = [
            'post_type' => 'apwp_pet',
            'posts_per_page' => $perPage,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        $query = new \WP_Query($args);
        return $query->posts;
    }

    public function count(): int {
        $args = [
            'post_type' => 'apwp_pet',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ];

        $query = new \WP_Query($args);
        return $query->found_posts;
    }

    private function hydrate($post): Pet {
        return new Pet(
            $post->post_title,
            get_post_meta($post->ID, 'pet_species', true),
            get_post_meta($post->ID, 'pet_size', true)
        );
    }
}
