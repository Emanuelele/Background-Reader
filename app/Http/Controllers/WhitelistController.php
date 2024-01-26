<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Whitelist;

class WhitelistController extends Controller {
    
    public function make(Request $request) {
        try{
            DB::beginTransaction();
            $validateData = $request->validate(Whitelist::$rules);
            Whitelist::create($validateData);
            DB::commit();
            return response()->json('Request ok', 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json('Bad request' . $e, 400);
        }
    }
}
