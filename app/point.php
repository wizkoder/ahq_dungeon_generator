<?php
// point direction
const heading_north = 0;
const heading_east = 1;
const heading_south = 2;
const heading_west = 3;
const heading_end = 4;

class point
{
    // Properties
    public int $pos_x;
    public int $pos_y;
    public int $direction;

    function __construct( int $pos_x, int $pos_y, int $direction )
    {
        $this->set_pos_x( $pos_x );
        $this->set_pos_y( $pos_y );
        $this->set_direction( $direction );
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