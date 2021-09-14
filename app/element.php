<?php
// elements [ name, widht, height, symbol ]
const element_passage_one = [ "Passage", 5, 2, "_" ];
const element_passage_two = [ "Passage", 10, 2, "_" ];
const element_passage_three = [ "Passage", 15, 2, "_" ];
const element_t_junction_right = [ "T-Junction Right", 2, 2, "t" ];
const element_t_junction_left = [ "T-Junction Left", 2, 2, "t" ];
const element_dead_end = [ "Dead End", 5, 2, "e" ];
const element_corner_right = [ "Corner Right", 2, 2, "r" ];
const element_corner_left = [ "Corner Left", 2, 2, "l" ];
const element_stairs_start = [ "Stairway", 2, 2, "s" ];
const element_stairs_down = [ "Stairs", 2, 2, "d" ];
const element_stairs_out = [ "Stairs", 2, 2, "u" ];
const element_room_large = [ "Room", 10, 5, "-" ];
const element_room_small = [ "Room", 5, 5, "-" ];
const element_room_revolving = [ "Room", 5, 5, "-" ];

class element
{
    // Properties
    public string $type;
    public int $width;
    public int $height;
    public int $direction;
    public array $tiles;
    public string $feature;

    function __construct( array $element, int $direction )
    {
        $this->type = $element[ 0 ];
        $this->width = $element[ $direction == heading_west || $direction == heading_east ? 1 : 2 ];
        $this->height = $element[ $direction == heading_west || $direction == heading_east ? 2 : 1 ];

        for ( $y = 0; $y < $this->height; $y++ )
        {
            for ( $x = 0; $x < $this->width; $x++ )
            {
                $this->tiles[ $y ][ $x ] = $element[ 3 ];
            }
        }

        $this->direction = $direction;
    }

    // Methods
    function set_type( int $type )
    {
        $this->type = $type;
    }

    function get_type()
    {
        return $this->type;
    }

    function set_width( int $width )
    {
        $this->width = $width;
    }

    function get_width()
    {
        return $this->width;
    }

    function set_height( int $height )
    {
        $this->height = $height;
    }

    function get_height()
    {
        return $this->height;
    }

    function set_direction( int $direction )
    {
        $this->direction = $direction;
    }

    function get_direction()
    {
        return $this->direction;
    }

    function set_tile( int $pos_x, int $pos_y, string $value )
    {
        $this->tiles[ $pos_y ][ $pos_x ] = $value;
    }

    function get_tile( int $pos_x, int $pos_y )
    {
        return $this->tiles[ $pos_y ][ $pos_x ];
    }

    function get_tiles()
    {
        return $this->tiles;
    }

    function set_feature( string $feature)
    {
        $this->feature = $feature;
    }

    function get_feature()
    {
        return $this->feature;
    }

    function roll_feature()
    {
        $dice = new dice();

        $roll = array_sum( $dice->roll( "2D12" ) );

        if ( $roll <= 5 || $roll >= 22 ) // Wandering Monsters
        {
            $this->feature = "Wandering Monsters";
            $this->tiles[ random_int( 1, $this->height ) - 1 ][ random_int( 1, $this->width ) - 1 ] = "M";
        }

        if ( $roll >= 6 && $roll <= 14 ) // Nothing
        {
            $this->feature = "Nothing";
            // draw nothing :-)
        }

        if ( $roll >= 15 && $roll <= 19 ) // 1 Door
        {
            $this->feature = "1 Door";
            $this->tiles[ random_int( 1, $this->height ) - 1 ][ random_int( 1, $this->width ) - 1 ] = "D";
        }

        if ( $roll >= 20 && $roll <= 21 ) // 2 Doors
        {
            $this->feature = "2 Doors";

            if ( $this->direction == heading_west || $this->direction == heading_east )
            {
                $this->tiles[ 0 ][ random_int( 1, $this->width ) - 1 ] = "D";
                $this->tiles[ 1 ][ random_int( 1, $this->width ) - 1 ] = "D";
            }
            else
            {
                $this->tiles[ random_int( 1, $this->height ) - 1 ][ 0 ] = "D";
                $this->tiles[ random_int( 1, $this->height ) - 1 ][ 1 ] = "D";
            }
        }
    }
}
?>