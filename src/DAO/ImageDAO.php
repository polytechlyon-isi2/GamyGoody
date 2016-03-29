<?php

namespace GamyGoody\DAO;

use GamyGoody\Domain\Image;

class ImageDAO extends DAO 
{
    /**
     * Creates an Comment object based on a DB row.
     *
     * @param array $row The DB row containing Comment data.
     * @return \MicroCMS\Domain\Comment
     */
    protected function buildDomainObject($row) 
    {
        $image = new Image();
        $image ->setId($row['img_id']);
        $image ->setUrl($row['img_url']);
        $image ->setAlt($row['img_alt']);
        
        return $image;
    }

    public function save(Image $image) 
    {        
        $image->preUpload();

            $imageData = array(
            'img_url' => $image->getUrl(),
            'img_alt' => $image->getAlt()
            );
        
        if ($image->getId()) {
            // The comment has already been saved : update it
            $this->getDb()->update('image', $imageData, array('img_id' => $image->getId()));
        } else {
            // The comment has never been saved : insert it
            $this->getDb()->insert('image', $imageData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $image->setId($id);
        }
        $image->upload();
    }

    /**
     * Removes a comment from the database.
     *
     * @param @param integer $id The comment id
     */
    
    public function deleteImage(Image $image) {
        // Delete the comment
        $image->preRemoveUpload();
        $this->getDb()->delete('image', array('img_id' => $image->getId()));
        $image->removeUpload();
    }

    public function delete($id) {
        $this->deleteImage($this->find($id));
    }
    
    public function find($id) {
        $sql = "select * from image where img_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No comment matching id " . $id);
    }
    
    private function getAbsDir()
    {
        return __DIR__.'/../..'.$this->getDir();
    }

    private function getDir()
    {
        return '/web/images/upload';
    }
}