<?php

const dungeon_type_nothing = ".";

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

        for ( $y = 0; $y < $height; $y++ )
        {
            for ( $x = 0; $x < $width; $x++ )
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

        for ( $y = 0; $y < $element->get_height(); $y++ )
        {
            for ( $x = 0; $x < $element->get_width(); $x++ )
            {
                $grid_x = $point->get_pos_x() + ( $x * $point->get_direction()[ 0 ] );
                $grid_y = $point->get_pos_y() + ( $y * $point->get_direction()[ 1 ] );

                $this->grid[ $grid_y ][ $grid_x ] = $element->get_tile( $x, $y );
            }
        }

        switch ( $element->get_type() )
        {
            case "T-Junction":
                if ( $point->get_direction() == heading_north_east )
                {
                    array_push( $points, new point( $point->get_pos_x() + $element->get_width(), $point->get_pos_y() - $element->get_height() / 2, heading_east_south ) );
                    array_push( $points, new point( $point->get_pos_x() - $element->get_width() / 2, $point->get_pos_y(), heading_west_north ) );
                }

                if ( $point->get_direction() == heading_east_south )
                {
                    array_push( $points, new point( $point->get_pos_x() + $element->get_width() / 2, $point->get_pos_y() + $element->get_height(), heading_south_west ) );
                    array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $element->get_height() / 2, heading_north_east ) );
                }

                if ( $point->get_direction() == heading_south_west )
                {
                    array_push( $points, new point( $point->get_pos_x() - $element->get_width(), $point->get_pos_y() + $element->get_height() / 2, heading_west_north ) );
                    array_push( $points, new point( $point->get_pos_x() + $element->get_width() / 2, $point->get_pos_y(), heading_east_south ) );
                }

                if ( $point->get_direction() == heading_west_north )
                {
                    array_push( $points, new point( $point->get_pos_x() - $element->get_width() / 2, $point->get_pos_y() - $element->get_height(), heading_north_east ) );
                    array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $element->get_width() / 2, heading_south_west ) );
                }
                break;

            case "Corner Right":
                if ( $point->get_direction() == heading_north_east )
                {
                    array_push( $points, new point( $point->get_pos_x() + $element->get_width(), $point->get_pos_y() - $element->get_height() / 2, heading_east_south ) );
                }

                if ( $point->get_direction() == heading_east_south )
                {
                    array_push( $points, new point( $point->get_pos_x() + $element->get_width() / 2, $point->get_pos_y() + $element->get_height(), heading_south_west ) );
                }

                if ( $point->get_direction() == heading_south_west )
                {
                    array_push( $points, new point( $point->get_pos_x() - $element->get_width(), $point->get_pos_y() + $element->get_height() / 2, heading_west_north ) );
                }

                if ( $point->get_direction() == heading_west_north )
                {
                    array_push( $points, new point( $point->get_pos_x() - $element->get_width() / 2, $point->get_pos_y() - $element->get_height(), heading_north_east ) );
                }
                break;

            case "Corner Left":
                if ( $point->get_direction() == heading_north_east )
                {
                    array_push( $points, new point( $point->get_pos_x() - $element->get_width() / 2, $point->get_pos_y(), heading_west_north ) );
                }

                if ( $point->get_direction() == heading_east_south )
                {
                    array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() - $element->get_height() / 2, heading_north_east ) );
                }

                if ( $point->get_direction() == heading_south_west )
                {
                    array_push( $points, new point( $point->get_pos_x() + $element->get_width() / 2, $point->get_pos_y(), heading_east_south ) );
                }

                if ( $point->get_direction() == heading_west_north )
                {
                    array_push( $points, new point( $point->get_pos_x(), $point->get_pos_y() + $element->get_width() / 2, heading_south_west ) );
                }
                break;

            case "Dead End":
            case "Stairs Down":
            case "Stairs Out":
                $points = array();
                break;

            default:
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
                break;
        }

        return $points;
    }

    function draw( int $tile_size = 30, bool $with_grid = false, bool $ascii = false )
    {
        $matrix = $this->grid;
    
        if ( !$with_grid )
        {
            $matrix = $this->array_remove_unique_lines( $matrix );
            $matrix = $this->array_transpose( $matrix );
            $matrix = $this->array_remove_unique_lines( $matrix );
            $matrix = $this->array_transpose( $matrix );
        }

        if ( $ascii )
        {
            echo '<p style="font-family: monospace, monospace; font-size: '.$tile_size.'px;">';

            foreach ( $matrix as $row )
            {
                foreach ( $row as $cell )
                {
                    echo $cell;
                }

                echo '<br />';
            }

            echo '</p>';
        }
        else
        {
            echo '<div style="font-family: monospace, monospace; font-size: '.( $tile_size / 1.5 ).'px; width: '.( $tile_size * count( reset( $matrix ) ) ).'px; height: '.( $tile_size * count( $matrix ) ).'px; margin: 0; padding: 0;">';

            foreach ( $matrix as $row )
            {
                foreach ( $row as $cell )
                {
                    echo '<div style="position: relative; display: inline-block; text-align: center; color: white; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black; margin: 0; padding: 0;">';
                    echo '<img alt="tile" style="margin: 0; padding: 0; width: '.$tile_size.'px; height: '.$tile_size.'px;" src="img/';
    
                    switch ( $cell )
                    {
                        case dungeon_type_nothing:
                            echo 'tile_00.png">';
                            break;
    
                        case 'e':
                        case 's':
                        case 'd':
                        case 'o':
                        case 'D':
                            echo 'tile_0'.random_int( 1, 8 ).'.png">';
                            echo '<span style="margin: 0; padding: 0; position: absolute; top:50%; left:50%; transform: translate(-50%, -50%);">'.$cell.'</span>';
                            break;
    
                        default:
                            echo 'tile_0'.random_int( 1, 8 ).'.png">';
                            break;
                    }
    
                    echo '</div>';
                }
    
                echo '<br />';
            }

            echo '</div>';
        }
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

        return $array;
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