<?php

class point
{
    // Properties
    public int $pos_x;
    public int $pos_y;

    function __construct( int $pos_x, int $pos_y )
    {
        $this->pos_x = $pos_x;
        $this->pos_y = $pos_y;
    }

    // Methods
    function set_pos_x( int $pos_x )
    {
        $this->pos_x = $pos_x;
    }

    function get_pos_x()
    {
        return $this->pos_x;
    }

    function set_pos_y( int $pos_y )
    {
        $this->pos_y = $pos_y;
    }

    function get_pos_y()
    {
        return $this->pos_y;
    }
}

?>