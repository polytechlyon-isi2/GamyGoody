<?php

namespace GamyGoody\DAO;

use GamyGoody\Domain\Category;

class CategoryDAO extends DAO
{
    /**
     * Return a list of all categories, sorted by date (most recent first).
     *
     * @return array A list of all categories.
     */
    public function findAll() {
        $sql = "select * from category order by cat_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $categories = array();
        foreach ($result as $row) {
            $categoryId = $row['cat_id'];
            $categories[$categoryId] = $this->buildDomainObject($row);
        }
        return $categories;
    }

    /**
     * Creates an category object based on a DB row.
     *
     * @param array $row The DB row containing category data.
     * @return \GamyGoody\Domain\category
     */
    protected function buildDomainObject($row) {
        $category = new Category();
        $category->setId($row['cat_id']);
        $category->setTitle($row['cat_title']);
        return $category;
    }

    public function find($id) {
        $sql = "select * from category where cat_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No category matching id " . $id);
    }

    /**
     * Saves an category into the database.
     *
     * @param \GamyGoody\Domain\category $category The category to save
     */
    public function save(category $category) {
        $categoryData = array(
            'cat_title' => $category->getTitle()
            );

        if ($category->getId()) {
            // The category has already been saved : update it
            $this->getDb()->update('category', $categoryData, array('cat_id' => $category->getId()));
        } else {
            // The category has never been saved : insert it
            $this->getDb()->insert('category', $categoryData);
            // Get the id of the newly created category and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $category->setId($id);
        }
    }

    /**
     * Removes an category from the database.
     *
     * @param integer $id The category id.
     */
    public function delete($id) {
        // Delete the category
        $this->getDb()->delete('category', array('cat_id' => $id));
    }
    
    public function findAllTitles() {
        $sql = "select * from category order by cat_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $category = array();
        foreach ($result as $row) {
            $catId         = $row['cat_id'];
            $category[$catId] = $row['cat_title'];
        }
        return $category;
    }
    
    public function deleteAllByCategory($id) {
        $this->getDb()->delete('category', array('cat_id' => $id));
    }
}