<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class RecipeManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List recipes
     *
     * @param array{
     *    recipe_id?: int,
     *    name?: string,
     *    page?: int,
     *    per_page?: int
     * } $filters Optional filters
     * @param bool $raw Return raw API response
     * @return array Returns formatted recipe list when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function list(array $filters = [], bool $raw = false): array
    {
        try {
            $params = [
                'page' => $filters['page'] ?? 1,
                'reslen' => $filters['per_page'] ?? 50
            ];

            if (isset($filters['recipe_id'])) {
                $params['rid'] = $filters['recipe_id'];
            }

            if (isset($filters['name'])) {
                $params['rname'] = $filters['name'];
            }

            $response = $this->api->listRecipes($params);

            if ($raw) {
                return $response;
            }

            $recipes = [];
            foreach ($response['recipe'] ?? [] as $recipe) {
                $recipes[] = [
                    'id' => (int) $recipe['rid'],
                    'name' => $recipe['name'],
                    'code' => $recipe['code'],
                    'description' => $recipe['desc'],
                    'logo' => $recipe['logo'],
                    'is_active' => (bool) $recipe['status']
                ];
            }

            return [
                'recipes' => $recipes,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list recipes: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Available shell types
     */
    private const SHELL_TYPES = [
        'sh' => '#!/bin/sh',
        'bash' => '#!/bin/bash',
        'ksh' => '#!/bin/ksh',
        'zsh' => '#!/bin/zsh'
    ];

    /**
     * Create a new recipe
     *
     * @param array{
     *    name: string,
     *    script: string,
     *    description?: string,
     *    logo?: string,
     *    shell?: string
     * } $params Recipe parameters
     * @param bool $raw Return raw API response
     * @return array|int Returns recipe ID when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function create(array $params, bool $raw = false): array|int
    {
        try {
            // Validate required fields
            if (empty($params['name'])) {
                throw new VirtualizorApiException('Recipe name is required');
            }
            if (empty($params['script'])) {
                throw new VirtualizorApiException('Recipe script is required');
            }

            // Validate shell type if provided
            if (isset($params['shell']) && !array_key_exists(strtolower($params['shell']), self::SHELL_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid shell type. Available types: ' . implode(', ', array_keys(self::SHELL_TYPES))
                );
            }

            $requestParams = [
                'recipe_name' => $params['name'],
                'recipe_script' => $params['script'],
                'desc' => $params['description'] ?? '',
                'recipe_logo' => $params['logo'] ?? '',
                'shell' => $params['shell'] ?? 'sh'
            ];

            $response = $this->api->createRecipe($requestParams);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to create recipe: Operation unsuccessful'
                );
            }

            return (int) $response['done'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create recipe: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Update an existing recipe
     *
     * @param int $recipeId Recipe ID to update
     * @param array{
     *    name?: string,
     *    script?: string,
     *    description?: string,
     *    logo?: string,
     *    shell?: string
     * } $params Recipe parameters to update
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function update(int $recipeId, array $params, bool $raw = false): array|bool
    {
        try {
            // Validate recipe exists
            $recipes = $this->list(['recipe_id' => $recipeId]);
            if (empty($recipes['recipes'])) {
                throw new VirtualizorApiException("Recipe with ID {$recipeId} not found");
            }

            // Validate shell type if provided
            if (isset($params['shell']) && !array_key_exists(strtolower($params['shell']), self::SHELL_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid shell type. Available types: ' . implode(', ', array_keys(self::SHELL_TYPES))
                );
            }

            $requestParams = [
                'rid' => $recipeId,
                'name' => $params['name'] ?? null,
                'code' => $params['script'] ?? null,
                'desc' => $params['description'] ?? null,
                'logo' => $params['logo'] ?? null,
                'shell' => $params['shell'] ?? null
            ];

            // Remove null values
            $requestParams = array_filter($requestParams, fn($value) => !is_null($value));

            $response = $this->api->updateRecipe($requestParams);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to update recipe: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to update recipe: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete recipe(s)
     *
     * @param int|array $recipeIds Single recipe ID or array of recipe IDs
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function delete(int|array $recipeIds, bool $raw = false): array|bool
    {
        try {
            $params = [
                'delete' => is_array($recipeIds) ? implode(',', $recipeIds) : $recipeIds
            ];

            $response = $this->api->deleteRecipes($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete recipe(s): Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete recipe(s): ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Activate recipe(s)
     *
     * @param int|array $recipeIds Single recipe ID or array of recipe IDs
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function activate(int|array $recipeIds, bool $raw = false): array|bool
    {
        try {
            $params = [
                'activate' => is_array($recipeIds) ? implode(',', $recipeIds) : $recipeIds
            ];

            $response = $this->api->activateRecipes($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to activate recipe(s): Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to activate recipe(s): ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Deactivate recipe(s)
     *
     * @param int|array $recipeIds Single recipe ID or array of recipe IDs
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deactivate(int|array $recipeIds, bool $raw = false): array|bool
    {
        try {
            $params = [
                'deactivate' => is_array($recipeIds) ? implode(',', $recipeIds) : $recipeIds
            ];

            $response = $this->api->deactivateRecipes($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to deactivate recipe(s): Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to deactivate recipe(s): ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 