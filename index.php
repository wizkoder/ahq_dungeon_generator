<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
<?php
    const dungeon_size = 100;
    const dungeon_start_x = 49;
    const dungeon_start_y = 0;
    const dungeon_start_elements = ["staircase_up_down", "corridor_up_down", "corridor_up_down", "t_junction_left_right"];

    const tile_alignment_horizontal = 0;
    const tile_alignment_vertical = 1;

    const nothing = "&blk14;";
    const normal_floor = "_";
    const t_junction = "T";
    const staircase_up_down = "-";
    const staircase_left_right = "|";

    class element
    {
        public $name;
        public $width;
        public $height;
        public $alignment;
        public $feature;
        public $tile;

        function setName( $name )
        {
            $this->name = $name;
        }

        function getName()
        {
            return $this->name;
        }

        function setWidth( $width )
        {
            $this->width = $width;
        }

        function getWidth()
        {
            return $this->width;
        }

        function setHeight( $height )
        {
            $this->height = $height;
        }

        function getHeight()
        {
            return $this->height;
        }

        function setAlignment( $alignment )
        {
            $this->alignment = $alignment;
        }

        function getAlignment()
        {
            return $this->alignment;
        }

        function setFeature( $feature )
        {
            $this->feature = $feature;
        }

        function getFeature()
        {
            return $this->feature;
        }

        function setTile( $tile )
        {
            $this->tile = $tile;
        }

        function getTile()
        {
            return $this->tile;
        }
    }

    class dungeon_generator
    {
        public $dungeon = array();
        public $elements = array();

        public function __construct()
        {
            $this->init_elements();
            $this->init_dungeon();
            $this->generate_dungeon();
            $this->show_dungeon();
        }

        function init_elements()
        {
            $new_element = new element();
            $new_element->setName( "staircase_up_down" );
            $new_element->setWidth( 2 );
            $new_element->setHeight( 2 );
            $new_element->setAlignment( tile_alignment_vertical );
            $new_element->setTile( staircase_up_down );
            $this->elements["staircase_up_down"] = $new_element;

            $new_element = new element();
            $new_element->setName( "staircase_left_right" );
            $new_element->setWidth( 2 );
            $new_element->setHeight( 2 );
            $new_element->setAlignment( tile_alignment_horizontal );
            $new_element->setTile( staircase_left_right );
            $this->elements["staircase_left_right"] = $new_element;

            $new_element = new element();
            $new_element->setName( "corridor_up_down" );
            $new_element->setWidth( 2 );
            $new_element->setHeight( 5 );
            $new_element->setAlignment( tile_alignment_vertical );
            $new_element->setTile( normal_floor );
            $this->elements["corridor_up_down"] = $new_element;

            $new_element = new element();
            $new_element->setName( "corridor_left_right" );
            $new_element->setWidth( 5 );
            $new_element->setHeight( 2 );
            $new_element->setAlignment( tile_alignment_horizontal );
            $new_element->setTile( normal_floor );
            $this->elements["corridor_left_right"] = $new_element;

            $new_element = new element();
            $new_element->setName( "t_junction_up_down" );
            $new_element->setWidth( 2 );
            $new_element->setHeight( 2 );
            $new_element->setAlignment( tile_alignment_vertical );
            $new_element->setTile( t_junction );
            $this->elements["t_junction_up_down"] = $new_element;

            $new_element = new element();
            $new_element->setName( "t_junction_left_right" );
            $new_element->setWidth( 2 );
            $new_element->setHeight( 2 );
            $new_element->setAlignment( tile_alignment_horizontal );
            $new_element->setTile( t_junction );
            $this->elements["t_junction_left_right"] = $new_element;
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

            foreach ( dungeon_start_elements as $key => $value )
            {
                $curr_element = $this->elements[$value];

                $this->place_tile( $curr_element, $pos_x, $pos_y );
    
                if ( $curr_element->getAlignment() == tile_alignment_horizontal )
                {
                    $pos_x = $pos_x + $curr_element->getWidth();
                }
                else {
                    $pos_y = $pos_y + $curr_element->getHeight();
                }
            }

            foreach ( $this->get_sections() as $key => $section )
            {
                $this->place_tile( $section, $pos_x, $pos_y );
    
                if ( $section->getAlignment() == tile_alignment_horizontal )
                {
                    $pos_x = $pos_x + $section->getWidth();
                }
                else {
                    $pos_y = $pos_y + $section->getHeight();
                }
            }
        }

        function show_dungeon()
        {
            echo "<p  class='font-monospace'>";

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

        function get_sections()
        {
            $section_count = random_int(1, 12);

            if ($section_count <= 3) $section_count = 1;
            if ($section_count >= 4 && $section_count <= 8) $section_count = 2;
            if ($section_count >= 9) $section_count = 3;

            $sections = array( $section_count );

            for ($i=0; $i < $section_count; $i++) { 
                $section = unserialize( serialize( $this->elements["corridor_left_right"] ) );

                $feature = random_int(1, 12) + random_int(1, 12);

                if ($feature <= 5) $section->setFeature( "M" );
                if ($feature >= 6 && $section_count <= 14) $section->setFeature( "N" );
                if ($feature >= 15 && $section_count <= 19) $section->setFeature( "1" );
                if ($feature >= 20 && $section_count <= 21) $section->setFeature( "2" );
                if ($feature >= 22) $section->setFeature( "M" );

                $sections[$i] = $section;
            }

            return $sections;
        }

        function place_tile( $tile, $width, $height )
        {
            $placeable = true;
    
            for ( $y = 0; $y < $tile->getHeight(); $y++ )
            {
                for ( $x = 0; $x < $tile->getWidth(); $x++ )
                {
                    if ( $this->dungeon[$y+$height][$x+$width] != nothing )
                    {
                        $placeable = false;
                    }
                }
            }

            if ( $placeable )
            {
                for ( $y = 0; $y < $tile->getHeight(); $y++ )
                {
                    for ( $x = 0; $x < $tile->getWidth(); $x++ )
                    {
                        if ($tile->getFeature())
                        {
                            $this->dungeon[$y+$height][$x+$width] = $tile->getFeature();
                        }
                        else
                        {
                            $this->dungeon[$y+$height][$x+$width] = $tile->getTile();
                        }
                    }
                }
            }
    
            return $placeable;
        }
    }

    $ahqdg = new dungeon_generator();

?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
  </body>
</html>