<?php
// elements [ name, widht, height, symbol ]
const element_passage_one = [ "Passage", 5, 2, "_" ];
const element_passage_two = [ "Passage", 10, 2, "_" ];
const element_passage_three = [ "Passage", 15, 2, "_" ];
const element_t_junction = [ "T-Junction", 2, 2, "t" ];
const element_dead_end = [ "Dead End", 5, 2, "e" ];
const element_corner_right = [ "Corner Right", 2, 2, "r" ];
const element_corner_left = [ "Corner Left", 2, 2, "l" ];
const element_stairs_start = [ "Stairway", 2, 2, "s" ];
const element_stairs_down = [ "Stairs Down", 2, 2, "d" ];
const element_stairs_out = [ "Stairs Out", 2, 2, "u" ];
const element_room_large = [ "Room", 10, 5, "R" ];
const element_room_small = [ "Room", 5, 5, "R" ];
const element_room_revolving = [ "Room", 5, 5, "-" ];

class element
{
    // Properties
    public string $type;
    public int $width;
    public int $height;
    public array $direction;
    public array $tiles;
    public string $feature;

    function __construct( array $element, array $direction )
    {
        $this->type = $element[ 0 ];

        if ( $direction == heading_north_east || $direction == heading_south_west )
        {
            $this->width = $element[ 2 ];
            $this->height = $element[ 1 ];
        }
        else
        {
            $this->width = $element[ 1 ];
            $this->height = $element[ 2 ];
        }

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

    function get_placeable_width()
    {
        return $this->width + ( $this->direction == heading_north_east || $this->direction == heading_south_west ? 0 : 2 );
    }

    function set_height( int $height )
    {
        $this->height = $height;
    }

    function get_height()
    {
        return $this->height;
    }

    function get_placeable_height()
    {
        return $this->height + ( $this->direction == heading_north_east || $this->direction == heading_south_west ? 2 : 0 );
    }

    function set_direction( array $direction )
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

    function roll_feature( point $point )
    {
        $points = array();
        $dice = new dice();
        $roll = array_sum( $dice->roll( "2D12" ) );

        if ( $roll <= 5 || $roll >= 22 ) // Wandering Monsters
        {
            $this->feature = "Wandering Monsters";

            $pos_x = random_int( 1, $this->width ) - 1;
            $pos_y = random_int( 1, $this->height ) - 1;
    
            $this->tiles[ $pos_y ][ $pos_x ] = "M";
        }

        if ( $roll >= 6 && $roll <= 14 ) // Nothing
        {
            $this->feature = "Nothing";
            // draw nothing :-)
        }

        if ( $roll >= 15 && $roll <= 19 ) // 1 Door
        {
            $this->feature = "1 Door";

            $pos_x = random_int( 1, $this->width ) - 1;
            $pos_y = random_int( 1, $this->height ) - 1;
    
            $this->tiles[ $pos_y ][ $pos_x ] = "D";

            if ( $this->direction == heading_north_east )
            {
                if ( $pos_x == 0 ) array_push( $points, new point( $point->get_pos_x() - $this->get_width() / 2, $point->get_pos_y(), heading_west_north ) );
                if ( $pos_x == 1 ) array_push( $points, new point( $point->get_pos_x() + $this->get_width(), $point->get_pos_y(), heading_north_east ) );
            }

            if ( $this->direction == heading_east_south )
            {
                if ( $pos_y == 0 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $this->get_height() / 2, heading_north_east ) );
                if ( $pos_y == 1 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $this->get_height(), heading_south_west ) );
            }

            if ( $this->direction == heading_south_west )
            {
                if ( $pos_x == 0 ) array_push( $points, new point( $point->get_pos_x() + $this->get_width() / 2, $point->get_pos_y(), heading_east_south ) );
                if ( $pos_x == 1 ) array_push( $points, new point( $point->get_pos_x() - $this->get_width(), $point->get_pos_y(), heading_south_west ) );
            }

            if ( $this->direction == heading_west_north )
            {
                if ( $pos_y == 0 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $this->get_height() / 2, heading_south_west ) );
                if ( $pos_y == 1 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $this->get_height(), heading_west_north ) );
            }
        }

        if ( $roll >= 20 && $roll <= 21 ) // 2 Doors
        {
            $this->feature = "2 Doors";

            for ( $i = 0; $i < 2; $i++ )
            { 
                $pos_x = random_int( 1, $this->width ) - 1;
                $pos_y = random_int( 1, $this->height ) - 1;

                if ( $this->direction == heading_north_east )
                {
                    $this->tiles[ $pos_y ][ $i ] = "D";

                    if ( $pos_x == 0 ) array_push( $points, new point( $point->get_pos_x() - $this->get_width() / 2, $point->get_pos_y(), heading_west_north ) );
                    if ( $pos_x == 1 ) array_push( $points, new point( $point->get_pos_x() + $this->get_width(), $point->get_pos_y(), heading_north_east ) );
                }
    
                if ( $this->direction == heading_east_south )
                {
                    $this->tiles[ $i ][ $pos_x ] = "D";

                    if ( $pos_y == 0 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $this->get_height() / 2, heading_north_east ) );
                    if ( $pos_y == 1 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $this->get_height(), heading_south_west ) );
                }
    
                if ( $this->direction == heading_south_west )
                {
                    $this->tiles[ $pos_y ][ $i ] = "D";

                    if ( $pos_x == 0 ) array_push( $points, new point( $point->get_pos_x() + $this->get_width() / 2, $point->get_pos_y(), heading_east_south ) );
                    if ( $pos_x == 1 ) array_push( $points, new point( $point->get_pos_x() - $this->get_width(), $point->get_pos_y(), heading_south_west ) );
                }
    
                if ( $this->direction == heading_west_north )
                {
                    $this->tiles[ $i ][ $pos_x ] = "D";

                    if ( $pos_y == 0 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $this->get_height() / 2, heading_south_west ) );
                    if ( $pos_y == 1 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $this->get_height(), heading_west_north ) );
                }
            }
        }

        return $points;
    }
}
?>