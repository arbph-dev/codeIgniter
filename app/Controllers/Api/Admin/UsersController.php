<?php
// a renommer
// app/Controllers/Api/Admin/UsersController.php
declare(strict_types=1);

namespace App\Controllers\Api\Admin;

use App\Services\Users\UserService;
use App\Traits\ApiResponse;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class UsersController extends ResourceController
{
    use ApiResponse;

    protected $format = 'json';

    protected UserService $service;

    public function __construct()
    {
        $this->service = service('user');
    }

    /**
     * GET /api/admin/users
     *
     * Liste des utilisateurs.
     *
     * Filtres :
     *   q
     *   group
     *   permission
     *   active
     *   page
     *   per_page
     */
    public function index(): ResponseInterface
    {
        $filters = [
            'q'          => $this->request->getGet('q'),
            'group'      => $this->request->getGet('group'),
            'permission' => $this->request->getGet('permission'),
            'active'     => $this->request->getGet('active'),
            'page'       => $this->request->getGet('page'),
            'per_page'   => $this->request->getGet('per_page'),
        ];

        $result = $this->service->list($filters);

        return $this->apiOk(
            $result['data'],
            $result['meta'] ?? null,
            'Liste des utilisateurs'
        );
    }

    /**
     * GET /api/admin/users/{id}
     */
    public function show($id = null): ResponseInterface
    {
        if (! $id) {
            return $this->apiBadRequest('ID manquant.');
        }

        $result = $this->service->find((int) $id);

        if (! $result) {
            return $this->apiNotFound(
                "Utilisateur #{$id} introuvable."
            );
        }

        return $this->apiOk(
            $result,
            null,
            'Détail de l’utilisateur'
        );
    }

    /**
     * GET /api/admin/users/search?q=...
     *
     * Recherche courte pour autocomplete / sélecteurs.
     */
    public function search(): ResponseInterface
    {
        $q = trim((string) $this->request->getGet('q'));

        if ($q === '') {
            return $this->apiBadRequest(
                'Le paramètre q est requis.'
            );
        }

        $limit = (int) (
            $this->request->getGet('len') ?? 20
        );

        $data = $this->service->search($q, $limit);

        return $this->apiOk(
            $data,
            null,
            'Recherche des utilisateurs'
        );
    }
}
