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
    
    public function findAllTitles() {
        $sql = "select * from game order by game_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $games = array();
        foreach ($result as $row) {
            $gameId         = $row['game_id'];
            $games[$gameId] = $row['game_title'];
        }
        return $games;
    }

    /**
     * [isGameExistant : return true if this game id exists]
     * @param  [int]  $id [id of the wanted game]
     * @return boolean           [exists ?]
     */
    public function isGameExistant($id) 
    {
        $sql = "select * from game where game_id=? limit 1";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return true;
        else
            return false;
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
        $game->setLogoByEx($row['game_logo_ex']);
        $game->setBackgroundByEx($row['game_bg_ex']);
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
    public function save(game $game) 
    {

        $logo = $game->getLogo();
        $bg = $game->getBackground();

        if($logo->isValid() && $bg->isValid())
        {
            $logo_ex = $logo->guessExtension();
            $bg_ex = $bg->guessExtension();

            $gameData = array(
            'game_title' => $game->getTitle(),
            'game_logo_ex' => $logo_ex,
            'game_bg_ex' => $bg_ex
            );
        }
        else
        {
            $gameData = array(
            'game_title' => $game->getTitle()
            );
        }


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

        $logo_name = 'logo_'.$game->getId().'.'.$logo_ex;
        $bg_name = 'bg_'.$game->getId().'.'.$bg_ex;
        $dir = $this->getAbsDir();

        $logo->move($dir, $logo_name);
        $bg->move($dir, $bg_name);

        $game->setLogo($this->getDir().$logo_name);
        $game->setBackground($this->getDir().$bg_name);
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

    private function getAbsDir()
    {
        return __DIR__.'/../..'.$this->getDir();
    }

    private function getDir()
    {
        return '/web/images/games';
    }
}