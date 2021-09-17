<?php

class point
{
    public int $pos_x;
    public int $pos_y;
    public int $direction;

    function __construct( int $pos_x, int $pos_y, int $direction )
    {
        $this->set_pos_x( $pos_x );
        $this->set_pos_y( $pos_y );
        $this->set_direction( $direction );
    }

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

    function set_direction( int $direction )
    {
        $this->direction = $direction;
    }

    function get_direction()
    {
        return $this->direction;
    }
}

?>