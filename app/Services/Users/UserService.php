<?php
// app/Services/Users/UserService.php
declare(strict_types=1);

namespace App\Services\Users;

use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class UserService
{
    protected UserModel $model;

    protected $db;

    /**
     * Noms des tables Shield.
     */
    protected array $tables;

    public function __construct()
    {
        $this->model = model(UserModel::class);
        $this->db = db_connect();
        $this->tables = config('Auth')->tables;
    }

    /**
     * Liste paginée des utilisateurs.
     *
     * Filtres :
     * - q
     * - group
     * - permission
     * - active
     * - page
     * - per_page
     */
    public function list(array $filters = []): array
    {
        $page = max(
            1,
            (int) ($filters['page'] ?? 1)
        );

        $perPage = min(
            100,
            max(
                1,
                (int) ($filters['per_page'] ?? 20)
            )
        );

        $builder = $this->buildQuery($filters);

        /*
         * Total des résultats AVANT pagination.
         */
        $total = $builder->countAllResults(false);

        if ($total === 0) {
            return [
                'data' => [],
                'meta' => [
                    'page'     => $page,
                    'per_page' => $perPage,
                    'total'    => 0,
                    'pages'    => 0,
                ],
            ];
        }

        $offset = ($page - 1) * $perPage;

        $rows = $builder
            ->select($this->tables['users'] . '.*')
            ->orderBy(
                $this->tables['users'] . '.id',
                'ASC'
            )
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        /*
         * Transformation en User Entity Shield.
         *
         * Les relations Shield sont chargées explicitement
         * pour disposer des identités, groupes et permissions.
         */
        $users = [];

        foreach ($rows as $row) {
            $user = $this->loadUser(
                (int) $row['id']
            );

            if ($user !== null) {
                $users[] = $user;
            }
        }

        return [
            'data' => array_map(
                fn (User $user): array =>
                    $this->toListArray($user),
                $users
            ),
            'meta' => [
                'page'     => $page,
                'per_page' => $perPage,
                'total'    => $total,
                'pages'    => (int) ceil(
                    $total / $perPage
                ),
            ],
        ];
    }

    /**
     * Recherche courte.
     *
     * Utilisée notamment pour :
     * - autocomplete
     * - RelationPickerDialog
     * - listes de sélection
     */
    public function search(
        string $q,
        int $limit = 20
    ): array {
        $q = trim($q);

        if ($q === '') {
            return [];
        }

        $limit = min(
            100,
            max(1, $limit)
        );

        $builder = $this->buildQuery([
            'q' => $q,
        ]);

        $rows = $builder
            ->select($this->tables['users'] . '.*')
            ->orderBy(
                $this->tables['users'] . '.username',
                'ASC'
            )
            ->limit($limit)
            ->get()
            ->getResultArray();

        $users = [];

        foreach ($rows as $row) {
            $user = $this->loadUser(
                (int) $row['id']
            );

            if ($user !== null) {
                $users[] = $user;
            }
        }

        return array_map(
            fn (User $user): array =>
                $this->toListArray($user),
            $users
        );
    }

    /**
     * Retourne le détail d'un utilisateur.
     */
    public function find(int $id): ?array
    {
        $user = $this->loadUser($id);

        if ($user === null) {
            return null;
        }

        return $this->toDetailArray($user);
    }

    /**
     * Charge une User Entity avec les relations Shield.
     */
    protected function loadUser(int $id): ?User
    {
        $user = $this->model
            ->withIdentities()
            ->withGroups()
            ->withPermissions()
            ->find($id);

        return $user instanceof User
            ? $user
            : null;
    }

    /**
     * Groupes de l'utilisateur.
     */
    public function groups(User $user): array
    {
        return $user->getGroups() ?? [];
    }

    /**
     * Permissions directes de l'utilisateur.
     */
    public function permissions(User $user): array
    {
        return array_keys(
            $user->getPermissions() ?? []
        );
    }

    /**
     * Construit la requête SQL de filtrage.
     *
     * Les filtres sont appliqués AVANT la pagination.
     */
    protected function buildQuery(array $filters)
    {
        $usersTable = $this->tables['users'];
        $identityTable = $this->tables['identities'];
        $groupsTable = $this->tables['groups_users'];
        $permissionsTable = $this->tables['permissions_users'];

        $builder = $this->db
            ->table($usersTable)
            ->select($usersTable . '.*')
            ->distinct();

        /*
         * ----------------------------------------------------------
         * Recherche q
         * ----------------------------------------------------------
         *
         * Recherche sur :
         * - username
         * - id
         * - email
         *
         * L'email est stocké dans auth_identities.secret.
         *
         * On limite la jointure à l'identité email/password
         * afin de ne jamais rechercher dans les autres secrets
         * Shield (tokens, HMAC, etc.).
         */
        $q = trim(
            (string) ($filters['q'] ?? '')
        );

        if ($q !== '') {
            $builder->join(
                $identityTable,
                $identityTable . '.user_id = ' .
                $usersTable . '.id AND ' .
                $identityTable . '.type = ' .
                $this->db->escape(
                    Session::ID_TYPE_EMAIL_PASSWORD
                ),
                'left'
            );

            $builder
                ->groupStart()
                    ->like(
                        $usersTable . '.username',
                        $q
                    )
                    ->orLike(
                        $usersTable . '.id',
                        $q
                    )
                    ->orLike(
                        $identityTable . '.secret',
                        $q
                    )
                ->groupEnd();
        }

        /*
         * ----------------------------------------------------------
         * Filtre active
         * ----------------------------------------------------------
         */
        if (
            array_key_exists('active', $filters)
            && $filters['active'] !== ''
            && $filters['active'] !== null
        ) {
            $active = $this->normalizeBoolean(
                $filters['active']
            );

            if ($active !== null) {
                $builder->where(
                    $usersTable . '.active',
                    $active ? 1 : 0
                );
            }
        }

        /*
         * ----------------------------------------------------------
         * Filtre group
         * ----------------------------------------------------------
         */
        $group = trim(
            (string) ($filters['group'] ?? '')
        );

        if ($group !== '') {
            $builder
                ->join(
                    $groupsTable,
                    $groupsTable . '.user_id = ' .
                    $usersTable . '.id',
                    'inner'
                )
                ->where(
                    $groupsTable . '.group',
                    strtolower($group)
                );
        }

        /*
         * ----------------------------------------------------------
         * Filtre permission
         * ----------------------------------------------------------
         */
        $permission = trim(
            (string) ($filters['permission'] ?? '')
        );

        if ($permission !== '') {
            $builder
                ->join(
                    $permissionsTable,
                    $permissionsTable . '.user_id = ' .
                    $usersTable . '.id',
                    'inner'
                )
                ->where(
                    $permissionsTable . '.permission',
                    strtolower($permission)
                );
        }

        return $builder;
    }

    /**
     * DTO léger destiné aux listes.
     */
    protected function toListArray(User $user): array
    {
        return [
            'id'       => $user->id,
            'username' => $user->username,
            'email'    => $user->email,
            'active'   => (bool) $user->active,
        ];
    }

    /**
     * DTO complet destiné au détail.
     */
    protected function toDetailArray(User $user): array
    {
        return [
            'id'          => $user->id,
            'username'    => $user->username,
            'email'       => $user->email,
            'active'      => (bool) $user->active,
            'groups'      => $this->groups($user),
            'permissions' => $this->permissions($user),
            'created_at'  => $user->created_at,
            'last_active' => $user->last_active,
        ];
    }

    /**
     * Normalise un paramètre booléen HTTP.
     */
    protected function normalizeBoolean(
        mixed $value
    ): ?bool {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(
            trim((string) $value)
        );

        return match ($value) {
            '1', 'true', 'yes', 'on'  => true,
            '0', 'false', 'no', 'off' => false,
            default                   => null,
        };
    }
}
