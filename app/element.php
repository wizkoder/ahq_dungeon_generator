<?php
// element alignment
const element_horizontal = 0;
const element_vertical = 1;

// elements [ name, widht, height, symbol ]
const element_passage_one = [ "Passage", 5, 2, "_" ];
const element_passage_two = [ "Passage", 10, 2, "_" ];
const element_passage_three = [ "Passage", 15, 2, "_" ];
const element_t_junction = [ "T-Junction", 2, 2, "t" ];
const element_dead_end = [ "Dead End", 5, 2, "e" ];
const element_corner_right = [ "Corner", 2, 2, "r" ];
const element_corner_left = [ "Corner", 2, 2, "l" ];
const element_stairs_down = [ "Stairs", 2, 2, "s" ];
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
    public int $alignment;
    public array $tiles;
    public string $feature;

    function __construct( array $element, int $alignment )
    {
        $this->type = $element[ 0 ];
        $this->width = $element[ $alignment == element_horizontal ? 1 : 2 ];
        $this->height = $element[ $alignment == element_horizontal ? 2 : 1 ];

        for ( $y = 0; $y < $this->height; $y++ )
        {
            for ( $x = 0; $x < $this->width; $x++ )
            {
                $this->tiles[ $y ][ $x ] = $element[ 3 ];
            }
        }

        $this->alignment = $alignment;
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

    function set_alignment( int $alignment )
    {
        $this->alignment = $alignment;
    }

    function get_alignment()
    {
        return $this->alignment;
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

            if ($this->alignment == element_horizontal)
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