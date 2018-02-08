<?php

    namespace TU\Controllers;

    use Silex\Application;
    use TU\DAO\ImageDAO;
    use TU\DAO\PlayerDAO;
    use RandomLib;
    use SecurityLib;
    use Symfony\Component\HttpFoundation\Request;
    use TU\Utils\MathUtils;
    use TU\Utils\Equation;
    use TU\Utils\Ellipse;
    use TU\Utils\FormatUtils;
    use TU\Utils\Ransac;

    class AdminController {

        private $TOKEN_BAN_UNBAN = 'token_ban_unban';

        public function getSignup(Application $app) {

            return $app['twig']->render('signup.twig');

        }

        public function postSignup(Application $app, Request $req) {

            $username = $req->get('username');
            $pwd = $req->get('pwd');
            $pwd_repeat = $req->get('pwd_repeat');

            $pdao = new PlayerDAO();

            $errors = array();			

            // Username
            if(strlen($username) == 0) {

                    $errors[] = "Username is empty";

            }			
            elseif (preg_match("/^[0-9A-Za-z_]+$/", $username) == 0) {

                    $errors[] = "Username can only contain ASCII letters a-z (smalls and caps), numbers and the underscore";

            }			
            elseif($pdao->existsUsername($app, $username)) {

                    $errors[] = "Username already exists";

            }

            // Password
            if(strlen($pwd) == 0) {

                    $errors[] = "Password is empty";

            }			
            if(strcmp($pwd, $pwd_repeat) != 0) {

                    $errors[] = "Passwords do not match";

            }

            // User creation / error display
            if(count($errors) > 0) {

                    $data = array(
                            'username' => $username
                    );

                    return $app['twig']->render('signup.twig', array('errors' => $errors, 'data' => $data));

            }
            else {

                    $pdao->createAdmin($app, $username, $pwd);
                    return $app->redirect($app['url_generator']->generate('index'));

            }

        }

        public function getPercentageCSV(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);

            
            
            $h = 0;
            $header = array('id', 'name', 'clicks');
                // Output CSV file with the image params
            $fileName = "D:\params.csv";
            //add BOM to fix UTF-8 in Excel
            //fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            //fputcsv($fp, $header); 

            $imcent = array();



            //$x = 0;
            //$info = array();

            $fp = fopen('/params.csv','w');
            foreach($images as $image) {
                $j = 0;
                $i = 0;

                $pointsraw = $imDAO->getAllClicksImage($app, $image['id']);
                if ($image['id'] > 0) {
                $n = count($pointsraw);
                $tmp = array();
                shuffle($pointsraw);
                for ($bbq = 0; $bbq < $n; $bbq++) {
                    $tmp[$bbq]['x'] = $pointsraw[$bbq]['x'];
                    $tmp[$bbq]['y'] = $pointsraw[$bbq]['y'];
                }
                $pointsraw = $tmp; 

                while ($i < 5) {
                    // REMOVE BANNED PTS
                        //$x = $x+1;

                    $i = $i + 1;

                    if (count($pointsraw) > 0) {
                        $ransac = new Ransac;
                        $points = $ransac->ransacAlg($pointsraw);

                        $centroid = array('x' => 0, 'y' => 0);

                            Equation::setfrompoints($points);

                        $centroid = Equation::getCenter();		


                        //$ellipse_params = Equation::getEllipseParams();


                        //$axis = Equation::getAxisLength();

                        //$angle = Equation::getAngle();
                        if (($centroid['x'] > 0) && ($centroid['y'] > 0)) {
                            $tmp2 = array_merge($image,$centroid);
                            //$imcent = array_merge($tmp2,$ellipse_params);
                            //$imcent = array_merge($x,$imcent);
                            //$j = $j+1;
                            fputcsv($fp, $tmp2);

                        } else {
                            $i = $i -1;
                        }

                    }
                }

                //$j = $j+1;
                }
            }
            /*
            $current = file_get_contents($fileName);
            foreach($imcent as $im) {
                foreach($im as $title => $info) {
                    $current .= $info . ',';
                }
                $current .= PHP_EOL;
            }
            file_put_contents($fileName, $current);
            */   

            fclose($fp);
                
          
            
            /*$fp = fopen('/params.csv','w');
            
            foreach ($imcent as $im) {
                fputcsv($fp, $im);
            }
            fclose($fp);*/
            
            return $app['twig']->render('csv.twig', array(
                    
            ));
        }
        
        public function getNClicksFromImg(Application $app) {
            $imDAO = new ImageDAO();
            // For the test, get img with id 168
            $image = $imDAO->getImageById($app, 168);
            $header = array('id', 'name', 'clicks');
            // Output CSV file with the image params
            $fileName = "D:\params.csv";
            // Output CSV file with the image params 
            $fp = fopen('/params.csv','w');
            $j = 0;
            
            // REMOVE BANNED PTS ? UNDONE ?
            $pointsraw = $imDAO->getAllClicksImage($app, $image['id']);
            $n = count($pointsraw);
            shuffle($pointsraw);
            
            for ($x = 4; $x < $n; $x++) {
                $tmp = array();    
                $points = array();
                // FOR TESTING SMALLER PERCENTAGES OF DATA
                for ($h = 0; $h < $x; $h++) {
                    $tmp[$h]['x'] = $pointsraw[$h]['x'];
                    $tmp[$h]['y'] = $pointsraw[$h]['y'];
                    //print_r($data[$bbq]);
                }
                $i = 0;
                while ($i < 5) {

                    $i = $i + 1;

                    $ransac = new Ransac;
                    $points = $ransac->ransacAlg($tmp);

                    $centroid = array('x' => 0, 'y' => 0);

                    Equation::setfrompoints($points);

                    $centroid = Equation::getCenter();		
                    
                    $ellipse_params = Equation::getEllipseParams();
                    $axis = Equation::getAxisLength();
                    $angle = Equation::getAngle();
                    if (($centroid['x'] > 0) && ($centroid['y'] > 0)) {
                        $imcent = array_merge($image,$centroid);
                        array_push($imcent,$x+1);
                        //$imcent = array_merge($x,$imcent);
                        //$j = $j+1;
                        fputcsv($fp, $imcent);
                        
                    } else {
                        $i = $i -1;
                    }
                }
            }
           
            fclose($fp);
            
            return $app['twig']->render('csv2.twig', array(
                    
            ));
        }
        
        // CHANGE NAME TO CONSISTENCY
        public function getConcurrency(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);

            $h = 0;
           // $header = array('id', 'name', 'clicks');
                // Output CSV file with the image params
            //$fileName = "D:\params.csv";
            //add BOM to fix UTF-8 in Excel
            //fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            //fputcsv($fp, $header); 

            $imcent = array();
            $filter = 0.2; // Percentage of points from 50% to get    
            $iter = 0;

            //$x = 0;
            //$info = array();
            echo("menoks");
            var_dump("menoks");
            Ransac::console_log("Alotus");
            $fp = fopen('/params.csv','w');
            for ($iter = 0; $iter < 2; $iter++) {
            foreach($images as $image) {   
                // 465
                //if (($image['id'] > 297) && ($image['id'] < 299)) {
                if ($image['id'] == 298) {
                //    Ransac::console_log( $image['id'] );
                $pointsraw = $imDAO->getAllClicksImage($app, $image['id']);
                $n = count($pointsraw);
                $half = $n/2;
                $tmp = array();
                shuffle($pointsraw);    

                $chunked1 = array_slice($pointsraw, 0, $n / 2);
                $chunked2 = array_slice($pointsraw, $n / 2);
                
                //list($chunked1, $chunked2) = array_chunk($pointsraw, ceil($n/2), true);
                    $j = 0;
                    $i = 0;
                    if ($iter == 0) {
                        for ($bbq = 0; $bbq < count($chunked1)*$filter; $bbq++) {
                            $tmp[$bbq]['x'] = $chunked1[$bbq]['x'];
                            $tmp[$bbq]['y'] = $chunked1[$bbq]['y'];
                        }
                    }
                    if ($iter == 1) {
                        for ($bbq = 0; $bbq < count($chunked2)*$filter; $bbq++) {
                            $tmp[$bbq]['x'] = $chunked2[$bbq]['x'];
                            $tmp[$bbq]['y'] = $chunked2[$bbq]['y'];
                        }
                    }
                    $pointsraw = $tmp; 
                    var_dump($pointsraw);
                    while ($i < 5) {
                        // REMOVE BANNED PTS
                            //$x = $x+1;

                        $i = $i + 1;

                        if (count($pointsraw) > 0) {
                            $ransac = new Ransac;
                            $points = $ransac->ransacAlg($pointsraw);

                            $centroid = array('x' => 0, 'y' => 0);

                            Equation::setfrompoints($points);

                            $centroid = Equation::getCenter();		


                            //$ellipse_params = Equation::getEllipseParams();


                            //$axis = Equation::getAxisLength();

                            //$angle = Equation::getAngle();
                            if (($centroid['x'] > 0) && ($centroid['y'] > 0)) {
                                $tmp2 = array_merge($image,$centroid);
                                //$imcent = array_merge($tmp2,$ellipse_params);
                                //$imcent = array_merge($x,$imcent);
                                //$j = $j+1;
                                fputcsv($fp, $tmp2);

                            } else {
                                $i = $i -1;
                            }

                        }
                    }
                    }
                }
                //$j = $j+1;
                }

            fclose($fp);
             
            return $app['twig']->render('csv4.twig', array(
                    
            ));
        }
        
        public function getUserClickAmount(Application $app) {
            
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);
            $pointsraw = $imDAO->getAllClicks($app);
            $fp = fopen('/clicks.csv','w');
            $header = array('id', 'name', 'clicks');
            //fputcsv($fp, $header); 
            
            foreach ($pointsraw as $point) {
                fputcsv($fp, $point);
            }
            fclose($fp);
             /*
            $fp = fopen('/players.csv','w');
            $pDAO = new PlayerDAO();
            $players = $pDAO->getAllPlayers($app);
            
            foreach ($players as $player) {
                fputcsv($fp, $player);
            }
            fclose($fp);
            
             */
            return $app['twig']->render('csv3.twig', array(
                    
            ));
            
        }


        public function showMainMenu(Application $app) {

            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);
            $pointsraw = $imDAO->getAllClicks($app);
            $pDAO = new PlayerDAO();
            $players = $pDAO->getAllPlayers($app);

            return $app['twig']->render('list.twig', array(
                    'images' => $images,
                    'players' => $players
            ));        

        }

        public function showPlayerAdminMenu(Application $app, $id) {

            $imDAO = new ImageDAO();
            $images = $imDAO->getAllPlayerImages($app, $id);

            $pDAO = new PlayerDAO();
            $player = $pDAO->getPlayerById($app, $id);

            $f = new RandomLib\Factory();
            $g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));
            $token = $g->generateString(64);
            $app['session']->set($this->TOKEN_BAN_UNBAN, $token);

            return $app['twig']->render('admin_player.twig', array(
                    'player' => $player,
                    'images' => $images,
                    'token' => $token
            ));

        }

        public function userOp(Application $app, Request $req) {

            $t = $req->get('t');
            $t_s = $app['session']->get($this->TOKEN_BAN_UNBAN);

            if($t != $t_s) {
                $app->abort(400, "Invalid request");
            }

            var_dump($t);
            var_dump($t_s);

            $pDAO = new PlayerDAO();

            $op = $req->get('op');
            $user_id = $req->get('id');

            $pDAO->userOp($app, $op, $user_id);

            return "";


        }
        
        
        public function getTimestampsCSV(Application $app) {
            $pDAO = new PlayerDAO();
            $players = $pDAO->getAllPlayers($app);
            $imDAO = new ImageDAO();
            
            $fp = fopen('/stamps.csv','w');
            foreach ($players as $player) {
                $stamps = $imDAO->getAllStampsUser($app, $player['id']);
                foreach ($stamps as $stamp) {
                    //var_dump($stamp);
                    //$ar = array('pid' => $player['id'], 'stamp' => $stamp);
                    //$csv_array = array($player,$ar);
                    fputcsv($fp, $stamp);
                }
            }
            fclose($fp);
            
            return $app['twig']->render('csv5.twig', array(
                    
            ));
        }
        
        
        
    public function console_log(application $app, $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }
    
}

 