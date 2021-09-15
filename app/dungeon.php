<?php

const dungeon_type_nothing = ".";

class dungeon
{
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
                $this->grid[ $x ][ $y ] = dungeon_type_nothing;
            }
        }
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

    function element_placeable( $point, $element, $offset_x, $offset_y )
    {
        $placeable = true;
        $xtest = $point->get_pos_x();
        $ytest = $point->get_pos_y();

        for ( $y = 0; $y < $element->get_height(); $y++ )
        {
            for ( $x = 0; $x < $element->get_width(); $x++ )
            {
                if ( $this->grid[ $xtest ][ $ytest ] != dungeon_type_nothing )
                {
                    $placeable = false;
                }
                
                $xtest = $xtest + $offset_x;
                $ytest = $ytest + $offset_y;
            }
    
            if ( $offset_x == 0 )
            {
                $xtest++;
                $ytest = $point->get_pos_y();
            }
    
            if ( $offset_y == 0 )
            {
                $ytest++;
                $xtest = $point->get_pos_x();
            }

            if ( $xtest < 0 or $xtest > $this->width )
            {
                return false;
            }

            if ( $ytest < 0 or $ytest > $this->height )
            {
                return false;
            }
    
        }

        return $placeable;
    }

    function place_element_2( $element, $point, $offset_x, $offset_y )
    {
        $xtest = $point->get_pos_x();
        $ytest = $point->get_pos_y();

        for ( $y = 0; $y < $element->get_height(); $y++ )
        {
            for ( $x = 0; $x < $element->get_width(); $x++ )
            {
                $this->grid[ $xtest ][ $ytest ] = $element->get_symbol();

                //echo "x:".$xtest."/y:".$ytest."/symbol:".$element->get_symbol()."<br/>";
                
                $xtest = $xtest + $offset_x;
                $ytest = $ytest + $offset_y;
            }
    
            if ( $offset_x == 0 )
            {
                $xtest++;
                $ytest = $point->get_pos_y();
            }
    
            if ( $offset_y == 0 )
            {
                $ytest++;
                $xtest = $point->get_pos_x();
            }
        }
    }

    function place_element( element $element, point $point )
    {
        if ( $point->get_direction() == heading_north )
        {
            $placeable = $this->element_placeable( $point, $element, 0, -1 );

            if ( $placeable )
            {
                $this->place_element_2( $element, $point , 0, -1 );
            }

            return $placeable;
        }

        if ( $point->get_direction() == heading_south )
        {
            $placeable = $this->element_placeable( $point, $element, 0, 1 );

            if ( $placeable )
            {
                $this->place_element_2( $element, $point, 0, 1 );
            }

            return $placeable;
        }

        if ( $point->get_direction() == heading_east )
        {
            $placeable = $this->element_placeable( $point, $element, 1, 0 );

            if ( $placeable )
            {
                $this->place_element_2( $element, $point, 1, 0 );
            }

            return $placeable;
        }

        if ( $point->get_direction() == heading_west )
        {
            $placeable = $this->element_placeable( $point, $element, -1, 0 );

            if ( $placeable )
            {
                $this->place_element_2( $element, $point, -1, 0 );
            }

            return $placeable;
        }
    }

    function draw()
    {
        $matrix = $this->grid;
        /*
        $matrix = $this->array_remove_unique_lines( $matrix );
        $matrix = $this->array_transpose( $matrix );
        $matrix = $this->array_remove_unique_lines( $matrix );
        $matrix = $this->array_transpose( $matrix );
        */

        echo "<p  style='font-family: monospace, monospace'>";

        for ( $y=0; $y<dungeon_size; $y++ )
        {
            for ( $x=0; $x<dungeon_size; $x++ )
            {
                echo $matrix[$x][$y];
            }
            echo "<br/>";
        }

        echo "</p>";
    }

    function array_remove_unique_lines( array $array )
    {
        foreach ( $array as $row_nr => $row )
        {
            if ( count( array_unique( $row ) ) === 1 && array_count_values( $array[ $row_nr ] )[ dungeon_type_nothing ] == count( $array[ $row_nr ] ) )
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