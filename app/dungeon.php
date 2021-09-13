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

    function place_element( element $element, point $point )
    {
        if ( $point->get_direction() == heading_north )
        {
            for ( $y = 0; $y < $element->get_height(); $y++ )
            {
                for ( $x = 0; $x < $element->get_width(); $x++ )
                {
                    $this->grid[ $point->get_pos_y() - $y ][ $point->get_pos_x() - $x ] = $element->get_tile( $x, $y );
                }
            }

            $point->set_pos_y( $point->get_pos_y() - $element->get_height() );

            if ( $element->get_type() == "T-Junction" ) // REFACTOR T-JUNCTION
            {
                $point = new point( $point->get_pos_x() + $element->get_width() / 2, $point->get_pos_y() + $element->get_height() / 2, heading_east );
            }

            if ( $element->get_type() == "Corner Right" )
            {
                $point = new point( $point->get_pos_x() + $element->get_width() / 2, $point->get_pos_y() + $element->get_height() / 2, heading_east );
            }

            if ( $element->get_type() == "Corner Left" )
            {
                $point = new point( $point->get_pos_x() - $element->get_width(), $point->get_pos_y() + $element->get_height(), heading_west );
            }

            if ( $element->get_type() == "Dead End" || $element->get_type() == "Stairs" )
            {
                $point->set_direction( heading_end );
            }

            return $point;
        }

        if ( $point->get_direction() == heading_east )
        {
            for ( $y = 0; $y < $element->get_height(); $y++ )
            {
                for ( $x = 0; $x < $element->get_width(); $x++ )
                {
                    $this->grid[ $point->get_pos_y() + $y ][ $point->get_pos_x() + $x ] = $element->get_tile( $x, $y );
                }
            }

            $point->set_pos_x( $point->get_pos_x() + $element->get_width() );

            if ( $element->get_type() == "T-Junction" ) // REFACTOR T-JUNCTION
            {
                $point = new point( $point->get_pos_x() - $element->get_width(), $point->get_pos_y() + $element->get_height(), heading_south );
            }

            if ( $element->get_type() == "Corner Right" )
            {
                $point = new point( $point->get_pos_x() - $element->get_width(), $point->get_pos_y() + $element->get_height(), heading_south );
            }

            if ( $element->get_type() == "Corner Left" )
            {
                $point = new point( $point->get_pos_x() - $element->get_width() / 2, $point->get_pos_y() - $element->get_height() / 2, heading_north );
            }

            if ( $element->get_type() == "Dead End" || $element->get_type() == "Stairs" )
            {
                $point->set_direction( heading_end );
            }

            return $point;
        }

        if ( $point->get_direction() == heading_south )
        {
            for ( $y = 0; $y < $element->get_height(); $y++ )
            {
                for ( $x = 0; $x < $element->get_width(); $x++ )
                {
                    $this->grid[ $point->get_pos_y() + $y ][ $point->get_pos_x() + $x ] = $element->get_tile( $x, $y );
                }
            }

            $point->set_pos_y( $point->get_pos_y() + $element->get_height() );

            if ( $element->get_type() == "T-Junction" ) // REFACTOR T-JUNCTION
            {
                $point = new point( $point->get_pos_x() + $element->get_width(), $point->get_pos_y() - $element->get_height(), heading_east );
            }

            if ( $element->get_type() == "Corner Right" )
            {
                $point = new point( $point->get_pos_x() - $element->get_width() / 2, $point->get_pos_y() - $element->get_height() / 2, heading_west );
            }

            if ( $element->get_type() == "Corner Left" )
            {
                $point = new point( $point->get_pos_x() + $element->get_width(), $point->get_pos_y() - $element->get_height(), heading_east );
            }

            if ( $element->get_type() == "Dead End" || $element->get_type() == "Stairs" )
            {
                $point->set_direction( heading_end );
            }

            return $point;
        }

        if ( $point->get_direction() == heading_west )
        {
            for ( $y = 0; $y < $element->get_height(); $y++ )
            {
                for ( $x = 0; $x < $element->get_width(); $x++ )
                {
                    $this->grid[ $point->get_pos_y() - $y ][ $point->get_pos_x() - $x ] = $element->get_tile( $x, $y );
                }
            }

            $point->set_pos_x( $point->get_pos_x() - $element->get_width() );

            if ( $element->get_type() == "T-Junction" ) // REFACTOR T-JUNCTION
            {
                $point = new point( $point->get_pos_x() + $element->get_width() / 2, $point->get_pos_y() + $element->get_height() / 2, heading_south );
            }

            if ( $element->get_type() == "Corner Right" )
            {
                $point = new point( $point->get_pos_x() + $element->get_width(), $point->get_pos_y() - $element->get_height(), heading_north );
            }

            if ( $element->get_type() == "Corner Left" )
            {
                $point = new point( $point->get_pos_x() + $element->get_width() / 2, $point->get_pos_y() + $element->get_height() / 2, heading_south );
            }

            if ( $element->get_type() == "Dead End" || $element->get_type() == "Stairs" )
            {
                $point->set_direction( heading_end );
            }

            return $point;
        }

        /*
        $placeable = true;

        for ( $y = 0; $y < $element->get_height(); $y++ )
        {
            for ( $x = 0; $x < $element->get_width(); $x++ )
            {
                if ( $this->grid[ $y + $point->get_pos_y() ][ $x + $point->get_pos_x() ] != dungeon_type_nothing )
                {
                    $placeable = false;
                }
            }
        }

        if ( $placeable )
        {
            for ( $y = 0; $y < $element->get_height(); $y++ )
            {
                for ( $x = 0; $x < $element->get_width(); $x++ )
                {
                    $this->grid[ $y + $point->get_pos_y() ][ $x + $point->get_pos_x() ] = $element->get_tile( $x, $y );
                }
            }
        }

        return $placeable;
        */
    }

    function draw()
    {
        $matrix = $this->grid;
        $matrix = $this->array_remove_unique_lines( $matrix );
        $matrix = $this->array_transpose( $matrix );
        $matrix = $this->array_remove_unique_lines( $matrix );
        $matrix = $this->array_transpose( $matrix );

        echo "<p  style='font-family: monospace, monospace'>";

        foreach ( $matrix as $row )
        {
            foreach ( $row as $cell )
            {
                echo $cell;
            }

            echo "<br />";
        }

        echo "</p>";
    }

    function array_remove_unique_lines( array $array )
    {
        foreach ( $array as $row_nr => $row )
        {
            if ( count( array_unique( $row ) ) === 1 )
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