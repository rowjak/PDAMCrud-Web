<?php

    function distance($lat1, $lon1, $lat2, $lon2) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $miles = $miles * 1.609344;
      return $miles;
    }

    function sort_array_by_value($key, &$array) {
        $sorter = array();
        $ret = array();

        reset($array);

        foreach($array as $ii => $value) {
            $sorter[$ii] = $value[$key];
        }

        asort($sorter);

        foreach($sorter as $ii => $value) {
            $ret[$ii] = $array[$ii];
        }

        $array = $ret;
    }

	function tanggal_indo($tanggal, $cetak_hari = false){
	  $hari = array ( 1 =>    'Senin',
	        'Selasa',
	        'Rabu',
	        'Kamis',
	        'Jumat',
	        'Sabtu',
	        'Minggu'
	      );
	      
	  $bulan = array (1 =>   'Januari',
	        'Februari',
	        'Maret',
	        'April',
	        'Mei',
	        'Juni',
	        'Juli',
	        'Agustus',
	        'September',
	        'Oktober',
	        'November',
	        'Desember'
	      );
	  $split    = explode('-', $tanggal);
	  $tgl_indo = $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
	  
	  if ($cetak_hari) {
	    $num = date('N', strtotime($tanggal));
	    return $hari[$num] . ', ' . $tgl_indo;
	  }
	  return $tgl_indo;
	}

	function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' ){
	    $datetime1 = date_create($date_2);
	    $datetime2 = date_create($date_1);
	    $interval = date_diff($datetime1, $datetime2);
	    return $interval->format($differenceFormat);
	}

    function push_notif($topik, $title, $message){
        $title = $title;
        $message = $message;
        $click_action = 'com.ragshion.ayosekolah.pushnotif_TARGET_NOTIFICATION';

        ini_set('display_errors', 'On');
     
        $type = 'topics';
        $fields = NULL;
        if($type == "topics") {
            $topics = $topik;
            
            $res = array();
            $res['body'] = $message;
            $res['title'] = $title;
            $res['sound'] = 'default';
            $res['click_action'] = $click_action;

            $fields = array(
                'to' => '/topics/' . $topics,
                'notification' => $res
            );

        }
        
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key = "AAAAZpdzZ5k:APA91bGMOYqu9P-yCKnZr19uotKwi0S2hloZmPNUnKsIur2Kjge_h8R5SCJ3L8Qv5LtMiqGzPnAEH6simdHlIOrihitChF1HLwauCZ5CpgVmvMflhmstJwFX97ZhoIa0GcGWqGbuc45e";
        
        $headers = array(
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            echo 'Curl failed: ' . curl_error($ch);
        }
 
        // Close connection
        curl_close($ch);
    }

    function smart_resize_image($file,$string = null,$width= 0, $height= 0, $proportional= false,$output= 'file', $delete_original= true, $use_linux_commands = false,$quality= 100,$grayscale= false){
        
        if ( $height <= 0 && $width <= 0 ) return false;
        if ( $file === null && $string === null ) return false;
        # Setting defaults and meta
        $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
        $image                        = '';
        $final_width                  = 0;
        $final_height                 = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;
        # Calculating proportionality
        if ($proportional) {
            if      ($width  == 0)  $factor = $height/$height_old;
            elseif  ($height == 0)  $factor = $width/$width_old;
            else                    $factor = min( $width / $width_old, $height / $height_old );
            $final_width  = round( $width_old * $factor );
            $final_height = round( $height_old * $factor );
        }else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }
        # Loading image to memory according to type
        switch ( $info[2] ) {
            case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
            case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
            case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
            default: return false;
        }
        
        # Making the image grayscale, if needed
        if ($grayscale) {
            imagefilter($image, IMG_FILTER_GRAYSCALE);
        }    
        
        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);
            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color  = imagecolorsforindex($image, $transparency);
                $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            } else if ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }

        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);
        
        
        # Taking care of original, if needed
        if ( $delete_original ) {
            if ( $use_linux_commands ) exec('rm '.$file);
            else @unlink($file);
        }
        # Preparing a method of providing result
        switch ( strtolower($output) ) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
            break;
            case 'file':
                $output = $file;
            break;
            case 'return':
                return $image_resized;
            break;
                default:
            break;
        }
        
        # Writing image according to type to the output destination and image quality
        switch ( $info[2] ) {
            case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
            case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9*$quality)/10.0);
                imagepng($image_resized, $output, $quality);
            break;
                default: return false;
        }
        return true;
    }

    


 ?>