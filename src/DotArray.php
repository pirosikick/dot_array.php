<?php

/**
 * DotArray
 *
 * @uses ArrayAccess
 * @uses Countable
 * @author Hiroyuki Anai <hiroyuki_anai@yahoo.co.jp>
 */
class DotArray implements ArrayAccess, Countable
{
    private $_array;

    /**
     * constructor
     *
     * @param array $array
     * @access public
     * @return void
     */
    public function __construct(array $array = array())
    {
        $this->_array = $array;
    }

    /**
     * get corresponding value
     *
     * @param string $offset
     * @access public
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $found = $this->_find($offset, $this->_array);
        return is_array($found) ? new self($found) : $found;
    }

    /**
     * check that corresponding value exists
     *
     * @param string $offset
     * @access public
     * @return bool
     */
    public function offsetExists($offset)
    {
        return ($this->offsetGet($offset) !== null);
    }

    /**
     * set value
     *
     * @param string $offset
     * @param mixed $value
     * @access public
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $keys = explode('.', $offset);
        $lastKey = array_pop($keys);
        $refs =& $this->_array;
        foreach ($keys as $key) {
            !isset($refs[$key]) and $refs[$key] = array();
            $refs =& $refs[$key];
        }
        $refs[$lastKey] = $value;
    }

    /**
     * unset value
     *
     * @param string $offset
     * @access public
     * @return void
     */
    public function offsetUnset($offset)
    {
        $keys = explode('.', $offset);
        $lastKey = array_pop($keys);
        $refs =& $this->_array;
        foreach ($keys as $key) {
            if (!isset($refs[$key])) {
                return false;
            }
            $refs =& $refs[$key];
        }
        unset($refs[$lastKey]);
    }

    /**
     * count array size
     *
     * @access public
     * @return int
     */
    public function count()
    {
        return count($this->_array);
    }

    private function _find($offset)
    {
        $current = $this->_array;
        foreach (explode('.', $offset) as $key) {
            if (!isset($current[$key])) {
                return null;
            }
            $current = $current[$key];
        }
        return $current;
    }
}
