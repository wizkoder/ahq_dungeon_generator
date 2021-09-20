<?php

const dungeon_type_nothing = "·";

class dungeon
{
    // Properties
    public int $width;
    public int $height;
    public array $grid;

    function __construct( int $width, int $height )
    {
        $this->width = $width;
        $this->height = $height;

        for ( $y = 0; $y < $this->height; $y++ )
        {
            for ( $x = 0; $x < $this->width; $x++ )
            {
                $this->grid[ $y ][ $x ] = dungeon_type_nothing;
            }
        }
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

    function place_point( point $point )
    {
        $this->grid[ $point->get_pos_y() ][ $point->get_pos_x() ] = "#";
    }

    function is_element_placable( element $element, point $point, bool $include_space )
    {
        $placeable = true;

        $width = $include_space ? $element->get_placeable_width() : $element->get_width();
        $height = $include_space ? $element->get_placeable_height() : $element->get_height() ;

        for ( $x = 0; $x < $width; $x++ )
        {
            for ( $y = 0; $y < $height; $y++ )
            {
                $grid_x = $point->get_pos_x() + ( $x * $point->get_direction()[ 0 ] );
                $grid_y = $point->get_pos_y() + ( $y * $point->get_direction()[ 1 ] );

                if ( $grid_x < 0 || $grid_x >= $this->get_width() || $grid_y < 0 || $grid_y >= $this->get_height() || $this->grid[ $grid_y ][ $grid_x ] != dungeon_type_nothing )
                {
                    $placeable = false;
                }
            }
        }

        return $placeable;
    }

    function place_element( element $element, point $point )
    {
        $points = array();

        for ( $x = 0; $x < $element->get_width(); $x++ )
        {
            for ( $y = 0; $y < $element->get_height(); $y++ )
            {
                $grid_x = $point->get_pos_x() + ( $x * $point->get_direction()[ 0 ] );
                $grid_y = $point->get_pos_y() + ( $y * $point->get_direction()[ 1 ] );

                $this->grid[ $grid_y ][ $grid_x ] = $element->get_segment( $x, $y, $point->get_direction() );
            }
        }

        if ( in_array( $element->get_type(), [ "Corner Right", "T-Junction" ] ) )
        {
            if ( $point->get_direction() == heading_north_east ) $direction = heading_east_south;
            if ( $point->get_direction() == heading_east_south ) $direction = heading_south_west;
            if ( $point->get_direction() == heading_south_west ) $direction = heading_west_north;
            if ( $point->get_direction() == heading_west_north ) $direction = heading_north_east;

            if ( $direction == heading_north_east || $direction == heading_south_west )
            {
                $pos_x = $point->get_pos_x() + $point->get_direction()[ 0 ];
                $pos_y = $point->get_pos_y() + ( $element->get_height() * $direction[ 1 ] );
            }
            else
            {
                $pos_x = $point->get_pos_x() + ( $element->get_width() * $direction[ 0 ] );
                $pos_y = $point->get_pos_y() + $point->get_direction()[ 1 ];
            }

            array_push( $points, new point( $pos_x, $pos_y, $direction ) );
        }
        
        if ( in_array( $element->get_type(), [ "Corner Left", "T-Junction" ] ) )
        {
            if ( $point->get_direction() == heading_north_east ) $direction = heading_west_north;
            if ( $point->get_direction() == heading_east_south ) $direction = heading_north_east;
            if ( $point->get_direction() == heading_south_west ) $direction = heading_east_south;
            if ( $point->get_direction() == heading_west_north ) $direction = heading_south_west;

            if ( $direction == heading_north_east || $direction == heading_south_west )
            {
                $pos_x = $point->get_pos_x();
                $pos_y = $point->get_pos_y() + $direction[ 1 ];
            }
            else
            {
                $pos_x = $point->get_pos_x() + $direction[ 0 ];
                $pos_y = $point->get_pos_y();
            }

            array_push( $points, new point( $pos_x, $pos_y, $direction ) );
        }
        
        if ( in_array( $element->get_type(), [ "Passage 1 section", "Passage 2 sections", "Passage 3 sections", "Stairs Start", "Room Large", "Room Small", "Room Revolving" ] ) )
        {
            if ( $point->get_direction() == heading_north_east || $point->get_direction() == heading_south_west )
            {
                $pos_x = $point->get_pos_x();
                $pos_y = $point->get_pos_y() + ( $element->get_height() * $point->get_direction()[ 1 ] );
            }
            else
            {
                $pos_x = $point->get_pos_x() + ( $element->get_width() * $point->get_direction()[ 0 ] );
                $pos_y = $point->get_pos_y();
            }

            array_push( $points, new point( $pos_x, $pos_y, $point->get_direction() ) );
        }

        return $points;
    }

    function get_tiles( bool $with_grid = false )
    {
        $tiles = $this->grid;

        if ( !$with_grid )
        {
            $tiles = $this->array_remove_unique_lines( $tiles );
            $tiles = $this->array_transpose( $tiles );
            $tiles = $this->array_remove_unique_lines( $tiles );
            $tiles = $this->array_transpose( $tiles );
        }

        return $tiles;
    }

    function array_remove_unique_lines( array $array )
    {
        foreach ( $array as $row_nr => $row )
        {
            $array_count_values = array_count_values( $array[ $row_nr ] );

            if ( array_key_exists( dungeon_type_nothing, $array_count_values ) && $array_count_values[ dungeon_type_nothing ] == count( $array[ $row_nr ] ) )
            {
                unset( $array[ $row_nr ] );
            }
        }

        return array_values( $array );
    }

    function array_transpose( array $array )
    {
        $out = array();

        foreach ( $array as $key => $subarr )
        {
            foreach ( $subarr as $subkey => $subvalue )
            {
                $out[$subkey][$key] = $subvalue;
            }
        }

        return $out;
    }
}
?>