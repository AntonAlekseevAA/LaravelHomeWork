<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\CountValidator\AtLeast;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {/**test*/
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /** вызов Request::capture() не дает возможность взять юзера
        нужно параметром задать экземрляр и от него user отдаст */
    public function index(Request $request)
    {
        $dd = $request->user();
        $url =  Request::capture()->url();

        $user = Auth::user();

        if ($user != null) {
            $d = 4;
        }

        return view('home');
    }
}
