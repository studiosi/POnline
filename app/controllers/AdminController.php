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

            $filter = 1; // Percentage of points to get    
            $array_len = 0;    
            $fp = fopen('/params.csv','w');
            
            foreach($images as $image) {   
                //if ($image['id'] == 298) {
                $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $image['id']);
                $pointsraw_tmp = array_unique($pointsraw_unfiltered,SORT_REGULAR);
                $pointsraw = array_values($pointsraw_tmp);
                shuffle($pointsraw);    
                //$n = count($pointsraw);
                /*
                $chunked1 = array_slice($pointsraw, 0, $n / 2);
                $chunked2 = array_slice($pointsraw, $n / 2);

                $i = 0;
                $array_len = min(count($chunked1), count($chunked2));
                $len = ceil(max(5, $array_len*$filter));
                $tmp1 = array_slice($chunked1, 0, $len);
                $tmp2 = array_slice($chunked2, 0, $len);
                */
                $i = 0;
                while ($i < 5) {
                    //shuffle($tmp1); 
                    $ransac = new Ransac;
                    $points = $ransac->ransacAlg($pointsraw);

                    $centroid = array('x' => 0, 'y' => 0);
                    Equation::setfrompoints($points);
                    $centroid = Equation::getCenter();		
                    //$ellipse_params = Equation::getEllipseParams();
                    //$axis = Equation::getAxisLength();
                    //$angle = Equation::getAngle();
                    
                    if (($centroid['x'] >= 0) && ($centroid['y'] >= 0)) {
                        $temp1 = array_merge($image,$centroid);
                        //$imcent = array_merge($tmp2,$ellipse_params);
                        //$imcent = array_merge($x,$imcent);
                        //$j = $j+1;
                        fputcsv($fp, $temp1);
                        $i = $i + 1;
                    }
                }
                    
                    
            }
            fclose($fp);
             
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

            $filter = 0.2; // Percentage of points from 50% to get    
            $array_len = 0;    
            $fp = fopen('/params.csv','w');
            
            foreach($images as $image) {   
                //if ($image['id'] == 298) {
 
                $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $image['id']);
                $pointsraw_tmp = array_unique($pointsraw_unfiltered,SORT_REGULAR);
                $pointsraw = array_values($pointsraw_tmp);
                shuffle($pointsraw);    
                $n = count($pointsraw);
                $chunked1 = array_slice($pointsraw, 0, $n / 2);
                $chunked2 = array_slice($pointsraw, $n / 2);
               
                
                    $j = 0;
                    $i = 0;
                    $x = 0;
                    $array_len = min(count($chunked1), count($chunked2));
                    //var_dump($array_len);
                    // At least 5 pts
                    $len = ceil(max(5, $array_len*$filter));
                    //print($len);
                    //var_dump($len);
                    $tmp1 = array_slice($chunked1, 0, $len);
                    $tmp2 = array_slice($chunked2, 0, $len);

                    while ($i < 5) {
                       shuffle($tmp1); 
                            $ransac = new Ransac;
                            $points = $ransac->ransacAlg($tmp1);

                            $centroid = array('x' => 0, 'y' => 0);

                            Equation::setfrompoints($points);

                            $centroid = Equation::getCenter();		


                            //$ellipse_params = Equation::getEllipseParams();


                            //$axis = Equation::getAxisLength();

                            //$angle = Equation::getAngle();
                            if (($centroid['x'] >= 0) && ($centroid['y'] >= 0)) {
                                $temp1 = array_merge($image,$centroid);
                                //$imcent = array_merge($tmp2,$ellipse_params);
                                //$imcent = array_merge($x,$imcent);
                                //$j = $j+1;
                                fputcsv($fp, $temp1);
                                $i = $i + 1;
                            }
                    }
                    
                    while ($x < 5) {
                        shuffle($tmp2);     
                            $ransac = new Ransac;
                            $points = $ransac->ransacAlg($tmp2);

                            $centroid = array('x' => 0, 'y' => 0);

                            Equation::setfrompoints($points);

                            $centroid = Equation::getCenter();		

                            //$ellipse_params = Equation::getEllipseParams();
                            //$axis = Equation::getAxisLength();
                            //$angle = Equation::getAngle();
                            if (($centroid['x'] >= 0) && ($centroid['y'] >= 0)) {
                                $temp2 = array_merge($image,$centroid);
                                fputcsv($fp, $temp2);
                                $x = $x + 1;

                            } 
                    //}               
                    }  
            }
            fclose($fp);
             
            return $app['twig']->render('csv4.twig', array(
                    
            ));
        }
        
        public function get5ToNAll(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);

           // $header = array('id', 'name', 'clicks');
                // Output CSV file with the image params
            //$fileName = "D:\params.csv";
            //add BOM to fix UTF-8 in Excel
            //fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            //fputcsv($fp, $header); 
 
            $array_len = 0;    
            $fp = fopen('/improvement.csv','w');
            
            /* IMG amount count array for each iteration 142-5=137 */
            /* Filled with zeros */
            //$imcounts = array_fill(0, 137, 0);
            
            for ($i = 1; $i <= 142; $i++) {
                foreach($images as $image) {   
                    // GET IMAGEs THAT HAS $y clicks
                    
                    $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $image['id']);
                    
                    if (count($pointsraw_unfiltered) > $i) {
                        /* Increase according image count array*/
                        //$imcounts[$i-5]++;
                        // WE CANT REORDER PTS, TAKE WITH THE ORDER THEY ARE TAKEN
                        $pointsraw_tmp = array_unique($pointsraw_unfiltered,SORT_REGULAR);
                        $pointsraw = array_values($pointsraw_tmp);
                        shuffle($pointsraw);    
                        $n = count($pointsraw);
                        $chunked = array_slice($pointsraw, 0, $i);

                        $j = 0;
                        while ($j < 5) {
                            $centroid = array('x' => 0, 'y' => 0);
                            
                            if ($i > 5 ) { 
                                $ransac = new Ransac;
                                $points = $ransac->ransacAlg($chunked);
                                Equation::setfrompoints($points);
                                $centroid = Equation::getCenter();
                                if (($centroid['x'] > 0) && ($centroid['y'] > 0)) {
                                    $temp1 = array_merge($image,$centroid);
                                    fputcsv($fp, $temp1);
                                    $j = $j + 1;
                                }
                            }
                            else {
                                Equation::setfrompoints($chunked);
                                $centroid = Equation::getCenter();
                                $temp1 = array_merge($image,$centroid);
                                fputcsv($fp, $temp1);
                                $j = $j + 1;
                            }
                            
                            
                        }
                    } 
                    /* If less than $i click count: input 5 times 0,0 as centroid */
                    else {
                        $j = 0;
                        while ($j < 5) {
                            $centroid = array('x' => 0, 'y' => 0);
                            $temp1 = array_merge($image,$centroid);
                            fputcsv($fp, $temp1);
                            $j = $j + 1;
                        }
                    }   
                }
            }

            fclose($fp);
             
            return $app['twig']->render('csv6.twig', array(
                    
            ));
        }
        
        
        public function getImgCountsForImprovement(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);

           // $header = array('id', 'name', 'clicks');
                // Output CSV file with the image params
            //$fileName = "D:\params.csv";
            //add BOM to fix UTF-8 in Excel
            //fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            //fputcsv($fp, $header); 
 
            $array_len = 0;    
            $fp = fopen('/imgcountforclicks.csv','w');
            
            /* IMG amount count array for each iteration 142-5=137 */
            /* Filled with zeros */
            $imcounts = array_fill(0, 138, 0);
            
            for ($i = 5; $i <= 142; $i++) {
                foreach($images as $image) {   
                    // GET IMAGEs THAT HAS $y clicks
                    $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $image['id']);
                    if (count($pointsraw_unfiltered) > $i) {
                        /* Increase according image count array*/
                        $imcounts[$i-5]++;
                    }
                }
            }
            fputcsv($fp, $imcounts);
            fclose($fp);
             
            return $app['twig']->render('csv7.twig', array(
                    
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

 