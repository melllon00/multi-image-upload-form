<?php

    include_once 'common.php';

    function imageCompressAndUpload( $uploads_dir, $changedName, $inputAreaID){
        $allowed_ext = array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF');

        // 폴더 존재 여부 확인 ( 없으면 생성 ) 
        if ( !is_dir ( $uploads_dir ) ){
            mkdir( $uploads_dir );
        }
         
        // 변수 정리
        $error = $_FILES[$inputAreaID]['error'];
        $name = $_FILES[$inputAreaID]['name'];
        $ext = array_pop(explode('.', $name));
         
        // 오류 확인
        if( $error != UPLOAD_ERR_OK ) {
            echo "{\"status\":\"";
            switch( $error ) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "파일이 너무 큽니다.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "BREAK";
                    break;
                default:
                    echo "파일이 제대로 업로드되지 않았습니다.";
            }
            echo "\"}";
            exit;
        }
         
        // 확장자 확인
        if( !in_array($ext, $allowed_ext) ) {
            echo "{\"status\":\"";
            echo "허용되지 않는 확장자입니다.";
            echo "\"}";
            exit;
        }

        $url = "{$uploads_dir}/{$changedName}.{$ext}";
        $filename="";
        if ( $_FILES[$inputAreaID]['size'] > 500000 ){
            $filename = compress($_FILES[$inputAreaID]["tmp_name"], $url, 80);
        }else{
            move_uploaded_file( $_FILES[$inputAreaID]['tmp_name'], $url);
        }

        echo "{\"status\":\"";
        echo "OK\",";
        echo "\"파일명\" : \"$name\", ";
        echo "\"확장자\" : \"$ext\", ";
        echo "\"파일형식\" : \"{$_FILES[$inputAreaID]['type']}\", ";
        echo "\"파일크기\" : \"{$_FILES[$inputAreaID]['size']} 바이트\", ";
        echo "\"url\" : \"{$url}\", ";
        echo "\"filename\" : \"{$filename}\"}";

        return $url;
    }

    // 위의 함수를 바탕으로 renewal 한 함수
    function multiImageCompressAndUpload( $uploads_dir, $changedName, $inputAreaName){
        $allowed_ext = array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF');
        $statusArr = array();
        $statusArr["totalNum"] = count($_FILES[$inputAreaName]["name"]);
        $statusArr["allStatus"] ="";

        // 폴더 존재 여부 확인 ( 없으면 생성 )
        if ( !is_dir ( $uploads_dir ) ){
            mkdir( $uploads_dir );
        }

        $statusArr["ITEM"] = array();

        // 파일의 크기와 확장자 확인
        foreach ($_FILES[$inputAreaName]["error"] as $key => $error) {
            // 변수 정리
            $name = $_FILES[$inputAreaName]["name"][$key];
            $ext = array_pop(explode('.', $name));

            // 오류 확인
            // 파일이 없는건 스킵한다.
            if( $error != UPLOAD_ERR_OK && $error != UPLOAD_ERR_NO_FILE) {
                switch( $error ) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $statusArr["ITEM"][$key]["key"] = $key;
                        $statusArr["ITEM"][$key]["status"] =  "파일이 너무 큽니다.";
                        break;
//                    case UPLOAD_ERR_NO_FILE:
//                        $statusArr["ITEM"][$key]["status"] =  "파일이 없습니다.";
//                        break;
                    default:
                        $statusArr["ITEM"][$key]["key"] = $key;
                        $statusArr["ITEM"][$key]["status"] =  "파일이 제대로 업로드되지 않았습니다.";
                }
                $statusArr["allStatus"]="FALSE";
            }

            // 확장자 확인
//            if( !in_array($ext, $allowed_ext)  ) {
            if( !in_array($ext, $allowed_ext) && $error != UPLOAD_ERR_NO_FILE ) {
                $statusArr["ITEM"][$key]["key"] = $key;
                $statusArr["ITEM"][$key]["status"] = "허용되지 않는 확장자입니다.";
                $statusArr["allStatus"]="FALSE";
            }

        }

        // 만약 전체가 실패이면 함수 종료.
        if ( $statusArr["allStatus"] == "FALSE"){
            echo json_encode2($statusArr) ;
            return $statusArr;
        }else{
            $statusArr["allStatus"] ="OK";
        }


        // 업로드
        foreach ($_FILES[$inputAreaName]["error"] as $key => $error) {
            // 변수 정리
            $name = $_FILES[$inputAreaName]["name"][$key];
            $ext = array_pop(explode('.', $name));

            $statusArr["ITEM"][$key]["key"]   = $key;
            $statusArr["ITEM"][$key]["error"] = $error;
            $statusArr["ITEM"][$key]["파일형식"] = $ext;
            $statusArr["ITEM"][$key]["name"] = $name;
            $statusArr["ITEM"][$key]["tmp_name"] = $_FILES[$inputAreaName]["tmp_name"][$key];


            $url = "{$uploads_dir}/{$changedName}_{$key}.{$ext}";
            $filename="";

            if ( $_FILES[$inputAreaName]["size"][$key]> 50000 ){
                $filename = compress($_FILES[$inputAreaName]["tmp_name"][$key], $url, 40);
            }else{
                move_uploaded_file( $_FILES[$inputAreaName]["tmp_name"][$key], $url);
            }

            if ($error == UPLOAD_ERR_NO_FILE){
                $statusArr["ITEM"][$key]["status"] = "EMPTY";
            }else if ($error == UPLOAD_ERR_OK) {
                $statusArr["ITEM"][$key]["status"] = "OK";
            }else {
                $statusArr["ITEM"][$key]["status"] = "ERR";
            }

            $statusArr["ITEM"][$key]["파일명"] = $name;
            $statusArr["ITEM"][$key]["확장자"] = $ext;
            $statusArr["ITEM"][$key]["파일크기"] = $_FILES[$inputAreaName]['size']."바이트";
            $statusArr["ITEM"][$key]["url"] = $url;
            $statusArr["ITEM"][$key]["filename"] = $filename;

        }

        echo json_encode2($statusArr) ;

        return $statusArr;
    }



    // 파일 압축 메소드 
    function compress($source, $destination, $quality) {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg') 
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/gif') 
            $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png') 
            $image = imagecreatefrompng($source);

        imagejpeg($image, $destination, $quality);

        return $destination;
    }




?>

