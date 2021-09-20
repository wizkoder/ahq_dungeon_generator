<?php
// elements [ name, widht, height, symbol, segments ] -> http://www.kreativekorp.com/software/fonts/ksquare.shtml
const element_passage_one = [ "Passage 1 section", 5, 2, "_", [ [ "─", "━" ], [ "│", "┃" ] ] ];
const element_passage_two = [ "Passage 2 sections", 10, 2, "_", [ [ "─", "━" ], [ "│", "┃" ] ] ];
const element_passage_three = [ "Passage 3 sections", 15, 2, "_", [ [ "─", "━" ], [ "│", "┃" ] ] ];
const element_t_junction = [ "T-Junction", 2, 2, "t", [ [ [ "┑", "─" ], [ "┏", "─" ] ], [ [ "┘", "┑" ], [ "┃", "┃" ] ], [ [ "┖", "━" ], [ "┘", "━" ] ], [ [ "┏", "┖" ], [ "│", "│" ] ] ] ];
const element_dead_end = [ "Dead End", 5, 2, "e", [
    [
        [ "│", "┃" ],
        [ "┌", "┒" ]
    ],
    [
        [ "─", "┐" ],
        [ "━", "┙" ]
    ],
    [
        [ "└", "┚" ],
        [ "│", "┃" ]
    ],
    [
        [ "┌", "─" ],
        [ "┕", "━" ]
    ]
] ];
const element_corner_right = [ "Corner Right", 2, 2, "r", [ [ [ "│", "┌" ], [ "┏", "─" ] ], [ [ "─", "┑" ], [ "┒", "┃" ] ], [ [ "┃", "┛" ], [ "┘", "━" ] ], [ [ "━", "┖" ], [ "┕", "│" ] ] ] ];
const element_corner_left = [ "Corner Left", 2, 2, "l", [ [ [ "┑", "─" ], [ "┃", "┒" ] ], [ [ "┘", "━" ], [ "┃", "┛" ] ], [ [ "┖", "━" ], [ "│", "┕" ] ], [ [ "┏", "─" ], [ "│", "┌" ] ] ] ];
const element_stairs_start = [ "Stairs Start", 2, 2, "s", [
    [
        [ "s", "p" ],
        [ "", "" ]
    ],
    [
        [ "s", "" ],
        [ "p", "" ]
    ],
    [
        [ "", "" ],
        [ "s", "p" ]
    ],
    [
        [ "", "s" ],
        [ "", "p" ]
    ]
] ];
const element_stairs_down = [ "Stairs Down", 2, 2, "d", [
    [
        [ "", "" ],
        [ "s", "d" ]
    ],
    [
        [ "", "s" ],
        [ "", "d" ]
    ],
    [
        [ "s", "d" ],
        [ "", "" ]
    ],
    [
        [ "s", "" ],
        [ "d", "" ]
    ]
] ];
const element_stairs_out = [ "Stairs Out", 2, 2, "o", [
    [
        [ "", "" ],
        [ "s", "o" ]
    ],
    [
        [ "", "s" ],
        [ "", "o" ]
    ],
    [
        [ "s", "o" ],
        [ "", "" ]
    ],
    [
        [ "s", "" ],
        [ "o", "" ]
    ]
] ];
const element_room_large = [ "Room Large", 10, 5, "R", [ [ "┌", "─", "┒" ], [ "│", "&nbsp;", "┃" ], [ "┕", "━", "┛" ] ] ];
const element_room_small = [ "Room Small", 5, 5, "R", [ [ "┌", "─", "┒" ], [ "│", "&nbsp;", "┃" ], [ "┕", "━", "┛" ] ] ];
const element_room_revolving = [ "Room Revolving", 5, 5, "R", [ [ "┌", "─", "┒" ], [ "│", "&nbsp;", "┃" ], [ "┕", "━", "┛" ] ] ];

class element
{
    // Properties
    public string $type;
    public int $width;
    public int $height;
    public array $direction;
    public array $tiles;
    public array $segments;
    public string $feature;

