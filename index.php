<?php
    const dungeon_size = 100;
    const dungeon_start_x = 49;
    const dungeon_start_y = 0;
    const dungeon_start_elements = [ "staircase_vertical", "corridor_vertical", "corridor_vertical", "t_junction_horizontal" ];

    const element_alignment_horizontal = 0;
    const element_alignment_vertical = 1;

    const nothing = ".";
    const staircase = "s";
    const floor = "_";
    const t_junction = "t";

    class element
    {
        public int $width;
        public int $height;
        public int $alignment;
        public string $type;
        public string $feature;
        public array $tiles;

        public function __construct( int $width, int $height, int $alignment, string $type )
        {
            $this->setWidth( $width );
            $this->setHeight( $height );
            $this->setAlignment( $alignment );

            $this->setType( $type );
            $this->setFeature( $type );

            for ( $y = 0; $y < $this->height; $y++ )
            {
                for ( $x = 0; $x < $this->width; $x++ )
                {
                    $this->tiles[ $y ][ $x ] = $this->type;
                }
            }
        }

        function setWidth( int $width )
        {
            $this->width = $width;
        }

        function getWidth()
        {
            return $this->width;
        }

        function setHeight( int $height )
        {
            $this->height = $height;
        }

        function getHeight()
        {
            return $this->height;
        }

        function setAlignment( int $alignment )
        {
            $this->alignment = $alignment;
        }

        function getAlignment()
        {
            return $this->alignment;
        }

        function setType( string $type )
        {
            $this->type = $type;
        }

        function getType()
        {
            return $this->type;
        }

        function getTiles()
        {
            return $this->tiles;
        }

        function setFeature( string $feature )
        {
            // Wandering Monsters
            if ( $feature == "M" )
            {
                $this->tiles[ random_int( 1, $this->height ) - 1 ][ random_int( 1, $this->width ) - 1 ] = "M";
            }

            // Nothing
            if ( $feature == "N" )
            {
                // draw nothing :-)
            }

            // 1 Door
            if ( $feature == "1" )
            {
                $this->tiles[ random_int( 1, $this->height ) - 1 ][ random_int( 1, $this->width ) - 1 ] = "D";
            }

            // 2 Doors
            if ( $feature == "2" )
            {
                $door1x = random_int( 1, $this->width ) - 1;
                $door1y = random_int( 1, $this->height ) - 1;

                $this->tiles[ $door1y ][ $door1x ] = "D";

                do
                {
                    $door2x = random_int( 1, $this->width ) - 1;
                    $door2y = random_int( 1, $this->height ) - 1;
                } while ( $door2x == $door1x && $door2y == $door1y );

                $this->tiles[ $door2y ][ $door2x ] = "D";
            }

            $this->feature = $feature;
        }

        function getFeature()
        {
            return $this->feature;
        }
    }

    class dungeon_generator
    {
        public $elements = array();
        public $dungeon = array();

        public function __construct()
        {
            $this->init_elements();
            $this->init_dungeon();
            $this->generate_dungeon();
            $this->show_dungeon();
        }

        function init_elements()
        {
            // staircase
            $new_element = new element( 2, 2, element_alignment_horizontal, staircase );
            $this->elements["staircase_horizontal"] = $new_element;

            $new_element = new element( 2, 2, element_alignment_vertical, staircase );
            $this->elements["staircase_vertical"] = $new_element;

            // corridor
            $new_element = new element( 5, 2, element_alignment_horizontal, floor );
            $this->elements["corridor_horizontal"] = $new_element;

            $new_element = new element( 2, 5, element_alignment_vertical, floor );
            $this->elements["corridor_vertical"] = $new_element;

            // t_junction
            $new_element = new element( 2, 2, element_alignment_horizontal, t_junction );
            $this->elements["t_junction_horizontal"] = $new_element;

            $new_element = new element( 2, 2, element_alignment_vertical, t_junction );
            $this->elements["t_junction_vertical"] = $new_element;
        }

        function init_dungeon()
        {
            for ( $y = 0; $y < dungeon_size; $y++ )
            {
                for ( $x = 0; $x < dungeon_size; $x++ )
                {
                    $this->dungeon[$y][$x] = nothing;
                }
            }
        }

        function generate_dungeon()
        {
            $pos_x = dungeon_start_x;
            $pos_y = dungeon_start_y;

            // START DUNGEON
            foreach ( dungeon_start_elements as $key => $element )
            {
                $curr_element = unserialize( serialize( $this->elements[$element] ) );

                $this->place_element( $curr_element, $pos_x, $pos_y );

                if ( $curr_element->getAlignment() == element_alignment_horizontal )
                {
                    $pos_x = $pos_x + $curr_element->getWidth();
                }
                else {
                    $pos_y = $pos_y + $curr_element->getHeight();
                }
            }

            // GENERATING DUNGEON
            foreach ( $this->get_sections() as $key => $section )
            {
                $this->place_element( $section, $pos_x, $pos_y );

                // DOOR(S)?
                if ( $section->getFeature() == "1" || $section->getFeature() == "2" )
                {
                    // set room(s)
                }
                
                if ( $section->getAlignment() == element_alignment_horizontal )
                {
                    $pos_x = $pos_x + $section->getWidth();
                }
                else {
                    $pos_y = $pos_y + $section->getHeight();
                }
            }
        }

        function get_sections()
        {
            $section_count = array_sum( $this->roll( "1D12" ) );

            if ($section_count <= 3) $section_count = 1;
            if ($section_count >= 4 && $section_count <= 8) $section_count = 2;
            if ($section_count >= 9) $section_count = 3;

            $sections = array( $section_count );

            for ( $i=0; $i < $section_count; $i++ )
            { 
                $section = unserialize( serialize( $this->elements["corridor_horizontal"] ) );

                $feature = array_sum( $this->roll( "2D12" ) );

                if ( $feature <= 5 || $feature >= 22 ) $section->setFeature( "M" );     // Wandering Monsters
                if ( $feature >= 6 && $feature <= 14 ) $section->setFeature( "N" );     // Nothing
                if ( $feature >= 15 && $feature <= 19 ) $section->setFeature( "1" );    // 1 Door
                if ( $feature >= 20 && $feature <= 21 ) $section->setFeature( "2" );    // 2 Doors

                $sections[$i] = $section;
            }

            return $sections;
        }

        function show_dungeon()
        {
            echo "<p  style='font-family: monospace, monospace'>";

            for ( $y = 0; $y < dungeon_size; $y++ )
            {
                for ( $x = 0; $x < dungeon_size; $x++ )
                {
                    echo $this->dungeon[$y][$x];
                }

                echo "<br />";
            }

            echo "</p>";
        }

        function place_element( $element, $pos_x, $pos_y )
        {
            $placeable = true;
    
            for ( $y = 0; $y < $element->getHeight(); $y++ )
            {
                for ( $x = 0; $x < $element->getWidth(); $x++ )
                {
                    if ( $this->dungeon[$y+$pos_y][$x+$pos_x] != nothing )
                    {
                        $placeable = false;
                    }
                }
            }

            if ( $placeable )
            {
                for ( $y = 0; $y < $element->getHeight(); $y++ )
                {
                    for ( $x = 0; $x < $element->getWidth(); $x++ )
                    {
                        $this->dungeon[$y + $pos_y][$x + $pos_x] = $element->getTiles()[$y][$x];
                    }
                }
            }
    
            return $placeable;
        }

        /**
         * Roll the dice
         *
         * Here's an example:<br />
         * <br />
         * $throws = roll("5d6");<br />
         * echo array_sum($throws)." [".implode(", ", $throws)."]";<br />
         * <br />
         * Output: 17 [1, 3, 6, 2, 5]
         *
         * @param string $notation - the dice notation e.g. 1d6, 4d6, etc.
         * @author sg
         * @return array - an array of integer values (the throws)
         */
        function roll( $notation )
        {
            $throws = array();

            if ( preg_match( "/(\d+)?[dDwW](\d+)+/", $notation, $dice ) )
            {
                for ( $throw = 0; $throw < (!$dice[1] ? 1 : $dice[1]); $throw++ )
                {
                    array_push( $throws, random_int( 1, $dice[2] ) );
                }
            }

            return $throws;
        }
    }

    $ahqdg = new dungeon_generator();
?>