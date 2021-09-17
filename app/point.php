<?php
// point direction
const heading_north_east = [ 1, -1 ];
const heading_east_south = [ 1, 1 ];
const heading_south_west = [ -1, 1 ];
const heading_west_north = [ -1, -1 ];

class point
{
    // Properties
    public int $pos_x;
    public int $pos_y;
    public array $direction;

    function __construct( int $pos_x, int $pos_y, array $direction )
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

    function set_direction( array $direction )
    {
        $this->direction = $direction;
    }

    function get_direction()
    {
        return $this->direction;
    }
}

?>