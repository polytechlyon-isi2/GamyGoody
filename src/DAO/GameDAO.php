<?php

namespace GamyGoody\DAO;

use GamyGoody\Domain\Game;

class GameDAO extends DAO
{
    /**
     * Return a list of all games, sorted by date (most recent first).
     *
     * @return array A list of all games.
     */
    
    public function findAll() {
        $sql = "select * from game order by game_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $games = array();
        foreach ($result as $row) {
            $gameId         = $row['game_id'];
            $games[$gameId] = $this->buildDomainObject($row);
        }
        return $games;
    }

    /**
     * Creates an game object based on a DB row.
     *
     * @param array $row The DB row containing game data.
     * @return \GamyGoody\Domain\game
     */
    
    /**
     * [buildDomainObject description]
     * @param  [type] $row [description]
     * @return [type]      [description]
     */
    protected function buildDomainObject($row) {
        $game = new Game();
        $game->setId($row['game_id']);
        $game->setTitle($row['game_title']);
        return $game;
    }

    /**
     * [find description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function find($id) {
        $sql = "select * from game where game_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No game matching id " . $id);
    }

    /**
     * Saves an game into the database.
     *
     * @param \GamyGoody\Domain\game $game The game to save
     */
    public function save(game $game) {
        $gameData = array(
            'game_title' => $game->getTitle()
            );

        if ($game->getId()) {
            // The game has already been saved : update it
            $this->getDb()->update('game', $gameData, array('game_id' => $game->getId()));
        } else {
            // The game has never been saved : insert it
            $this->getDb()->insert('game', $gameData);
            // Get the id of the newly created game and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $game->setId($id);
        }
    }

    /**
     * Removes an game from the database.
     *
     * @param integer $id The game id.
     */
    public function delete($id) {
        // Delete the game
        $this->getDb()->delete('game', array('game_id' => $id));
    }
}