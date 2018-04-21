<?php
//classe générique de table pouvant être utilisée par toutes sortes de tables (qui en hériteront)
namespace Framework\Database; /*comparée à l'ancien PostTable */

use Pagerfanta\Pagerfanta;

class Table
{

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Table name
     * @var string
     */
    protected $table; /*ajout*/

    /**
     * Entity that we need to use
     * @var string|null
     */
    protected $entity; /*Ajout*/

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Items pagination
     *
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            /*'SELECT * FROM posts ORDER BY created_at DESC',
            'SELECT COUNT(id) FROM posts',
            Post::class);****Ancien*/
            $this->paginationQuery(), /*ajout*/
            "SELECT COUNT(id) FROM {$this->table}", /*modif*/
            $this->entity /*ajout*/
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    protected function paginationQuery() /*ajout*/
    {
        return "SELECT * FROM {$this->table}";
    }

    /**
     * Getting a key-value list from db records
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(\PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Getting post from ID
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id)//: ?Post
    {
        $query = $this->pdo
            ->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        if ($this->entity) { /*ajout*/
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetch() ?: null;
    }

    /**
     * Update post in db
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    /**
     * Add new record
     *
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        /*ancien: $values = array_map(function ($field) {
            return ':' . $field;
        }, $fields);*/
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields); /*ajout*/
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $statement->execute($params);
    }

    /**
     * Delete a record
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $statement->execute([$id]);
    }

    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * @return mixed
     */
    public function getEntity(): string //getter pour récupérer entity *ajout*
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getTable(): string //getter pour récupérer table *ajout*
    {
        return $this->table;
    }

    /**
     * Validate if record exists
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $statement = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO //getter pour obtenir le PDO *ajout*
    {
        return $this->pdo;
    }
}
