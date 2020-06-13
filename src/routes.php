<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile; //21.7.18

/**
* SLIM API POS
*
* @package  SLIM API POS
* @author   Ketut Ugi Diranta <nugi.dirta@gmail.com>
*/

// Routes
$app->get('/[{name}]', 
    function (Request $request, Response $response, array $args) 
    {
        // Sample log message
        $this->logger->info("Slim-Skeleton '/' route");
        // Render index view
        return $this->renderer->render($response, 'index.phtml', $args);
    }
);

// Ambil Semua Data
$app->get("/items/", 
    function (Request $request, Response $response)
    {
        $sql = "SELECT * FROM tbl_item";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);    
    }
);

// Ambil 1 Data
$app->get("/items/{kodeitem}", 
    function (Request $request, Response $response, $args)
    {
        $sql = "SELECT tbl_item.* FROM tbl_item WHERE tbl_item.kodeitem=:kodeitem";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["kodeitem" => $args["kodeitem"]]);
        $result = $stmt->fetch();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    }
);

// Cari Data
$app->get("/items/cari/", 
    function (Request $request, Response $response, $args)
    {
        $keyword = $request->getQueryParam("keyword");
        $keyword = strtoupper($keyword);
        $sql = "SELECT tbl_item.* FROM tbl_item 
                WHERE tbl_item.kodeitem LIKE :keyword OR tbl_item.namaitem LIKE :keyword";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["keyword" => "%".$keyword."%"]);
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    }
);

// Cari 1 Data
$app->get("/items/cari/namaitem/", 
    function (Request $request, Response $response, $args)
    {
        $keyword = $request->getQueryParam("keyword");
        $keyword = strtoupper($keyword);
        $sql = "SELECT tbl_item.* FROM tbl_item 
                WHERE tbl_item.namaitem LIKE :keyword";    
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["keyword" => "%".$keyword."%"]);
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    }
);

// Simpan Data dan Kirim data
$app->post("/items/", 
    function (Request $request, Response $response) 
    {
        $get_data = $request->getParsedBody();

        $sql = "INSERT INTO tbl_item(kodeitem, namaitem, jenis, tipe) 
                VALUES (:kodeitem, :namaitem, :jenis, :tipe)";
        $stmt = $this->db->prepare($sql);

        $data = [
            "kodeitem" => $get_data["kodeitem"],
            "namaitem" => $get_data["namaitem"],
            "jenis" => null,//$get_data["jenis"],
            "tipe" => null//$get_data["tipe"]
        ];

        if($stmt->execute($data))
        {
            return $response->withJson(["status" => "success", "data" => $data], 200);
        }
        else
        {
            return $response->withJson(["status" => "failed", "data" => "0"], 200);
        }      
    }
);

// Update Data
$app->put("/items/{kodeitem}", 
    function (Request $request, Response $response, $args)
    {
        $get_data = $request->getParsedBody();
        $sql = "UPDATE tbl_item SET 
                namaitem=:namaitem,
                jenis=:jenis,
                tipe=:tipe
                WHERE kodeitem=:kodeitem";
        $stmt = $this->db->prepare($sql);
        
        $data = [            
            "namaitem" => $get_data["namaitem"],
            "jenis" => null,//$get_data["jenis"],
            "tipe" => null,//$get_data["tipe"],
            "kodeitem" => $args["kodeitem"]
        ];

        if($stmt->execute($data))
        {
            return $response->withJson(["status" => "success", "data" => "1"], 200);
        }
        else
        {
            return $response->withJson(["status" => "failed", "data" => "0"], 200);
        }
    }
);

// Hapus Data
$app->delete("/items/{kodeitem}", 
    function (Request $request, Response $response, $args)
    {
        $sql = "DELETE FROM tbl_item WHERE kodeitem=:kodeitem";
        $stmt = $this->db->prepare($sql);

        if($stmt->execute(["kodeitem"=>$args["kodeitem"]]))
        {
            return $response->withJson(["status" => "success", "data" => "1"], 200);
        }
        else
        {                    
            return $response->withJson(["status" => "failed", "data" => "0"], 200);
        }
    }
);

// Simpan Gambar
$app->post('/items/gambaritem/{id}', 
    function(Request $request, Response $response, $args) 
    {        
        $uploadedFiles = $request->getUploadedFiles();
        
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['gambaritem'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            
            // ubah nama file dengan id buku
            $filename = sprintf('%s.%0.8s', $args["id"], $extension);
            
            $directory = $this->get('settings')['upload_directory'];
            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

            // simpan nama file ke database
            $sql = "UPDATE items SET gambaritem=:gambaritem WHERE id=:id";
            $stmt = $this->db->prepare($sql);
            $params = [
                "id" => $args["id"],
                "gambaritem" => $filename
            ];
            
            if($stmt->execute($params))
            {
                // ambil base url dan gabungkan dengan file name untuk membentuk URL file
                $url = $request->getUri()->getBaseUrl()."/uploads/".$filename;
                return $response->withJson(["status" => "success", "data" => $url], 200);
            }
            else
            {            
                return $response->withJson(["status" => "failed", "data" => "0"], 200);
            }
        }
    }
);