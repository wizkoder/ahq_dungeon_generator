<?php

const element_alignment_horizontal = 0;
const element_alignment_vertical = 1;

const element_type_staircase = "s";
const element_type_floor = "_";
const element_type_t_junction = "t";
const element_type_dead_end = "e";
const element_type_right_turn = "r";
const element_type_left_turn = "l";
const element_type_stairs_down = "s";
const element_type_stairs_out = "u";

class element
{
    // Properties
    public int $width;
    public int $height;
    public int $alignment;
    public array $tiles;
    public string $feature;

    function __construct( int $width, int $height, string $type, int $alignment )
    {
        $this->width = $width;
        $this->height = $height;

        for ( $y = 0; $y < $this->height; $y++ )
        {
            for ( $x = 0; $x < $this->width; $x++ )
            {
                $this->tiles[ $y ][ $x ] = $type;
            }
        }

        $this->alignment = $alignment;
    }

    // Methods
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

            if ($this->alignment == element_alignment_horizontal)
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