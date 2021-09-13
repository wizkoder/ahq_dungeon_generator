<?php
    require_once "app/point.php";
    require_once "app/element.php";
    require_once "app/dungeon.php";
    require_once "app/dice.php";

    // init dungeon
    $dice = new dice();
    $dungeon = new dungeon( 200, 200, dungeon_type_nothing );
    $point = new point( 99, 99, heading_south ); // start point, every $dungeon->place_element call, returns the calculated point for the next element

    // base dungeon elements
    $point = $dungeon->place_element( new element( element_stairs_start, $point->get_direction() ), $point );
    $point = $dungeon->place_element( new element( element_passage_two, $point->get_direction() ), $point );
    $point = $dungeon->place_element( new element( element_t_junction, $point->get_direction() ), $point );

    /**
    * !!! START generate dungeon !!!
    */

    do
    {
        // first step passage
        $passage = new element( element_passage_one, $point->get_direction() );

        $roll = array_sum( $dice->roll( "1D12" ) );

        if ($roll >= 4 && $roll <= 8) $passage = new element( element_passage_two, $point->get_direction() );
        if ($roll >= 9) $passage = new element( element_passage_three, $point->get_direction() );
        
        // second step passage function
        $passage->roll_feature();

        // place passage
        $point = $dungeon->place_element( $passage, $point );

        // if passage has room(s), place it
        if ( $passage->get_feature() == "1 Door" || $passage->get_feature() == "2 Doors" )
        {
            $direction = $passage->get_direction();
            
        }


        // third step passage end
        $passage_end = new element( element_t_junction, $point->get_direction() );

        $roll = array_sum( $dice->roll( "2D12" ) );

        if ($roll >= 4 && $roll <= 8) $passage_end = new element( element_dead_end, $point->get_direction() );
        if ($roll >= 9 && $roll <= 11) $passage_end = new element( element_corner_right, $point->get_direction() );
        if ($roll >= 12 && $roll <= 14) $passage_end = new element( element_t_junction, $point->get_direction() );
        if ($roll >= 15 && $roll <= 17) $passage_end = new element( element_corner_left, $point->get_direction() );
        if ($roll >= 18 && $roll <= 19) $passage_end = new element( element_stairs_down, $point->get_direction() );
        if ($roll >= 20 && $roll <= 22) $passage_end = new element( element_stairs_out, $point->get_direction() );

        $point = $dungeon->place_element( $passage_end, $point );
    }
    while ($point->get_direction() != heading_end);

    /**
    * !!! END generate dungeon !!!
    */

    // draw dungeon
    $dungeon->draw();