    function __construct( array $element, array $direction )
    {
        $this->type = $element[ 0 ];
        $this->direction = $direction;

        if ( $this->direction == heading_north_east || $this->direction == heading_south_west )
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

        $this->segments = $element[ 4 ];
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

    function get_segment( int $pos_x, int $pos_y, array $direction )
    {
        $segment = $this->get_tile( $pos_x, $pos_y );

        if ( $direction == heading_north_east )
        {
            if ( str_starts_with( $this->get_type(), "Room" ) )
            {
                if ( $pos_x == 0 ) $array_y = 0;
                elseif ( $pos_x == ( $this->width - 1 ) ) $array_y = 2;
                else $array_y = 1;

                if ( $pos_y == 0 ) $array_x = 2;
                elseif ( $pos_y == ( $this->height - 1 ) ) $array_x = 0;
                else $array_x = 1;

                $segment = $this->segments[ $array_x ][ $array_y ];
            }

            if ( str_starts_with( $this->get_type(), "Passage" ) && $segment == "_" )
            {
                $array_x = 1;

                if ( $pos_x == 0 ) $array_y = 0;
                else $array_y = 1;

                $segment = $this->segments[ $array_x ][ $array_y ];
            }

            if ( in_array( $this->get_type(), [ "Dead End", "Stairs Start", "Stairs Down", "Stairs Out" ] ) )
            {
                if ( $pos_x == ( $this->width - 1 ) ) $array_y = 1;
                else $array_y = 0;

                if ( $pos_y == ( $this->height - 1 ) ) $array_x = 1;
                else $array_x = 0;

                $segment = $this->segments[ 0 ][ $array_x ][ $array_y ];
            }

            if ( in_array( $this->get_type(), [ "Corner Right", "Corner Left", "T-Junction" ] ) )
            {
                $segment = $this->segments[ 0 ][ $pos_x ][ $pos_y ];
            }
        }

        if ( $direction == heading_east_south )
        {
            if ( str_starts_with( $this->get_type(), "Room" ) )
            {
                if ( $pos_x == 0 ) $array_y = 0;
                elseif ( $pos_x == ( $this->width - 1 ) ) $array_y = 2;
                else $array_y = 1;

                if ( $pos_y == 0 ) $array_x = 0;
                elseif ( $pos_y == ( $this->height - 1 ) ) $array_x = 2;
                else $array_x = 1;

                $segment = $this->segments[ $array_x ][ $array_y ];
            }

            if ( str_starts_with( $this->get_type(), "Passage" ) && $segment == "_" )
            {
                $array_x = 0;

                if ( $pos_y == 0 ) $array_y = 0;
                else $array_y = 1;

                $segment = $this->segments[ $array_x ][ $array_y ];
            }

            if ( in_array( $this->get_type(), [ "Dead End", "Stairs Start", "Stairs Down", "Stairs Out" ] ) )
            {
                if ( $pos_x == ( $this->width - 1 ) ) $array_y = 1;
                else $array_y = 0;

                if ( $pos_y == ( $this->height - 1 ) ) $array_x = 1;
                else $array_x = 0;

                $segment = $this->segments[ 1 ][ $array_x ][ $array_y ];
            }

            if ( in_array( $this->get_type(), [ "Corner Right", "Corner Left", "T-Junction" ] ) )
            {
                $segment = $this->segments[ 1 ][ $pos_x ][ $pos_y ];
            }
        }

        if ( $direction == heading_south_west )
        {
            if ( str_starts_with( $this->get_type(), "Room" ) )
            {
                if ( $pos_x == 0 ) $array_y = 2;
                elseif ( $pos_x == ( $this->width - 1 ) ) $array_y = 0;
                else $array_y = 1;

                if ( $pos_y == 0 ) $array_x = 0;
                elseif ( $pos_y == ( $this->height - 1 ) ) $array_x = 2;
                else $array_x = 1;

                $segment = $this->segments[ $array_x ][ $array_y ];
            }

            if ( str_starts_with( $this->get_type(), "Passage" ) && $segment == "_" )
            {
                $array_x = 1;

                if ( $pos_x == 0 ) $array_y = 1;
                else $array_y = 0;

                $segment = $this->segments[ $array_x ][ $array_y ];
            }

            if ( in_array( $this->get_type(), [ "Dead End", "Stairs Start", "Stairs Down", "Stairs Out" ] ) )
            {
                if ( $pos_x == ( $this->width - 1 ) ) $array_y = 0;
                else $array_y = 1;

                if ( $pos_y == ( $this->height - 1 ) ) $array_x = 0;
                else $array_x = 1;

                $segment = $this->segments[ 2 ][ $array_x ][ $array_y ];
            }

            if ( in_array( $this->get_type(), [ "Corner Right", "Corner Left", "T-Junction" ] ) )
            {
                $segment = $this->segments[ 2 ][ $pos_x ][ $pos_y ];
            }
        }

        if ( $direction == heading_west_north )
        {
            if ( str_starts_with( $this->get_type(), "Room" ) )
            {
                if ( $pos_x == 0 ) $array_y = 2;
                elseif ( $pos_x == ( $this->width - 1 ) ) $array_y = 0;
                else $array_y = 1;

                if ( $pos_y == 0 ) $array_x = 2;
                elseif ( $pos_y == ( $this->height - 1 ) ) $array_x = 0;
                else $array_x = 1;

                $segment = $this->segments[ $array_x ][ $array_y ];
            }

            if ( str_starts_with( $this->get_type(), "Passage" ) && $segment == "_" )
            {
                $array_x = 0;

                if ( $pos_y == 0 ) $array_y = 1;
                else $array_y = 0;

                $segment = $this->segments[ $array_x ][ $array_y ];
            }

            if ( in_array( $this->get_type(), [ "Dead End", "Stairs Start", "Stairs Down", "Stairs Out" ] ) )
            {
                if ( $pos_x == ( $this->width - 1 ) ) $array_y = 0;
                else $array_y = 1;

                if ( $pos_y == ( $this->height - 1 ) ) $array_x = 0;
                else $array_x = 1;

                $segment = $this->segments[ 3 ][ $array_x ][ $array_y ];
            }

            if ( in_array( $this->get_type(), [ "Corner Right", "Corner Left", "T-Junction" ] ) )
            {
                $segment = $this->segments[ 3 ][ $pos_x ][ $pos_y ];
            }
        }

        return $segment;
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

            $pos_x = random_int( 1, 5 ) - 1;
            $pos_y = random_int( 1, 5 ) - 1;
    
            $this->tiles[ $pos_y ][ $pos_x ] = "D";

            if ( $point->get_direction() == heading_north_east )
            {
                if ( $pos_x == 0 ) array_push( $points, new point( $point->get_pos_x() - $this->get_width() / 2, $point->get_pos_y(), heading_west_north ) );
                if ( $pos_x == 1 ) array_push( $points, new point( $point->get_pos_x() + $this->get_width(), $point->get_pos_y(), heading_north_east ) );
            }

            if ( $point->get_direction() == heading_east_south )
            {
                if ( $pos_y == 0 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $this->get_height() / 2, heading_north_east ) );
                if ( $pos_y == 1 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $this->get_height(), heading_east_south ) );
            }

