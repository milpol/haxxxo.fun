<?php

namespace Repositories;

use PDO;

class ResultsRepository
{
    const CONTEST_START_TIME = 1677142800;

    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllResults()
    {
        $statement = $this->pdo->prepare('SELECT * FROM results');
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_NAMED);
    }

    public function setResult(array $data)
    {
        $statement = $this->pdo->prepare('
            INSERT INTO results (task, user, epoch_time)
            VALUES (:task, :user, :epochTime)');
        $epoch = time() - self::CONTEST_START_TIME;
        $username = str_replace('@zendesk.com', '', $data['username']);
        $statement->bindParam('task', $data['task'], PDO::PARAM_INT);
        $statement->bindParam('user', $username, PDO::PARAM_STR);
        $statement->bindParam('epochTime', $epoch, PDO::PARAM_INT);
        return $statement->execute();
    }

    public function getTasksResults($task)
    {
        $statement = $this->pdo->prepare(
            'SELECT user, epoch_time FROM results WHERE task = :task ORDER BY epoch_time ASC');
        $statement->bindParam('task', $task, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_NAMED);
    }

    public function isNotDefined($data)
    {
        $statement = $this->pdo->prepare('
            SELECT id FROM results WHERE user = :user AND task = :task');
        $username = str_replace('@zendesk.com', '', $data['username']);
        $statement->bindParam('task', $data['task'], PDO::PARAM_INT);
        $statement->bindParam('user', $username, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_NAMED);
        return empty($result);
    }

    public function getTasksFinalResults()
    {
        $statement = $this->pdo->prepare(
            'SELECT user, all_time FROM (
                        SELECT user, SUM(epoch_time) AS all_time, COUNT(id) AS tasks FROM results GROUP BY user ORDER BY all_time ASC) tmp 
                    WHERE tasks = 6;');
        $statement->bindParam('task', $task, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_NAMED);
    }
}