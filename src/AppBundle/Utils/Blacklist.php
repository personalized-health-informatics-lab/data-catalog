<?php

namespace AppBundle\Utils;

/**
 * A utility class associates with black list of author names
 */
class Blacklist
{

    /**
     * @var array local list
     */
    private $_black_list = array();

    /**
     * Return the validity of the name
     *
     * @param string $name the author name
     *
     * @return boolean whether author name is valid
     */
    public function isValid($name)
    {
        return !in_array($name, $this->_black_list);
    }


    /**
     * Return the list
     *
     * @return array the list
     */
    public function getBlackList()
    {
        return $this->_black_list;
    }

}