            if ( $point->get_direction() == heading_south_west )
            {
                if ( $pos_x == 0 ) array_push( $points, new point( $point->get_pos_x() + $this->get_width() / 2, $point->get_pos_y(), heading_east_south ) );
                if ( $pos_x == 1 ) array_push( $points, new point( $point->get_pos_x() - $this->get_width(), $point->get_pos_y(), heading_south_west ) );
            }

            if ( $point->get_direction() == heading_west_north )
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
                $pos_x = random_int( 1, 5 ) - 1;
                $pos_y = random_int( 1, 5 ) - 1;
    
                if ( $point->get_direction() == heading_north_east )
                {
                    $this->tiles[ $pos_y ][ $i ] = "D";

                    if ( $i == 0 ) array_push( $points, new point( $point->get_pos_x() - $this->get_width() / 2, $point->get_pos_y(), heading_west_north ) );
                    if ( $i == 1 ) array_push( $points, new point( $point->get_pos_x() + $this->get_width(), $point->get_pos_y(), heading_north_east ) );
                }
    
                if ( $point->get_direction() == heading_east_south )
                {
                    $this->tiles[ $i ][ $pos_x ] = "D";

                    if ( $i == 0 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $this->get_height() / 2, heading_north_east ) );
                    if ( $i == 1 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $this->get_height(), heading_east_south ) );
                }
    
                if ( $point->get_direction() == heading_south_west )
                {
                    $this->tiles[ $pos_y ][ $i ] = "D";

                    if ( $i == 0 ) array_push( $points, new point( $point->get_pos_x() + $this->get_width() / 2, $point->get_pos_y(), heading_east_south ) );
                    if ( $i == 1 ) array_push( $points, new point( $point->get_pos_x() - $this->get_width(), $point->get_pos_y(), heading_south_west ) );
                }
    
                if ( $point->get_direction() == heading_west_north )
                {
                    $this->tiles[ $i ][ $pos_x ] = "D";

                    if ( $i == 0 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $this->get_height() / 2, heading_south_west ) );
                    if ( $i == 1 ) array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $this->get_height(), heading_west_north ) );
                }
            }
        }

        return $points;
    }
}
?>