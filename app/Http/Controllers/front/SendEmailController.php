<?php
namespace App\Http\Controllers\front;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class SendEmailController extends Controller
{
    function index()
    {
     return view('send_email');
    }

    function send(Request $request)
    {
     $this->validate($request, [
      'firstname'     =>  'required',
      'lastname'     =>  'required',
      'email'  =>  'required|email',
      'message' =>  'required'
     ]);

        $data = array(
            'name'      =>  $request->name,
            'message'   =>   $request->message
        );

     Mail::to('osama.arshad@geeksroot.com')->send(new SendMail($data));
     return back()->with('success', 'Thanks for contacting us!');

    }
}