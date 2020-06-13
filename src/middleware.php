<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

// middleware untuk validasi api key => dibuat 21.07.18


$cekAPIKey = function ($request, $response, $next) {
    
    $key = $request->getQueryParam("key");

    if(!isset($key)){
        return $response->withJson(["status" => "API Key required"], 401);
    }
    
    $sql = "SELECT * FROM api_users WHERE api_key=:api_key";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":api_key" => $key]);
    
    if($stmt->rowCount() > 0){
        $result = $stmt->fetch();
        if($key == $result["api_key"]){
        
            // update hit
            $sql = "UPDATE api_users SET hit=hit+1 WHERE api_key=:api_key";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([":api_key" => $key]);
            
            return $response = $next($request, $response);
        }
    }

    return $response->withJson(["status" => "Unauthorized"], 401);

};

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

/* $app->add(function ($request, $response, $next) {
    
    $key = $request->getQueryParam("key");

    if(!isset($key)){
        return $response->withJson(["status" => "API Key required"], 401);
    }
    
    $sql = "SELECT * FROM api_users WHERE api_key=:api_key";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":api_key" => $key]);
    
    if($stmt->rowCount() > 0){
        $result = $stmt->fetch();
        if($key == $result["api_key"]){
        
            // update hit
            $sql = "UPDATE api_users SET hit=hit+1 WHERE api_key=:api_key";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([":api_key" => $key]);
            
            return $response = $next($request, $response);
        }
    }

    return $response->withJson(["status" => "Unauthorized"], 401);

}); */