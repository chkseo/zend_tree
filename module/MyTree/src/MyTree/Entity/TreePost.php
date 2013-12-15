<?php
namespace MyTree\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 *
 * @author Denis Kozikov <manden90@gmail.com>
 */
class TreePost
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer",  columnDefinition="INT(9) AUTO_INCREMENT") 
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @var int
     * @ORM\Column(type="integer", columnDefinition="INT(9)")
     */
    protected $parent;
	
	protected $tree;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set text.
     *
     * @param string $text
     *
     * @return void
     */
    public function setParent($text)
    {
        $this->parent = $parent;
    }

  
    /**
     * Helper function.
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = ($val !== null) ? $val : null;
            }
        }
    }

    /**
     * Helper function
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
	
	public function getTreeCategories($om){
		$array = $this->getListCategories($om, 'tree');
		if (!$array || !is_array($array)){
			return 'Нет записей';
		}
		
		foreach ($array AS $key => $value){
			$arr[$key]['id'] = $value->id;
			$arr[$key]['title'] = $value->title;
			$arr[$key]['parent'] = $value->parent;
		}
		
		foreach ($arr AS $key => $value){
			$resArr[$value['parent']][] = $value;
		}

		$result = $this->build_tree($resArr, 1);
		return $result;
	}
	
	private function build_tree($cats,$parent_id,$only_parent = false){
		if (count($cats) === 1){
			
			return '';
		}
		if(is_array($cats) and isset($cats[$parent_id])){
	        $tree = '<ul>';
	        if($only_parent==false){
	            foreach($cats[$parent_id] as $cat){
	                $tree .= '<li><a href="/tree/edit/' . $cat['id'] . '">'.$cat['title'] . '</a>';
					$tree .= '<a href="/tree/delete/' . $cat['id'] . '" title="Удалить ' . $cat['title'] . '?">';
					$tree .= '<img src="http://4udak.com/wp-content/uploads/2011/09/delete.png" class="img_delete" />';
					$tree .= '</a>';
	                $tree .=  $this->build_tree($cats,$cat['id']);
	                $tree .= '</li>';
	            }
	        }elseif(is_numeric($only_parent)){
	            $cat = $cats[$parent_id][$only_parent];
	            $tree .= '<li>'.$cat['name'].' #'.$cat['id'];
	            $tree .=  $this->build_tree($cats,$cat['id']);
	            $tree .= '</li>';
	        }
	        $tree .= '</ul>';
	    }
	    else return null;
	    return $tree;
		}
	
	public function getListCategories($objectManager, $type = 0){
		$post = $objectManager
        ->getRepository('\MyTree\Entity\TreePost')
        ->findAll();
		if ($type === 'tree'){
			return $post;
		}
		if (!$post){
			return '<option value="1">Нет записей</option>';
		}
		
		if (is_array($post)){
			$result = '';
			foreach ($post AS $item){
				$result .= '<option value="' . $item->id . '">' . $item->title . '</option>' . "\n";
			}
		}
		return $result;
	}

}