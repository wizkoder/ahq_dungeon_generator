<?php

class element
{
    public string $type;
    public int $width;
    public int $height;
    public int $direction;
    public string $symbol;
    public string $feature;

    function __construct( array $element, int $direction2 )
    {
        $this->type = $element[ 0 ];
        $this->direction = $direction2;
        $this->symbol = $element[ 3 ];

        if ( $direction2 == heading_west or $direction2 == heading_east )
        {
            $this->height = $element[ 2 ];
            $this->width = $element[ 1 ];
        }
        else
        {
            $this->height = $element[ 2 ];
            $this->width = $element[ 1 ];
        }
    }

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

    function set_feature( string $feature)
    {
        $this->feature = $feature;
    }

    function get_feature()
    {
        return $this->feature;
    }

    function set_symbol( string $symbol)
    {
        $this->symbol = $symbol;
    }

    function get_symbol()
    {
        return $this->symbol;
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