?>
<?php
    /* OLD code
    const dungeon_size = 100;
    const dungeon_start_x = 49;
    const dungeon_start_y = 0;
    const dungeon_start_elements = [ "staircase_vertical", "corridor_2_vertical", "t_junction_horizontal" ];

    const element_alignment_horizontal = 0;
    const element_alignment_vertical = 1;

    const nothing = ".";
    const staircase = "s";
    const floor = "_";
    const t_junction = "t";
    const dead_end = "e";

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
            $this->setFeature( "Nothing" );

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
            $this->feature = $feature;

            // Wandering Monsters
            if ( $feature == "Wandering Monsters" )
            {
                $this->tiles[ random_int( 1, $this->height ) - 1 ][ random_int( 1, $this->width ) - 1 ] = "M";
            }

            // Nothing
            if ( $feature == "Nothing" )
            {
                // draw nothing :-)
            }

            // 1 Door
            if ( $feature == "1 Door" )
            {
                $this->tiles[ random_int( 1, $this->height ) - 1 ][ random_int( 1, $this->width ) - 1 ] = "D";
            }

            // 2 Doors
            if ( $feature == "2 Doors" )
            {
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

        function getFeature()
        {
            return $this->feature;
        }

        function rollFeature()
        {
            $dice = new dice();

            $roll = array_sum( $dice->roll( "2D12" ) );

            if ( $roll <= 5 ) $this->setFeature( "Wandering Monsters" );        // Wandering Monsters
            if ( $roll >= 6 && $roll <= 14 ) $this->setFeature( "Nothing" );    // Nothing
            if ( $roll >= 15 && $roll <= 19 ) $this->setFeature( "1 Door" );    // 1 Door
            if ( $roll >= 20 && $roll <= 21 ) $this->setFeature( "2 Doors" );   // 2 Doors
            if ( $roll >= 22 ) $this->setFeature( "Wandering Monsters" );       // Wandering Monsters
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

            // corridor 1 section
            $new_element = new element( 5, 2, element_alignment_horizontal, floor );
            $this->elements["corridor_1_horizontal"] = $new_element;

            $new_element = new element( 2, 5, element_alignment_vertical, floor );
            $this->elements["corridor_1_vertical"] = $new_element;

            // corridor 2 sections
            $new_element = new element( 10, 2, element_alignment_horizontal, floor );
            $this->elements["corridor_2_horizontal"] = $new_element;

            $new_element = new element( 2, 10, element_alignment_vertical, floor );
            $this->elements["corridor_2_vertical"] = $new_element;

            // corridor 3 sections
            $new_element = new element( 15, 2, element_alignment_horizontal, floor );
            $this->elements["corridor_3_horizontal"] = $new_element;

            $new_element = new element( 2, 15, element_alignment_vertical, floor );
            $this->elements["corridor_3_vertical"] = $new_element;

            // t_junction
            $new_element = new element( 2, 2, element_alignment_horizontal, t_junction );
            $this->elements["t_junction_horizontal"] = $new_element;

            $new_element = new element( 2, 2, element_alignment_vertical, t_junction );
            $this->elements["t_junction_vertical"] = $new_element;

            // dead_end
            $new_element = new element( 2, 2, element_alignment_horizontal, dead_end );
            $this->elements["dead_end_horizontal"] = $new_element;

            $new_element = new element( 2, 2, element_alignment_vertical, dead_end );
            $this->elements["dead_end_vertical"] = $new_element;

            // right_turn
            $new_element = new element( 2, 2, element_alignment_horizontal, floor );
            $this->elements["right_turn_horizontal"] = $new_element;

            $new_element = new element( 2, 2, element_alignment_vertical, floor );
            $this->elements["right_turn_vertical"] = $new_element;

            // left_turn
            $new_element = new element( 2, 2, element_alignment_horizontal, floor );
            $this->elements["left_turn_horizontal"] = $new_element;

            $new_element = new element( 2, 2, element_alignment_vertical, floor );
            $this->elements["left_turn_vertical"] = $new_element;

            // stairs_down
            $new_element = new element( 2, 2, element_alignment_horizontal, staircase );
            $this->elements["stairs_down_horizontal"] = $new_element;

            $new_element = new element( 2, 2, element_alignment_vertical, staircase );
            $this->elements["stairs_down_vertical"] = $new_element;

            // stairs_out
            $new_element = new element( 2, 2, element_alignment_horizontal, staircase );
            $this->elements["stairs_out_horizontal"] = $new_element;

            $new_element = new element( 2, 2, element_alignment_vertical, staircase );
            $this->elements["stairs_out_vertical"] = $new_element;
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
            $passage = $this->get_passage();

            $placeable = $this->place_element( $passage, $pos_x, $pos_y );

            // DOOR(S)?
            if ( $passage->getFeature() == "1 Door" || $passage->getFeature() == "2 Doors" )
            {
                // set room(s)
            }
            
            if ( $passage->getAlignment() == element_alignment_horizontal )
            {
                $pos_x = $pos_x + $passage->getWidth();
            }
            else {
                $pos_y = $pos_y + $passage->getHeight();
            }

            $placeable = $this->place_element( $this->get_passage_end( $passage->getAlignment() ), $pos_x, $pos_y );
        }

        function get_passage()
        {
            $dice = new dice();

            $roll = array_sum( $dice->roll( "1D12" ) );

            if ($roll <= 3) $passage_lenght = 1;
            if ($roll >= 4 && $roll <= 8) $passage_lenght = 2;
            if ($roll >= 9) $passage_lenght = 3;

            $passage = unserialize( serialize( $this->elements["corridor_".$passage_lenght."_horizontal"] ) );
            $passage->rollFeature();

            return $passage;
        }

        function get_passage_end(int $element_alignment)
        {
            $dice = new dice();

            $roll = array_sum( $dice->roll( "2D12" ) );

            if ($element_alignment == element_alignment_horizontal)
            {
                $element_alignment = "horizontal";
            }
            else
            {
                $element_alignment = "vertical";
            }

            if ($roll <= 3) $passage = unserialize( serialize( $this->elements["t_junction_".$element_alignment] ) );
            if ($roll >= 4 && $roll <= 8) $passage = unserialize( serialize( $this->elements["dead_end_".$element_alignment] ) );
            if ($roll >= 9 && $roll <= 11) $passage = unserialize( serialize( $this->elements["right_turn_".$element_alignment] ) );
            if ($roll >= 12 && $roll <= 14) $passage = unserialize( serialize( $this->elements["t_junction_".$element_alignment] ) );
            if ($roll >= 15 && $roll <= 17) $passage = unserialize( serialize( $this->elements["left_turn_".$element_alignment] ) );
            if ($roll >= 18 && $roll <= 19) $passage = unserialize( serialize( $this->elements["stairs_down_".$element_alignment] ) );
            if ($roll >= 20 && $roll <= 22) $passage = unserialize( serialize( $this->elements["stairs_out_".$element_alignment] ) );
            if ($roll >= 23) $passage = unserialize( serialize( $this->elements["t_junction_".$element_alignment] ) );

            return $passage;
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
    }

    class dice
    {
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
        /*function roll( $notation )
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
    */
?>