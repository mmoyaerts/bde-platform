<?php

namespace App\Service\DB;

use Exception;
use PDO;
use PDOException;

class EntityManager
{
    private PDO $conn;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $dbManager = new DatabaseManager();
        $this->conn = $dbManager->connect();
    }

    /**
     * Exécute une requête SQL et retourne le résultat.
     *
     * @param string $sql La requête SQL.
     * @param array $params Les paramètres de la requête (pour les requêtes préparées).
     * @return array|bool Retourne un tableau associatif pour une requête SELECT, true pour une requête réussie sans résultat.
     * @throws Exception
     */
    public function executeRequest(string $sql, array $params = []): array|bool
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            // Vérifier si la requête est un SELECT (elle doit retourner des données)
            if (stripos($sql, 'SELECT') === 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return true;
        } catch (PDOException $e) {
            var_dump($stmt);
            var_dump($params);
            throw new Exception('Erreur SQL : ' . $e->getMessage());
        }
    }
}
